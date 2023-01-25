<?php

namespace Lib;

use Lib\LibraryException;

class ImageOperations {
    /**
     * Uses the GD library to re-size an image, maintaining aspect ratio
     * returns false on failure, or the new dimensions on success.
     * @param $file
     * @param $outputfile
     * @param $maxwidth
     * @param $maxheight
     * @param $jpgQuality
     * @return array|bool
     * @throws LibraryException
     */
    function proportionalResize( $file, $outputfile = '', $maxwidth = 0, $maxheight = 0, $jpgQuality = 60 ) {
        if( ! file_exists( $file )) {
            throw new LibraryException( 'File not found: '.$file );
        }

        $extension = strrchr( strtolower( $file ), '.' );

        list( $origwidth, $origheight ) = @getimagesize( $file );

        if((( $origwidth > $maxwidth ) || ( $origheight > $maxheight )) || empty( $outputfile )) {
            list( $new_w, $new_h ) = $this->getProportionalSize( $origwidth, $origheight, $maxwidth, $maxheight );

            switch( $extension ) {
                case '.jpg':
                case '.jpeg':
                    return $this->_imageResizeJpeg( $file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0, $jpgQuality );

                case '.png':
                    return $this->_imageResizePng( $file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0 );

                case '.gif':
                    return $this->_imageResizeGif( $file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0 );

                default :
                    throw new LibraryException( 'File not supported: '.$extension );
            }
        } else {
            if( @copy( $file, $outputfile )) {
                return array( $origwidth, $origheight );
            } else {
                throw new LibraryException( "Unable to copy '{$file}' to '{$outputfile}'" );
            }
        }
    }

    /**
     * @param $origwidth
     * @param $origheight
     * @param $maxwidth
     * @param $maxheight
     * @return array
     */
    function getProportionalSize( $origwidth, $origheight, $maxwidth = 0, $maxheight = 0 ){
        if( empty( $maxwidth )) {
            $maxwidth = $origwidth;
        }

        if( empty( $maxheight )) {
            $maxheight = $origheight;
        }

        if(( $origwidth > $maxwidth ) || ( $origheight > $maxheight )) {
            if(( $origwidth > $maxwidth ) && ( $origheight > $maxheight )) {
                if(( $origwidth / $maxwidth ) > ( $origheight / $maxwidth )) {
                    $newscale = $maxwidth / $origwidth;
                } else {
                    $newscale = $maxheight / $origheight;
                }
            }
            elseif( $origwidth > $maxwidth ) {
                $newscale = $maxwidth / $origwidth;
            } else {
                $newscale = $maxheight / $origheight;
            }

            //-----------------------------------------
            // Calculate the new aspect ratio
            //-----------------------------------------

            $new_w = abs( $origwidth * $newscale  );
            $new_h = abs( $origheight * $newscale );

            return array( $new_w, $new_h );
        } else {
            return array( $origwidth, $origheight );
        }
    }

    /**
     * Create base image
     * @param $width
     * @param $height
     * @return false|resource
     */
    function _imageCreateBase( $width, $height ) {
        if( function_exists( 'imagecreatetruecolor' ) && function_exists( 'imagecreate' )) {
            if( $base_image = @imagecreatetruecolor( $width, $height )) {
                return $base_image;
            }
            elseif( $base_image = @imagecreate( $width, $height )) {
                return $base_image;
            }
        }
        elseif( function_exists( 'imagecreate' )) {
            if( $base_image = @imagecreate( $width, $height )) {
                return $base_image;
            }
        }

        return false;
    }

