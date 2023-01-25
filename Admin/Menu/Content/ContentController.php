<?php

namespace Admin\Menu\Content;

use Admin\Action\PageContent\PageContentModel;
use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\AdminException;
use Admin\WidgetsView;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Module\ModuleFactory;
use App\Page\ContentTemplate;
use App\Page\Exception;
use App\Page\ModuleTemplate;
use App\Page\Page;
use App\UI\FormBuilder;
use App\UI\TableBuilder;
use Data\Page\Table;
use Lib\ResultInterface;

class ContentController implements AdminControllerInterface {

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        return $this->parsePage();
    }

    public function parsePage() {
        $widgets = new WidgetsView();
        $pageId = (int)$this->app->in('x');

        try {
            $page = Page::createById($pageId, new Table());
        } catch (\Exception $e) {
            return $widgets->error( $e->getMessage() );
        }

        $pageContent = new PageContentModel( $this->app->db() );
        $templates = $pageContent->parseLayout( $this->app, $page );
        $mods = $txts = [];

        foreach( $templates AS $content ) {
            if( ! $content->canAdmin() ) {
                continue;
            }

            $tpl = $content->getTemplate();

            switch( true ) {
                case $tpl instanceof ContentTemplate:
                    if( ! empty( $tpl->id )) {
                        $txts[] = $tpl->id;
                    }
                    break;

                case $tpl instanceof ModuleTemplate:
                    if( ! empty( $tpl->mod )) {
                        list( $name, $id ) = explode(':', $tpl->mod );
                        $mods[] = [
                            'id' => $id,
                            'name' => $name,
                            'placeholder' => $content->getPlaceholder()
                        ];
                    }
                    break;
            }
        }

        //-----------------------------------------
        // Если на странице присутствует только
        // один текстовый контент, то его сразу
        // и выводим.
        //-----------------------------------------

        if( count( $mods ) == 0 AND count( $txts ) == 1 ) {
            return $this->editContent( $txts[0] );
        }

        $mods_results = '';
        $view = new ContentView();

        foreach( $mods AS $mod ) {
            $path = $this->app->rootDir( 'Module', ucfirst( $mod['name'] ));

            try {
                $baseModule = ModuleFactory::loadModule( $path, $this->app );

                if( ! $baseModule->isInstallable() ) {
                    throw new AdminException("Этот модуль не имеет административных функций" );
                }

                $module = $baseModule->getBackend( $mod['id'] );

                if( empty( $module )) {
                    throw new AdminException("Этот модуль не имеет административных функций" );
                }

                if( property_exists( $module, 'parent_page' ) && empty( $module->parent_page )) {
                    $module->parent_page = $pageId;
                }

                if( $module instanceof AdminControllerInterface ) {
                    $module->setRequestParameters( $this->parameters );
                }

                $auto_run = $module->auto_run();

                if( is_array( $auto_run )) {
                    $mod_result = $auto_run['content'];
                    $mod_title = $auto_run['title'];
                }
                elseif ( $auto_run instanceof ResultInterface ) {
                    $mod_result = $auto_run->getResult();
                    $mod_title = $mod['placeholder'];
                }
                else {
                    $mod_result = $auto_run;
                    $mod_title = $mod['placeholder'];
                }
            } catch( \Exception $e ) {
                $mod_result = $e->getMessage();
                $mod_title = $mod['placeholder'];
            }

            $mod_title = str_replace('_', ' ', $mod_title );
            $mods_results .= $view->spacer();
            $mods_results .= $view->moduleBox( $mod_result, $mod_title, md5( uniqid() ));
        }

        $out = '<div class="placeHeader">Модули на странице:</div>';

        if( empty( $mods_results )) {
            $out .= $widgets->messageBox( 'Нет управляемых модулей ' );
        } else {
            $out .= $mods_results;
        }

        //$out .= $view->spacer();
        $out .= '<div class="placeHeader">Контент на странице:</div>';

        //-----------------------------------------
        // Не один из присутствующих на странице модулей
        // не обладал интерфейсом администрирования, а
        // текст только один, стало быть его и выводим.
        //-----------------------------------------

        if( count( $txts ) == 0 ) {
            $out .= $widgets->messageBox( 'Нет редактируемого контента ' );
        }
        else if( count( $txts ) == 1 ) {
            $out .= $this->editContent( $txts[0] );
        }
        elseif( count( $txts ) >= 1 ) {
            $out .= '<table width="100%" border="0" cellspacing="20" align="left">';
            $i = 0;
            $n = 3;
            $textTable = new \Data\Text\Table();

            foreach( $txts AS $txt ) {
                if( $i == 0 ) {
                    $out .= '<tr>';
                }
                elseif( $i == $n ) {
                    $out .= '</tr><tr><td colspan="'.$n.'">&nbsp;</td></tr><tr>';
                    $i = 0;
                }

                $r = $textTable->find( $txt )->current();
                $icon = '[img://]shortcuts/05.png';
                $link = '[tab://]&a=page&i=text&x='.$txt;
                $name = str_replace( '_', ' ', $r['title'] );
                $out .= $view->cell( $name, $link, $icon );
                $i++;
            }

            for( $j = $i; $j < $n; $j++ ) {
                $out .= '<td>&nbsp;</td>';
            }

            $out .= '</table>';
        }

        return $out;
    }

    /**
     * @param int $textId
     * @param string $content
     * @param string $title
     * @return string
     */
    function editContent( $textId, $content = null, $title = null ) {
        $textTable = new \Data\Text\Table();
        $widgets = new WidgetsView();

        try {
            $textRow = $textTable->find( $textId )->current();
        } catch( \Exception $e ) {
            return $widgets->error( $e->getMessage() );
        }

        if( empty( $textRow )) {
            return $widgets->error("Контент с id = '{$textId}' не найден");
        }

        if( $content === null ) {
            $content = $textRow['text_formatted'];
        }

        if( $title === null ) {
            $title = $textRow['title'];
        }

        $table = new TableBuilder();
        $table->firstRowWidth = '10%';
        $table->secondRowWidth = '90%';
        $table->addInput(
            $widgets->formInput('title', $title ),
            'Название'
        );
        $table->addRichTextEditor( 'content', $content );
        $table->addSubmit('Сохранить');
        $form = new FormBuilder( $table->build() );
        $form->makeWYSIWYG();
        $form->addHidden('i', 'add');
        $form->addHidden('x', $textId );

        return $widgets->tableHeader( $title ) . $form->build();
    }

    /**
     * @return string
     */
    function editContentAction() {
        return $this->editContent( $this->parameters->idx );
    }

    /**
     * @return string[]
     */
    function availableActions() {
        return [
            'text' => 'editContent',
            'add' => 'saveContent'
        ];
    }

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    function saveContentAction() {
        $contentId = $this->parameters->idx;
        $widgets = new WidgetsView();

        if( ! $contentId ) {
            return $widgets->error('ID контента не был передан');
        }

        $title = trim( $this->app->in('title' ));
        $text = $_POST['content'];

        if( empty( $title )) {
            return $widgets->error('Заголовок не должен быть пустым' )
                . $this->editContent( $contentId, $text, $title );
        }

        $textTable = new \Data\Text\Table();
        $textTable->update([
            'title' => $title,
            'text_formatted' => $text,
		    'text_searchable' => strip_tags( $text ),
        ], "id = {$contentId}");

        return $widgets->messageBox('Страница обновлена успешно')
            . $this->editContent( $contentId, $text, $title );
    }
}