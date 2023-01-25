<?php

namespace Lib;

/**
 * override arrayAccessProperty() in your class and return a name of a property to use for array access
 */
trait ArrayAccess {

    protected function arrayAccessProperty() {
        return null;
    }

    public function offsetSet( $offset, $value ) {
        $var = $this->arrayAccessProperty();

        if( is_null( $offset )) {
            $this->{$var}[] = $value;
        } else {
            $this->{$var}[ $offset ] = $value;
        }
    }

    public function offsetExists( $offset ) {
        $var = $this->arrayAccessProperty();
        return isset( $this->{$var}[ $offset ] );
    }

    public function offsetUnset( $offset ) {
        $var = $this->arrayAccessProperty();
        unset( $this->{$var}[ $offset ] );
    }

    public function offsetGet( $offset ) {
        $var = $this->arrayAccessProperty();
        return isset( $this->{$var}[ $offset ] )
            ? $this->{$var}[ $offset ]
            : null;
    }
}
