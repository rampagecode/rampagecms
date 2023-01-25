<?php

namespace Lib;

use App\Parser\ParserInterface;

interface ResultInterface {
    /**
     * @param string $content
     * @return void
     */
    function setContent( $content );

    /**
     * @param ParserInterface $parser
     * @return void
     */
    function setParser( ParserInterface $parser );

    /**
     * @return string
     */
    function getResult();

    /**
     * @param string $log
     * @return void
     */
    function embedLog( $log );
}
