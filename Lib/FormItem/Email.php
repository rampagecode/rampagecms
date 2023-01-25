<?php

namespace Lib\FormItem;

use Lib\FormItem;

class Email extends FormItem {
    /**
     * @param mixed $value
     * @return string
     */
    function processValue( $value ) {
        $value = trim( $value );
        $value = str_replace( " ", "", $value );
        $value = strtolower( $value );

        if( substr_count( $value, '@' ) > 1 ) {
            return FALSE;
        }

        $value = preg_replace( "#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/\s]#", "", $value );

        if( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $value )) {
            return $value;
        } else {
            return '';
        }
    }
}