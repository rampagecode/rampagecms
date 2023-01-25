<?php

namespace Sys\Input;

use Lib\ArrayAccess;
use Sys\Config\ConfigManager;
use Sys\Language\LanguageManager;
use Sys\Log\Logger;

class InputManager implements InputInterface {
	private $data;

    /**
     * @var ConfigManager
     */
    private $conf;

    use ArrayAccess;

    protected function arrayAccessProperty() {
        return 'data';
    }

    function __construct( Logger $logger, ConfigManager $conf, LanguageManager $lang ) {
		$logger->add("Input");

        $this->conf = $conf;

        InputCleaner::cleanGlobals( $_GET );
        InputCleaner::cleanGlobals( $_POST );
        InputCleaner::cleanGlobals( $_COOKIE );
        InputCleaner::cleanGlobals( $_REQUEST );

        $this->data = [];
        $this->data = InputCleaner::parseGlobals( $_GET, array() );
        $this->data = InputCleaner::parseGlobals( $_POST, $this->data );
    }

    /**
     * Возвращает информацию о браузере пользователя
     * @return array Массив типа [ browser => ie, version => 6, name => IE 6 ]
     */
    public function getBrowser() {
        $version   = 0;
        $browser   = "Неизвестен";
        $name      = "Неизвестен";
        $useragent = strtolower( $_SERVER['HTTP_USER_AGENT'] );

        // Opera
        if( strstr( $useragent, 'opera' )) {
            preg_match( "#opera[ /]([0-9\.]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'opera', 'version' => $ver[1], 'name' => 'Opera '.$ver[1] );
        }

        // IE
        if ( strstr( $useragent, 'msie' ) ) {
            preg_match( "#msie[ /]([0-9\.]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'ie', 'version' => $ver[1], 'name' => 'IE '.$ver[1] );
        }

        // Chrome
        if ( strstr( $useragent, 'chrome' ) ) {
            preg_match( "#chrome/([0-9.]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'chrome', 'version' => $ver[1], 'name' => 'Chrome '.$ver[1] );
        }

        // Safari
        if ( strstr( $useragent, 'safari' ) ) {
            preg_match( "#safari/([0-9.]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'safari', 'version' => $ver[1], 'name' => 'Safari '.$ver[1] );
        }

        // FireFox
        if ( strstr( $useragent, 'firefox' ) ) {
            preg_match( "#firefox[ /]([0-9\.]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'firefox', 'version' => $ver[1], 'name' => 'FireFox '.$ver[1] );
        }

        // Mozilla
        if ( strstr( $useragent, 'gecko' ) ) {
            preg_match( "#gecko/(\d+)#", $useragent, $ver );
            return array( 'browser' => 'gecko', 'version' => $ver[1], 'name' => 'Gecko Engine '.$ver[1] );
        }

        // Older Mozilla
        if ( strstr( $useragent, 'mozilla' ) ) {
            preg_match( "#^mozilla/[5-9]\.[0-9.]{1,10}.+rv:([0-9a-z.+]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'mozilla', 'version' => $ver[1], 'name' => 'Mozilla '.$ver[1] );
        }

        // Konqueror
        if ( strstr( $useragent, 'konqueror' ) ) {
            preg_match( "#konqueror/([0-9.]{1,10})#", $useragent, $ver );
            return array( 'browser' => 'konqueror', 'version' => $ver[1], 'name' => 'Konqueror '.$ver[1] );
        }

        return array( 'browser' => $browser, 'version' => $version, 'name' => $name );
    }

    /**
     * Возвращает предполагаемый IP-адрес текущего клиента
     * @return string|null IP-адрес
     */
    public function getIpAddr() {
        $xforward_matching = true;
        $addrs = array();
        $ipAddr = null;

        if( $xforward_matching ) {
            foreach( array_reverse( explode( ',', $this->conf->getEnv( 'HTTP_X_FORWARDED_FOR' ))) as $a ) {
                $a = trim( $a );

                if( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $a )) {
                    $addrs[] = $a;
                }
            }

            $addrs[] = $this->conf->getEnv('HTTP_CLIENT_IP');
            $addrs[] = $this->conf->getEnv('HTTP_X_CLUSTER_CLIENT_IP');
            $addrs[] = $this->conf->getEnv('HTTP_PROXY_USER');
        }

        $addrs[] = $this->conf->getEnv('REMOTE_ADDR');

        foreach ( $addrs as $ip ) {
            if( $ip ) {
                preg_match( "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/", $ip, $match );

                $ipAddr = $match[1].'.'.$match[2].'.'.$match[3].'.'.$match[4];

                if( !empty($ipAddr) AND $ipAddr != '...' ) {
                    break;
                }
            }
        }

        return $ipAddr;
    }
}