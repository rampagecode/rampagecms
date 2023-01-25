<?php

namespace Module\Posts;

use App\AppInterface;
use App\Module\InstallableModule;
use App\Module\ModuleInfo;

class Posts extends InstallableModule {
    /**
     * @param int $moduleId
     * @return string
     */
    public function getTableName( $moduleId ) {
        return "mod_posts_{$moduleId}";
    }

    /**
     * @return ModuleInfo
     */
    public function getInfo() {
        return new ModuleInfo(
            'Posts',
            'Посты',
            'Позволяет вести разделы типа новостей, статей, блога и т.д.'
        );
    }

    /**
     * @param int $moduleId
     * @return void
     */
    function onInstall( $moduleId ) {
        $sql = new PostsSQL();
        $tableName = $this->getTableName( $moduleId );
        $this->app->db()->query( $sql->dropTable( $tableName ), [] );
        $this->app->db()->query( $sql->createTable( $tableName ), [] );
    }

    /**
     * @param int $moduleId
     * @return void
     */
    function onUninstall( $moduleId ) {
        $sql = new PostsSQL();
        $tableName = $this->getTableName( $moduleId );
        $this->app->db()->query( $sql->dropTable( $tableName ), [] );
    }

    /**
     * @return Frontend\PostsController
     */
    function getFrontend() {
        return new Frontend\PostsController( $this->app );
    }

    /**
     * @param $moduleId
     * @return Backend\PostsController
     * @throws \Module\ModuleException
     */
    function getBackend( $moduleId ) {
        $controller = new Backend\PostsController( $this->app );

        if( empty( $moduleId )) {
            $moduleId = $controller->getModuleIdFromRequest();
        }

        $controller->setModuleId( $moduleId );
        $controller->setModuleDelegate( $this );

        return $controller;
    }
}