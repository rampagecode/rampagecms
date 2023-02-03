<?php

namespace App\Module;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;

abstract class ModuleBackendController extends ModuleFrontendController implements AdminControllerInterface {
    /**
     * @var AdminControllerParameters
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $moduleIdUrlParameter = 'cms_module_id';

    function availableActions() {
        return [];
    }

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    /**
     * @param int $idx
     * @param string $action
     * @param array<string, mixed> $parameters
     * @return string
     */
    function buildURL( $idx, $action, $parameters = [] ) {
        $baseURL = $this->parameters->buildURL( $idx, $action, 'Posts', 'module' );

        if( !isset( $parameters[ $this->moduleIdUrlParameter ] )) {
            $parameters[ $this->moduleIdUrlParameter ] = $this->moduleId;
        }

        return $baseURL .'&'. http_build_query( $parameters );
    }

    /**
     * @return int
     */
    function getModuleIdFromRequest() {
        return intval( $this->app->in( $this->moduleIdUrlParameter ));
    }
}
