<?php

namespace App\UI;

class ToggleBuilder extends AbstractBuilder {

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $isOn;

    /**
     * @return string
     */
    function build() {
        $sharedProperties = [
            'type' => 'radio',
            'name' => $this->name,
            'class' => '',
        ];

        $yes = (new InputBuilder( $sharedProperties ))->set( 'value', '1' )->set( 'id', 'green' );
        $no = (new InputBuilder( $sharedProperties ))->set( 'value', '0' )->set( 'id', 'red' );

        if( $this->isOn ) {
            $yes->set( 'checked', 'checked' );
        } else {
            $no->set( 'checked', 'checked' );
        }

        return 'Да &nbsp; '.$yes->build().'&nbsp;&nbsp;&nbsp;Нет &nbsp; '.$no->build();
    }
}