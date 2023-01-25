<?php

namespace Module\Menu;

use App\AppInterface;
use App\Module\ModuleInfo;
use App\Module\NonInstallableModule;
use Module\Menu\Frontend\MenuController;


class Menu extends NonInstallableModule {
    /**
     * @return ModuleInfo
     */
    public function getInfo() {
        return new ModuleInfo(
            'Menu',
            'Меню сайта',
            'Строит меню на основе дерева сайта'
        );
    }

    /**
     * @return MenuController
     */
    function getFrontend() {
        return new MenuController( $this->app );
    }

    /**
     * @param $moduleId
     * @return null
     */
    function getBackend( $moduleId ) {
        return null;
    }
}
