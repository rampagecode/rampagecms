<?php

namespace Admin;

use App\Parser\ParserInterface;
use Lib\ResultInterface;
use Sys\Log\LoggerInterface;
use Sys\Log\Strategy\HeadersLogger;

class JsonResult implements ResultInterface {
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $logs;

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
        $this->logs = $log;
    }

    /**
     * @return string
     */
    function getResult() {
        if( is_string( $this->content )) {
            $result = $this->content;

            if( $this->parser instanceof ParserInterface ) {
                $result = $this->parser->parse( $result );
            }

            if( ! empty( $this->logs )) {
                $base = json_decode( $result, false );

                if( $base === null ) {
                    $result .= $this->logs;
                } else {
                    $data =& $base;

                    while( true ) {
                        if( is_array( $data )) {
                            $data =& $data[0];
                        } else {
                            break;
                        }
                    }

                    if( is_object( $data )) {
                        $data->___runlog = $this->logs;
                    }

                    $result = json_encode( $base );
                }
            }
        }
        elseif( $this->content instanceof ResultInterface ) {
            $this->content->embedLog( $this->logs );
            $result = $this->content->getResult();

            if( $this->parser instanceof ParserInterface ) {
                $result = $this->parser->parse( $result );
            }
        }
        else {
            $result = 'Unknown result';
        }

        return $result;
    }
}