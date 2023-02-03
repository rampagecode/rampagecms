<?php

namespace App\Page;

use Data\Page\Table;
use Sys\Input\InputManager;

class PageManager {

    /**
     * @var array
     */
    private $tree = [];

    /**
     * @var
     */
    private $cacheFileName;

    /**
     * @param $tree_cache_file
     * @param Table $table
     */
    function __construct( $tree_cache_file, Table $table ) {
        $this->cacheFileName = $tree_cache_file;

        if( file_exists( $tree_cache_file )) {
            include $tree_cache_file;
        }

        if( isset( $site_tree )) {
            $this->tree = $site_tree;
        } else {
            $this->updateCache( $table );
        }
    }

    /**
     * @param Table $table
     */
    function updateCache( Table $table ) {
        $pages = $table->getAllPages();
        $this->tree = $this->build_tree_from_pages( $pages, 0, '/' );
        $this->writeToFile( $this->tree, $this->cacheFileName );
    }

    /**
     * Возвращает ID страницы расположенной по указанному адресу
     * @param $pageURL
     * @return int
     */
    function getPageId( $pageURL ) {
        return (int)array_search( rtrim( $pageURL, '/') . '/', $this->tree );
    }

    /**
     * @param $tree
     * @param $file
     * @return void
     */
    private function writeToFile( $tree, $file ) {
        if( ! is_array( $tree )) {
            $tree = [];
        }

        $cache = "<?php\n\n\$site_tree = array\n(\n";

        foreach( $tree AS $id => $path ) {
            $cache .= "\t{$id}\t=> '{$path}',\n";
        }

        $cache .= ");\n\n?>";

        // Записываем в файл
        fclose( fopen( $file, 'a+b' ));

        $f = fopen( $file, 'r+t' );
        flock( $f, LOCK_EX );
        ftruncate( $f, 0 );
        fseek( $f, 0, SEEK_SET );
        fwrite( $f, $cache );
        fclose( $f );
    }

    /**
     * Рекурсивно упорядочивает переданный массив страниц сайта в дерево
     * --------------------------------------------------------------------------
     * @param array Массив страниц в виде id => [ parent, alias ]
     * @param int ID корневой страницы
     * @param string Альяс корневой страницы
     * @return array|string Упорядоченный в дерево массив
     */
    private function build_tree_from_pages( $pages, $root, $root_path ) {
        $tree = array();

        if( ! count( $pages )) {
            return '';
        }

        foreach( $pages AS $page ) {
            if( $page['parent'] == $root ) {
                $tree[ $page['id'] ] = $root_path . $page['alias'] . '/';
                $tree += $this->build_tree_from_pages( $pages, $page['id'], $tree[ $page['id'] ] );
            }
        }

        return $tree;
    }

    function pageAddressById( $id ) {
        return $this->tree[ $id ];
    }
}