<?php

namespace Data\Module;

use Data\DataException;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'modules';
    protected $_primary = 'id';
    protected $_rowClass = 'Data\Module\Row';
}