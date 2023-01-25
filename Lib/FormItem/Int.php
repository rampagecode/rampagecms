<?php

namespace Lib\FormItem;

use Lib\FormItem;

class Int extends FormItem {
    /**
     * @param mixed $value
     * @return int
     */
    function processValue( $value ) {
        // Если число введено с разделителями в виде пробелов, точек и запятых, то стираем их.
        $value = str_replace( array('.',',',' '), '', $value );
        return intval( $value );
    }
}