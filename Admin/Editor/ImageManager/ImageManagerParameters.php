<?php

namespace Admin\Editor\ImageManager;

use Admin\Editor\ContentManager\ContentManagerParameters;

class ImageManagerParameters extends ContentManagerParameters {
    protected $db_root_key = 'db_imgs_mgr_root_id';
    protected $rte_folder_key = 'rte_img_folder';
    protected $rte_sort_dir_key = 'rte_img_sort_dir';
    protected $rte_sort_by_key = 'rte_img_sort_by';
}