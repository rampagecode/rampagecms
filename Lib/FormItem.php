<?php

namespace Lib;

use Sys\Input\InputInterface;
use Lib\LibraryException;

abstract class FormItem {
    public $name;
    public $title;
    public $required;
    public $validValues = [];
    public $invalidValues = [];
    public $rowId = null;

    /**
     * @param string $name
     * @param string $title
     * @param bool $required
     * @param array $validValues
     */
    public function __construct( $name, $title, $required = false, $validValues = [] ) {
        $this->name = $name;
        $this->title = $title;
        $this->required = $required;
        $this->validValues = $validValues;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract protected function processValue( $value );

    /**
     * @param InputInterface $input
     * @return mixed
     */
    function getRawValue( InputInterface $input ) {
        return $input[ $this->name ];
    }

    /**
     * @param InputInterface $input
     * @return mixed
     * @throws LibraryException
     */
    function process( InputInterface $input ) {
        $value = $this->getRawValue( $input );
        $value = $this->processValue( $value );

        if( empty( $value ) && ! empty( $this->required )) {
            throw new LibraryException( $this->validationError( $this->title ));
        }

        if( ! empty( $this->validValues )) {
            if( ! in_array( $value, $this->validValues )) {
                throw new LibraryException( $this->validationError( $this->title ));
            }
        }

        if( ! empty( $this->invalidValues )) {
            if( in_array( $value, $this->invalidValues )) {
                throw new LibraryException( $this->validationError( $this->title ));
            }
        }

        return $value;
    }

    /**
     * @param $title
     * @return string
     */
    function validationError( $title ) {
        return 'Вы должны правильно заполнить поле <b>' . $title . '</b>';
    }
}
