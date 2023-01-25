<?php

define('ROOT_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR.'Installer');

require_once ROOT_PATH.DIRECTORY_SEPARATOR.'Templates.php';
require_once ROOT_PATH.DIRECTORY_SEPARATOR.'Installer.php';
require_once ROOT_PATH.DIRECTORY_SEPARATOR.'FormMaker.php';

$view = new Templates();
$form = new FormMaker( $view );

if( empty( $_POST['install'] )) {
    echo $form->baseForm();
    die();
}

$root = str_replace( "\\", "/", dirname( ROOT_PATH )).DIRECTORY_SEPARATOR;

$confPath = $root.'conf/db.ini';
$dumpPath = ROOT_PATH.DIRECTORY_SEPARATOR."dump.sql";
$setup = new Installer(
    $_POST['db_host'],
    $_POST['db_user'],
    $_POST['db_password'],
    $_POST['db_name'],
    $_POST['username'],
    $_POST['password']
);

if( ! $setup->install( $confPath, $dumpPath )) {
    $form->postForm( $setup->errors, $setup->messages );
    die();
}

$cacheFiles = [
    $root.'conf/cache.global.php',
    $root.'conf/cache.tree.php',
];

foreach( $cacheFiles as $file ) {
    if (file_exists( $file )) {
        unlink( $file );
    }
}

if( ! rename(__FILE__, uniqid(__FILE__.'~'))) {
    $setup->errors[] = 'Unable to rename <b>install</b> folder';
    $setup->errors[] = 'Please rename or delete install folder for security reasons';
}

$setup->messages[] = '';
$setup->messages[] = 'Installation completed.';
$setup->messages[] = '';
$setup->messages[] = 'Open <a href="/">Website</a> or <a href="/admin">Admin Panel</a>';

echo $view->pageHTML(
    $view->messagesConsole( $setup->messages, $setup->errors ),
    'body { background-color: #d6f452; }'
);
