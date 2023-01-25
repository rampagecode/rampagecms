<?php

namespace App\Module;

use App\AppInterface;

abstract class NonInstallableModule implements ModuleInterface {
    /**
     * @var AppInterface
     */
    protected $app;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    /**
     * @return false
     */
    public function isInstallable() {
        return false;
    }

    /**
     * @param int $moduleId
     * @return void
     */
    function onInstall( $moduleId ) {}

    /**
     * @param int $moduleId
     * @return void
     */
    function onUninstall( $moduleId ) {}
}
