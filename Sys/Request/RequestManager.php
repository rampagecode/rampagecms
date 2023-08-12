<?php

namespace Sys\Request;

class RequestManager {
    private $url_parts;
    private $page_url;
    private $base_url;
    private $base_page_addr;

    /**
     * @var string[]
     */
    private $base_url_params = [];

    function __construct() {
        if( $_GET['_3xiqstr'] == '' ) {
            $_GET['_3xiqstr'] = $_SERVER['REQUEST_URI'];
            $_GET['_3xiqstr'] = preg_replace( '#\?(.*?)$#', '', $_GET['_3xiqstr'] );
        }

        // Адрес страницы
        $this->page_url = isset( $_GET['_3xiqstr'] )
            ? preg_replace( "/[^A-Za-z0-9_\-\.\/]/", '', $_GET['_3xiqstr'] )
            : '';

        $this->page_url = trim( $this->page_url );
        $this->page_url = trim( $this->page_url, '/' );
        $this->url_parts = explode( '/', $this->page_url );

        switch( $this->url_parts[0] ) {
            case 'admin':
                $this->base_url = '/admin/';
                $this->base_page_addr = '/admin/';
                break;

            case 'en':
                $this->base_url =  '/en/';
                $this->base_page_addr = '/en/index/';
                break;

            default:
                $this->base_url = '/';
                $this->base_page_addr = '/index/';
        }

        if( $this->page_url == '' ) {
            $this->page_url = '/';
        }

        $this->page_url = $this->page_url == $this->base_url
            ? $this->base_page_addr
            : ( '/' . trim( $this->page_url, '/' ) .'/' )
        ;
    }

    /**
     * @param string $args,...
     * @return string
     */
    function pageURL( $args = null ) {
        $url = $this->page_url;
        $url = $this->addArgs( $url, func_get_args() );

        if( count( $this->base_url_params )) {
            $url .= '?'.$this->baseUrlParamsToString();
        }

        return $url;
    }

    /**
     * @param string $args,...
     * @return string
     */
    function baseURL( $args = null ) {
        $url = $this->base_url;
        $url = $this->addArgs( $url, func_get_args() );

        if( count( $this->base_url_params )) {
            $url .= '?'.$this->baseUrlParamsToString();
        }

        return $url;
    }

    function rootURL() {
        $url = '/';
        $url = $this->addArgs( $url, func_get_args() );

        return $url;
    }

    function assetURL() {
        $url = $this->rootURL( 'assets', $this->isAdmin() ? 'admin' : 'public' );
        $url = $this->addArgs( $url, func_get_args() );

        return $url;
    }

    function basePageAddress() {
        return $this->base_page_addr;
    }

    private function addArgs( $url, $args ) {
        $url = rtrim( $url, '/' );

        if( count( $args ) > 0 ) {
            $url .= '/';
            $url .= join('/', $args);
        }

        return $url;
    }

    /**
     * @return string[]
     */
    function urlParts() {
        return $this->url_parts;
    }

    /**
     * @return bool
     */
    function isAdmin() {
        return $this->urlParts()[0] == 'admin';
    }

    /**
     * @return bool
     */
    function isAjaxRequest() {
        return( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );
    }

    /**
     * @return bool
     */
    function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    function addBaseUrlParameter( $name, $value ) {
        $this->base_url_params[ $name ] = $value;
    }

    /**
     * @param string $name
     * @return void
     */
    function removeBaseUrlParameter( $name ) {
        unset( $this->base_url_params[ $name ] );
    }

    /**
     * @return string
     */
    private function baseUrlParamsToString() {
        $keys = array_keys( $this->base_url_params );
        $vars = [];

        foreach( $keys as $key ) {
            $vars[] = $key.'='.$this->base_url_params[ $key ];
        }

        return join( '&', $vars );
    }
}