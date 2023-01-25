<?php

namespace Lib\FormItem;

use Lib\FormItem;

class UserName extends FormItem {
    /**
     * @param mixed $value
     * @return string
     */
    function processValue( $value ) {
        $value = trim( $value );

        if( preg_match( "/^[_0-9a-zа-я.\s]{3,25}$/ui", $value )) {
            // Удаляем множественные пробелы
            $value = preg_replace( "/\s{2,}/", " ", $value );

            // Удаляем хитрые пробелы
            $value = str_replace( chr(160), ' ', $value );
            $value = str_replace( chr(173), ' ', $value );

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
        return "<b>{$title}</b>: Имя может содержать только буквы русского и английского алфавитов, цифры, пробел и знак подчеркивания. Длина от 3 до 25 символов.";
    }
}