<?php

namespace Lib\FormItem;

use Lib\FormItem;

class Float extends FormItem {
    /**
     * @param mixed $value
     * @return float
     */
    function processValue( $value ) {
        // Если число введено с разделителем - запятой, то заменяем на точку
        $value = str_replace( ',', '.', $value );
        return floatval( $value );
    }
}