try { var actype = ca; } catch(e) { var actype = ''; }

SiteTreeClass = function(){
	
	/**
	 *
	 *
	 */
    SiteTreeClass.superclass.constructor.call(this, {        
        region: 'west',
        title: 'Дерево сайта',
        split: true,
        width: 400,
		height: '100%',
        minSize: 400,
        maxSize: 600,
        collapsible: true,
        rootVisible:true,
        lines: false,
        autoScroll: true,
		useArrows: false,
		rootVisible: true,
		enableDD: actype == 'link' ? false : true,
		
        root: new Ext.tree.AsyncTreeNode({
			text: '/',
			draggable: false,
			id: 'sitetree0'
			//expanded: false,
			//allowDrop: false
		}),
		
		loader: new Ext.tree.TreeLoader({
			dataUrl: global_base_url + '&u=x&a=sitetree&i=load'            
        }),
				
		tools:[{
			id:'refresh',
			qtip: 'Перезагрузить дерево',				
			handler: function(event, toolEl, panel){
				panel.getLoader().load( panel.getRootNode() );
				panel.getRootNode().expand();
			}
		}]
    });
	
	/**
	 *
	 *
	 */
	this.menu = new Ext.menu.Menu({
		id:'feeds-ctx',								
		items: [{
			style: 'text-align: left;',
			id: 'addpage',                    
			iconCls: 'addpage-icon',
			text: 'Создать страницу',					
			scope: this,
			handler: function() {
				this.ctxNode.select();
				mainWindow.pageInfo.setActiveTab(0);
				mainWindow.pageInfo.getActiveTab().fireEvent('activate', {
					nodeId: this.ctxNode.id.substr(8),
					action: 'newpage'
				});
			}							
		},{
			style: 'text-align: left;',
			id: 'add_simple_page',
			text: 'Добавить текстовую страницу',
			iconCls: 'addpage-icon',
			scope: this,
			handler: function() {
				this.ctxNode.select();
				mainWindow.pageInfo.setActiveTab(0);
				mainWindow.pageInfo.getActiveTab().fireEvent('activate', {
					nodeId: this.ctxNode.id.substr(8),
					action: 'newtextpage'
				});
			}
		},{
			style: 'text-align: left;',
			id: 'do_it_link',
			text: 'Сделать ссылкой на страницу..',
			iconCls: 'linkpage-icon',
			scope: this,
			handler:function()
			{
				this.ctxNode.select();
				mainWindow.pageInfo.setActiveTab(0);
				mainWindow.pageInfo.getActiveTab().fireEvent('activate', {
					nodeId: this.ctxNode.id.substr(8),
					action: 'newlink'
				});									
			}												
		},{
			style: 'text-align: left;',
			id: 'delpage',
			text: 'Удалить',			
			iconCls: 'delpage-icon',
			scope: this,
			handler: function() {
				/**
				 * Удаление страницы
				 */
				Ext.MessageBox.confirm('Удалить?', 'Эта и все принадлежащие ей страницы будут безвозвратно удалены.', function(btn){										
					if( btn == 'yes' ) {
						
						// Получаем таб "Параметры"
						var treePageParams = mainWindow.pageInfo.getItem('tree_page_params');
												
						// Устанавливаем его активным, при этом суспендим эвенты.
						// Если эвенты не суспендить, то будет вызвано действие
						// как при переключении табов - загрузка параметров
						// активной страницы. Нам этот лишний запрос совсем не нужен.
						
						treePageParams.suspendEvents();
						mainWindow.pageInfo.setActiveTab( treePageParams );
						treePageParams.resumeEvents();
						
						// Теперь выполянем запрос удаляющий странцу.
						
						treePageParams.fireEvent('activate', {
							nodeId: this.ctxNode.id.substr(8),
							action: 'delpage',
							scope: this,
							callback: function()
							{
								// Удаляем страницу визуально из дерева сайта.
								
								// @Todo: сделать проверку ответа сервера действительно
								// ли нод был удален.
								
								this.ctxNode.ui.removeClass('x-node-ctx');
								this.ctxNode.remove();
								this.ctxNode = null;
							}							
						});						
						
						// Устанавливаем красную подсветку строки символизирующую,
						// что нод удаляется.
						this.ctxNode.ui.addClass('siteTreeNodeDeleteing');
					}
				}, this );									
			}
		}]
	});
	
	/**
	 *
	 *
	 */
	this.on('click', function( node, event ) {
		
		var id = node.id.substr( 8 );
		
		try { var actype = ca; } catch(e) { var actype = ''; }
		
		if( actype == 'link' )
		{
			if( id ) {
				parent.document.getElementById('site_address').value = node.attributes._addr;
				parent.document.getElementById('site_title').value = node.attributes._name;
			} else {
				parent.document.getElementById('site_address').value = '/';
				parent.document.getElementById('site_title').value = 'Главная страница';				
			}
		} else {
			mainWindow.pageInfo.getActiveTab().fireEvent('activate', { nodeId: id });						
		}		
	});
	
	/**
	 *
	 *
	 */
			
	this.on('contextmenu', function( node, e ) {
		
		try { var actype = ca; } catch(x) { var actype = ''; }
		
		if( actype != 'link' )
		{					
			if(this.ctxNode) {
				this.ctxNode.ui.removeClass('x-node-ctx');
				this.ctxNode = null;
			}
			
			this.ctxNode = node;
			this.ctxNode.ui.addClass('x-node-ctx');
			this.menu.items.get('addpage').setDisabled( node.isLeaf() ? true : false );
			this.menu.items.get('add_simple_page').setDisabled( node.isLeaf() ? true : false );
			this.menu.items.get('delpage').setDisabled( node.attributes._not_deletable == 0 ? false : true );
			this.menu.showAt(e.getXY());
			//this.menu.showAt([0,0]);
			this.menu.items.get('delpage').setIconClass( node.isLeaf() ? 'delpage-icon' : 'delfolder-icon' );						
		}
	});
	
	/**
	 *
	 *
	 *
	 */
	this.on('movenode', function( tree, node, old_parent, new_parent, index ) {
		
		try { var actype = ca; } catch(e) { var actype = ''; }
		
		if( actype != 'link' )
		{
			Ext.Ajax.request({
				url: global_base_url + '&u=x&a=sitetree&i=xchg',            
				success: function( response, options ) {
					var r = Ext.decode( response.responseText );
					
					if( ! r.result ) alert( "Произошла ошибка!" + "\n\n" + r.error );
				},
				failure: function( response, options ) {
					alert( "Ошибка получения данных!" + "\n\nreadyState:" + response.readyState+"\nstatus: " + response.status );
				},
				params: {
					id: node.id,
					oldp: old_parent.id,
					newp: new_parent.id,
					pos: index
				},
				scope: this            
			});		
		}
	});
	
	/**
	 *
	 *
	 */
	this.on('render', function(tree) {
		tree.root.expand();
	});		
	
}

Ext.extend( SiteTreeClass, Ext.tree.TreePanel, {
	
});