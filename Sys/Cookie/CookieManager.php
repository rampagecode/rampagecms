<?php

namespace Sys\Cookie;

use Sys\Input\InputCleaner;

class CookieManager {

    /**
     * Возвращает значение установленной куки по имени
     * @param string $name Имя куки
     * @return false|string Значение куки
     */
    public function getCookie( $name ) {
        $cookie_id 	= '';

        if( isset( $_COOKIE[ $cookie_id.$name] )) {
            return InputCleaner::parseCleanValue( urldecode( $_COOKIE[ $cookie_id.$name ] ));
        } else {
            return false;
        }
    }

    /**
     * Устанавливает куки.
     * @param string $name Имя куки
     * @param string $value Значение куки
     * @param bool $sticky Устанавливаем куку на год
     * @param int $expires Срок годности куки (в днях), только если $sticky = false
     */
    public function setCookie( $name, $value = '', $sticky = true, $expires = 0 ) {
        if( $sticky ) {
            $expires = time() + ( 60*60*24*365 );
        } else
            if( $expires ) {
                $expires = time() + ( $expires * 86400 );
            } else {
                $expires = false;
            }

        $cookie_id 	= '';
        $domain 	= '';
        $path   	= '/';

        if( in_array( $name, array( 'session_id', 'member_id', 'pass_hash' ))) {
            if( PHP_VERSION < 5.2 ) {
                @setcookie( $cookie_id.$name, $value, $expires, $path );
            } else {
                @setcookie( $cookie_id.$name, $value, $expires, $path, $domain, null, true );
            }
        } else {
            @setcookie( $cookie_id.$name, $value, $expires, $path, $domain );
        }
    }
}