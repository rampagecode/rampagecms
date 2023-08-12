<?php

namespace App\Page;

use Admin\Action\PageContent\PageContentModel;
use App\AppException;
use App\AppManager;
use App\Module\ModuleFactory;
use Data\Page\Table;
use Module\ModuleException;

class Page {
    protected $id;
    protected $pagetitle;
    protected $longtitle;
    protected $alias;
    protected $published;
    protected $parent;
    protected $isfolder;
    protected $templates;
    protected $layout;
    protected $searchable;
    protected $cacheable;
    protected $createdby;
    protected $createdon;
    protected $editedby;
    protected $editedon;
    protected $deleted;
    protected $deletedon;
    protected $deletedby;
    protected $publishedon;
    protected $publishedby;
    protected $donthit;
    protected $haskeywords;
    protected $hasmetatags;
    protected $menu_pos;
    protected $admin_pr;
    protected $keywords;
    protected $admin_groups_access;
    protected $hidden;
    protected $in_module;
    protected $link;
    protected $is_link;
    protected $only_user;
    protected $show_in_admin_menu;
    protected $show_in_public_menu;

    /**
     * @param int id
     * @param Table $table
     * @return Page
     * @throws AppException
     */
    public static function createById( $id, Table $table ) {
        $id = intval( $id );

        if( $id > 0 ) {
            if( $r = $table->fetchRow( $table->select()->where('id = ?', $id ))) {
                return self::createByRow( $r );
            }
            else {
                throw new AppException('No record found');
            }
        } else {
            throw new AppException('No record found');
        }
    }

