<?php

namespace App\Module;

use App\AppInterface;
use Module\ModuleException;

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

    /**
     * @param $moduleId
     * @return void
     * @throws ModuleException
     */
    function getComplexFrontend( $moduleId ) {
        throw new ModuleException('For a non-installable module use getFrontend()');
    }
}
