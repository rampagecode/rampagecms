<?php

namespace App\Task;

use App\AppInterface;
use Data\Task\Row;

interface TaskInterface {
    function name();
    function run( Row $task, AppInterface $app );
}

