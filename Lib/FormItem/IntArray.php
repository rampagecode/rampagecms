<?php

namespace Lib\FormItem;

use Lib\FormItem;

class IntArray extends FormItem {
    /**
     * @param mixed $value
     * @return int[]
     */
    function processValue( $value ) {
        if( ! is_array( $value )) {
            return [];
        }

        $result = [];

        foreach( $value as $item ) {
            $item = str_replace( array('.',',',' '), '', $item );
            $result[] = intval( $item );
        }

        return $result;
    }
}