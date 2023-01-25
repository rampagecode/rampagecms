<?php

namespace Admin\Action\PageContent\TemplatesFactory;

use Admin\Action\PageContent\PageContentFactory;
use Admin\ButtonsView;
use Admin\AdminException;
use Admin\WidgetsView;
use App\Module\ModuleInfo;
use App\Page\ContentTemplate;
use App\Page\ModuleTemplate;
use App\Page\PageTemplate;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;

class OverriddenRowFactory implements PageContentFactory {

    /**
     * @var int
     */
    private $page_id;

    /**
     * @var TemplatesFactoryDelegate
     */
    private $delegate;

    /**
     * @var PageTemplate
     */
    private $template;

    /**
     * @var ModuleInfo[]
     */
    private $mods;

    /**
     * @param int $page_id
     * @param PageTemplate $template
     * @param TemplatesFactoryDelegate $delegate
     * @param ModuleInfo[] $mods
     */
    public function __construct( $page_id, PageTemplate $template, TemplatesFactoryDelegate $delegate, $mods ) {
        $this->page_id = $page_id;
        $this->delegate = $delegate;
        $this->template = $template;
        $this->mods = $mods;
    }

    /**
     * @return string
     */
    function make() {
        $widgets = new WidgetsView();
        $buttons = new ButtonsView();
        $view = new TemplatesFactoryView();

        try {
            return $this->makeRow( $this->template, $this->mods, $view, $widgets, $buttons );
        } catch( \Exception $e ) {
            return $view->onErrorRow( $e->getMessage() );
        }
    }

    /**
     * @param PageTemplate $template
     * @param ModuleInfo[] $mods
     * @param TemplatesFactoryView $view
     * @param WidgetsView $widgets
     * @param ButtonsView $buttons
     * @return string
     * @throws \Zend_Db_Statement_Exception|AdminException
     */
    private function makeRow(
        PageTemplate $template,
        $mods,
        TemplatesFactoryView $view,
        WidgetsView $widgets,
        ButtonsView $buttons
    ) {
        if( ! $template->isOverloaded() ) {
            throw new AdminException('The template is not overloaded');
        }

        $tpl_name = $template->getPlaceholder();
        $tpl_name_hash = md5( $tpl_name );
        $tpl = $template->getTemplate();

        switch( true ) {
            case $tpl instanceof ContentTemplate:
                $tpl_type = 'content';
                break;

            case $tpl instanceof ModuleTemplate:
                $tpl_type = 'module';
                break;

            default:
                throw new AdminException('Wrong module class' );
        }

        return $view->tableRow(
            $this->makeTitle( $buttons, $tpl_name ),
            $tpl_name_hash,
            $this->makeTypeSelect( $tpl_name_hash, $tpl_type ),
            $this->makeModuleSelect( $tpl_name_hash, $tpl->mod, $mods ),
            $widgets->formInput( $tpl_name_hash.'_var', $tpl->varValue ),
            $this->makeContentField( $widgets, $view, $tpl_name_hash, $tpl->id ),
            $widgets->formInput( $tpl_name_hash.'_act', $tpl->act ),
            $widgets->formYesNo( $tpl_name_hash.'_noadmin', intval( ! $template->canAdmin() ))
        );
    }

    /**
     * Выпадающий список типа поля
     * @param string $hash
     * @param string $value
     * @return string
     */
    private function makeTypeSelect( $hash, $value ) {
        $selectBuilder = new SelectBuilder();
        $selectBuilder->name = $hash.'_type';
        $selectBuilder->setValue( $value );
        $selectBuilder->onChange = "change_type_show('{$hash}', this.value )";
        $selectBuilder->setOptions([
            new SelectOptionBuilder('Модуль', 'module' ),
            new SelectOptionBuilder('Контент', 'content' ),
        ]);

        return $selectBuilder->build();
    }

    /**
     * Поля содержащее заголовок контента, его идентификатор и кнопку его редактирования
     * @param WidgetsView $widgets
     * @param TemplatesFactoryView $view
     * @param string $hash
     * @param string $value
     * @param int $id
     * @return string
     */
    private function makeContentField( WidgetsView $widgets, TemplatesFactoryView $view, $hash, $id ) {
        $title = $this->delegate->getTextTitle( $id );
        $content_field = $widgets->formInput(
            $hash.'_title',
            $title
        );

        $content_field .= $view->editContentImg( intval( $id ), $hash );
        $content_field .= $widgets->formInput(
            $hash.'_id',
            $id,
            'hidden'
        );

        return $content_field;
    }

    /**
     * @param ButtonsView $buttons
     * @param string $tpl_name
     * @return string
     */
    private function makeTitle( ButtonsView $buttons, $tpl_name ) {
        $title = $buttons->unload('[mod://]&i=skinUnload&x='.$this->page_id.'&tpl='.$tpl_name );
        $title .= '&nbsp;';
        $title .= ucwords( str_replace( '_', '&nbsp;', $tpl_name )) . '&nbsp;';

        return $title;
    }

    /**
     * Выпадающий список модулей для этого поля
     * @param string $hash
     * @param string $value
     * @param ModuleInfo[] $mods
     * @return string
     */
    private function makeModuleSelect( $hash, $value, $mods ) {
        $options = [];

        foreach( $mods as $mod ) {
            $options[] = new SelectOptionBuilder( $mod->title, $mod->name );
        }

        $selectBuilder = new SelectBuilder();
        $selectBuilder->name = $hash.'_mod';
        $selectBuilder->setValue( $value );
        $selectBuilder->setOptions( $options );

        return $selectBuilder->build();
    }
}