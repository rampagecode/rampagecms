<?php

namespace Data\AdminSession;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'admin_sessions';
    protected $_primary = 'session_id';

    /**
     * @param string $sessionId
     * @param int $userId
     * @return void
     */
    function updateUsage( $sessionId, $userId ) {
        $this->update([
            'session_running_time' 	=> time(),
            'session_location' 		=> 'acp',
        ], 'session_member_id='.intval( $userId )." and session_id = '".$sessionId."'" );
    }
}