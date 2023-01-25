<?php

namespace Module\Posts\Backend;

use Admin\ButtonsView;
use Admin\WidgetsView;
use App\Module\ModuleBackendController;
use App\Page\ContentTemplate;
use App\Page\Page;
use App\UI\FormBuilder;
use App\UI\Paginator;
use Data\Module\Table;
use Module\ModuleException;
use Module\Posts\Model\PostFormDTO;
use Module\Posts\Model\PostsForm;
use Module\Posts\Model\PostOperations;

class PostsController extends ModuleBackendController {
    public function auto_run() {
        return $this->showListAction();
    }

    function showListAction() {
        $view = new PostsView();
        $buttons = new ButtonsView();
        $postsTableName = $this->moduleDelegate->getTableName( $this->moduleId );
        $postsCount = $this->app->db()->select()->from( $postsTableName, 'COUNT(id)' )->query()->fetchColumn();

        $postsPerPage = 5;
        $offset = intval( $this->app->in('posts_offset' ));
        $paginator_url = $this->parameters->buildURL() . '&posts_offset=';
        $paginator = new Paginator( $offset, $postsCount, $postsPerPage, $paginator_url );

        $postRows = $this->app->db()->select()
            ->from(['m' => $this->moduleDelegate->getTableName( $this->moduleId )])
            ->join(
                ['s' => 'site_tree'],
                's.id = m.page_id',
                ['pagetitle']
            )
            ->order('id DESC')
            ->limitPage( $paginator->currentPage, $postsPerPage )
            ->query()
            ->fetchAll();

        $listRows = [];
        foreach( $postRows as $row ) {
            $editButton = $buttons->edit( $this->buildURL( $row['id'], 'edit' ));
            $deleteButton = $buttons->delete( $this->buildURL( $row['id'], 'delete' ));
            $listRows[] = $view->postsTableRow( $editButton.' '.$deleteButton, $row['date'], $row['pagetitle']);
        }

        return $view->postsTable(
            implode("\n", $listRows),
            $paginator->resultHTML,
            $buttons->createNew( $this->buildURL(0,'create' ), 'Создать новый' )
        );
    }

    function createAction() {
        $data = new PostFormDTO();
        $data->day 	 = date('d');
        $data->month = date('m');
        $data->year	 = date('Y');

        return $this->editForm( $data );
    }

    function editAction() {
        $operations = new PostOperations( $this->app->db(), $this->getModuleTable() );
        $postId = intval( $this->parameters->idx );
        $data = $operations->loadPostData( $postId );

        return $this->editForm( $data );
    }

    function saveAction() {
        $data = new PostFormDTO();
        $data->id = intval( $this->app->in('x'));
        $data->day = intval( $this->app->in('date_day'));
        $data->month = intval( $this->app->in('date_month'));
        $data->year	= intval( $this->app->in('date_year'));
        $data->title = trim( $this->app->in('title'));
        $data->shortText = $_POST['short_text'];
        $data->longText = $_POST['long_text'];

        $widgets = new WidgetsView();
        $processing = new \Module\Posts\Model\PostsForm();

        try {
            $processing->validateFormData( $data );
        } catch (ModuleException $e) {
            return $widgets->error( $e->getMessage() ) . $this->editForm( $data );
        }

        $operations = new PostOperations( $this->app->db(), $this->getModuleTable() );

        try {
            if( empty( $data->id )) {
                $moduleTable = new Table();
                $moduleRow = $moduleTable->find( $this->moduleId )->current()->toArray();

                $operations->createPost(
                    $data,
                    $moduleRow['main_page_id'],
                    $moduleRow['item_page_layout'],
                    $moduleRow['item_page_placeholder'],
                    $moduleRow['item_text_folder_id']
                );

                return $widgets->messageBox("Запись создана") . $this->showListAction();
            } else {
                $operations->updatePost( $data );

                return $widgets->messageBox("Запись обновлена") . $this->showListAction();
            }
        } catch (\Exception $e) {
            return $widgets->error($e->getMessage() ) . $this->editForm( $data );
        }
    }

    function deleteAction() {
        $postId = $this->parameters->idx;
        $widgets = new WidgetsView();

        $this->app->db()->beginTransaction();
        try {
            if( empty( $postId )) {
                throw new ModuleException('');
            }

            $postRow = $this->getModuleTable()->find( $postId )->current();

            if( empty( $postRow )) {
                throw new ModuleException('');
            }

            $pageId = $postRow['page_id'];
            $page = Page::createById( $pageId, new \Data\Page\Table());
            $deleted = $page->deletePage(true);

            $this->getModuleTable()->delete('id = '.$postId);

            $this->app->pages()->updateCache(
                $this->app->rootDir('conf', 'cache.tree.php'),
                new \Data\Page\Table()
            );

            $this->app->db()->commit();

            return $widgets->messageBox("<b>{$deleted}</b> страниц было удалено" ) . $this->showListAction();
        } catch(\Exception $e ) {
            $this->app->db()->rollBack();
            return $widgets->error( $e->getMessage() ) . $this->showListAction();
        }
    }

    /**
     * @param PostFormDTO $data
     * @return string
     */
    private function editForm( PostFormDTO $data ) {
        $widgets = new WidgetsView();
        $formModel = new PostsForm();
        $form = new FormBuilder( $formModel->buildTable( $widgets, $data )->build() );
        $form->makeWYSIWYG();
        $form->addHidden('i', 'save');

        if( !empty( $data->id )) {
            $form->addHidden('x', $data->id );
            $title = "Редактирование записи";
        } else {
            $title = "Новая запись";
        }

        return $widgets->tableHeader( $title ) . $form->build();
    }
}
