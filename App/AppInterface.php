<?php

namespace App;

use App\Page\PageManager;
use App\User\User;
use Sys\Config\ConfigManager;
use Sys\Database\DatabaseInterface;
use Sys\Input\InputInterface;
use Sys\Language\LanguageManager;
use Sys\Log\LoggerInterface;

interface AppInterface {
    /**
     * @param string|null $name
     * @return string|InputInterface
     */
    function in( $name = null );

    /**
     * @return DatabaseInterface
     */
    function db();

    /**
     * @return User
     */
    function getUser();

    /**
     * @param User $user
     * @return void
     */
    function setUser( User $user );

    /**
     * @return void
     */
    function setGuest();

    /**
     * @param string $args,...
     * @return string
     */
    function baseURL( $args = null );

    /**
     * @param string $args,...
     * @return string
     */
    function pageURL( $args = null );

    /**
     * @param string $name Имя куки
     * @return false|string Значение куки
     */
    public function getCookie( $name );

    /**
     * @param string $name Имя куки
     * @param string $value Значение куки
     * @param bool $sticky Устанавливаем куку на год
     * @param int $expires Срок годности куки (в днях), только если $sticky = false
     */
    public function setCookie( $name, $value = '', $sticky = true, $expires = 0 );

    /**
     * @return string|null IP-адрес
     */
    public function getIpAddr();

    /**
     * @return array
     */
    public function getBrowser();

    /**
     * @param string $url
     * @return void
     */
    function redirect( $url );

    /**
     * @param string $key
     * @return mixed
     */
    public function getVar( $key );

    /**
     * @param string $message
     * @param int $priority
     * @return void|LoggerInterface
     */
    public function log( $message, $priority = null );

    /**
     * @param string $args,...
     * @return string
     */
    function rootDir( $args = null );

    /**
     * @param string $args,...
     * @return string
     */
    function assetDir( $args = null );

    /**
     * @return LanguageManager
     */
    function language();

    /**
     * @return ConfigManager
     */
    function config();

    /**
     * @param string $args,...
     * @return string
     */
    function assetURL( $args = null );

    /**
     * @return bool
     */
    function isAjaxRequest();

    /**
     * @return PageManager
     */
    function pages();

    /**
     * @return int
     */
    function getTimeOffset();

    /**
     * @return bool
     */
    function isSiteOffline();

    /**
     * @param bool $offline
     * @return bool
     */
    function toggleOffline( $offline );

    /**
     * @return int
     */
    function getCurrentSitePageId();
}