//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.show_menu = function( type, srcElement ) {
	var frame = document.getElementById( this.id + '_' + type + '_frame' )
	
  	if( frame.style.display == 'none' ) {
    	this.hide_menu()    	
    	
    	//frameObj = eval( "this." + type + "_frame" )
		var writeIt;
    	
    	try {
      		writeIt = !frameThis.written;
    	} 
    	catch( e ) {
       		writeIt = true
    	}
    	
    	if( writeIt ) {
      		var frameDoc = eval( "this." + type + "_frame.document" );

      		if( this.styles == '' ) {
        		this.styles = this.make_styles();
      		}

			var style_url = this.url_3xi_css + 'style.editor.dropdown.css';
			var menu_code = '<!doctype html><html><head>';
			menu_code += '<link rel="stylesheet" href="'+style_url+'" type="text/css">';
			menu_code += this.styles;
			menu_code += '<script type="text/javascript">function on (elm) {elm.className="on";} function off (elm) {elm.className="off";}</script>';
			menu_code += '<body><div id="container">';
      		
      		if( type == "font" ) {
				menu_code += document.getElementById(this.id+"_font-menu").innerHTML;
      		} 
      		else if( type == "size" ) {
        		menu_code += document.getElementById(this.id+"_size-menu").innerHTML;
      		} 
      		else if( type == "format" ) {
        		menu_code += document.getElementById(this.id+"_format-menu").innerHTML;
      		} 
      		else if( type == "class") {
        		menu_code += document.getElementById(this.id+"_class-menu").innerHTML;
      		}

			menu_code += '</body></html>';

			frameDoc.open();
			frameDoc.write( menu_code );
			frameDoc.close();
			
      		try {
      			frameThis.written = true
      		} 
      		catch (e) {}
			
			oCurrentEditor = this
      		
      		setTimeout( "oCurrentEditor.set_menu_height( '" + type + "' )", 400 );						
    	}

		frame.style.display = "block"
  	} 
  	else {
    	this.hide_menu()
  	}
}

//=========================================================================
//
// Set menu height
//
//=========================================================================

Editor.prototype.set_menu_height = function( type ) 
{		
	try 
	{
    	maxHeight = 202
    	
    	var docHeight = document.getElementById( this.id + '_editFrame' ).height
    	    	
    	if( docHeight > 190 ) 
    	{
      		maxHeight = docHeight - 12
    	}
    	
    	var frame = document.getElementById( this.id + "_" + type + "_frame" )
    	
    	if( this.is_ie ) 
    	{
      		var height = document.frames( this.id + "_" + type + "_frame" ).document.getElementById('container').offsetHeight
    	} 
    	else 
    	{
      		var height = document.getElementById( this.id + "_" + type + "_frame" ).contentWindow.document.getElementById('container').offsetHeight
    	}
    	
    	if( height < maxHeight && height > 0 ) 
    	{
      		frame.height = height + 2
    	} 
    	else 
    	{
      		frame.height = maxHeight
    	}
  	} 
  	catch(e) 
  	{		
    	return
  	}
}

//=========================================================================
//
// 
//
//=========================================================================

