<?php

namespace Admin\Editor\ImageManager;

use Admin\AdminControllerParameters;
use Admin\Editor\ContentManager\ContentManagerModel;
use Admin\Editor\ContentManager\ContentManagerResult;
use Admin\Editor\ContentManager\ContentManagerView;
use Admin\Editor\FolderManager\FolderManagerModel;
use Admin\Editor\FolderManager\FolderManagerView;
use Admin\AdminException;
use Admin\WidgetsView;
use App\AppInterface;
use App\Content\Folder;
use App\Content\Image;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;
use Data\Image\Table;

class ImageManagerController implements \Admin\AdminControllerInterface {

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @var ImageManagerParameters
     */
    private $win;

    function availableActions() {
        return [
            'upload_image' => 'upload',
        ];
    }

    function setRequestParameters( \Admin\AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    public function __construct( \App\AppInterface $app ) {
        $this->app = $app;
        $this->win = new ImageManagerParameters( $app );
    }

    public function auto_run() {
        return $this->showFormAction();
    }

    function showFormAction( $message = null ) {
        $_html = '';
        $folderTable = new \Data\Folder\Table();

        $parent = $folderTable->findParent( $this->win->folder );
        $contentView = new ContentManagerView();

        if( $parent ) {
            $_html .= $contentView->moveUpRow( $parent );
        }

        $folders = $folderTable->findFolderRows( $this->win->folder, $this->win->sort_dir );
        $model = new FolderManagerModel();

        foreach( $folders as $folder ) {
            $_html .= $model->makeFolderRow( $folder, $this->parameters, true );
        }

        $imageTable = new Table();
        $rows = $imageTable->findImageRows( $this->win->folder, $this->win->sort_dir );
        $model = new ImageManagerModel();
        $_js = '';

        foreach ( $rows as $row ) {
            $_html .= $model->makeImageRow( $row, $this->parameters );

            $_js .= 'imgs_width['.$row['id'].'] = "'.$row['width'].'";'."\n";
            $_js .= 'imgs_height['.$row['id'].'] = "'.$row['height'].'";'."\n";
            $_js .= 'imgs_url['.$row['id'].'] = "'.$this->app->getVar('imgs_url') . $row['sys_name'].'";'."\n";
        }

        $_html = $contentView->listTable( $_html, $_js );

        if( ! empty( $message )) {
            $_html = $message . $_html;
        }

        $view = new ImageManagerView();
        $createFolderURL = $this->parameters->buildURL( $this->win->folder, 'create', 'folder' );

        return $view->window( $this->parameters->jsObj, $this->win->folder, '', '', '', '', '', $_html, $createFolderURL );
    }

    function uploadAction() {
        $this->app->log("Загрузка изображения");

        $extensionsString = $this->app->getVar('rte_img_types');
        $acceptedExtensions = explode( ',', str_replace( ' ', '', strtolower( $extensionsString )));
        $maxFileSize = $this->app->getVar('max_img_file_size');
        $imgDir = $this->app->getVar('imgs_dir');
        $vfsFolder = $this->win->folder;
        $maxWidth = $this->app->getVar('max_image_width');
        $maxHeight = $this->app->getVar('max_image_height');
        $model = new ImageManagerModel();
        $message = null;

        try {
            $row = $model->uploadImage(
                'upload_field',
                $imgDir,
                $this->app->log(),
                $acceptedExtensions,
                $maxFileSize,
                $maxWidth,
                $maxHeight,
                $vfsFolder
            );

             $this->app->db()->insert( 'vfs_imgs', $row );
        } catch (AdminException $e) {
            $this->app->log( $e->getMessage() );
            $widgets = new WidgetsView();
            $message = $widgets->error( $e->getMessage() );
        }

        return $this->showFormAction( $message );
    }

    function deleteAction() {
        $this->app->log("Удаление изображения");

        $imgId = (int) $this->parameters->idx;
        $imgDir = $this->app->getVar('imgs_dir');
        $model = new ImageManagerModel();
        $message = null;

        try {
            $model->deleteImage( $imgId, $imgDir, $this->app->log() );
        } catch (\Exception $e) {
            $this->app->log( $e->getMessage() );
            $widgets = new WidgetsView();
            $message = $widgets->error( $e->getMessage() );
        }

        return $this->showFormAction( $message );
    }

    function editAction() {
        $this->app->log("Редактирование изображения");

        $imgId = (int) $this->parameters->idx;
        $imageTable = new Table();

        try {
            $imageRow = $imageTable->findById( $imgId );

            if( empty( $imageRow )) {
                throw new AdminException("Изображение не найдено" );
            }

            $newFileName = trim( $this->app->in('file_name'));

            if( empty( $newFileName )) {
                $view = new ImageManagerView();
                $form = $view->imageEditForm( $imageRow['real_name'], $imageRow['id'] );
                $contentView = new ContentManagerView();

                return $contentView->window(
                    "Редактирование параметров изображения",
                    $form,
                    $this->parameters->jsObj
                );
            } else {
                $imageRow['real_name'] = $newFileName;
                $imageRow->save();

                return $this->showFormAction();
            }
        } catch( \Exception $e ) {
            $this->app->log( $e->getMessage() );

            $widgets = new WidgetsView();

            return $this->showFormAction( $widgets->error( $e->getMessage() ));
        }
    }

    function optionsAction() {
        $in = $this->app->in();

        $v['alt'] 		= $in['alt'];
        $v['image'] 	= $in['image'];
        $v['align'] 	= $in['align'];
        $v['mtop'] 		= intval( $in['mtop'] );
        $v['mleft'] 	= intval( $in['mleft'] );
        $v['mright'] 	= intval( $in['mright'] );
        $v['mbottom'] 	= intval( $in['mbottom'] );
        $v['border'] 	= intval( $in['border'] );
        $v['width'] 	= intval( $in['width'] );
        $v['height'] 	= intval( $in['height'] );

        if( preg_match( "/[a-z0-9]{32}\.[a-z]{3,4}/", $v['image'], $matches )) {
            $v['thumb'] = $matches[0];

            $imageTable = new Table();

            if( $r = $imageTable->fetchRow('sys_name = "'.$matches[0].'"') ) {
                $file_ext = substr( strrchr( strtolower( $v['image'] ), '.' ), 1 );

                if( $file_ext == 'swf' ) {
                    $v['thumb'] = '<img id="wrap" src="[img://]editor/flash_thumb.jpg" width="'.$r['thumb_width'].'" height="'.$r['thumb_height'].'" border="0">';
                }
                elseif( $file_ext == 'wmv' ) {
                    $v['thumb'] = '<img id="wrap" src="[img://]editor/wmv_thumb.jpg" width="'.$r['thumb_width'].'" height="'.$r['thumb_height'].'" border="0">';
                }
                else {
                    $v['thumb'] = '<img id="wrap" src="[thumb://]'.$r['thumb_name'].'" width="'.$r['thumb_width'].'" height="'.$r['thumb_height'].'" border="0">';
                }
            }
        }

        if( ! isset( $v['thumb'] ) || $v['thumb'] == '' ) {
            $v['thumb'] = '<img id="wrap" src="[img://]wrap_preview.gif" width="48" height="48" border="0">';
        }

        $v['real_width']  = intval( $r['width']  );
        $v['real_height'] = intval( $r['height'] );

        $view = new ImageManagerView();

        return $view->optionsWindow( $v, $this->parameters->jsObj );
    }

    function zoomAction() {
        $_html = '';
        $_js = '';
        $folderTable = new \Data\Folder\Table();

        $parent = $folderTable->findParent( $this->win->folder );
        $contentView = new ContentManagerView();
        $view = new ImageManagerView();
        $folderView = new FolderManagerView();

        if( $parent ) {
            $_html .= $contentView->moveUpRow( $parent );
        }

        $folders = $folderTable->findFolderRows( $this->win->folder, $this->win->sort_dir );

        foreach( $folders as $folder ) {
            $_html .=  $folderView->listFolderRowNoControls(
                $folder['id'],
                $folder['name']
            );
        }

        $imageTable = new Table();
        $rows = $imageTable->findImageRows( $this->win->folder, $this->win->sort_dir );

        foreach ( $rows as $row ) {
            $thumb = $view->thumbImage( '[thumb://]'.$row['thumb_name'], $row['thumb_width'], $row['thumb_height'] );
            $size = $row['width'].'&nbsp;x&nbsp;'.$row['height'];

            $_html .= $view->listImageRowNoControls( $row['id'], $thumb, $row['real_name'], $row['file_ext'], $size, $row['file_size'] );

            $_js .= 'imgs_width['.$row['id'].'] = "'.$row['width'].'";'."\n";
            $_js .= 'imgs_height['.$row['id'].'] = "'.$row['height'].'";'."\n";
            $_js .= 'imgs_url['.$row['id'].'] = "'.$this->app->getVar('imgs_url') . $row['sys_name'].'";'."\n";
        }

        $_html = $contentView->listTable( $_html, $_js );

        return $view->zoomWindow( $this->parameters->jsObj, $_html );
    }

    function moveAction() {
        $id = $this->parameters->idx;
        $table = new Table();
        $row = $table->findById( $id );
        $newFolderId = (int)$this->app->in('newfolder');

        if( !empty( $newFolderId )) {
            $row->setFromArray(['vfs_folder' => $newFolderId])->save();

            return $this->showFormAction();
        }

        $model = new ContentManagerModel();

        return $model->moveItemForm( $id, $row, 'Перемещение изображения', $this->parameters );
    }
}