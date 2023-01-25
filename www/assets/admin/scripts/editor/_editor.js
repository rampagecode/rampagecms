function StringBuilder( sString ) 
{
	this.append = function( sString ) 
	{
	    this.length += ( this._parts[ this._current++ ] = String( sString )).length;
	    this._string = null
	    return this;
  	}
  	
  	this.toString = function () 
  	{
    	if( this._string != null )
    	{
      		return this._string;
      	}
      	
    	var s = this._parts.join('');
    	
	    this._parts 	= [s]
	    this._current 	= 1
	    this.length 	= s.length
	    
    	return this._string = s
  	}
  	
  	this._current  = 0
  	this._parts    = []
  	this._string   = null
  	
  	if( sString != null )
  	{
    	this.append( sString )
    }
}

var image_action 	= null
var wp_debug_mode 	= false
var wp_inbase 		= false

// 222

var wp_donetext 	= null
var wp_ignore_next 	= false

// tag groups

var wp_special_body_events 				= /^(onload|onunload|onbeforeunload|onfocus|onblur)$/i;
var wp_inline_tags 						= /^(a|abbr|acronym|b|bdo|big|br|cite|code|dfn|em|font|i|img|kbd|label|q|s|samp|select|small|span|strike|strong|sub|sup|textarea|tt|u|var)$/i;
var wp_cant_have_children 				= /^(script|meta|link|input|br|hr|spacer|img|bgsound|embed|param|wbr|area|applet|object|basefont|base|style|title|comment|textarea|iframe)$/i;
var wp_cant_have_children_has_close_tag = /^(textarea|iframe|embed|object|applet)$/i;
var wp_boolean_attributes 				= /^(nowrap|ismap|declare|noshade|checked|disabled|readonly|multiple|selected|noresize|defer)$/i;
var wp_supported_blocks 				= /^(h1|h2|h3|h4|h5|h6|p|div|address|pre)$/i;
var wp_not_specified_ignore 			= /^(selected|coords|shape|type|value)$/i;
var wp_bogus_attributes 				= /^(_base_href|_moz_dirty|_moz_editor_bogus_node|_done)$/i;
var wp_attribute_allowed_empty 			= /^(alt|title|action|href|src|value)$/i;
var wp_attribute_case_must_lower 		= /^(align|valign|shape|type)$/i;
var wp_link_attributes 					= /^(src|href|action)$/i;

String.prototype.trim = function() 
{
   return this.replace(/^\s+|\s+$/g,"")
}

String.prototype.dblTrim = function() 
{
   return this.replace(/^\s{2,}|\s{2,}$/g," ")
}

//-----------------------------------------
// Hide errors
//-----------------------------------------
/*
window.onerror = function( evt ) 
{
	if( ! wp_debug_mode ) 
	{
		if( evt.stopPropagation ) 
		{
      		evt.stopPropagation();
      		evt.preventDefault();
    	}
    	return true;
  	}
}
*/
//-----------------------------------------
// Глобальная переменная содержит ссылку 
// на текущий объект, необоходима в 
// функциях типа setTimeout()
//-----------------------------------------

var oCurrentEditor = null;

//=========================================================================
//
//
//
//=========================================================================

function Editor( sName )
{
	if( ! sName ) return null;

	var baseWindow = window;

	while( typeof baseWindow.ModalDialog !== 'object' || baseWindow !== baseWindow.parent ) {
		baseWindow = baseWindow.parent;
	}

	this.ModalDialog = baseWindow.ModalDialog;
	
	oCurrentEditor = this;
	
	var conf = configuration[ sName ];
	
	this.id = sName;
	
	this.adsess_url		= conf['adsess_url'];
	this.images_url		= conf['images_url'];	
	this.is_ie50 		= conf['is_ie50'];	
	this.url_3xi_css	= conf['url_3xi_css'];
	this.url_3xi_img	= conf['url_3xi_img'];	
	
	// Strings:
	
	this.encoding 		= conf['encoding']
	this.xhtml_lang 	= conf['xhtml_lang']
	this.baseURLurl 	= conf['baseURLurl']
	this.baseURL 		= conf['baseURL']
	this.doctype 		= conf['doctype']
	this.charset 		= conf['charset']
	
	if( conf['domain1'] ) 
	{
		this.domain1 = conf['domain1']
		this.domain2 = conf['domain2']
	}
	
	this.instance_img_dir 	= conf['instance_img_dir']
	this.instance_doc_dir	= conf['instance_doc_dir']
	this.imagewindow 	 	= conf['imagewindow']
	this.links 			 	= conf['links']
	this.stylesheet 		= conf['stylesheet']
	this.styles 			= ''
	this.color_swatches   	= conf['color_swatches']
	
	// Lang
	
	this.lng = language
	
	// Integers
	
	this.imenu_height 	= conf['imenu_height']
	this.bmenu_height 	= conf['bmenu_height']
	this.smenu_height 	= conf['smenu_height']
	this.tmenu_height 	= conf['tmenu_height']
	this.border_visible = conf['border_visible']
	
	// Booleans
	
	this.usep = conf['usep']
	
	if( this.usep ) 
	{
		this.tdInners = '<p>&nbsp;</p>';
	} 
	else 
	{
		this.tdInners = '<div>&nbsp;</div>';
	}
		
	this.useXHTML 			= conf['useXHTML']
	this.snippit 			= true
	this.html_mode			= false
	this.safe 				= true
	this.preview_mode		= false
	this.initfocus 			= false				
	
	this.is_ie = document.all ? true: false
	
	// Undo/Redo settings

	this.undoLevels 		= new Array();
	this.undoIndex 			= 0;
	this.typingUndoIndex 	= -1;
	this.undoRedo 			= true;
	this.undoRedoLevel 		= true;
	this.undoBookmark 		= '';
	
	// Global vars
	
	this.curHyperlink	= null;
	this.curRow 		= null;
	this.curCell 		= null;
	this.curTable 		= null;

	// My clipboard

	this.clipboard		= '';	
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getEditorByName = function( name ) 
{
	var editors = document.getElementsByTagName( 'TEXTAREA' )
	
  	for( var i = 0; i < editors.length; i++ ) 
  	{
    	if( editors[i].className == "wpHtmlEditArea" && editors[i].id == name ) 
    	{
      		return editors[i];
    	}
  	}
}

/**
 *
 * @param code
 * @returns {*}
 */
Editor.prototype.replace_bookmark = function( code ) {
	code = code.replace(/<a name="([^"]+)[^>]+><\/a>/gi, "<img name=\"$1\" src=\"" + this.images_url + "bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">")
	code = code.replace(/<a name="([^"]+)[^>]+>&nbsp;<\/a>/gi, "<img name=\"$1\" src=\"" + this.images_url + "bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">")
	
	return code
}

