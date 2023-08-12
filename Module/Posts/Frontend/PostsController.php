<?php

namespace Module\Posts\Frontend;

use Admin\ButtonsView;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Module\ModuleFrontendController;
use App\UI\Paginator;

class PostsController extends ModuleFrontendController {

    /**
     * @return string
     */
    public function auto_run() {
        return $this->showListAction();
    }

    function showListAction() {
        $view = new PostsView();
        $buttons = new ButtonsView();
        $postsTableName = $this->moduleDelegate->getTableName( $this->moduleId );
        $postsCount = $this->app->db()->select()->from( $postsTableName, 'COUNT(id)' )->query()->fetchColumn();

        $postsPerPage = 5;
        $offset = intval( $this->app->in('posts_offset' ));
        $paginator_url = '&posts_offset=';
        $paginator = new Paginator( $offset, $postsCount, $postsPerPage, $paginator_url );

        $postRows = $this->app->db()->select()
            ->from(['m' => $this->moduleDelegate->getTableName( $this->moduleId )])
            ->join(
                ['s' => 'site_tree'],
                's.id = m.page_id',
                ['pagetitle', 'alias', 'parent']
            )
            ->join(
                ['t' => 'vfs_texts'],
                't.id = m.text_id',
                ['text_formatted']
            )
            ->order('id DESC')
            ->limitPage( $paginator->currentPage, $postsPerPage )
            ->query()
            ->fetchAll();

        $listRows = [];
        foreach( $postRows as $row ) {
            $parentPageURL = $this->app->pages->pageAddressById( $row['parent'] );            
            $postURL = $parentPageURL.$row['alias'];
            $listRows[] = $view->postListRow( $row['pagetitle'], $row['text_formatted'], $postURL );
        }

        return $view->postList(
            implode("\n", $listRows),
            $paginator->resultHTML,
            $buttons->createNew( 'create', 'Создать новый' )
        );
    }
}