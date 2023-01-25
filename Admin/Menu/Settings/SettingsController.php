<?php

namespace Admin\Menu\Settings;

use Admin\AdminException;
use Admin\Menu\MenuView;
use Admin\WidgetsView;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class SettingsController implements ModuleControllerProtocol {
    /**
     * @var AppInterface
     */
    private $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        $model = new SettingsModel( $this->app->db(), $this->app->in() );
        $view = new SettingsView();
        $html = $view->group_start_table();
        $groups = $model->getGroups();

        foreach( $groups as $i => $r ) {
            if ( $r['conf_title_noshow'] != 0 ) {
                if ( $this->app->getUser()->getGroup() == $this->app->getVar('admin_group') ) {
                    $r['extra'] = '<span style="font-weight: normal;">[скрытая]</span>';
                } else {
                    continue;
                }
            }

            $html .= $view->group_row( $r );
        }

        $html .= $view->group_end_table();

        return $html;
    }

    /**
     * @throws AdminException
     */
    public function viewAction() {
        $conf_group = $this->app->in('x');

        if( empty( $conf_group )) {
            throw new AdminException("Settings group id was not found");
        }

        $model = new SettingsModel( $this->app->db(), $this->app->in() );
        $settings = $model->getSettings( $conf_group );
        $group_title = '';
        $conf_entry = [];

        foreach( $settings as $r ) {
            $group_title = $r['conf_title_title'];
            $conf_entry[ $r['conf_id'] ] = $r;
        }

        //-----------------------------------------
        // Строим таблицу с настройками
        //-----------------------------------------

        $_rows = '';
        $widgets = new WidgetsView();
        $view = new SettingsView();
        $key_array = [];

        if( ! empty( $conf_entry )) {
            foreach( $conf_entry as $r ) {
                $control = $model->createControl( $r, $widgets );
                $_rows .= $view->settingRow( $r['conf_title'], $r['conf_key'], $r['conf_desc'], $control );
                $key_array[] = $r['conf_key'];
            }
        }

        return $view->group_view_table( $conf_group, $group_title, $_rows, implode( ',' , $key_array ));
    }

    /**
     * @throws AdminException
     */
    function updateAction() {
        $fields = explode( ',' , trim( $this->app->in('settings_save')));
        $model = new SettingsModel( $this->app->db(), $this->app->in() );
        $model->updateFields( $fields );
        $this->app->config()->updateSettingsCache();
        $view = new MenuView();

        return $view->showMessage('Настройки обновлены' );
    }
}
