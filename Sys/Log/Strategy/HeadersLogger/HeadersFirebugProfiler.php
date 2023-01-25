<?php

namespace Sys\Log\Strategy\HeadersLogger;

use Zend_Db_Profiler_Firebug;
use Zend_Wildfire_Channel_HttpHeaders;
use Zend_Wildfire_Plugin_FirePhp_TableMessage;
use Zend_Wildfire_Protocol_JsonStream;

class HeadersFirebugProfiler extends Zend_Db_Profiler_Firebug implements \Zend_Wildfire_Plugin_Interface {
    /**
     * @var Zend_Wildfire_Protocol_JsonStream
     */
    private $stream;

    /**
     * @var string
     */
    private $structure = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';

    function setEnabled( $enable ) {
        $this->_enabled = (boolean) $enable;

        if( $this->getEnabled() ) {
            if (!$this->_message) {
                $this->_message = new Zend_Wildfire_Plugin_FirePhp_TableMessage( $this->_label );
                $this->_message->setBuffered(true);
                $this->_message->setHeader(array('Time','Event','Parameters'));
                $this->_message->setDestroy(true);
                $this->_message->setOption('includeLineNumbers', false);
            }
        } else {
            if( $this->_message ) {
                $this->_message->setDestroy(true);
                $this->_message = null;
            }
        }

        return $this;
    }

    /**
     * @param Zend_Wildfire_Channel_HttpHeaders $channel
     * @return void
     */
    public function setChannel( Zend_Wildfire_Channel_HttpHeaders $channel ) {
        $this->stream = $channel->getProtocol(Zend_Wildfire_Protocol_JsonStream::PROTOCOL_URI );
        $this->stream->registerPlugin( $this );
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
        $var = $this->_message->getMessage();
        $my = new HeadersFirePhpPlugin();
        $t = $my->encodeTable( $var );
        
        $this->stream->recordMessage(
            $this,
            $this->structure,
            [['Type' => 'TABLE', 'Label' => 'Database Profiler'], $t]
        );
    }
}
