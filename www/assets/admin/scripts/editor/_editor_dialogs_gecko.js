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

/**
 *
 */
Editor.prototype.open_image_window = function() {
	var dialogURL = this.adsess_url + '&u=r&a=img_mgr';
	var selection = this.currentSelection();
	var imageNode = selection.imageNode();
	var windowWidth = 516;
	var windowHeight = 515;

	if( imageNode ) {
		windowHeight = 665;

		if(( imageNode.getAttribute( 'name' )) 
		&& ( imageNode.src.search( this.images_url + 'bookmark_symbol.gif' ) != -1 )) {
			//
		} else {
			var str 	= '';
			var image 	= imageNode.getAttribute( 'src', 2 );
			var height 	= '';
			var width 	= '';
			
			if( imageNode.style.height ) {
				str = imageNode.style.height;
				height = str.replace( /px/, '' );
			} else {
				height = imageNode.getAttribute('height');
			}

			if( imageNode.style.width ) {
				str = imageNode.style.width;
				width = str.replace(/px/, '');
			} else {
				width = imageNode.getAttribute('width');
			}

			var alt 	= imageNode.getAttribute('alt');
			var align 	= imageNode.getAttribute('align');
			var mtop 	= imageNode.style.marginTop; 
			var mbottom = imageNode.style.marginBottom; 
			var mleft 	= imageNode.style.marginLeft; 
			var mright 	= imageNode.style.marginRight; 
			var iborder = imageNode.getAttribute('border');
			
			if( ! iborder ) {
				iborder = '';
			}

			if( ! width ) {
				width = '';
			}

			if( ! height ) {
				height = '';
			}
									
			dialogURL += '&i=options' + '&image=' + image + '&width=' + width + '&height=' + height + '&alt=' + alt + '&align=' + align + '&mtop=' + mtop + '&mbottom=' + mbottom + '&mleft=' + mleft + '&mright=' + mright + '&border=' + iborder;						
		}
	} 
	
	this.imgwin = this.openDialog( dialogURL, 'modal', windowWidth, windowHeight );
}

/**
 *
 */
Editor.prototype.open_image_zoom_window = function() {
	var dialogURL = this.adsess_url + '&u=r&a=img_mgr&i=zoom';
	var selection = this.currentSelection();
	var imageNode = selection.imageNode();

	if( imageNode ) {
		if(( imageNode.getAttribute( 'name' )) 
		&& ( imageNode.src.search( this.images_url + 'bookmark_symbol.gif' ) != -1 )) {
			//
		} 
		else {
			this.imgwin = this.openDialog( dialogURL, 'modal', 501, 655 );
		}
	} 		
}

/**
 * @param srcElement
 */
Editor.prototype.open_hyperlink_window = function( srcElement ) {
	var data = this.generic_link_window_function( srcElement );
	
	if( data ) {
		var thisTarget 	= data['target'];
		var thisTitle 	= data['title'];
		var _width  = 666;
		var _height = 360;
		var szURL 	= this.adsess_url + '&u=r&a=hyperlink&target=' + thisTarget + '&title=' + thisTitle;

		this.linkwin = this.openDialog( szURL, 'modal', _width, _height );
	}
}

/**
 *
 * @param srcElement
 * @returns {{title: string, target: string}}
 */
Editor.prototype.generic_link_window_function = function( srcElement ) 
{
 	var range 		= this.edit_object.getSelection().getRangeAt(0);
	var container 	= range.startContainer;
	
	if(( range == '' ) && ( container.nodeType != 1 ) && ( ! this.isInside( 'A' ))) {
		alert( this.lng['select_hyperlink_text'] );
		return;
	}
	
	var thisTarget = "";
	var thisTitle  = "";
	
	if( this.isInside( 'A' )) {
		if( container.nodeType != 1 ) {
			container = container.parentNode;
		}
		
		var thisA = container;
		
		while( thisA.tagName != "A" && thisA.tagName != "BODY" ) {
			thisA = thisA.parentNode;
		}
		
		if( thisA.tagName == "A" ) {
			var thisLink = thisA.getAttribute( "HREF", 2 );
			
			this.curHyperlink = thisLink;
			
			if( thisA.getAttribute( "target" )) {
				thisTarget = thisA.getAttribute( "target" );
			}
			
			if( thisA.getAttribute( "title" )) {
				thisTitle = thisA.getAttribute( "title" );
			}
		} else {
			this.curHyperlink = '';
		}
	} else {
		this.curHyperlink = '';
	}
	
	return {
		target: thisTarget,
		title: thisTitle
	}
}

/**
 *
 * @param srcElement
 */
Editor.prototype.open_document_window = function( srcElement ) {
	var data = this.generic_link_window_function( srcElement );
	
	if( data ) {
		this.docwin = this.openDialog( this.adsess_url + '&u=r&a=doc_mgr&link=' + this.curHyperlink, 'modal', 950, 400 );
	}
}

/**
 *
 * @param srcElement
 */
Editor.prototype.open_bookmark_window = function( srcElement ) {
	var selection = this.currentSelection();
	var imageNode = selection.imageNode();
	var arr 	  = '';

	if( imageNode
		&& imageNode.getAttribute('name')
		&& ( imageNode.src.search( this.images_url + 'bookmark_symbol.gif' ) !== -1 )
	) {
		arr = imageNode.name;
	}

	this.bookwin = this.openDialog( this.adsess_url + '&u=r&a=bookmark&name=' + arr, 'modal', 300, 115 );
}

/**
 *
 * @param iurl
 * @param iwidth
 * @param iheight
 * @param ialign
 * @param ialt
 * @param iborder
 * @param imargin
 */
Editor.prototype.createImageHTML = function( iurl, iwidth, iheight, ialign, ialt, iborder, imargin ) {
	if( iurl == '' ) {
		return;
	}
	
	this.edit_object.focus();
		
	// are we editing an existing image?
	
	var editing 	= false;
	var selection = this.currentSelection();
	var imageNode = selection.imageNode();

	if( imageNode ) {
		editing = true;
	} else {
		imageNode = this.edit_object.document.createElement("img");
	}

	imageNode.setAttribute( "src", iurl );
	
	if(( iwidth != '' ) && ( iheight != '' ) && ( iwidth != 0 ) && ( iheight != 0 ) && ( iheight != null )) {
		imageNode.setAttribute( "width", iwidth );
		imageNode.setAttribute( "height", iheight );
	}
	
	if(( ialign != '' ) && ( ialign != 0 ) && ( ialign != null )) {
		imageNode.setAttribute( "align", ialign );
	}
	
	if(( iborder != '') && ( iborder != null )) {
		imageNode.setAttribute( "border", iborder );
	}

	imageNode.setAttribute( "alt", ialt );
	imageNode.setAttribute( "title", ialt );
	
	if(( imargin != '' ) && ( imargin != null )) {
		imageNode.setAttribute( "style", 'margin:' + imargin );
	}
	
	if( ! editing ) {
		this.insertNodeAtSelection( this.edit_object, imageNode );
	}

	this.edit_object.focus();
}


