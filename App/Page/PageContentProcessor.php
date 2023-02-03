<?php

namespace App\Page;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Module\ModuleFactory;

class PageContentProcessor {
    /**
     * @var ContentTemplate[]
     */
    private $contents;

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @param PageTemplate[] $templates
     */
    public function __construct( array $templates, AppInterface $app ) {
        $this->contents = [];
        $this->app = $app;

        foreach( $templates as $t ) {
            $i = $t->getTemplate();

            if( $i instanceof ContentTemplate ) {
                if( empty( $i->id ) && !empty( $i->reference )) {
                    foreach( $templates as $tt ) {
                        $j = $tt->getTemplate();

                        if( $i->reference == $tt->getPlaceholder() && $j instanceof ContentTemplate ) {
                            $i->id = $j->id;
                        }
                    }
                }

                $this->contents[ $t->getSrc() ] = $i;
            }
        }
    }

    /**
     * @return array{src: array<string>, val: array<string>}
     */
    function process() {
        $src = array();
        $val = array();

        if( !empty( $this->contents )) {
            $cids = array_map( function( $value ) { return $value->id; }, array_values( $this->contents ));
            $cids = array_filter( $cids, function( $value ) { return !empty( $value ); });
            $cids = array_unique( $cids );

            if( empty( $cids )) {
                $rows = [];
            } else {
                $cids = implode(',', $cids );
                $rows = $this->app->db()->select()
                    ->from('vfs_texts')
                    ->where(new \Zend_Db_Expr("id IN ({$cids})"))
                    ->query()
                    ->fetchAll();
            }

            foreach( $this->contents as $key => $content ) {
                foreach( $rows as $row ) {
                    if( $row['id'] == $content->id ) {
                        $src[] = $key;

                        if( !empty( $content->property ) && isset( $row[ $content->property ] )) {
                            $val[] = $row[ $content->property ];
                        } else {
                            $val[] = $row['text_formatted'];
                        }
                    }
                }
            }
        }

        return [
            'src' => $src,
            'val' => $val,
        ];
    }
}