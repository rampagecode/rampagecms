<?php

namespace Module\Posts\Frontend;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class PostsController implements ModuleControllerProtocol {

    public function __construct( AppInterface $app ) {
    }

    public function auto_run() {
        $this->showListAction();
    }

    function showListAction() {

    }
}