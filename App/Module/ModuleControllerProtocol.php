<?php

namespace App\Module;

use App\AppInterface;

interface ModuleControllerProtocol {
    public function __construct( AppInterface $app );

    /**
     * @return string
     */
    public function auto_run();
}
