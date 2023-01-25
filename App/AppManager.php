<?php

namespace App;

use Admin\Action\Login\LoginController;
use Admin\AdminController;
use Admin\AdminControllerParameters;
use Admin\AdminSession;
use App\Page\PageLayout;
use App\Page\PageResult;
use App\User\User;
use Lib\FileFlag;
use Lib\ResultInterface;
use Sys\Config\ConfigManager as ConfigManager;
use Sys\Database\DatabaseInterface;
use Sys\Database\DatabaseManager as DatabaseManager;
use Data\Page\Table;
use Sys\File\FileManager;
use Sys\Input\InputInterface;
use Sys\Language\LanguageManager;
use Sys\Log\Factory;
use Sys\Log\Logger;
use Sys\Cookie\CookieManager;
use App\Page\Page;
use App\Page\PageManager;
use App\Page\PageParser;
use App\Session\SessionManager as SessionManager;
use Sys\Input\InputManager as InputManager;
use Sys\Error\ErrorHandler;
use Sys\Log\LoggerInterface;
use Sys\Request\RequestManager;

require_once 'AppInterface.php';

class AppManager implements AppInterface {
    /**
     * @var AppManager
     */
	protected static $_instance = null;

    /**
     * @var RequestManager
     */
    public $request;

    /**
     * @var InputManager
     */
	public $input;

    /**
     * @var ConfigManager
     */
	public $config;

    /**
     * @var SessionManager
     */
	public $session;

    /**
     * @var DatabaseManager
     */
	public $database;

    /**
     * @var LanguageManager
     */
    public $language;

    /**
     * @var PageManager
     */
    public $pages;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var CookieManager
     */
    public $cookies;

    /**
     * @var FileManager
     */
    public $files;

    /**
     * @var ResultInterface
     */
    private $result = "";

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

	private function __clone() {}
	private function __construct() {}

    /**
     * @return AppManager|null
     */
	static function start() {
		if( is_null( self::$_instance )) {
			self::$_instance = new self();
            self::$_instance->run();
		}

        return self::$_instance;
	}

    /**
     * @return AppManager|null
     */
    static function getInstance() {
        return self::$_instance;
    }

    /**
     * @return bool
     */
    static function checkInstallFolder() {
        return file_exists(ROOT_PATH.DIRECTORY_SEPARATOR.'Installer' );
    }

    /**
     * @return bool
     */
    static function checkDatabaseConfig() {
        $path = ROOT_PATH.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'db.ini';
        $text = @file_get_contents( $path );
        return ! empty( $text );
    }

    /**
     * Время в миллисекундах
     * @return float
     */
    private function microTime() {
        $mtime = microtime();
        $mtime = explode (' ', $mtime);
        return $mtime[1] + $mtime[0];
    }

	private function run() {
        $startTime = $this->microTime();

        spl_autoload_register( function( $className ) {
            $debug = DEBUG_ON;
            $debug && print( 'Original class name: <b>'.$className.'</b><br>'."\n" );

            $className  = str_replace( '_', DIRECTORY_SEPARATOR, $className );
            $className  = str_replace( '\\', DIRECTORY_SEPARATOR, $className );
            $className .= '.php';

            $debug && print( 'Translated to file path: <b>'.$className.'</b><br>'."\n" );

            $incPath = explode( PATH_SEPARATOR, get_include_path() );
            $incPath = array_reverse( $incPath );

            foreach( $incPath AS $path ) {
                $path .= DIRECTORY_SEPARATOR;
                $testPath = $path . $className;

                $debug && print( 'Test include path: <b>'.$testPath.'</b><br>'."\n" );

                if( file_exists( $testPath )) {
                    $debug && print( '<u>File exists: <b>'.$testPath.'</b></u><br><br>'."\n" );

                    require( $testPath );
                    return true;
                }
            }

            $debug && print( '<u>File not found </u><br><br>'."\n" );
        });

        $this->errorHandler = new ErrorHandler();

        if( USE_CUSTOM_ERRORS ) {
            ob_start([ $this->errorHandler, 'fatalErrorHandler' ]);
            set_error_handler([ $this->errorHandler, 'errorHandler' ]);
        } else {
            ob_start();
        }

        $this->initSystem();

        if( $this->request->isAdmin() ) {
            $this->session = new SessionManager( $this->input, $this->config, $this->cookies, $this->logger, $this->request );
            $this->pages = new PageManager( $this->files->rootDir('conf', 'cache.tree.php'), new Table());
            $this->result = $this->runAdmin();
        } else {
            if( $this->loadLimitExceeded() ) {
                $this->result = new PageResult();
                $this->result->setContent('<h1>Server too busy.</h1> Please wait a moment and try again.');
                $this->logger->end();
                ob_clean();
                return;
            }

            $this->session = new SessionManager( $this->input, $this->config, $this->cookies, $this->logger, $this->request );

            if( $this->isSiteOffline()
                && false == $this->session->getUser()->getGroupObject()->getAccess()->canAccessOffline()
            ) {
                $this->result = new PageResult();
                $this->result->setContent('<h1>Site is offline.</h1> Please try again later.');
                $this->logger->end();
                ob_clean();
                return;
            }

            $this->pages = new PageManager( $this->files->rootDir('conf', 'cache.tree.php'), new Table());
            $this->result = $this->runPublic();
        }

        if( DEBUG_ON ) {
            $currentTime = $this->microTime();
            $runningTime = round(( $currentTime - $startTime ), 5 );
            $this->log("Время выполнения: {$runningTime}");

            $runlog = ob_get_contents();

            if( ! empty( $runlog )) {
                $this->log( strip_tags( $runlog ), LoggerInterface::ERR );
                $this->result->embedLog( $runlog );
            }
        }

        $this->logger->end();
        ob_clean();
    }

