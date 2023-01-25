<?php

namespace App\Access;

class GroupAccess extends ResourceAccess {
    /**
     * @return AccessibleResource[]
     */
    function getResources() {
        return [
            new SimpleResource('adminPanel', 'Доступ к системе управления сайтом' ),
            new SimpleResource('offline', 'Доступ к отключенному сайту' ),
            new ComplexResource( new AdminAccess(), 'admin', 'Функции панели управления' ),
        ];
    }

    /**
     * @return bool
     */
    function canAccessAdmin() {
        return $this->canAccess('adminPanel');
    }

    /**
     * @return bool
     */
    function canAccessOffline() {
        return $this->canAccess('offline');
    }

    /**
     * @return AdminAccess
     */
    function adminAccess() {
        return new AdminAccess( $this->accessList['admin'] );
    }
}