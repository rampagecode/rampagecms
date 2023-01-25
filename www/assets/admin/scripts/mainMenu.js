Ext.onReady(function(){

	Ext.QuickTips.init();
	
	var menu   = Ext.get('ext_dd_tree');		
    var button = Ext.get('mm_dd');
	
	var editors = [];
	
	menu.hide();

	var tree_not_rendered = true;	

    button.on('click', function()
	{
		if( menu.isVisible() )
		{
			menu.hide();
			tree.hide();
		} else {
			
			if( tree_not_rendered )
			{
				tree.render();
				root.expand();
				tree_not_rendered = false;
			}
			
			menu.show();
			tree.show();
		}
    });
	
	button.hover( 	
		function ()
		{
			button.applyStyles({
				background: '#F7CC76',
				backgroundImage: 'url('+global_img_url+'dmenu/mm_active.gif)'			
			});
		},
		
		function ()
		{
			button.applyStyles({
				background: '#7FB1EE',
				backgroundImage: 'url('+global_img_url+'dmenu/mm_default.gif)'			
			});
		}
	);

	var tree = new Ext.tree.TreePanel({
		el:'ext_dd_tree',
		autoScroll:true,
		animate:true,
		enableDD:false,
		containerScroll: true,
		autoHeight: true,
		rootVisible: true,
		style: 'z-index: 9999;',
		loader: new Ext.tree.TreeLoader({
			dataUrl: global_site_menu_path+'&i=menu'								
		})
	});
	
	var store = new Ext.data.JsonStore({
        url: adsessGlobalURL,
		root: 'data',
		baseParams: {			
			u: 'a',
			s: 'content',
			a: 'page',
			i: 'ajax_get_content'			
		},        
        fields: [{ 
			name: 'id', 
			type: 'int'
		},{
			name: 'title',
			type: 'string'
		},{
			name: 'source',
			type: 'string'
		}]
    });
		
	store.on( 'load', function( store, rec, opt ) {
				
		var id = rec[0]['data']['id'];
		
		editors[id]['html'].setValue( rec[0]['data']['source'] );
		editors[id]['win'].setTitle( rec[0]['data']['title'] );
		editors[id]['win'].el.unmask();
	})
   
    /*
	tree.on( 'beforeclick', function( node, evt ) {
		
		if( node.attributes._one_text_page == true )
		{
			var id = node.attributes.id.substr( 8 );
			
			if( ! editors[id] )
			{
				editors[id] = [];
				
				editors[id]['html'] = new Ext.form.RichTextEditor({
					
					winID: 'WinRTE'+id
				});
				
				editors[id]['win']  = new Ext.Window({
					id: 'WinRTE'+id,
					title: 'Текстовый редактор',
					width: 550,
					height:310,
					minWidth: 550,
					minHeight: 310,
					layout: 'fit',
					resizable: true,
					bodyStyle: 'padding:5px;',								
					closeAction:'hide',
					items: editors[id]['html']
				});
			}
			
			store.baseParams.x = id;
			store.load();
			editors[id]['win'].show(this);
			
			editors[id]['win'].el.mask('Загружается...', 'x-mask-loading');			
			
			return false;
		}
	});
	*/
	
	// set the root node
	var root = new Ext.tree.AsyncTreeNode({
		text: 'Разделы сайта',
		draggable:false,
		id:'sitetree0'
	});
	
	tree.hide();
	tree.setRootNode(root);			
});
