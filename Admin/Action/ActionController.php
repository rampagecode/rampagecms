<?php

namespace Admin\Action;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\JsonResult;
use App\AppInterface;
use App\Parser\ParserInterface;
use Lib\ResultInterface;

class ActionController extends JsonResult implements AdminControllerInterface, ResultInterface  {

    private $map = [
        'login'			=> 'Login',
        'floateditor'	=> 'float_editor',
        'floatgallery'	=> 'float_gallery',
        'img'			=> 'image_manager',
        'colors'		=> 'color_dialog',
        'spec_char'		=> 'special_characters',
        'bookmark'		=> 'bookmarks',
        'hr'			=> 'insert_hr',
        'rte'			=> 'rte_dialogs',
        'sitetree'		=> 'SiteTree',
        'tree2pcm'		=> 'PageContent',
    ];

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {

    }

    public function auto_run() {

    }

    function availableActions() {
        return $this->map;
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;

        if( ! isset( $this->map[ $this->parameters->mod ] )) {
            $this->parameters->mod = 'login';
            $this->parameters->act = $this->parameters->idx = null;
        }

        $this->parameters->tab = null;
    }
}