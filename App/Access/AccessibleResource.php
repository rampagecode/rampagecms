<?php

namespace App\Access;

abstract class AccessibleResource {
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;
}
