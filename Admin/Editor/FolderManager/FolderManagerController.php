<?php

namespace Admin\Editor\FolderManager;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\Editor\ContentManager\ContentManagerResult;
use Admin\Editor\ContentManager\ContentManagerView;
use Admin\Editor\ContentManager\ContentManagerParameters;
use Admin\Editor\DocumentManager\DocumentManagerParameters;
use Admin\Editor\ImageManager\ImageManagerParameters;
use Admin\Editor\TextManager\TextManagerParameters;
use App\AppInterface;
use App\Content\Folder;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;
use Data\Folder\Table;
use Lib\ResultInterface;

class FolderManagerController implements AdminControllerInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var FolderManagerView
     */
    private $view;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @var ContentManagerParameters
     */
    private $win;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->view = new FolderManagerView();
        $this->win = ContentManagerParameters::createByFolder( $app, $app->in('x' ));
    }

    public function auto_run() {
        return $this->show();
    }

    function availableActions() {
        return [
            'create_dir' => 'create',
            'edit_folder' => 'edit',
            'del_folder' => 'delete',
            'mov_folder' => 'move',
        ];
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    /**
     * @return ResultInterface|null
     */
    function show() {
        switch ( true ) {
            case $this->win instanceof TextManagerParameters:
                $this->app->redirect( $this->parameters->buildURL( $this->win->folder, 'showForm', 'content' ));
                break;

            case $this->win instanceof ImageManagerParameters:
                $this->app->redirect( $this->parameters->buildURL( $this->win->folder, 'showForm', 'img_mgr' ));
                break;

            case $this->win instanceof DocumentManagerParameters:
                $this->app->redirect( $this->parameters->buildURL( $this->win->folder, 'showForm', 'doc_mgr' ));
        }

        return null;
    }

    function createAction() {
        $dirName = trim( $this->app->in('dir_name'));
        $parentDir = $this->win->folder;

        $result = new ContentManagerResult( $this->parameters );
        $result->setTitle('Создание директории');

        if( ! empty( $dirName )) {
            try {
                (new Table())->create( $dirName, $parentDir );
                $result = $this->show();
            } catch( \Exception $e ) {
                $contentView = new ContentManagerView();
                $content = $contentView->error('Ошибка при создании директории', $e->getMessage() );
                $result->setContent( $content );
            }
        } else {
            $content = $this->view->createDir( $this->parameters->idx );
            $result->setContent( $content );
        }

        return $result;
    }

    function editAction() {
        $id = $this->parameters->idx;
        $table = new Table();
        $row = $table->fetchRow( $table->select()->where('id = ?', $id ));
        $result = new ContentManagerResult( $this->parameters );
        $result->setTitle('Редактирование директории');

        if( $row ) {
            $newName = $this->app->in('folder_name');

            if( ! empty( $newName )) {
                $row['name'] = $newName;
                $row->save();

                $result = $this->show();
            } else {
                $content = $this->view->editFolder( $row['id'], $row['name'] );
                $result->setContent( $content );
            }
        } else {
            $contentView = new ContentManagerView();
            $content = $contentView->error(
                'Ошибка при редактировании директории',
                'Директория не найдена'
            );
            $result->setContent( $content );
        }

        return $result;
    }

    function deleteAction() {
        $id = $this->parameters->idx;
        $folderTable = new Table();
        $folders = $folderTable->findChildren( $id );
        $folders[] = $id;
        $folderTable->delete('id IN ('.join(',', $folders).')' );

        return $this->show();
    }

    function moveAction() {
        $folderId = $this->parameters->idx;
        $parentId = $this->app->in('newfolder');
        $table = new Table();

        if( ! empty( $parentId )) {
            $table->update( [ 'parent' => $parentId ], 'id = '.intval( $folderId ));
            return $this->show();
        }

        $r = $table->fetchRow('id = '.intval( $folderId ));
        $r = $table->fetchRow('id = '.$r['parent'] );

        $options = array();

        if( intval( $r['parent'] )) {
            $options[] = new SelectOptionBuilder( 'На уровень выше..', $r['parent'] );
        }

        $rows = $table->fetchAll('parent = '.$r['id'].' AND id <> '.intval( $folderId ));

        foreach( $rows as $r ) {
            $options[] = new SelectOptionBuilder( $r['name'], $r['id'] );
        }

        if( count( $options )) {
            $builder = new SelectBuilder();
            $builder->name = 'newfolder';
            $builder->setOptions( $options );
            $select = $builder->build();
        } else {
            $select = '<i>Некуда</i>';
        }

        $title = 'Перемещение директории';
        $result = new ContentManagerResult( $this->parameters );
        $result->setTitle( $title );
        $result->setContent( $this->view->moveFolder( $folderId, $title, $select ));

        return $result;
    }
}