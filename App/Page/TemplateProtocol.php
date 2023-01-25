<?php

namespace App\Page;

interface TemplateProtocol {
    function parseTemplate( array $r );
    function parseOverload( array $overload );
    function buildOverload();
}
