<?php

namespace Data\Page;

use Data\DataException;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'site_tree';
    protected $_primary = 'id';
    protected $_rowClass = 'Data\Page\Row';

    public function getAllPages() {
        $query = $this->select()->order('id ASC')->query();
        $pages = [];

        while( $r = $query->fetch() ) {
            $pages[ $r['id'] ] = $r;
        }

        return $pages;
    }

    /**
     * @param $id
     * @return array
     * @throws DataException
     */
    public function getTemplates( $id ) {
        $id = intval( $id );

        if( empty( $id )) {
            throw new DataException('the id is empty');
        }

        $row = $this->fetchRow( 'id = '.$id );

        if( empty( $row )) {
            throw new DataException('the row was not found');
        }

        $data = $row->toArray();

        if( empty( $data ) || ! is_array( $data )) {
            throw new DataException('the row has no data');
        }

        if( ! empty( $data['templates'] )) {
            $result = @unserialize( $data['templates'] );

            if( ! is_array( $result )) {
                throw new DataException('unable to unserialize the data');
            }

            return $result;
        } else {
            return [];
        }
    }

    /**
     * @param array $data
     * @param int $id
     * @return bool
     * @throws DataException
     */
    function setTemplates( $data, $id ) {
        if( ! is_array( $data )) {
            throw new DataException('the data is not an array');
        }

        $id = intval( $id );

        if( empty( $id )) {
            throw new DataException('the id is empty');
        }

        $templates = serialize( $data );

        $result = $this->update([ 'templates' => $templates ], 'id = '.$id );

        return $result == 1;
    }

    /**
     * @param int $id
     * @return void
     */
    function adjustMenuPositionAfterDeletingPageWithId( $id ) {
        $rows = $this->fetchAll('id = '.$id );

        foreach( $rows as $r ) {
            $this->update([
                'menu_pos' => new \Zend_Db_Expr('menu_pos - 1')
            ],
                'menu_pos > '.$r['menu_pos'].' AND parent = '.$r['parent']
            );
        }
    }

    /**
     * @param int[] $pages_ids
     * @return array<int, int> [textId: pageId]
     */
    function collectContentIds( $pages_ids ) {
        $rows = $this->fetchAll('id IN ('. implode( ',', $pages_ids ) .')')->toArray();
        $texts = [];

        foreach( $rows as $r ) {
            if( is_array( $templates = unserialize( $r['templates'] ))) {
                foreach( $templates AS $t ) {
                    if( $t['type'] == 'content' ) {
                        $texts[ (int)$t['id'] ] = (int)$r['id'];
                    }
                }
            }
        }

        return $texts;
    }

    /**
     * @param int $id parent page id
     * @return int[] children pages ids
     * @throws DataException
     */
    function findAllChildPagesToDelete( $id, $kill_module_pages = false )  {
        $parents = [ $id ];
        $pages = [ $id ];

        while( list( $num, $pid ) = each( $parents )) {
            $rows = $this->fetchAll('parent = ' . $pid);

            foreach( $rows as $r ) {
                if( $r['in_module'] && !$kill_module_pages ) {
                    throw new DataException("Эта страница не может быть удалена, так как содержат страницы управляемые модулями. Удалите их через соответствующие модулей, а затем попробуйте еще раз.");
                }

                if( ! in_array( $r['id'], $pages ) && $r['id'] ) {
                    $pages[] = (int)$r['id'];
                }

                if( ! in_array( $r['id'], $parents ) && $r['id'] && $r['isfolder'] ) {
                    $parents[] = $r['id2'];
                }
            }
        }

        return $pages;
    }

    /**
     * @param $parentId
     * @return Row[]
     */
    function fetchFirstLevelChildren( $parentId ) {
        $select = $this->select()->where('parent = ?', $parentId )->order('menu_pos ASC');
        return $this->fetchAll( $select );
    }
}