<?php

namespace Admin\Action\PageContent;

class PageContentView {
    function window( $path, $content ) { return <<<EOF
    <div style="overflow: scroll; height: 100%;">
        <div style="width: 96%; margin-left: auto; margin-right: auto; margin-top: 10px; margin-bottom: 10px;">
            <table class="tab" cellspacing="0">
                <tr><td class="row0">Веб-путь к странице</td></tr>
                <tr><td class="row1"><input type="text" class="textinput" value="{$path}" /></td></tr>
            </table>
            <br />
            {$content}
        </div>
    </div>
EOF;
    }
}

