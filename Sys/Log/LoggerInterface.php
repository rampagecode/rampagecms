<?php

namespace Sys\Log;

interface LoggerInterface {
    const WARN	  = 4; // Предупреждение, выполнение продолжается
    const ERR     = 3; // Фатальная ошибка, дальнейшее выполнение невозможно
    const NOTICE  = 5; // Замечание
    const INFO    = 6; // Информационное сообщение
    const DEBUG   = 7; // Отладочное сообщение

    public function add( $message, $priority = null );
    public function end();
    public function table( &$table, $_self = false );
    public function getDbProfiler();
}