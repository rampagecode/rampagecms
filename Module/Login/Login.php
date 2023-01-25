<?php

namespace Module\Login;

use App\AppInterface;
use App\Module\ModuleInfo;
use App\Module\NonInstallableModule;

class Login extends NonInstallableModule {
    /**
     * @return ModuleInfo
     */
    public function getInfo() {
        return new ModuleInfo(
            'Login',
            'Форма входа',
            'Форма для авторизации пользователей нар сайте'
        );
    }

    /**
     * @return Frontend\LoginController
     */
    function getFrontend() {
        return new \Module\Login\Frontend\LoginController( $this->app );
    }

    /**
     * @return null
     */
    function getBackend( $moduleId ) {
        return null;
    }
}