    /**
     * @param \Data\Page\Row $row
     * @return Page
     */
    public static function createByRow( \Data\Page\Row $row ) {
        $obj = new self();
        return $obj->setFromArray( $row->toArray() );
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setFromArray( $values ) {
        foreach( $values as $var => $value ) {
            $this->$var = $value;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        return (bool) $this->hidden;
    }

    /**
     * @return bool
     */
    public function isDeleted() {
        return (bool) $this->deleted;
    }

    /**
     * @return bool
     */
    public function isFolder() {
        return (bool) $this->isfolder;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->pagetitle;
    }

    /**
     * @return int
     */
    public function getId() {
        return intval( $this->id );
    }

    /**
     * @return bool
     */
    public function isLink() {
        return (bool) $this->is_link;
    }

    /**
     * @return int|null
     * @throws AppException
     */
    public function getLink() {
        if( $this->isLink() ) {
            if( $this->link == '' ) {
                throw new AppException('Эта страница обозначена как ссылка, но сама ссылка не была создана');
            } else {
                return $this->link;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getLayout() {
        return (string) $this->layout;
    }

    /**
     * @return string
     */
    public function getLongTitle() {
        return (string) $this->longtitle;
    }

    /**
     * @return string
     */
    public function getKeywords() {
        return (string) $this->keywords;
    }

    /**
     * @return string
     */
    public function getAlias() {
        return (string) $this->alias;
    }

    /**
     * @return bool
     */
    public function isSearchable() {
        return (bool) $this->searchable;
    }

    /**
     * @return bool
     */
    public function isCacheable() {
        return (bool) $this->cacheable;
    }

    /**
     * @return bool
     */
    public function isPublished() {
        return (bool) $this->published;
    }

    /**
     * @return string
     */
    public function getTemplates() {
        return (string) $this->templates;
    }

    /**
     * @return int[]
     */
    public function getAdminGroupAccess() {
        return unserialize( $this->admin_groups_access );
    }

    /**
     * @return bool
     */
    public function isUserOnly() {
        return (bool) $this->only_user;
    }

    /**
     * @return bool
     */
    public function showInAdminMenu() {
        return (bool) $this->show_in_admin_menu;
    }

    /**
     * @return bool
     */
    public function showInPublicMenu() {
        return (bool) $this->show_in_public_menu;
    }

    /**
     * @return int
     */
    public function getParentId() {
        return (int) $this->parent;
    }

    /**
     * @param bool $kill_free_content
     * @return int
     * @throws \Data\DataException
     */
    function deletePage( $kill_free_content = false, $kill_module_pages = false ) {
        $id = $this->getId();

        if( ! $id ) {
            return 0;
        }

        $pageTable = new Table();

        // Получаем ID удаляемых страниц
        $pages = $pageTable->findAllChildPagesToDelete( $id, $kill_module_pages );

        // Смещаем позиции меню
        $pageTable->adjustMenuPositionAfterDeletingPageWithId( $id );

        // Получаем ID контента с удаляемых страниц
        $texts = $pageTable->collectContentIds( $pages );

        //-----------------------------------------
        // Удаляем из контента ID удаляемых страниц
        //-----------------------------------------

        $textTable = new \Data\Text\Table();

        if( is_array( $texts ) && count( $texts )) {
            foreach( $texts AS $text_id => $page_id ) {
                $textTable->removePage( $text_id, $page_id );

                // Удаляем контент если на него не ссылается больше ни одна страница.
                if( $text_id && $kill_free_content ) {
                    $textTable->delete(
                        new \Zend_Db_Expr("id = {$text_id} AND LENGTH( TRIM( present_at_pages )) = 0")
                    );
                }
            }
        }

        //-----------------------------------------
        // Удаляем страницы
        //-----------------------------------------

        if( is_array( $pages ) && count( $pages )) {
            $deleted = $pageTable->delete(new \Zend_Db_Expr('id IN ('. implode( ',', $pages ) .')'));
        } else {
            $deleted = 0;
        }

        return $deleted;
    }

    /**
     * @return bool
     */
    public function haveEditableChildren() {
        if( ! $this->isFolder() ) {
            return false;
        }

        $table = new Table();
        $rows = $table->fetchFirstLevelChildren( $this->getId() );
        $nextLevel = [];

        if( ! count( $rows )) {
            return false;
        }

        foreach( $rows as $row ) {
            $page = self::createByRow( $row );

            if( $page->canCurrentUserAdminThisPage() ) {
                if( $page->haveAdminOperations() ) {
                    return true;
                }
            }

            if( $page->isFolder() ) {
                $nextLevel[] = $page;
            }
        }

        if( count( $nextLevel )) {
            foreach( $nextLevel AS $nextPage ) {
                if( $nextPage->haveEditableChildren() ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canCurrentUserAdminThisPage() {
        $gAccess = $this->getAdminGroupAccess();
        $app = AppManager::getInstance();
        $userGroup = $app->getUser()->getGroup();
        $adminGroup = $app->getVar('admin_group' );

        if ( $userGroup == $adminGroup ) {
            return true;
        } else {
            if( is_array( $gAccess )) {
                return in_array( $userGroup, $gAccess );
            } else {
                return false;
            }
        }
    }

    /**
     *
     * @return bool return true if the page have any admin operations
     */
    public function haveAdminOperations() {
        if( $this->isDeleted() ) {
            return false;
        }

        if( empty( $this->templates )) {
            return false;
        }

        if( empty( $this->layout )) {
            return false;
        }

        if( ! $this->canCurrentUserAdminThisPage() ) {
            return false;
        }

        $app = AppManager::getInstance();
        $pageContent = new PageContentModel( $app->db() );
        $templates = $pageContent->parseLayout( $app, $this );
        $mods = $txts = [];

        foreach( $templates AS $content ) {
            if( ! $content->canAdmin() ) {
                continue;
            }

            $tpl = $content->getTemplate();

            switch( true ) {
                case $tpl instanceof ContentTemplate:
                    if( ! empty( $tpl->id )) {
                        $txts[] = $tpl->id;
                    }
                    break;

                case $tpl instanceof ModuleTemplate:
                    if( ! empty( $tpl->mod )) {
                        $mods[] = $tpl->mod;
                    }
                    break;
            }
        }

        //-----------------------------------------
        // Если на странице присутствует только
        // один текстовый контент, то его сразу
        // и выводим.
        //-----------------------------------------

        if( count( $mods ) == 0 AND count( $txts ) == 1 ) {
            return true;
        }

        if( count( $mods )) {
            foreach( $mods AS $mod ) {
                $path = $app->rootDir( 'Module', ucfirst( $mod ));

                try {
                    ModuleFactory::loadModule( $path, $app );
                    return true;
                } catch( AppException $e ) {
                    // does not matter
                }
            }
        }

        return count( $txts ) > 0;
    }

    function isChildOf( $pageId, $pages = [] ) {
        if( empty( $this->id )) {
            return false;
        }

        if( empty( $this->parent )) {
            return false;
        }

        if( $this->parent == $pageId ) {
            return true;
        }

        if( empty( $pages )) {
            $table = new Table();
            $pages = $table->fetchAll();
        }

        foreach( $pages as $pageRow ) {
            $page = Page::createByRow( $pageRow );

            if( $page->getId() == $this->getParentId() ) {
                return $page->isChildOf( $pageId, $pages );
            }
        }

        return false;
    }

    function isParentFor( $pageId, $pages = [] ) {
        if( empty( $this->id )) {
            return false;
        }

        if( empty( $pages )) {
            $table = new Table();
            $pages = $table->fetchAll();
        }

        foreach( $pages as $pageRow ) {
            $page = Page::createByRow( $pageRow );

            if( $page->getId() == $pageId ) {
                return $page->isChildOf( $this->getId() );
            }
        }

        return false;
    }
}