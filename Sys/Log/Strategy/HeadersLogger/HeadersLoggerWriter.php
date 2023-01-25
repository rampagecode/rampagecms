<?php

namespace Sys\Log\Strategy\HeadersLogger;

use Zend_Config;
use Zend_Log_Writer_Abstract;
use Zend_Wildfire_Channel_HttpHeaders;
use Zend_Wildfire_Plugin_Interface;
use Zend_Wildfire_Protocol_JsonStream;

class HeadersLoggerWriter extends Zend_Log_Writer_Abstract implements Zend_Wildfire_Plugin_Interface {
    /**
     * @var Zend_Wildfire_Protocol_JsonStream
     */
    private $stream;

    /**
     * @var string
     */
    private $structure = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';

    /**
     * @param Zend_Wildfire_Channel_HttpHeaders $channel
     * @return void
     */
    public function setChannel( Zend_Wildfire_Channel_HttpHeaders $channel ) {
        $this->stream = $channel->getProtocol(Zend_Wildfire_Protocol_JsonStream::PROTOCOL_URI );
        $this->stream->registerPlugin( $this );
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write( $event ) {
        $type = $event['priorityName'] == 'ERR' ? 'ERROR' : 'LOG';

        $this->stream->recordMessage(
            $this,
            $this->structure,
            [['Type' => $type, 'Label' => $event['timestamp']], $event['message']]
        );
    }

    /**
     * @param  array|Zend_Config $config
     * @return HeadersLoggerWriter
     */
    static public function factory( $config ) {
        return new self();
    }

    /*
     * Zend_Wildfire_Plugin_Interface
     */

    /**
     * @return string Returns the URI of the plugin.
     */
    public function getUri() {
        return 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
    }

    /**
     * @param string $protocolUri The URI of the protocol that should be flushed to
     * @return void
     */
    public function flushMessages( $protocolUri ) {

    }
}

