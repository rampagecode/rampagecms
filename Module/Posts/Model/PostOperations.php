<?php

namespace Module\Posts\Model;

use App\Page\ContentTemplate;
use App\Page\Page;
use App\Page\PageManager;
use Module\ModuleException;
use Sys\Database\DatabaseInterface;

class PostOperations {
    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @var PageManager
     */
    private $pm;

    /**
     * @var \Zend_Db_Table
     */
    private $moduleTable;

    /**
     * @param DatabaseInterface $db
     * @param \Zend_Db_Table $moduleTable
     */
    public function __construct( DatabaseInterface $db, PageManager $pm, \Zend_Db_Table $moduleTable) {
        $this->db = $db;
        $this->pm = $pm;
        $this->moduleTable = $moduleTable;
    }

    /**
     * @param PostFormDTO $data
     * @return void
     * @throws ModuleException
     * @throws \Zend_Db_Table_Exception
     */
    function updatePost( PostFormDTO $data ) {
        $postId = intval( $data->id );

        if( empty( $postId )) {
            throw new ModuleException('post id is not found');
        }

        $postRow = $this->moduleTable->find( $postId )->current();

        if( empty( $postRow )) {
            throw new ModuleException('post row is not found');
        }

        $textTable = new \Data\Text\Table();
        $textRow = $textTable->find( $postRow['text_id'] )->current();

        if( empty( $textRow )) {
            throw new ModuleException('text row is not found');
        }

        $pageTable = new \Data\Page\Table();
        $pageRow = $pageTable->find( $postRow['page_id'] )->current();

        if( empty( $pageRow )) {
            throw new ModuleException('page row is not found');
        }

        $date = implode( '-', [ $data->year, $data->month, $data->day ]);
        $text = $data->shortText .'<!--textcut-->'. $data->longText;

        $this->db->beginTransaction();
        try {
            $textRow->setFromArray([
                'title' => $data->title,
                'text_formatted' => $text,
                'text_searchable' => strip_tags( $text ),
            ])->save();

            $pageRow->setFromArray([
                'pagetitle' => $data->title,
                'longtitle' => $data->title
            ])->save();

            $postRow->setFromArray([
                'date' => $date,
            ])->save();

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param $parentPageId
     * @param $textId
     * @param $title
     * @param $layout
     * @param $placeholder
     * @return mixed
     */
    private function createPage( $parentPageId, $textId, $title, $layout, $placeholder ) {
        $contentTemplate = new ContentTemplate( $textId );
        $templates = [
            $placeholder => $contentTemplate->buildOverload()
        ];
        $values = [
            'parent' => $parentPageId,
            'in_module' => 1,
            'pagetitle' => $title,
            'longtitle' => $title,
            'alias' => $textId,
            'published' => 1,
            'isfolder' => 0,
            'layout' => $layout,
            'templates' => serialize( $templates ),
        ];

        $values['menu_pos'] = (int) $this->db->select()
            ->from(['st' => 'site_tree'], ['maxpos' => new \Zend_Db_Expr('COUNT(id)')])
            ->where('st.parent = '.$parentPageId )
            ->query()
            ->fetchColumn(0)
        ;

        $pageTable = new \Data\Page\Table();
        return $pageTable->insert( $values );
    }

    /**
     * @param PostFormDTO $data
     * @param $parentPageId
     * @param $layout
     * @param $placeholder
     * @param $textDirId
     * @return void
     * @throws \Exception
     */
    function createPost( PostFormDTO $data, $parentPageId, $layout, $placeholder, $textDirId ) {
        $date = implode( '-', [ $data->year, $data->month, $data->day ]);
        $text = $data->shortText .'<!--textcut-->'. $data->longText;

        $this->db->beginTransaction();
        try {
            $textTable = new \Data\Text\Table();
            $content_id = $textTable->create($data->title, $textDirId, $text);
            $page_id = $this->createPage($parentPageId, $content_id, $data->title, $layout, $placeholder);
            $textTable->addPage($content_id, $page_id);
            $this->moduleTable->createRow([
                'date' => $date,
                'text_id' => $content_id,
                'page_id' => $page_id,
            ])->save();

            $this->pm->updateCache( new \Data\Page\Table() );
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param $postId
     * @return PostFormDTO
     * @throws ModuleException
     * @throws \Zend_Db_Table_Exception
     */
    function loadPostData( $postId ) {
        if( empty( $postId )) {
            throw new ModuleException('post id is not found');
        }

        $postRow = $this->moduleTable->find( $postId )->current();

        if( empty( $postRow )) {
            throw new ModuleException('post row  is not found');
        }

        $textTable = new \Data\Text\Table();
        $textRow = $textTable->find( $postRow['text_id'] )->current();

        if( empty( $textRow )) {
            throw new ModuleException('text row  is not found');
        }

        list( $shortText, $longText ) = preg_split( "/<!--textcut-->/", $textRow['text_formatted'] );
        list( $year, $month, $day ) = explode( '-', $postRow['date'] );

        $data = new PostFormDTO();
        $data->id = $postRow['id'];
        $data->day = $day;
        $data->month = $month;
        $data->year	= $year;
        $data->title = $textRow['title'];
        $data->shortText = $shortText;
        $data->longText = $longText;

        return $data;
    }

    /**
     * @param int $postId
     * @return int number of deleted pages
     * @throws ModuleException
     * @throws \App\AppException
     * @throws \Data\DataException
     * @throws \Zend_Db_Table_Exception
     */
    function deletePost( $postId ) {
        $this->db->beginTransaction();

        if( empty( $postId )) {
            throw new ModuleException('Post id is not set');
        }

        $postRow = $this->moduleTable->find( $postId )->current();

        if( empty( $postRow )) {
            throw new ModuleException('Post row is not found');
        }

        $pageId = $postRow['page_id'];
        $page = Page::createById( $pageId, new \Data\Page\Table() );
        $deleted = $page->deletePage(true );

        $this->moduleTable->delete('id = '.$postId );
        $this->pm->updateCache( new \Data\Page\Table() );
        $this->db->commit();

        return $deleted;
    }
}