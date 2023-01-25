<?php

namespace Admin\Menu\Desktop;

use Admin\Menu\MenuView;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class DesktopController implements ModuleControllerProtocol {

    /**
     * @var AppInterface
     */
    private $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        $iconsLimit = 9;
        $groupId = $this->app->getUser()->getGroup();
        $adminGroupId = $this->app->getVar('admin_group');
        $view = new DesktopView();
        $model = new DesktopModel( $this->app->db() );
        $tree = $model->loadSiteTree( $groupId, $adminGroupId, $iconsLimit );
        $number = 0;
        $limit = 3;
        $rows = '';
        $cells = '';

        foreach( $tree as $r ) {
            if( $number == $limit ) {
                $rows .= $view->row( $cells );
                $rows .= $view->space( $limit );
                $cells = '';
                $number = 0;
            }

            $cells .= $view->cell( $r['pagetitle'], $r['id'] );
            $number++;
        }

        if( $number > 0 ) {
            $rows .= $view->row( $cells );
        }

        $content = $view->table( $rows );
        $mainView = new MenuView();

        return $mainView->without_menu( $content );
    }
}