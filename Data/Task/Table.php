<?php

namespace Data\Task;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'task_manager';
    protected $_primary = 'task_id';
}
