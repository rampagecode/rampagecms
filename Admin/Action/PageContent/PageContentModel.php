<?php

namespace Admin\Action\PageContent;

use Admin\Action\PageContent\InfoFactory\FormFactory;
use Admin\Action\PageContent\InfoFactory\SelectGroupFactory;
use Admin\Action\PageContent\InfoFactory\SelectLayoutFactory;
use Admin\Action\PageContent\InfoFactory\TableFactory;
use Admin\Action\PageContent\TemplatesFactory\OverriddenRowFactory;
use Admin\Action\PageContent\TemplatesFactory\TemplatesFactoryDelegate;
use Admin\Action\PageContent\TemplatesFactory\TemplatesFactoryView;
use Admin\Action\PageContent\TemplatesFactory\TemplatesTableFactory;
use Admin\Action\SiteTree\SiteTreeModel;
use Admin\ButtonsView;
use Admin\AdminException;
use Admin\WidgetsView;
use App\AppException;
use App\AppInterface;
use App\Module\ModuleInfo;
use App\Module\ModuleReader;
use App\Page\ContentTemplate;
use App\Page\Page;
use App\Page\PageLayout;
use App\Page\PageParser;
use App\Page\PageTemplate;
use App\UI\FormBuilder;
use App\UI\FormSelectOptions;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;
use App\UI\TableBuilder;
use App\User\User;
use Data\DataException;
use Data\Page\Table;
use Lib\FormProcessing;
use Sys\Database\DatabaseInterface;
use Sys\Input\InputInterface;

class PageContentModel implements TemplatesFactoryDelegate {

    /**
     * @var DatabaseInterface
     */
    private $db;

    public function __construct( DatabaseInterface $db ) {
        $this->db = $db;
    }

    /**
     * Получаем путь к данной страницы
     * @param int $id
     * @param DatabaseInterface $db
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    function getPagePath( $id, DatabaseInterface $db ) {
		$aliases = array();
		$pagesQuery = $db->select()->from('site_tree')->query();

		while( $r = $pagesQuery->fetch() ) {
			$aliases[ $r['id'] ] = array( 'parent' => $r['parent'], 'alias' => $r['alias'] );
		}

		$path = array();

		while( $node = $aliases[ $id ] ) {
			$path[] = $node['alias'];
			$id = $node['parent'];
		}

		return count( $path ) ? ( '/' . implode( '/', array_reverse( $path )) . '/' ) : '/';
	}

    /**
     * @param AppInterface $app
     * @param Page $page
     * @param bool $simplePage
     * @return string
     */
    function buildPageSettings( AppInterface $app, Page $page, $simplePage = false ) {
        $content = '';
        $widgets = new WidgetsView();

        if( $page->getId() == 0 ) {
            if( ! $simplePage ) {
                $content .= $widgets->tableHeader( 'Создание <i>новой</i> страницы' );
            } else {
                $content .= $widgets->tableHeader( 'Создание новой <i>текстовой</i> страницы' );
            }
        } else {
            // Проверяем ссылки
            if( $page->isLink() ) {
                try {
                    $linkId = $page->getLink();
                    $link = $app->pages()->pageAddressById( $linkId );
                    $text = 'Эта страница ссылается на страницу <b>'.$link.'</b> (id:'.$linkId.')';
                    $content .= $widgets->messageBox( $text );
                } catch( \Exception $e ) {
                    $content .= $widgets->error( $e->getMessage() );
                }
            }

            $content .= $widgets->tableHeader( 'Параметры страницы' );
        }

        $builder = new TableBuilder();
        $form = new PageSettingsForm();
        $form->createRows( $app, $page, $simplePage );
        $rowsToHide = [];

        if( $simplePage ) {
            $rowsToHide = $form->addSelectContentRows( $app, $page );
        }

        $content .= $form->buildTable( $builder, 'Сохранить' );
        $content .= '<script>toggleViews("'.implode('", "', $rowsToHide).'")</script>';

        return $content;
    }

