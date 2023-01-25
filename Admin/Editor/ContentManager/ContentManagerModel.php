<?php

namespace Admin\Editor\ContentManager;

use Admin\AdminException;
use Admin\Editor\FolderManager\FolderManagerView;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;
use Data\Document\Table;
use Sys\Log\Logger;

class ContentManagerModel {
    function moveItemForm( $id, $tableRow, $title, $parameters ) {
        $folderTable = new \Data\Folder\Table();
        $folderRow = $folderTable->findById( $tableRow['vfs_folder'] );
        $options = [];

        if( intval( $folderRow['parent'] )) {
            $options[] = new SelectOptionBuilder( 'На уровень выше..', $folderRow['parent'] );
        }

        $otherRows = $folderTable->findFolderRows( $folderRow['id'], 'ASC' );

        foreach( $otherRows as $row ) {
            $options[] = new SelectOptionBuilder( $row['name'], $row['id'] );
        }

        if( count( $options )) {
            $builder = new SelectBuilder();
            $builder->name = 'newfolder';
            $builder->setOptions( $options );
            $select = $builder->build();
        } else {
            $select = '<i>Некуда</i>';
        }

        $view = new FolderManagerView();
        $result = new ContentManagerResult( $parameters );
        $result->setTitle( $title );
        $result->setContent( $view->moveFolder( $id, $title, $select ));

        return $result;
    }

    function editItemForm( $row, $parameters, $nameValue = null ) {
        if( empty( $row )) {
            throw new AdminException("Запись не найдена" );
        }

        if( empty( $nameValue )) {
            $nameValue = $row['real_name'];
        }

        $view = new ContentManagerView();
        $form = $view->itemEditForm( $nameValue, $row['id'] );
        $contentView = new ContentManagerView();

        return $contentView->window(
            "Редактирование параметров",
            $form,
            $parameters->jsObj
        );
    }
}