    private function initSystem() {
        $this->request = new RequestManager();
        $this->files = new FileManager( $this->request->isAdmin() );
        $this->logger = new Logger( new Factory(), $this->files );
        $this->errorHandler->setLogger( $this->logger );
        Logger::setDefault( $this->logger );
        $this->database = new DatabaseManager( $this->files->rootDir( 'conf', 'db.ini' ), $this->logger );
        $this->cookies = new CookieManager();
        $this->config = new ConfigManager( $this->logger, $this->database, $this->files, $this->request );
        $this->language = new LanguageManager( $this->request, $this->files );
        $this->input = new InputManager( $this->logger, $this->config, $this->language );
    }

    /**
     * @return ResultInterface
     * @throws AppException
     */
    private function runPublic() {
        if( ! $this->getCurrentSitePageId() ) {
            if( $this->pages->getPageId( $this->request->baseURL( 'error_404' ))) {
                $this->redirect( $this->request->baseURL( 'error_404' ));
            } else {
                $this->redirect( $this->request->baseURL() );
            }
        }

        //-----------------------------------------
        // Загрузка страниц
        //-----------------------------------------

        $pageId = $this->getCurrentSitePageId();
        $page = Page::createById( $pageId, new Table() );
        $template = $this->rootDir('Public', 'Layouts', $page->getLayout() . '.html');
        $layout = new PageLayout( $template );
        $result = new PageResult();
        $parser = new PageParser( $this, $page );
        $result->setParser( $parser );
        $result->setContent( $layout->load() );

        return $result;
    }

    /**
     * @return ResultInterface
     */
    private function runAdmin() {
        $sidName = 'adsess';
        $session = new AdminSession( $this->config );
        $session->validate( $this->in( $sidName ));
        $params = new AdminControllerParameters( $this );

        if( $session->isValid() ) {
            $this->setUser( $session->getSessionUser() );
            $this->request->addBaseUrlParameter( $sidName, $this->in( $sidName ));

            $controller = new AdminController( $this, $params );
            return $controller->run();
        } else {
            $controller = new LoginController( $this );
            $controller->setSession( $session );
            $controller->setRequestParameters( $params );
            $controller->auto_run();
            return $controller;
        }
    }

    /**
     * @return string
     */
    function getResult() {
        return $this->result->getResult();
    }

    /**
     * @param string $url
     * @return void
     */
    function redirect( $url ) {
        $base_url = 'http'.( empty( $_SERVER['HTTPS'] ) ? '' : 's' ).'://'.$_SERVER['SERVER_NAME'];
        $bad  = array( '?', '/', '&', '.', '=' );
        $good = array( '\?', '\/', '\&', '\.', '\=' );

        $test_base_url = str_replace( $bad, $good, $base_url );

        if( ! preg_match( "#^{$test_base_url}#", $url )) {
            $url = $base_url . $url;
        }

        if( $this->config->getVar('header_redirect') == 'refresh' ) {
            header("Refresh: 0;url=".$url);
        }
        elseif( $this->config->getVar('header_redirect') == 'html' ) {
            echo("<html><head><meta http-equiv='refresh' content='0; url=$url'></head><body></body></html>");
        } else {
            header("Refresh: 0;url=".$url );
        }

        exit();
    }

    //-----------------------------------------
    // AppInterface implementation
    //-----------------------------------------

    /**
     * @param $name string|null
     * @return string|InputInterface
     */
    public function in( $name = null ) {
        if( empty( $name )) {
            return $this->input;
        } else {
            return $this->input[ $name ];
        }
    }

    /**
     * @return DatabaseInterface
     */
    public function db() {
        return $this->database;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->session->getUser();
    }

    /**
     * @param User $user
     * @return void
     */
    function setUser( User $user ) {
        $this->session->setUser( $user );
    }

