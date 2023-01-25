<?php

namespace App\User;

use App\AppException;
use Data\User\Row;

class UserAuth {
    private $login;
    private $password;

    /**
     * @param string $login
     * @return void
     * @throws AppException
     */
    public function setLogin( $login ) {
        if( $login == '' ) {
            throw new AppException('Введите логин');
        }

        if( mb_strlen( $login ) > 32 ) {
            throw new AppException('Слишком длинный логин');
        }

        if( preg_match("/[^a-z0-9\.\-\_\@]/i", $login, $matches )) {
            throw new AppException('Логин может содержать только буквы латинского алфавита, цифры и символы _.-@');
        }

        $this->login = $login;
    }

    /**
     * @param string $password
     * @throws AppException
     */
    public function setPassword( $password ) {
        if( $password == '' ) {
            throw new AppException('Введите пароль');
        }

        $this->password = $password;
    }

    /**
     * @param UserAuthProvider $provider
     * @return Row
     * @throws AppException
     */
    public function auth( UserAuthProvider $provider ) {
        $row = $provider->findUserByLogin( $this->login );

        if( ! is_object( $row )) {
            throw new AppException('Пользователь не найден');
        }

        $pass_hash = \App\User\Password::generateCompiledPasshash(
            $row->pass_salt,
            md5( $this->password )
        );

        if ($row->pass_hash != $pass_hash) {
            throw new AppException('Неверный пароль');
        }

        if ($row->mgroup == User::GROUP_AUTH) {
            throw new AppException('Извините, ваш аккаунт еще не активирован');
        }

        return $row;
    }
}