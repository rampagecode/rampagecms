<?php

namespace Sys\Log\Strategy;

use Sys\Log\Strategy;
use Sys\Log\Strategy\HeadersLogger\HeadersLoggerChannel;
use Sys\Log\Strategy\HeadersLogger\HeadersLoggerWriter;
use Sys\Log\Strategy\HeadersLogger\HeadersProfiler;

class HeadersLogger extends Strategy {
    private $profiler;
    private $channel;
    private $response;

    public function getChannel() {
        if( empty( $this->channel )) {
            $request = new \Zend_Controller_Request_Http();
            $this->response = new \Zend_Controller_Response_Http();

            $this->channel = new HeadersLoggerChannel();
            $this->channel->setRequest( $request );
            $this->channel->setResponse( $this->response );
        }

        return $this->channel;
    }

    public function create() {
        $writer = new HeadersLoggerWriter();

        $this->logger = new \Zend_Log( $writer );
        $this->logger->addPriority('TABLE', 8);

        $writer->setChannel( $this->getChannel() );
    }

    public function flush() {
        // Вывод логов в Firebug
        if( $this->channel !== null ){
            $this->channel->flush();
            $this->response->sendHeaders();
        }

        if ( $this->profiler !== null ){
            $this->profiler->flush();
        }
    }

    public function initProfiler() {
        if( empty( $this->profiler )) {
            $this->profiler = new HeadersProfiler();
            $this->profiler->create( $this->getChannel() );
        }

        return $this->profiler->getProfiler();
    }
}
