<?php

namespace Module\Posts\Frontend;

class PostsView {
    function postList( $rows, $pagelinks, $topButton = '' ) { return <<<EOF
<div class="postList">
    {$rows}
    <div align="right">{$pagelinks}</div>
</div>
EOF;
    }

    function postListRow( $title, $text, $url ) { return <<<EOF
<div class="postRow">    
    <h2 class="postTitle"><a href="{$url}">{$title}</a></h2>
    <div class="postText">{$text}</div>        
</div>
EOF;
    }
}