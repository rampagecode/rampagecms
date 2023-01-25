<?php

namespace App\Content;

use Data\Text\Row as Row;

class Text {
    /**
     * @var Row
     */
    private $row;

    /**
     * @param Row|null $row
     */
    public function __construct( $row = null ) {
        $this->row = $row;
    }
}