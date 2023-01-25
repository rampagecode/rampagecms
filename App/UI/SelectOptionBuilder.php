<?php

namespace App\UI;

class SelectOptionBuilder extends AbstractBuilder {
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $value;

    /**
     * @var bool
     */
    public $selected = false;

    /**
     * @param string $title
     * @param string $value
     */
    public function __construct( $title, $value ) {
        $this->title = $title;
        $this->value = $value;
    }

    /**
     * @return string
     */
    function build() {
        $selected = $this->selected ? 'selected="selected"' : '';

        return '<option value="'.$this->value.'" '.$selected.'>'.$this->title.'</option>';
    }
}
