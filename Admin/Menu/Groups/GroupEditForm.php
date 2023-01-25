<?php

namespace Admin\Menu\Groups;

use App\Access\GroupAccess;
use App\AppException;
use App\UI\FormBuilder;
use App\User\UserGroup;
use Lib\FormItem;

class GroupEditForm extends FormBuilder {
    /**
     * @var FormItem[]
     */
    public $items = [];

    public function __construct() {
        parent::__construct('');

        $this->items = [
            'title' => new \Lib\FormItem\String('g_title', 'Название группы', true),
            'prefix' => new \Lib\FormItem\String('prefix', 'Префикс'),
            'suffix' => new \Lib\FormItem\String('suffix', 'Суффикс'),
        ];

        $groupAccess = new GroupAccess();
        $resources = $groupAccess->flattenResources();

        foreach( $resources as $r ) {
            $this->items[ $r->name ] = new \Lib\FormItem\Bool( 'g_access/'.$r->name, $r->title, false );
        }
    }

    /**
     * @param UserGroup $group
     * @return $this
     * @throws AppException
     */
    function createRows( UserGroup $group ) {
        $this->addInput( $this->items['title'], $group->getTitle() );
        $this->addInput( $this->items['prefix'], $group->getPrefix(), 'Текст или HTML-код который будет добавлен <b>перед</b> названием группы');
        $this->addInput( $this->items['suffix'], $group->getSuffix(), 'Текст или HTML-код который будет добавлен <b>после</b> названием группы');

        $groupAccess = new GroupAccess();
        $resources = $groupAccess->flattenResources();

        foreach( $resources as $r ) {
            $this->addToggle(
                $this->items[ $r->name ],
                $group->getAccess()->canAccess( $r->name ),
                join("<br/>", array_filter([ $r->description, "<i>{$r->name}</i>" ]))
            );
        }

        $this->makeSimple('');
        $this->addHidden('i', 'save');
        $this->addHidden('x', $group->id());

        return $this;
    }
}