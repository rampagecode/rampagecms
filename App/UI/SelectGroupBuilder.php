<?php

namespace App\UI;

class SelectGroupBuilder extends AbstractBuilder {
    /**
     * @var string
     */
    public $label;

    /**
     * @var SelectOptionBuilder[]
     */
    public $options;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param SelectOptionBuilder[] $options
     * @param string $label
     */
    public function __construct( array $options, $label ) {
        $this->label = $label;
        $this->options = $options;
    }

    /**
     * @return string
     */
    function build() {
        $out = '<optgroup label="' . $this->label . '">';

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

            $out .= $option->build() . "\n";
        }

        $out .= '</optgroup>';

        return $out;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    function setValue( $value ) {
        $this->value = $value;
        return $this;
    }
}
