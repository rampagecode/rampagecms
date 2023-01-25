<?php

namespace Sys\Log\Strategy;

use Sys\Log\Strategy;

class FileLogger extends Strategy {

    private $path;

    public function __construct( $path ) {
        $this->path = $path;
        parent::__construct();
    }

    function create() {
        $writer = new \Zend_Log_Writer_Stream( $this->path );
        $this->logger = new \Zend_Log( $writer );
    }
}
