<?php

namespace Admin\Action\SiteTree;

class SiteTreeView {
    function choosePageWindow() { return <<<EOF
<html>
<head>
	<title>Document Tree</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
	<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-2.2.1/resources/css/ext-all.css" />
	<link rel="stylesheet" type="text/css" href="[css://]siteTreeWindow.css" />
	
	<script type="text/javascript" src="[js://]jquery/jquery.js"></script>		
	<script type="text/javascript" src="http://extjs.cachefly.net/builds/ext-cdn-778.js"></script>				
	<script type="text/javascript" src="[js://]siteTree/siteTreeClass.js"></script>
	
    <script type="text/javascript">
		var global_base_url 		= "[base://]";				
		Ext.BLANK_IMAGE_URL 		= '[img://]s.gif';		
    </script>
</head>
<body>	
	<div id="siteTreeWindow"></div>
	<div id="siteTreePageParamsHelp" style="display: none;"></div>
	<div id="siteTreePageTemplatesHelp" style="display: none;"></div>
	
	<script type="text/javascript">
		var siteTree = new SiteTreeClass();
		siteTree.width  = '100%';
		siteTree.render('siteTreeWindow');
	</script>
</body>
</html>
EOF;
    }
}