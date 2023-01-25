<?php

namespace Admin\Editor\TextManager;

use Admin\AdminControllerParameters;
use App\Parser\ParserInterface;
use Lib\ResultInterface;

class TextManagerResult implements ResultInterface {
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $logs;

    /**
     * @var TextManagerParameters
     */
    private $win;

    /**
     * @var AdminControllerParameters
     */
    private $info;

    /**
     * @param TextManagerParameters $parameters
     * @param AdminControllerParameters $info
     */
    public function __construct(
        TextManagerParameters $parameters,
        AdminControllerParameters $info
    ) {
        $this->win = $parameters;
        $this->info = $info;
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
     * @return string
     */
    function getResult() {
        $view = new TextManagerView();
        $sortDir = $this->win->sort_dir == 'asc' ? 'desc' : 'asc';
        $sortByNameURL = "[mod://]&sort_dir={$sortDir}";
        $sortByNameImg = '[img://]editor/arrow_'.( $this->win->sort_dir == 'asc' ? 'up' : 'down' ).'.gif';

        $toolBar = $view->toolBar( '', $this->win->folder, $this->info->buildURL( $this->win->folder, 'create', 'folder' ));
        $sortBar = $view->sortBar( $sortByNameURL, $sortByNameImg );
        $content = $view->content( $this->content . $this->logs );

        return $view->build( $toolBar, $sortBar, $content, $this->info->jsObj );
    }
}