<?php

namespace Module\LangSwitch;

use App\AppInterface;
use App\Module\ModuleInfo;
use App\Module\NonInstallableModule;
use Module\LangSwitch\Frontend\LangSwitchController;

class LangSwitch extends NonInstallableModule {
    /**
     * @return ModuleInfo
     */
    public function getInfo() {
        return new ModuleInfo(
            'LangSwitch',
            'Переключатель языков',
            'Переключает язык сайта'
        );
    }

    /**
     * @return Frontend\LangSwitchController
     */
    function getFrontend() {
        return new LangSwitchController( $this->app );
    }

    /**
     * @return null
     */
    function getBackend( $moduleId ) {
        return null;
    }
}