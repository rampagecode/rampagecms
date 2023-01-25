<?php

namespace App\UI;

class TagBuilder extends AbstractBuilder {
    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $innerHTML;

    /**
     * @param string $tag
     * @param string $innerHTML
     * @return TagBuilder
     */
    public static function make( $tag, $innerHTML = null ) {
        return new self( $tag, $innerHTML );
    }

    /**
     * @param string $tag
     * @param string $innerHTML if null then single tag will be build
     */
    public function __construct( $tag, $innerHTML = null ) {
        $this->tag = $tag;
        $this->innerHTML = $innerHTML;
    }

    function setInnerHTML( $html ) {
        $this->innerHTML = $html;
    }

    function appendInnerHTML( $html ) {
        $this->innerHTML .= $html;
    }

    /**
     * @param string $value
     * @return string
     */
    private function escapeQuotes( $value ) {
        $value = str_replace( '"', "&quot;", $value );
        $value = str_replace( "'", "&#39;", $value );

        return $value;
    }

    function build() {
        $getPublicProperties = create_function('$obj', 'return get_object_vars( $obj );');
        $properties = $getPublicProperties( $this );
        $properties = array_filter( $properties, function( $value ) {
            if( is_null( $value )) {
                return false;
            }

            if( is_string( $value ) && strlen( $value ) == 0 ) {
                return false;
            }

            if( is_bool( $value ) && $value === false ) {
                return false;
            }

            return true;
        });
        $attributes = array_map( function( $value, $key ) {
            $value = $this->escapeQuotes( $value );

            return "{$key}=\"{$value}\"";
        }, array_values( $properties ), array_keys( $properties ));

        if( $this->innerHTML === null ) {
            return "<{$this->tag} ".join(' ', $attributes ).' />';
        } else {
            return "<{$this->tag} ".join(' ', $attributes )." >{$this->innerHTML}</{$this->tag}>";
        }
    }
}