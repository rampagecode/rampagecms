<?php

namespace Admin\Editor\FolderManager;

use Admin\AdminControllerParameters;
use Admin\ButtonsView;
use Admin\Editor\TextManager\TextManagerView;
use Data\Folder\Row;

class FolderManagerModel {
    /**
     * @param Row $row
     * @param AdminControllerParameters $info
     * @return string
     */
    function makeFolderRow( Row $row, AdminControllerParameters $info ) {
        $buttons = new ButtonsView();
        $view = new FolderManagerView();

        $cmd = '';
        $cmd .= $buttons->edit( $info->buildURL( $row['id'], 'edit', 'folder' ));
        $cmd .= $buttons->delete( $info->buildURL( $row['id'], 'delete', 'folder' ));
        $cmd .= $buttons->moveTo( $info->buildURL( $row['id'], 'move', 'folder' ));

        return $view->listFolderRow(
            $row['id'],
            $row['name'],
            $cmd
        );
    }
}