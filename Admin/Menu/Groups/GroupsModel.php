<?php

namespace Admin\Menu\Groups;

use Sys\Database\DatabaseInterface;

class GroupsModel {
    private $db;

    public function __construct( DatabaseInterface $db ) {
        $this->db = $db;
    }

    /**
     * @return array
     */
    function fetchGroupsList() {
        $query = $this->db->select()
            ->from(['g' => 'groups'])
            ->joinLeft(
                ['m' => 'members'],
                'm.mgroup = g.g_id',
                ['members_count' => new \Zend_Db_Expr('COUNT(m.id)')]
            )
            ->group('g.g_id')
            ->order('g.g_title')
            ->query()
        ;

        return $query->fetchAll();
    }
}