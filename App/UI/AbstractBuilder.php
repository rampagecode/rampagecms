<?php

namespace App\UI;

use App\AppException;

abstract class AbstractBuilder {
    /**
     * @return string
     */
    abstract function build();

    /**
     * @param string $property
     * @param mixed $value
     * @return AbstractBuilder
     */
    function set( $property, $value ) {
        $this->$property = $value;
        return $this;
    }

    /**
     * @param $property
     * @return mixed
     * @throws AppException
     */
    function get( $property ) {
        if( ! property_exists( $this, $property )) {
            $class = get_class( $this );
            throw new AppException("Property '{$property}' does not exists in the class '{$class}'");
        }

        return $this->$property;
    }
}
