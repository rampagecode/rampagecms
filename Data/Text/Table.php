<?php

namespace Data\Text;

use Admin\Editor\ContentManager\ContentManagerParameters;
use Data\DataException;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'vfs_texts';
    protected $_primary = 'id';
    protected $_rowClass = 'Data\Text\Row';

    /**
     * @param string $name
     * @param int $folder
     * @param string $text
     * @return int
     */
    function create( $name, $folder, $text = '' ) {
        return $this->insert([
            'title'					=> $name,
            'text_formatted'		=> $text,
            'text_searchable'		=> strip_tags( $text ),
            'present_at_pages'		=> '',
            'vfs_folder'			=> $folder,
        ]);
    }

    /**
     * Добавляет в запись контента ссылку на страницу, где он расположен.
     * @param int $text_id
     * @param int $page_id
     * @return bool
     */
    function addPage( $text_id, $page_id ) {
        return (bool) $this->update([
            'present_at_pages' => new \Zend_Db_Expr("TRIM( BOTH ',' FROM CONCAT_WS( ',', present_at_pages, '{$page_id}' ))")
        ], "id = {$text_id} AND NOT FIND_IN_SET( '{$page_id}', present_at_pages )");
    }

    /**
     * Удаляет из записи контента одну или несколько ссылок на страницы
     * где он расположен. $page_id может быть как числом, например '32' -
     * тогда удаляет одна ссылка, так и строкой вида '32,12,4' тогда
     * удаляется несколько ссылок сразу.
     * @param int $text_id
     * @param int|string $page_id
     * @return bool
     */
    function removePage( $text_id, $page_id ) {
        if( ! $text_id = intval( $text_id )) {
            return FALSE;
        }

        if( is_numeric( $page_id )) {
            return (bool) $this->update([
                'present_at_pages' => new \Zend_Db_Expr("TRIM( BOTH ',' FROM REPLACE( REPLACE( present_at_pages, '{$page_id}', '' ), ',,', ',' ))")
            ], "id = {$text_id} AND FIND_IN_SET( '{$page_id}', present_at_pages )");
        }
        elseif( is_string( $page_id )) {
            if( $r = $this->fetchRow("id = {$text_id}" )) {
                $delete = explode( ',', str_replace( ' ', '', $page_id ));
                $search = explode( ',', $r['present_at_pages'] );
                $result = array_diff( $search, $delete );

                return $this->update([
                    'present_at_pages' => "'".implode( ',', $result ).'"'
                ], 'present_at_pages = "'.$r['present_at_pages'].'"');
            } else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * @param string $folder
     * @param string $sortDir
     * @return Row[]
     */
    function findTextRows( $folder, $sortDir ) {
        $table = new Table();
        $select = $table->select()
            ->where('vfs_folder = ?', $folder )
            ->order("title {$sortDir}" )
        ;

        $rows = $table->fetchAll( $select );
        $data = [];

        while ( $r = $rows->current() ) {
            if( $r instanceof Row ) {
                $data[] = $r;
            }

            $rows->next();
        }

        return $data;
    }
}