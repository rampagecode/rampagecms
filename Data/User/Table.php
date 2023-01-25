<?php

namespace Data\User;

use App\User\UserAuthProvider;

class Table extends \Zend_Db_Table_Abstract implements UserAuthProvider {
    protected $_name = 'members';
    protected $_primary = 'id';
    protected $_rowClass = 'Data\User\Row';

    /**
     * @param int $id
     * @return Row|null
     * @throws \Zend_Db_Table_Exception|null
     */
    function findById( $id ) {
        $row = $this->find( $id )->current();

        if( $row instanceof Row ) {
            return $row;
        }

        return null;
    }

    /**
     * @param string $login
     * @return Row|null
     */
    function findUserByLogin( $login ) {
        $row = $this->fetchRow( $this->select()->where('LOWER(login) = ?', $login));

        if( $row instanceof Row ) {
            return $row;
        }

        return null;
    }
}
