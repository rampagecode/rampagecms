<?php

namespace Sys\Error;

use Sys\Log\Logger as Logger;
use Sys\Log\LoggerInterface;

class ErrorHandler {
    private $log;
    private $email;

    public function __construct( Logger $logger = null ) {
        if( !empty( $logger )) {
            $this->setLogger( $logger );
        }
    }

    public function setLogger( Logger $logger ) {
        $this->log = $logger;
    }

    private function log( $message, $priority = null ) {
        if( !empty( $this->log )) {
            $this->log->add( $message, $priority );
        }
    }

    private function logTable( $table ) {
        if( !empty( $this->log )) {
            $this->log->table( $table );
        }
    }

    private function endLog() {
        if( !empty( $this->log )) {
            $this->log->end();
        }
    }

    public function setEmail( $email ) {
        $this->email = $email;
    }

    /**
     * Обработчик ошибок E_WARNING, E_NOTICE и E_USER_...;
     * Ошибки пишет в журнал по умолчанию. Нотисы не обрабатывает.
     *
     */
    public function errorHandler( $errno, $errmsg, $filename, $linenum, $vars ) {
        $errType = array(
            E_WARNING => array('Warning', LoggerInterface::WARN),
            //E_NOTICE		=> array( 'Notice', LoggerInterface::NOTICE ),
            E_USER_ERROR => array('User Error', LoggerInterface::ERR),
            E_USER_WARNING => array('User Warning', LoggerInterface::WARN),
            E_USER_NOTICE => array('User Notice', LoggerInterface::NOTICE)
        );

        if( isset( $errType[ $errno ])) {

            $file = $this->cleanRootPath( $filename );
            $text = $errType[$errno][0] . ': ' . $errmsg . ' in ' . $file . ' on line ' . $linenum;

            $this->log( $text, $errType[$errno][1] );
        }
    }

