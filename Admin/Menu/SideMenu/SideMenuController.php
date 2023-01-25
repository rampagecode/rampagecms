<?php

namespace Admin\Menu\SideMenu;

use Admin\AdminException;
use Admin\Menu\MenuView;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Parser\ParserInterface;
use Lib\ResultInterface;

class SideMenuController implements ModuleControllerProtocol, ResultInterface {

    /**
     * @var string
     */
    public $section;

    /**
     * @var AppInterface
     */
    protected $app;

    /**
     * @var string
     */
    protected $menu;

    /**
     * @var string
     */
    protected $content;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    /**
     * @throws AdminException
     */
    public function auto_run() {
        if( empty( $this->section )) {
            throw new AdminException('Section is not set');
        }

        $model = new SideMenuModel( $this->app->db() );
        $section = $model->loadSection( $this->section );

        $max_row = count( $section );
        $num_row = 0;
        $menu = '';
        $view = new SideMenuView();

        foreach ( $section as $sect_r ) {
            if( $sect_r['hidden'] ) {
                continue;
            }

            $num_row ++;
            $menu_box = '';
            $catalog = $model->loadCatalog( $sect_r['id'] );

            foreach ( $catalog as $menu_r ) {
                if( $menu_r['hidden'] ) {
                    continue;
                }

                $link = $menu_r['link'] . '&admenuid='.$menu_r['id'];

                if( $menu_r['type'] == 'normal' ) {
                    $icon = '[img://]item.gif';
                }
                elseif( $menu_r['type'] == 'shortcut' ) {
                    $icon = '[img://]menu_shortcut.gif';
                } else {
                    continue;
                }

                $menu_box .= $view->row( $link, $menu_r['name'], $icon );
            }

            if( $menu_box != '' ) {
                $menu .= $view->header( $sect_r['name'] );
                $menu .= $view->body( $menu_box );
            }

            if( $num_row < $max_row ) {
                $menu .= $view->spacer();
            }
        }

        $this->menu = $menu;
    }

    function setContent( $content ) {
        $this->content = $content;
    }

    function getResult() {
        $view = new MenuView();
        $content = empty( $this->content ) ? $view->no_mod_active() : $this->content;

        return $view->with_menu( $this->menu, $content );
    }

    /**
     * @throws AdminException
     */
    function embedLog( $log ) {
        throw new AdminException('Not applicable');
    }

    /**
     * @throws AdminException
     */
    function setParser( ParserInterface $parser ) {
        throw new AdminException('Not applicable');
    }
}