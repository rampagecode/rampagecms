<?php

namespace Data\Document;

class Row extends \Zend_Db_Table_Row {

    /**
     * @param $groupId
     * @return bool
     */
    function checkGroupAccess( $groupId ) {
        return in_array( $groupId, unserialize( $this['group_access'] ));
    }

    /**
     * @return string
     */
    function getSystemName() {
        return $this['sys_name'];
    }

    function getFileExtension() {
        return $this['file_ext'];
    }

    function getPublicName() {
        $name = preg_replace( '#\&[a-z0-9\#]{2,9}\;#', '', $this['real_name'] );
        $name = str_replace( '.', '_', $name );
        $name = str_replace( '"', "'", $name );

        return $name;
    }

    function getPublicNameWithExtension() {
        return $this->getPublicName().'.'.$this->getFileExtension();
    }
}