    /**
     * Функция получает страницу и проверяет есть ли в ней фатальные ошибки, такие
     * как E_PARSE, E_ERROR. Если да, то эти ошибки выделяются и пишутся в журнал.
     * В любом случае - это последний код который будет выполнен до завершения скрипта
     * поэтому здесь также вызывается метод theEnd().
     *
     * @param string $text HTML-страница - результат работы скрипта, которую выведет браузер.
     */
    public function fatalErrorHandler( $text ) {
        $fatal = array();
        $exc = array();

        // Регулярное выражение производит поиск фатальной ошибки на странице

        $prefix = ini_get('error_prepend_string');
        $suffix = ini_get('error_append_string');
        $fatalRE = '{^(.*)(' .
            preg_quote($prefix, '{}') .
            "<br />\r?\n<b>(\w+ error)</b>: \s*" .
            '(.*)' .
            ' in <b>)(.*?)(</b>' .
            ' on line <b>)(\d+)(</b><br />' .
            "\r?\n" .
            preg_quote($suffix, '{}') .
            ')(.*)$' .
            '}s';

        $p = null;

        if( ! preg_match( $fatalRE, $text, $p )) {
            // Фатальных ошибок не найдено - выводим страницу как она была.
            $this->theEnd();
            return $text;
        }

        list(
            $fatal['full'],
            $fatal['content'],
            $fatal['beforeFile'],
            $fatal['error'],
            $fatal['msg'],
            $fatal['file'],
            $fatal['beforeLine'],
            $fatal['line'],
            $fatal['afterLine'],
            $fatal['tail']
        ) = $p;

        $fatal['file'] = $this->cleanRootPath($fatal['file']);
        $fatal['msg'] = str_replace('&gt;', '>', $fatal['msg']);
        $fatal['msg'] = str_replace('&lt;', '<', $fatal['msg']);

        /**
         * Регулярное выражение для поиска непойманного исключения
         * с возможным стеком вызовов
         */
        $excRE = '{(' .
            "Uncaught exception '(.*?)' with message '(.*?)' in (.*?):(\d+)\r?\n" .
            "(.*?)(Stack trace:\r?\n(.*?)thrown)?" .
            ')}smi';

        $p = null;

        if( preg_match( $excRE, $fatal['msg'], $p )) {
            list(
                ,
                ,
                $exc['exception'],
                $exc['message'],
                $exc['file'],
                $exc['line'],
                ,
                ,
                $exc['stack']
            ) = $p;

            $exc['stack'] = $this->cleanRootPath($exc['stack']);
            $exc['stack'] = rtrim($exc['stack'], "\n ");

            $fatal['exc_msg'] = $this->tplUncaughtException( $exc['exception'], $exc['message'], $exc['stack'] );
        }

        if( ! $this->inDebug() && ! empty( $this->email )) {
            // Сайт работает в продакшн режиме - сообщения о фатальных ошибках
            // отправляются на email

            $addr = $this->email;
            $subj = 'PHP: ' . $fatal['error'] . ' on ' . $_SERVER['SERVER_NAME'];
            $mess = $fatal['full'];

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

            if( ! @mail( $addr, $subj, $mess, $headers )) {
                if( ! $this->isAjaxRequest() ) {
                    // Если не удается отправить на email, то выводим на экран сообщение
                    // с просьбой пользователя помочь нам и оправить на email отчет
                    // об ошибке самому

                    $subj = 'USER: ' . $fatal['error'] . ' on ' . $_SERVER['SERVER_NAME'];

                    $this->theEnd();

                    return $this->tplFatalErrorUserHelp($addr, $subj, urlencode(strip_tags($mess)));
                } else { // Это Ajax запрос
                    // Если это произошло во время Ajax-запроса, тот даже не знаю че поделать.

                    $this->theEnd();
                    return '{}';

                    // @TODO:
                    // Может быть так - return $text.'alert('Fatal error');' если ответ предполагался
                    // в JSON и обрабатывался eval-ом.
                }
            } else { // Сообщение об ошибке было отправлено разработчику
                // Пользователю мы покажем что-нибудь по проще.
                $this->theEnd();

                return $this->tplUserFatalError();
            }
        } else { // Сайт работает в режиме отладки
            if( ! $this->isAjaxRequest() ) {
                $this->theEnd();

                return $this->tplFatalErr(
                    strtoupper($fatal['error']),
                    count($exc)
                        ? $fatal['exc_msg']
                        : $fatal['msg'], $fatal['file'], $fatal['line']
                );
            } else {
                // Фатальная ошибка от непойманного исключения
                // содержащая стек вызовов.
                if( count( $exc ) && isset( $fatal['msg'] )) {
                    // Выводим сообшение об ошибке без трассировки стека
                    preg_match('{(.*)}', $fatal['msg'], $msgMatch);
                    $this->log( $msgMatch[0], LoggerInterface::ERR );

                    // Выводим стек как таблицу
                    preg_match_all('{\#(\d{1,2})\s(.*?)\r?\n}', $exc['stack'], $matches);

                    $table = array('Трассировка стека вызовов:', array());
                    //$table[1][] = array( 'No', 'Вызов:' );

                    $i = count($matches[2]);

                    if ($i > 0) {
                        foreach ($matches[2] as $line) {
                            $table[1][] = array($i--, $line);
                        }

                        $this->logTable($table);
                    }
                } else { // Просто фатальная ошибка
                    $tplErr = "{$fatal['error']}: {$fatal['msg']} in '{$fatal['file']}' on line {$fatal['line']}";
                    $this->log( $tplErr, LoggerInterface::ERR );
                }

                $this->theEnd();
                return '{}';
            }
        }
    }

    /**
     * Этот метод автоматически выполняется в момент завершения скрипта, а также
     * при возникновении фатальных ошибок, таких как E_ERROR и E_PARSE
     */
    private function theEnd() {
        // Вывод логов в Firebug
        $this->endLog();

        // Указываем кодировку для Ajax-запросов, если еще не был указан
        $headers_list = headers_list();
        $headers_send = array(
            "Content-Type" 	=> "text/html; charset=utf-8",
            "Cache-Control" => "no-store, no-cache,  must-revalidate",
            "Expires" 		=> date("r"),
            "Last-Modified" => date("r")
        );

        foreach( $headers_send as $sendHeaderName => $sendHeader ){
            $sendHeaderNotFound = true;

            foreach( $headers_list as $listHeader ){
                if( stristr( $listHeader, $sendHeaderName ) !== false ){
                    $sendHeaderNotFound = false;
                    break;
                }
            }

            if( $sendHeaderNotFound ){
                header( $sendHeaderName.': '.$sendHeader );
            }
        }
    }

