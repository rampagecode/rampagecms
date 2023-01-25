<?php

namespace App\Module;

class ModuleInfo {
    public $name;
    public $title;
    public $description;

    public function __construct( $name, $title, $description ) {
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
    }
}