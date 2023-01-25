<?php

namespace App\Parser;

interface ParserInterface {
    /**
     * @param string $content
     * @return string
     */
    function parse( $content );
}