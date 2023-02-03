<?php

namespace App\Page;

use App\AppException;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Module\ModuleFactory;

class PageModulesProcessor {
    /**
     * @var ModuleTemplate[]
     */
    private $modules;

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @param PageTemplate[] $templates
     */
    public function __construct( array $templates, AppInterface $app ) {
        $this->modules = [];
        $this->app = $app;

        foreach( $templates as $t ) {
            $i = $t->getTemplate();

            if( $i instanceof ModuleTemplate ) {
                $this->modules[ $t->getSrc() ] = $i;
            }
        }
    }

    /**
     * @return array{src: array<string>, val: array<string>}
     */
    function process() {
        $mods = [];

        foreach( $this->modules as $src => $i ) {
            $mods[ $i->mod ][ $i->act ][ $src ] = $i;
        }

        $mod_src = array();
        $mod_val = array();

        foreach( $mods AS $mod_name => $mod_actions ) {
            try {
                $mod_obj = $this->loadModule( $mod_name );
            } catch( \Exception $e ) {
                foreach( $mod_actions AS $mod_templates ) {
                    foreach( $mod_templates as $src => $template ) {
                        $mod_src[] = $src;
                        $mod_val[] = $e->getMessage();
                    }
                }

                continue;
            }

            foreach( $mod_actions AS $mod_act => $mod_templates ) {
                try {
                    $f_return = $this->runModule( $mod_obj, $mod_act );
                } catch( \Exception $e ) {
                    foreach( $mod_templates as $src => $template ) {
                        $mod_src[] = $src;
                        $mod_val[] = $e->getMessage();
                    }

                    continue;
                }

                foreach( $mod_templates as $src => $template ) {
                    $varValue = $template->varValue;
                    $temp_var = null;

                    if( $template->varType == 'scalar' ) {
                        $temp_var = $mod_obj->$varValue;
                    }
                    elseif( $template->varType == 'array' ) {
                        list( $_array, $_key ) = explode(',', $varValue );

                        $temp_var = $mod_obj->$_array;
                        $temp_var = $temp_var[ $_key ];
                    }
                    elseif( $template->varType == 'return' ) {
                        $temp_var = $f_return;
                    }

                    // Заменяем локальные языковые паттерны модуля
                    /*
                    if( count( $lang )) {
                        $tempvar = preg_replace( "#\[\@lang\|([a-z_]+)\]#sie", "\$lang['\\1']", $tempvar );
                    }
                    */

                    $mod_src[] = $src;
                    $mod_val[] = $temp_var;
                }
            }
        }

        return [
            'src' => $mod_src,
            'val' => $mod_val,
        ];
    }

    /**
     * Load a module and return its object
     * @param string Имя модуля
     * @return ModuleControllerProtocol
     * @throws AppException
     */
    function loadModule( $name ) {
        if( empty( $name )) {
            throw new AppException('Отсутствует имя модуля' );
        }

        list( $name, $id ) = explode(':', $name );

        $path = $this->app->rootDir( 'Module', ucfirst( $name ));
        $module = ModuleFactory::loadModule( $path, $this->app );
        $front = empty( $id )
            ? $module->getFrontend()
            : $module->getComplexFrontend( $id )
        ;

        if( empty( $front )) {
            throw new AppException('У модуля нет публичного интерфейса' );
        }

        return $front;
    }

    /**
     * Load end execute each module and get required variables
     * @param $modInstance
     * @param $func_exec
     * @return mixed|null
     * @throws AppException
     */
    function runModule( $modInstance, $func_exec ) {
        $mod_src = array();
        $mod_val = array();
        $f_return = null;

        if( $func_exec == 'auto_run' ) {
            if( method_exists( $modInstance, $func_exec )) {
                $f_return = $modInstance->$func_exec();
            }
        } else {
            $method_name = substr( $func_exec, 0, strpos( $func_exec, '(' ));

            if( method_exists( $modInstance, $method_name )) {
                //-----------------------------------------
                // Т.к. строка вызова функции храниться в
                // БД в "безопасном" виде, т.е. в виде
                // HTML-мнемоник, мы конвертируем их обратно
                // в обычные символы, чтобы получить
                // валидный PHP-код.
                //-----------------------------------------

                $trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
                $trans_tbl = array_flip($trans_tbl);
                $func_exec = strtr($func_exec, $trans_tbl);

                //echo $func_exec;

                eval('$f_return = $modInstance->' . $func_exec . ';');
            } else {
                throw new AppException('Method (' . $method_name . ') does not exists');
            }
        }

        return $f_return;
    }
}