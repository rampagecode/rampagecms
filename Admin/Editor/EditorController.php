<?php

namespace Admin\Editor;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\JsonResult;
use App\AppInterface;
use Lib\ResultInterface;

class EditorController extends JsonResult implements AdminControllerInterface, ResultInterface  {

    private $map = [
        'wrap' => 'ModalBox',
        'content' => 'TextManager',
        'folder' => 'FolderManager',
        'text' => 'TextManager',
        'find' => 'Operations',
        'img_mgr' => 'ImageManager',
        'hyperlink' => 'Hyperlink',
        'doc_mgr' => 'DocumentManager',
        'tab' => 'Operations',
        'colors' => 'Operations',
        'char' => 'Operations',
        'bookmark' => 'Operations',
        'hr' => 'Operations',
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
        if( ! isset( $this->map[ $parameters->mod ] )) {
            $parameters->mod = 'login';
            $parameters->act = $parameters->idx = null;
        }

        $parameters->tab = null;
    }
}