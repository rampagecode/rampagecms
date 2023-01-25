mainWindow = {};

Ext.onReady(function(){
    Ext.QuickTips.init();
	
	function createBox(t, s){
        return ['<div class="msg">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
    } 		
	
	mainWindow.msgCt = false;
	
	mainWindow.msg = function(title, format)
	{
		if(!this.msgCt){
			this.msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
		}
		this.msgCt.alignTo(document, 't-t');		
		var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
		var m = Ext.DomHelper.append(this.msgCt, {html:createBox(title, s)}, true);
		m.slideIn('t').pause(5).ghost("t", {remove:true});
    }
	
	//--------------------------------------------
	// Создаем класс аналогичный Viewport,
	// за исключением того, что он рендерится
	// в указанный элемент, а не в document.body
	//--------------------------------------------
	
	var myViewport = Ext.extend( Ext.Container, {
		
		initComponent : function() {
			Ext.Viewport.superclass.initComponent.call(this);
			document.getElementById('siteTreeWindow').className += ' x-viewport';			
			this.el = Ext.get('siteTreeWindow');		
			this.el.setHeight = Ext.emptyFn;
			this.el.setWidth = Ext.emptyFn;
			this.el.setSize = Ext.emptyFn;
			this.el.dom.scroll = 'no';
			this.allowDomMove = false;
			this.autoWidth = true;
			this.autoHeight = true;
			Ext.EventManager.onWindowResize(this.fireResize, this);
			this.renderTo = this.el;
		},
	
		fireResize : function(w, h){
			this.fireEvent('resize', this, w, h, w, h);
		}
	});
	
	mainWindow.baseUrl 	= global_base_url+'&u=x';
	
	mainWindow.siteTree = new SiteTreeClass();
	mainWindow.pageInfo = new PageInfoClass();	
	mainWindow.viewport = new myViewport({
		style: 'background: #fff;',
        layout:'border',
        items:[
			mainWindow.siteTree,
			mainWindow.pageInfo
		]
    });
	
});