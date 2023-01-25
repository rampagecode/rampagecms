<?php

namespace Lib\FormItem;

use Lib\FormItem;

class Bool extends FormItem {
    /**
     * @param mixed $value
     * @return bool
     */
    function processValue( $value ) {
        return $value ? true : false;
    }
}