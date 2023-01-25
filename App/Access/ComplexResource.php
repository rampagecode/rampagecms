<?php

namespace App\Access;

class ComplexResource extends AccessibleResource {
    /**
     * @var ResourceAccess
     */
    public $resource;

    public function __construct( $resource, $name, $title, $description = null ) {
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
        $this->resource = $resource;
    }
}