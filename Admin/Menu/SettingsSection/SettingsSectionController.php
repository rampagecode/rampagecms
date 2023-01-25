<?php

namespace Admin\Menu\SettingsSection;

use Admin\AdminException;
use Admin\Menu\Settings\SettingsModel;
use Admin\Menu\SideMenu\SideMenuController;
use Admin\Menu\SideMenu\SideMenuView;

class SettingsSectionController extends SideMenuController {
    public $section = 'SettingsSection';

    public function auto_run() {
        if( empty( $this->section )) {
            throw new AdminException('Section is not set');
        }

        $model = new SettingsModel( $this->app->db(), $this->app->in() );
        $items = $model->getGroups();
        $view = new SideMenuView();
        $menu = $view->header('Настройки');
        $rows = '';

        foreach( $items as $r ) {
            if ( $r['conf_title_noshow'] != 0 ) {
                if ( $this->app->getUser()->getGroup() == $this->app->getVar('admin_group') ) {
                    $r['extra'] = '<span style="font-weight: normal;">[скрытая]</span>';
                } else {
                    continue;
                }
            }

            $link = '[tab://]&a=conf&i=view&x='.$r['conf_title_id'];
            $icon = '[img://]item.gif';
            $rows .= $view->row( $link, $r['conf_title_title'], $icon );
        }

        $menu .= $view->body( $rows );

        $this->menu = $menu;
    }
}