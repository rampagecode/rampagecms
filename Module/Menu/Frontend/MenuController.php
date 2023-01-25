<?php

namespace Module\Menu\Frontend;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Page\Page;
use Data\Page\Table;

class MenuController implements ModuleControllerProtocol {

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var array
     */
    private $tree = [];

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;

        include $this->app->rootDir('conf', 'cache.tree.php');

        if( isset( $site_tree )) {
            $this->tree = $site_tree;
        }
    }

    public function auto_run() {
        return $this->topLevel();
    }

    public function topLevel() {
        $pageTable = new Table();
        $pageRows = $pageTable->fetchAll('parent = 0 AND show_in_public_menu = 1', 'menu_pos ASC');
        $view = new MenuView();
        $rows = [];
        $currentPageId = $this->app->getCurrentSitePageId();

        foreach( $pageRows as $row ) {
            $page = Page::createByRow( $row );
            $title = $page->getTitle();
            $url = $this->tree[ $page->getId() ];

            if( $page->getId() == $currentPageId || $page->isParentFor( $currentPageId )) {
                $rows[] = $view->activeRow( $title, $url );
            } else {
                $rows[] = $view->inactiveRow( $title, $url );
            }
        }

        return $view->rowsWrapper( join('', $rows ));
    }

    public function breadcrumbs() {
        $currentPageId = $this->app->getCurrentSitePageId();
        $pageTable = new Table();
        $pageRows = $pageTable->fetchAll("parent < {$currentPageId}", 'parent DESC')->toArray();
        $lastPage = array_filter(
            $pageRows,
            function( $row ) use ($currentPageId) { return $row['id'] == $currentPageId; }
        );

        if( empty( $lastPage )) {
            return '';
        }

        $lastPage = array_shift( $lastPage );
        $pageStack = [ $lastPage ];

        while( $lastPage['parent'] > 0 ) {
            $lastPage =  array_filter(
                $pageRows,
                function( $row ) use ($lastPage) { return $row['id'] == $lastPage['parent']; }
            );

            if( empty( $lastPage )) {
                break;
            }

            $lastPage = array_shift( $lastPage );
            $pageStack[] = $lastPage;
        }

        $pageStack = array_reverse( $pageStack );
        $view = new MenuView();
        $links = [];

        foreach( $pageStack as $pageData ) {
            $page = new Page();
            $page->setFromArray( $pageData );
            $links[] = $view->breadcrumbLink( $page );
        }

        return join('/', $links );
    }

}