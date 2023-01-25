<?php

namespace App\UI;

use App\AppManager;
use Sys\Language\LanguageManager;

class Paginator {
    /**
     * Количество страниц которое показывать по сторонам от текущей, например: ... 4 5 [6] 7 8 ...
     * @var int
     */
    public $leaveOut = 2;

    /**
     * @var string
     */
    public $singlePage = 'Одна страница';

    /**
     * @var string
     */
    public $resultHTML;

    /**
     * @var int
     */
    public $currentPage;

    /**
     * @var LanguageManager
     */
    private $lang;

    private $offset;
    private $total;
    private $perPage;
    private $baseURL;

    private $start;
    private $start_dots;
    private $previous_link;
    private $pages;
    private $next_link;
    private $end_dots;

    /**
     * @param int $offset
     * @param int $total
     * @param int $perPage
     * @return void
     */
    public function __construct( $offset, $total, $perPage, $baseURL ) {
        $this->lang = AppManager::getInstance()->language();
        $this->offset = $offset;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->baseURL = $baseURL;

        $maxOffset = max( $perPage, $total - $perPage);

        if( $this->offset == -1 || $this->offset > $maxOffset ) {
            $this->offset = $maxOffset;
        }

        if( ! $this->offset || $this->offset < -1 ) {
            $this->offset = 0;
        }

        if( $total > 0 ) {
            $pages = ceil( $total / $perPage );
        }

        $pages = isset( $pages ) ? $pages : 1;
        $this->currentPage = $this->offset > 0
            ? ( $this->offset / $perPage ) + 1
            : 1;

        if( $this->currentPage > 1 ) {
            $start = max(0, $this->offset - $perPage );
            $this->previous_link = $this->prevLink( "{$baseURL}$start" );
        }

        if( $this->currentPage < $pages ) {
            $start = $this->offset + $perPage;
            $this->next_link = $this->nextLink( "{$baseURL}$start" );
        }

        $this->pages = '';

        if( $pages > 1 ) {
            $this->start = $this->jumpTo( $pages );

            for( $i = 0; $i <= $pages - 1; ++$i ) {
                $currentOffset = $i * $perPage;
                $pageNumber = $i + 1;

                if( $currentOffset == $this->offset ) {
                    $this->pages .= $this->pagination_current_page( $pageNumber );
                } else {
                    if( $pageNumber < ( $this->currentPage - $this->leaveOut )) {
                        $this->start_dots = $this->pagination_start_dots( $baseURL );
                        continue;
                    }

                    // If the next page is out of our section range, add some dotty dots!

                    if( $pageNumber > ( $this->currentPage + $this->leaveOut )) {
                        $this->end_dots = $this->pagination_end_dots( $baseURL . ( $pages - 1 ) * $perPage );
                        break;
                    }


                    $this->pages .= $this->pagination_page_link( $baseURL . $currentOffset, $pageNumber );
                }
            }

            $this->resultHTML = $this->pagination_compile();
        } else {
            $this->resultHTML = $this->singlePage;
        }
    }

    /**
     * @param string $url
     * @return string
     */
    private function prevLink( $url ) {
        return ' ' . TagBuilder::make('span',
            TagBuilder::make('a', '&lt;' )
                ->set('href', $url )
                ->set('title', $this->lang['tpl_prev'] )
                ->build()
        )->set('class', 'pagelink' )
            ->build();
    }

    /**
     * @param string $url
     * @return string
     */
    private function nextLink( $url ) {
        return ' ' . TagBuilder::make('span',
            TagBuilder::make('a', '&gt;' )
                ->set('href', $url )
                ->set('title', $this->lang['tpl_next'] )
                ->build()
        )->set('class', 'pagelink' )
            ->build();
    }

    /**
     * @param int $pages
     * @return string
     */
    private function jumpTo( $pages ) {
        return TagBuilder::make('span',
            $this->lang['tpl_pages']
            . " {$pages} "
            . TagBuilder::make('img' )
                ->set('src', '[img://]menu_action_down.gif' )
                ->set('alt', 'V' )
                ->set('title', $this->lang['global_open_menu'] )
                ->set('border', 0)
                ->build()
        )->set('class', 'pagelink' )
            ->set('id', 'page-jump')
            ->set('role', 'button')
            ->set('data-bs-toggle', 'dropdown')
            ->set('aria-expanded', 'false')
            ->build()
            . '&nbsp;'
        ;
    }

    /**
     * @param int $page
     * @return string
     */
    function pagination_current_page( $page ) {
        return '&nbsp;'.TagBuilder::make('span', $page )
            ->set( 'class', 'pagecurrent' )
            ->build()
        ;
    }


    /**
     * На первую страницу
     * @param string $url
     * @return string
     */
    function pagination_start_dots( $url ) {
        return TagBuilder::make('span',
            TagBuilder::make('a', '&laquo;' )
                ->set('href', $url )
                ->set('title', $this->lang['tpl_gotofirst'] )
                ->build()
        )->set('class', 'pagelinklast')
            ->build()
        ;
    }

    /**
     * На последнюю страницу..
     * @param string $url
     * @return string
     */
    function pagination_end_dots( $url ) {
        return '&nbsp;'.TagBuilder::make('span',
                TagBuilder::make('a', '&raquo;' )
                    ->set('href', $url )
                    ->set('title', $this->lang['tpl_gotolast'] )
                    ->build()
            )->set('class', 'pagelinklast')
                ->build()
            .'&nbsp;'
        ;
    }

    /**
     * @param string $url
     * @param int $page
     * @return string
     */
    function pagination_page_link( $url, $page ) {
        return '&nbsp;'.TagBuilder::make('span',
            TagBuilder::make('a', $page )
                ->set('href', $url )
                ->set('title', $page )
                ->build()
        )->set('class', 'pagelink')
            ->build()
        ;
    }

    /**
     * Собираем все элементы навигации вместе
     * @return string
     */
    function pagination_compile() {
        $inpId = uniqid();
        $input = InputBuilder::make('input')
            ->set('type', 'text')
            ->set('class', 'popup-input')
            ->set('size', 5)
            ->set('maxlength', 6)
            ->set('id', $inpId)
            ->build()
        ;
        $button = InputBuilder::make('input')
            ->set('type', 'button')
            ->set('class', 'popup-button')
            ->set('onclick', "window.location = '{$this->baseURL}'+$('#{$inpId}').val()")
            ->set('value', "Перейти")
            ->build()
        ;
        $ul = TagBuilder::make('ul', TagBuilder::make('li', $input.$button )->build())
            ->set('class', 'dropdown-menu pagelinkPopup')
            ->build()
        ;
        return $this->start
            .$this->start_dots
            .$this->previous_link
            .$this->pages
            .$this->next_link
            .$this->end_dots
            .$ul
        ;
    }
}