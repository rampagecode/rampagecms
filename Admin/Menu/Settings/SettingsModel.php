<?php

namespace Admin\Menu\Settings;

use Admin\AdminException;
use Admin\WidgetsView;
use Sys\Database\DatabaseInterface;
use Sys\Input\InputInterface;

class SettingsModel {
    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @var InputInterface
     */
    private $in;

    public function __construct( DatabaseInterface $db, InputInterface $in ) {
        $this->db = $db;
        $this->in = $in;
    }

    function getGroups( $withHidden = false ) {
        $select = $this->db->select()
            ->from('conf_settings_titles')
            ->order('conf_title_position')
        ;

        if( ! $withHidden ) {
            $select->where('conf_title_noshow = 0');
        }

        $query = $select->query();
        $groups = [];

        while( $r = $query->fetch() ) {
            $groups[ $r['conf_title_id'] ] = $r;
        }

        return $groups;
    }

    function getSettings( $conf_group ) {
        $query = $this->db->select()
            ->from(['c' => 'conf_settings'])
            ->where( 'c.conf_group = ?', $conf_group)
            ->where('conf_noshow = ?', 0)
            ->order(['c.conf_position', 'c.conf_title'])
            ->joinLeft(
                ['cc' => 'conf_settings_titles'],
                'cc.conf_title_id = c.conf_group',
                ['cc.conf_title_title']
            )
            ->query()
        ;

        return $query->fetchAll();
    }

    function createControl( $conf_settings, WidgetsView $view ) {
        $key = $conf_settings['conf_key'];
        $value = $conf_settings['conf_value'] != "" ? $conf_settings['conf_value'] : $conf_settings['conf_default'];

        switch( $conf_settings['conf_type'] ) {
            case 'input':
                $control = $view->formInput( $key, str_replace( "'", "&#39;", $value ));
                break;

            case 'textarea':
                $control = $view->formTextarea( $key, $value, 45, 5 );
                break;

            case 'yes_no':
                $control = $view->formYesNo( $key, $value );
                break;

            default:
                $dropdown = [];

                if( $conf_settings['conf_extra'] ) {
                    foreach( explode( "\n", $conf_settings['conf_extra'] ) as $l ) {
                        list( $k, $v ) = explode( "=", $l );

                        if( $k != "" and $v != "" ) {
                            $dropdown[] = array( trim( $k ), trim( $v ));
                        }
                    }
                }

                if( $conf_settings['conf_type'] == 'dropdown' ) {
                    $control = $view->formDropdown( $key, $dropdown, $value );
                } else {
                    $control = $view->formMultiselect( $key, $dropdown, explode( ",", $value ), 5 );
                }

                break;
        }

        return $control;
    }

    /**
     * @param string[] $fields
     * @return void
     * @throws AdminException|\Zend_Db_Statement_Exception
     */
    function updateFields( $fields ) {
        if( ! count( $fields )) {
            throw new AdminException('Fields not set');
        }

        foreach( $fields as $f ) {
            if( ! preg_match('/^[\w\d\_\-]+$/', $f )) {
                throw new AdminException('Fields have bad names');
            }
        }

        $fields = "'" . implode( "','", $fields ) . "'";

        $query = $this->db->select()
            ->from('conf_settings')
            ->where(new \Zend_Db_Expr("conf_key IN ({$fields})"))
            ->query()
        ;

        $db_fields = array();

        while( $r = $query->fetch() ) {
            $db_fields[ $r['conf_key']  ] = $r;
        }

        foreach( $db_fields as $key => $data ) {
            if( $this->in[ $key ] != $data['conf_default'] ) {
                $value = $this->in[ $key ];

                $this->db->update(
                    'conf_settings',
                    [ 'conf_value' => $value ],
                    'conf_id = '.$data['conf_id']
                );
            } else {
                $this->db->update(
                    'conf_settings',
                    [ 'conf_value' => '' ],
                    'conf_id = '.$data['conf_id']
                );
            }
        }
    }
}
