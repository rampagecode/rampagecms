/**
 *
 */
Editor.prototype.init = function() {
	this.html_edit_area = this.getEditorByName( this.id );
	this.format_list	= document.getElementById( this.id + '_format_list');
	this.font_face		= document.getElementById( this.id + '_font_face'	);
	this.font_size 		= document.getElementById( this.id + '_font_size'	);
	this.class_menu		= document.getElementById( this.id + '_class_menu'	);
	this.foo 			= this.html_edit_area.value;
	this.format_frame 	= document.getElementById( this.id + "_format_frame").contentWindow;
	this.class_frame 	= document.getElementById( this.id + "_class_frame"	).contentWindow;
	this.font_frame 	= document.getElementById( this.id + "_font_frame"	).contentWindow;
	this.size_frame 	= document.getElementById( this.id + "_size_frame"	).contentWindow;

	try {
		this.format_frame.written 	= false;
		this.class_frame.written 	= false;
		this.font_frame.written	 	= false;
		this.size_frame.written 	= false;
	} catch (e) {
		console.log(e);
	}

	this.safe 			= true;
	this.edit_object 	= document.getElementById( this.id + '_editFrame').contentWindow;
	this.previewFrame 	= document.getElementById( this.id + "_previewFrame").contentWindow;
	
	// submit_form start
	var edForm = document.getElementsByName('wysiwygEditorForm')[0];
	
	if( edForm && edForm.tagName == 'FORM' ) {
		eval( "edForm.addEventListener( 'submit', " + this.id + "_submitHandler, false )" );
	} else {
		alert('Warning: Cannot submit the form');
	}
	// end	
	
	var str = this.html_edit_area.value;
	
	if( str.search( /<body/gi) != -1 ) {
		this.snippit = false;
		str = this.doctype;
		
		if( this.baseURL != '' ) {
			str += this.baseURL;
		}
		
		if( this.stylesheet != '' ) {
			var num = this.stylesheet.length;
			
			for( var i=0; i < num; i++ ) {
				str += '<link rel="stylesheet" href="' + this.stylesheet[i] + '" type="text/css">';
			}
		}
	} else {
		this.snippit = true;
		
		str = this.doctype + '<html><head><title></title>' + this.charset;
		
		if( this.baseURL != '' ) {
			str += this.baseURL;
		}
		
		if( this.stylesheet != '' ) {
			var num = this.stylesheet.length;
			
			for( var i=0; i < num; i++ ) {
				str += '<link rel="stylesheet" href="' + this.stylesheet[i] + '" type="text/css">';
			}
		}

		str += '</head><body></body></html>';
	}
	
	try {
		this.edit_object.document.open();
	} 
	catch(e) {
		this.edit_object.document.close();
		this.fail();
		return;
	}
	
	this.edit_object.document.write(str);
	this.edit_object.document.close();
	this.edit_object.stop();

	this.load_data();
}

/**
 *
 */
Editor.prototype.load_data = function() {
	if( this.edit_object.document.body ) {
		this.send_to_edit_object( true );
	} else {
		oCurrentEditor = this;
		
		setTimeout( "oCurrentEditor.load_data()", 100 );
	}			
}

/**
 *
 * @param init
 */
