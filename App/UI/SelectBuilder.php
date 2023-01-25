<?php

namespace App\UI;

class SelectBuilder extends TagBuilder {
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $size = 1;

    /**
     * @var string
     */
    public $class = 'dropdown';

    /**
     * @var bool
     */
    public $multiple = false;

    /**
     * @var string
     */
    public $onChange;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var SelectOptionBuilder[]
     */
    private $options = [];

    /**
     * @var SelectGroupBuilder[]
     */
    private $groups = [];

    public function __construct() {
        parent::__construct( 'select', '' );
    }

    /**
     * @return string
     */
    function build() {
        $content = '';

        foreach( $this->groups as $group ) {
            $content .= $group->build() . "\n";
        }

        foreach( $this->options as $option ) {
            if( ! empty( $this->value )) {
                if( is_array( $this->value )) {
                    if( in_array( $option->value, $this->value )) {
                        $option->selected = true;
                    }
                } else {
                    if( $option->value == $this->value ) {
                        $option->selected = true;
                    }
                }
            }

            $content .= $option->build() . "\n";
        }

        $this->setInnerHTML( $content );

        if( $this->multiple && substr( $this->name, -2 ) != '[]' ) {
            $this->name .= '[]';
        }

        return parent::build();
    }

    /**
     * @param SelectOptionBuilder[] $options
     * @return SelectBuilder
     */
    function setOptions( $options ) {
        $this->options = $options;
        return $this;
    }

    /**
     * @param SelectOptionBuilder $option
     * @return SelectBuilder
     */
    function addOption( $option ) {
        $this->options[] = $option;
        return $this;
    }

    /**
     * @param SelectGroupBuilder[] $groups
     * @return SelectBuilder
     */
    function setGroups( $groups ) {
        $this->groups = $groups;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    function setValue( $value ) {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    function getValue() {
        if( empty( $this->value )) {
            return $this->options[0]->value;
        } else {
            return $this->value;
        }
    }
}