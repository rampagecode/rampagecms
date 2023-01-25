<?php

namespace Admin\Action\SiteTree;

use App\Page\PageManager;
use Sys\Database\DatabaseInterface;
use Sys\Input\InputInterface;

class SiteTreeModel {
    function loadTree( $rootNode, DatabaseInterface $db, PageManager $pm ) {
        $rootNode = intval( $rootNode );
        $nodes = array();
        $query = $db->select()
            ->from('site_tree')
            ->where('parent = ?', $rootNode )
            ->order('menu_pos ASC')
            ->query()
        ;

        while( $r = $query->fetch() ) {
            $nodes[] = $this->convertToJs( $r, $pm );
        }

        return $nodes;
    }

    public function convertToJs( $row, PageManager $pm ) {
        $r = $row;
        $v = array();

        $v['id']	= 'sitetree'.$r['id'];
        $v['leaf']	= $r['isfolder'] == 1 ? false : true;

        if( $r['in_module'] == 1 ) {
            $v['_not_deletable'] = 1;
            $v['allowDrag'] = false;
        } else {
            $v['_not_deletable'] = 0;
            $v['allowDrag'] = true;
        }

        if( $r['hidden'] AND $r['deleted'] ) {
            $v['cls'] = 'treeHiddenDeletedNode';
        }
        elseif( $r['hidden'] ) {
            $v['cls'] = 'treeHiddenNode';
        }
        elseif( $r['published'] == 0 AND $r['deleted'] == 1 ) {
            $v['cls'] = 'treeUnpublishedDeletedNode';
        }
        elseif( ! $r['published'] ) {
            $v['cls'] = 'treeUnpublishedNode';
        }
        elseif( $r['deleted'] ) {
            $v['cls'] = 'treeDeletedNode';
        }
        else {
            $v['cls'] = 'treePublishedNode';
        }
        if  ($r['tupemenu']) {
            $v['cls'] .= ' TupeMenuTop';
        }
        $v['text'] = $r['pagetitle'];
        $v['qtip'] = "Id: {$r['id']}";
        $v['_addr'] = $pm->pageAddressById( $r['id'] );
        $v['_name'] = $r['pagetitle'];

        return $v;
    }
}