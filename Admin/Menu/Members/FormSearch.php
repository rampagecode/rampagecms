<?php

namespace Admin\Menu\Members;

use App\AppInterface;
use Lib\FormItem;
use Lib\FormProcessing;
use Sys\Database\DatabaseInterface;
use Sys\Input\InputInterface;

class FormSearch {
    /**
     * @var FormItem[]
     */
    public $fields = [];

    /**
     * @var InputInterface
     */
    private $in;

    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @var FormProcessing
     */
    private $form;

    /**
     * @var \Zend_Db_Select
     */
    private $query;

    /**
     * @var string[]
     */
    private $errors;

    /**
     * @var array
     */
    private $values;

    /**
     * @var bool
     */
    private $processingComplete = false;

    /**
     * @var bool
     */
    private $processingSucceed = false;

    /**
     * @param InputInterface $in
     * @param DatabaseInterface $db
     */
    public function __construct( InputInterface $in, DatabaseInterface $db ) {
        $this->in = $in;
        $this->db = $db;

        $rules = ['begin', 'is', 'contains', 'ends'];
        $this->fields = [
            new \Lib\FormItem\String('loginwhere', 'Логин-условие', false, $rules),
            new \Lib\FormItem\String('login', 'Логин' ),
            new \Lib\FormItem\String('dnamewhere', 'Никнейм-условие', false, $rules),
            new \Lib\FormItem\String('dname', 'Никнейм' ),
            new \Lib\FormItem\Int('memberid', 'ID пользователя'),
            new \Lib\FormItem\String( 'email', 'E-mail' ),
            new \Lib\FormItem\String( 'registered_first', 'Зарегистрирован c ...' ),
            new \Lib\FormItem\String( 'registered_last', 'Зарегистрирован до ...' ),
            new \Lib\FormItem\String( 'last_activity_first', 'Активность с ...' ),
            new \Lib\FormItem\String( 'last_activity_last', 'Активность до ...' ),
            new \Lib\FormItem\IntArray('mgroup', 'Принадлежит группе..' )
        ];
        $this->form = new FormProcessing( $this->fields );
        $this->query = $this->db->select()->from(['m' => 'members']);
    }

    /**
     * @param $date
     * @return false|int|null
     */
    private function dateStringToUnixTime( $date ) {
        list( $month, $day, $year ) = explode( '-', $date );

        if( @checkdate( (int)$month, (int)$day, (int)$year )) {
            return mktime( 0, 0, 0, $month, $day, $year );
        } else {
            return null;
        }
    }

    /**
     * @return array of errors
     */
    private function processDateFields() {
        $dateFields = ['registered_first', 'registered_last', 'last_activity_first', 'last_activity_last'];
        $errors = [];

        foreach( $dateFields as $field ) {
            if( ! empty( $this->values[ $field ] )) {
                $time = $this->dateStringToUnixTime( $this->values[ $field ] );

                if( ! $time ) {
                    $title = $this->form->getItemByName( $field )->title;
                    $errors[] = "<b>{$title}</b>: Дата должна быть в формате ММ-ДД-ГГГГ";
                } else {
                    $operator = null;

                    switch( $field ) {
                        case 'registered_first':
                        case 'last_activity_first':
                            $operator = '>=';
                            break;
                        case 'registered_last':
                        case 'last_activity_last':
                            $operator = '<=';
                            break;
                    }

                    if( $operator ) {
                        $this->query->where("{$field} {$operator} ?", $time );
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @return array of errors
     */
    private function processRuleFields() {
        $errors = [];
        $searchByRule = [
            'login' => 'loginwhere',
            'dname' => 'dnamewhere',
        ];

        foreach( $searchByRule as $field => $rule ) {
            if( ! empty( $this->values[ $field ] )) {
                $value = $this->values[ $field ];

                switch( $this->values[ $rule ] ) {
                    case 'begin':
                        $like = "{$value}%";
                        break;
                    case 'is':
                        $like = "{$value}";
                        break;
                    case 'contains':
                        $like = "%{$value}%";
                        break;
                    case 'ends':
                        $like = "%{$value}";
                        break;
                }

                if( empty( $like )) {
                    $title = $this->form->getItemByName( $rule )->title;
                    $errors[] = "<b>{$title}</b>: поле имеет неверное значение";
                } else {
                    $this->query->where(new \Zend_Db_Expr("LOWER({$field}) LIKE '{$like}'" ));
                }
            }
        }

        return $errors;
    }

    /**
     * @return bool
     */
    function process() {
        $this->form->process( $this->in );
        $this->values = $this->form->getValues();
        $this->errors = $this->form->getErrors();
        $this->errors = array_merge( $this->errors, $this->processDateFields());
        $this->errors = array_merge( $this->errors, $this->processRuleFields());

        if( ! empty( $this->values['email'] )) {
            $this->query->where("email LIKE '%{$this->values['email']}%'");
        }

        if( ! empty( $this->values['memberid'] )) {
            $this->query->where('id = ?', $this->values['memberid'] );
        }

        if( ! empty( $this->values['mgroup'] )) {
            $this->query->where('mgroup IN (?)', join(',', $this->values['mgroup'] ));
        }

        $this->processingComplete = true;
        $this->processingSucceed = !count( $this->errors );

        return $this->processingSucceed;
    }

    /**
     * @return bool
     */
    function isCompleted() {
        return $this->processingComplete;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function isSucceed() {
        if( ! $this->isCompleted() ) {
            throw new \Exception("Processing was not completed yes");
        }

        return $this->processingSucceed;
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    function getErrors() {
        if( ! $this->isCompleted() ) {
            throw new \Exception("Processing was not completed yes");
        }

        return $this->errors;
    }

    /**
     * @return \Zend_Db_Select
     * @throws \Exception
     */
    function getQuery() {
        if( ! $this->isCompleted() ) {
            throw new \Exception("Processing was not completed yes");
        }

        return clone $this->query;
    }

    /**
     * @return array
     * @throws \Exception
     */
    function getValues() {
        if( ! $this->isCompleted() ) {
            throw new \Exception("Processing was not completed yes");
        }

        return $this->values;
    }
}