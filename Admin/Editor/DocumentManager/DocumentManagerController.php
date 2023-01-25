<?php

namespace Admin\Editor\DocumentManager;

use Admin\AdminControllerParameters;
use Admin\Editor\ContentManager\ContentManagerModel;
use Admin\Editor\ContentManager\ContentManagerView;
use Admin\Editor\FolderManager\FolderManagerModel;
use Admin\Editor\ImageManager\ImageManagerModel;
use Admin\Editor\ImageManager\ImageManagerParameters;
use Admin\Editor\ImageManager\ImageManagerView;
use Admin\AdminException;
use Admin\WidgetsView;
use App\AppInterface;
use App\Content\Folder;
use App\Content\Image;
use App\Content\Text;
use Data\Folder\Table;
use Lib\TextFormatter;

class DocumentManagerController implements \Admin\AdminControllerInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;


    /**
     * @var DocumentManagerParameters
     */
    private $win;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->win = new DocumentManagerParameters( $app );
    }

    public function auto_run() {
        return $this->showFormAction();
    }

    function showFormAction( $message = null ) {
        $_html = '';
        $folderTable = new Table();

        $parent = $folderTable->findParent( $this->win->folder );
        $contentView = new ContentManagerView();

        if( $parent ) {
            $_html .= $contentView->moveUpRow( $parent );
        }

        $folders_sort_dir = $this->win->sort_by == 'name'
            ? $this->win->sort_dir
            : 'asc'
        ;

        $folders = $folderTable->findFolderRows( $this->win->folder, $folders_sort_dir );
        $model = new FolderManagerModel();

        foreach( $folders as $folder ) {
            $_html .= $model->makeFolderRow( $folder, $this->parameters );
        }

        $docTable = new \Data\Document\Table();
        $rows = $docTable->findDocumentRows( $this->win->folder, $this->win->sort_dir );
        $model = new DocumentManagerModel();
        $_js = '';
        $formatter = new TextFormatter();

        foreach ( $rows as $row ) {
            $fileInfo = $model->getFileInfo( $row['file_ext'], $this->app->language() );

            $_html .= $model->makeDocumentRow( $row, $this->parameters, $fileInfo );

            //-----------------------------------------
            // Добавляем в JavaScript массив информацию
            // о размере этого файла
            //-----------------------------------------

            $_js .= 'aDocSize['.$row['id'].'] = "'.$formatter->formatSize(( $row['file_size'] )).'";'."\n";

            //-----------------------------------------
            // Об имени файла
            //-----------------------------------------

            $_js .= 'aDocName['.$row['id'].'] = "'.$row['real_name'].'.'.$row['file_ext'].'";'."\n";

            //-----------------------------------------
            // Ссылка на скачивание с сайта
            //-----------------------------------------

            $_js .= 'aDocURL['.$row['id'].'] = "'.$this->app->getVar('root_url').'?download='.$row['id'].'";'."\n";

            //-----------------------------------------
            // Когда был загружен файл
            //-----------------------------------------

            $dateString = $formatter->formatDate(
                $row['time_upload'],
                $this->app->config(),
                $this->app->language(),
                'TINY'
            );

            $_js .= 'aDocDate['.$row['id'].'] = "'.$dateString.'";'."\n";

            //-----------------------------------------
            // Формируем список групп пользователей
            // имеющих доступ к документу
            //-----------------------------------------

            $access = unserialize( $row['group_access'] );
            $g_list = array();

            foreach( $access AS $group ) {
                $group_cache = $this->app->config()->getGroupCache( $group );

                if( !empty( $group_cache )) {
                    $g_list[] = $group_cache['prefix'] . $group_cache['g_title'] . $group_cache['suffix'];
                }
            }

            $_js .= 'aDocGroup['.$row['id'].'] = "'.implode( ', ', $g_list ).'";'."\n";
        }

        $view = new DocumentManagerView();
        $sortDir = $this->win->sort_dir == 'asc' ? 'desc' : 'asc';
        $sortByNameURL = "[mod://]&sort_by=name&sort_dir={$sortDir}";
        $sortByTypeURL = "[mod://]&sort_by=type&sort_dir={$sortDir}";
        $sortByNameImg = '[img://]editor/arrow_'.( $this->win->sort_dir == 'asc' ? 'up' : 'down' ).'.gif';
        $sortByTypeImg = '[img://]editor/arrow_'.( $this->win->sort_dir == 'asc' ? 'up' : 'down' ).'.gif';

        switch( $this->win->sort_by ) {
            case 'name':
                $sortByTypeImg = '[img://]s.gif';
                break;
            case 'type':
                $sortByNameImg = '[img://]s.gif';
                break;
            default:
                $sortByNameImg = '[img://]s.gif';
                $sortByTypeImg = '[img://]s.gif';
        }

        $sortTableHeader = $view->sortTableHeader( $sortByNameURL, $sortByNameImg, $sortByTypeURL, $sortByTypeImg );
        $maxFileSize = $this->app->getVar('max_doc_file_size');
        $extensionsString = $this->app->getVar('rte_doc_types');
        $acceptedExtensions = explode( ',', str_replace( ' ', '', strtolower( $extensionsString )));
        $extensionsAsString = '';

        foreach( $acceptedExtensions AS $ext ) {
            $extensionsAsString .= "*.{$ext};";
        }

        $path = $folderTable->pathTo($this->win->folder, $this->win->root );
        $_html = $view->list_table( $_html, $_js );
        $createFolderURL = $this->parameters->buildURL( $this->win->folder, 'create', 'folder' );

        if( ! empty( $message )) {
            $_html = $message . $_html;
        }

        return $view->window(
            $_html,
            $sortTableHeader,
            $path,
            $this->win->folder,
            $maxFileSize,
            $extensionsAsString,
            $this->parameters->jsObj,
            $createFolderURL
        );
    }

    function availableActions() {
        return [
            'upload_file' => 'upload',
        ];
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    function uploadAction() {
        $this->app->log('Загрузка документа');

        $sender = 'upload_field';
        $extensionsString = $this->app->getVar('rte_doc_types');
        $acceptedExtensions = explode( ',', str_replace( ' ', '', strtolower( $extensionsString )));
        $maxFileSize = $this->app->getVar('max_doc_file_size');
        $vfsFolder = $this->win->folder;
        $cache = $this->app->config()->getCache();
        $docDir = $this->app->getVar('docs_dir');

        $model = new DocumentManagerModel();

        try {
            $row = $model->uploadFile(
                $sender,
                $this->app->log(),
                $cache,
                $acceptedExtensions,
                $maxFileSize,
                $vfsFolder,
                $docDir
            );

            $this->app->db()->insert( 'vfs_docs', $row );
        } catch( AdminException $e ) {
            $this->app->log( $e->getMessage() );
            $widgets = new WidgetsView();

            return $this->showFormAction( $widgets->error( $e->getMessage() ));
        }

        return $this->showFormAction();
    }

    function moveAction() {
        $id = $this->parameters->idx;
        $table = new \Data\Document\Table();
        $row = $table->findById( $id );
        $newFolderId = (int)$this->app->in('newfolder');

        if( !empty( $newFolderId )) {
            $row->setFromArray(['vfs_folder' => $newFolderId])->save();

            return $this->showFormAction();
        }

        $model = new ContentManagerModel();

        return $model->moveItemForm( $id, $row, 'Перемещение документа', $this->parameters );
    }

    function editAction() {
        $this->app->log("Редактирование изображения");
        $table = new \Data\Document\Table();

        try {
            $row = $table->findById( (int) $this->parameters->idx );
            $newFileName = trim( $this->app->in('file_name'));

            if( $this->parameters->isPostRequest() && !empty( $newFileName )) {
                $row['real_name'] = $newFileName;
                $row->save();
                return $this->showFormAction();
            } else {
                $model = new ContentManagerModel();
                return $model->editItemForm( $row, $this->parameters, $newFileName );
            }
        } catch( \Exception $e ) {
            $this->app->log( $e->getMessage() );
            $widgets = new WidgetsView();

            return $this->showFormAction( $widgets->error( $e->getMessage() ));
        }
    }

    function deleteAction() {
        $this->app->log("Удаление документа");

        $docId = (int) $this->parameters->idx;
        $docDir = $this->app->getVar('docs_dir');
        $model = new DocumentManagerModel();
        $message = null;

        try {
            $model->deleteDocument( $docId, $docDir, $this->app->log() );
        } catch (\Exception $e) {
            $this->app->log( $e->getMessage() );
            $widgets = new WidgetsView();
            $message = $widgets->error( $e->getMessage() );
        }

        return $this->showFormAction( $message );
    }
}