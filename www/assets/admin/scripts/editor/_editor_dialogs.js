/**
 *
 * @param url
 * @param modal
 * @param width
 * @param height
 * @param features
 * @returns {Window}
 */
Editor.prototype.openDialog = function( url, modal, width, height, features = '' ) {
	if( oCurrentEditor ) {
		oCurrentEditor.closePopup();
	}

	url += '&jsobj=oEd_' + this.id;

	if( this.ModalDialog ) {
		this.ModalDialog.showModal( url, width, height );
		return this.ModalDialog.topDialogWindow();
	}

	if( ! features ) {
		features = ''
	}

	var params 	= ''
	var name 	= '';
	var win;

	for( var i = 0; i < 16; i++ ) {
		 name += String.fromCharCode( Math.round( Math.random() * 25 ) + 97 )
	}

	if( features.search('left') === -1 ) {
		params = "left=" + (( screen.width / 2 ) - ( width / 2 )) + ","
	}

	if( features.search('top') === -1 ) {
		params += "top=" + (( screen.height / 2 ) - ( height / 2 )) + ","
	}

	if( modal === 'modeless' ) {
		win = window.open( url, name, "dependent=yes,width=" + width + "px,height=" + height + "px," + features + "," + params )
	} else {
		win = window.open( url, name, "modal=yes,width=" + width + "px,height=" + height + "px," + features + "," + params )
	}

	win.focus();

	return win
}

Editor.prototype.closeWindow = function() {
	if( this.ModalDialog ) {
		this.ModalDialog.closeTopDialog();
	} else {
		this.closeWindow();
	}
}

Editor.prototype.closeTopWindow = function() {
	if( this.ModalDialog ) {
		this.ModalDialog.closeTopDialog();
	} else {
		top.window.close();
	}
}

Editor.prototype.closeParentWindow = function() {
	if( this.ModalDialog ) {
		this.ModalDialog.closeTopDialog();
	} else {
		parent.window.close();
	}
}


/**
 *
 * @param srcElement
 * @returns {number}
 */
Editor.prototype.open_table_window = function( srcElement ) {
	var url = this.adsess_url + '&u=r&a=tab';
	
	if( srcElement.className === "wpDisabled" ) {
    	return 0;
  	}

	this.openDialog( url, 'modal', 456, 415 );
}

/**
 * Открывает модальное окно выбора цвета для фона или текста в RTE
 * @param srcElement
 * @param action
 */
Editor.prototype.colordialog = function( srcElement, action ) {
	if( srcElement.className === "wpDisabled" ) {
		return;
	}

	var url	= this.adsess_url + '&u=r&a=colors' + '&x=' + action;
	var h = 325;
	var w = 279;

	this.openDialog( url, 'modal', w, h );
}

/**
 * Окно диалога для поиска и замены текста в RTE
 */
Editor.prototype.openFind = function() {
	var url = this.adsess_url + '&u=r&a=find';
	var _width  = 355;
	var _height = 160;

	this.openDialog( url, 'modeless', _width, _height );
}

/**
 *
 * @param srcElement
 */
Editor.prototype.open_spec_char_window = function( srcElement ) {
	if( srcElement.className === "wpDisabled" ) {
		return;
	}  
		
	this.openDialog( this.adsess_url + '&u=r&a=char', 'modal', 500, 252 );
}

/**
 *
 * @param srcElement
 */
Editor.prototype.open_rule_window = function( srcElement ) {
	if( srcElement.className === "wpDisabled" ) {
		return
	}
			
	this.openDialog( this.adsess_url + '&u=r&a=hr', 'modal', 270, 175 );
}

/**
 *
 */
Editor.prototype.open_table_editor = function() {
	if( this.isInside( 'TD' )) {
    	this.getTable();
    	this.openDialog( this.adsess_url + '&u=r&a=tab&i=edit', 'modal', 420, 550 );
  	} else {
    	alert( this.lng['place_cursor_in_table'] );
  	}
}

