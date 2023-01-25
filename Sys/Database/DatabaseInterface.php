<?php

namespace Sys\Database;

interface DatabaseInterface {
    /**
     * @param string $sql
     * @param array $bind
     * @return \Zend_Db_Statement_Interface
     */
    public function query( $sql, $bind );

    /**
     * @return \Zend_Db_Select
     */
    public function select();

    /**
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert( $table, array $bind );

    /**
     * @param  mixed $table The table to update.
     * @param  mixed $where DELETE WHERE clause(s).
     * @return int The number of affected rows.
     */
    public function delete( $table, $where = '' );

    /**
     * @param  mixed $table The table to update.
     * @param  array $bind  Column-value pairs.
     * @param  mixed $where UPDATE WHERE clause(s).
     * @return int The number of affected rows.
     */
    public function update( $table, array $bind, $where = '' );

    /**
     * @return string
     */
    public function lastInsertId();

    /**
     * @return DatabaseInterface
     */
    public function beginTransaction();

    /**
     * @return DatabaseInterface
     */
    public function commit();

    /**
     * @return DatabaseInterface
     */
    public function rollBack();
}