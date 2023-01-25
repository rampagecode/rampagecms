<?php

namespace Admin\Menu\Tree;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class TreeController implements ModuleControllerProtocol {
    /**
     * @var AppInterface
     */
    private $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        $view = new TreeView();
        return $view->main();
    }
}