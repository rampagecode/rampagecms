<?php

namespace App\User;

use App\Access\GroupAccess;
use App\AppException;
use Data\UserGroup\Row;

class UserGroup {

    private $g_id = 0;
    private $g_title = '';
    private $suffix = '';
    private $prefix = '';
    private $g_access = '';

    /**
     * @param array $row
     */
    public function __construct( $row = null ) {
        if( is_array( $row ) || $row instanceof Row ) {
            foreach( $row as $_var => $_val ) {
                $this->$_var = $_val;
            }
        }
    }

    /**
     * @return int
     */
    function id() {
        return $this->g_id;
    }

    /**
     * @param bool $formatted
     * @return string
     */
    function getTitle( $formatted = false ) {
        return $formatted
            ? $this->prefix . $this->g_title . $this->suffix
            : $this->g_title
        ;
    }

    /**
     * @return string
     */
    function getPrefix() {
        return $this->prefix;
    }

    /**
     * @return string
     */
    function getSuffix() {
        return $this->suffix;
    }

    /**
     * @return GroupAccess
     * @throws AppException
     */
    function getAccess() {
        return new GroupAccess( $this->g_access );
    }
}
