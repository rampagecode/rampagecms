<?php

namespace App\Session;

use App;
use App\AppException;
use Sys\Config\ConfigManager;
use Sys\Cookie\CookieManager;
use App\User\UserGroup;
use Sys\Input\InputManager;
use App\User\User as User;
use Data\Session\Table as SessionTable;
use Data\User\Table as UserTable;
use Sys\Log\LoggerInterface;
use Sys\Request\RequestManager;
use Zend_Db_Table_Exception;

class SessionManager {
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var CookieManager
     */
    private $cookieManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param InputManager $input
     * @param ConfigManager $config
     * @param CookieManager $cookieManager
     * @param LoggerInterface $log
     * @param RequestManager $request
     * @throws App\User\Exception
     * @throws Zend_Db_Table_Exception
     */
    function __construct(
        InputManager         $input,
        ConfigManager        $config,
        CookieManager        $cookieManager,
        LoggerInterface      $log,
        RequestManager       $request
    ) {
        $log->add( 'Начата процедура аутентификации по сессии', LoggerInterface::DEBUG );

        $this->log = $log;
        $this->cookieManager = $cookieManager;

        $cookie_session_id = $cookieManager->getCookie('session_id');

        $session = new Session( new User() );
        $session->time_now	= time();
        $session->ip_address = $input->getIpAddr();
        $session->browser = $input->getBrowser()['name'];
        $session->match_ipaddress = $config->getVar('match_ipaddress');
        $session->page_url = $request->pageURL();

        if( !empty( $cookie_session_id )) {
            $log->add( 'Id сессии найден в куках', LoggerInterface::DEBUG );
            $session->sess_id = $cookie_session_id;
            $session->loadSession( new SessionTable(), new UserTable(), $log );
        } else {
            $session->sess_id = 0;
        }

        if( $session->sess_id ) {
            $log->add( 'Сессия найдена в БД', LoggerInterface::DEBUG );

            if( $session->checkUser() ) {
                $session->updateMemberSession( new SessionTable(), $log );
            } else {
                $session->updateGuestSession( new SessionTable() );
            }
        } else {
            $log->add( 'Сессия не найдена или не валидна', LoggerInterface::DEBUG );

            // Проверяем куки
            $cookie_member_id = intval( $cookieManager->getCookie('member_id') );
            $cookie_pass_hash = $cookieManager->getCookie('pass_hash');

            if( $cookie_member_id != 0 and $cookie_pass_hash != '' ) {
                $session = $this->authenticate( $session, $cookie_member_id, $cookie_pass_hash );
            } else {
                $log->add( 'В куках не найдены данные для проведения авторизации, загружаем гостя', LoggerInterface::DEBUG );
                $session->createGuestSession( new SessionTable() );
            }
        }

        if( $session->unloadMember ) {
            $session->unloadMember = false;
            $this->unloadMember();
        }

        if( empty( $session->user )) {
            $session->user = new User();
        }

        $http_moz = $config->getEnv('HTTP_X_MOZ');
        $this->blockWebAccelerators( $session, $http_moz );

        //-----------------------------------------
        // Загружаем права группы пользователя
        //-----------------------------------------

        try {
            $log->add( 'Загружаем права группы пользователя', LoggerInterface::DEBUG );

            if( $session->checkUser() ) {
                $groupId = $session->user->getGroup();
                $groupObject = $session->user->getGroupObject();

                if( $groupId != $groupObject->id() ) {
                    throw new AppException('Загружена некорректная группа пользователя');
                }

                if( $groupId == 0 ) {
                    throw new AppException('Указан Id группы пользователя = 0');
                } else {
                    $log->add('Группа пользователя = ' . $groupId );
                }
            }
        }
        catch( AppException $e ) {
            $this->unloadMember();
            $session->updateGuestSession( new SessionTable() );

            $log->add( $e->getMessage(), LoggerInterface::WARN );
        }

        if( $session->checkUser() ) {
            $log->add( 'Пользователь и его группа нормально загружены', LoggerInterface::DEBUG );

            if( $session->user->getLastVisit() == 0 ) {
                // Это первый визит пользователя

                $session->user
                    ->setLastVisit( $session->time_now )
                    ->setLastActive( $session->time_now )
                    ->commit()
                ;
            }
            elseif(( time() - $session->user->getLastActive() ) > 300 ) {
                // Если последний клик был больше чем 5 минут назад

                $session->user
                    ->setLastActive( $session->time_now )
                    ->commit()
                ;
            }
        }

        $cookieManager->setCookie( 'session_id', $session->sess_id, false );

        $this->session = $session;
    }

    /**
     * @return User
     */
    function getUser() {
        return $this->session->user;
    }

    /**
     * @param User $user
     * @return void
     */
    function setUser( App\User\User $user ) {
        $this->session->user = $user;
    }

    /**
     * @param Session $session
     * @param $userId
     * @param $passHash
     * @return Session
     * @throws Zend_Db_Table_Exception
     */
    private function authenticate( Session $session, $userId, $passHash ) {
        $this->log->add( 'В куках найдены Id пользователя и ключ авторизации', LoggerInterface::DEBUG );

        try {
            $userTable = new UserTable();
            $userRow = $userTable->findById( $userId );
            $session->user = new User( $userRow );

            if( ! $session->checkUser()) {
                $session->user = null;
                throw new AppException('Пользователь загружен не корректно');
            }
        }
        catch( AppException $e ) {
            $this->unloadMember();
            $session->updateGuestSession( new SessionTable() );

            $this->log->add( $e->getMessage(), LoggerInterface::WARN );
        }

        if( !empty( $session->user ) and $session->checkUser() and ( $session->user->getLoginKey() == $passHash )) {
            $this->log->add( 'Пользователь успешно загружен', LoggerInterface::DEBUG );

            if( time() > $session->user->getLoginKeyExpire() ) {
                $this->log->add( 'Срок действия ключа авторизации истек, загружаем как гостя', LoggerInterface::DEBUG );

                $this->unloadMember();
                $session->user = null;
                $session->createGuestSession( new SessionTable() );
            } else {
                $session->createMemberSession( new SessionTable() );
                $this->log->add( 'Сессия пользователя создана', LoggerInterface::DEBUG );
            }
        }
        else {
            $this->log->add( 'Пользователь не был загружен, так как ключ авторизации не соответствует указанному в куках', LoggerInterface::DEBUG );
            $this->unloadMember();
            $session->user = null;
            $session->createGuestSession( new SessionTable() );
        }

        return $session;
    }

    public function unloadMember() {
        $this->cookieManager->setCookie( 'member_id', '0', false );
        $this->cookieManager->setCookie( 'pass_hash', '0', false );
    }

    public function updateGuestSession() {
        $this->session->updateGuestSession( new SessionTable() );
    }

    private function blockWebAccelerators( Session $session, $http_moz ) {
        if( isset( $http_moz ) and strstr( strtolower( $http_moz ), 'prefetch' ) and $session->checkUser()) {
            $sapi = php_sapi_name();

            if( $sapi == 'cgi-fcgi' or $sapi == 'cgi' )	{
                @header('Status: 403 Forbidden');
            } else {
                @header('HTTP/1.1 403 Forbidden');
            }

            print "Использование веб-ускорителей, таких как Google Web Accelerator, запрещено.";
            exit();
        }
    }
}