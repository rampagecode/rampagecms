<?php

namespace Admin\Editor\ModalBox;

use App\AppInterface;
use App\Module\ModuleControllerProtocol;

class ModalBoxController implements ModuleControllerProtocol {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    public function auto_run() {
        $q = preg_replace(
            "/a=(wrap)&real=([a-z_]+)(\.)?/",
            "a=\\2\\3",
            $_SERVER['QUERY_STRING']
        );

        $link = $this->app->baseURL('?'.$q.'&jsobj='.$this->app->in('jsobj' ));

        return $HTML = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="padding:0;margin:0;">
	<iframe src="{$link}" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
</body>
</html>
EOF;
    }
}