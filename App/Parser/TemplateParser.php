<?php

namespace App\Parser;

use Admin\AdminControllerParameters;
use Admin\Menu\AdminControllerInterface;
use App\AppInterface;
use App\Parser\ParserInterface;

class TemplateParser implements ParserInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var string
     */
    private $modURL = '';
    private $envURL = '';
    private $tabURL = '';

    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    /**
     * @param string $content
     * @return string
     */
    function parse( $content ) {
        return $this->replacePlaceholders( $content );
    }

    /**
     * @param string $html
     * @return string
     */
    function replacePlaceholders( $html ) {
        $html = preg_replace_callback('/\[([a-z]+)\:\/\/\]/si', [$this, 'replaceUrls'], $html );
        $html = preg_replace_callback('/\[\?([a-z\_]+)\]/si', [$this, 'replaceVars'], $html );

        return $html;
    }

    function setAdminControllerParameters( AdminControllerParameters $parameters ) {
        $this->tabURL = $parameters->tabURL();
        $this->envURL = $parameters->envURL();
        $this->modURL = $parameters->modURL();
    }

    /**
     * @param $matches
     * @return string|null
     */
    private function replaceUrls( $matches ) {
        if( empty( $matches ) || !is_array( $matches ) || count( $matches ) != 2 ) {
            return null;
        }

        switch( $matches[1] ) {
            // replace [img://]
            case 'img':
                return $this->app->assetURL('images') . '/';

            // replace [mod://]
            case 'mod':
                return $this->modURL;

            // replace [js://]
            case 'js':
                return $this->app->assetURL('scripts') . '/';

            // replace [css://]
            case 'css':
                return $this->app->assetURL() . '/';

            // replace [base://]
            case 'base':
                return $this->app->baseURL();

            // replace [tab://]
            case 'tab':
                return $this->tabURL;

            // replace [env://]
            case 'env':
                return $this->envURL;

            // replace [image://]
            case 'image':
                return '/images/';

            // replace [thumb://]
            case 'thumb':
                return '/images/thumbs/';

            default:
                return null;
        }
    }

    /**
     * @param $matches
     * @return string|null
     */
    private function replaceVars( $matches ) {
        if( empty( $matches ) || !is_array( $matches ) || count( $matches ) != 2 ) {
            return null;
        }

        return $this->app->getVar( $matches[1] );
    }
}