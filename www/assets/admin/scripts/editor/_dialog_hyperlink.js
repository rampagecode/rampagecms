/**
 * Highlight
 * @param srcElement
 */
Dialog.prototype.highlight = function( srcElement ) {
	if( this.hl ) {
		this.hl.style.backgroundColor 	= '#ffffff';
		this.hl.style.color 			='#003399';
	}
	
	srcElement.style.backgroundColor	= 'highlight';
	srcElement.style.color 				= 'highlighttext';
	
	this.hl = srcElement;
}

/**
 * Initialize
 */
Dialog.prototype.init = function() {
	// show anchors
	var anchors = this.Editor.edit_object.document.getElementsByTagName('IMG');
	var anchorLinks = '<p><a class="filelink" id="#" style="height:22px; margin:0px 0px 0px 0px;" onclick="oDialog.highlight(this)" href="javascript:oDialog.localLink(\'#\');" title="URL: #"><img src="' + this.Editor.images_url + 'spacer.gif" width="1" height="22" alt="" border="0" align="absmiddle">Вверх документа</a></p>\n';
	var l = anchors.length
	
	anchorLinks += '<p><b><img src="' + this.Editor.images_url + 'spacer.gif" width="1" height="22" alt="" border="0" align="absmiddle">Закладки:</b></p>';
			
	for( var i=0; i < l; i++ ) {
		if(( anchors[i].getAttribute('name')) && ( anchors[i].src.search( this.Editor.images_url + 'bookmark_symbol.gif' ) != -1 )) {
			var name = anchors[i].getAttribute('name')
			var nameSlashed = name.replace(/'/, "\\'")
			
			anchorLinks += '<p><a class="filelink" id="#' + name + '" style="height:22px; margin:0;" onclick="oDialog.highlight(this)" href="javascript:oDialog.localLink(\'#'+nameSlashed+'\');" title="URL: #'+name+'"><img src="' + this.Editor.images_url + 'bookmark.gif" width="22" height="22" alt="" border="0" align="absmiddle">' + name + '</a></p>\n'
		}
	}
		
	document.getElementById('page_button').style.display = 'block';
	document.getElementById('site_button').style.display = 'block';
	document.getElementById('anchors').innerHTML = anchorLinks;
	
	var current_href = this.Editor.curHyperlink;		
	
	if(( current_href != "" ) && ( current_href != null )) {
		if( document.getElementById( current_href )) {
			document.getElementById( current_href ).style.backgroundColor	= 'highlight';
			document.getElementById( current_href ).style.color 			= 'highlighttext';
			
			this.hl = document.getElementById( current_href );
			
			if( current_href.substring( 0, 1 ) === "#" ) {
				document.getElementById('page_address').value 	= current_href;
				document.getElementById('page_title').value 	= "";
				
				this.showAnchors();
			} else {
				document.getElementById('site_target').value 		= ""
				document.getElementById('site_target_list').value 	= ""
				document.getElementById('site_title').value 		= ""
				document.getElementById('site_address').value 		= current_href;
				
				this.showLinks();
				this.localLink( current_href );
			}
			
			document.getElementById( current_href ).focus();
			
		} 
		else if( current_href.substring(0,7) === "mailto:" ) {
			var email_array = current_href.split('?subject=');
			
			document.getElementById('email_address').value = email_array[0].replace(/^mailto:/i,'');
			
			if( email_array[1] ) {
				document.getElementById('email_subject').value = email_array[1];
			}
			
			document.getElementById('email_title').value = "";
			
			this.showEmail();
		} else {
			document.getElementById('web_target').value = ""
			document.getElementById('web_target_list').value = ""
			document.getElementById('web_address').value = current_href;
			document.getElementById('web_title').value = ""
			
			this.showWeb();
		}	
	} else {
		this.showWeb();
	}
	
	this.hideLoadMessage();
}

/**
 * Mouse over
 * @param srcElement
 */
Dialog.prototype.mTabOver = function( srcElement ) {
	if( srcElement.className !== 'tbuttonDown' ) {
		srcElement.className = 'tbuttonOver';
	}
}

/**
 * Mouse out
 * @param srcElement
 */
Dialog.prototype.mTabOut = function( srcElement ) {
	if( srcElement.className !== 'tbuttonDown' ) {
		srcElement.className = 'tbuttonUp';
	}
}

/**
 * Mouse Down
 * @param srcElement
 */
Dialog.prototype.mTabDown = function( srcElement ) {
	if( srcElement.className !== 'tbuttonDown' ) {
		srcElement.className = 'tbuttonDown';
	}
}

/**
 * Mouse Click
 * @param srcElement
 */
Dialog.prototype.mTabClick = function( srcElement ) {
	kids = document.getElementById('outlookbar').getElementsByTagName('div');
	
	for( var i = 0; i < kids.length; i++ ) {
		if( kids[i].className === "tbuttonDown" ) {
			if( kids[i] !== srcElement ) {
				kids[i].className = "tbuttonUp";
			}
		}
	}	
	
	if( srcElement.id === 'site_button' ) {
		this.showLinks();
	}
	else if( srcElement.id === 'page_button' ) {
		this.showAnchors();
	} 
	else if( srcElement.id === 'email_button' ) {
		this.showEmail();
	} 
	else if( srcElement.id === 'web_button' ) {
		this.showWeb();
	}
}

/**
 * Make a link
 * @returns {boolean}
 */
Dialog.prototype.linkit = function() {
	var address = '';
	var target = '';
	var title = '';
	
	if( document.getElementById('placeonthissite').style.display === 'block' ) {
		address = document.getElementById('site_address').value;
		target = document.getElementById('site_target').value;
		title = document.getElementById('site_title').value;
		
	} 
	else if( document.getElementById('placeonthispage').style.display === 'block' ) {
		address = document.getElementById('page_address').value;
		title = document.getElementById('page_title').value;	
		
	} 
	else if( document.getElementById('email').style.display === 'block' ) {
		if( document.getElementById('email_address').value.substring(0,7) === "mailto:" ) {
			address = document.getElementById('email_address').value;
		} else {
			address = 'mailto:' + document.getElementById('email_address').value;
		}
		
		if( document.getElementById('email_subject').value !== '' ) {
			address = address + '?subject=' + document.getElementById('email_subject').value;
		}
		
		title = document.getElementById('email_title').value;		
	} 
	else if( document.getElementById('external').style.display === 'block' ) {
		address = document.getElementById('web_address').value;
		target = document.getElementById('web_target').value;
		title = document.getElementById('web_title').value;
	}

	if( address != '' ) {
		this.Editor.doHyperlink( address, target, title );
		this.Editor.addUndoLevel();
	}

	this.closeTopWindow();

	return false;
}

/**
 * Local link
 * @param page
 */
Dialog.prototype.localLink = function( page ) 
{
	page = page.replace(/&quot;/gi, '"');
	
	if( document.getElementById('placeonthissite').style.display === 'block' ) {
		document.getElementById('site_address').value = page;
	} 
	else if( document.getElementById('placeonthispage').style.display === 'block' ) {
		document.getElementById('page_address').value = page;
	}
}

/**
 * Show links
 */
Dialog.prototype.showLinks = function() {
	var st = document.getElementById('siteTree');
	
	if( st.childNodes[0] === undefined ) {
		document.getElementById('siteTreeLoadMessage').style.display = 'block';
		
		if( this.Editor.is_ie ) {
			var iframe_src = '<iframe onLoad="' + "frames['siteTreeContent'].ca = 'link'; document.frames[0].document.getElementById('siteTreeLoadMessage').style.display = 'none';" + '" name="siteTreeContent" style="width:100%;height:100%" frameborder="0" src="'+this.Editor.adsess_url + '&u=x&a=sitetree&i=build&for=hyperlink"></iframe>';
		} else {
			var iframe_src = '<iframe onLoad="' + "frames['siteTreeContent'].ca = 'link'; document.getElementById('siteTreeLoadMessage').style.display = 'none';" + '" name="siteTreeContent" style="width:100%;height:100%" frameborder="0" src="'+this.Editor.adsess_url + '&u=x&a=sitetree&i=build&for=hyperlink"></iframe>';
		}
	
		var place = document.getElementById('siteTree');

		place.innerHTML = iframe_src;	
	}							

	document.getElementById('site_button').className 		 = 'tbuttonDown';
	document.getElementById('placeonthissite').style.display = 'block';
	document.getElementById('placeonthispage').style.display = 'none';
	document.getElementById('email').style.display 			 = 'none';
	document.getElementById('external').style.display 		 = 'none';
}

/**
 * Show anchors
 */
Dialog.prototype.showAnchors = function() {
	document.getElementById('page_button').className 		 = 'tbuttonDown';
	document.getElementById('placeonthissite').style.display = 'none';
	document.getElementById('placeonthispage').style.display = 'block';
	document.getElementById('email').style.display 			 = 'none';
	document.getElementById('external').style.display 		 = 'none';
}

/**
 * Show Email
 */
Dialog.prototype.showEmail = function() {
	document.getElementById('email_button').className 		 = 'tbuttonDown';
	document.getElementById('placeonthissite').style.display = 'none';
	document.getElementById('placeonthispage').style.display = 'none';
	document.getElementById('email').style.display 			 = 'block';
	document.getElementById('external').style.display 		 = 'none';
}

/**
 * Show Web
 */
Dialog.prototype.showWeb = function() {
	document.getElementById('web_button').className 		 = 'tbuttonDown';
	document.getElementById('placeonthissite').style.display = 'none';
	document.getElementById('placeonthispage').style.display = 'none';
	document.getElementById('email').style.display 			 = 'none';
	document.getElementById('external').style.display 		 = 'block';
}
