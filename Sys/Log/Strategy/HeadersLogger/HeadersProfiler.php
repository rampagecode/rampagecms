<?php

namespace Sys\Log\Strategy\HeadersLogger;

use Zend_Wildfire_Channel_HttpHeaders;

class HeadersProfiler {
    private $profiler;
    private $channel;

    function getProfiler() {
        return $this->profiler;
    }

    public function create( Zend_Wildfire_Channel_HttpHeaders $channel ) {
        $this->channel = $channel;

        $this->profiler = new HeadersFirebugProfiler('All DB Queries');
        $this->profiler->setChannel( $channel );
        $this->profiler->setEnabled(true);
    }

    public function flush() {
        if( $this->channel !== null ){
            $this->channel->flush();
        }
    }
}

