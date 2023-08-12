<?php

namespace Data\Folder;

use Data\DataException;
use Zend_Db_Table_Row_Abstract;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'vfs_folders';
    protected $_primary = 'id';
    protected $_rowClass = 'Data\Folder\Row';

    /**
     * @param string $name
     * @param int $parent
     * @return int
     * @throws DataException|\Admin\AdminException
     */
    function create( $name, $parent = null ) {
        if( empty( $name )) {
            throw new \Admin\AdminException('Имя директории не может быть пустым' );
        }

        $parent = $parent ?: 0;
        $row = $this->findByName( $name, $parent );

        if( ! empty( $row )) {
            throw new \Admin\AdminException("Директория именем '{$name}' уже существует в '{$parent}'");
        }

        return $this->insert([
            'name'   => $name,
            'parent' => $parent,
        ]);
    }

    /**
     * @param Zend_Db_Table_Row_Abstract $row
     * @return Row|null
     * @throws DataException
     */
    function castRow( $row ) {
        if( empty( $row )) {
            return null;
        }
        elseif( $row instanceof Row ) {
            return $row;
        } else {
            throw new DataException('Row is not an instance of \Data\Folder\Row class');
        }
    }

    /**
     * @param $id
     * @return Zend_Db_Table_Row_Abstract|null;
     * @throws DataException
     */
    function findById( $id ) {
        $row = $this->fetchRow('id = ' . intval( $id ));
        return $this->castRow( $row );
    }

    /**
     * @param string $name
     * @param int $parent
     * @return Row|null
     * @throws DataException
     */
    function findByName( $name, $parent ) {
        $table = new Table();
        $row = $table->fetchRow(
            $table->select()->where('name = ?', $name )->where('parent = ?', $parent )
        );

        return $this->castRow( $row );
    }

    /**
     * Вычисляем полный путь до текущего каталога
     * @param string $folder
     * @param string $root
     * @return string
     */
    function pathTo( $folder, $root ) {
        $rows = $this->fetchAll();
        $all_folders = array();

        foreach( $rows as $r ) {
            $all_folders[ $r['id'] ]['name'] 	= $r['name'];
            $all_folders[ $r['id'] ]['parent'] 	= $r['parent'];
        }

        $path = array();
        $i_folder = $folder;

        while( true ) {
            if( $all_folders[ $i_folder ]['parent'] ) {
                $path[] 	= $all_folders[ $i_folder ]['name'];
                $i_folder 	= $all_folders[ $i_folder ]['parent'];
            } else {
                break;
            }
        }

        $path[] = $all_folders[ $root ]['name'];

        krsort( $path );

        return implode( '/', $path );
    }

    /**
     * @param string $name
     * @param int $parent
     * @return \Zend_Db_Table_Row_Abstract|null
     */
    function findRow( $name, $parent ) {
        return $this->fetchRow(
            $this->select()->where('name = ?', $name )->where('parent = ?', $parent )
        );
    }

    /**
     * Функция вычисляет поддиректории находящиеся в указанной директории.
     * @param int $parent Идентификатор директории в которой будет происходить поиск
     * @param array $children Массив в формате ['id директории'] = ['id директории в которой она находится']
     * @return array
     */
    function findChildren( $parent, $children = null ) {
        // Получаем список всех директорий
        if( empty( $children )) {
            $rows = $this->fetchAll();
            $children = array();

            foreach( $rows as $r ) {
                $children[ $r['id'] ] = $r['parent'];
            }
        }

        // Вычисляем всех потомков
        $relatives = array_keys( $children, $parent );

        if( count( $relatives )) {
            $all = array_values( $relatives );

            foreach( $relatives as $kind ) {
                $all = array_merge( $all, array_values( $this->findChildren( $kind, $children )));
            }

            return $all;
        }

        return [];
    }

    /**
     * @param int $id
     * @return int
     */
    function findRoot( $id ) {
        $rows = $this->fetchAll();

        foreach( $rows as $r ) {
            $children[ $r['id'] ] = $r['parent'];
        }

        while( true ) {
            if( empty( $children[ $id ] )) {
                break;
            } else {
                $id = $children[ $id ];
            }
        }

        return $id;
    }

    /**
     * @param string $folder
     * @param string $sortDir
     * @return Row[]
     */
    function findFolderRows( $folder, $sortDir ) {
        $select = $this->select()
            ->where('parent = ?', $folder )
            ->order("name {$sortDir}" )
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

    /**
     * @param int $folder
     * @return int|null
     */
    function findParent( $folder ) {
        try {
            $row = $this->findById($folder);
        } catch (DataException $e) {
            return null;
        }

        if( ! empty( $row ) && ! empty( $row['parent'] )) {
            return intval( $row['parent'] );
        } else {
            return null;
        }
    }
}