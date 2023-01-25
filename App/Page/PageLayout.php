<?php

namespace App\Page;

use App\AppException;

class PageLayout {
    private $path;

    public function __construct( $path ) {
        $this->path = $path;
    }

    /**
     * @return string
     * @throws AppException
     */
    public function load() {
        $path = $this->path;

        if( ! file_exists( $path )) {
            throw new AppException('File not found at path ' . $path );
        }

        $content = @file_get_contents( $path );

        if( ! empty( $content )) {
            return preg_replace_callback(
                "#<!--@([a-z]+)\s(.+?)-->#i",
                [ $this, 'replace' ],
                $content
            );
        } else {
            throw new AppException('Layout content is empty');
        }
    }

    /**
     * @param string[]
     * @return string
     */
    private function replace( $matches ) {
        $cmd = $matches[1];
        $inc = $matches[2];

        if( $cmd == '' ) {
            return '';
        }

        $inc = trim( $inc );

        if( $cmd == 'include' ) {
            $filepath = dirname( $this->path ) . DIRECTORY_SEPARATOR . $inc;

            if( file_exists( $filepath )) {
                return file_get_contents( $filepath );
            }
        }

        return null;
    }
}