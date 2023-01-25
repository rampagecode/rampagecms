<?php

namespace Sys\Config;

use Lib\ArrayAccess;
use Sys\Database\DatabaseInterface;

class Cache implements \ArrayAccess {
    private $data = [];
    private $auth_group;
    private $path;
    private $db;

    use ArrayAccess;

    protected function arrayAccessProperty() {
        return 'data';
    }

    /**
     * @param int $auth_group an identifier of a user group which data we'd like to cache
     * @param string $path a path to the cache file
     * @param DatabaseInterface $db
     */
    function __construct( $auth_group, $path, DatabaseInterface $db ) {
        $this->path = $path;
        $this->auth_group = $auth_group;
        $this->db = $db;
        $this->load();
    }

    public function build() {
        $this->cacheGroups();
        $this->cacheSettings();
        $this->cacheStats();
        $this->cacheTasks();
        $this->save();
    }

    public function cacheGroups() {
        $cache = [];
        $q = $this->db->select()->from('groups')->query();

        while( $r = $q->fetch() ) {
            $cache[ $r['g_id'] ] =  $r;
        }

        $this->data['group_cache'] = $cache;
    }

    public function cacheSettings() {
        $cache = [];
        $q = $this->db->select()
            ->from('conf_settings')
            ->where('conf_add_cache=1')
            ->query()
        ;

        while( $r = $q->fetch() ) {
            $value = $r['conf_value'] != "" ?  $r['conf_value'] : $r['conf_default'];

            if( $value == '{blank}' ) {
                $value = '';
            }

            $cache[ $r['conf_key'] ] = $value;
        }

        $this->data['settings'] = $cache;
    }

    public function cacheStats() {
        $cache = [];

        $row = $this->db->select()
            ->from('members')
            ->where("mgroup <> '".$this->auth_group."'")
            ->columns(['mem_count' => new \Zend_Db_Expr('COUNT(id)')])
            ->query()
            ->fetch()
        ;
        $cache['mem_count'] = $row['mem_count'];

        $row = $this->db->select()
            ->from('members')
            ->where("mgroup <> '".$this->auth_group."'")
            ->columns(['id', 'login', 'dname'])
            ->order('id DESC')
            ->limit(1, 0)
            ->query()
            ->fetch()
        ;
        $cache['last_mem_name'] = $row['dname'] ? $row['dname'] : $row['login'];
        $cache['last_mem_id']   = $row['id'];

        $this->data['stats'] = $cache;
    }

    public function cacheTasks() {
        $cache = [];
        $row = $this->db->select()
            ->from('task_manager')
            ->where('task_enabled = 1')
            ->columns(['task_next_run'])
            ->order('task_next_run ASC')
            ->limit(1, 0)
            ->query()
            ->fetch()
        ;

        if( ! $row['task_next_run'] ) {
            $cache['task_next_run'] = time() + 3600;
        } else {
            $cache['task_next_run'] = $row['task_next_run'];
        }

        $this->data['tasks'] = $cache;
    }

    /**
     * Записываем данные в кэш-файл
     * --------------------------------------------------------------------------
     * @return void
     */
    public function save()
    {
        if( $file = fopen( $this->path, 'w' )) {
            $out  = "<?php\n\n\$cache = array\n(\n";
            $out .= $this->array_to_string( $this->data, "\t" );
            $out .= ");\n\n?>";

            fputs ( $file, $out );
            fclose( $file );
        }
    }

    public function load() {
        $cache = [];

        if( file_exists( $this->path )) {
            require $this->path;
            $this->data = $cache;
        } else {
            $this->build();
        }
    }

    /**
     * Рекурсивно переводит многомерный масив в строку с форматированием.
     * --------------------------------------------------------------------------
     * @param array Массив
     * @param string Отступы, символы табуляции
     * @return string
     */

    private function array_to_string( $a, $t = '' ) {
        $s = '';

        foreach( $a as $k => $v ) {
            if( is_array( $v )) {
                $s .= "{$t}\"{$k}\" => array\n{$t}(\n" . $this->array_to_string( $v, ($t."\t") ) . "{$t}),\n";
            } else {
                $v = addslashes( $v );
                $s .= "{$t}\"{$k}\" => \"{$v}\",\n";
            }
        }
        return $s;
    }
}