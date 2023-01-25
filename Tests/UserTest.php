<?php

use Lib\Autoloader;
use Data\User\Row;
use App\User\User;

require_once(dirname(__DIR__) . '/Lib/Autoloader.php');

spl_autoload_register( function( $class ) {
    Autoloader::autoload( $class );
});

final class UserTest extends PHPUnit_Framework_TestCase {
    private $dbAdapter;

    protected function setUp() {
        $this->dbAdapter = new Zend_Test_DbAdapter();
    }

    protected function tearDown() {
        if( $this->hasFailed() ) {
            $adapter = $this->dbAdapter;

            $n = $adapter->getProfiler()->getTotalNumQueries();

            for( $i = 0; $i < $n; $i++) {
                $s = $adapter->getProfiler()->getQueryProfile($i)->getQuery()
                    . print_r( $adapter->getProfiler()->getQueryProfile($i)->getQueryParams(), true )
                ;
                fwrite(STDOUT, "\n" . $s . "\n");
            }
        }

        $this->dbAdapter = null;
    }

    public function testConstructor() {
        $row = new Row([ 'data' => [ 'id' => 123 ]]);

        $user = new User( $row );
        $this->assertSame(123, $user->getId() );
    }

    public function testSetter() {
        $row = new Row([ 'data' => [ 'login_key' => 'key1' ]]);
        $user = new User( $row );
        $user->setLoginKey('key2' );
        $this->assertSame( $user->getLoginKey(), 'key1' );
    }

    public function testCommit() {
        $adapter = $this->dbAdapter;
        $adapter->setDescribeTable('user', [
            'id' => [
                'SCHEMA_NAME' => 'schema_name',
                'TABLE_NAME'  => 'user',
                'COLUMN_NAME' => 'id',
                'PRIMARY'     => true
            ],
            'login_key' => [
                'SCHEMA_NAME' => 'schema_name',
                'TABLE_NAME'  => 'user',
                'COLUMN_NAME' => 'login_key',
                'PRIMARY'     => false
            ]
        ]);

        $oldValue = 'key1';
        $newValue = 'key2';

        $adapter->appendStatementToStack( Zend_Test_DbStatement::createSelectStatement([
            ['id' => 1, 'login_key' => $newValue]
        ]));

        $adapter->appendStatementToStack( Zend_Test_DbStatement::createUpdateStatement(1));

        $table = new Zend_Db_Table([
            Zend_Db_Table::SCHEMA => 'schema_name',
            Zend_Db_Table::ADAPTER => $adapter,
            Zend_Db_Table::PRIMARY => 'id',
            Zend_Db_Table::COLS => ['id'],
            Zend_Db_Table::NAME => 'user',
        ]);

        $row = new Row([
            'data' => [ 'id' => 1, 'login_key' => $oldValue ],
            'table' => $table,
            'stored' => true,
        ]);

        $user = new User( $row );
        $user->setLoginKey( $newValue );
        $user->commit();

        $this->assertSame( $user->getLoginKey(), $newValue );
    }
}