    /**
     * Превращает полный путь к файлу в файловой системе сервера к относительному
     * текущего корневого каталога
     *
     * @param string $path Полный путь
     * @return string Относительный путь
     */
    private function cleanRootPath( $path ) {
        return str_replace(
            str_replace("\\", '/', ROOT_PATH ),
            '/',
            str_replace('\\', '/',$path )
        );
    }

    /**
     * @return bool
     */
    public function isAjaxRequest() {
        return( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );
    }

    /**
     * @return bool
     */
    private function inDebug() {
        return DEBUG_ON;
    }

    /**
     * @param $exception
     * @param $message
     * @param $stack
     * @return string
     */
    private function tplUncaughtException($exception, $message, $stack) { return <<<EOF
Непойманное исключение <b>{$exception}</b> <br>
cообщает: <b>{$message}</b> <br>
Стек вызовов: <br>
<pre style="margin: 0px; padding: 0px; margin-left: 20px; margin-top: 10px;">{$stack}</pre>
EOF;
    }

    /**
     * @param $errType
     * @param $msg
     * @param $file
     * @param $line
     * @return string
     */
    private function tplFatalErr($errType, $msg, $file, $line) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>	
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{$errType}</title>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="content-language" content="ru">
	
	<style>
		html, body {
			margin: 0px;
			padding: 0px;
			width: 100%;
			text-align: center;
			font-family: Verdana;
			font-size: 12px;			
		}
		#tplWrapper {
			width: 800px;			
			margin-left: auto;
			margin-right: auto;
			margin-top: 100px;
			border: 1px solid #ccc;
			text-align: left;
			background: #f4f4f4;			
		}
		#tplTitle {
			width: 100%;
			height: 40px;
			line-height: 40px;
			background: #ccc;
			color: #ff0000;
			font-size: 22px;
			font-weight: bold;
			text-align: center;
		}
		#tplMsg {			
			padding: 6px;
			font-size: 16px;
			
		}				
		#tplFile,
		#tplLine {
			padding: 6px;
			font-weight: bold;
			font-size: 16px;
		}		
		
		#tplFile span,
		#tplLine span {
			font-weight: normal;
		}
	</style>
</head>
<body>
	<div id="tplWrapper">
		<div id="tplTitle">{$errType}</div>
		<br>
		<div id="tplMsg">{$msg}</div>
		<div id="tplFile"><span>В файле: </span>{$file}</div>
		<br>
		<div id="tplLine"><span>На строке: </span>{$line}</div>
	</div>
</body>
</html>
EOF;
    }

    /**
     * @param $addr
     * @param $subj
     * @param $mess
     * @return string
     */
    private function tplFatalErrorUserHelp($addr, $subj, $mess) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>	
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Fatal Error</title>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="content-language" content="ru">
</head>
<body style="font-family: Times New Roman;">
	<div style="font-size: 26px;">Помогите сделать этот мир немного лучше! <span style="font-size: 12px;">(+1 в карму)</span></div>
	<div style="margin-top: 10px; margin-bottom: 10px;">
		На сайте, в системе обработки критических ошибок, произошла критическая ошибка <span style="font-family: Verdana; font-size: 12px;">:-(</span> <br>
		Поэтому нам сейчас очень нужна ваша помошь!
	</div>
	<a style="font-size: 18px;" href="mailto:{$addr}?subject={$subj}?&body={$mess}">Нажмите сюда, чтобы отправить письмо разработчикам веб-сайта.</a>
</body>
</html>
EOF;
    }

    /**
     * @return string
     */
    private function tplUserFatalError() { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>	
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Fatal Error</title>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="content-language" content="ru">
</head>
<body>
	Произошла ошибка, меры по ее устранению уже приняты. Приносим свои извинения. Попробуйте зайти позднее.
</body>
</html>
EOF;
    }
}
