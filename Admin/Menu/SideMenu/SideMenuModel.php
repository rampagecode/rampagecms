<?php

namespace Admin\Menu\SideMenu;

use Sys\Database\DatabaseInterface;

class SideMenuModel {
    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @param DatabaseInterface $db
     */
    public function __construct( DatabaseInterface $db ) {
        $this->db = $db;
    }

    /**
     * @param int $id
     * @return array
     */
    function loadCatalog( $id ) {
        return $this->db->select()
            ->from('admin_menu')
            ->where('catalog = '.intval( $id ))
            ->order('pos ASC')
            ->query()
            ->fetchAll()
        ;
    }

    /**
     * @param int $id
     * @return array
     */
    function loadSection( $id ) {
        return $this->db->select()
            ->from('admin_menu')
            ->where("section = '{$id}' AND type = 'catalog'")
            ->order('pos ASC')
            ->query()
            ->fetchAll()
        ;
    }
}