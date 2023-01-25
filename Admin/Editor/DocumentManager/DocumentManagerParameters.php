<?php

namespace Admin\Editor\DocumentManager;

use Admin\Editor\ContentManager\ContentManagerParameters;
use App\AppInterface;
use Data\Folder\Table;

class DocumentManagerParameters extends ContentManagerParameters {
    protected $db_root_key = 'db_docs_mgr_root_id';
    protected $rte_folder_key = 'rte_doc_folder';
    protected $rte_sort_dir_key = 'rte_doc_sort_dir';
    protected $rte_sort_by_key = 'rte_doc_sort_by';

    public function __construct( AppInterface $app ) {
        parent::__construct( $app );

        // Если мы открываем окно по ссылке, то выделяем из ссылки идентификатор записи
        if( preg_match( "/\/([0-9]+)\/$/", $app->in('link'), $matches )) {
            // Получаем из базы ID каталога в котором находится данный документ
            $docTable = new \Data\Document\Table();

            try {
                $docRow = $docTable->findById($matches[1]);
                $this->folder = $docRow['vfs_folder'];
            } catch (\Zend_Db_Table_Exception $e) {

            }
        }
    }
}