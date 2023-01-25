<?php

namespace Admin\Action\PageContent;

use Admin\AdminException;
use App\AppInterface;
use App\Page\Page;
use App\Page\PageLayout;
use App\Page\PageParser;
use App\UI\FormBuilder;
use App\UI\FormSelectOptions;
use App\User\User;
use Data\Page\Table;
use Sys\Database\DatabaseInterface;

class PageSettingsForm extends FormBuilder {
    public $items = [];

    public function __construct() {
        parent::__construct('');

        $this->items = [
            'pagetitle' => new \Lib\FormItem\String('pagetitle', 'Заголовок страницы', true ),
            'longtitle' => new \Lib\FormItem\String('longtitle', 'Подзаголовок' ),
            'keywords' => new \Lib\FormItem\String('keywords', 'Ключевые слова' ),
            'alias' => new \Lib\FormItem\String('alias', 'Альяс', true ),
            'layout' => new \Lib\FormItem\String('layout', 'Макет', true ),
            'searchable' => new \Lib\FormItem\Bool('searchable', 'Участвует в поиске' ),
            'published' => new \Lib\FormItem\Bool('published', 'Опубликована' ),
            'hidden' => new \Lib\FormItem\Bool('hidden', 'Скрыта' ),
            'only_user' => new \Lib\FormItem\Bool('only_user', 'Спец. страница' ),
            'show_in_admin_menu' => new \Lib\FormItem\Bool('show_in_admin_menu', 'Показывать в меню в панели' ),
            'show_in_public_menu' => new \Lib\FormItem\Bool('show_in_public_menu', 'Показывать в меню на сайте' ),
            'is_link' => new \Lib\FormItem\Bool('is_link', 'Ссылка' ),
            'isfolder' => new \Lib\FormItem\Bool('isfolder', 'Директория' ),
            'admin_groups_access' => new \Lib\FormItem\IntArray('admin_groups_access', 'Группы пользователей'),
        ];
    }

    /**
     * @param AppInterface $app
     * @param Page $page
     * @param bool $simplePage
     * @return $this
     */
    function createRows( AppInterface $app, Page $page, $simplePage = false ) {
        $this->addInput( $this->items['pagetitle'], $page->getTitle() );
        $this->addInput( $this->items['longtitle'], $page->getLongTitle(), 'Необязательно' );
        $this->addInput( $this->items['keywords'], $page->getKeywords(), 'META-тэг только для этой страницы' );
        $this->addInput( $this->items['alias'], $page->getAlias(), 'Элемент адреса, определяющий эту страницу. По-английски, без пробелов.' );

        try {
            $layoutOptions = $this->getLayoutSelectOptions( $app->rootDir('Public', 'Layouts' ));
            $this->addSelect( $this->items['layout'], $layoutOptions, $this->getLayoutByDefault( $page ));
        } catch( \Exception $e ) {
            $this->addText( $this->items['layout'], $e->getMessage() );
        }

        $this->addToggle(
            $this->items['searchable'],
            $page->isSearchable() ? true : $simplePage,
            'Для внутреннего поиска по сайту. Это не затрагивает поисковые системы'
        );

        $this->addToggle( $this->items['published'], $page->isPublished(), 'Неопубликованные страницы видны в навигационном меню системы управления, но скрыты в меню на сайте' );
        $this->addToggle( $this->items['hidden'], $page->isHidden(), 'Страница будет недоступна на сайте' );
        $this->addToggle( $this->items['only_user'], $page->isUserOnly(), 'Доступна только зарегистрированным пользователям' );
        $this->addToggle( $this->items['show_in_admin_menu'], $page->showInAdminMenu(), 'Страница будет отображаться в Навигационном меню в Панели управления' );
        $this->addToggle( $this->items['show_in_public_menu'], $page->showInPublicMenu(), 'Страница будет отображаться наа сайте в меню сгенерированных модулем Меню');
        $this->addToggle( $this->items['is_link'], $page->isLink(), 'Использовать эту страницу как ссылку' );
        $this->addToggle( $this->items['isfolder'], $page->isFolder() );

        try {
            $groupOptions = $this->getUserGroupSelectOptions( $app->db(), $app->getVar('admin_group' ));
            $groupValues = $this->getUserGroupSelectValues( $page, $app->getUser(), $app->getVar('admin_group' ));

            $this->addMultiSelect(
                $this->items['admin_groups_access'],
                $groupOptions,
                $groupValues,
                'Кто имеет право редактировать содержимое этих страниц'
            );
        } catch( \Exception $e ) {
            $this->addText( $this->items['admin_groups_access'], $e->getMessage() );
        }

        $this->makeSimple('');
        $this->addHidden('i', 'saveParams' );
        $this->addHidden('x', $page->getId() );
        $this->addHidden('pid', $page->getParentId() );

        if( $simplePage ) {
            $this->addHidden('hidden', 0 );
            $this->addHidden('deleted', 0 );
            $this->addHidden('create_content', 1 );
        }

        return $this;
    }

