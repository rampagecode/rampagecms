<?php

namespace App\User;

use App\AppManager;
use Sys\Config\ConfigManager;
use Data\User\Row as UserRow;

final class User {
    const GROUP_AUTH = 1;
    const GROUP_GUEST = 2;
    const GROUP_MEMBER = 3;
    const GROUP_ADMIN = 4;

    private $stackChanges = array();

    /**
     * @var UserRow
     */
    private $userRow;

    /**
     * @param UserRow|null $userRow
     */
    public function __construct( $userRow = null ) {
        $this->userRow = $userRow;
    }

    /**
     * @param UserRow $userRow
     * @return void
     */
    public function setUser( UserRow $userRow ) {
        $this->stackChanges = array();
        $this->userRow = $userRow;
    }

    public function setGuest() {
        $this->stackChanges = [];
        $this->userRow = null;
    }

    /**
     * Вносит изменения, произведенные с помощью сеттеров этого объекта,
     * в базу данных и ТОЛЬКО ПОСЛЕ ЭТОГО, в свойства самого объекта.
     * @return bool
     */
    public function commit() {
        if( is_array( $this->stackChanges ) && count( $this->stackChanges )) {
            $this->userRow->setFromArray( $this->stackChanges );
            $this->userRow->save();
            $this->stackChanges = array();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Отменяет все изменения внесенные с помощью сеттеров этого объекта
     * с момента последнего выполнения метода `commit()`.
     */
    public function discard() {
        $this->stackChanges = array();
    }

    /**
     * Возвращает уникальный хэш пользователя для подтверждения, что именно
     * он совершает данную операцию.
     * @return string
     */
    function userHash() {
        $id = $this->getId();

        if( !empty( $id ) && !$this->isGuest() ) {
            return md5( $this->getEmail() . '&' . $this->getPassHash() . '&' . $this->getRegTime() );
        } else {
            return md5( 'this is only here to prevent it breaking on guests' );
        }
    }

    //==========================================================================
    // ГЕТТЕРЫ
    //==========================================================================

    /**
     * @return int Возвращает уникальный идентификатор пользователя который соответствует ID строки таблицы БД
     */
    public function getId() {
        return empty($this->userRow)
            ? ''
            : (int)$this->userRow->toArray()['id']
        ;
    }

    /**
     * @return int
     */
    public function getLoginKeyExpire() {
        return (int)$this->userRow->toArray()['login_key_expire'];
    }

    /**
     * @return string
     */
    public function getLoginKey() {
        return (string)$this->userRow->toArray()['login_key'];
    }

    /**
     * @return string Возвращает IP-адрес пользователя, под которым он первый раз совершил вход в систему
     */
    public function getIpAddr() {
        return (string)$this->userRow->toArray()['ip_address'];
    }

    /**
     * @return string Уникальный логин пользователя под которым он совершает вход в систему
     */
    public function getLogin() {
        return empty($this->userRow)
            ? ''
            : (string)$this->userRow->toArray()['login']
        ;
    }

    /**
     * @return string Отображаемое имя пользователя, которое выводится на экран, не обязательно уникальное
     */
    public function getName() {
        return empty($this->userRow)
            ? 'Гость'
            : (string)$this->userRow->toArray()['dname']
        ;
    }

    /**
     * @return int Идентификатор группы к которой принадлежит пользователь
     */
    public function getGroup() {
        return empty( $this->userRow )
            ? self::GROUP_GUEST
            : (int)$this->userRow->toArray()['mgroup']
        ;
    }

    /**
     * @return int
     */
    public function getLastActive() {
        return (int)$this->userRow->toArray()['last_activity'];
    }

    /**
     * @return int
     */
    public function getLastVisit() {
        return (int)$this->userRow->toArray()['last_visit'];
    }

    /**
     * @return string
     */
    public function getPassSalt() {
        return (string)$this->userRow->toArray()['pass_salt'];
    }

    /**
     * @return string
     */
    public function getPassHash() {
        return (string)$this->userRow->toArray()['pass_hash'];
    }

    /**
     * @return string
     */
    public function getEmail() {
        return (string)$this->userRow->toArray()['email'];
    }

    /**
     * @return int
     */
    public function getTimeOffset() {
        return (int)$this->userRow->toArray()['time_offset'];
    }

    /**
     * @return int
     */
    public function getRegTime() {
        return (int)$this->userRow->toArray()['reg_time'];
    }

    /**
     * Use daylight saving time
     * @return int
     */
    public function useDST() {
        return (int)$this->userRow->toArray()['dst_in_use'] == 1;
    }

    /**
     * @return UserGroup
     */
    public function getGroupObject( ConfigManager $config = null ) {
        $groupId = $this->getGroup();
        $config = $config ?: AppManager::getInstance()->config();
        $groupInfo = $config->getGroupCache( $groupId );

        return new UserGroup( $groupInfo );
    }

    //==========================================================================
    // СЕТТЕРЫ
    //==========================================================================

    /**
     *
     */
    public function setLoginKey($s) {
        $this->stackChanges['login_key'] = $s;
        return $this;
    }

    /**
     *
     */
    public function setLoginKeyExpire($s) {
        $this->stackChanges['login_key_expire'] = $s;
        return $this;
    }

    /**
     *
     */
    public function setIpAddr($s) {
        $this->stackChanges['ip_address'] = $s;
        return $this;
    }

    /**
     *
     */
    public function setLastVisit($s) {
        $this->stackChanges['last_visit'] = $s;
        return $this;
    }

    /**
     *
     */
    public function setLastActive($s) {
        $this->stackChanges['last_activity'] = $s;
        return $this;
    }

    /**
     *
     */
    public function setGroup($s) {
        $s = intval($s);

        if ($s == 0) {
            return;
        }

        $this->stackChanges['mgroup'] = $s;
        return $this;
    }

    /**
     *
     */
    public function setPassHash($s) {
        $this->stackChanges['pass_hash'] = $s;
        return $this;
    }

    //==========================================================================
    // ЧЕКЕРЫ
    //==========================================================================

    /**
     * @return bool
     */
    public function isAdmin() {
        return (bool)($this->getGroup() === self::GROUP_ADMIN);
    }

    /**
     * @return bool
     */
    public function isGuest() {
        return (bool)($this->getGroup() === self::GROUP_GUEST);
    }
}