<?php

namespace Admin\Action\PageContent\TemplatesFactory;

use Admin\Action\PageContent\PageContentFactory;
use Admin\ButtonsView;
use Admin\WidgetsView;
use App\Page\ContentTemplate;
use App\Page\ModuleTemplate;
use App\Page\PageTemplate;
use App\UI\TableBuilder;

class TemplatesTableFactory implements PageContentFactory  {

    /**
     * @var TemplatesFactoryDelegate
     */
    private $delegate;

    /**
     * @var int
     */
    private $page_id;

    /**
     * @var PageTemplate[]
     */
    private $templates;

    /**
     * @param int $page_id
     * @param PageTemplate[] $templates
     * @param TemplatesFactoryDelegate $delegate
     */
    public function __construct( $page_id, $templates, TemplatesFactoryDelegate $delegate ) {
        $this->delegate = $delegate;
        $this->page_id = $page_id;
        $this->templates = $templates;
    }

    /**
     * @return string
     */
    function make() {
        $widgets = new WidgetsView();
        $buttons = new ButtonsView();
        $view = new TemplatesFactoryView();
        $table = $this->makeTable( $this->templates, $widgets, $buttons, $view );

        return $table->build();
    }

    /**
     * Строим список шаблонов по-умолчанию
     * @param PageTemplate[] $templates
     * @param WidgetsView $widgets
     * @param ButtonsView $buttons
     * @return TableBuilder
     */
    private function makeTable( $templates, WidgetsView $widgets, ButtonsView $buttons, TemplatesFactoryView $view ) {
        $tb = new TableBuilder();
        $tb->firstRowWidth = '1%';
        $tb->secondRowWidth = '99%';

        foreach( $templates as $template ) {
            if( ! $template->isOverloaded() ) {
                $tb->addInput(
                    $this->makeRowContent( $template, $widgets, $view ),
                    $this->makeRowTitle( $template, $buttons )
                );
            }
        }

        if( $tb->rowCount() == 0 ) {
            $tb->addText('Пусто');
        }

        return $tb;
    }

    /**
     * @param PageTemplate $template
     * @param WidgetsView $widgets
     * @return string
     */
    private function makeRowContent( PageTemplate $template, WidgetsView $widgets, TemplatesFactoryView $view ) {
        $tpl = $template->getTemplate();
        $s = '';

        switch( true ) {
            case $tpl instanceof ContentTemplate:
                $s .= $widgets->formInput('', $this->delegate->getTextTitle( $tpl->id ));
                break;

            case $tpl instanceof ModuleTemplate:
                $s .= $view->mod_params_table( $tpl->mod, $tpl->varValue, $tpl->act );
                break;

            default:
                $s .= $widgets->error( 'Wrong module class' );
        }

        $s .= $view->controlAbilityRow( $template->canAdmin() );

        return $s;
    }

    /**
     * @param PageTemplate $template
     * @param ButtonsView $buttons
     * @return string
     */
    private function makeRowTitle( PageTemplate $template, ButtonsView $buttons ) {
        $tpl_name = $template->getPlaceholder();

        $t = '<div style="white-space: nowrap;">';
        $t .= $buttons->overload('[mod://]&i=skinOverload&x=' . $this->page_id . '&tpl=' . $tpl_name );
        $t .= '&nbsp;' . ucwords( str_replace('_', '&nbsp;', $tpl_name )) . '&nbsp;';
        $t .= '</div>';

        return $t;
    }
}