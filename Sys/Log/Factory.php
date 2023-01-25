<?php

namespace Sys\Log;

use Sys\Log\Strategy\FileLogger;
use Sys\Log\Strategy\HeadersLogger;
use Sys\Log\Strategy\ScreenLogger;

class Factory {
    /**
     * @param bool $inDebug
     * @param bool $isAjaxRequest
     * @param bool $canShowOnScreen
     * @param string $filePath
     * @return Strategy
     */
    public function getLoggerStrategy(
        $inDebug,
        $isAjaxRequest,
        $canShowOnScreen,
        $filePath
    ) {
        // Работаем в режиме разработки / отладки
        if( $inDebug ) {
            if( $isAjaxRequest ){
                $strategy = new HeadersLogger();
            } else if( $canShowOnScreen ) {
                // Если Firebug не доступен, то в режиме отладки все сообщения
                // выводятся на экран
                $strategy = new ScreenLogger();
            } else {
                // Иначе в файл
                $strategy = new FileLogger( $filePath );
            }
        } else {
            // Работая в режиме продакшн логи пишутся в файл.
            $strategy = new FileLogger( $filePath );
        }

        return $strategy;
    }
}