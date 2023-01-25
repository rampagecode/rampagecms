<?php

namespace App\UI;

class FormSelectOptions {

    /**
     * @var array<string, mixed>
     */
    private $options = [];

    function addOption( $title, $value ) {
        $this->options[ $title ] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    function getOptions() {
        return $this->options;
    }
}
