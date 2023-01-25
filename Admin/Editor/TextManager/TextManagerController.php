<?php

namespace Admin\Editor\TextManager;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\ButtonsView;
use Admin\Editor\ContentManager\ContentManagerModel;
use Admin\Editor\ContentManager\ContentManagerResult;
use Admin\Editor\ContentManager\ContentManagerView;
use Admin\Editor\FolderManager\FolderManagerModel;
use Admin\WidgetsView;
use App\AppInterface;
use App\Content\Folder;
use App\Content\Text;
use App\Module\ModuleControllerProtocol;
use App\UI\FormBuilder;
use App\UI\TableBuilder;
use Data\Folder\Table;

class TextManagerController implements AdminControllerInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var TextManagerView
     */
    private $view;

    /**
     * @var TextManagerParameters
     */
    private $win;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->view = new TextManagerView();
        $this->win = new TextManagerParameters( $this->app );
    }

    public function auto_run() {
        return $this->showFormAction();
    }

    function availableActions() {
        return [
            'del_file' => 'del_file',
            'mov_doc' => 'mov_doc',
            'save_text' => 'save',
        ];
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    function showFormAction() {
        $_html = '';
        $folderTable = new Table();
        $parent = $folderTable->findParent( $this->win->folder );
        $contentView = new ContentManagerView();

        if( $parent ) {
            $_html .= $contentView->moveUpRow( $parent );
        }

        $folders = $folderTable->findFolderRows( $this->win->folder, $this->win->sort_dir );
        $model = new FolderManagerModel();

        foreach( $folders as $folder ) {
            $_html .= $model->makeFolderRow( $folder, $this->parameters );
        }

        $textTable = new \Data\Text\Table();
        $texts = $textTable->findTextRows( $this->win->folder, $this->win->sort_dir );
        $model = new TextManagerModel();

        foreach ( $texts as $text ) {
            $_html .= $model->makeTextRow( $text, $this->parameters );
        }

        $_html = $contentView->listTable( $_html, '' );

        $result = new TextManagerResult( $this->win, $this->parameters );
        $result->setContent( $_html );

        return $result;
    }

    function editAction() {
        $id = $this->parameters->idx;
        $title = $id == 0 ? 'Создание нового контента' : 'Редактирование существующего контента';
        $table = new \Data\Text\Table();
        $row = $table->fetchRow('id = ' . intval( $id ));
        $widgets = new WidgetsView();
        $table = new TableBuilder();
        $table->firstRowWidth = '10%';
        $table->secondRowWidth = '90%';
        $table->addInput(
            $widgets->formInput('title', $row['title'] ),
            'Заголовок'
        );
        $table->addRichTextEditor( 'content', $row['text_formatted'] );
        $table->addSubmit('Сохранить');
        $form = new FormBuilder( $table->build() );
        $form->makeWYSIWYG();
        $form->addHidden('i', 'save_text');
        $form->addHidden('x', $id );

        return $this->view->openContentWindow( $form->build(), $title );
    }

    function saveAction() {
        $v = [
            'text_formatted'	=> $_POST['content'],
            'text_searchable'	=> strip_tags( $_POST['content'] ),
            'title'				=> trim( $this->app->in('title' )),
        ];

        $contentView = new ContentManagerView();

        if( empty( $v['title'] )) {
            $content = $contentView->error( 'Ошибка', 'Заголовок пустой' );
            $result = new ContentManagerResult( $this->parameters );
            $result->setContent( $content );

            return $result;
        }

        $id = $this->parameters->idx;
        $table = new \Data\Text\Table();

        if( empty( $id )) {
            $v['vfs_folder'] = $this->win->folder;
            $v['id'] = $id;
            $v['present_at_pages'] = '';

            $table->insert( $v );
        } else {
            $table->update( $v, 'id = '.intval( $id ));
        }

        return $this->view->closeContentWindow();
    }

    function deleteAction() {
        $table = new \Data\Text\Table();
        $table->delete('id = '.intval( $this->parameters->idx ));

        return $this->showFormAction();
    }

    function moveAction() {
        $id = $this->parameters->idx;
        $table = new \Data\Text\Table();
        $row = $table->find( $id )->current();
        $newFolderId = (int)$this->app->in('newfolder');

        if( !empty( $newFolderId )) {
            $row->setFromArray(['vfs_folder' => $newFolderId])->save();

            return $this->showFormAction();
        }

        $model = new ContentManagerModel();

        return $model->moveItemForm( $id, $row, 'Перемещение контента', $this->parameters );
    }
}