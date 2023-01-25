<?php

namespace Admin\Editor\ContentManager;

use Admin\AdminControllerParameters;
use App\Parser\ParserInterface;
use Lib\ResultInterface;

class ContentManagerResult implements ResultInterface {
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $logs;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @var string
     */
    private $title;

    /**
     * @param AdminControllerParameters $parameters
     */
    public function __construct( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
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
        $this->logs = $log;
    }

    /**
     * @param string $title
     * @return void
     */
    function setTitle( $title ) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    function getResult() {
        $view = new ContentManagerView();
        $content = $this->content . $this->logs;

        return $view->window( $this->title, $content, $this->parameters->jsObj );
    }
}