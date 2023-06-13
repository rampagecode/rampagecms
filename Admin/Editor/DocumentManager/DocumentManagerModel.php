<?php

namespace Admin\Editor\DocumentManager;

use Admin\AdminControllerParameters;
use Admin\ButtonsView;
use Admin\AdminException;
use Data\Document\Row;
use Data\Document\Table;
use Sys\Config\Cache;
use Sys\Language\LanguageManager;
use Sys\Log\Logger;

class DocumentManagerModel {
    /**
     * @param Row $r
     * @param AdminControllerParameters $info
     * @param string[] $fileInfo
     * @return string
     */
    function makeDocumentRow( Row $r, AdminControllerParameters $info, $fileInfo ) {
        $buttons = new ButtonsView();
        $view = new DocumentManagerView();
        $model = new DocumentManagerModel();

        $cmd = $buttons->edit( $info->buildURL( $r['id'], 'edit', 'doc_mgr' ));
        $cmd .= $buttons->delete( $info->buildURL( $r['id'], 'delete', 'doc_mgr' ));
        $cmd .= $buttons->moveTo( $info->buildURL( $r['id'], 'move', 'doc_mgr' ));

        $file = array(
            'id'		=> $r['id'],
            'controls' 	=> $cmd,
            'type'		=> $fileInfo['description'],
            'name'		=> $r['real_name'],
            'icon'		=> $fileInfo['icon'],
            'cursor'	=> 'default',
            'onclick'	=> 'oDialog.showInfo('.$r['id'].')',
        );

        return $view->list_row( $file );
    }

    /**
     * @param string $sender
     * @param Logger $log
     * @param Cache $cache
     * @param string[] $acceptedExtensions
     * @param int $maxFileSize
     * @param string $vfsFolder
     * @param string $docDir
     * @return array
     * @throws AdminException
     */
    function uploadFile( $sender, Logger $log, Cache $cache, $acceptedExtensions, $maxFileSize, $vfsFolder, $docDir ) {
        if( ! @is_uploaded_file( $_FILES[ $sender ]['tmp_name'] )) {
            throw new AdminException( 'Файл не найден под именем '.$_FILES[ $sender ]['tmp_name'] );
        }

        $log->add( 'Файл был загружен под именем '.$_FILES[ $sender ]['tmp_name'] );

        $v = [];
        $v['real_name'] = $_FILES[ $sender ]['name'];
        $v['file_ext'] 	= substr( strrchr( strtolower( $v['real_name'] ), '.' ), 1 );
        $v['real_name'] = substr( $v['real_name'], 0, ( strlen( $v['real_name'] ) - strlen( $v['file_ext'] ) - 1 ));

        $log->add( 'Настоящее имя: '.$v['real_name'] );
        $log->add( 'Расширение: '.$v['file_ext'] );

        // Возможно ли загружать файлы данного типа?
        if( ! in_array( $v['file_ext'], $acceptedExtensions )) {
            throw new AdminException('Провал: Файл с таким расширением не допущен к загрузке');
        }

        // Допустим ли размер файла?
        if( $_FILES[ $sender ]['size'] > $maxFileSize ) {
            throw new AdminException('Провал: Слишком большой размер файла' );
        }

        $v['file_size'] = $_FILES[ $sender ]['size'];

        //-----------------------------------------
        // Прочие параметры
        //-----------------------------------------

        $v['time_upload'] 	= time();
        $v['sys_name'] 		= md5( $v['time_upload'] ) .'.'. $v['file_ext'];
        $v['vfs_folder'] 	= $vfsFolder;

        //-----------------------------------------
        // Доступ к файлу для ВСЕХ групп пользователей по-умолчанию
        //-----------------------------------------

        $full_access = array();

        foreach( $cache['group_cache'] AS $gid ) {
            $full_access[] = $gid['g_id'];
        }

        $v['group_access'] 	= serialize( $full_access );

        //-----------------------------------------
        // копируем файл в директорию для хранения
        //-----------------------------------------

        $log->add( 'document directory : '.$docDir );

        if( ! is_dir( $docDir )) {
            mkdir( $docDir );
        }

        if( ! in_array( substr( $docDir, -1 ), ['/', '\\'] )) {
            $docDir .= DIRECTORY_SEPARATOR;
        }

        // Копируем файл в директорию для хранения
        if( ! @copy( $_FILES[ $sender ]['tmp_name'], $docDir.$v['sys_name'] )) {
            throw new AdminException( 'Провал: Не удалось скопировать файл в целевую директорию' );
        }

        return $v;
    }

