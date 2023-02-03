<?php

namespace App\Page;

class ContentTemplate implements TemplateProtocol {
    public $id;
    public $property;
    public $reference;

    public function __construct( $id = null, $property = null, $reference = null ) {
        $this->id = $id;
        $this->property = $property;
        $this->reference = $reference;
    }

    /**
     * @param array $r
     * @return void
     */
    function parseTemplate( array $r ) {
        if( isset( $r[0] )) {
            if( $id = intval( $r[0] )) {
                $this->id = $id;
            } else {
                $this->reference = $r[0];
            }
        }

        if( isset( $r[1] ) && $r[1] == '?' ) {
            if( isset( $r[2] )) {
                $this->property = $r[2];
            }
        }
    }

    /**
     * @param array $overload
     * @return void
     */
    function parseOverload( array $overload ) {
        $this->id = isset( $overload['id'] ) ? $overload['id'] : $this->id;
    }

    /**
     * @return array
     */
    function buildOverload() {
        return [
            'id' => $this->id,
            'type' => 'content',
        ];
    }
}