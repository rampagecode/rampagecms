<?php

namespace App\Page;

use App\Parser\ParserInterface;
use Lib\ResultInterface;

class PageResult implements ResultInterface {
    private $content = '';

    /**
     * @var ParserInterface
     */
    private $parser;

    public function __construct() {

    }

    /**
     * @param string $content
     * @return void
     */
    function setContent( $content ) {
        $this->content = $content;
    }

    /**
     * @param ParserInterface $parser
     * @return void
     */
    function setParser( ParserInterface $parser ) {
        $this->parser = $parser;
    }

    /**
     * @param string $log
     * @return void
     */
    function embedLog( $log ) {
        $this->content .= "\n\n\n<!--\n{$log}\n-->\n";
    }

    /**
     * @return string
     */
    function getResult() {
        $result = $this->content;

        if( $this->parser instanceof ParserInterface ) {
            $result = $this->parser->parse( $result );
        }

        return $result;
    }
}