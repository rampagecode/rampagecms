<?php

use App\Page\PageParser;
use Lib\Autoloader;

require_once(dirname(__DIR__) . '/Lib/Autoloader.php');

spl_autoload_register( function( $class ) {
    Autoloader::autoload( $class );
});

final class PageTest extends PHPUnit_Framework_TestCase {
    function testTemplate() {
        $text = '<!--[!Меню=menu*build(2, true)]-->';
        $app = $this->getMockBuilder('App\AppInterface')->getMock();
        $parser = new PageParser( $app );
        $templates = $parser->parseTemplates(1, $text, []);

        $this->assertNotEmpty( $templates );
        $this->assertEquals(1, $this->count( $templates ));

        $t = $templates[0];
        $this->assertTrue( $t->canAdmin() );
        $this->assertEquals('Меню', $t->getPlaceholder() );
        $this->assertEquals( $text, $t->getSrc());
    }
}