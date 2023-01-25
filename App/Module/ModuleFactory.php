<?php

namespace App\Module;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\AdminException;
use App\AppException;
use App\AppInterface;

class ModuleFactory {

    /**
     * @param string $path
     * @param $namespace
     * @param AppInterface $app
     * @return ModuleControllerProtocol
     * @throws AppException
     */
    static function loadModuleController( $path, $namespace, AppInterface $app ) {
        $name = ucfirst( basename( $path ));

        if( empty( $path )) {
            throw new AppException("Директория модуля '{$name}' не указана" );
        }

        if( ! file_exists( $path )) {
            throw new AppException("Директория модуля '{$path}' не найдена" );
        }

        $className = $name.'Controller';
        $classPath = $path.DIRECTORY_SEPARATOR.$className.'.php';

        if( ! file_exists( $classPath )) {
            throw new AppException('Модуль не найден по адресу: '.$classPath );
        }

        include_once $classPath;

        $fullName = "{$namespace}\\{$name}\\{$className}";

        if( ! class_exists( $fullName, false )) {
            throw new AppException('Класс модуля не найден с именем: '.$fullName );
        }

        $instance = new $fullName( $app );

        if( ! $instance instanceof ModuleControllerProtocol ) {
            throw new AppException("Код модуля {$fullName} не соответствует протоколу 'ModuleControllerProtocol'" );
        }

        return $instance;
    }

    /**
     * @param string $path
     * @param AppInterface $app
     * @return ModuleInterface
     * @throws AppException
     */
    static function loadModule( $path, AppInterface $app ) {
        $name = ucfirst( basename( $path ));

        if( empty( $path )) {
            throw new AppException("Директория модуля '{$name}' не указана" );
        }

        if( ! file_exists( $path )) {
            throw new AppException("Директория модуля '{$path}' не найдена" );
        }

        $moduleClassPath = $path.DIRECTORY_SEPARATOR.$name.'.php';

        if( ! file_exists( $moduleClassPath )) {
            throw new AppException('Модуль не найден по адресу: '.$moduleClassPath );
        }

        include_once $moduleClassPath;

        $moduleClass = "Module\\{$name}\\{$name}";

        if( ! class_exists( $moduleClass, false )) {
            throw new AppException('Класс модуля не найден с именем: '.$moduleClass );
        }

        $module = new $moduleClass( $app );

        if( ! $module instanceof ModuleInterface ) {
            throw new AppException("Код модуля {$moduleClass} не соответствует протоколу 'ModuleInterface'" );
        }

        return $module;
    }

    /**
     * @param ModuleControllerProtocol $module
     * @param AdminControllerParameters $parameters
     * @return string
     */
    static function executeAdminModule( ModuleControllerProtocol $module, AdminControllerParameters $parameters ) {
        try {
            $act = $parameters->act;

            if( $module instanceof AdminControllerInterface ) {
                $map = $module->availableActions();

                if( is_array( $map ) && ! empty( $act ) && isset( $map[ $act ] )) {
                    $act = $map[ $act ];
                }

                $module->setRequestParameters( $parameters );
            }

            if( ! empty( $act )) {
                $method = $act . 'Action';

                if( method_exists( $module, $method )) {
                    $moduleResult = $module->$method();
                } else {
                    $class = get_class( $module );
                    throw new AdminException("Method '{$method}' not found in '{$class}'" );
                }
            } else {
                $moduleResult = $module->auto_run();
            }

            if( is_array( $moduleResult )) {
                $moduleResult = $moduleResult['content'];
            }
        } catch( AdminException $e ) {
            $moduleResult = $e->getMessage();
        }

        return $moduleResult;
    }
}