<?php

namespace App\Module;

use Admin\AdminException;
use App\AppException;
use App\AppInterface;

class ModuleReader {
    /**
     * @param string $mods_dir
     * @param AppInterface $app
     * @return ModuleInterface[]
     * @throws AdminException|AppException
     */
    function readDirForModules( $mods_dir, AppInterface $app ) {
        $mods = array();

        if( $dir = @opendir( $mods_dir )) {
            while(( $file = readdir( $dir )) !== false ) {
                $modDirPath = $mods_dir.DIRECTORY_SEPARATOR.$file;

                if( $file != '.' && $file != '..' && is_dir( $modDirPath )) {
                    $module = ModuleFactory::loadModule( $modDirPath, $app );

                    if( ! empty( $module )) {
                        $mods[ $file ] = $module;
                    }
                }
            }
        } else {
            throw new AdminException( "Не удалось открыть директорию '{$mods_dir}'" );
        }

        if( count( $mods ) == 0 ) {
            throw new AdminException( "Нет модулей в директории '{$mods_dir}'" );
        }

        return $mods;
    }
}