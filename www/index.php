<?php

use App\AppManager;
use Lib\FileFlag;

header('Content-Type: text/html; charset=utf-8');
error_reporting( E_ALL ^ E_NOTICE );

define('DEBUG_LOG_TO_SCREEN', DEBUG_ON == true );
define('USE_CUSTOM_ERRORS'  , DEBUG_ON == false );
define('ROOT_PATH', dirname(__DIR__) );

set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_PATH);

require_once(ROOT_PATH.DIRECTORY_SEPARATOR.'Lib/FileFlag.php');
$debugFlag = new FileFlag(ROOT_PATH.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'debug_on');

define('DEBUG_ON', $debugFlag->getFlag() );

require_once(ROOT_PATH.DIRECTORY_SEPARATOR.'vendor/autoload.php');
require_once(ROOT_PATH.DIRECTORY_SEPARATOR.'Lib/Autoloader.php');
require_once(ROOT_PATH.DIRECTORY_SEPARATOR.'App/AppManager.php');

if( ! AppManager::checkDatabaseConfig() ) {
    if( AppManager::checkInstallFolder() ) {
        header('Location: /install.php');
    } else {
        echo 'Fatal Error: file `/install.php` not found and database configuration file does not exists';
        exit();
    }
}

try {
    $app = AppManager::start();
    echo $app->getResult();
}
catch( \Exception $e ) {
    echo "Fatal error: ";

     if( DEBUG_ON ) {
        echo '<b>'.$e->getFile().' line '.$e->getLine().'</b><br/>';
        echo '<i>'.$e->getMessage().'</i>';
        echo '<pre>'.$e->getTraceAsString().'</pre>';
     } else {
         echo 'To see the error enable Debug Mode in admin panel or create an empty file named `debug_on` in the directory named `conf`.';
     }
}