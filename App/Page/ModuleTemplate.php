<?php

namespace App\Page;

class ModuleTemplate implements TemplateProtocol {
    /**
     * @var string module name
     */
    public $mod;

    /**
     * @var string module method
     */
    public $act;

    /**
     * @var string module variable
     */
    public $varValue;

    /**
     * @var string 'scalar' or 'array'
     */
    public $varType;

    public function __construct( $mod = null, $act = null, $var = null ) {
        $this->mod = $mod;
        $this->act = $act;
        $this->varValue = $var;
    }

    function parseTemplate( array $r ) {
        $this->mod = $r[0];

        if( $r[1] == '*' ) {
            // метод определен
            $this->act = $r[2];
            $_var_pos = 4;
        } else {
            // метод по-умолчанию
            $this->act = 'auto_run';
            $_var_pos = 1;
        }

        $this->varValue = $r[$_var_pos + 1];

        // Куда возвращается результат функции:
        if( $r[$_var_pos] == '?' ) {
            $mod_type = 'scalar';
        } elseif( $r[$_var_pos] == '@' ) {
            $mod_type = 'array';
        } else {
            $mod_type = 'return';
        }

        $this->varType = $mod_type;
    }

    /**
     * @param array $overload
     * @return void
     */
    function parseOverload( array $overload ) {
        $this->mod = $overload['mod'];
        $this->act = $overload['act'] ?: 'auto_run';
        $this->varValue = $overload['var'];
        $this->varType = strstr($overload['var'], '|')
            ? 'array'
            : (trim($overload['var']) ? 'scalar' : 'return');
    }

    /**
     * @return array
     */
    function buildOverload() {
        return [
            'mod' => $this->mod,
            'act' => $this->act,
            'var' => $this->varValue,
            'type' => 'module',
        ];
    }
}