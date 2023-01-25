<?php

namespace Sys\Log;

abstract class Strategy {
    protected $logger;

    abstract function create();

    public function __construct() {
        $this->create();
    }

    function flush() {}

    function log( $message, $priority = null ) {
        $this->logger->log( $message, $priority );
    }

    function table( $table ) {
        $this->logger->table( $table );
    }

    public function initProfiler() {
        return null;
    }
}