Editor.prototype.send_to_edit_object = function( init ) {
	this.html_edit_area.value = this.replace_bookmark( this.html_edit_area.value );
	this.html_edit_area.value = this.replace_textcut( this.html_edit_area.value );
	
	var str = this.html_edit_area.value;
	str = str.replace(/<strong>/gi, '<b>');
	str = str.replace(/<strong /gi, '<b ');
	str = str.replace(/<\/strong>/gi, '</b>');
	str = str.replace(/<em>/gi, '<i>');
	str = str.replace(/<em /gi, '<i ');
	str = str.replace(/<\/em>/gi, '</i>');
	str = str.replace(/<%([^%]+)%>/gi, "<!--asp$1-->");
	str = str.replace(/<\?php([^\?]+)\?>/gi, "<!--p$1-->");
	
	this.html_edit_area.value = str;
	
	if( this.html_edit_area.value.search( /<body/gi ) != -1 ) {
		this.snippit = false;
	} else {
		this.snippit = true;
	}
	
	if(( ! this.snippit ) && ( this.html_edit_area.value != '' )) {
		str = this.html_edit_area.value;

		var htmlseparator 	= new RegExp("<html[^>]*?>","gi");
		var bodyseparator 	= new RegExp("<body[^>]*?>","gi");
		var htmlsplit	= str.split( htmlseparator );
		var bodysplit	= str.split( bodyseparator );
		var headsplit 	= str.split( /<head>/gi	   );
		var head 	= '';
		var html 	= '';
		var bodyc 	= '';
		var arrsplit = str.split( /<body/gi );
		var bodytag = this.edit_object.document.getElementsByTagName('BODY');
		var attrs = bodytag[0].attributes;
		var l = attrs.length;
		
		for( var i = 0; i < l; i++ ) {
			bodytag[0].setAttribute( attrs[i].nodeName, '' );
		}
		
		if( arrsplit.length > 0 ) {
			var arrsplit2 = arrsplit[1].split(">");
			var attribute_array = arrsplit2[0].split('" ');
			var n = attribute_array.length;
			
			for( i=0; i < n; i++ ) {
				if( attribute_array[i].search("=") != -1 ) {
					var attribute 	= attribute_array[i].split("=");
					var elm 		= attribute[0].trim().replace(/"/gi,'');
					var val 		= attribute[1].trim().replace(/"/gi,'');
					
					bodytag[0].setAttribute( elm, val, 0 );
				}
			}
		}
		
		if( headsplit.length > 1 ) {
			var head2 = headsplit[1].split(/<\/head>/gi);
			head = head2[0];
		} 
		
		if( bodysplit.length > 1 ) {
			var body2 = bodysplit[1].split(/<\/body>/gi);
			bodyc = body2[0];
		} 
		
		this.edit_object.document.body.innerHTML = bodyc;

		var headtag = this.edit_object.document.getElementsByTagName('HEAD');
		var headcontent = this.baseURL;
		
		if( this.stylesheet != '' ) {
			var num = this.stylesheet.length;
			
			for( var i = 0; i < num; i++ ) {
				headcontent += '<link rel="stylesheet" href="' + this.stylesheet[i] + '" type="text/css">';
			}
		}
		
		headcontent += head;
		headtag[0].innerHTML = headcontent;
	} else {
		var headtag 	= this.edit_object.document.getElementsByTagName('HEAD');
		var headcontent = this.charset + this.baseURL;
		
		if( this.stylesheet != '' ) {
			var num = this.stylesheet.length;
			
			for( var i = 0; i < num; i++ ) {
				headcontent += '<link rel="stylesheet" href="' + this.stylesheet[i] + '" type="text/css">';
			}
		}
		
		headtag[0].innerHTML = headcontent;
		
		this.edit_object.document.body.innerHTML = this.html_edit_area.value;
	}
	
	if( init ) {
		// Add First undo level
		this.addUndo({ content : this.edit_object.document.body.innerHTML.trim() });

		// Add first bookmark location
		setTimeout( "oCurrentEditor.undoLevels[0].bookmark = oCurrentEditor.getBookmark()", 100 );				
	}

	if( this.border_visible == 1 ) {
		this.edit_object.document.onload = this.show_borders();
	} else {
		this.edit_object.document.onload = this.hide_borders();
	}
	
	this.styles = this.make_styles();
	
	try {
		this.format_frame.written = false
		this.class_frame.written = false
		this.font_frame.written = false
		this.size_frame.written = false
	} 
	catch(e) {
		console.log(e);
	}

	this.font_hack( this.edit_object.document.body );
	
	eval( "this.edit_object.document.addEventListener( 'contextmenu'	, " + this.id + "_contextHandler, true )" );
	eval( "this.edit_object.document.addEventListener( 'mouseup'		, " + this.id + "_mouseUpHandler, true )" );			
	eval( "this.edit_object.document.addEventListener( 'keypress'		, " + this.id + "_keyHandler, true )" );	
	eval( "this.edit_object.document.addEventListener( 'keyup'			, " + this.id + "_keyHandler, true )" );
	eval( "this.edit_object.document.addEventListener( 'keydown'		, " + this.id + "_keyHandler, true )" );
	
	this.edit_object.document.addEventListener( 'mousedown', this.closePopup, true );

	eval( "this.edit_object.document.addEventListener( 'copy', " 	+ this.id + "_beforeCopyHandler, true )" );
	eval( "this.edit_object.document.addEventListener( 'paste', " 	+ this.id + "_onPasteHandler, true )" );
	
	if( init ) {
		this.enable_designMode();
	}
	
	document.getElementById( this.id + '_load_message' ).style.display = 'none';
}

Editor.prototype.beforeCopyHandler = function( evt ) {
	evt.clipboardData.setData('text/plain', this.edit_object.document.getSelection().toString());
	evt.returnValue = false;
	evt.preventDefault();
}

Editor.prototype.onPasteHandler = function( evt ) {
	this.insert_code( evt.clipboardData.getData('text') )
	evt.returnValue = false;
	evt.preventDefault();
}

Editor.prototype.insert_code = function( code ) {
	if( code != null ) {
		this.edit_object.focus();

		if( this.edit_object.document.selection.type == "Control" ) {
			this.edit_object.document.execCommand('delete')
		}

		this.edit_object.document.selection.createRange().paste( code )
	}

	if( this.border_visible == 1 ) {
		this.show_borders();
	}

	this.edit_object.focus();
}

/**
 *
 */
Editor.prototype.enable_designMode = function() {
	try {
		this.edit_object.document.designMode = "on"
	} 
	catch(e) {
		console.log(e);
		return;
	}
	
	try {
		this.edit_object.document.execCommand( "usecss", false, true )
	} 
	catch(e) {
		console.log(e);
		return;
	}	
}

/**
 *
 */
Editor.prototype.fail = function() {
	document.getElementById( this.id + '_tab_one'	).style.display = "none";
	document.getElementById( this.id + '_tab_two'	).style.display = "block";
	document.getElementById( this.id + '_tab_three'	).style.display = "none";
	document.getElementById( this.id + '_tab_table'	).style.display = "none";
	
	this.html_edit_area.style.visibility = "visible";
	this.html_edit_area.value = this.foo;
	this.html_mode = true;
	
	document.getElementById( this.id + '_load_message' ).style.display ='none';	
}

/**
 *
 */
Editor.prototype.send_to_html = function() {
	var str1 = this.edit_object.document.body.innerHTML;
			
	str1 = str1.replace(/\&nbsp;/gi, '<!-- WP_SPACEHOLDER -->');
	str1 = str1.replace(/<<(.*?)>>/gi, "<$1>");
	str1 = str1.replace(/<\/<(.*?)>>/gi, "</$1>");
	str1 = str1.replace(/<>/gi, "");
	str1 = str1.replace(/<\/>/gi, "");
	
	this.edit_object.document.body.innerHTML = str1;
	
	if( this.html_edit_area.value.search( /<body/gi ) != -1 ) {
		this.snippit = false;
		this.html_edit_area.value = this.gethtml( this.edit_object.document );
	} else {
		this.snippit = true;
		this.html_edit_area.value = this.gethtml( this.edit_object.document.body );
	}
	
	var str = this.html_edit_area.value;
	
	RegExp.multiline = true;
	
	if( this.domain1 && this.domain2 ) {
		str = str.replace( this.domain1, '$1"' );
		str = str.replace( this.domain2, '$1"' );
	}		
	
	str = str.replace(/ type=\"_moz\"/gi, '');
	str = str.replace(/ style=\"\"/gi, "");
	str = str.replace(/<\!-- WP_SPACEHOLDER -->/gi, '&nbsp;');
	str = str.replace(/<b>/gi, '<strong>');
	str = str.replace(/<b /gi, '<strong ');
	str = str.replace(/<\/b>/gi, '</strong>');
	str = str.replace(/<i>/gi, '<em>');
	str = str.replace(/<i /gi, '<em ');
	str = str.replace(/<\/i>/gi, '</em>');
	str = str.replace(/<p><\/p>/gi, '');
	str = str.replace(/<div><\/div>/gi, '');
	str = str.replace(/([a-zA-Z0-9\.,:;\!])<br[^>]*?>\n<\/(p|div|h1|h2|h3|h4|h5|h6)>/gi, '$1</$2>');
	str = str.replace(/<(p|div|h1|h2|h3|h4|h5|h6)([^>]*?)><br[^>]*?>\n<\/(p|div|h1|h2|h3|h4|h5|h6)>/gi, '<$1$2>&nbsp;</$3>');	
	str = str.replace(/<divt \/>/gi, '');	
	
	this.html_edit_area.value = str;
}

/**
 *
 */
Editor.prototype.closePopup = function() {
	var editors = document.getElementsByTagName( "TEXTAREA" );
	
	for( var i = 0; i < editors.length; i++ ) {
		if( editors[i].className == "wpHtmlEditArea" ) {
			document.getElementById( editors[i].id + "_bookmarkMenu" ).style.display = 'none';
			document.getElementById( editors[i].id + "_imageMenu" 	 ).style.display = 'none';
			document.getElementById( editors[i].id + "_standardMenu" ).style.display = 'none';
			document.getElementById( editors[i].id + "_tableMenu"	 ).style.display = 'none';
			document.getElementById( editors[i].id + "_standardMenu" ).style.display = 'none';
		}
	}
}

Editor.prototype.mouseUpHandler = function( evt ) {
	this.endTyping();
	this.hide_menu();
	this.set_button_states();
	this.select_fix();		
}

/**
 * @returns {{container: Node, selection: Selection, offset: number, imageNode: ((function(): (ChildNode|null))|*)}}
 */
Editor.prototype.currentSelection = function() {
	// window.getSelection().removeAllRanges();

	// window.focus();
	// window.document.body.focus();
	// this.edit_object.focus();

	var selection	= this.edit_object.getSelection();
	var container 	= selection.focusNode;
	var offset 		= Math.max(0, selection.focusOffset - 1);

	return {
		selection: selection,
		container: container,
		offset: offset,
		imageNode: function () {
			if( container.childNodes && container.childNodes[offset] && container.childNodes[offset].nodeName ) {
				if( container.childNodes[offset].nodeName.toLowerCase() === 'img' ) {
					return container.childNodes[offset];
				}
			}

			return null;
		}
	}
}

//=========================================================================
// This changes the states of buttons everytime the selection changes, 
// so that buttons that cannot be used based on the current user selection 
// appear disabled.
//=========================================================================

Editor.prototype.set_button_states = function() {
	try {
		this.edit_object.document.queryCommandValue('FontName');
	} catch(e) {
		this.reactivate();		
	}
	
	this.initfocus = true;
	
	var imageSelected 	= false;
	var selection		= this.currentSelection();
	var container 		= selection.container;
	var canLink 		= true;
	var inside_link 	= this.isInside( 'A' );
	var imageNode 		= selection.imageNode();
	
	if( container.nodeType !== 1 && ! inside_link ) {
		canLink = false;
	}

	if( imageNode ) {
		imageSelected = true;
	}
	
	var inside_table = this.isInside( 'TD' );
	
	if( inside_table ) {
		this.curCell = container.parentNode;
		
		while( this.curCell.tagName != "TD" && this.curCell.tagName != "HTML" ) {
			this.curCell = this.curCell.parentNode;
		}
	}	
	
	// evaluate and set the toolbar button states
	var buttons = document.getElementById( this.id + "_tab_one" ).getElementsByTagName('BUTTON');

	for( var i = 0; i < buttons.length; i++ ) {
		var pbtn = buttons[i];
		var type = pbtn.getAttribute("type");
		
		if( type ) {
			var cmd = pbtn.getAttribute("cid");
			
			if( ! cmd ) {
				break;
			}
			
			if(( cmd == "edittable" ) || ( cmd == 'splitcell' )) {
				// table editing buttons
				
				if( inside_table ) {
					if( cmd == 'splitcell' ) {
						if(( this.curCell.rowSpan >= 2 ) || ( this.curCell.colSpan >= 2 )) {
							pbtn.className = "wpReady";
						} else {
							pbtn.className = "wpDisabled";
						}
					} else {
						pbtn.className = "wpReady";
					}
				} else {
					pbtn.className = "wpDisabled";
				}
			} 
			else if( cmd == "createlink" ) {
				if( canLink ) {
					pbtn.className = "wpReady";
				} else {
					pbtn.className = "wpDisabled";
				}
			} 
			else if(( cmd == "undo" ) ||  ( cmd == "redo" )) {
				pbtn.className = "wpReady";
			} else {
				try {
					if( this.edit_object.document.queryCommandState( cmd )) {
						pbtn.className = "wpLatched";
					} 
					else if( ! this.edit_object.document.queryCommandEnabled( cmd )) {
						pbtn.className = "wpDisabled";
					} else {	
						pbtn.className = "wpReady";
					}
				} catch(e) {
					pbtn.className = "wpReady";
				}
			}
		}
	}
	
	var font_face_value 	= this.edit_object.document.queryCommandValue('FontName');
	var font_size_value 	= this.edit_object.document.queryCommandValue('FontSize');
	var format_list_value 	= this.edit_object.document.queryCommandValue('FormatBlock');
	var class_menu_value 	= '';
	
	format_list_value = this.translate_format( format_list_value );
	
	if( ! imageSelected ) {
		var foo = container.parentNode;
		
		if( foo.tagName ) {
			while( ! foo.className && foo.tagName != "BODY" && foo.tagName != "HTML" && foo.tagName ) {
				foo = foo.parentNode;
			}

			class_menu_value = foo.className;
		}
	}
	
	this.set_list_text( 'font-face'	 , font_face_value	, 'font'   );
	this.set_list_text( 'font_size'	 , font_size_value	, 'size'   );
	this.set_list_text( 'format_list', format_list_value, 'format' );
	
	var class_menu_text = document.getElementById( this.id + '_class_menu-text' );
	
	if( class_menu_value ) {
		if( class_menu_value == "wp_none" ) {
			if( class_menu_text.innerHTML != this.lng['class'] ) {
				class_menu_text.innerHTML = this.lng['class'];
			}
		} 
		else if( class_menu_text.innerHTML != class_menu_value ) {
			class_menu_text.innerHTML = class_menu_value;
		}
	} else {
		if( class_menu_text.innerHTML != this.lng['class'] ) {
			class_menu_text.innerHTML = this.lng['class'];
		}
	}
}

/**
 *
 * @param tag
 * @returns {boolean}
 */
Editor.prototype.isInside = function( tag ) {
	var sel 	= this.edit_object.getSelection();
  	var range 	= sel.getRangeAt(0);
	var container = range.startContainer;
	
	if( container.nodeType != 1 ) {
    	container 	 = container.parentNode;
	}

	while( container.tagName != tag && container.tagName != "BODY" ) {
		container = container.parentNode;
	}

	return container.tagName == tag;
}

/**
 *
 * @param str
 * @returns {string}
 */
Editor.prototype.translate_format = function( str ) {
	if( wp_supported_blocks.test( str )) {
		str = str.replace(/h([0-9])/gi, "Heading $1");
		str = str.replace(/\bp\b/gi, "Normal");
		str = str.replace(/div/gi, "Normal");
		str = str.replace(/pre/gi, "Formatted");
		str = str.replace(/address/gi, "Address");
	} 
	else if( str == "x" ) {
		str = "Format";
	}
	
	return str;
}

/**
 *
 * @param list
 * @param value
 * @param lang
 */
Editor.prototype.set_list_text = function( list, value, lang ) {
	var list_text = document.getElementById( this.id + '_' + list + '-text' );
	
	if( value ) {
		if( list_text.innerHTML != value ) {
			list_text.innerHTML = value;
		}
	} else {
		if( list_text.innerHTML != this.lng[ lang ] ) {
			list_text.innerHTML = this.lng[ lang ];
		}
	}
}

/**
 * Moves cursor to beginning of tags that contain only &nbsp;
 */
Editor.prototype.select_fix = function() {
	var sel 			= this.edit_object.getSelection();
	var range 			= sel.getRangeAt(0);
	var startContainer 	= range.startContainer;
	var endContainer 	= range.endContainer;
	var startNode 		= startContainer.parentNode;
	var endNode 		= endContainer.parentNode;
	
	if( startNode == endNode ) {
		while( startNode.firstChild && wp_inline_tags.test( startNode.firstChild.nodeName )) {
			startNode = startNode.firstChild;
		}
		
		if( startNode.innerHTML == '&nbsp;' && startNode.firstChild && startNode.firstChild.nodeType == 3 ) {
			startNode = startNode.firstChild;
			
			var rngCaret = this.edit_object.document.createRange();
			
			rngCaret.setStart( startNode, 0 );
			rngCaret.collapse( true );
			
			sel = this.edit_object.getSelection();
			
			sel.removeAllRanges();
			sel.addRange( rngCaret );
		}
	}
}

//=========================================================================
//
// Non-br returns
//
//=========================================================================

Editor.prototype.divReturn = function( evt )
{
	if( this.isInside( 'LI' ))
	{
		return;
	}
	
	var sel 			= this.edit_object.getSelection()
	var range 			= sel.getRangeAt(0)
	var startContainer 	= range.startContainer
	var container 		= startContainer.parentNode
	
	// find the parent node	
	
	var parentTag 		= this.skipInline( container )
	var endContainer 	= range.endContainer
	var endNode1 		= endContainer.parentNode
	
	// determine the tag that the parent node replacement (before node) should be
	
	var beforeTag; 
	var afterTag; 
	var addAttributes = false; 
	var attributes; 
	var className; 
	var cssText;
	
	if( parentTag.tagName ) 
	{
		// if a supported block get attributes
		
		if( wp_supported_blocks.test( parentTag.tagName )) 
		{
			addAttributes = true
			attributes = parentTag.attributes
		}
		
		if( parentTag.tagName != 'P' && wp_supported_blocks.test( parentTag.tagName )) 
		{
			beforeTag = parentTag.tagName;
		} 
		else if( this.usep ) 
		{
			beforeTag = 'P'
		} 
		else if( ! wp_supported_blocks.test( parentTag.tagName )) 
		{
			beforeTag = 'DIV'
		} 
		else 
		{
			beforeTag = 'DIV'
			
			// replace with div tag then continue
			
			this.edit_object.document.execCommand("FormatBlock", false, "div")
		}
	} 
	else if( this.usep ) 
	{
		beforeTag = 'P'
	} 
	else 
	{
		beforeTag = 'DIV'
	}
	
	// determine the tag that the new after node should be (adjust this later)
	
	var afterTag = beforeTag
	
	// make sure we overwrite the selection
	
	if( container != endNode1 ) 
	{
		this.edit_object.document.execCommand('Delete', false, null)
	}
	
	// create and find ranges to cut
	
	var rngbefore 	= this.edit_object.document.createRange()		
	var rngafter 	= this.edit_object.document.createRange()
	
	rngbefore.setStart(	sel.anchorNode	, sel.anchorOffset );	
	rngafter.setStart(	sel.focusNode	, sel.focusOffset  );
	
	rngbefore.collapse(	true );					
	rngafter.collapse(	true );
	
	var direct = rngbefore.compareBoundaryPoints( rngbefore.START_TO_END, rngafter ) < 0;
	
	var startNode 	= direct ? sel.anchorNode 	: sel.focusNode;
	var startOffset = direct ? sel.anchorOffset : sel.focusOffset;
	var endNode 	= direct ? sel.focusNode 	: sel.anchorNode;
	var endOffset 	= direct ? sel.focusOffset 	: sel.anchorOffset;
	
	// find parent blocks
	
	var startBlock 	= this.skipInline( startNode );
	var endBlock 	= this.skipInline( endNode );
	
	// find start and end points
	
	var startCut = startNode;
	var endCut 	 = endNode;
	
	while(( startCut.previousSibling && startCut.previousSibling.nodeName != beforeTag ) 
	|| 	  ( startCut.parentNode && startCut.parentNode != startBlock && startCut.parentNode.nodeType != 9 )) 
	{
		startCut = startCut.previousSibling ? startCut.previousSibling : startCut.parentNode;
	}
	
	while(( endCut.nextSibling && endCut.nextSibling.nodeName != afterTag ) 
	|| 	  ( endCut.parentNode && endCut.parentNode != endBlock && endCut.parentNode.nodeType != 9 )) 
	{
		endCut = endCut.nextSibling ? endCut.nextSibling : endCut.parentNode;
	}
	
	// get the contents for each new tag
	
	rngbefore.setStartBefore( startCut );
	rngbefore.setEnd( startNode, startOffset );
	
	var beforeContents = rngbefore.cloneContents()
	
	rngafter.setEndAfter( endCut );
	rngafter.setStart( endNode, endOffset );
	
	var afterContents = rngafter.cloneContents()
	
	// test to see if after tag will be empty and if so change to p or div
			
	if( ! this.has_content( afterContents )) 
	{
		if( this.usep ) 
		{
			afterTag = 'p'
		} 
		else 
		{
			afterTag = 'div'
		}
	}		
	
	// create the new elements
	
	var newbefore 	= this.edit_object.document.createElement( beforeTag	);
	var newafter 	= this.edit_object.document.createElement( afterTag	);		
	
	// place content into the new tags
	
	newbefore.appendChild( beforeContents )
	newafter.appendChild(  afterContents  )
	
	// fill tags if empty
	
	this.fill_content( newbefore )
	this.fill_content( newafter  )		
	
	// add attributes
	
	if( addAttributes ) 
	{
		this.add_attributes( newbefore	, attributes )
		this.add_attributes( newafter	, attributes, false, true )
	}
	
	// make a range around everything
	
	var rngSurround = this.edit_object.document.createRange();
	
	if( ! startCut.previousSibling && startCut.parentNode.nodeName == beforeTag ) 
	{
		rngSurround.setStartBefore( startCut.parentNode );
	} 
	else 
	{
		rngSurround.setStart( rngbefore.startContainer, rngbefore.startOffset )
	}
	
	if( ! endCut.nextSibling && endCut.parentNode.nodeName == beforeTag ) 
	{
		rngSurround.setEndAfter( endCut.parentNode );
	} else {
		rngSurround.setEnd( rngafter.endContainer, rngafter.endOffset )
	}
	
	// delete old tag
	
	rngSurround.deleteContents();
	
	// insert the two new tags
	
	rngSurround.insertNode( newafter  )
	rngSurround.insertNode( newbefore )
	
	// scroll to the new cursor position
	
	var scrollTop 	= this.edit_object.document.body.scrollTop 	+ this.edit_object.document.documentElement.scrollTop
	var scrollLeft 	= this.edit_object.document.body.scrollLeft + this.edit_object.document.documentElement.scrollLeft
	
	var scrollBottom = document.getElementById( this.id + '_editFrame' ).style.height
	
	scrollBottom = scrollBottom.replace(/px/i, '')	
	
	var frameHeight = scrollBottom
	
	scrollBottom = scrollTop + parseInt( scrollBottom )
	
	var afterposition = this.getElementPosition( newafter )
	
	if( afterposition['top'] > scrollBottom - 25 ) 
	{
		this.edit_object.scrollTo( afterposition['left'], afterposition['top'] - parseInt( frameHeight ) + 25 )
	} 
	else 
	{
		this.edit_object.scrollBy( afterposition['left'] - scrollLeft, 0 )
	}
	
	// move the cursor
	
	while( newafter.firstChild && wp_inline_tags.test( newafter.firstChild.nodeName )) 
	{
		newafter = newafter.firstChild;
		
		if( newafter.tagName == 'A' ) 
		{
			var pnode 	= newafter.parentNode;
			var cn 		= newafter.childNodes;
			
			for( i=0; i < cn.length; i++ ) 
			{
				pnode.insertBefore( cn[i].cloneNode( true ), newafter );
			}
			
			pnode.removeChild( newafter );
			newafter = pnode;
		}
	}
	
	if( newafter.firstChild && newafter.firstChild.nodeType == 3 ) 
	{
		newafter = newafter.firstChild
	}
	
	var rngCaret = this.edit_object.document.createRange()
	
	rngCaret.setStart( newafter, 0 );
	rngCaret.collapse( true );
	
	sel = this.edit_object.getSelection()
	
	sel.removeAllRanges()
	sel.addRange( rngCaret )
	
	// stop browser default action
	
	evt.stopPropagation()
	evt.preventDefault()
}

//=========================================================================
//
// finds first block level tag surrounding a given node
//
//=========================================================================

Editor.prototype.skipInline = function( foo ) 
{
	while( foo.parentNode && ( foo.nodeType != 1 || wp_inline_tags.test( foo.tagName ))) 
	{
		foo = foo.parentNode;
	}
	
	return foo
}

//=========================================================================
// Returns true if a node contains text content, if node contains only empty 
// tags return false.
//
//=========================================================================

Editor.prototype.has_content = function( node ) 
{				
	if( node.firstChild ) 
	{
		var istChild = node.firstChild;
		var val;
		
		while( istChild ) 
		{						
			if( wp_cant_have_children.test( istChild.tagName )) 
			{
				return true;
			} 
			else if( istChild.nodeType == 1 && ! wp_inline_tags.test( istChild.nodeName )) 
			{
				return true;
			} 
			else if( istChild.nodeType == 3 && istChild.nodeValue.trim() != '' ) 
			{
				return true;
			} 
			else if(( val = this.has_content( istChild )) != false ) 
			{
				return val;
			}			
			
			istChild = istChild.nextSibling;
		}
	}	
	return false
}

//=========================================================================
//  
// Adds white space to a node with no text nodes
//
//=========================================================================

Editor.prototype.fill_content = function( node ) 
{
	if( ! this.has_content( node )) 
	{
		// node.innerHTML = node.innerHTML.trim()
		
		while( node.firstChild && node.firstChild.nodeType == 1 ) 
		{
			node = node.firstChild;
		}
		
		node.innerHTML = '&nbsp;'
	}
}

//=========================================================================
//
// Determins the absolute position of an element
//
//=========================================================================

Editor.prototype.getElementPosition = function( elem ) 
{
    var offsetTrail = elem;
    var offsetLeft 	= 0;
    var offsetTop 	= 0;
    
    while( offsetTrail ) 
    {
        offsetLeft 	+= offsetTrail.offsetLeft;
        offsetTop 	+= offsetTrail.offsetTop;
        
        offsetTrail  = offsetTrail.offsetParent;
    }
	
    return { left : offsetLeft, top : offsetTop };
}

//=========================================================================
//
// Removes those br tags that mozilla adds when you backspace through a node
//
//=========================================================================

Editor.prototype.remove_first_br = function() 
{
	var sel 			= this.edit_object.getSelection()
	var range 			= sel.getRangeAt(0)
	var startContainer 	= range.startContainer
	var container 		= startContainer.parentNode
	
	// find the parent node
	
	var node = this.skipInline( container )
	
	// traverse the node backwards to find the last br
	
	if( node.firstChild ) 
	{
		if( node.firstChild.nodeType == 3 ) 
		{
			if( node.firstChild.nextSibling ) 
			{
				node = node.firstChild.nextSibling;
			}
		}
		
		while( node.firstChild && node.firstChild.nodeType == 1 ) 
		{
			node = node.firstChild;
		}
		
		// if more than one br assume it's meant to be there otherwise remove it
		
		var previousTag = '';
		
		if( node.nextSibling ) 
		{
			nextTag = node.nextSibling.nodeName
			
			if( node.nodeName == 'BR' && nextTag != 'BR' ) 
			{
				node.parentNode.removeChild( node )
			}
		}
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.context = function( evnt ) {
	window.getSelection().removeAllRanges();

	this.closePopup();

	// window.focus();
	// window.document.body.focus();
	// this.edit_object.focus();

	var selection	= this.edit_object.getSelection();
	var container 	= selection.focusNode;
	var pos 		= Math.max(0, selection.focusOffset - 1);
	var imageNode 	= null;
	var tableNode 	= null;
	var canLink 	= true;
	var inside_link = this.isInside( 'A' );

	if( container.nodeType !== 1 && ! inside_link ) {
		canLink = false;
	}

	if( container.childNodes && container.childNodes[pos] && container.childNodes[pos].nodeName ) {
		if( container.childNodes[pos].nodeName.toLowerCase() === 'img' ) {
			imageNode = container.childNodes[pos];
		}
	}

	var menu, oWidth, oHeight;

	if( imageNode ) {
		if(( imageNode.getAttribute('name')) 
		&& ( imageNode.src.search( this.images_url + 'bookmark_symbol.gif' ) !== -1 ))
		{
			menu = document.getElementById( this.id + "_bookmarkMenu" );
			oWidth = 230;
			oHeight = this.bmenu_height + 2;
		} 
		else {
			menu = document.getElementById( this.id + "_imageMenu" );
			oWidth = 230;
			oHeight = this.imenu_height + 2;
		}
	} 
	else if( this.isInside( 'TD' )) {
		menu = document.getElementById( this.id + "_tableMenu" );
		oWidth	= 270;
		oHeight = this.tmenu_height + 2;
		
		this.getTable()
	} 
	else {
		menu = document.getElementById( this.id + "_standardMenu" );
		oWidth	= 230;
		oHeight = this.smenu_height + 2;
	}		 
	
	// make inactive menu items disabled
	var menuRows = menu.getElementsByTagName('TR');
	
	if( menuRows.length >= 1 ) {
		for( var i=0; i < menuRows.length; i++ ) {
			var cmd = menuRows[i].getAttribute('cid');
			tds = menuRows[i].getElementsByTagName('TD');
			
			tds[0].className = "wpContextCellOne";
			tds[1].className = "wpContextCellTwo";
			
			if( cmd === "createlink" ) {
				if( ! canLink ) {
					menuRows[i].disabled = true;
					tds[1].style.color = 'threedshadow';
				} 
				else {
					menuRows[i].disabled = false;
					tds[1].style.color = '';
				}
			} 
			else if( cmd === 'unmergeright' ) {
				if( this.curCell.colSpan < 2 ) {
					menuRows[i].disabled = true;
					tds[1].style.color = 'threedshadow';
				} 
				else {
					menuRows[i].disabled = false;
					tds[1].style.color = '';
				}
			} 
			else if( cmd == 'mergeright' ) {
				if(( ! this.curCell.nextSibling ) || ( this.curCell.rowSpan != this.curCell.nextSibling.rowSpan )) {
					menuRows[i].disabled = true;
					tds[1].style.color = 'threedshadow';
				} 
				else {
					menuRows[i].disabled = false;
					tds[1].style.color = '';
				}
			} 
			else if( cmd == 'mergebelow' ) {
				var numrows 	= this.curTable.getElementsByTagName('TR').length;
				var topRowIndex = this.curRow.rowIndex;
				
				if(( ! this.curRow.nextSibling ) || ( numrows - ( topRowIndex + this.curCell.rowSpan ) <= 0 )) {
					menuRows[i].disabled = true;
					tds[1].style.color = 'threedshadow';
				} 
				else {
					menuRows[i].disabled = false;
					tds[1].style.color = '';
				}
			} 
			else if( cmd == 'unmergebelow' ) {
				if( this.curCell.rowSpan < 2 ) {
					menuRows[i].disabled = true;
					tds[1].style.color = 'threedshadow';
				} 
				else {
					menuRows[i].disabled = false;
					tds[1].style.color = '';
				}
			} 
			else {
				try {
					if( ! this.edit_object.document.queryCommandEnabled( cmd )) {
						menuRows[i].disabled = true;
						tds[1].style.color = 'threedshadow';
					} 
					else {
						menuRows[i].disabled = false;
						tds[1].style.color = '';
					}
				} 
				catch( e ) {
					menuRows[i].disabled = false;
					tds[1].style.color = '';
				}
			}
		}
		
		// now actually make the menus
		var frame = document.getElementById( this.id + '_editFrame' )
		var position = this.getElementPosition( frame );
		var topPos;
		var leftPos;
		var scrollLeft 	= document.body.scrollLeft + document.documentElement.scrollLeft;
		var scrollTop 	= document.body.scrollTop + document.documentElement.scrollTop;
		var availHeight = window.innerHeight + scrollTop;
		var availWidth 	= window.innerWidth + scrollLeft;
		var clientX 	= evnt.clientX + position['left'];
		var clientY 	= evnt.clientY + position['top'];
		
		if( clientX + oWidth > availWidth ) {
			leftPos = availWidth - oWidth - 2
		} 
		else {
			leftPos = clientX;
		}
		
		if( clientY + oHeight > availHeight ) {
			topPos = availHeight - oHeight;
		} 
		else {
			topPos = clientY;
		}
		
		menu.style.position = 'absolute';
		menu.style.left 	= leftPos + 'px';
		menu.style.top 		= topPos  + 'px';
		menu.style.width 	= oWidth  + 'px';
		menu.style.display	= 'block';
	}	
	
	evnt.stopPropagation();
	evnt.preventDefault();
}

//=========================================================================
// Finds the current table, row and cell and puts them in global variables 
// that the other table functions and the table editing window can use.
// requires the current selection
//=========================================================================

Editor.prototype.getTable = function() 
{
	var sel 		= this.edit_object.getSelection()
 	var range 		= sel.getRangeAt(0)
	var container 	= range.startContainer
	
	if( container.nodeType != 1 ) 
	{
		var textNode = container
    	container = textNode.parentNode
	}
	
	this.curCell = container
	
	while( this.curCell.tagName != "TD" && this.curCell.tagName != "BODY" ) 
	{
		this.curCell = this.curCell.parentNode
	}
	
	this.curRow = this.curCell
	
	while( this.curRow.tagName != "TR" && this.curRow.tagName != "BODY" ) 
	{
		this.curRow = this.curRow.parentNode
	}
	
	this.curTable = this.curRow
	
	while( this.curTable.tagName != "TABLE" && this.curTable.tagName != "BODY" ) 
	{
		this.curTable = this.curTable.parentNode
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.m_over = function( element ) 
{
	if( this.initfocus == false ) 
	{
		return
	}
	
	var cmd = element.getAttribute("cid")
	
	if( element.className == "wpDisabled" ) 
	{
		return
	}
	
	if(( cmd == "edittable" ) || ( cmd == "splitcell" )) 
	{
		cmd = "inserthorizontalrule"
	}
	
	if( cmd == "border" ) 
	{
		if( this.border_visible ) 
		{
			element.className = "wpLatchedOver"
		} else {
			element.className = "wpOver"
		}		
		return
	} 
	else if( cmd == "ignore" ) 
	{
		element.className = "wpOver"
		return	
	} 
	else if(( cmd == "undo" ) ||  ( cmd == "redo" )) 
	{
		element.className="wpOver"
		return
	} 
	else 
	{
		try 
		{
			if( this.edit_object.document.queryCommandState( cmd )) 
			{
				element.className = "wpLatchedOver"
				return
			}
		} 
		catch(e) 
		{
			element.className="wpOver"
			return
		}
	}
	element.className = "wpOver"
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.m_out = function( element ) 
{
	if( this.initfocus == false ) 
	{
		return
	}
	
	var cmd = element.getAttribute( "cid" )
	
	if( element.className == "wpDisabled" ) 
	{
		return
	}
	
	if(( cmd == "edittable" ) || ( cmd == "splitcell" )) 
	{
		cmd = "inserthorizontalrule"
	}
	
	if( cmd == "border" ) 
	{
		if( this.border_visible ) 
		{
			element.className = "wpLatched"
		} else {
			element.className = "wpReady"
		}
		return
	} 
	else if( cmd == "ignore" ) 
	{
		element.className = "wpReady"
		return	
	} 
	else if(( cmd == "undo" ) || ( cmd == "redo" )) 
	{
		element.className = "wpReady"
		return
	} 
	else 
	{
		try 
		{
			if( ! this.edit_object.document.queryCommandEnabled( cmd )) 
			{
				element.className = "wpDisabled"
				return
			} 
			else if( this.edit_object.document.queryCommandState( cmd )) 
			{
				element.className = "wpLatched"
				return
			}
		} 
		catch(e) 
		{	
			element.className = "wpReady"
			return
		}
	}
	element.className = "wpReady"
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.m_down = function( element ) 
{
	this.closePopup();
	
	if( this.initfocus == false ) 
	{
		this.edit_object.focus();
		this.initfocus = true;
	}
	
	if( element.className == "wpDisabled" ) 
	{
		return
	}
	
	element.className = "wpDown"
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.m_up = function( element ) 
{
	var style = element.className
	
	if( style == "wpDisabled" ) 
	{
		return
	} 
	else 
	{
		if( style == "wpLatched" ) 
		{
			return
		}
	}
	
	element.className = "wpOver";
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.insertTable = function( rows, cols, width, percent1, height, percent2, border, bordercolor, bgcolor, cellpadding, cellspacing, bCollapse ) 
{
	// edit_object.focus()    
	   
	// generate column widths
	
	table = this.edit_object.document.createElement("table")
	
	if( border != '' ) 
	{
		table.setAttribute( "border", border )
	}		
 	if( bordercolor != "" ) 
 	{
 		table.setAttribute("bordercolor", bordercolor)
	} 
	if( cellpadding != "" ) 
	{
 		table.setAttribute("cellpadding", cellpadding)
	}
	if( cellspacing != "" ) 
	{
 		table.setAttribute("cellspacing", cellspacing)
	}
 	if( bgcolor != "" ) 
	{
 		table.setAttribute("bgcolor", bgcolor)
	}
 	if( width != "" ) 
	{
 		table.setAttribute("width", width+percent1)
	}
 	if( height != "" ) 
	{
 		table.setAttribute("height", height+percent2)
	}
 	if( bCollapse == true ) 
	{
 		table.style.borderCollapse = "collapse"
	}
	
	var tdwidth = 100 / cols;
	
	tdwidth += "%";
	
	for( var i = 0; i < rows; i++ ) 
	{
		row = this.edit_object.document.createElement("tr")
		
    	for( var j = 0; j < cols; j++ ) 
    	{
			cell = this.edit_object.document.createElement("td")
			
			cell.setAttribute("valign", 'top')
			cell.setAttribute("width", tdwidth)
			
			cell.innerHTML = this.tdInners
			
			row.appendChild( cell )
  		}
		
    	table.appendChild( row )
	}
	
	this.edit_object.focus();
	
	this.insertNodeAtSelection( this.edit_object, table );
	
	if( this.border_visible == 1 ) 
	{
		this.show_borders()
	}
	
	this.send_to_html();
	this.send_to_edit_object();
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.insertNodeAtSelection = function( win, insertNode ) 
{
	var sel 	= win.getSelection()
	var range 	= sel.getRangeAt(0)
	
	sel.removeAllRanges()	
	range.deleteContents()
	
	var container 	= range.startContainer
	var pos 		= range.startOffset
	
	range = document.createRange();
	
	if( container.nodeType == 3 && insertNode.nodeType == 3 ) 
	{
		container.insertData( pos, insertNode.nodeValue )
		
		range.setEnd(	container, pos + insertNode.length )
		range.setStart(	container, pos + insertNode.length )
	} 
	else 
	{
		var afterNode
		
		if( container.nodeType == 3 ) 
		{
			var textNode 	= container
			container 		= textNode.parentNode
			var text 		= textNode.nodeValue
			var textBefore 	= text.substr( 0, pos )
			var textAfter 	= text.substr( pos )
			var beforeNode 	= document.createTextNode( textBefore )
			var afterNode 	= document.createTextNode( textAfter  )
			
			container.insertBefore( afterNode, textNode	)
			container.insertBefore( insertNode, afterNode )
			container.insertBefore( beforeNode, insertNode )
			container.removeChild(  textNode )
		} 
		else 
		{
			afterNode = container.childNodes[pos]
			container.insertBefore( insertNode, afterNode )
		}
		
		if( insertNode.tagName ) 
		{
			if( insertNode.tagName == 'IMG' ) 
			{
				range.selectNode( insertNode )
			} 
			else 
			{
				range.setEnd(	afterNode, 0 )
				range.setStart(	afterNode, 0 )
			}
		} 
		else 
		{
			range.setEnd( 	afterNode, 0 )
			range.setStart( afterNode, 0 )
		}		
	}
	
	sel = win.getSelection();
	
	sel.removeAllRanges();
	sel.addRange( range );
	
	//sel.addRange(rngCaret);
	
	win.focus()
}

//=========================================================================
//
// Insert code in editor
//
//=========================================================================

Editor.prototype.insert_code = function( code ) 
{
	//if(( code != "" ) && ( code != null ))
	if( code != null )
	{
		this.edit_object.focus();
		
		span = this.edit_object.document.createElement("SPAN");
		span.innerHTML = code;
		
		this.insertNodeAtSelection( this.edit_object, span );
	}
	
	if( this.border_visible == 1 ) 
	{
		this.show_borders();
	}
	
	this.edit_object.focus();
}

//=========================================================================
//
// This creates the hyperlink html from data sent from the hyperlink window
//
//=========================================================================

Editor.prototype.doHyperlink = function( iHref, iTarget, iTitle ) 
{
	// if no link data sent then unlink any existing link
	
	if( iHref == "" 
	||  iHref == "file://" 
	||  iHref == "http://"
	||  iHref == "https://"
	||  iHref == "mailto:" ) 
	{ 
		this.callFormatting( "Unlink" );		
		this.edit_object.focus();
		return;		
	} 
	else 
	{ 
		//var range = this.edit_object.getSelection().getRangeAt(0);
		var range = this.getRng();
		
		var container 	= range.startContainer;
		var pos 		= range.startOffset;
		var imageNode 	= null;
		
		if( container.tagName ) 
		{
			var images = container.getElementsByTagName('IMG');
			
			var cn = container.childNodes;
			
			if( cn[ pos ] ) 
			{
				if( cn[ pos ].tagName == 'IMG' ) 
				{
					cn[ pos ].setAttribute( 'border', 0 );
				}
			}
		}
		
		if( this.isInside( 'A' )) 
		{
			var sel 		= this.edit_object.getSelection();
			var range 		= sel.getRangeAt(0);
			var container 	= range.startContainer;
			
			if( container.nodeType != 1 ) 
			{
				var textNode = container;
				container = textNode.parentNode;
			}
			
			thisA = container;
			
			while( thisA.tagName != "A" && thisA.tagName != "BODY" ) 
			{
				thisA = thisA.parentNode;
			}
			
			if( thisA.tagName == "A" ) 
			{
				thisA.setAttribute( 'href', iHref );
				thisA.setAttribute( 'target', iTarget );
				thisA.setAttribute( 'title', iTitle );
			} 
		} 
		else 
		{
			this.edit_object.document.execCommand( "CreateLink", false, 'WP_TEMP_LINK_' + iHref );
			
			var links = this.edit_object.document.getElementsByTagName('A');
			
			var l = links.length;
			
			for( var i = 0; i < l; i++ ) 
			{
				if( links[i].getAttribute( 'href' )) 
				{
					if( links[i].getAttribute( 'href' ).search('WP_TEMP_LINK_') != -1 ) 
					{
						links[i].setAttribute( 'href', iHref );
						
						if( iTitle != '' ) 
						{
							links[i].setAttribute( 'title', iTitle );
						}
						
						if( iTarget != '' ) 
						{
							links[i].setAttribute( 'target', iTarget );
						}
					}
				}
			}
			try 
			{
				var sel 	= this.edit_object.getSelection();
				var range 	= sel.getRangeAt(0);
				
				sel.removeAllRanges();
			} 
			catch(e) {}
		}
	}
	
	this.edit_object.focus();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.create_bookmark = function( name ) 
{
	if(( name != '' ) && ( name != null )) 
	{
		img = this.edit_object.document.createElement("img");
		
		img.setAttribute( 'src'		, this.images_url + 'bookmark_symbol.gif' )
		img.setAttribute( 'name'	, name )
		img.setAttribute( 'width'	, 16 ) 
		img.setAttribute( 'height'	, 13 )
		img.setAttribute( 'alt'		, 'Bookmark: ' + name )
		img.setAttribute( 'title'	, 'Bookmark: ' + name )
		img.setAttribute( 'border'	, 0 )
		
		this.insertNodeAtSelection( this.edit_object, img );
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.create_hr = function( align, color, size, width, percent2 ) 
{
	this.edit_object.focus();
	
	hr = this.edit_object.document.createElement("hr");
	
	if( align != '' ) 
	{
		hr.setAttribute( "align", align );
	}	
	
 	if( color != "" ) 
 	{
 		hr.setAttribute( "color", color );
		hr.style.backgroundColor = color
		hr.setAttribute( "noshade", "noshade" );
	} 
	
	if( size != "" ) 
	{
 		hr.setAttribute( "size", size );
	}
	
	if( width != "" ) 
	{
 		hr.setAttribute( "width", width + percent2 );
	}
	
	this.insertNodeAtSelection( this.edit_object, hr );
	this.edit_object.focus();
}

