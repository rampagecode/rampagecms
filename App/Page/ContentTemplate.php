<?php

namespace App\Page;

class ContentTemplate implements TemplateProtocol {
    public $id;
    public $property;

    public function __construct( $id = null ) {
        $this->id = $id;
    }

    /**
     * @param array $r
     * @return void
     */
    function parseTemplate( array $r ) {
        switch( $r[1] ) {
            case '~':
                if( $val = intval( $r[2] )) {
                    $this->id = $val;
                }
                break;

            case '?':
                $this->property = $r[2];
                break;
        }
    }

    /**
     * @param array $overload
     * @return void
     */
    function parseOverload( array $overload ) {
        $this->id = isset( $overload['id'] ) ? $overload['id'] : $this->id;
        $this->property = isset( $overload['property'] ) ? $overload['property'] : $this->property;
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