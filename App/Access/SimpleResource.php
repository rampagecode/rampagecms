<?php

namespace App\Access;

class SimpleResource extends AccessibleResource {
    public function __construct( $name, $title, $description = null ) {
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
    }
}
