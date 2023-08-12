<?php

namespace Module\LangSwitch\Frontend;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class LangSwitchController implements ModuleControllerProtocol {

    /**
     * @var LangSwitchView
     */
    var $view;

    /**
     * @var AppInterface
     */
    var $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->view = new LangSwitchView();
    }

    function auto_run() {
        $thisPageURL = $this->app->request->pageURL();
        $thisPageURLPaths = explode('/', trim( trim( $thisPageURL ), '/' ));
        $thisPageURLFirst = strtolower( $thisPageURLPaths[0] );

        if( $thisPageURLFirst == "en" ) {
            array_shift( $thisPageURLPaths );
            $title = 'Switch to the Russian version';
            $fallbackURL = '/index/';
            
        } else {
            array_unshift( $thisPageURLPaths, 'en' );
            $newPageURL = '/' . implode( '/', $thisPageURLPaths );
            $newPageID = $this->app->pages->getPageId( $newPageURL );
            $title = 'Switch to the English version';
            $fallbackURL = '/en/index/';            
        }

        $newPageURL = '/' . implode( '/', $thisPageURLPaths );
        $newPageID = $this->app->pages->getPageId( $newPageURL );

        if( !$newPageID ) {
            $newPageURL = $fallbackURL;
        }
            
        return "<a href=\"{$newPageURL}\">$title</a>";
    }
}





