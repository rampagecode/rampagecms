<?php

namespace Admin;

use App\AppInterface;

class AdminControllerParameters {
    private $app;

    public $env = ''; // Environment
    public $tab = ''; // Section
    public $mod = ''; // Module
    public $act = ''; // Action
    public $idx = ''; // Index

    /**
     * @var string
     */
    public $jsObj;

    public function __construct( AppInterface $app ) {
        $this->app = $app;

        $envKey = $app->getVar('env_var');
        $tabKey = $app->getVar('tab_var');
        $modKey = $app->getVar('mod_var');

        $this->env = $app->in( $envKey );
        $this->tab = $app->in( $tabKey );
        $this->mod = $app->in( $modKey );
        $this->act = $app->in( $app->getVar('act_var'));
        $this->idx = $app->in( $app->getVar('idx_var'));

        if( $app->in('jsobj' )) {
            $this->jsObj = $app->in('jsobj' );
        } else {
            $this->jsObj = $app->getCookie( 'rte_jsobj' );
        }
    }

    function envURL() {
        $envKey = $this->app->getVar('env_var');

        return $this->app->baseURL() . "&{$envKey}=" . $this->env;
    }

    function tabURL() {
        $tabKey = $this->app->getVar('tab_var');

        return empty( $this->tab )
            ? $this->envURL()
            : $this->envURL() . "&{$tabKey}=" . $this->tab;
    }

    function modURL() {
        $modKey = $this->app->getVar('mod_var');

        return $this->tabURL() . "&{$modKey}=" . $this->mod;
    }

    function buildURL( $idx = null, $act = null, $mod = null, $tab = null, $env = null ) {
        $mod = is_null( $mod ) ? $this->mod : $mod;
        $tab = is_null( $tab ) ? $this->tab : $tab;
        $env = is_null( $env ) ? $this->env : $env;
        $act = is_null( $act ) ? $this->act : $act;
        $idx = is_null( $idx ) ? $this->idx : $idx;

        $envKey = $this->app->getVar('env_var');
        $tabKey = $this->app->getVar('tab_var');
        $modKey = $this->app->getVar('mod_var');
        $actKey = $this->app->getVar('act_var');
        $idxKey = $this->app->getVar('idx_var');

        $params = [
            "&{$envKey}={$env}",
            "&{$tabKey}={$tab}",
            "&{$modKey}={$mod}",
            "&{$actKey}={$act}",
            "&{$idxKey}={$idx}",
        ];

        return $this->app->baseURL() . join('', $params );
    }

    /**
     * @return bool
     */
    function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
