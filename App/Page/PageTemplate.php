<?php

namespace App\Page;

use Sys\Log\Logger;

class PageTemplate {
    /**
     * @var string see CONTENT in this class doc section
     */
    private $content;

    /**
     * @var string see OPTION in this class doc section
     */
    private $option;

    /**
     * @var string template placeholder
     */
    private $key;

    /**
     * @var string the template string representation
     */
    private $src;

    /**
     * @var bool
     */
    private $noadmin;

    /**
     * @var TemplateProtocol
     */
    private $template;

    /**
     * @var bool
     */
    private $overloaded;

    /**
     * @param string $content see CONTENT in this class doc section
     * @param string $option see OPTION in this class doc section
     * @param int $pageId
     * @param array $overloads
     */
    public function __construct( $pageId, array $overloads, $content, $option = '' ) {
        $this->src = '<!--[' . $content . ']' . $option . '-->';
        $this->content = $content;
        $this->option = $option;
        $this->parse( $pageId, $overloads );
    }

    /**
     * @return TemplateProtocol
     */
    public function getTemplate() {
        return $this->template;
    }

    public function getPlaceholder() {
        return $this->key;

    }

    public function getSrc() {
        return $this->src;
    }

    public function canAdmin() {
        return empty( $this->noadmin );
    }

    public function isOverloaded() {
        return $this->overloaded == true;
    }

    /**
     * @param int $pageId
     * @param array $overloads
     * @return void
     */
    private function parse( $pageId, array $overloads ) {
        $keyValue = preg_split(
            "/([\=\~])/",
            $this->content,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        if( count( $keyValue ) == 1 ) {
            // only a placeholder that should be overridden in the admin panel
            $content = $keyValue[0];
            $this->template = new ContentTemplate();
        }
        elseif( count( $keyValue ) == 3 ) {
            // check $keyValue[1] to determine the type of the template
            $this->key = $keyValue[0];
            $content = $keyValue[2];

            switch( $keyValue[1] ) {
                case '=':
                    $this->template = new ModuleTemplate();
                    break;

                case '~':
                    $this->template = new ContentTemplate();
                    break;

                case '^':
                    if( empty( $overloads[ $this->key ] )) {
                        return;
                    }
                    break;
            }
        }
        else {
            // incorrect syntax of the template
            return;
        }

        $r = preg_split(
            "/([\?\@\|\*\~])/",
            $content,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        if( empty( $this->key )) {
            $this->key = $r[0];
        }

        $this->template->parseTemplate( $r );

        if( strstr( $this->option, 'noadmin')) {
            if( ! $this->overloaded ) {
                $this->noadmin = true;
            }
        } elseif( strstr( $this->option, 'adminon=' )) {
            $tplPageId = (int) str_replace("adminon=", "", $this->option );

            if( $tplPageId == $pageId ) {
                $this->noadmin = false;
            } else {
                $this->noadmin = true;
            }
        }

        if( !empty( $overloads[ $this->key ] )) {
            $overload = $overloads[ $this->key ];
            $this->noadmin = (bool) $overload['noadmin'];

            switch( $overload['type'] ) {
                case 'module':
                    $this->template = new ModuleTemplate();
                    $this->overloaded = true;
                    break;

                case 'content':
                    $this->template = new ContentTemplate();
                    $this->overloaded = true;
                    break;

                default:
                    $this->overloaded = false;
                    return;
            }

            $this->template->parseOverload( $overload );
        }
    }
}