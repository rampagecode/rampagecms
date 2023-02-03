<?php

namespace App\Module;

use App\AppInterface;
use Module\ModuleException;

abstract class ModuleFrontendController implements ModuleControllerProtocol {
    /**
     * @var AppInterface
     */
    protected $app;

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
     * @param InstallableModule $delegate
     * @return void
     */
    function setModuleDelegate( InstallableModule $delegate ) {
        $this->moduleDelegate = $delegate;
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