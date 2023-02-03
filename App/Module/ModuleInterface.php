<?php

namespace App\Module;

use App\AppInterface;

interface ModuleInterface {
    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app );

    /**
     * @return bool
     */
    function isInstallable();

    /**
     * @param int $moduleId
     * @return void
     */
    function onInstall( $moduleId );

    /**
     * @param int $moduleId
     * @return void
     */
    function onUninstall( $moduleId );

    /**
     * @return ModuleControllerProtocol
     */
    function getFrontend();

    /**
     * @param int $moduleId
     * @return ModuleControllerProtocol
     */
    function getComplexFrontend( $moduleId );

    /**
     * @param int $moduleId
     * @return ModuleBackendController
     */
    function getBackend( $moduleId );

    /**
     * @return ModuleInfo
     */
    function getInfo();
}
