<?php

namespace Sys\Log\Strategy;

use Sys\Log\Strategy;


class ScreenLogger extends Strategy {
    function create() {
        $writer = new \Zend_Log_Writer_Stream('php://output');
        $this->logger = new \Zend_Log( $writer );
    }
}
