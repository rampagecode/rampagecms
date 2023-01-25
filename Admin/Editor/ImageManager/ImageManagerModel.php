<?php

namespace Admin\Editor\ImageManager;

use Admin\AdminControllerParameters;
use Admin\ButtonsView;
use Admin\AdminException;
use Data\Image\Row;
use Data\Image\Table;
use getID3;
use Sys\Log\Logger;

class ImageManagerModel {
    /**
     * @param Row $r
     * @param AdminControllerParameters $info
     * @return string
     */
    function makeImageRow( Row $r, AdminControllerParameters $info ) {
        $buttons = new ButtonsView();
        $view = new ImageManagerView();

        $cmd = $buttons->edit( $info->buildURL( $r['id'], 'edit', 'img_mgr' ));
        $cmd .= $buttons->delete( $info->buildURL( $r['id'], 'delete', 'img_mgr' ));
        $cmd .= $buttons->moveTo( $info->buildURL( $r['id'], 'move', 'img_mgr' ));

        $thumb = $view->thumbImage( '[thumb://]'.$r['thumb_name'], $r['thumb_width'], $r['thumb_height'] );
        $size = $r['width'].'&nbsp;x&nbsp;'.$r['height'];

        return $view->listImageRow( $r['id'], $thumb, $r['real_name'], $r['file_ext'], $size, $r['file_size'], $cmd );
    }

