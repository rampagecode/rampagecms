<?php

namespace Lib\FormItem;

use Lib\FormItem;

class String extends FormItem {
    /**
     * @param mixed $value
     * @return string
     */
    function processValue( $value ) {
        return trim( $value );
    }
}