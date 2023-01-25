<?php

namespace App\User;

class Password {
    /**
     * Генерирует password salt
     *
     * Возвращает строку указанной длины случайных символов исключая обратный слэш
     *
     * @param integer $len Длина строки, по умолчанию 5
     * @return string Строка случайных символов
     */
    public static function generatePasswordSalt( $len = 5 ) {
        $salt = '';

        for( $i = 0; $i < $len; $i++ )
        {
            $num = rand(33, 126);

            if( $num == 92 ) $num++;

            $salt .= chr( $num );
        }

        return $salt;
    }

    /**
     * Генерирует ключ авторизации
     *
     * @return string MD5 хэш случайных 60 символов
     */
    public static function generateAutologinKey() {
        return md5( self::generatePasswordSalt( 60 ));
    }

    /**
     * Генерирует скомпилированный хеш пароля
     *
     * Возвращает новый MD5-хеш содержащий `соль` и MD5-хеш пароля
     *
     * @param string	$salt Соль (5 случайных символов)
     * @param string	$md5_once_password Пароль пользователя в MD5
     * @return string MD5-хеш
     */

    public static function generateCompiledPasshash( $salt, $md5_once_password ) {
        return md5( md5( $salt ) . $md5_once_password );
    }
}