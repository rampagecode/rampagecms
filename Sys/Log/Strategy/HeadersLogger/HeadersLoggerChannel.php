<?php

namespace Sys\Log\Strategy\HeadersLogger;

use Zend_Controller_Request_Http;
use Zend_Wildfire_Channel_HttpHeaders;

class HeadersLoggerChannel extends Zend_Wildfire_Channel_HttpHeaders {
    public function isReady( $forceCheckRequest = false ) {
        if( ! $forceCheckRequest
            && ! $this->_request
            && ! $this->_response
        ) {
            return true;
        }

        if( ! ( $this->getRequest() instanceof Zend_Controller_Request_Http )) {
            return false;
        }

        return true;
    }
}
