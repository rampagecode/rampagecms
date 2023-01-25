<?php

namespace Admin\Editor\TextManager;

use Admin\AdminControllerParameters;
use Admin\ButtonsView;
use Data\Text\Row;

class TextManagerModel {

    /**
     * @param Row $r
     * @param AdminControllerParameters $info
     * @return string
     */
    function makeTextRow( Row $r, AdminControllerParameters $info ) {
        $buttons = new ButtonsView();
        $view = new TextManagerView();

        $cmd = $buttons->delete( $info->buildURL( $r['id'], 'delete', 'text' ));
        $cmd .= $buttons->moveTo( $info->buildURL( $r['id'], 'move', 'text' ));

        return $view->listFileRow(
            $r['id'],
            str_replace( '_', ' ', $r['title'] ),
            implode( ', ', explode( ',', $r['present_at_pages'] )),
            $cmd
        );
    }
}
