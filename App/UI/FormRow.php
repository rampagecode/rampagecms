<?php

namespace App\UI;

use Lib\FormItem;

class FormRow {
    /**
     * @var AbstractBuilder
     */
    public $builder;

    /**
     * @var FormItem
     */
    public $formItem;

    /**
     * @var string|null
     */
    public $description;

    public function __construct( AbstractBuilder $builder, FormItem $formItem, $description = null ) {
        $this->builder = $builder;
        $this->formItem = $formItem;
        $this->description = $description;
    }
}