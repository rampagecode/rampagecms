<?php

namespace Admin\Editor\Operations;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class OperationsController implements AdminControllerInterface {

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

    /**
     * @return string
     */
    public function auto_run() {
        switch( $this->parameters->mod ) {
            case 'find':
                return $this->showFind();
            case 'tab':
                return $this->showTable();
            case 'colors':
                return $this->showColors();
            case 'char':
                return $this->showChars();
            case 'bookmark':
                return $this->showBookmark();
            case 'hr':
                return $this->showHR();
            default:
                return '';
        }
    }

    /**
     * @return string[]
     */
    function availableActions() {
        return [
            'edit' => 'editTable',
        ];
    }

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    function showFind() {
        $view = new FindView();

        return $view->window(
            $view->findForm(),
            'Поиск и замена',
            $this->app->in('jsobj')
        );
    }

    /**
     * @return string
     */
    function showTable() {
        $view = new CreateTableView();

        return $view->window(
            $view->content(),
            $this->app->in('jsobj')
        );
    }

    /**
     * @return string
     */
    function editTableAction() {
        $view = new EditTableView();
        $jsObj = $this->parameters->jsObj;

        return $view->window(
            $view->content(
                $view->tab1( $jsObj ),
                $view->tab2( $jsObj ),
                $view->tab3( $jsObj ),
                $view->preview()
            ),
            $jsObj
        );
    }

    /**
     * @return string
     */
    function showColors() {
        $action = $this->app->in('x');
        $jsObj = $this->parameters->jsObj;
        $view = new ColorView();
        return $view->window( $view->content(), $jsObj, $action );
    }

    /**
     * @return string
     */
    function showChars() {
        $view = new CharView();
        return $view->window( $view->content(), $this->parameters->jsObj );
    }

    /**
     * @return string
     */
    function showBookmark() {
        $view = new BookmarkView();
        $name = preg_replace( "/[^A-Za-z0-9]+/", '', $this->app->in('name' ));

        return $view->window(
            $view->content( $name ),
            $this->parameters->jsObj
        );
    }

    /**
     * @return string
     */
    function showHR() {
        $view = new HorizontalRuleView();
        return $view->window( $view->content(), $this->parameters->jsObj );
    }
}