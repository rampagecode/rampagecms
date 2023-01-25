<?php

namespace App\Module;

use App\AppInterface;

abstract class InstallableModule implements ModuleInterface {
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
     * @return bool
     */
    public function isInstallable() {
        return true;
    }

    /**
     * @param int $moduleId
     * @return string
     */
    abstract function getTableName( $moduleId );

    /**
     * @param int $moduleId
     * @return \Zend_Db_Table
     */
    function getTable( $moduleId ) {
        return new \Zend_Db_Table([
            \Zend_Db_Table_Abstract::NAME => $this->getTableName( $moduleId )
        ]);
    }
}