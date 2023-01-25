<?php

namespace App\Session;

use App\AppException;
use App\User\User;
use Data\Session\Table as SessionTable;
use Data\User\Table as UserTable;
use Sys\Log\LoggerInterface;
use Zend_Db_Table_Row_Exception;

class Session {
    /**
     * @var User
     */
    public $user;
    public $sess_id = 0;
    public $dead_id = 0;
    public $browser;
    public $ip_address;
    public $last_click;
    public $location;
    public $match_ipaddress;
    public $time_now;
    public $page_url;
    public $lifetime = 1800; // полчаса
    public $unloadMember = false;

    public function __construct( User $user ) {
        $this->user = $user;
    }

    function loadSession(
        SessionTable $sessionTable,
        UserTable $userTable,
        LoggerInterface $log
    ) {
        $sessionRow = $sessionTable->findSession( $this->sess_id );
        $data = empty( $sessionRow ) ? [] : $sessionRow->toArray();

        // Проверка на время существования сессии
        $lifetime = $this->time_now - $this->lifetime;

        if( !empty( $data['running_time'] ) && $data['running_time'] < $lifetime ) {
            $log->add('Превышено время жизни сессии');

            try {
                $sessionRow->delete();

                unset( $sessionRow );
                unset( $data );
            } catch( Zend_Db_Table_Row_Exception $e ) {
                $log->add('Не удалось активную сессию');
            }
        }

        if( ! empty( $data )) {
            $log->add('Загружаем сессию');
            $this->sess_id    = $data['id'];
            $this->last_click = $data['running_time'];
            $this->location   = $data['location'];

            $userId = intval( $data['member_id'] );

            if( $userId > 0 ) {
                $log->add('Загружаем пользователя');

                try {
                    $userRow = $userTable->findById( $userId );

                    if( ! empty( $userRow ) && $userRow->toArray()['id'] == $userId ) {
                        $this->user->setUser( $userRow );
                    } else {
                        throw new AppException();
                    }
                } catch( \Exception $e ) {
                    $this->unloadMember = true;
                    $log->add('Не удалось загрузить пользователя');
                }
            } else {
                $log->add('Это гостевая сессия');
            }
        } else {
            $log->add('Обнуляем сессию');
            $this->dead_id = $this->sess_id;
            $this->sess_id = 0;
            $this->user->setGuest();
        }
    }

    function updateMemberSession( SessionTable $table, LoggerInterface $log ) {
        if( ! $this->sess_id ) {
            $log->add('Создаем пользовательскую сессию, так как нет идентификатора');
            $this->createMemberSession( $table );
            return;
        }

        if( ! $this->checkUser() ) {
            $log->add('Создаем гостевую сессию, так как пользователь - гость');
            $this->unloadMember = true;
            $this->user->setGuest();
            $this->createGuestSession( $table );
            return;
        }

        if( time() - $this->user->getLastActive() > 3600 ) {
            $log->add('Создаем пользовательскую сессию, так как превышено время жизни');
            $this->createMemberSession( $table );
            return;
        }

        $log->add('Обновляем пользовательскую сессию');
        $table->updateMemberSession( $this );
    }

    function createMemberSession( SessionTable $table ) {
        if( $this->checkUser() ) {
            $this->sess_id = md5( uniqid( microtime() ));

            $table->deleteUserSessions( $this->user->getId() );
            $table->insertMemberSession( $this );

            if( time() - $this->user->getLastActive() > 3600 ) {
                $this->user->setLastVisit( $this->time_now )
                    ->setLastActive( $this->time_now )
                    ->commit()
                ;
            }
        } else {
            $this->createGuestSession( $table );
        }
    }

    function createGuestSession( SessionTable $table ) {
        $this->sess_id = md5( uniqid( microtime() ));
        $this->user->setGuest();

        $table->deleteDeadSessions( $this );
        $table->insertGuestSession( $this );
    }

    function updateGuestSession( SessionTable $table ) {
        if( ! $this->sess_id ) {
            $this->createGuestSession( $table );
            return;
        }

        $table->updateGuestSession( $this );
    }

    function checkUser() {
        $user = $this->user;

        if(( $user != null )
            and is_object( $user )
            and ( $user instanceof User )
            and ( $user->getId() != 0 )
            and ( $user->isGuest() === false )
        ) {
            return true;
        } else {
            return false;
        }
    }
}