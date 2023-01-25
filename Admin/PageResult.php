<?php

namespace Admin;

use Admin\Menu\MenuView;
use App\Parser\ParserInterface;
use Lib\ResultInterface;

class PageResult implements ResultInterface {
    private $content = '';
    private $log = '';
    private $view;
    private $tabs;
    private $siteName;
    private $pageURL;
    private $inDebug;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @param MenuView $view
     * @param string $tabs
     * @param string $siteName
     * @param string $pageURL
     */
    public function __construct( MenuView $view, $tabs, $siteName, $pageURL, $inDebug ) {
        $this->view = $view;
        $this->tabs = $tabs;
        $this->siteName = $siteName;
        $this->pageURL = $pageURL;
        $this->inDebug = $inDebug;
    }

    /**
     * @param string $content
     * @return void
     */
    function setContent( $content ) {
        $this->content = $content;
    }

    /**
     * @param ParserInterface $parser
     * @return void
     */
    function setParser( ParserInterface $parser ) {
        $this->parser = $parser;
    }

    /**
     * @param string $log
     * @return void
     */
    function embedLog( $log ) {
        $this->log = $log;
    }

    /**
     * @return string
     */
    function getResult() {
        $headTitle = "&nbsp; Система управления сайтом &laquo;{$this->siteName}&raquo;";
        $pageTitle = "Система управления сайтом &laquo;{$this->siteName}&raquo; &nbsp;&nbsp;&nbsp; {$this->pageURL}";
        $notice = $this->inDebug ? $this->view->noticeMsg( 'Сайт работает в режиме отладки' ) : '';
        $logs = $this->inDebug ? $this->view->log( $this->log ) : '';

        $result = $this->view->main(
            $this->view->head(
                $headTitle,
                $this->view->styles(),
                $this->view->scripts()
            ),
            $this->view->body(
                $pageTitle,
                $this->tabs,
                $this->content,
                $logs,
                $notice
            )
        );

        if( $this->parser instanceof ParserInterface ) {
            $result = $this->parser->parse( $result );
        }

        return $result;
    }
}