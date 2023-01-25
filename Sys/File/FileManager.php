<?php

namespace Sys\File;

use Sys\Request\RequestManager;

class FileManager {
    /**
     * @var bool
     */
    private $isAdmin;

    /**
     * @param bool $isAdmin
     */
    public function __construct( $isAdmin ) {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @param string $args,...
     * @return string
     */
    function rootDir( $args = null ) {
        $path = str_replace( "\\", DIRECTORY_SEPARATOR, ROOT_PATH);
        $path = rtrim( $path, DIRECTORY_SEPARATOR );
        $args = func_get_args();

        if( count( $args ) > 0 ) {
            $path .= DIRECTORY_SEPARATOR;
            $path .= join(DIRECTORY_SEPARATOR, $args );
        }

        return $path;
    }

    /**
     * @param string $args,...
     * @return string
     */
    function assetDir( $args = null ) {
        $path = $this->rootDir('assets', $this->isAdmin ? 'admin' : 'public' );
        $args = func_get_args();

        if( count( $args) > 0 ) {
            $path .= DIRECTORY_SEPARATOR;
            $path .= join(DIRECTORY_SEPARATOR, $args );
        }

        return $path;
    }

    /**
     * @param string $args,...
     * @return string
     */
    function langDir( $args = null ) {
        $path = $this->rootDir( $this->isAdmin ? 'Admin' : 'Public', 'Lang' );
        $args = func_get_args();

        if( count( $args) > 0 ) {
            $path .= DIRECTORY_SEPARATOR;
            $path .= join(DIRECTORY_SEPARATOR, $args );
        }

        return $path;
    }
}