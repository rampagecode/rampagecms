<?php

namespace App\Access;

class AdminAccess extends ResourceAccess {
    /**
     * @return AccessibleResource[]
     */
    function getResources() {
        return [
            new SimpleResource('desktop', 'Доступ к рабочему столу' ),
            new SimpleResource('tree', 'Доступ к дереву сайта' ),
            new SimpleResource('content', 'Доступ к навигационному меню' ),
            new SimpleResource('groups', 'Доступ к управлению группами пользователей' ),
            new SimpleResource('users', 'Доступ к пользователям сайта' ),
            new SimpleResource('settings', 'Доступ к управлению настройками сайта' ),
            new SimpleResource('system', 'Доступ к системным функциям сайта' ),
            new SimpleResource('module', 'Доступ к управлению модулями' ),
        ];
    }

    /**
     * @return bool
     */
    function canAccessDesktop() {
        return $this->canAccess('desktop');
    }

    /**
     * @return bool
     */
    function canAccessTree() {
        return $this->canAccess('tree');
    }

    /**
     * @return bool
     */
    function canAccessContent() {
        return $this->canAccess('content');
    }

    /**
     * @return bool
     */
    function canAccessGroups() {
        return $this->canAccess('groups');
    }

    /**
     * @return bool
     */
    function canAccessUsers() {
        return $this->canAccess('users');
    }

    /**
     * @return bool
     */
    function canAccessSettings() {
        return $this->canAccess('settings');
    }

    /**
     * @return bool
     */
    function canAccessSystem() {
        return $this->canAccess('system');
    }

    /**
     * @return bool
     */
    function canAccessModule() {
        return $this->canAccess('module');
    }
}