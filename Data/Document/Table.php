<?php

namespace Data\Document;

use Data\DataException;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'vfs_docs';
    protected $_primary = 'id';
    protected $_rowClass = 'Data\Document\Row';

    /**
     * @param int $id
     * @return Row|null
     * @throws \Zend_Db_Table_Exception
     */
    function findById( $id ) {
        $row = $this->find( $id )->current();

        if( $row instanceof Row ) {
            return $row;
        }

        return null;
    }

    /**
     * @param string $folder
     * @param string $sortDir
     * @return Row[]
     */
    function findDocumentRows( $folder, $sortDir ) {
        $select = $this->select()
            ->where('vfs_folder = ?', $folder )
            ->order("real_name {$sortDir}" )
        ;
        $rows = $this->fetchAll( $select );
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
