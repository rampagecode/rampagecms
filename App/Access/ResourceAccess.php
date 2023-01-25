<?php

namespace App\Access;

use App\AppException;

abstract class ResourceAccess {
    /**
     * @var array<string, bool> [RESOURCE: true|false]
     */
    protected $accessList;

    /**
     * @param string $data
     * @throws AppException
     */
    public function __construct( $data = null ) {

        if( is_string( $data )) {
            $data = json_decode( $data, true );
        }

        if( ! is_array( $data )) {
            $data = [];
        }

        $this->accessList = $this->buildAccessList( $data );
    }

    /**
     * @return AccessibleResource[]
     */
    abstract function getResources();

    /**
     * @return array<string, bool> [RESOURCE: true|false]
     * @throws AppException
     */
    protected function buildAccessList( $data ) {
        $list = [];
        $resources = $this->getResources();

        foreach( $resources as $r ) {
            if( $r instanceof SimpleResource ) {
                $list[ $r->name ] = $data[ $r->name ] ?: false;
            }
            elseif( $r instanceof ComplexResource ) {
                $list[ $r->name ] = $r->resource->buildAccessList( $data[ $r->name ] ?: [] );
            }
            else {
                throw new AppException('Unknown resource');
            }
        }

        return $list;
    }

    /**
     * @param $resource
     * @return bool
     */
    function canAccess( $resource ) {
        if( empty( $resource )) {
            return false;
        }

        if( ! is_string( $resource )) {
            return false;
        }

        $path = explode('/', $resource );

        if( empty( $path )) {
            return false;
        }

        $list = $this->accessList;

        while( $key = array_shift( $path )) {
            if( ! isset( $list[ $key ] )) {
                return false;
            }

            if( is_bool( $list[ $key ] )) {
                return $list[ $key ];
            }

            if( is_array( $list[ $key ] )) {
                $list = $list[ $key ];
            }
        }

        return false;
    }

    function getAccessList() {
        return $this->accessList;
    }

    function getAccessListEncoded() {
        return json_encode( $this->accessList );
    }

    /**
     * @return SimpleResource[]
     * @throws AppException
     */
    function flattenResources() {
        $resources = [];

        foreach( $this->getResources() as $r ) {
            if( $r instanceof SimpleResource ) {
                $resources[] = $r;
            }
            elseif( $r instanceof ComplexResource ) {
                foreach( $r->resource->flattenResources() as $r2 ) {
                    $resources[] = new SimpleResource( $r->name . '/' . $r2->name, $r2->title, $r2->description );
                }
            }
            else {
                throw new AppException('Unknown resource');
            }
        }

        return $resources;
    }

    /**
     * @param string $key
     * @param bool $value
     * @return void
     */
    static function unFlattenResource( $key, $value, &$resources ) {
        $parts = explode('/', $key);
        $tail = &$resources;

        foreach( $parts as $i => $part ) {
            if( !isset( $tail[ $part ] )) {
                $tail[ $part ] = [];
            }

            if( $i < count( $parts ) - 1 ) {
                $tail = &$tail[ $part ];
            }
        }

        if( isset( $part )) {
            $tail[ $part ] = $value;
        }
    }
}