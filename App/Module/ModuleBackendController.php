<?php

namespace App\Module;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use App\AppInterface;
use Data\Module\Table;
use Module\ModuleException;

abstract class ModuleBackendController implements ModuleControllerProtocol, AdminControllerInterface {
    /**
     * @var AppInterface
     */
    protected $app;

    /**
     * @var AdminControllerParameters
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $moduleIdUrlParameter = 'cms_module_id';

    /**
     * @var int
     */
    protected $moduleId;

    /**
     * @var InstallableModule
     */
    protected $moduleDelegate;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    function availableActions() {
        return [];
    }

    /**
     * @param int $id
     * @return void
     * @throws ModuleException
     */
    function setModuleId( $id ) {
        $this->moduleId = $id;

        if( empty( $this->moduleId )) {
            throw new ModuleException('Module Id is empty');
        }
    }

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    /**
     * @param InstallableModule $delegate
     * @return void
     */
    function setModuleDelegate( InstallableModule $delegate ) {
        $this->moduleDelegate = $delegate;
    }

    /**
     * @param int $idx
     * @param string $action
     * @param array<string, mixed> $parameters
     * @return string
     */
    function buildURL( $idx, $action, $parameters = [] ) {
        $baseURL = $this->parameters->buildURL( $idx, $action, 'Posts', 'module' );

        if( !isset( $parameters[ $this->moduleIdUrlParameter ] )) {
            $parameters[ $this->moduleIdUrlParameter ] = $this->moduleId;
        }

        return $baseURL .'&'. http_build_query( $parameters );
    }

    /**
     * @return int
     */
    function getModuleIdFromRequest() {
        return intval( $this->app->in( $this->moduleIdUrlParameter ));
    }

    /**
     * @return \Zend_Db_Table
     */
    function getModuleTable() {
        return new \Zend_Db_Table([
            \Zend_Db_Table_Abstract::NAME => $this->moduleDelegate->getTableName( $this->moduleId )
        ]);
    }
}
