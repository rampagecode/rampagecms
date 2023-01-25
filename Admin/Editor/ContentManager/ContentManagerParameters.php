<?php

namespace Admin\Editor\ContentManager;

use Admin\Editor\TextManager\TextManagerParameters;
use Admin\Editor\ImageManager\ImageManagerParameters;
use Admin\Editor\DocumentManager\DocumentManagerParameters;
use App\AppInterface;
use App\Content\Folder;
use Data\Folder\Table;

abstract class ContentManagerParameters {
    /**
     * root directory
     * @var string
     */
    public $root;

    /**
     * current directory
     * @var string
     */
    public $folder;

    /**
     * sorting direction
     * @var string
     */
    public $sort_dir;

    /**
     * sorting field
     * @var string
     */
    public $sort_by;

    protected $db_root_key = null;
    protected $rte_folder_key = null;
    protected $rte_sort_dir_key = null;
    protected $rte_sort_by_key = null;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $in = $app->in();

        // Устанавливаем коревой каталог
        $this->root = $app->getVar( $this->db_root_key );

        // Узнаем текущий каталог
        if( $in['folder'] ) {
            $this->folder = intval( $in['folder'] );
        }
        elseif( $app->getCookie( $this->rte_folder_key )) {
            $this->folder = intval( $app->getCookie( $this->rte_folder_key ));
        } else {
            $this->folder = $this->root;
        }

        // Проверяем существует ли этот каталог
        $folderRow = (new Table())->findById( $this->folder );

        // Если каталог не существует используем корневой каталог этого раздела
        if( empty( $folderRow ))  {
            $this->folder = $this->root;
        }

        // Сортировка по направлению
        if( $in['sort_dir'] ) {
            $this->sort_dir = $in['sort_dir'];
        } else {
            $this->sort_dir = $app->getCookie( $this->rte_sort_dir_key );
        }

        if( $in['sort_by'] ) {
            $this->sort_by = $in['sort_by'];
        } else {
            $this->sort_by = $app->getCookie( $this->rte_sort_by_key );
        }

        // Если не задано, то по возрастанию
        if( ! in_array( $this->sort_dir, [ 'asc' , 'desc' ] )) {
            $this->sort_dir = 'asc';
        }

        // Сохраняем куку до конца сессии
        $app->setCookie( $this->rte_folder_key, $this->folder, false );

        // Сохраняем в куку на год
        $app->setCookie( $this->rte_sort_dir_key, $this->sort_dir, true );
        $app->setCookie( $this->rte_sort_by_key, $this->sort_by, true );

        if( $in['jsobj'] ) {
            $app->setCookie( 'rte_jsobj', $in['jsobj'], false );
        }
    }

    /**
     * @param AppInterface $app
     * @param int $id
     * @return ContentManagerParameters|null
     */
    static function createByFolder( AppInterface $app, $id ) {
        $folder = new Table();
        $root = $folder->findRoot( $id );

        switch( $root ) {
            case $app->getVar( 'db_text_mgr_root_id' ):
                return new TextManagerParameters( $app );

            case $app->getVar( 'db_imgs_mgr_root_id' ):
                return new ImageManagerParameters( $app );

            case $app->getVar('db_docs_mgr_root_id'):
                return new DocumentManagerParameters( $app );

            default:
                return null;
        }
    }
}