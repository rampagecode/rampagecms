<?php

namespace Admin\Menu\Members;

use App\UI\FormBuilder;
use App\UI\FormSelectOptions;
use Data\User\Row;
use Lib\FormItemsCollection;
use Sys\Input\InputInterface;

class FormCreate extends FormBuilder {

    /**
     * @var FormItemsCollection
     */
    private $items;

    public function __construct() {
        parent::__construct('');

        $this->items = new FormItemsCollection([
            new \Lib\FormItem\Login('login', 'Системный логин', true ),
            new \Lib\FormItem\UserName('name', 'Имя пользователя', true ),
            new \Lib\FormItem\Password('pass', 'Пароль', true ),
            new \Lib\FormItem\Email('email', 'E-mail', true ),
            new \Lib\FormItem\Int('group', 'Группа пользователя', true ),
        ]);
    }

    /**
     * @param $values
     * @param FormSelectOptions $options
     * @return $this
     */
    function createRows( $values, FormSelectOptions $options ) {
        $login = '';
        $name = '';
        $email = '';
        $group = '';

        extract($values );

        $this->addInput( $this->items->byName('login'), $login );
        $this->addInput( $this->items->byName('name'), $name );
        $this->addInput( $this->items->byName('pass'))->set('type', 'password');
        $this->addInput( $this->items->byName('email'), $email );
        $this->addSelect( $this->items->byName('group'), $options, $group );

        return $this;
    }

    /**
     * @param Row $row
     * @param FormSelectOptions $options
     * @return $this
     */
    function createFromRow( Row $row, FormSelectOptions $options ) {
        return $this->createRows([
            'login' => $row['login'],
            'name' => $row['dname'],
            'pass' => '',
            'email' => $row['email'],
            'group' => $row['mgroup'],
        ], $options );
    }

    /**
     * @param InputInterface $in
     * @param FormSelectOptions $options
     * @return $this
     */
    function createFromInput( InputInterface $in, FormSelectOptions $options ) {
        return $this->createRows([
            'login' => $in['login'],
            'name' => $in['name'],
            'pass' => '',
            'email' => $in['email'],
            'group' => $in['group'],
        ], $options );
    }

    /**
     * @return \Lib\FormItem[]
     */
    function getItemsArray() {
        return $this->items->toArray();
    }

    /**
     * @param $name
     * @return $this
     */
    function removeItemByName( $name ) {
        $this->items->remove( $name );
        return $this;
    }
}