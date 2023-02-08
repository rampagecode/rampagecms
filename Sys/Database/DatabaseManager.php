<?php

namespace Sys\Database;

use Sys\Log\LoggerInterface;
use Sys\SystemException;

class DatabaseManager implements DatabaseInterface {
    private $db;

	function __construct( $configPath, LoggerInterface $logger ) {
		$config = $this->loadConfig( $configPath );
        $params = [
            'host'     => $config['hostname'],
            'username' => $config['username'],
            'password' => $config['password'],
            'dbname'   => $config['database'],
        ];

        // Используем драйвер PDO MySQL
        $this->db = \Zend_Db::factory('Pdo_Mysql', $params );

        // Устанавливаем соединение
        $this->db->getConnection();

        // Устанавливаем кодировку
        $this->db->query('set names utf8');

        \Zend_Db_Table_Abstract::setDefaultAdapter( $this->db );

        $profiler = $logger->getDbProfiler();

        if( $profiler ) {
            $this->db->setProfiler( $profiler );
        }
	}

    /**
     * @param string $sql
     * @param array $bind
     * @return \Zend_Db_Statement_Interface
     */
    public function query( $sql, $bind ) {
        return $this->db->query( $sql, $bind );
    }

    /**
     * @return \Zend_Db_Select
     */
    public function select() {
        return $this->db->select();
    }

    /**
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert( $table, array $bind ) {
        return $this->db->insert( $table, $bind );
    }

    /**
     * @param  mixed $table The table to update.
     * @param  mixed $where DELETE WHERE clause(s).
     * @return int The number of affected rows.
     */
    public function delete( $table, $where = '' ) {
        return $this->db->delete( $table, $where );
    }

    /**
     * @param  mixed $table The table to update.
     * @param  array $bind  Column-value pairs.
     * @param  mixed $where UPDATE WHERE clause(s).
     * @return int The number of affected rows.
     */
    public function update( $table, array $bind, $where = '' ) {
        return $this->db->update( $table, $bind, $where );
    }

    /**
     * @return string
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }

    /**
     * @return DatabaseInterface
     */
    public function beginTransaction() {
        $this->db->beginTransaction();
        return $this;
    }

    /**
     * @return DatabaseInterface
     */
    public function commit() {
        $this->db->commit();
        return $this;
    }

    /**
     * @return DatabaseInterface
     */
    public function rollBack() {
        $this->db->rollBack();
        return $this;
    }

    /**
     * @return \PDO
     */
    public function getConnection() {
        return $this->db->getConnection();
    }

    /**
     * @throws SystemException
     */
    private function loadConfig( $path ) {

        if( ! file_exists( $path )) {
            throw new SystemException("Config file not found at path: ($path)" );
        }

        $result = parse_ini_file( $path );

        if( $result === false ) {
            throw new SystemException( "Unable to parse the config file: ($path)" );
        }

        return $result;
    }
}