<?php

namespace Lib\FormItem;

use Lib\FormItem;

class Login extends FormItem {
    /**
     * @param mixed $value
     * @return string
     */
    function processValue( $value ) {
        $value = trim( $value );

        // Удаляем хитрые пробелы
        $value = str_replace( chr(160), ' ', $value );
        $value = str_replace( chr(173), ' ', $value );

        if( preg_match( "/^[_0-9a-zA-Z]{3,32}$/", $value )) {
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
        return "<b>{$title}</b>: Логин может содержать только буквы английского алфавитов, цифры и знак подчеркивания. Длина от 3 до 32 символов.";
    }
}