<?php

namespace App\Content;

use Data\Image\Row as Row;

class Image {
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