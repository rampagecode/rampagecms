<?php

use Lib\Autoloader;
use App\User;

require_once(dirname(__DIR__) . '/Lib/Autoloader.php');

spl_autoload_register( function( $class ) {
    Autoloader::autoload( $class );
});

final class UserAuthProviderMock implements User\UserAuthProvider {
    private $password;

    function __construct( $password ) {
        $this->password = $password;
    }

    function findUserByLogin( $login ) {
        $salt = User\Password::generatePasswordSalt();

        return new Data\User\Row(
            [
                'data' => [
                    'mgroup' => App\User\User::GROUP_MEMBER,
                    'pass_salt' => $salt,
                    'pass_hash' => User\Password::generateCompiledPasshash($salt, md5($this->password)),
                ]
            ]
        );
    }
}

final class UserAuthTest extends PHPUnit_Framework_TestCase {
    function testSetEmptyLogin() {
        $this->setExpectedException('\App\User\Exception');

        $auth = new User\UserAuth();
        $auth->setLogin('');
    }

    function testSetLongLogin() {
        $this->setExpectedException('\App\User\Exception');

        $auth = new User\UserAuth();
        $auth->setLogin('0123456789012345678901234567890123');
    }

    function testSetWrongLogin1() {
        $this->setExpectedException('\App\User\Exception');

        $auth = new User\UserAuth();
        $auth->setLogin('логин');
    }

    function testSetWrongLogin2() {
        $this->setExpectedException('\App\User\Exception');

        $auth = new User\UserAuth();
        $auth->setLogin(' ');
    }

    function testSetEmptyPassword() {
        $this->setExpectedException('\App\User\Exception');

        $auth = new User\UserAuth();
        $auth->setPassword('');
    }

    function testAuth() {
        $auth = new User\UserAuth();
        $auth->setLogin('login');
        $auth->setPassword('password');
        $auth->auth( new UserAuthProviderMock('password') );
    }
}