    /**
     * @param AppInterface $app
     * @param int $page_id
     * @param int $parent_id
     * @return string
     */
    function buildPageTemplates( AppInterface $app, $page_id, $parent_id ) {
        $widgets = new WidgetsView();

        try {
            $page = Page::createById( $page_id, new Table() );
            $templates = $this->parseLayout( $app, $page );

            if( empty( $templates )) {
                throw new AdminException( 'В данном макете нет ни одного шаблона' );
            }

            // Получаем список модулей
            $reader = new ModuleReader();
            $moduleFiles = $reader->readDirForModules( $app->rootDir('Module'), $app );
            $nonInstallableModules = array_filter( $moduleFiles, function( $module ) { return !$module->isInstallable(); });
            $mods = array_map( function( $value ) { return $value->getInfo(); }, $nonInstallableModules );

            $installedModules = $app->db()->select()->from('modules')->query()->fetchAll();
            foreach( $installedModules as $installed ) {
                $mods[] = new ModuleInfo(
                    $installed['name'].':'.$installed['id'],
                    $installed['title'],
                    $installed['description']
                );
            }
        } catch( \Exception $e ) {
            return $widgets->error( $e->getMessage() );
        }

        // Строим список перегруженных шаблонов
        $rows = '';
        $mod_names = [];

        foreach( $templates as $template ) {
            if( ! $template->isOverloaded() ) {
                continue;
            }

            try {
                $overriddenRow = new OverriddenRowFactory( $page_id, $template, $this, $mods );
                $rows .= $overriddenRow->make();
                $mod_names[] = $template->getPlaceholder();
            } catch( \Exception $e ) {
                $rows .= $widgets->error( $e->getMessage() );
                continue;
            }
        }

        $view = new TemplatesFactoryView();

        if( empty( $rows )) {
            $rows .= $view->onErrorRow('Пусто' );
        } else {
            $rows .= $view->submitRow();
        }

        $hiddens = array(
            'i'			 => 'saveSkin',
            'x'			 => $page_id,
            'pid'		 => $parent_id,
            'layout'	 => $page->getLayout(),
            'mod_names'  => implode( ',', $mod_names ),
        );

        $templatesTable = new TemplatesTableFactory( $page_id, $templates, $this );

        $out = $widgets->tableHeader('Перегруженные шаблоны');
        $out .= $view->tableBody( $rows, $hiddens );
        $out .= '<div>&nbsp;</div>';
        $out .= $widgets->tableHeader( 'Стандартные шаблоны' );
        $out .= $templatesTable->make();

        return $out;
    }

    /**
     * @param int $id
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    function getTextTitle( $id ) {
        if( ! empty( $id )) {
            $textRow = $this->db->select()
                ->from('vfs_texts')
                ->where('id = ?', $id )
                ->query()
                ->fetch();

            if( ! empty( $textRow ) && ! empty( $textRow['id'] )) {
                return str_replace('_', ' ', $textRow['title']);
            }
        }

        return 'ВНИМАНИЕ! ТЕКСТ НЕ НАЙДЕН';
    }

    /**
     * @param AppInterface $app
     * @param int $page_id
     * @param string $tpl_name
     * @return void
     * @throws AdminException
     * @throws AppException
     * @throws DataException
     */
    function overloadTemplate( AppInterface $app, $page_id, $tpl_name ) {
        $pageTable = new Table();
        $page = Page::createById( $page_id, $pageTable );
        $templates = $this->parseLayout( $app, $page );
        $overload = null;

        foreach( $templates as $template ) {
            if( $template->getPlaceholder() == $tpl_name ) {
                if( $template->isOverloaded() ) {
                    throw new AdminException('Этот шаблон уже перегружен - '.$tpl_name );
                }

                $overload = $template->getTemplate()->buildOverload();
                break;
            }
        }

        if( empty( $overload )) {
            throw new AdminException('Шаблон не найден в макете - '.$tpl_name );
        }

        $overloads = $pageTable->getTemplates( $page_id );

        if( isset( $overloads[ $tpl_name ] )) {
            throw new AdminException('Этот шаблон уже был перегружен - '.$tpl_name );
        }

        $overloads[ $tpl_name ] = $overload;
        $pageTable->setTemplates( $overloads, $page_id );
    }

    /**
     * @param int $page_id
     * @param string $tpl_name
     * @return void
     * @throws AdminException
     * @throws DataException
     */
    function unloadTemplate( $page_id, $tpl_name ) {
        $pageTable = new Table();
        $overloads = $pageTable->getTemplates( $page_id );

        if( ! isset( $overloads[ $tpl_name ] )) {
            throw new AdminException( 'Этот шаблон не перегружен - '.$tpl_name );
        }

        unset( $overloads[ $tpl_name ] );
        $pageTable->setTemplates( $overloads, $page_id );
    }

