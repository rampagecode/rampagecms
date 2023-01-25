<?php

use Lib\Autoloader;

require_once(dirname(__DIR__) . '/Lib/Autoloader.php');

spl_autoload_register( function( $class ) {
    Autoloader::autoload( $class );
});

final class SessionTest extends PHPUnit_Framework_TestCase {

    private $logger;
    private $userRow;
    private $userTable;
    private $sessionRow;
    private $sessionTable;

    protected function setUp() {
        $this->userRow = $this
            ->getMockBuilder('Data\User\Row')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->userTable = $this
            ->getMockBuilder('Data\User\Table')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->sessionTable = $this
            ->getMockBuilder('Data\Session\Table')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->sessionRow = $this
            ->getMockBuilder('Data\Session\Row')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->logger = $this
            ->getMockBuilder('Sys\Log\LoggerInterface')
            ->getMock()
        ;
    }

    function testLoadBadSession() {
        $this->userTable->expects( $this->any() )->method( 'findById' )->willReturn( $this->userRow );
        $this->sessionRow->expects( $this->any() )->method( 'toArray' )->willReturn([]);
        $this->sessionTable->expects( $this->any() )->method( 'findSession' )->willReturn( $this->sessionRow );

        $session = new App\Session\Session( new \App\User\User() );
        $session->sess_id = 123;
        $session->loadSession( $this->sessionTable, $this->userTable, $this->logger );

        $this->assertSame(0, $session->sess_id);
        $this->assertSame(123, $session->dead_id);
    }

    function testLoadGoodSession() {
        $this->userTable->expects( $this->any() )->method( 'findById' )->willReturn( $this->userRow );
        $this->userRow->expects( $this->any() )->method( 'toArray' )->willReturn([
            'id' => 567,
        ]);
        $this->sessionTable->expects( $this->any() )->method( 'findSession' )->willReturn( $this->sessionRow );
        $this->sessionRow->expects( $this->any() )->method( 'toArray' )->willReturn([
            'id' => 123,
            'member_id' => 567,
        ]);

        $session = new App\Session\Session( new \App\User\User() );
        $session->sess_id = 123;
        $session->loadSession( $this->sessionTable, $this->userTable, $this->logger );

        $this->assertSame(123, $session->sess_id);
        $this->assertSame(0, $session->dead_id);
        $this->assertSame(567, $session->user->getId());
    }
}