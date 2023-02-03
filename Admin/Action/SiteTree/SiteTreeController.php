<?php

namespace Admin\Action\SiteTree;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Page\Page;
use Data\Page\Table;

class SiteTreeController implements ModuleControllerProtocol  {
    /**
     * @var AppInterface
     */
    private $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        return '';
    }

    function loadAction() {
        $model = new SiteTreeModel();
        $root = intval( substr( $this->app->in('node'), 8 ));
        $tree = $model->loadTree( $root, $this->app->db(), $this->app->pages() );

        return json_encode( $tree );
    }

    function buildAction() {
        $view = new SiteTreeView();
        return $view->choosePageWindow();
    }

    function menuAction() {
        $root = intval( substr( $this->app->in('node'), 8 ));
        $table = new Table();
        $rows = $table->fetchFirstLevelChildren( $root );
        $nodes = [];

        foreach( $rows as $row ) {
            $page = Page::createByRow( $row );
            $haveChildren = $page->haveEditableChildren();
            $canAdmin = $page->haveAdminOperations();
            $link = $this->app->baseURL().'&u=a&s=content&a=page&x='.$page->getId();
            $v = array();

            if( $haveChildren && ! $canAdmin ) {
                $v['leaf']  = false;
                $v['id']	= 'sitetree'.$page->getId();
                $v['text']  = $page->getTitle();
                $v['cls']	= 'menuDisabledNode';
            }
            elseif( $haveChildren && $canAdmin ) {
                $v['leaf']  = false;
                $v['id']	= 'sitetree'.$page->getId();
                $v['text']  = $page->getTitle();
                $v['cls']	= 'menuEnabledNode';
                $v['href']	= $link;
            } else {
                $v['leaf']  = true;
                $v['id']	= 'sitetree'.$page->getId();
                $v['text']  = $page->getTitle();
                $v['href']	= $link;
                $v['cls']	= 'menuEnabledNode';
            }

            if( count( $v ) && $page->showInAdminMenu() ) {
                $nodes[] = $v;
            }
        }

        return json_encode( $nodes );
    }

    function xchgAction() {
        $in          = $this->app->in();
        $pageId 	 = intval( substr( $in['id'],   8 ));
        $oldParentId = intval( substr( $in['oldp'], 8 ));
        $newParentId = intval( substr( $in['newp'], 8 ));
        $newPos 	 = intval( $in['pos'] );

        $q_all = $this->app->db()->select()->from('site_tree')
            ->where('`parent`='.$newParentId)
            ->where('`id`<>'.$pageId)
            ->order('menu_pos')
            ->query()
            ->fetchAll();

        $this->app->db()->update('site_tree',array(
            'parent' => $newParentId,
            'menu_pos' => $newPos,
        ),'`id`='.$pageId);

        $c = 0;

        foreach( $q_all as $index => $value ) {
            if( $value['id'] != $pageId ) {
                if( $c == $newPos ) {
                    $c++;
                }

                $this->app->db()->update('site_tree', array(
                    'menu_pos' => $c
                ),'`id`='.$value['id'] );
            }

            $c++;
        }

        $this->app->pages()->updateCache( new Table() );

        return json_encode( array( 'result' => true, 'error' => 'ERROR#1' ));
    }
}