    /**
     * @param AppInterface $app
     * @param $page_id
     * @param $parent_id
     * @return string
     * @throws \Zend_Db_Select_Exception
     */
    function savePageParams( AppInterface $app, &$page_id, &$parent_id, &$values ) {
        $widgets = new WidgetsView();

        // Если мы не знаем ID родителя страницы, но знаем ID самой странице, то узнаем родителя
        if( $parent_id == 0 && $page_id != 0 ) {
            try {
                $parent_id = \App\Page\Page::createById( $page_id, new Table() )->getParentId();
            } catch (\Exception $e) {
                $result = $widgets->errorList( $e->getMessage() );
                $result .= '<!--@@'.json_encode([ 'success' => 'false' ]).'@@-->';

                return $result;
            }
        }

        $form = new FormProcessing(( new PageSettingsForm() )->items );
        $form->process( $app->in() );
        $errors = $form->getErrors();
        $values = $form->getValues();

        if( empty( $errors )) {
            try {
                $this->checkPageAlias( $values['alias'], $page_id, $parent_id, $app->getVar('illegal_aliases' ));
            } catch( AdminException $e ) {
                $errors[] = $e->getMessage();
            }
        }

        if( count( $errors )) {
            $result = $widgets->errorList( $errors );
            $result .= '<!--@@'.json_encode([ 'success' => 'false' ]).'@@-->';

            return $result;
        }

        //-----------------------------------------
        // Создаем новую запись или обновляем существующую
        //-----------------------------------------

        $values['admin_groups_access'] = serialize( $values['admin_groups_access'] );

        if( $page_id ) {
            $newPage = false;
            $app->db()->update( 'site_tree', $values, 'id='.$page_id );
        } else {
            $newPage = true;
            $page_id = $this->createPage( $parent_id, $values );

            // Создаем простой текстовый контент, если нужно
            if( $app->in( 'create_content' ) == 1 ) {
                try {
                    $placeholderKey = $values['layout'].'_placeholder';
                    $placeholder = $app->in( $placeholderKey );

                    if( empty( $placeholder )) {
                        throw new AdminException( "Контент не был создан так как в макете нет места для контента." );
                    }

                    // Для контента нужна директория, проверяем есть ли подходящая
                    $content_dir = $this->findContentDirectory( $parent_id, $app->getVar('db_text_mgr_root_id' ));

                    // Если новая страница - директория, то создаем для нее папку
                    if( $values['isfolder'] ) {
                        $content_dir = (new \Data\Folder\Table())->create( $values['pagetitle'], $content_dir );
                    }

                    // Создаем контент
                    $content_id = $this->createPageContent( $placeholder, $page_id, $values['pagetitle'], $content_dir );
                    $page_templates = [ $placeholder => array( 'type' => 'content', 'id' => $content_id )];

                    // Перегружаем шаблоны на странице
                    (new Table())->setTemplates( $page_templates, $page_id );
                } catch ( \Exception $e ) {
                    $result = $widgets->error($e->getMessage() );
                    $result .= '<!--@@'.json_encode([ 'success' => 'false' ]).'@@-->';

                    return $result;
                }
            }
        }

        if( $newPage ) {
            $result = $widgets->messageBox('Новая страница успешно создана');
        } else {
            $result = $widgets->messageBox('Параметры страницы были успешно обновлены');
        }

        $values['id'] = $page_id;
        $model = new SiteTreeModel();

        $ajaxParams = $model->convertToJs( $values, $app->pages() );
        $ajaxParams['success'] 	= true;

        $result .= '<!--@@'.json_encode( $ajaxParams ).'@@-->';

        return $result;
    }

    /**
     * @param int $id
     * @param int $db_text_mgr_root_id
     * @return int
     * @throws AdminException
     * @throws DataException
     * @throws \Zend_Db_Statement_Exception
     */
    private function findContentDirectory( $id, $db_text_mgr_root_id ) {
        $v = $this->findParentDirectories( $id );
        krsort( $v );

        $parent_folder = $db_text_mgr_root_id;

        foreach( $v AS $page ) {
            $row = $this->db->select()
                ->from('vfs_folders')
                ->where('parent = ?', $parent_folder )
                ->where('name = ?', $page['pagetitle'] )
                ->query()
                ->fetch()
            ;

            if( empty( $row )) {
                $parent_folder = (new \Data\Folder\Table())->create( $page['pagetitle'], $parent_folder );
            } else {
                $parent_folder = $row['id'];
            }
        }

        return $parent_folder;
    }

    /**
     * @param int $id
     * @return int[]
     */
    private function findParentDirectories( $id ) {
        if( $r = (new Table())->fetchRow('id = '.$id )) {
            $x = array();
            $v = $this->findParentDirectories( $r['parent'] );
            $x[] = $r;

            foreach( $v AS $vr ) {
                $x[] = $vr;
            }

            return $x;
        } else {
            return [];
        }
    }