    /**
     * @return void
     */
    function setGuest() {
        $this->session->unloadMember();
        $this->session->updateGuestSession();
    }

    /**
     * @param string $args,...
     * @return string
     */
    public function baseURL( $args = null ) {
        return call_user_func_array([ $this->request, 'baseURL'], func_get_args() );
    }

    /**
     * @param string $args,...
     * @return string
     */
    public function pageURL( $args = null ) {
        return call_user_func_array([ $this->request, 'pageURL'], func_get_args() );
    }

    /**
     * @param string $name Имя куки
     * @return false|string Значение куки
     */
    public function getCookie( $name ) {
        return $this->cookies->getCookie( $name );
    }

    /**
     * @param string $name Имя куки
     * @param string $value Значение куки
     * @param bool $sticky Устанавливаем куку на год
     * @param int $expires Срок годности куки (в днях), только если $sticky = false
     */
    public function setCookie( $name, $value = '', $sticky = true, $expires = 0 ) {
        $this->cookies->setCookie( $name, $value, $sticky, $expires );
    }

    /**
     * @return string|null IP-адрес
     */
    public function getIpAddr() {
        return $this->input->getIpAddr();
    }

    /**
     * @return array
     */
    public function getBrowser() {
        return $this->input->getBrowser();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getVar( $key ) {
        return $this->config->getVar( $key );
    }

    /**
     * @param string $message
     * @param int $priority
     * @return void|LoggerInterface
     */
    public function log( $message = null, $priority = null ) {
        if( $message == null ) {
            return $this->logger;
        } else {
            $this->logger->add( $message, $priority );
        }
    }

    /**
     * @param string $args,...
     * @return string
     */
    function rootDir( $args = null ) {
        return call_user_func_array([ $this->files, 'rootDir'], func_get_args() );
    }

    /**
     * @param string $args,...
     * @return string
     */
    function assetDir( $args = null ) {
        return call_user_func_array([ $this->files, 'assetDir'], func_get_args() );
    }

    function language() {
        return $this->language;
    }

    /**
     * @return ConfigManager
     */
    function config() {
        return $this->config;
    }

    /**
     * @param string $args,...
     * @return string
     */
    public function assetURL( $args = null ) {
        return call_user_func_array([ $this->request, 'assetURL'], func_get_args() );
    }

    /**
     * @return bool
     */
    function isAjaxRequest() {
        return $this->request->isAjaxRequest();
    }

    /**
     * @return PageManager
     */
    function pages() {
        return $this->pages;
    }

    /**
     * @return int
     */
    function getTimeOffset() {
        $user = $this->getUser();

        $offset = $user instanceof User
            ? $user->getTimeOffset()
            : (int)$this->getVar('time_offset') * 3600
        ;

        $adjust = (int)$this->getVar('time_adjust' );

        if( ! empty( $adjust )) {
            $offset += $adjust * 60;
        }

        if( $user->useDST() ) {
            $offset += 3600;
        }

        return $offset;
    }

    private function loadLimitExceeded() {
        $loadLimit = (int)$this->config->getVar('load_limit');

        if( $loadLimit == 0 ) {
            return false;
        }

        if( @file_exists('/proc/loadavg' )) {
            if( $fh = @fopen( '/proc/loadavg', 'r' )) {
                $data = @fread( $fh, 6 );
                @fclose( $fh );

                $load_avg = explode( ' ', $data );
                $serverLoad = (int)trim( $load_avg[0] );

                if( $serverLoad > $loadLimit ) {
                    $this->log( 'Превышен предел нагрузки на сервер!' );
                    $this->log( 'Текущая нагрузка: ' . $serverLoad );
                    $this->log( 'Максимально допустимая: ' . $loadLimit );

                    return true;
                }
            }
        }
        elseif( $uptime = @exec( 'uptime' )) {
            preg_match( "/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $uptime, $load );

            $serverLoad = (int)$load[1];

            if( $serverLoad > $loadLimit ) {
                $this->log( 'Превышен предел нагрузки на сервер!' );
                $this->log( 'Текущая нагрузка: ' . $serverLoad );
                $this->log( 'Максимально допустимая: ' . $loadLimit );

                return true;
            }
        } else {
            $this->logger->add('Невозможно оценить загруженность сервера.');
        }

        return false;
    }

    /**
     * @return bool
     */
    function isSiteOffline() {
        $file = $this->rootDir('conf', 'site_is_offline');
        $flag = new FileFlag( $file );

        return $flag->getFlag();
    }

    /**
     * @param bool $offline
     * @return bool
     */
    function toggleOffline( $offline ) {
        $file = $this->rootDir('conf', 'site_is_offline');
        $flag = new FileFlag( $file );

        return $flag->setFlag( $offline );
    }

    /**
     * @return int
     */
    function getCurrentSitePageId() {
        return $this->pages->getPageId( $this->request->pageURL() );
    }
}