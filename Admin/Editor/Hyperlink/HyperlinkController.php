<?php

namespace Admin\Editor\Hyperlink;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use App\AppInterface;

class HyperlinkController implements AdminControllerInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        $view = new HyperlinkView();

        return $view->show( $this->parameters->jsObj );
    }

    function availableActions() {
        return [];
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }
}