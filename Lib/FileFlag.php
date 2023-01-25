<?php

namespace Lib;

class FileFlag {
    /**
     * @var string
     */
    private $file;

    /**
     * @param string $file
     */
    public function __construct( $file ) {
        $this->file = $file;
    }

    /**
     * @return bool
     */
    function getFlag() {
        return file_exists( $this->file );
    }

    /**
     * @param bool $enable
     * @return bool
     */
    function setFlag( $enable ) {
        if( $enable ) {
            if( file_exists( $this->file )) {
                return true;
            } else {
                return file_put_contents( $this->file, '1') > 0;
            }
        } else {
            if( !file_exists( $this->file )) {
                return true;
            } else {
                return unlink( $this->file );
            }
        }
    }
}
