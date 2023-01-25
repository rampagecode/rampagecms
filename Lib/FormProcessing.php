<?php

namespace Lib;

use Sys\Input\InputInterface;
use Lib\LibraryException;

class FormProcessing {
    /**
     * @var FormItem[]
     */
    private $items = [];

    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @param FormItem[] $items
     */
    public function __construct( $items ) {
        $this->items = $items;
    }

    function process( InputInterface $input ) {
        foreach( $this->items as $item ) {
            try {
                $this->values[ $item->name ] = $item->process( $input );
            } catch( LibraryException $e ) {
                $this->values[ $item->name ] = $item->getRawValue( $input );
                $this->errors[] = $e->getMessage();
            }
        }
    }

    /**
     * @return string[]
     */
    function getErrors() {
        return $this->errors;
    }

    /**
     * @return array
     */
    function getValues() {
        return $this->values;
    }

    function getItemByName( $name ) {
        $items = array_filter( $this->items, function( $item ) use ( $name ) {
           return $item->name === $name;
        });

        if( count( $items ) > 0 ) {
            return $items[0];
        }

        return null;
    }
}