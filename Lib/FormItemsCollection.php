<?php

namespace Lib;

class FormItemsCollection {
    /**
     * @var array FormItem[]
     */
    private $items = [];

    /**
     * @param FormItem[] $items
     */
    public function __construct( $items ) {
        $this->items = $items;
    }

    /**
     * @param $name
     * @return FormItem|null
     */
    public function byName( $name ) {
        foreach( $this->items as $item ) {
            if( $item->name == $name ) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return FormItem[]
     */
    function toArray() {
        return $this->items;
    }

    /**
     * @param $name
     * @return void
     */
    function remove( $name ) {
        $this->items = array_filter(
            $this->items,
            function( $item ) use ( $name ) {
                return $item->name != $name;
            }
        );
    }
}