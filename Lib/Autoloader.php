<?php

namespace Lib;

class Autoloader {
    static function autoload( $className, $debug = false ) {
        $debug && print( 'Original class name: <b>'.$className.'</b><br>'."\n" );

        $className  = str_replace( '_', DIRECTORY_SEPARATOR, $className );
        $className  = str_replace( '\\', DIRECTORY_SEPARATOR, $className );
        $className .= '.php';

        $debug && print( 'Translated to file path: <b>'.$className.'</b><br>'."\n" );

        $incPath = explode( PATH_SEPARATOR, get_include_path() );
        $incPath = array_reverse( $incPath );

        foreach( $incPath AS $path ) {
            $path .= DIRECTORY_SEPARATOR;
            $testPath = $path . $className;

            $debug && print( 'Test include path: <b>'.$testPath.'</b><br>'."\n" );

            if( file_exists( $testPath )) {
                $debug && print( '<u>File exists: <b>'.$testPath.'</b></u><br><br>'."\n" );

                require( $testPath );
                return true;
            }
        }

        $debug && print( '<u>File not found </u><br><br>'."\n" );
    }
}