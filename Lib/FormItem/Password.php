<?php

namespace Lib\FormItem;

use Lib\FormItem;

class Password extends FormItem {
    /**
     * @param mixed $value
     * @return string
     */
    function processValue( $value ) {
        $value = trim( $value );

        if( preg_match( "/^(.+){6,32}$/", $value )) {
            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param $title
     * @return string
     */
    public function validationError( $title ) {
        return "<b>{$title}</b>: Пароль должен содержать от 6 до 32 символов.";
    }
}