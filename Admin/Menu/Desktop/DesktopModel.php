<?php

namespace Admin\Menu\Desktop;

use Sys\Database\DatabaseInterface;

class DesktopModel {
    private $db;

    public function __construct( DatabaseInterface $db ) {
        $this->db = $db;
    }

    /**
     * @param $groupId
     * @param $adminGroupId
     * @param $iconsLimit
     * @return array
     */
    function loadSiteTree( $groupId, $adminGroupId, $iconsLimit ) {
        if( $groupId != $adminGroupId ) {
            $where = 'admin_groups_access LIKE \'%s:'.strlen( strval( $groupId )).':"'.$groupId.'";%\'';
        } else {
            $where = '1=1';
        }

        return $this->db->select()
            ->from('site_tree')
            ->order('admin_pr DESC')
            ->limit( $iconsLimit, 0 )
            ->where( $where.' AND hidden = 0' )
            ->query()
            ->fetchAll()
        ;
    }
}