    /**
     * @param string $sender
     * @param string $imgDir
     * @param Logger $log
     * @param string[] $acceptedExtensions
     * @param int $maxFileSize
     * @param int $maxWidth
     * @param int $maxHeight
     * @param string $vfsFolder
     * @return array
     * @throws AdminException
     */
    function uploadImage( $sender, $imgDir, Logger $log, $acceptedExtensions, $maxFileSize, $maxWidth, $maxHeight, $vfsFolder ) {
        if( ! @is_uploaded_file( $_FILES[ $sender ]['tmp_name'] )) {
            throw new AdminException( 'Файл не найден под именем '.$_FILES[ $sender ]['tmp_name'] );
        }

        $log->add( 'Файл был загружен под именем '.$_FILES[ $sender ]['tmp_name'] );

        $v['real_name'] = $_FILES[ $sender ]['name'];
        $v['file_ext'] 	= substr( strrchr( strtolower( $v['real_name'] ), '.' ), 1 );
        $v['real_name'] = substr( $v['real_name'], 0, ( strlen( $v['real_name'] ) - strlen( $v['file_ext'] ) - 1 ));

        $log->add( 'Настоящее имя: '.$v['real_name'] );
        $log->add( 'Расширение: '.$v['file_ext'] );

        // Возможно ли загружать файлы данного типа?
        if( ! in_array( $v['file_ext'], $acceptedExtensions )) {
            throw new AdminException('Провал: Файл с таким расширением не допущен к загрузке');
        }

        $is_flash = $v['file_ext'] == 'swf';
        $is_wmv = $v['file_ext'] == 'wmv';

        // Допустим ли размер файла?
        if( $_FILES[ $sender ]['size'] > $maxFileSize ) {
            throw new AdminException('Провал: Слишком большой размер файла' );
        }

        $v['file_size'] = $_FILES[ $sender ]['size'];

        // Узнаем размеры изображения
        if( ! $is_wmv ) {
            list( $v['width'], $v['height'] ) = @getimagesize( $_FILES[ $sender ]['tmp_name'] );
        } else {
            $getID3 = new getID3;

            $file_info   = $getID3->analyze( $_FILES[ $sender ]['tmp_name'] );
            $v['width']  = $file_info['video']['resolution_x'];
            $v['height'] = $file_info['video']['resolution_y'];

            if( ! empty( $file_info['error'] )) {
                $log->add( 'getID3 error :'.$file_info['error'] );
            }

            if( ! empty( $file_info['warning'] )) {
                $log->add( 'getID3 warning :'.$file_info['warning'] );
            }
        }

        $log->add( 'Ширина: '.$v['width'] );
        $log->add( 'Высота: '.$v['height'] );

        // Допустимы ли такие размеры?
        if( $v['width']  > $maxWidth || $v['height'] > $maxHeight ) {
            throw new AdminException( 'Провал: Слишком большие длинна и/или ширина' );
        }

        //-----------------------------------------
        // Прочие параметры
        //-----------------------------------------

        $v['time_upload'] 	= time();
        $v['sys_name'] 		= md5( $v['time_upload'] ) .'.'. $v['file_ext'];
        $v['vfs_folder'] 	= $vfsFolder;
        $v['thumb_name']	= strrev( md5( $v['time_upload'] )) .'.'. $v['file_ext'];

        //-----------------------------------------
        // Существуют ли нужные нам директории
        //-----------------------------------------

        $log->add( 'image directory : '.$imgDir );

        if( ! is_dir( $imgDir )) {
            mkdir( $imgDir );
        }

        if( ! in_array( substr( $imgDir, -1 ), ['/', '\\'] )) {
            $imgDir .= DIRECTORY_SEPARATOR;
        }

        if( ! is_dir( $imgDir.'thumbs/' )) {
            mkdir( $imgDir.'thumbs/' );
        }

        // Копируем файл в директорию для хранения
        if( ! @copy( $_FILES[ $sender ]['tmp_name'], $imgDir.$v['sys_name'] )) {
            throw new AdminException( 'Провал: Не удалось скопировать файл в целевую директорию' );
        }

        //-----------------------------------------
        // Создаем иконку для изображения (thumbnail)
        //-----------------------------------------

        if( @function_exists( 'imagecreate' ) && $is_flash == FALSE && $is_wmv == FALSE ) {
            $_in  = $imgDir.$v['sys_name'];
            $_out = $imgDir.'thumbs/'.$v['thumb_name'];

            if( $v['width'] <= 94 && $v['height'] <= 94 ) {
                $v['thumb_width'] 	= $v['width'];
                $v['thumb_height'] 	= $v['height'];

                // Исходный файл сгодится и для превью - просто копируем его
                if( ! copy( $_in, $_out )) {
                    throw new AdminException('Провал: Не удалось скопировать файл для превью' );
                }
            } else {
                $lib = new \Lib\ImageOperations();

                $log->add('_in: '.$_in );
                $log->add('_out: '.$_out );

                try {
                    list( $v['thumb_width'], $v['thumb_height']) = $lib->proportionalResize(
                        $_in,
                        $_out,
                        94,
                        94
                    );
                } catch( \Exception $e ) {
                    throw new AdminException( $e->getMessage() );
                }

                $log->add('thumb_width: '.$v['thumb_width'] );
                $log->add('thumb_height: '.$v['thumb_height'] );
            }
        }
        elseif( $is_flash || $is_wmv ) {
            $v['thumb_name'] = '';
            $v['thumb_width'] = 94;
            $v['thumb_height'] = 94;
        }

        return $v;
    }

    /**
     * @param $imgId
     * @param $imgDir
     * @param Logger $log
     * @return void
     * @throws AdminException
     * @throws \Zend_Db_Table_Exception
     * @throws \Zend_Db_Table_Row_Exception
     */
    function deleteImage( $imgId, $imgDir, Logger $log ) {
        if( empty( $imgId )) {
            throw new AdminException("Идентификатор изображения не найден" );
        }

        $imageTable = new Table();
        $imageRow = $imageTable->findById( $imgId );

        if( empty( $imageRow )) {
            throw new AdminException("Изображение не найдено" );
        }

        if( ! in_array( substr( $imgDir, -1 ), ['/', '\\'] )) {
            $imgDir .= DIRECTORY_SEPARATOR;
        }

        $imagePath = $imgDir.$imageRow['sys_name'];
        $thumbPath = $imgDir.'thumbs/'.$imageRow['thumb_name'];

        $log->add("delete file ".$imagePath );
        $log->add("delete file ".$thumbPath );

        @unlink( $imagePath );
        @unlink( $thumbPath );

        $imageRow->delete();
    }
}