<?php

namespace  Admin\Menu\ModuleSection;

use Admin\AdminException;
use App\AppInterface;
use App\Page\Page;
use App\Page\PageLayout;
use App\Page\PageParser;
use App\UI\FormBuilder;
use App\UI\FormSelectOptions;

class ModuleSettingsForm extends FormBuilder {
    public $items = [];
    public $id = 'ModuleSettingsForm';

    public function __construct() {
        parent::__construct('');

        $this->items = [
            'title' => new \Lib\FormItem\String('title', 'Название', true ),
            'description' => new \Lib\FormItem\String('description', 'Описание' ),
            'main_page_layout' => new \Lib\FormItem\String('main_page_layout', 'Макет основной страницы', true ),
            'item_page_layout' => new \Lib\FormItem\String('item_page_layout', 'Макет дочерней страницы', true ),
            'main_page_placeholder' => new \Lib\FormItem\String('main_page_placeholder', 'Место контента основной страницы', true ),
            'item_page_placeholder' => new \Lib\FormItem\String('item_page_placeholder', 'Место контента дочерней страницы', true ),
        ];
    }

    /**
     * @param AppInterface $app
     * @param array<string, mixed> $values
     * @return ModuleSettingsForm
     * @throws AdminException
     * @throws \App\AppException
     */
    function createRows( AppInterface $app, $values ) {
        if( isset( $this->items['title'] )) {
            $this->addInput( $this->items['title'], $values['title'] );
        }

        if( isset( $this->items['description'] )) {
            $this->addInput($this->items['description'], $values['description']);
        }

        $layoutOptions = $this->getLayoutSelectOptions( $app->rootDir( 'Public', 'Layouts' ));

        if( isset( $this->items['main_page_layout'] )) {
            $this->addSelect($this->items['main_page_layout'], $layoutOptions, $values['main_page_layout'])
                ->onChange = "document.getElementById('{$this->id}').submit();";
        }

        if( isset( $this->items['item_page_layout'] )) {
            $this->addSelect($this->items['item_page_layout'], $layoutOptions, $values['item_page_layout'])
                ->onChange = "document.getElementById('{$this->id}').submit();";
        }

        $page = new Page();
        $parser = new PageParser( $app, $page );

        foreach( $layoutOptions->getOptions() as $title => $name ) {
            if( empty( $name )) {
                continue;
            }

            $path = $app->rootDir( 'Public', 'Layouts', $name . '.html');

            if( $name == $values['main_page_layout'] && isset( $this->items['main_page_placeholder'] )) {
                $templateOptions = $this->getTemplateSelectOptions( $path, $parser );
                $this->addSelect( $this->items['main_page_placeholder'], $templateOptions, $values['main_page_placeholder'] );
            }

            if( $name == $values['item_page_layout'] && isset( $this->items['item_page_placeholder'] )) {
                $templateOptions = $this->getTemplateSelectOptions( $path, $parser );
                $this->addSelect( $this->items['item_page_placeholder'], $templateOptions, $values['item_page_placeholder'] );
            }
        }
        
        return $this;
    }

    /**
     * @param $path
     * @param PageParser $parser
     * @return FormSelectOptions
     * @throws \App\AppException
     */
    private function getTemplateSelectOptions( $path, PageParser $parser ) {
        $layout = new PageLayout( $path, $parser );
        $content = $layout->load();
        $templates = $parser->parseTemplates(null, $content, []);

        $options = new FormSelectOptions();
        $options->addOption('', '');

        foreach( $templates as $tpl ) {
            $options->addOption( $tpl->getPlaceholder(), $tpl->getPlaceholder() );
        }

        return $options;
    }

    /**
     * @param string $path
     * @return FormSelectOptions
     * @throws AdminException
     */
    private function getLayoutSelectOptions( $path ) {
        $options = new FormSelectOptions();
        $options->addOption('', '');

        $foundPublicLayout = false;
        $foundSystemLayout = false;

        if( $dir = @opendir( $path )) {
            while(( $file = readdir( $dir )) !== false ) {
                if( $file != '.' && $file != '..' && $file[0] != "." && is_file( $path . DIRECTORY_SEPARATOR . $file )) {
                    if( $file[0] == "_" ) {
                        $foundSystemLayout = true;
                    } else {
                        $foundPublicLayout = true;
                        $title = $value = str_replace( '.html', '', $file );
                        $title = ucwords( str_replace( '_', ' ', $title ));
                        $options->addOption( $title, $value );
                    }
                }
            }
        } else {
            throw new AdminException("Не удалось открыть директорию '{$path}'");
        }

        if( ! $foundPublicLayout ) {
            if( $foundSystemLayout ) {
                throw new AdminException("В директории '{$path}' не найдены публичные макеты страниц (у которых имя файла не начинается с подчеркивания)");
            } else {
                throw new AdminException("В директории '{$path}' не найдены макеты страниц");
            }
        }

        return $options;
    }
}