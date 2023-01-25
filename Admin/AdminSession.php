<?php

namespace Admin;

use App\User\User;
use Data\AdminSession\Table;
use Data\Session\Row;
use Sys\Config\ConfigManager;

class AdminSession {
    /**
     * @var bool
     */
    private $validated = false;

    /**
     * @var string|null
     */
    private $message = null;

    /**
     * @var ConfigManager
     */
    private $config;

    /**
     * @var string[]
     */
    private $lang = [
        'admin_session_not_found'		=> 'Администраторская сессия не найдена',
        'admin_session_not_retrieve'	=> 'Администраторская сессия не существует',
        'admin_member_not_valid'		=> 'Владелец сессии не найден',
        'admin_password_mismatch'		=> 'Неверный администраторский пароль',
        'admin_session_expired'			=> 'Администраторская сессия истекла',
        'admin_not_acp_access'			=> 'У вас нет прав на доступ к панели управления',
        'admin_ip_not_match'			=> 'Ваш IP-адрес изменился, вам нужно войти снова',
    ];

    /**
     * @var Row|null
     */
    private $sessionRow = null;

    /**
     * @var User|null
     */
    private $user = null;

    /**
     * @param ConfigManager $config
     */
    public function __construct( ConfigManager $config ) {
        $this->config = $config;
    }

    /**
     * @param string $sessionId
     * @return bool
     */
    function validate( $sessionId ) {
        if( empty( $sessionId )) {
            $this->validated = false;
            $this->message = $this->lang['admin_session_not_found'];
            return false;
        }

        $sessionTable = new Table();
        $sessionRow = $sessionTable->fetchRow( $sessionTable->select()->where('session_id = ?', $sessionId ));

        if( empty( $sessionRow ) || empty( $sessionRow['session_id'] )) {
            $this->validated = false;
            $this->message = $this->lang['admin_session_not_retrieve'];
            return false;
        }

        if( empty( $sessionRow['session_member_id'] )) {
            $this->validated = false;
            $this->message = $this->lang['admin_member_not_valid'] . ' (1)';
            return false;
        }

        if( $sessionRow['session_running_time']  < ( time() - 60*40 )) {
            $this->validated = false;
            $this->message = $this->lang['admin_session_expired'];
            return false;
        }

        $userTable = new \Data\User\Table();

        try {
            $userRow = $userTable->findById($sessionRow['session_member_id']);
        } catch( \Zend_Db_Table_Exception $e ) {
            $this->validated = false;
            $this->message = $this->lang['admin_member_not_valid'] . ' (2)';
            return false;
        }

        $user = new User( $userRow );

        if( $user->getId() == '' ) {
            $this->validated = false;
            $this->message = $this->lang['admin_member_not_valid'] . ' (3)';
            return false;
        }

        if( $sessionRow['session_member_pass_hash'] != $user->getPassHash() ) {
            $this->validated = false;
            $this->message = $this->lang['admin_password_mismatch'];
            return false;
        }

        if( ! $user->getGroupObject( $this->config )->getAccess()->canAccessAdmin() ) {
            $this->validated = false;
            $this->message = $this->lang['admin_not_acp_access'];
            return false;
        }

        $this->validated = true;
        $this->sessionRow = $sessionRow;
        $this->user = $user;

        $sessionTable->updateUsage( $sessionId, $user->getId() );

        return true;
    }

    /**
     * @return bool
     */
    function isValid() {
        return $this->validated;
    }

    /**
     * @return string|null
     */
    function getValidationMessage() {
        return $this->message;
    }

    /**
     * @return array
     */
    function getSessionInfo() {
        if( empty( $this->sessionRow )) {
            return [];
        } else {
            return $this->sessionRow->toArray();
        }
    }

    /**
     * @return User|null
     */
    function getSessionUser() {
        return $this->user;
    }
}