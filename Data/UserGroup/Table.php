<?php

namespace Data\UserGroup;

class Table extends \Zend_Db_Table_Abstract {
    protected $_name = 'groups';
    protected $_primary = 'g_id';
    protected $_rowClass = 'Data\UserGroup\Row';
}
