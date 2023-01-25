<?php

namespace Admin\Action\PageContent\TemplatesFactory;

interface TemplatesFactoryDelegate {
    /**
     * @param int $id
     * @return string
     */
    function getTextTitle( $id );
}