/**
 *
 * @param code
 * @returns {*}
 */
Editor.prototype.replace_textcut = function( code ) {
	var image = '/assets/admin/images/editor/imgtextcut.png';
	var title = 'Текст расположенный ниже этой линии будет скрыт, а на его месте появится ссылка';
	var code = code.replace(/<!--textcut-->/g, '<input style="border-right: medium none; background-position: center 50%; border-top: medium none; background-image: url(' + image + '); border-left: medium none; width: 100%; border-bottom: medium none; background-repeat: repeat-x; height: 15px; background-color: transparent;" id="textcuttag" title="' + title + '" disabled="disabled" type="text" value="">' );
	return code;
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.show_borders = function() 
{
  	var tables = this.edit_object.document.getElementsByTagName('TABLE')
	
  	var l = tables.length
  	
  	for( var i = 0; i < l; i++ ) 
  	{
    	if( tables[i].border == 0 || tables[i].border == null ) 
    	{
      		if( this.is_ie ) 
      		{
        		tables[i].runtimeStyle.border = "1px dashed #7F7C75"
      		}
      		
      		var tableCells = tables[i].getElementsByTagName('TD')
			
      		var m = tableCells.length
      		
      		for( var j = 0; j < m; j++ ) 
      		{
        		if( this.is_ie ) 
        		{
          			tableCells[j].runtimeStyle.border = "1px dashed #7F7C75"
        		}
        		else
        		{
          			tableCells[j].style.border = "1px dashed #7F7C75"
        		}
      		}
    	}
  	}
  	
  	this.border_visible = 1
  	
  	var message = document.getElementById( this.id + '_messages' )
  	
  	if( message.innerHTML != this.lng['guidelines_visible'] ) 
  	{
    	message.innerHTML = this.lng['guidelines_visible']
  	}
  	
  	message.style.textDecoration = 'none'
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.hide_borders = function() 
{
	if( this.is_ie ) 
	{
    	var tables = this.edit_object.document.getElementsByTagName('TABLE')
    	
    	var l = tables.length
    	
    	for( var i=0; i < l; i++ ) 
    	{
			tables[i].runtimeStyle.borderLeft = ""
			tables[i].runtimeStyle.borderTop = ""
			tables[i].runtimeStyle.borderRight = ""
			tables[i].runtimeStyle.borderBottom = ""
			tables[i].runtimeStyle.border = ""
    	}
  	}
  	
	var tableCells = this.edit_object.document.getElementsByTagName('TD')
	
	var l = tableCells.length
	
  	for( var i=0; i < l; i++ ) 
  	{
    	if( this.is_ie ) 
    	{
      		var rcsstext = tableCells[i].runtimeStyle.cssText
    	} 
    	else 
    	{
      		var rcsstext = tableCells[i].style.cssText
    	}
    	
    	if( rcsstext.length > 1 ) 
    	{
			var propArray = rcsstext.split(';')
			var pl = propArray.length
			var icsstext = ''
			
      		for( var j = 0; j < pl; j++ ) 
      		{
        		if( propArray[j].length > 1 ) 
        		{
          			var propVal = propArray[j].split(':')
          			
          			if( propVal[1] != " 1px dashed rgb(127, 124, 117)"          			
          			&&  propVal[1] != " #7f7c75 1px dashed" ) 
          			{
			            icsstext += propVal[0] + ':'
			            icsstext += propVal[1] + ';'
					}
        		}
      		}
      		
      		if( this.is_ie ) 
      		{
        		tableCells[i].runtimeStyle.cssText = icsstext
      		}  
      		else 
      		{
        		tableCells[i].style.cssText = icsstext
      		}
    	}
  	}
  
  	this.border_visible = 0
  
  	var message = document.getElementById( this.id + '_messages' )
  
  	if( message.innerHTML != this.lng['guidelines_hidden'] ) 
  	{
    	message.innerHTML = this.lng['guidelines_hidden']
  	}
  	
  	message.style.textDecoration = 'none'
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.make_styles = function() 
{
	var styles = ''
	
  	if( this.stylesheet != '' ) 
  	{
    	var num = this.stylesheet.length;
    	
    	for( var i=0; i < num; i++ ) 
    	{
      		styles += '<link rel="stylesheet" href="'+this.stylesheet[i]+'" type="text/css">'
    	}
  	}
  	
  	var stylesheets = this.edit_object.document.getElementsByTagName('link')
	
  	var l = stylesheets.length
  	
  	for( var i = 0; i < l; i++ ) 
  	{
    	if( stylesheets[i].href ) 
    	{
      		if( stylesheets[i].rel ) 
      		{
        		if( stylesheets[i].rel.toLowerCase() == "stylesheet" ) 
        		{
          			styles += '<link rel="stylesheet" href="'+ stylesheets[i].href +'" type="text/css">'
        		}
      		} 
      		else if( stylesheets[i].type ) 
      		{
        		if( stylesheets[i].type.toLowerCase() == "text/css" ) 
        		{
          			styles += '<link rel="stylesheet" href="'+ stylesheets[i].href +'" type="text/css">'
        		}
      		}
    	}
	}
	
  	var styleTags = this.edit_object.document.getElementsByTagName('style')
	
  	var l = styleTags.length;
  	
  	for( var i = 0; i < l; i++ ) 
  	{
    	styles += '<style type="text/css">'+ styleTags[i].innerHTML +'</style>'
  	}
  	
  	return styles
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.font_hack = function( node ) 
{
	var spans = node.getElementsByTagName("SPAN")
	var n = spans.length
	var j = 0
	
	for( var i = 0; i < n; i++ ) 
	{
    	if( spans[j] )
    	{
      		if( spans[j].className || spans[j].style.cssText.length > 1 ) 
      		{
        		var newNode 	= this.edit_object.document.createElement("FONT")
        		var attributes 	= spans[j].attributes
        		
        		this.add_attributes( newNode, attributes, spans[j] )
        		
        		this.font_hack( spans[j] )
        		
        		try 
        		{
          			if( this.is_ie ) 
          			{
            			newNode.innerHTML = '<span>&nbsp;</span>'+spans[j].innerHTML;
          			} 
          			else 
          			{
            			newNode.innerHTML = '<span></span>'+spans[j].innerHTML;
          			}
          			
          			spans[j].parentNode.insertBefore( newNode, spans[j].nextSibling )
          			spans[j].parentNode.removeChild( spans[j] );
          			
          			if( this.is_ie ) 
          			{
            			newNode.removeChild( newNode.firstChild );
          			}
        		} 
        		catch(e) { j++ }
      		} 
      		else { j++ }
    	} 
    	else { j++ }
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.add_attributes = function( node, attributes, oldNode, ignore_unique ) 
{
	var l = attributes.length;
	
  	for( var j = 0; j < l; j++ ) 
  	{
    	if( attributes[j].nodeName == 'id' && ignore_unique ) 
    	{
      		continue;
    	} 
    	else if( attributes[j].specified 
    		 &&  attributes[j].nodeName != 'class' 
    		 &&  attributes[j].nodeName != 'style' ) 
    	{
      		node.setAttribute( attributes[j].nodeName, attributes[j].nodeValue, 0 )
    	} 
    	else if( attributes[j].nodeName=='class' ) 
    	{
      		node.className = attributes[j].nodeValue;
    	} 
    	else if( attributes[j].nodeName=='style' ) 
    	{
      		if( oldNode ) 
      		{
        		node.style.cssText = oldNode.style.cssText;
      		} 
      		else 
      		{
        		node.style.cssText = attributes[j].nodeValue
      		}
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.prepare_submission = function() 
{
	if( this.html_mode == false ) 
	{
    	this.send_to_html();
  	}
  	
  	var str = this.html_edit_area.value;		
  	
	if( str == '<p>&nbsp;</p>'
	||  str == '<div>&nbsp;</div>'
	||  str == '<div><br>\n</div>'
	||  str == '<div><br />\n</div>'
	||  str == '<p><br>\n</p>'
	||  str == '<p><br />\n</p>'
	||  str == '<br>\n'
	||  str == '<br />\n'
	||  str == '&nbsp;'
	||  str == '&nbsp;<br>\n'
	||  str == '&nbsp;<br />\n' ) 
	{
    	this.html_edit_area.value = '';
  	}
	
  	return true;
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.gethtml = function( node ) 
{	
	var sb = new StringBuilder();		
	
	wp_inbase 		= false
	wp_donetext 	= null
	wp_ignore_next 	= false
	
  	var cn = node.childNodes
  	var n  = cn.length
  	
  	for( var i = 0; i < n; i++ ) 
  	{
    	this.appendNodeHTML( cn[i], sb )				
  	}		
  	
  	// 222
  	
  	if( this.html_mode != true && this.is_ie ) 
  	{
    	this.remove_done( node )
  	}
  	
  	// end 222
  	
  	var doctype = '';
  	
  	if( this.useXHTML && ! this.snippit ) 
  	{
    	doctype =  '<'+'?xml version="1.0" encoding="' + this.encoding + '"?'+'>\n'+this.doctype+'\n'
  	} 
  	else if (!this.useXHTML && !this.snippit) 
  	{
    	doctype =  this.doctype+'\n'
  	}
  	
  	var code = doctype + sb.toString()
	
  	code = code.replace(/\s\n/gi, '\n');
	
  	return code.trim()
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.appendNodeHTML = function( node, sb ) 
{
	switch( node.nodeType ) 
	{
    	case 1:  // ELEMENT
			
      		if( node.nodeName == "!" ) 
      		{
        		if(( node.text.search( /DOCTYPE/gi ) != -1 ) 
        		|| ( node.text.search( /version=\"1.0\" encoding\=/gi ) != -1 )) 
        		{
          			sb.append('')
        		} else {
          			sb.append( node.text )
        		}
        		break
      		}
      		
      		var name = node.nodeName
      		name = name.toLowerCase()
      		
      		// wp 222
      		
      		if( ! name || name == '' ) 
      		{
        		// childNodes
        		
        		var cs = node.childNodes
        		
        		l = cs.length
        		
        		for( var i = 0; i < l; i++ ) 
        		{
          			this.appendNodeHTML( cs[i], sb )
        		}
        		break
      		}
      		
      		if( name == '/embed' ) 
      		{
      			return
      		}
      		
      		// end wp 222
      		
      	if( wp_inbase == true && name == 'body' ) 
      	{
        	break
      	}
      	
      	if( name == "base" || name=="basefont" ) 
      	{
        	if( this.is_ie ) 
        	{
          		wp_inbase = true
          		
          		// childNodes
          		
          		var cs = node.childNodes
          		l = cs.length
          		
          		for( var i = 0; i < l; i++ ) 
          		{
            		this.appendNodeHTML( cs[i], sb )
          		}
          		
          		wp_inbase = false
        	}
        	
        	if( name == "base" )
        	{
          		if( node.getAttribute('href') == this.baseURLurl ) 
          		{
            		break
          		}
        	}
      	}
      	
      	// 222 repition
      	
      	if( wp_ignore_next ) 
      	{
        	if( node.canHaveChildren && node.innerHTML == '' ) 
        	{
          		wp_ignore_next = false
          		break;
        	}
      	}
      	
      	if( this.is_ie ) 
      	{
        	if( node._done ) 
        	{
          		wp_ignore_next = true
          		break;
        	}
        	
        	node._done = true
      	}
      	
      	// end 222
      	
      	if( name == "link" && this.stylesheet != '' ) 
      	{
	        var num = this.stylesheet.length;
			
	        var dobreak = false;
	        
        	for( var i = 0; i < num; i++ ) 
        	{
          		if( node.getAttribute('href') == this.stylesheet[i] ) 
          		{
            		dobreak = true
            		break
          		}
        	}
        	
        	if( dobreak ) 
        	{
          		break
        	}
      	}
      	
      	if( name == "meta" ) 
      	{
        	if( node.getAttribute( 'name' )) 
        	{
          		if( node.getAttribute('name').toLowerCase() == "generator" && this.is_ie ) 
          		{
            		break
          		}
        	}
      	}
		
		if( name == "input" )
		{
			if( node.getAttribute('id') == 'textcuttag' )
			{
				sb.append( '<!--textcut-->' );
				break;
			}
		}      	
		
      	if( name == "img" ) 
      	{			
        	if(( node.getAttribute('name')) 
        	&& ( node.src.search( this.images_url + 'bookmark_symbol.gif' ) != -1 ))         	 
        	{								
          		sb.append( '<a name="' + node.getAttribute('name') + '"' )
          		
          		if( this.useXHTML ) 
          		{
            		sb.append( ' id="' + node.getAttribute('name') + '"></a>' )
          		} 
          		else 
          		{
            		sb.append('></a>')
          		}
          		break
        	}
        	
        	if( ! node.getAttribute( "alt" )) 
        	{
          		node.setAttribute("alt", "")
        	}			
      	}
      	
      	if( name == "area" ) 
      	{
        	if( ! node.getAttribute( "alt" )) 
        	{
          		node.setAttribute("alt", "")
        	}
      	}
      	
      	if( ! wp_inline_tags.test( name ) && name != "html" ) 
      	{
        	if( this.is_ie ) 
        	{
          		sb.append("\n")
        	} 
        	else if( name != 'tbody'
          		 &&  name != 'thead'
			     &&  name != 'tfoot'
			     &&  name != 'tr'
			     &&  name != 'td') 
			{
            	sb.append("\n")
        	}
      }	  	  
      
      // 222
      
      if( name == 'font' 
      &&  ! node.getAttribute('face') 
      &&  ! node.getAttribute('size') 
      &&  ! node.getAttribute('color')) 
      {
		  	name="span"
      }
      
      // end 222
      
      if((( name == "span" ) || ( name == "font" )) && ( node.style.cssText.length < 1 ))  
      {
		  	var attrs = node.attributes
          	var l = attrs.length
          
        	// IE5 fix: count specified attrs
        	
        	var n = 0
        	
        	for( var i = 0; i < l; i++ ) 
        	{
          		if( attrs[i].specified ) 
          		{
            		n ++
          		}
        	}
			
			n1 = this.is_ie ? 1 : 0;
        	
        	// 222 font removal fix
        	
        	if( n == n1 || ( n == ( n1 + 1 ) && node.className == "wp_none" ) || ( n == ( n1 + 1 ) && node.face == "null" )) 
        	{
          		if( node.hasChildNodes() ) 
          		{
            		// childNodes
            		
            		var cs = node.childNodes
            		l = cs.length
            		
            		for( var i = 0; i < l; i++ ) 
            		{
              			this.appendNodeHTML( cs[i], sb )
            		}
          		}
          		break
        	}
        	
        	// end 222
      	}
      	
      	sb.append("<" + name)
      	
      	if( name == "html" && this.useXHTML ) 
      	{
        	if( ! node.getAttribute( 'xmlns' )) 
        	{
          		sb.append(' xmlns="http://www.w3.org/1999/xhtml"')
        	}
        	
        	if( ! node.getAttribute('xml:lang')) 
        	{
          		sb.append(' xml:lang="' + this.xhtml_lang.toLowerCase() + '"')
        	}
        	
        	if( ! node.getAttribute('lang')) 
        	{
          		sb.append(' lang="' + this.xhtml_lang.toLowerCase() + '"')
        	}
      	}
      	
      	// inline styles
      	
      	if( node.style.cssText.length > 1 ) 
      	{
        	sb.append(' style="')
        	
        	var propArray = node.style.cssText.split(';')
        	var l = propArray.length
        	
        	for( var i = 0; i < l; i++ ) 
        	{
          		if( propArray[i].length > 1 ) 
          		{
            		var propVal = propArray[i].split(':')
            		
            		if( this.border_visible == 1 ) 
            		{
              			if( propVal[1] != " null"
			            &&  propVal[1] != " wp_bogus_font"
			            &&  propVal[1] != " 1px dashed rgb(127, 124, 117)"
			            &&  propVal[1] != " #7f7c75 1px dashed"
			            &&  propVal[0].substr(0,5) != " mso-"
			            &&  propVal[0].substr(0,4) != "mso-" ) 
			            {
                			sb.append( propVal[0].toLowerCase() + ':' )
								
                			sb.append( this.fixAttribute( propVal[1] ) + ';' )
              			}
            		} 
            		else 
            		{
              			if( propVal[1] != " null" && propVal[1] != " wp_bogus_font" ) 
              			{
			                sb.append( propVal[0].toLowerCase() + ':' )
			                sb.append( this.fixAttribute( propVal[1] ) + ';' )
              			}
            		}
          		}
        	}
        	
        	sb.append('"');
      	}
      	
      	// attributes
      	
      	var attrs = node.attributes
      	var l = attrs.length
      	
      	for( var i = 0; i < l; i++ ) 
      	{
        	this.getAttributeValue( attrs[i], node, sb )
      	}      			
		
      	// wp 222 iframe fix
      	
      	if( ! this.is_ie ) 
      	{
        	if( ! wp_cant_have_children.test( name )) 
        	{
          		node.canHaveChildren = true
        	} 
        	else 
        	{
          		node.canHaveChildren = false
        	}
      }
      
      if(( node.canHaveChildren || node.hasChildNodes() || wp_cant_have_children_has_close_tag.test( name )) 
      && name != 'basefont' 
      && name != 'base' 
      && name != 'script' 
      && name != 'style' 
      && name != 'title' ) 
      {
			sb.append(">")
			
        	if(( this.is_ie && node.innerHTML == '' || this.is_ie && node.innerHTML == ' ') && node.canHaveChildren ) 
        	{
        		// end 222
        		
          		sb.append('&nbsp;')
        	} 
        	else 
        	{
          		// fix for missing embed tag in IE
          		
          		if( name == 'object' && this.is_ie ) 
          		{
            		var cs = node.getElementsByTagName('PARAM');
            		
            		l = cs.length
            		
            		for( var i = 0; i < l; i++ ) 
            		{
              			this.appendNodeHTML( cs[i], sb )
            		}
            		
            		var f = this.edit_object.document.createElement('DIV');
            		
            		f.innerHTML = node.innerHTML;
            		
            		// childNodes
            		
            		var cs = f.childNodes
            		l = cs.length
            		
            		for( var i = 0; i < l; i++ ) 
            		{
              			this.appendNodeHTML( cs[i], sb )
            		}
          		} 
          		else 
          		{
            		// childNodes
            		
            		var cs = node.childNodes
            		l = cs.length
            		
            		for( var i = 0; i < l; i++ ) 
            		{
              			this.appendNodeHTML( cs[i], sb )
            		}
            		
            		if(( name == 'body' ) || ( name == 'html' ) || ( name == 'head' )) 
            		{
                		sb.append("\n")
            		}
          		}
        	}
        	
        	sb.append("</" + name + ">")
      } 
      else if( name == "script" ) 
      {
      		sb.append(">" + node.text + "</" + name + ">")
      } 
      else if( name == "style" ) 
      {
        	sb.append(">" + node.innerHTML.trim() + "</" + name + ">")
      } 
      else if( name == "title" || name == "comment" ) 
      {
        	sb.append(">" + node.innerHTML + "</" + name + ">")
      } 
      else if( this.useXHTML ) 
      {
        	sb.append(" />")
      } 
      else 
      {
        	sb.append(">")
      }
      
      if( name == 'br' )
      {
        	sb.append("\n")
      }
      
      break
      
    case 3:  // TEXT
    
    	if( node.nodeValue ) 
    	{
        	// 222 repitition fix
        	
        	if( wp_donetext == node ) 
        	{
          		wp_donetext = null;
          		break
        	}
        	
        	if( this.is_ie ) 
        	{
          		wp_donetext = node
        	}
        	
        	// end 222
        	
        	if( node.nodeValue == '\n' ) 
        	{
        		break
        	}
        	
        	var str = node.nodeValue
        	sb.append( this.fixText( str.dblTrim() ))
      	}
      	break
      	
    case 4:
      	sb.append("<![CDA" + "TA[\n" + node.nodeValue + "\n]" + "]>")
      	break
      	
    case 8:
    	if( this.is_ie ) 
    	{
        	if(( node.text.search(/DOCTYPE/gi) != -1 ) || ( node.text.search(/version=\"1.0\" encoding\=/gi) != -1 )) 
        	{
          		sb.append('')
        	} else {
          		sb.append("<!--" + node.nodeValue + "-->")
        	}
      	} 
      	else 
      	{
        	if( node.nodeValue.substr(0, 4) == "[if " ) 
        	{
          		return
        	} 
        	else 
        	{
          		sb.append("<!--" + node.nodeValue + "-->")
        	}
      	}
      	break
      	
    case 9:  // DOCUMENT
    	// childNodes
    	
      	var cs = node.childNodes
      	
      	l = cs.length
      	
      	for( var i = 0; i < l; i++ ) 
      	{
        	this.appendNodeHTML( cs[i], sb )
      	}
      	break
      	
    case 10:
    	sb.append('')
      	break
      	
    default:
    	if( wp_debug_mode ) 
    	{
        	sb.append("<!--\nUnsupported Node:\n\n" + "nodeType: " + node.nodeType + "\nnodeName: " + node.nodeName + "\n-->")
      	}
  	}		
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.fixAttribute = function( value ) 
{
	return String(value).replace(/\&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;").replace(/\xA0/g, "&nbsp;");
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.fixText = function( text ) 
{
	return String(text).replace(/\&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\xA0/g, "&nbsp;");
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getAttributeValue = function( attrNode, elementNode, sb ) 
{
	var name = attrNode.nodeName.toLowerCase();
	
  	if( this.is_ie ) 
  	{
    	if(( name == 'selected' ) && ( attrNode.nodeValue != true ))
    	{
    		return
    	}
    	
    	if( elementNode.tagName == 'BODY' && wp_special_body_events.test( name )) 
    	{
      		value = eval( "elementNode." + name );
      		
      		if( value ) 
      		{
        		sb.append(' '+name+'="'+value+'"');
        		return;
      		}
    	}
    	
    	if( ! attrNode.specified 
    	&&( ! wp_not_specified_ignore.test( name ) || ( elementNode.nodeName == 'LI' && name == 'value' )))
    	{ 
    		return
    	}
  	}
  	
  	if( wp_bogus_attributes.test( name )) 
  	{
    	return
  	}
  	
  	if( wp_boolean_attributes.test( name )) 
  	{
    	var value = name
  	} 
  	else 
  	{
    	var value = attrNode.nodeValue
  	}
  	
  	if( value == "" && ( ! wp_attribute_allowed_empty.test( name ))) 
  	{
  		return
  	}
  	
  	if( name == "class" && value == "wp_none" ) 
  	{
  		return
  	}
  	
  	if( value == "null" ) 
	{
		return
	}
	
  	if( name != 'style' ) 
  	{
    	if( ! isNaN( value )) 
    	{
      		if( elementNode.nodeName == "IMG" || elementNode.nodeName == "TABLE" ) 
      		{
        		if( name == 'height' && elementNode.style.height ) 
        		{
          			var str = elementNode.style.height
          			value   = str.replace(/px/, '');
        		} 
        		else if( name == 'width' && elementNode.style.width ) 
        		{
          			var str = elementNode.style.width
          			
          			value = str.replace(/px/, '');
	        	}  
	        	else 
	        	{
	          		value = elementNode.getAttribute( name, 2 )
	        	}
	      	} 
	      	else 
	      	{
	        	value = elementNode.getAttribute( name, 2 )
	      	}
	    } 
	    else if( wp_attribute_case_must_lower.test( name )) 
	    {
			if( elementNode.getAttribute( name )) 
			{
	        	value = elementNode.getAttribute( name ).toLowerCase()
	      	} 
	      	else 
	      	{
	        	value = attrNode.nodeValue.toLowerCase();
	      	}
		} 
		else if( wp_link_attributes.test( name )) 
		{
			if( this.domain1 ) 
			{
	        	value = elementNode.getAttribute( name, 2 )
	      	}
	      	
	      	if( value == null ) 
	      	{
	      		return;
	      	}
	      	
	      	if( value.search('#') != -1 ) 
	      	{								
	        	var string = document.location.toString();
		        var string = string.split('#');
		        var secure = new RegExp( this.quoteMeta( string[0] ), 'gi' );
		        
	        	s = value.split('#');
	        	
	        	if( s[0].toLowerCase() == string[0].toLowerCase() ) 
	        	{
	          		s[0] = s[0].replace( secure, '' );
	        	}
	        	value = s[0] + '#' + unescape( s[1] );
	      	}                                         	
    	}
    	
    	sb.append( " " + name + "=\"" + this.fixAttribute( value ) + "\"" )
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.remove_done = function( node ) 
{
	var body = false
	
  	if( node.nodeName ) 
  	{
    	if( node.nodeName == 'BODY' ) 
    	{
      		body = true
    	}
  	}
  	
  	if( body ) 
  	{
    	node.removeAttribute("_done", 1);
		
    	node.innerHTML = node.innerHTML.replace(/ _done="true"/gi, '')
  	} 
  	else 
  	{
    	if( node._done ) 
    	{
      		node.removeAttribute( "_done", 1 );
    	}
    	
    	var cn = node.childNodes
    	var n  = cn.length
    	
    	for( var i = 0; i < n; i++ ) 
    	{
      		if( cn[i]._done ) 
      		{
        		cn[i].removeAttribute('_done', 1 );
      		}
			
      		this.remove_done( cn[i] );
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.quoteMeta = function( str ) 
{
	return str.replace(/\//gi, '\\/').replace(/\./gi, '\\.').replace(/\{/gi, '\\{').replace(/\?/gi, '\\?').replace(/\(/gi, '\\(').replace(/\)/gi, '\\)');			 
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.hide_menu = function() 
{
	document.getElementById( this.id + "_format_frame"  ).style.display = "none"
	document.getElementById( this.id + "_class_frame"	).style.display = "none"
	document.getElementById( this.id + "_font_frame"	).style.display = "none"
	document.getElementById( this.id + "_size_frame"	).style.display = "none"
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.reactivate = function() 
{
	var editor
	
	if( editor = this.getEditorByName( this.id )) 
	{
    	if( editor.edit_object ) 
    	{
      		if( editor.edit_object.document.body ) 
      		{
        		editor.edit_object.document.designMode = "on"
      		}
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getSel = function() 
{	
	if( this.is_ie )
	{
		return this.edit_object.document.selection;		
	} else {			
		return this.edit_object.getSelection();
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getRng = function() 
{	
	var sel = this.getSel();

	if( sel == null ) return null;

	if( this.is_ie )
	{
		return sel.createRange();
	}
	else
	{
		try
		{
			return sel.getRangeAt(0);
		}
		catch(e){}
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getParentElement = function( node, names, attrib_name, attrib_value ) 
{
	if( typeof( names ) == "undefined" ) 
	{
		if( node.nodeType == 1 ) return node;

		// Find parent node that is a element
		
		while(( node = node.parentNode ) != null && node.nodeType != 1 );

		return node;
	}

	if( node == null ) return null;

	var namesAr = names.toUpperCase().split(',');

	do 
	{
		for( var i = 0; i < namesAr.length; i++ ) 
		{
			if( node.nodeName == namesAr[i] || names == "*" ) 
			{
				if( typeof( attrib_name ) == "undefined" )
				{
					return node;
				}
				else if( node.getAttribute( attrib_name )) 
				{
					if( typeof( attrib_value ) == "undefined" ) 
					{
						if( node.getAttribute( attrib_name ) != "" )
						{
							return node;
						}
					} 
					else if( node.getAttribute( attrib_name ) == attrib_value )
					{
						return node;
					}
				}
			}
		}
	} 
	while(( node = node.parentNode ) != null );

	return null;
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.setCode = function( code ) 
{
	this.html_edit_area.value = code;
  	this.send_to_edit_object();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.docolor = function( Action, color ) 
{
	this.edit_object.focus();
	
	if( ! this.is_ie && color == '' ) 
	{
    	if( Action == 'hilitecolor' ) 
    	{
      		color = 'rgb(127, 124, 117)';
    	} else {
      		color = 'null';
    	}
  	}
	
	if(( this.is_ie ) && ( Action == 'hilitecolor' ))
	{
		Action = 'backcolor';
	}				
  	
	if( Action == 'hilitecolor' ) 
	{
	    this.edit_object.document.execCommand( 'usecss'		 , false, false );
	    this.edit_object.document.execCommand( 'hilitecolor' , false, color );
	    this.edit_object.document.execCommand( 'usecss'		 , false, true  );
  	} 
  	else 
  	{		
    	this.edit_object.document.execCommand( Action, false, color )
  	}
  
  	if( ! this.is_ie ) 
  	{
    	if( Action == 'forecolor' && color == 'null' ) 
    	{
      		var fonts = this.edit_object.document.getElementsByTagName("FONT");
      		
      		this.remove_attributes( fonts, 'color' );
	    } 
	    else if( Action == 'hilitecolor' && color == 'rgb(127, 124, 117)' ) 
	    {
			this.remove_highlight( this.edit_object.document.body );
	    }
	}
	
  	this.edit_object.focus();
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.tidyHTML = function( code )
{
	if( ! this.usep ) 
	{
		// convert all p to div
		
		code = code.replace(/<p/gi, "<div")
		code = code.replace(/<\/p>/gi, "</div>")
	}
	
	if( this.is_ie ) 
	{
		code = code.replace(/<([\w]+) class=([^ |>]+)([^>]+)/gi, "<$1$3")
	} 
	else 
	{
		code = code.replace(/ class="([^"]+)([^>]+)/gi, "$2")
	}
	
	code = code.replace(/<font[^>]+>/gi, "")
	code = code.replace(/<\/font>/gi, "")
	
	var del = new RegExp("<del[^>]+>(.+)<\/del>","gi");
	
	code = code.replace(del, "")
	code = code.replace(/<ins[^>]+>/gi, "")
	code = code.replace(/<\/ins>/gi, "")
	
	if( this.is_ie ) 
	{
		code = code.replace(/<([\w]+) style="([^"]+)"/gi, "<$1 ")
	} 
	else 
	{
		code = code.replace(/ style="([^"]+)"/gi, "")
	}
		
	// add table collapse statements back in: 
	// (uncomment and change the line below if you need to format tables in a specific way)
	
	// code = code.replace(/<table/gi, "<table style=\"border-collapse: collapse\" bordercolor=\"#000000\"")
	
	code = code.replace(/<span[^>]+>/gi, "")
	code = code.replace(/<\/span>/gi, "")
	
	code = code.replace(/<([\w]+) lang=([^ |>]+)([^>]+)/gi, "<$1$3")
	
	code = code.replace(/<xml[^>]+>/gi, "")
	code = code.replace(/<\xml[^>]+>/gi, "")
	code = code.replace(/<?xml[^>]+>/gi, "")
	
	code = code.replace(/<\?[^>]+>/gi, "")
	code = code.replace(/<\/?\w+:[^>]+>/gi, "")
	
	code = code.replace(/<p[^>]+><\/p>/gi,"")
		
	code = code.replace(/<div[^>]+><\/div>/gi,"")
	
	code = code.replace(/<\!--(.*?)-->/gmi,'')	
	
	/*
	var string = document.location.toString();
	var string = string.split('pastewin.php')
	
	var secure = new RegExp("href=\""+quoteMeta(string[0])+"secure\\.htm#","gi");
	
	code = code.replace( secure, 'href="#' )
	*/
	
	// MSWord OLE_LINK clean first
	
	code = code.replace( /<a name="?OLE_LINK[0-9]+"?><\/a>/gi, "" );
	
	code = code.replace( /<a name="([^"]+)[^>]+><\/a>/gi, "<img name=\"$1\" src=\"" + this.url_3xi_img + "editor/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">" )
	
	code = code.replace( /<a name=([^>]+)><\/a>/gi, "<img name=\"$1\" src=\"" + this.url_3xi_img + "editor/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">" )
	
	return code	
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.toggle_table_borders = function( srcElement ) 
{
	if( srcElement.className == "wpDisabled" ) 
	{
    	return;
  	}
  	
  	if( this.border_visible == 0 ) 
  	{
    	this.show_borders();
  	} 
  	else 
  	{
    	this.hide_borders();
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.keyHandler = function( evt ) 
{
	var keyCode = ( evt.which || evt.charCode || evt.keyCode )
	
	var posKeyCodes = new Array( 13, 45, 36, 35, 33, 34, 37, 38, 39, 40 );
	
	var posKey = false;
	
	for( var i = 0; i < posKeyCodes.length; i++ ) 
	{
		if( posKeyCodes[i] == keyCode ) 
		{
			posKey = true;
			break;
		}
	}	
	
	if( evt.type == 'keypress' )
	{		
		if( this.is_ie )
		{
			// IEah!
			
			//-----------------------------------------
			// Make the enter keypress use <div> 
			// instead of <p> as the default.
			//-----------------------------------------
			
			if( evt.keyCode == 13 ) 
			{		
				// ENTER		
				
				if(( this.html_mode == false ) 
				&& ( this.safe == true )
				&& ( this.usep == false )
				&& ( this.edit_object.document.selection.type != "Control" )) 
				{
					this.divReturn();
				}
			} 
			else if( evt.keyCode == 9 ) 
			{
				//-----------------------------------------
				// Make the tab key create tabs rather moving 
				// the focus away from the editor, which just 
				// pisses off people who are used to MS Word.				
				
				if(( this.html_mode == false ) && ( this.safe )) 
				{
					var sel = this.edit_object.document.selection.createRange() 
					sel.pasteHTML(' &nbsp;&nbsp;&nbsp; ')
					return false
				}
			} 
			else if( evt.keyCode == 39 || evt.keyCode == 37 ) 
			{
				//-----------------------------------------
				// Arrow key selection fix
				//-----------------------------------------
				
				this.set_button_states();
			}						
		}
		else
		{
			// MOZZY
			
			if( this.edit_object.getSelection ) 
			{
				if( ! evt.shiftKey ) 
				{
					if( keyCode == 13 ) 
					{
						this.divReturn( evt );
					} 
					else if( keyCode == 8 ) 
					{
						this.remove_first_br();
					}
				} 
				else if( keyCode == 39 || keyCode == 37 ) 
				{			
					this.set_button_states();
				}
			}
		}
	}
	else if( evt.type == 'keyup' )
	{
		if( ! posKey )
		{			
			this.startTyping();
		}
		
		if( posKey || evt.ctrlKey )
		{			
			this.endTyping();
		}
	}
	else if( evt.type == 'keydown' )
	{
		if( posKey || evt.ctrlKey )
		{
			this.undoBookmark = this.getBookmark();
		}
	}	
}

Editor.prototype.execCopy = function() {
	this.edit_object.document.execCommand('copy');
	this.addUndoLevel();
}

Editor.prototype.execCut = function() {
	this.edit_object.document.execCommand('cut');
	this.addUndoLevel();
}

Editor.prototype.execPaste = function() {
	this.edit_object.document.execCommand('paste');
	this.addUndoLevel();
}


