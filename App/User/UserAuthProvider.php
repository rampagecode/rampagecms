<?php

namespace App\User;

use Data\User\Row as UserRow;

interface UserAuthProvider {
    /**
     * @param string $login
     * @return UserRow
     */
    function findUserByLogin( $login );
}
