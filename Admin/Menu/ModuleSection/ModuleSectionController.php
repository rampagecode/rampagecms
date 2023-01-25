<?php

namespace Admin\Menu\ModuleSection;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\AdminException;
use Admin\ButtonsView;
use Admin\Menu\MenuView;
use Admin\WidgetsView;
use App\AppInterface;
use App\Module\ModuleBackendController;
use App\Module\ModuleFactory;
use App\Module\ModuleInterface;
use App\Module\ModuleReader;
use App\Page\ModuleTemplate;
use App\Page\Page;
use App\Page\PageLayout;
use App\Page\PageParser;
use App\UI\FormBuilder;
use App\UI\FormSelectOptions;
use App\UI\TableBuilder;
use App\User\User;
use Data\Module\Table;
use Lib\FormProcessing;
use Sys\Database\DatabaseInterface;

class ModuleSectionController implements AdminControllerInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    /**
     * @return string[]
     */
    function availableActions() {
        return [
            'install' => 'installAction',
            'uninstall' => 'uninstallAction',
            'edit' => 'editAction',
        ];
    }

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function auto_run() {
        $mainView = new MenuView();

        if( empty( $this->parameters->mod )) {
            if( array_key_exists( $this->parameters->act, $this->availableActions() )) {
                $methodName = $this->availableActions()[ $this->parameters->act ];
                return $mainView->without_menu( $this->$methodName() );
            } else {
                return $mainView->without_menu( $this->showList() );
            }
        } else {
            $module = ModuleFactory::loadModule( $this->app->rootDir('Module', $this->parameters->mod ), $this->app );

            if( $module ) {
                $modController = $module->getBackend( null );
                $content = ModuleFactory::executeAdminModule( $modController, $this->parameters );

                return $mainView->without_menu( $content );
            }
        }
    }

    /**
     * @return string
     * @throws \Admin\AdminException
     * @throws \App\AppException
     */
    public function showList() {
        $reader = new ModuleReader();
        $mainView = new MenuView();
        $button = new ButtonsView();
        $modulesTable = new Table();
        $availableModules = $reader->readDirForModules( $this->app->rootDir('Module'), $this->app );
        $installedModules = $modulesTable->fetchAll()->toArray();
        $installedModulesTable = new TableBuilder();
        $installedModulesTable->firstRowWidth = '50%';
        $installedModulesTable->secondRowWidth = '50%';

        foreach( $installedModules as $moduleRow ) {
            if( empty( $availableModules[ $moduleRow['name'] ] )) {
                $installedModulesTable->addInput(
                    'Модуль не найден',
                    $moduleRow['name'],
                    $moduleRow['description']
                );
                continue;
            }

            $module = $availableModules[ $moduleRow['name'] ];

            if( !$module->isInstallable() ) {
                continue;
            }

            $controls = [];

            $backend = $availableModules[ $moduleRow['name'] ]->getBackend( $moduleRow['id'] );
            $backend->setRequestParameters( $this->parameters );

            if( $backend instanceof ModuleBackendController ) {
                $controls[] = $button->install( $backend->buildURL('', ''), 'Управлять' );
                $controls[] = $button->edit( $this->parameters->buildURL($moduleRow['id'], 'edit'), 'Редактировать' );
            }

            $controls[] = $button->delete(
                $this->parameters->buildURL( $moduleRow['id'], 'uninstall' ),
                'Удалить'
            );

            $installedModulesTable->addInput(
                implode('; ', $controls ),
                $moduleRow['title'],
                $moduleRow['description']
            );
        }

        $availableModulesTable = new TableBuilder();
        $availableModulesTable->firstRowWidth = '50%';
        $availableModulesTable->secondRowWidth = '50%';

        foreach( $availableModules as $mod ) {
            if( $mod->isInstallable()) {
                $installUrl = $this->parameters->buildURL(null, 'install')
                    .'&moduleName='.$mod->getInfo()->name;

                $availableModulesTable->addInput(
                    $button->install( $installUrl, 'Установить' ),
                    $mod->getInfo()->title,
                    $mod->getInfo()->description
                );
            } else {
                $installedModulesTable->addInput(
                    'Неуправляемый',
                    $mod->getInfo()->name,
                    $mod->getInfo()->description
                );
            }
        }

        if( $availableModulesTable->rowCount() == 0 ) {
            $availableModulesTable->addText('Пусто');
        }

        if( $installedModulesTable->rowCount() == 0 ) {
            $installedModulesTable->addText('Пусто');
        }

        $widgets = new WidgetsView();
        $content = $widgets->tableHeader('Установленные модули');
        $content .= $installedModulesTable->build();
        $content .= '<br/>';
        $content .= $widgets->tableHeader('Доступные для установки');
        $content .= $availableModulesTable->build();

        return $content;
    }

    function installAction() {
        $moduleName = $this->app->in('moduleName');
        $widgets = new WidgetsView();

        if( empty( $moduleName )) {
            return $widgets->error('Module name is not specified');
        }

        $module = ModuleFactory::loadModule( $this->app->rootDir('Module', $moduleName ), $this->app );

        if( empty( $module )) {
            return $widgets->error("Module '{$moduleName}' is not found");
        }

        $form = new ModuleSettingsForm();
        $form->addHidden('i', 'install' );
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        $isSubmit = !empty( $_POST['form_submit'] );

        if( $isPost ) {
            $processing = new FormProcessing( $form->items );
            $processing->process( $this->app->in() );
            $errors = $processing->getErrors();
            $values = $processing->getValues();
        } else {
            $values['title'] = $module->getInfo()->title;
            $values['description'] = $module->getInfo()->description;
        }

        $form->createRows( $this->app, $values );
        $widgets = new WidgetsView();
        $content = '';
        $builder = new TableBuilder();

        if( $isPost && $isSubmit && empty( $errors )) {
            $db = $this->app->db();
            try {
                $db->beginTransaction();
                $db->insert('modules', [
                    'name' => $module->getInfo()->name,
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'item_page_layout' => $values['item_page_layout'],
                    'item_page_placeholder' => $values['item_page_placeholder'],
                ]);
                $moduleRowId = $db->lastInsertId();

                $moduleTemplate = new ModuleTemplate(
                    "{$moduleName}:{$moduleRowId}",
                    'auto_run',
                    ''
                );

                $templates = [
                    $values['main_page_placeholder'] => $moduleTemplate->buildOverload(),
                ];

                $templates[$values['main_page_placeholder']]['noadmin'] = '0';

                $pageId = $this->createPage(
                    $module->getInfo()->name,
                    $module->getInfo()->title,
                    serialize( $templates ),
                    $values['main_page_layout']
                );

                // Создаем папку для контента к корневой директории
                $textFolderId = (new \Data\Folder\Table())->create(
                    $module->getInfo()->title,
                    $this->app->getVar('db_text_mgr_root_id' )
                );

                $db->update(
                    'modules',
                    [
                        'main_page_id' => $pageId,
                        'item_text_folder_id' => $textFolderId,
                    ],
                    "id = {$moduleRowId}"
                );

                $module->onInstall( $moduleRowId );
                $db->commit();

                return $widgets->messageBox("Модуль '{$moduleName}' ({$moduleRowId}) установлен")
                    . $this->showList();
            } catch ( \Exception $e ) {
                $db->rollBack();
                $errors[] = $e->getMessage();
            }
        }

        if( !empty( $errors )) {
            $content .= $widgets->errorList( $errors );
        }

        $content .= $widgets->tableHeader( 'Мастер установки' );
        $content .= $form->buildTable( $builder, "Применить" );

        return $content;
    }

    function uninstallAction() {
        $widgets = new WidgetsView();
        $moduleId = $this->parameters->idx;

        if( empty( $moduleId )) {
            return $widgets->error('Module id is not set');
        }

        $moduleTable = new Table();
        $moduleRow = $moduleTable->fetchRow('id = '.intval( $moduleId ));

        if( empty( $moduleRow )) {
            return $widgets->error('Module row is not found');
        }

        $moduleName = $moduleRow['name'];
        $moduleId = $moduleRow['id'];
        $pageId = $moduleRow['main_page_id'];
        $textFolderId = $moduleRow['item_text_folder_id'];
        $content = '';

        try {
            $module = ModuleFactory::loadModule($this->app->rootDir('Module', $moduleName), $this->app);

            if( !empty( $module )) {
                $module->onUninstall( $moduleId );
            }

            if( !empty( $pageId )) {
                $page = Page::createById( $pageId, new \Data\Page\Table());
                $deleted = $page->deletePage( true, true );
                $content .= $widgets->messageBox("<b>{$deleted}</b> страниц было удалено" );
            }

            if( !empty( $textFolderId )) {
                $folderTable = new \Data\Folder\Table();
                $folders = $folderTable->findChildren( $textFolderId );
                $folders[] = $textFolderId;
                $folderTable->delete('id IN ('.join(',', $folders).')' );
            }

            $moduleRow->delete();
            $content .= $widgets->messageBox("Модуль '{$moduleName}' ({$moduleId}) удален");
        } catch (\Exception $e) {
            $content .= $widgets->error( $e );
        }

        $content .= $this->showList();

        return $content;
    }

    function editAction() {
        $widgets = new WidgetsView();
        $moduleId = $this->parameters->idx;

        if( empty( $moduleId )) {
            return $widgets->error('Module id is not set');
        }

        $moduleTable = new Table();
        $moduleRow = $moduleTable->fetchRow('id = '.intval( $moduleId ));
        $moduleName = $moduleRow['name'];

        if( empty( $moduleRow )) {
            return $widgets->error("Модуль с ID = '{$moduleId}' не найден");
        }

        $form = new ModuleSettingsForm();
        $form->addHidden('i', 'edit' );
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        $isSubmit = !empty( $_POST['form_submit'] );
        $content = '';

        unset( $form->items['main_page_layout'] );
        unset( $form->items['main_page_placeholder'] );

        if( $isPost ) {
            $processing = new FormProcessing( $form->items );
            $processing->process( $this->app->in() );
            $values = $processing->getValues();

            $errors = $processing->getErrors();

            if( $isPost && $isSubmit && empty( $errors )) {
                $this->app->db()->update('modules', [
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'item_page_layout' => $values['item_page_layout'],
                    'item_page_placeholder' => $values['item_page_placeholder'],
                ], "id = {$moduleId}");

                return $widgets->messageBox("Модуль '{$moduleName}' ({$moduleId}) обновлен")
                    . $this->showList();
            } else {
                $content .= $widgets->errorList( $errors );
            }
        } else {
            $values = $moduleRow->toArray();
        }

        $form->createRows( $this->app, $values );

        $builder = new TableBuilder();
        $content .= $widgets->tableHeader( 'Мастер установки' );
        $content .= $form->buildTable( $builder, "Применить" );

        return $content;
    }


    /**
     * @param string $name
     * @param string $title
     * @param string $templates
     * @param string $layout
     * @return int
     */
    private function createPage( $name, $title, $templates, $layout ) {
        $parent = 0;
        $values = [
            'parent' => $parent,
            'in_module' => 1,
            'pagetitle' => $title,
            'longtitle' => $title,
            'alias' => strtolower( $name ),
            'published' => 0,
            'isfolder' => 1,
            'layout' => $layout,
            'templates' => $templates,
        ];

        $values['menu_pos'] = (int) $this->app->db()->select()
            ->from(['st' => 'site_tree'], ['maxpos' => new \Zend_Db_Expr('COUNT(id)')])
            ->where('st.parent = '.$parent )
            ->query()
            ->fetchColumn(0)
        ;

        $pageTable = new \Data\Page\Table();
        return $pageTable->insert( $values );
    }
}