    /**
     * @param string $alias
     * @param int $page_id
     * @param int $parent_id
     * @param string[] $illegal_aliases
     * @return void
     * @throws AdminException
     */
    function checkPageAlias( $alias, $page_id, $parent_id, $illegal_aliases ) {
        if( ! preg_match( "/^[_0-9a-zA-Z\-]+$/", $alias )) {
            throw new AdminException( 'Альяс не корректен' );
        }

        // Некоторые альясы недопустимо использовать в корневой директории
        if( $parent_id == 0 ) {
            if( in_array( $alias, $illegal_aliases )) {
                throw new AdminException( 'Этот альяс зарезервирован системой, вы не можете его использовать. Пожалуйста, придумайте другой.' );
            }
        }

        // Проверка на уникальность альяса
        $pageRow = (new Table())->fetchRow("alias = '{$alias}' AND parent = {$parent_id}");

        if( ! empty( $pageRow )) {
            $pageInfo = $pageRow->toArray();

            if( $page_id != $pageInfo['id'] ) {
                throw new AdminException( "Страница '{$pageInfo['pagetitle']} ({$pageInfo['id']})' уже имеет такой альяс" );
            }
        }
    }

    /**
     * @param int $parent_id
     * @param array $values
     * @return int
     */
    private function createPage( $parent_id, $values ) {
        $values['parent'] = $parent_id;
        $values['in_module'] = 0;
        $values['menu_pos'] = (int) $this->db->select()
            ->from(['st' => 'site_tree'], ['maxpos' => new \Zend_Db_Expr('COUNT(id)')])
            ->where('st.parent = '.$parent_id )
            ->query()
            ->fetchColumn(0)
        ;

        $pageTable = new Table();

        // Создаем страницу
        $page_id = $pageTable->insert( $values );

        // Объявляем родительскую страницу каталогом
        $pageTable->update(['isfolder' => 1 ], 'id = '.$parent_id );

        //Возвращаем идентификатор созданной страницы
        return $page_id;
    }

    /**
     * @param string $placeholder
     * @param int $page_id
     * @param int $page_title
     * @param string $content_dir
     * @return int
     */
    private function createPageContent( $placeholder, $page_id, $page_title, $content_dir ) {
        $dummy_text = 'Извините, данная страница находится в стадии наполнения.';
        $title = $page_title . " ({$placeholder})";
        $table = new \Data\Text\Table();
        $content_id = $table->create( $title, $content_dir, $dummy_text );

        // Связываем контент со страницей
        $table->addPage( $content_id, $page_id );

        return $content_id;
    }

    /**
     * @param InputInterface $in
     * @param int $page_id
     * @return string
     * @throws DataException
     */
    function saveSkin( InputInterface $in, $page_id ) {
        $widgets = new WidgetsView();
        $mod_names = $in['mod_names'];
        $templates = explode( ',', $mod_names );
        $layout = $in['layout'];

        try {
            if( ! preg_match( "/^[_0-9a-zA-Z\-]+$/", $layout )) {
                throw new AdminException( "Некорректное имя макета - '{$layout}'" );
            }

            if( ! count( $templates )) {
                throw new AdminException( "Макет '{$layout}' не найден" );
            }
        } catch ( AdminException $e ) {
            return $widgets->error( $e->getMessage() );
        }

        $mods = array();

        foreach( $templates AS $atom ) {
            $atom_hash = md5($atom);
            $text_id = intval( $in[ $atom_hash . '_id'] );

            (new \Data\Text\Table())->addPage( $text_id, $page_id );

            if( $in[ $atom_hash . '_type' ] == 'module' ) {
                $mods[ $atom ][ 'type' ] = 'module';
                $mods[ $atom ][ 'mod' ] = $in[ $atom_hash . '_mod' ];
                $mods[ $atom ][ 'var' ] = $in[ $atom_hash . '_var' ];
                $mods[ $atom ][ 'act' ] = $in[ $atom_hash . '_act' ];
            }
            elseif( $in[ $atom_hash . '_type' ] == 'content' ) {
                $mods[$atom]['type'] = 'content';
                $mods[$atom]['id'] = $in[ $atom_hash . '_id' ];
            }

            $mods[$atom]['noadmin'] = $in[ $atom_hash . '_noadmin' ];
        }

        (new \Data\Page\Table())->setTemplates( $mods, $page_id );

        return $widgets->messageBox('Информация была успешно обновлена' );
    }

    /**
     * @param AppInterface $app
     * @param Page $page
     * @return PageTemplate[]
     * @throws AppException
     */
    function parseLayout( $app, $page ) {
        $path = $app->rootDir( 'Public', 'Layouts', $page->getLayout() . '.html');
        $layout = new PageLayout( $path );
        $parser = new PageParser( $app, $page );
        $content = $layout->load();
        $templates = $parser->parseTemplates( $page->getId(), $content, $page->getTemplates() );

        return $templates;
    }
}