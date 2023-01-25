<?php

namespace App\UI;

use Lib\FormItem;

class FormBuilder extends TagBuilder {

    /**
     * @var string[]
     */
    private $hiddenFields = [];

    /**
     * @var FormRow[]
     */
    private $rows = [];

    public $name = '';
    public $action = '';
    public $method = 'post';
    public $style = '';
    public $enctype = '';
    public $id = '';
    public $onSubmit = '';

    public function __construct( $content ) {
        parent::__construct('form', $content );
    }

    function build() {
        if( strtolower( $this->method ) == 'get' ) {
            $queryParams = [];
            parse_str( parse_url( $this->action, PHP_URL_QUERY ), $queryParams );

            if( is_array( $queryParams )) {
                foreach( $queryParams as $name => $value ) {
                    $this->addHidden( $name, $value );
                }
            }
        }

        $hidden = implode("\n", $this->hiddenFields );
        $this->appendInnerHTML("\n{$hidden}" );

        return parent::build();
    }

    /**
     * @param string $name
     * @return FormBuilder
     */
    function makeSimple( $name ) {
        $this->name = $name;
        $this->style = 'marign:0; padding:0;';
        return $this;
    }

    /**
     * @return FormBuilder
     */
    function makeWYSIWYG() {
        $this->name = 'wysiwygEditorForm';
        $this->enctype = 'multipart/form-data';
        return $this;
    }

    /**
     * @param string $name
     * @return FormBuilder
     */
    function makeFile( $name ) {
        $this->name = $name;
        $this->enctype = 'multipart/form-data';
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return FormBuilder
     */
    function addHidden( $name, $value ) {
        $this->hiddenFields[] = '<input type="hidden" name="'.$name.'" value="'.$value.'">';
        return $this;
    }

    /**
     * @param FormItem $item
     * @param mixed $value
     * @param mixed $description
     * @return InputBuilder
     */
    function addInput( FormItem $item, $value = null, $description = null ) {
        $builder = new InputBuilder();
        $builder->name = $item->name;
        $builder->value = $value;

        $this->rows[] = new FormRow( $builder, $item, $description );

        return $builder;
    }

    /**
     * @param FormItem $item
     * @param FormSelectOptions $options
     * @param $value
     * @param $description
     * @return SelectBuilder
     */
    function addSelect( FormItem $item, FormSelectOptions $options, $value = null, $description = null ) {
        $selectOptions = [];
        foreach( $options->getOptions() as $optionTitle => $optionValue ) {
            $selectOptions[] = new SelectOptionBuilder( $optionTitle, $optionValue );
            $item->validValues[] = $optionValue;
        }

        $builder = (new SelectBuilder())
            ->setValue( $value )
            ->set( 'name', $item->name )
            ->setOptions( $selectOptions )
            ->set( 'size', 1 )
        ;

        $this->rows[] = new FormRow( $builder, $item, $description );

        return $builder;
    }

    /**
     * @param FormItem $item
     * @param FormSelectOptions $options
     * @param $value
     * @param $description
     * @return SelectBuilder
     */
    function addMultiSelect( FormItem $item, FormSelectOptions $options, $value = null, $description = null ) {
        $selectOptions = [];
        $items = $options->getOptions();
        foreach( $items as $optionTitle => $optionValue ) {
            $selectOptions[] = new SelectOptionBuilder( $optionTitle, $optionValue );
            $item->validValues[] = $optionValue;
        }

        $builder = (new SelectBuilder())
            ->setValue( $value )
            ->set( 'name', $item->name )
            ->setOptions( $selectOptions )
            ->set( 'size', count( $items ))
            ->set( 'multiple', 'multiple' )
        ;

        $this->rows[] = new FormRow( $builder, $item, $description );

        return $builder;
    }

    /**
     * @param FormItem $item
     * @param bool $isOn
     * @param string $description
     * @return ToggleBuilder
     */
    function addToggle( FormItem $item, $isOn, $description = null ) {
        $builder = new ToggleBuilder();
        $builder->name = $item->name;
        $builder->isOn = $isOn;

        $this->rows[] = new FormRow( $builder, $item, $description );

        return $builder;
    }

    /**
     * @param FormItem $item
     * @param $value
     * @param $description
     * @return TagBuilder
     */
    function addText( FormItem $item, $value = null, $description = null ) {
        $builder = new TagBuilder('span', $value );

        $this->rows[] = new FormRow( $builder, $item, $description );

        return $builder;
    }

    /**
     * @param TableBuilder $builder
     * @param string $submitTitle
     * @return string
     */
    function buildTable( TableBuilder $builder, $submitTitle ) {
        foreach( $this->rows as $row ) {
            $builder->addInput(
                $row->builder->build(),
                $row->formItem->title,
                $row->description,
                $row->formItem->rowId
            );
        }

        $builder->addSubmit( $submitTitle );
        $this->setInnerHTML( $builder->build() );

        return $this->build();
    }

    /**
     * @param string $name
     * @return void
     */
    function removeByFormItemName( $name ) {
        $this->rows = array_filter(
            $this->rows,
            function( $row ) use ( $name ) {
                return $row->formItem->name != $name;
            }
        );
    }

    /**
     * @param string $name
     * @return FormRow|void
     */
    function getRowByFormItemName( $name ) {
        foreach( $this->rows as $row ) {
            if( $row->formItem->name == $name ) {
                return $row;
            }
        }
    }

    /**
     * @param string $name
     * @return int|void
     */
    function getRowPositionByFormItemName( $name ) {
        foreach( $this->rows as $key => $row ) {
            if( $row->formItem->name == $name ) {
                return $key;
            }
        }
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @return $this
     */
    function changeRowPosition( $oldPosition, $newPosition ) {
        $rows = $this->rows;
        $movingRow = $rows[ $oldPosition ];
        unset( $rows[ $oldPosition ] );

        $newRows = [];
        foreach( $rows as $key => $row ) {
            if( $key == $newPosition ) {
                $newRows[] = $movingRow;
                $movingRow = null;
            }
            $newRows[] = $row;
        }

        if( $movingRow != null ) {
            $newRows[] = $movingRow;
        }

        $this->rows = $newRows;
        return $this;
    }
}
