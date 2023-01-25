<?php

namespace Sys\Log;

use Sys\File\FileManager;
use Sys\Log\Strategy\HeadersLogger;

class Logger implements LoggerInterface {

    /**
     * @var LoggerInterface
     */
    private static $_default = null;
    private $strategy;

    function __construct( Factory $factory, FileManager $files) {
        $this->strategy = $factory->getLoggerStrategy(
            $this->inDebug(),
            $this->isAjaxRequest(),
            $this->canShowOnScreen(),
            $files->rootDir( 'log.txt' )
        );
    }

    public static function setDefault( LoggerInterface $logger ) {
        self::$_default = $logger;
    }

    public static function getDefault() {
        return self::$_default;
    }

    /**
     * @return bool
     */
    private function inDebug() {
        return DEBUG_ON;
    }

    /**
     * @return bool
     */
    private function canShowOnScreen() {
        return DEBUG_LOG_TO_SCREEN;
    }

    /**
     * @return bool
     */
    public function isAjaxRequest() {
        return( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );
    }

    /**
     * @param string $message
     * @param int $priority
     * @return void
     */
    public function add( $message, $priority = null ) {
        if( $priority === null ) {
            $priority = self::INFO;
        }

        // В режиме продакшн в лог пишутся только Kernel_Log::ERR и Kernel_Log::WARN
        if( ! $this->inDebug() ) {
            if( in_array( $priority, array( self::NOTICE, self::INFO, self::DEBUG ))) {
                return;
            }
        }

        $this->strategy->log( $message, $priority );
    }

    public function end() {
        $this->strategy->flush();
    }

    public function table( &$table, $_self = false ) {
        if( count( $table )) {
            foreach( $table as $k => $v ) {
                if ( is_array( $v )) {
                    $this->table( $table[ $k ], true );
                } else {
                    $table[ $k ] = $v;
                }
            }

            if( ! $_self ) {
                $this->strategy->table( $table );
            }
        }
    }

    public function getDbProfiler() {
        return $this->strategy->initProfiler();
    }
}