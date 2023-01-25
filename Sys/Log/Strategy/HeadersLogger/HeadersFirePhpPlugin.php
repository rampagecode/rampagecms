<?php

namespace Sys\Log\Strategy\HeadersLogger;

use Zend_Wildfire_Plugin_FirePhp;

class HeadersFirePhpPlugin extends Zend_Wildfire_Plugin_FirePhp {
    public function __construct() {}

    function encodeTable( $table) {
        return $this->_encodeTable( $table );
    }
}
