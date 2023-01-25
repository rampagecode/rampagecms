<?php

namespace App\Content;

use Data\Folder\Row as Row;

class Folder {
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