<?php

namespace Data\Session;

use Data\Session\Table as SessionTable;
use App\Session\Session;
use App\User\User as User;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'sessions';
    protected $_primary = 'id';

    /**
     * @param string $sid Session ID
     * @param string|null $browser
     * @param string|null $ipAddr
     * @return Row|null
     */
    public function findSession( $sid, $browser = null, $ipAddr = null ) {
        $select = $this->select()->where('id = ?', $sid);

        if( !empty( $browser )) {
            $select->where('browser = ?', $browser);
        }

        if( !empty( $ipAddr )) {
            $select->where('ip_address = ?', $ipAddr);
        }

        $r = $this->fetchRow($select);

        return $r;
    }

    /**
     * @param $userId
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findUserSessions( $userId ) {
        $select = $this->select()->where('member_id = ?', $userId);

        return $this->fetchAll($select);
    }

    /**
     * @param $userId
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function deleteUserSessions( $userId ) {
        $select = $this->delete($this->getAdapter()->quoteInto('member_id = ?', $userId));
    }

    public function deleteDeadSessions( $session ) {
        $q = array();

        if(( $session->dead_id != 0 ) and ( ! empty( $session->dead_id ))) {
            $q[] = "id='" . $session->dead_id . "'";
        }

        if( $session->match_ipaddress && !empty( $session->ip_address ) ) {
            $q[] = "ip_address='" . $session->ip_address . "'";
        }

        if( count( $q )) {
            $this->delete( implode( " OR ", $q ));
        }
    }

    public function insertGuestSession( Session $session ) {
        $this->insert([
            'id'                 => $session->sess_id,
            'member_name'        => '',
            'member_id'          => 0,
            'member_group'       => User::GROUP_GUEST,
            'running_time'       => $session->time_now,
            'ip_address'         => $session->ip_address,
            'browser'            => $session->browser,
            'location'           => $session->page_url,
        ]);
    }

    public function updateGuestSession( Session $session ) {
        $this->update([
            'member_name'        => '',
            'member_id'          => 0,
            'member_group'       => User::GROUP_GUEST,
            'running_time'       => $session->time_now,
            'ip_address'         => $session->ip_address,
            'browser'            => $session->browser,
            'location'           => $session->page_url,
        ], 'id = "'.$session->sess_id .'"');
    }

    public function insertMemberSession( Session $session ) {
        $this->insert([
            'id'                 => $session->sess_id,
            'member_name'        => $session->user->getName(),
            'member_id'          => $session->user->getId(),
            'member_group'       => $session->user->getGroup(),
            'running_time'       => $session->time_now,
            'ip_address'         => $session->ip_address,
            'browser'            => $session->browser,
            'location'           => $session->page_url,
        ]);
    }

    public function updateMemberSession( Session $session ) {
        $this->update([
            'member_name'        => $session->user->getName(),
            'member_id'          => $session->user->getId(),
            'member_group'       => $session->user->getGroup(),
            'running_time'       => $session->time_now,
            'ip_address'         => $session->ip_address,
            'browser'            => $session->browser,
            'location'           => $session->page_url,
        ], 'id = "'.$session->sess_id .'"');
    }
}