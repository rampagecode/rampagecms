<?php

namespace Sys\Language;

use Lib\ArrayAccess;
use Sys\File\FileManager;
use Sys\Request\RequestManager;
use Sys\SystemException;

class LanguageManager implements \ArrayAccess {
    private $data;
    private $lang;
    private $prefix;
    private $files;

    use ArrayAccess;

    protected function arrayAccessProperty() {
        return 'data';
    }

    /**
     * @param RequestManager $request
     * @param FileManager $files
     * @throws SystemException
     */
    public function __construct( RequestManager $request, FileManager $files ) {
        $this->files = $files;

        switch( $request->urlParts()[0] ) {
            case 'eng':
                $this->load( 'eng', 'eng' );
                break;

            default:
                $this->load( 'rus', '' );
        }
    }

    /**
     * @param $language
     * @param $prefix
     * @return void
     * @throws SystemException
     */
    function load( $language, $prefix ) {
        $filename = $this->files->langDir( $language . '.lang.php' );

        if( file_exists( $filename )) {
            include $filename;

            if( isset( $lang )) {
                $this->data = $lang;
            } else {
                throw new SystemException('Language data not set');
            }
        } else {
            throw new SystemException('Language file not found at path: ' . $filename );
        }

        $this->lang = $language;
        $this->prefix = $prefix;
    }

    function language() {
        return $this->lang;
    }

    function prefix() {
        return $this->prefix;
    }
}