    /**
     * Определяем по расширению файла его тип, иконку и возможность пред. просмотра
     * @param string $extension
     * @param LanguageManager $lang
     * @return array
     */
    function getFileInfo( $extension, LanguageManager $lang ) {
        $v = [];

        switch( $extension ) {
            case 'htm':
            case 'html':
                $v['icon'] = 'htm_icon';
                $v['description'] = $lang['vfs_html'];
                $v['preview'] = 1;
                break;

            case 'pdf':
                $v['icon'] = 'pdf_icon';
                $v['description'] = $lang['vfs_pdf'];
                $v['preview'] = 0;
                break;

            case 'rtf':
                $v['icon'] = 'rtf_icon';
                $v['description'] = $lang['vfs_rtf'];
                $v['preview'] = 0;
                break;

            case 'txt':
                $v['icon'] = 'txt_icon';
                $v['description'] = $lang['vfs_txt'];
                $v['preview'] = 1;
                break;

            case 'doc':
                $v['icon'] = 'doc_icon';
                $v['description'] = $lang['vfs_doc'];
                $v['preview'] = 0;
                break;

            case 'xls':
            case 'xl':
                $v['icon'] = 'xl_icon';
                $v['description'] = $lang['vfs_xl'];
                $v['preview'] = 0;
                break;

            case 'ppt':
                $v['icon'] = 'ppt_icon';
                $v['description'] = $lang['vfs_ppt'];
                $v['preview'] = 0;
                break;

            case 'pps':
                $v['icon'] = 'pps_icon';
                $v['description'] = $lang['vfs_pps'];
                $v['preview'] = 0;
                break;

            case 'zip':
                $v['icon'] = 'zip_icon';
                $v['description'] = $lang['vfs_zip'];
                $v['preview'] = 0;
                break;

            case 'tar':
                $v['icon'] = 'zip_icon';
                $v['description'] = $lang['vfs_tar'];
                $v['preview'] = 0;
                break;

            case 'swf':
                $v['icon'] = 'swf_icon';
                $v['description'] = $lang['vfs_swf'];
                $v['preview'] = 0;
                break;

            case 'wmv':
                $v['icon'] = 'wmv_icon';
                $v['description'] = $lang['vfs_wmv'];
                $v['preview'] = 0;
                break;

            case 'rm':
                $v['icon'] = 'rm_icon';
                $v['description'] = $lang['vfs_rm'];
                $v['preview'] = 0;
                break;

            case 'mov':
                $v['icon'] = 'mov_icon';
                $v['description'] = $lang['vfs_mov'];
                $v['preview'] = 0;
                break;

            case 'jpeg':
            case 'jpg':
                $v['icon'] = 'jpg_icon';
                $v['description'] = $lang['vfs_jpg'];
                $v['preview'] = 1;
                break;

            case 'gif':
                $v['icon'] = 'gif_icon';
                $v['description'] = $lang['vfs_gif'];
                $v['preview'] = 1;
                break;

            case 'png':
                $v['icon'] = 'png_icon';
                $v['description'] = $lang['vfs_png'];
                $v['preview'] = 1;
                break;

            case 'exe':
                $v['icon'] = 'exe_icon';
                $v['description'] = $lang['vfs_exe'];
                $v['preview'] = 0;
                break;

            default:
                $v['icon'] = 'unknown_icon';
                $v['description'] = strtoupper( str_replace( '.', '', $extension )).' '.$lang['vfs_file'];
                $v['preview'] = 0;
                break;

            case 'rar':
                $v['icon'] = 'rar_icon';
                $v['description'] = $lang['vfs_rar'];
                $v['preview'] = 0;
                break;

            case 'bz2':
                $v['icon'] = 'zip_icon';
                $v['description'] = $lang['vfs_bz2'];
                $v['preview'] = 0;
                break;
        }

        $v['icon'] = '[img://]vfs/' . $v['icon'] . '.gif';

        return $v;
    }

    /**
     * @param $docId
     * @param $docDir
     * @param Logger $log
     * @return void
     * @throws AdminException
     * @throws \Zend_Db_Table_Exception
     * @throws \Zend_Db_Table_Row_Exception
     */
    function deleteDocument( $docId, $docDir, Logger $log ) {
        if( empty( $docId )) {
            throw new AdminException("Идентификатор документа не найден" );
        }

        $docTable = new Table();
        $docRow = $docTable->findById( $docId );

        if( empty( $docRow )) {
            throw new AdminException("Документ не найден" );
        }

        if( ! in_array( substr( $docDir, -1 ), ['/', '\\'] )) {
            $docRow .= DIRECTORY_SEPARATOR;
        }

        $path = $docDir.$docRow['sys_name'];

        $log->add("delete file ".$path );

        @unlink( $path );

        $docRow->delete();
    }
}