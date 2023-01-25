<?php

namespace Admin;

class AdminSection {
    public $name;
    public $modules;

    public function __construct( $name, $modules = [] ) {
        $this->name = $name;
        $this->modules = $modules;
    }
}