    function addSelectContentRows( AppInterface $app, Page $page ) {
        $layoutBuilder = $this->getRowByFormItemName('layout')->builder;
        $layoutOptions = $this->getLayoutSelectOptions( $app->rootDir( 'Public', 'Layouts' ));
        $parser = new PageParser( $app, $page );
        $contentRows = [];
        $rowsToHide = [];

        foreach( $layoutOptions->getOptions() as $title => $name ) {
            if( empty( $name )) {
                continue;
            }

            $formName = "{$name}_placeholder";
            $contentRows[] = $formName;
            $formItem = new \Lib\FormItem\String( $formName, 'Контент' );
            $formItem->rowId = $formName;
            $path = $app->rootDir( 'Public', 'Layouts', $name . '.html');
            $templateOptions = $this->getTemplateSelectOptions( $path, $parser );
            $this->addSelect( $formItem, $templateOptions );

            $layoutRowPosition = $this->getRowPositionByFormItemName('layout');
            $contentRowPosition = $this->getRowPositionByFormItemName( $formName );
            $this->changeRowPosition( $contentRowPosition, $layoutRowPosition + 1 );

            if( $name != $layoutBuilder->getValue() ) {
                $rowsToHide[] = $formName;
            }
        }

        $layoutBuilder->onChange = "toggleViews('".implode("','", $contentRows)."')";

        return $rowsToHide;
    }

    /**
     * @param $path
     * @param PageParser $parser
     * @return FormSelectOptions
     * @throws \App\AppException
     */
    private function getTemplateSelectOptions( $path, PageParser $parser ) {
        $layout = new PageLayout( $path, $parser );
        $content = $layout->load();
        $templates = $parser->parseTemplates(null, $content, []);

        $options = new FormSelectOptions();

        foreach( $templates as $tpl ) {
            $options->addOption( $tpl->getPlaceholder(), $tpl->getPlaceholder() );
        }

        return $options;
    }

    /**
     * @param string $path
     * @return FormSelectOptions
     * @throws AdminException
     */
    private function getLayoutSelectOptions( $path ) {
        $options = new FormSelectOptions();
        $foundPublicLayout = false;
        $foundSystemLayout = false;

        if( $dir = @opendir( $path )) {
            while(( $file = readdir( $dir )) !== false ) {
                if( $file != '.' && $file != '..' && $file[0] != "." && is_file( $path . DIRECTORY_SEPARATOR . $file )) {
                    if( $file[0] == "_" ) {
                        $foundSystemLayout = true;
                    } else {
                        $foundPublicLayout = true;
                        $title = $value = str_replace( '.html', '', $file );
                        $title = ucwords( str_replace( '_', ' ', $title ));
                        $options->addOption( $title, $value );
                    }
                }
            }
        } else {
            throw new AdminException("Не удалось открыть директорию '{$path}'");
        }

        if( ! $foundPublicLayout ) {
            if( $foundSystemLayout ) {
                throw new AdminException("В директории '{$path}' не найдены публичные макеты страниц (у которых имя файла не начинается с подчеркивания)");
            } else {
                throw new AdminException("В директории '{$path}' не найдены макеты страниц");
            }
        }

        return $options;
    }

    /**
     * @param DatabaseInterface $db
     * @param int $adminGroup
     * @return FormSelectOptions
     */
    private function getUserGroupSelectOptions( DatabaseInterface $db, $adminGroup ) {
        $mem_group = $db->select()
            ->from('groups' )
            ->where('g_id <> ?', $adminGroup )
            ->order('g_title')
            ->query()
            ->fetchAll()
        ;

        $options = new FormSelectOptions();

        foreach( $mem_group as $group ) {
            $options->addOption( $group['g_title'], $group['g_id'] );
        }

        return $options;
    }

    /**
     * @param Page $page
     * @param User $user
     * @param int $adminGroup
     * @return int[]
     */
    private function getUserGroupSelectValues( Page $page, User $user, $adminGroup ) {
        $userGroupIDs = [];

        if( $page->getId() != 0 ) {
            $userGroupIDs = $page->getAdminGroupAccess();
        } else {
            // Для новой страницы имеет доступ та группа, пользователь которой её создает
            if( $user->getGroup() != $adminGroup ) {
                // Супер-админ и так имеет полный доступ
                $userGroupIDs[] = $user->getGroup();
            }
        }

        return $userGroupIDs;
    }

    /**
     * Если макет не задан, то устанавливаем как у родительской страницы
     * @return string
     */
    private function getLayoutByDefault( Page $page ) {
        $pageLayout = $page->getLayout();

        if( empty( $pageLayout )) {
            try {
                $parentPage = Page::createById( $page->getParentId(), new Table() );
                $pageLayout = $parentPage->getLayout();
            } catch (\Exception $e) {

            }
        }

        return $pageLayout;
    }
}