Editor.prototype.menuover = function( srcElement ) 
{
	tds = srcElement.getElementsByTagName('TD')
	
	tds[0].className = "wpContextCellOneOver"
	tds[1].className = "wpContextCellTwoOver"
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.menuout = function( srcElement ) 
{
	tds = srcElement.getElementsByTagName('TD')
	
	tds[0].className = "wpContextCellOne"
	tds[1].className = "wpContextCellTwo"
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.remove_attributes = function( collection, attribute ) 
{
	var n = collection.length
  
	for( var i = 0; i < n; i++ ) 
	{
    	if( collection[i].getAttribute( attribute )) 
    	{
      		if( collection[i].getAttribute( attribute ) == 'null' ) 
      		{
        		collection[i].removeAttribute( attribute )
      		}
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.change_font_size = function( size ) 
{
	this.hide_menu();
  
	this.edit_object.focus()
  
	if( size == 'Default' ) 
  	{
    	this.edit_object.document.execCommand( "RemoveFormat", false, null )
  	} 
  	else 
  	{
    	this.edit_object.document.execCommand( "FontSize", false, size )
  	}
  
  	if( size == 'null' ) 
  	{
    	var fonts = this.edit_object.document.getElementsByTagName( "FONT" )
    	
    	this.remove_attributes( fonts, 'size' )
  	}
	
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.change_font = function( font ) 
{
	this.hide_menu()
	
  	this.edit_object.focus()
  	
  	if( font == 'Default' ) 
  	{
    	this.edit_object.document.execCommand( "RemoveFormat", false, null )
  	} 
  	else 
  	{
    	this.edit_object.document.execCommand( "FontName", false, font )
  	} 	
  	
  	if( font == 'null' ) 
  	{
    	var fonts = this.edit_object.document.getElementsByTagName( "FONT" )
		
    	this.remove_attributes( fonts, 'face' )
  	}
	
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.change_format = function( format ) 
{
	this.hide_menu();
	
	this.edit_object.focus();
	
  	if( ! this.is_ie ) 
  	{
    	// add attributes back in because formatblock removes them in mozilla
    	
    	//var sel 		= this.edit_object.getSelection();
    	//var range 	= sel.getRangeAt(0);
		
    	var range 		= this.getRng();		
    	var container 	= range.startContainer;
    	var textNode 	= container;
    	container 		= textNode.parentNode;
		
    	var parentTag 	= this.skipInline( container );
    	
    	// can't safley continue if this is not a supported tag for the formatblock command
    	
    	if( parentTag.tagName ) 
    	{
      		if( ! wp_supported_blocks.test( parentTag.tagName.toLowerCase() )) 
      		{
        		this.edit_object.document.execCommand( "FormatBlock", false, format );
				
        		return;
      		}
    	}
    	
    	var attributes = parentTag.attributes;
    	
    	this.edit_object.document.execCommand( "FormatBlock", false, format );
    	
    	container = textNode.parentNode;
    	
    	// add attributes back
    	
    	var parentTag = this.skipInline( container );
    	
    	this.add_attributes( parentTag, attributes );
  	} 
  	else 
  	{
    	this.edit_object.document.execCommand( "FormatBlock", false, format );
  	}
	
	//this.undoBookmark = null;
	this.addUndoLevel();	
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.remove_highlight = function( node ) 
{
	if( node.nodeType != 1 ) 
	{
    	return
  	}
  	
  	var cn = node.childNodes
  	var n  = cn.length
  	
  	for( var i = 0; i < n; i++ ) 
  	{
    	if( cn[i].style ) 
    	{
        	if( cn[i].style.backgroundColor == 'rgb(127, 124, 117)' ) 
        	{
          		cn[i].style.backgroundColor = ''
        	}
    	}
    	this.remove_highlight( cn[i] );
  	}
}

//=========================================================================
//
// Catch and execute the commands sent from the buttons and tools
//
//=========================================================================

Editor.prototype.callFormatting = function( sFormatString ) 
{
	this.edit_object.focus();
	
	if( this.is_ie )
	{
		if( this.is_ie50 ) 
		{
			var real_obj = this.edit_object.document;
		} 
		else 
		{
			var real_obj = document;
		}
		
		real_obj.execCommand( sFormatString, false, null )
	}
	else
	{			
		if( sFormatString == "CreateLink" ) 
		{
			var szURL = prompt( "Enter a URL:", "" )
			
			this.edit_object.document.execCommand( "CreateLink", false, szURL )
		} 
		else 
		{
			this.edit_object.document.execCommand( sFormatString, false, null )
		}
	}
	
	this.set_button_states();		
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.testTextCut = function()
{
	if( ! this.findtextcut )
	{
		this.findtextcut = new RegExp( '<input.+?id=[\"\']?textcuttag[\"\']?.*?>', 'i' );
	}
			
	if( this.findtextcut.test( this.edit_object.document.body.innerHTML ))
	{
		return true;
	} else {
		return false;
	}	
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.insertTextCut = function()
{
	var findtextcut = new RegExp( '<input.+?id=[\"\']?textcuttag[\"\']?.*?>', 'i' );
			
	if( this.testTextCut() )
	{
		alert( 'Обрезание уже сделано' );
	}
	else
	{
		this.edit_object.focus();
		
		elm = this.edit_object.document.createElement( 'input' );
		
		elm.setAttribute( 'title'	, "Текст расположенный ниже этой линии будет скрыт" );	
		elm.setAttribute( 'id'		, 'textcuttag' );		
		elm.setAttribute( 'disabled', 'disabled' );		
				
		if( this.is_ie )
		{								
			elm.style.backgroundImage 		= 'url(/assets/admin/images/editor/imgtextcut.png)';
			elm.style.backgroundPosition 	= 'center';
			elm.style.backgroundRepeat 		= 'repeat-x';
			elm.style.backgroundColor		= 'transparent';
			elm.style.border				= 'none';
			elm.style.height				= '15px';
			elm.style.width					= '100%';
			
			this.insert_code( elm.outerHTML );			
		}
		else
		{								
			styles = 'background-image: url(' + '/assets/admin/images/editor/' + 'imgtextcut.png' + ');'
			+ 'background-position: center; background-repeat: repeat-x; background-color: transparent;'
			+ 'border: none; height: 15px; width: 100%;'		
			
			elm.setAttribute( 'style', styles );
						
			this.insertNodeAtSelection( this.edit_object, elm );
		}
		
		this.edit_object.focus();
		
		this.addUndoLevel();
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.createFlashHTML = function( iurl, iwidth, iheight, ialign, ialt, iborder, imargin )
{
	this.edit_object.focus();
	
	/*
	var id = 'flash_';
	
	for( var i = 0; i < 8; i++ )
	{ 
		 id += String.fromCharCode( Math.round( Math.random() * 25 ) + 97 )
	}
	*/
			
	elm = this.edit_object.document.createElement( 'EMBED' );
	
	elm.setAttribute( 'src', iurl );
	elm.setAttribute( 'menu', 'false' );
	elm.setAttribute( 'quality', 'hight' );
	elm.setAttribute( 'width', iwidth );
	elm.setAttribute( 'height', iheight );
	elm.setAttribute( 'type', 'application/x-shockwave-flash' );
	elm.setAttribute( 'pluginspage', 'http://www.macromedia.com/go/getflashplayer' ); 	
	
	// src='http://img.gismeteo.ru/flash/fw88x31.swf?index=29846'
				
	if( this.is_ie )
	{
		/*
		elm.style.backgroundImage 		= 'url(/admin/images/editor/imgtextcut.png)';
		elm.style.backgroundPosition 	= 'center';
		elm.style.backgroundRepeat 		= 'repeat-x';
		elm.style.backgroundColor		= 'transparent';
		elm.style.border				= 'none';
		elm.style.height				= '15px';
		elm.style.width					= '100%';
		*/
		
		this.insert_code( elm.outerHTML );			
	}
	else
	{	
		/*
		styles = 'background-image: url(' + '/admin/images/editor/' + 'imgtextcut.png' + ');'
		+ 'background-position: center; background-repeat: repeat-x; background-color: transparent;'
		+ 'border: none; height: 15px; width: 100%;'		
		
		elm.setAttribute( 'style', styles );
		*/
		
		this.insertNodeAtSelection( this.edit_object, elm );
	}
	
	this.edit_object.focus();
	
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.insertHiddenBlock = function()
{
	this.edit_object.focus();	
								
	if( this.is_ie )
	{				
		var in_cite = false;
		
		var parent = this.edit_object.document.selection.createRange().parentElement();
		
		while( parent != null )
		{
			if( parent.name == 'editor_hiddendiv' )
			{				
				in_cite = true; 
				break;
			}
			
			parent = parent.parentNode;
		}
		
		if( ! in_cite )
		{						
			var cite = this.edit_object.document.createElement( 'CITE' );
			
			cite.setAttribute( 'name', 'editor_hiddendiv' );
			
			/*
			var div = this.edit_object.document.createElement( 'DIV' );
			
			div.style.border = '1px dashed black';
			div.style.backgroundColor = '#f4f4f4';
			div.style.height = '40px';
			div.style.fontStyle = 'normal';
			
			cite.appendChild( div );
			*/

			cite.style.border = '#000000 1px dashed';
			cite.style.backgroundColor = '#f4f4f4';
			cite.style.height = '40px';
			cite.style.width = '100%';
			cite.style.fontStyle = 'normal';			
			
			this.insert_code( cite.outerHTML );			
			this.edit_object.focus();		
			this.addUndoLevel();
		}
		else
		{
			alert('Нельзя создавать вложенные скрытые блоки');
		}
	}
	else
	{
		alert('Не поддерживается в FireFox!');
		return false;
	}	
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.createWmvHTML = function( iurl, iwidth, iheight, ialign, ialt, iborder, imargin )
{	
	iurl = 'http://www.kotel-nk' + iurl;
	
	this.edit_object.focus();		
	/*
	var object = this.edit_object.document.createElement( 'object' );
	object.setAttribute( 'classid', 'clsid:6bf52a52-394a-11d3-b153-00c04f79faa6' );
	object.setAttribute( 'width', iwidth );
	object.setAttribute( 'height', iheight );
	object.setAttribute( 'codebase', 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701' );		
	
	var param = this.edit_object.document.createElement( 'param' );
	param.setAttribute( 'name', 'src' );
	param.setAttribute( 'value', iurl );
	
	var embed = this.edit_object.document.createElement( 'embed' );
	embed.setAttribute( 'type', 'application/x-mplayer2' );
	embed.setAttribute( 'width', iwidth );
	embed.setAttribute( 'height', iheight );
	embed.setAttribute( 'src', iurl );		
	
	object.appendChild( param );		
	//object.appendChild( embed );		
	*/
	
	var cls = 'CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95';
	var codebase = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701';
	
	//h = '<object classid="clsid:' + cls + '" codebase="' + codebase + '" width="' + iwidth + '" height="' + iheight + '">';
	
	var out = '';
	
	alert( iurl );
	
	out += '<object classid="'+cls+'" width="'+iwidth+'" height="'+iheight+'" codebase="'+codebase+'" standby="Loading..." type="application/x-oleobject">';
	out += '<param name="fileName" value="'+iurl+'">';
	out += '<param name="animationatStart" value="true">';
	out += '<param name="transparentatStart" value="true">';
	out += '<param name="autoStart" value="true">';
	out += '<param name="showControls" value="true">';
	out += '<param name="loop" value="true">';
	out += '<embed type="application/x-mplayer2" width="'+iwidth+'" height="'+iheight+'" src="'+iurl+'" autostart="true" designtimesp="5311" loop="true" showdisplay="0" showstatusbar="-1" videoborder3d="-1" bgcolor="darkblue" showcontrols="true" showtracker="-1" id="mediaPlayer" name="mediaPlayer" displaysize="4" autosize="-1" pluginspage="http://microsoft.com/windows/mediaplayer/en/download/"></embed>';
	out += '</object>';	

/*	
      <OBJECT id='mediaPlayer' width="320" height="285" 
      classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' 
      codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'
      standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>
	  
      <param name='fileName' value="http://www.kotel-nk/images/a37632621ae3934232f82d8a36e5bdf7.wmv">
      <param name='animationatStart' value='true'>
      <param name='transparentatStart' value='true'>
      <param name='autoStart' value="true">
      <param name='showControls' value="true">
      <param name='loop' value="true">
	  
      <EMBED type='application/x-mplayer2'
        pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'
        id='mediaPlayer' name='mediaPlayer' displaysize='4' autosize='-1' 
        bgcolor='darkblue' showcontrols="true" showtracker='-1' 
        showdisplay='0' showstatusbar='-1' videoborder3d='-1' width="320" height="285"
        src="http://www.kotel-nk/images/a37632621ae3934232f82d8a36e5bdf7.wmv" autostart="true" designtimesp='5311' loop="true">
      </EMBED>
      </OBJECT>
	*/
	
	if( this.is_ie )
	{
		//this.insert_code( object.outerHTML );
		this.insert_code( out );
	}
	else
	{	
		this.insertNodeAtSelection( this.edit_object, object );
	}
	
	this.edit_object.focus();	
	this.addUndoLevel();
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.insertYoutubeVideo = function()
{
	this.edit_object.focus();
	
	var video_id = prompt("Введите идентификатор видео", "");	
	
	test_video_id = new RegExp( '^[a-z0-9]{11,}$', 'i' );	
			
	if( ! test_video_id.test( video_id ))
	{
		alert('Введен неверный идентификатор');
		return;
	}
	
	if( video = this.edit_object.document.getElementById( video_id ))
	{
		alert('Такое видео уже есть на странице');
		return;
	}

	if( this.is_ie )
	{			
		img = '<img name="youtubevideo" id="'+video_id+'" src="'+this.images_url+'youtube_splash.gif" contenteditable="false" width="425" height="335" title="" alt="" border="1">';
		
		this.insert_code( img );			
		this.edit_object.focus();
		this.addUndoLevel();
	}
	else
	{
		alert('Не поддерживается в FireFox!');
		return;
	}	
}