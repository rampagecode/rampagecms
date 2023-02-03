<?php

namespace Admin\Action\PageContent;

use Admin\WidgetsView;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Page\Page;
use App\UI\FormBuilder;
use App\UI\TableBuilder;
use Data\Page\Table;
use Lib\FormProcessing;

class PageContentController implements ModuleControllerProtocol  {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var PageContentView
     */
    private $view;

    /**
     * @var PageContentModel
     */
    private $model;

    /**
     * @var string
     */
    private $path;

    var $page_id		= 0;
    var $parent_id		= 0;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->page_id 	 = intval( $app->in('x'));
        $this->parent_id = intval( $app->in('pid'));
        $this->view = new PageContentView();
        $this->model = new PageContentModel( $app->db() );
        $this->path = $this->model->getPagePath(
            $this->page_id ?: $this->parent_id,
            $app->db()
        );
    }

    public function auto_run() {
        return '';
    }

    function newAction() {
        return $this->showAction();
    }

    function newTextPageAction() {
        return $this->showAction( true );
    }

    function showAction( $createTextPage = false ) {
        try {
            $page = empty( $this->page_id ) ? new Page() : Page::createById( $this->page_id, new Table() );

            if( ! $page->getParentId() && $this->parent_id ) {
                $page->setFromArray(['parent' => $this->parent_id ]);
            }

            $content = $this->model->buildPageSettings(
                $this->app,
                $page,
                $createTextPage
            );
        } catch (\Exception $e) {
            $content = $e->getMessage();
        }

        return $this->view->window( $this->path, $content );
    }

    function skinAction() {
        try {
            $content = $this->model->buildPageTemplates(
                $this->app,
                $this->page_id,
                $this->parent_id
            );
        } catch (\Exception $e) {
            $content = $e->getMessage();
        }

        return $this->view->window( $this->path, $content );
    }

    function skinOverloadAction() {
        $tpl_name = $this->app->in('tpl');

        try {
            $this->model->overloadTemplate( $this->app, $this->page_id, $tpl_name );

            $result = $this->model->buildPageTemplates(
                $this->app,
                $this->page_id,
                $this->parent_id
            );
        } catch( \Exception $e ) {
            $widgets = new WidgetsView();
            $result = $widgets->error( $e->getMessage() );
        }

        return $this->view->window( $this->path, $result );
    }

    function skinUnloadAction() {
        $tpl_name = $this->app->in('tpl');

        try {
            $this->model->unloadTemplate( $this->page_id, $tpl_name );

            $result = $this->model->buildPageTemplates(
                $this->app,
                $this->page_id,
                $this->parent_id
            );
        } catch (\Exception $e) {
            $widgets = new WidgetsView();
            $result = $widgets->error( $e->getMessage() );
        }

        return $this->view->window( $this->path, $result );
    }

    function saveParamsAction() {
        try {
            $values = [];

            $result = $this->model->savePageParams(
                $this->app,
                $this->page_id,
                $this->parent_id,
                $values
            );

            $this->path = $this->model->getPagePath(
                $this->page_id ?: $this->parent_id,
                $this->app->db()
            );

            $this->app->pages()->updateCache( new Table() );

            $page = empty( $this->page_id ) ? new Page() : Page::createById( $this->page_id, new Table() );
            $page->setFromArray( $values );

            $result .= $this->model->buildPageSettings(
                $this->app,
                $page,
                $this->app->in('create_content') == 1
            );
        } catch( \Exception $e ) {
            $widgets = new WidgetsView();
            $result = $widgets->error( $e->getMessage() );
            $result .= '<!--@@'.json_encode([ 'success' => false ]).'@@-->';
        }

        return $this->view->window( $this->path, $result );
    }

    function saveSkinAction() {
        try {
            $result = $this->model->saveSkin( $this->app->in(), $this->page_id );

            $result .= $this->model->buildPageTemplates(
                $this->app,
                $this->page_id,
                $this->parent_id
            );
        } catch (\Exception $e) {
            $widgets = new WidgetsView();
            $result = $widgets->error( $e->getMessage() );
        }

        return $this->view->window( $this->path, $result );
    }

    function newLinkAction() {
        $widgets = new WidgetsView();
        $builder = new TableBuilder();
        $builder->firstRowWidth = '50%';
        $builder->secondRowWidth = '50%';
        $builder->addInput(
            $widgets->formInput('link_to', '' ),
            'Id страницы на которую нужно ссылаться',
            'Его можно увидеть при наведении на элемент в дереве сайта'
        );
        $builder->addSubmit('Сохранить');

        $form = new FormBuilder( $builder->build() );
        $form->addHidden('i', 'makeLink' );
        $form->addHidden('x', $this->page_id );
        $form->addHidden('pid', $this->parent_id );

        $content = $widgets->tableHeader( 'Создание ссылки на страницу' );
        $content .= $form->build();

        return $this->view->window( $this->path, $content );
    }

    function makeLinkAction() {
        $from_page = $this->parent_id;
        $to_page = intval( $this->app->in('link_to'));
        $widgets = new WidgetsView();
        $content = '';

        if( $from_page && $to_page ) {
            $table = new Table();
            $table->update([ 'link' => $to_page, 'is_link' => 1 ], 'id = '.$from_page );

            $content = $widgets->messageBox('Ссылка сохранена');
        } else {
            $content = $widgets->error('Страница-ссылка: '.$from_page.'; Целевая страница: '.$to_page);
        }

        return $this->view->window( $this->path, $content );
    }

    function deletePageAction() {
        $id = (int) $this->app->in('pid');
        $widgets = new WidgetsView();
        $content = '';

        try {
            $page = Page::createById($id, new Table());
            $deleted = $page->deletePage();
            $content = $widgets->messageBox("<b>{$deleted}</b> страниц было удалено" );

            $this->app->pages()->updateCache( new Table() );
        } catch(\Exception $e ) {
            $content = $widgets->error( $e->getMessage() );
        }

        return $this->view->window( $this->path, $content );
    }
}