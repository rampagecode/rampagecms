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
        $this->src = '<!--[!' . $content . ']' . $option . '-->';
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
        $r = preg_split(
            "/([\!\=\?\@\|\*\~])/",
            $this->content,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        $this->key = $r[0];

        if( count( $r ) == 1 && !empty( $overloads[ $this->key ] )) {
            $this->overloaded = true;

            $overload = $overloads[ $this->key ];

            switch( $overload['type'] ) {
                case 'module':
                    $this->template = new ModuleTemplate();
                    break;

                case 'content':
                    $this->template = new ContentTemplate();
                    break;

                default:
                    return;
            }

            $this->template->parseOverload( $overload );
            $this->noadmin = (bool) $overload['noadmin'];
        } else {
            $this->overloaded = false;

            switch( $r[1] ) {
                case '=':
                    $this->template = new ModuleTemplate();
                    break;

                case '~':
                    $this->template = new ContentTemplate();
                    break;

                default:
                    if( empty( $overloads[ $this->key ] )) {
                        $this->template = new ContentTemplate();
                    } else {
                        $overload = $overloads[ $this->key ];

                        switch( $overload['type'] ) {
                            case 'module':
                                $this->template = new ModuleTemplate();
                                break;

                            case 'content':
                                $this->template = new ContentTemplate();
                                break;

                            default:
                                return;
                        }

                        $this->template->parseOverload( $overload );
                    }

                    break;
            }

            $this->template->parseTemplate( $r );
        }

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
    }
}