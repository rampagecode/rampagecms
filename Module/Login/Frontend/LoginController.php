<?php

namespace Module\Login\Frontend;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\User\UserAuth;

class LoginController implements ModuleControllerProtocol {
    var $time_now;
    var $out = '';


    /**
     * @var LoginView
     */
    var $view;

    /**
     * @var AppInterface
     */
    var $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->view = new LoginView();
    }

    function auto_run() {
        $this->time_now = time();

        if( $this->app->in('logout' )) {
            $act = 'logout';
        } else {
            $act = $this->app->in('a');
        }

        switch( $act ) {
            case 'login':
                $this->do_log_in();
                break;

            case 'logout':
                $this->do_log_out();
                break;

            default:
                $this->log_in_form();
                break;
        }

        return $this->out;
    }

    /**
     * Форма авторизации / Меню пользователя
     * @param string $msg
     * @return void
     */
    function log_in_form( $msg = '' ) {
        $user = $this->app->getUser();

        if( $user->isGuest() ) {
            $this->out .= $this->view->login_form( $msg, $this->app->pageURL() );
        } else {
            $this->out .= $this->view->user_menu(
                $user->getName(),
                $msg,
                $this->app->baseURL('?logout='.$user->userHash()),
                $this->app->pageURL()
            );
        }
    }

    /**
     * @return void
     */
    function do_log_in() {
        try {
            $login = $this->app->in('login' );
            $passw = $this->app->in('passw' );
            $table = new \Data\User\Table();
            $auth = new UserAuth();
            $auth->setLogin( $login );
            $auth->setPassword( $passw );
            $row = $auth->auth( $table );
            $user = new \App\User\User( $row );
        } catch( \Exception $e ) {
            $this->log_in_form( $e->getMessage() );
        }

        /**
         * Сбрасывать ключ авторизации пользователей при каждом входе?
         *
         * Если опция включена, то каждый раз при успешной авторизации пользователя
         * ключ авторизации пользователя в cookies, используемый как пароль, будет
         * сброшен. Таким образом невозможно одновременно авторизоваться на форуме
         * с более, чем одного компьютера.
         *
         * @todo: Перенести в настрокйи Панели управления.
         */

        $loginChangeKey = 0;

        //-----------------------------------------
        // Срок действия ключа авторизации, в днях.
        // `0` - срок действия не ограничен.
        //-----------------------------------------

        $loginKeyExpire = 7;

        //-----------------------------------------
        // Создаем новый ключ авторизации
        //-----------------------------------------

        $_time   = ( $loginKeyExpire ) ? ( time() + ( intval( $loginKeyExpire ) * 86400 )) : 0;
        $_sticky = $_time ? 0 : 1;
        $_days   = $_time ? $loginKeyExpire : 365;

        if( $loginChangeKey ||
            ! $user->getLoginKey()
            || ( $loginKeyExpire && ( time() > $user->getLoginKeyExpire() ))
        ) {
            $user->setLoginKey( \App\User\Password::generateAutologinKey() );
            $user->setLoginKeyExpire( $_time );
            $user->commit();
        }

        //-----------------------------------------
        // Устанавливаем куки
        //-----------------------------------------

        if( $this->app->in('remember_me' )) {
            $this->app->setCookie( 'member_id', $user->getId(), true );
            $this->app->setCookie( 'pass_hash', $user->getLoginKey(), $_sticky, $_days );
        }

        //-----------------------------------------
        // Если не установлен IP-адрес, то обновляем
        //-----------------------------------------

        if( $user->getIpAddr() == '' OR $user->getIpAddr() == '127.0.0.1' ) {
            $user->setIpAddr( $this->app->getIpAddr() );
            $user->commit();
        }

        //-----------------------------------------
        // Clean session...
        //-----------------------------------------

        $session_id = preg_replace(
            "/([^a-zA-Z0-9])/",
            "",
            $this->app->getCookie( 'session_id' )
        );

        if( $session_id ) {
            // У пользователя установлен идентификатор сессии - обновляем значения в БД
            $this->app->db()->update( 'sessions', array(
                'member_name'	=> $user->getName(),
                'member_id'		=> $user->getId(),
                'running_time'	=> time(),
                'member_group'	=> $user->getGroup()
            ), "id='{$session_id}'" );
        } else {
            // Id сессии не установлен - создаем новую сессию
            $session_id = md5( uniqid( microtime() ));

            $this->app->db()->insert( 'sessions', array(
                'id'			=> $session_id,
                'member_name'	=> $user->getName(),
                'member_id'		=> $user->getId(),
                'running_time'	=> time(),
                'member_group'	=> $user->getGroup(),
                'ip_address'	=> $this->app->getIpAddr(),
                'browser'		=> array_keys( $this->app->getBrowser(), 'name' )
            ));

            $this->app->setCookie( 'session_id', $session_id, true );
        }

        $this->app->setUser( $user );

        //-----------------------------------------
        // Синхронизация
        //-----------------------------------------

        // if( USE_SYNC == 1 ) $this->sys->sync->on_login( $member );

        //-----------------------------------------
        // Чистим обратный URL
        //-----------------------------------------

        if( $this->app->in('referer') and $this->app->in('a') != 'reg' ) {
            $url = $this->app->in('referer');
            $url = preg_replace( "!^\?!"       					, '', $url );
            $url = preg_replace( "!adsess=(\w){32}!"			, '', $url );
            $url = preg_replace( "!a=(login|reg|lostpass)!i"	, '', $url );
        }

        $this->app->redirect( $url );
    }

    /**
     * @param $ret
     * @return void
     */
    function do_log_out( $ret = 1 ) {
        $user = $this->app->getUser();

        //-----------------------------------------
        // INIT
        //-----------------------------------------

        if( $ret ) {
            if( $this->app->in('logout') != $user->userHash() ) {
                $this->log_in_form( '<b>Упс!</b><br />Вы не можете выйти т.к. ключ не подходит.' );
                return false;
            }
        }

        //-----------------------------------------
        // Время последнего посещения и активности
        //-----------------------------------------

        $t = time();

        $user->setLastActive( $t );
        $user->setLastVisit( $t );
        $user->commit();

        //-----------------------------------------
        // Обновляем сессию на гостевую..
        //-----------------------------------------

        $this->app->setGuest();

        // if( USE_SYNC == 1 ) $this->sys->sync->on_logout();

        if( $ret ) {
            $this->app->redirect( $this->app->pageURL() );
        }
    }
}