<?php

namespace Admin\Editor\TextManager;

use Admin\Editor\ContentManager\ContentManagerParameters;
use App\AppInterface;

class TextManagerParameters extends ContentManagerParameters {
    protected $db_root_key = 'db_text_mgr_root_id';
    protected $rte_folder_key = 'rte_txt_folder';
    protected $rte_sort_dir_key = 'rte_txt_sort_dir';
    protected $rte_sort_by_key = 'rte_txt_sort_by';
}