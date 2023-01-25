PageInfoClass = function(){
	PageInfoClass.superclass.constructor.call(this, {        
		region: 'center',
		autoHeight: false,
		autoScroll: true,
		style: 'padding: 0; margin: 0;',
		activeTab: 0,
		height: '100%',		
		items: [{
			autoHeight: false,
			id: 'tree_page_params',			
			title: 'Параметры',						
			html: Ext.get('siteTreePageParamsHelp').dom.innerHTML,
			listeners: {
				activate: function( param ) {					
					//if( param.ctype != "Ext.Component" ) {
						mainWindow.pageInfo.loadTabPanelContent( param, 'params', this );
					//}
				}
			}			
		},{
			autoHeight: false,
			id: 'tree_page_templates',
			title: 'Модули и контент',
			html: Ext.get('siteTreePageTemplatesHelp').dom.innerHTML,			
			listeners: {
				activate: function( param ){
					mainWindow.pageInfo.loadTabPanelContent( param, 'skin', this );
				}
			}
		}]
	});
	
	this.on('render', function(e){

		Ext.get( this.el ).on('click', function(e){
			
			// Обрабатываем ссыли которые имеют аттрибут href и у которых класс не начинается на 'x-' как классы Ext
			var link   = e.getTarget('a[href]:not(a[class^=x-])');		
			var submit = e.getTarget('input[type=submit]');
			
			if( link ) {			
				var href = $(link).attr('href');
				
				//console.log( href );
																
				// if( /adsess=[a-z0-9]+/.test( href ) && ( e.button == 0 || e.button == 1 ))
				// {
					var url = href + '&ajaxLoad=1&nocache=' + Ext.id( null, 'nocache' );
					
					//console.log( url );
					
					mainWindow.pageInfo.getActiveTab().getUpdater().update( url );
					
					e.stopEvent();
					e.preventDefault();
					return false;
				// }
			} else if( submit ) {
				
				var form = Ext.get(submit).findParent( 'form', null, false );
				
				var opts = {
					url: global_base_url + '&u=x&a=tree2pcm',
					success: function( response, y, z )
					{
						mainWindow.pageInfo.getActiveTab().body.update( response, true );
						
						// Выделенный узел в дереве
						var selectedNode = mainWindow.viewport.items.first().getSelectionModel().getSelectedNode();
												
						var action 		= $(z[0].i).fieldValue()[0];
						var pageId		= parseInt( $(z[0].x).fieldValue()[0], 10 );
						var isFolder 	= parseInt( $(z[0].isfolder).fieldValue()[0], 10 );

						//console.log('isFolder', isFolder);
						//console.log('pageId', pageId);
						//console.log('action', action);
						//return;
												
						if( action == 'saveParams' && pageId == 0 ) {
							// Параметры изменены для еще не созданной страницы,
							// так, что никаких изменений к выделенному узлу не применяется.
							// Вместо этого создаем новый узел.
							
							var s = response.match(/<!--@@(.*?)@@-->/)[1];
							if( s ) {
								eval( 'var newNodeParams = '+s );							
								
								if( newNodeParams.success == true ) {
									var newNode = selectedNode.appendChild( new Ext.tree.TreeNode( newNodeParams ));
																		
									if( newNodeParams.leaf == false ) {
										newNode.getUI().removeClass('x-tree-node-leaf');
										newNode.getUI().addClass('x-tree-node-collapsed');
										newNode.leaf = false;										
									}
									newNode.select();
									
									mainWindow.pageInfo.findById('tree_page_templates').enable();
								}
							}
						} else {
							// Если был изменен статус директории, то вносим изменение в дерево
							
							if( selectedNode.isLeaf() && isFolder ) {
								//console.log('leaf -> folder');
								selectedNode.getUI().removeClass('x-tree-node-leaf');
								selectedNode.getUI().addClass('x-tree-node-collapsed');
								selectedNode.leaf = false;
							} else
							if( selectedNode.isLeaf() == false && isFolder == 0 ) {
								//console.log('folder -> leaf');
								if( selectedNode.isExpanded() ) {
									selectedNode.getUI().removeClass('x-tree-node-expanded');	
								} else {
									selectedNode.getUI().removeClass('x-tree-node-collapsed');	
								}														
								selectedNode.getUI().addClass('x-tree-node-leaf');
								selectedNode.leaf = true;							
							}
						}
					}
				}
				
				$(form).ajaxSubmit( opts );
				
				e.stopEvent();
				e.preventDefault();
				return false;			
			}			
			
		});
	});
}

Ext.extend( PageInfoClass, Ext.TabPanel, {
	
	/**
	 * Функция загружает контент в соответствующий 
	 * таб, в зависимости от активного нода дерева
	 */
	
	loadTabPanelContent: function( param, type, obj )
	{		
		try {
			var selectedNode = mainWindow.viewport.items.first().getSelectionModel().getSelectedNode();
			
			if( param.nodeId ) {
				var id = param.nodeId;
			} else {
				var id = selectedNode.id.substr( 8 );
			}
		}
		catch(e){						
			return;
		}
		
		var url = mainWindow.baseUrl+'&a=tree2pcm';
				
		if( type == 'params' )
		{					
			if( param.action == 'newpage' ) {
				url += '&i=new&pid=' + id;
				mainWindow.pageInfo.findById('tree_page_templates').disable();
			}
			else if( param.action == 'newtextpage' ) {
				url += '&i=newTextPage&pid=' + id;
				mainWindow.pageInfo.findById('tree_page_templates').disable();
			}
			else if( param.action == 'newlink' ) {
				url += '&i=newLink&pid=' + id;
				mainWindow.pageInfo.findById('tree_page_templates').enable();
			}
			else if( param.action == 'delpage' ) {
				url += '&i=deletePage&pid=' + id;
				mainWindow.pageInfo.findById('tree_page_templates').enable();
			}						
			else if( id != 0 ) {
				url += '&i=show&x=' + id;
				mainWindow.pageInfo.findById('tree_page_templates').enable();
			} else {
				url += '&i=new&pid=0';
			}
		}
		else if( type == 'skin' ) {
			url += '&i=skin&x=' + id;
		}
		else return;
		
		var conf = {
			url: url,							
			scope: this,
			discardUrl: false,
			nocache: true,
			text: "Загружается...",
			timeout: 30,
			scripts: true			
		};
		
		if( param.callback ) {
			conf.callback = param.callback;
		}
		
		if( param.scope) {
			conf.scope = param.scope;
		}		
		
		obj.load( conf );		
	}		
});
