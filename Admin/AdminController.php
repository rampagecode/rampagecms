<?php

namespace Admin;

use App\AppException;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Parser\TemplateParser;
use \Exception;
use App\Module\ModuleFactory;
use Lib\ResultInterface;

class AdminController {
    /**
     * @var AppInterface
     */
    var $app;

    /**
     * @var AdminControllerParameters
     */
    private $params;

    public function __construct( AppInterface $app, AdminControllerParameters $params ) {
        $this->app = $app;
        $this->params = $params;
    }

    /**
     * @return ResultInterface
     */
    public function run() {
        $parser = new TemplateParser( $this->app );

        try {
            $result = $this->runEnviron();
            $parser->setAdminControllerParameters( $this->params );
        } catch( \Exception $e ) {
            $result = new JsonResult(); // @todo: replace with TextResult
            $result->setContent( $e->getMessage() );
        }

        $result->setParser( $parser );

        return $result;
    }

    /**
     * @return ResultInterface
     * @throws AdminException
     * @throws AppException
     */
    private function runEnviron() {
        $environs = [
            'a' => 'Menu',
            'x' => 'Action',
            'r' => 'Editor',
        ];

        if( empty( $this->params->env )) {
            $this->params->env = 'a';
        }

        if( ! isset( $environs[ $this->params->env ] )) {
            throw new \Admin\AdminException('Wrong environment');
        }

        $env = $environs[ $this->params->env ];
        $envPath = $this->app->rootDir('Admin', $env );
        $envController = ModuleFactory::loadModuleController( $envPath, "Admin", $this->app );

        if( $envController instanceof AdminControllerInterface ) {
            $envController->setRequestParameters( $this->params );
            $envController->auto_run();
            return $this->runModule( $envController, $env );
        } else {
            throw new \Admin\AdminException('Wrong environment controller');
        }
    }

    /**
     * @param AdminControllerInterface&ResultInterface $result
     * @param $env
     * @return ResultInterface
     * @throws AdminException
     * @throws AppException
     */
    private function runModule( AdminControllerInterface $result, $env ) {
        $mod = $this->params->mod;
        $map = $result->availableActions();

        if( is_array( $map ) && ! empty( $mod )) {
            if( ! isset( $map[ $mod ] )) {
                throw new \Admin\AdminException("Wrong module name {$mod}");
            }

            $name = $map[ $mod ];

            if( ! empty( $name )) {
                $modPath = $this->app->rootDir('Admin', $env, ucfirst( $name ));
                $modController = ModuleFactory::loadModuleController( $modPath, "Admin\\{$env}", $this->app );
                $modResult = ModuleFactory::executeAdminModule( $modController, $this->params );
                $result->setContent( $modResult );
            }
        }

        return $result;
    }
}