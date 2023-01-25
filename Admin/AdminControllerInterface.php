<?php

namespace Admin;

use App\Module\ModuleControllerProtocol;
use Lib\ResultInterface;

interface AdminControllerInterface extends ModuleControllerProtocol {
    /**
     * @return string[]
     */
    function availableActions();

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters );
}
