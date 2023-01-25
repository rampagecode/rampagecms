<?php

namespace App\UI;

class InputBuilder extends TagBuilder {
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type = 'text';

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var string
     */
    public $class = 'textinput';

    /**
     * @param array<string, mixed> $properties
     */
    public function __construct( $properties = [] ) {
        parent::__construct( 'input' );

        foreach( $properties as $key => $value ) {
            $this->set( $key, $value );
        }
    }
}