    /**
     * Resize a jpeg image
     * @param $file
     * @param $output
     * @param $origwidth
     * @param $origheight
     * @param $width
     * @param $height
     * @param $cropX
     * @param $cropY
     * @param $quality
     * @return array|false
     */
    function _imageResizeJpeg( $file, $output, $origwidth, $origheight, $width, $height, $cropX = 0, $cropY = 0, $quality = 60 ) {
        if( ! function_exists( 'imagejpeg' )) {
            throw new LibraryException( 'function imagejpeg does not exists' );
        }

        if( ! ( imagetypes() & IMG_JPG )) {
            throw new LibraryException( 'jpg image type is not supported' );
        }

        //-----------------------------------------
        // Create the blank limited-palette image
        //-----------------------------------------

        if( ! $base_image = $this->_imageCreateBase( $width, $height )) {
            throw new LibraryException( 'unable to create a blank image' );
        }

        //-----------------------------------------
        // Get the image pointer to the original image
        //-----------------------------------------

        $imageToResize = imagecreatefromjpeg( $file );

        if( function_exists( 'imagecopyresampled' )) {
            if( ! @imagecopyresampled( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight )) {
                imagecopyresized( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight );
            }
        } else {
            imagecopyresized( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight );
        }

        if( empty( $output )) {
            header( "Content-type: image/jpeg", TRUE );
        }

        $return = FALSE;

        //-----------------------------------------
        //create the resized image
        //-----------------------------------------

        if( @imagejpeg( $base_image, $output, $quality )) {
            $return = array($width, $height, $output);
        }

        imagedestroy( $base_image );
        imagedestroy( $imageToResize );

        return $return;
    }

    /**
     * Resize a PNG image
     * @param $file
     * @param $output
     * @param $origwidth
     * @param $origheight
     * @param $width
     * @param $height
     * @param $cropX
     * @param $cropY
     * @return array|false
     */
    function _imageResizePng( $file, $output, $origwidth, $origheight, $width, $height, $cropX = 0, $cropY = 0 ) {
        if( ! function_exists( 'imagepng' )) {
            return FALSE;
        }

        if( ! ( imagetypes() & IMG_PNG )) {
            return FALSE;
        }

        //-----------------------------------------
        // Create the blank limited-palette image
        //-----------------------------------------

        if( ! $base_image = $this->_imageCreateBase( $width, $height )) {
            return false;
        }

        //-----------------------------------------
        // Get the image pointer to the original image
        //-----------------------------------------

        $imageToResize = imagecreatefrompng( $file );

        if( function_exists( 'imagecopyresampled' )) {
            if( ! @imagecopyresampled( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight )) {
                imagecopyresized( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight );
            }
        } else {
            imagecopyresized( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight );
        }

        $return = false;

        if( empty( $output )) {
            header( "Content-type: image/x-png", true );

            if( imagepng( $base_image )) {
                $return = array( $width, $height, $output );
            }
        }
        elseif( @imagepng( $base_image, $output )) {
            // image destination
            $return = array( $width, $height, $output );
        }

        imagedestroy( $base_image );
        imagedestroy( $imageToResize );

        return $return;
    }

    /**
     * Resize a GIF image
     * @param $file
     * @param $output
     * @param $origwidth
     * @param $origheight
     * @param $width
     * @param $height
     * @param $cropX
     * @param $cropY
     * @return array|false
     */
    function _imageResizeGif( $file, $output, $origwidth, $origheight, $width, $height, $cropX = 0, $cropY = 0 ) {
        if( ! function_exists( 'imagegif' ) && (
            ! function_exists( 'imagecreatefromgif' ) ||
            ! function_exists( 'imagepng' )
        )) {
            return FALSE;
        }

        $extension = strrchr( strtolower( $file ), '.' );

        //-----------------------------------------
        // Create the blank limited-palette image
        //-----------------------------------------

        if( ! $base_image = $this->_imageCreateBase( $width, $height )) {
            return false;
        }

        //-----------------------------------------
        // get the image pointer to the original image
        //-----------------------------------------

        $imageToResize = @imagecreatefromgif( $file );

        if( function_exists( 'imagecopyresampled' )) {
            if( ! @imagecopyresampled( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight )) {
                imagecopyresized( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight );
            }
        } else {
            imagecopyresized( $base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight );
        }

        if( ! function_exists( 'imagegif' )) {
            $outputFunction = 'imagepng';
            $header 		= 'Content-type: image/x-png';
            $output 		= str_replace( $extension, '.png', $output );
        } else {
            $outputFunction = 'imagegif';
            $header 		= 'Content-type: image/gif';
        }

        $return = false;

        if( empty( $output )) {
            header( $header, true );

            if( $outputFunction( $base_image )) {
                $return = array( $width, $height, $output );
            }
        }
        elseif( $outputFunction( $base_image, $output )) {
            // image destination
            $return = array( $width, $height, $output );
        }

        imagedestroy( $base_image );
        imagedestroy( $imageToResize );

        return $return;
    }
}
