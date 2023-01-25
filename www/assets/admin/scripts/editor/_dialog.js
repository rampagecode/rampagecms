/**
 *
 * @param jsobj
 * @constructor
 */
function Dialog( jsobj ) {
	var baseWindow = window;

	while( typeof baseWindow.ModalDialog !== 'object' || baseWindow !== baseWindow.parent ) {
		baseWindow = baseWindow.parent;
	}

	if( baseWindow.oEd_content ) {
		this.Editor = baseWindow.oEd_content;
		this.ModalDialog = baseWindow.ModalDialog;
	}
	else if( baseWindow.ModalDialog ) {
		this.ModalDialog = baseWindow.ModalDialog;

		var topWindow = baseWindow.ModalDialog.prevDialogWindow();
		if( topWindow ) {
			if( topWindow.oEd_content ) {
				this.Editor = topWindow.oEd_content;
			} else if ( topWindow.oDialog ) {
				this.Editor = topWindow.oDialog.Editor;
			}
		}
	}
	else if( window.dialogArguments ) {
		try{
			this.Editor = window.dialogArguments.oDialog.Editor;
		}
		catch(e) {
			eval('this.Editor = window.dialogArguments.'+jsobj);	
		}		
		
		this.parentWindow = dialogArguments;
	} 
	else if( parent.window.dialogArguments ) {
		try {
			this.Editor = parent.window.dialogArguments.oDialog.Editor;
		}
		catch(e) {
			eval('this.Editor = parent.window.dialogArguments.'+jsobj);	
		}		
				
		this.parentWindow = parent.window.parentWindow;
	} 
	else if( window.opener ) {
		try {
			this.Editor = window.opener.oDialog.Editor;
		}
		catch(e) {
			eval('this.Editor = window.opener.'+jsobj);	
		}

		this.parentWindow = window.opener;	
	} 
	else if( parent.window.opener ) {
		try {
			this.Editor = parent.window.opener.oDialog.Editor;
		}
		catch(e) {
			eval('this.Editor = parent.window.opener.'+jsobj);					
		}
		
		this.parentWindow = parent.window.parentWindow;
	}
}

/**
 *
 * @param url
 * @param modal
 * @param width
 * @param height
 * @param features
 * @returns {*}
 */
Dialog.prototype.openDialog = function( url, modal, width, height, features = '' ) {
	if( window.oCurrentEditor ) {
		oCurrentEditor.closePopup();
	}

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

	return win;
}

Dialog.prototype.closeWindow = function() {
	if( this.ModalDialog ) {
		this.ModalDialog.closeTopDialog();
	} else {
		window.close();
	}
}

Dialog.prototype.closeTopWindow = function() {
	if( this.ModalDialog ) {
		this.ModalDialog.closeTopDialog();
	} else {
		top.window.close();
	}
}

Dialog.prototype.closeParentWindow = function() {
	if( this.ModalDialog ) {
		this.ModalDialog.closeTopDialog();
	} else {
		parent.window.close();
	}
}


/**
 *  Hide the loading message
 */
Dialog.prototype.hideLoadMessage = function() {
	document.getElementById('dialogLoadMessage').style.display = 'none';
}

/**
 *  Show the loading message
 */
Dialog.prototype.showLoadMessage = function() {
	document.getElementById('dialogLoadMessage').style.display = 'block';
}

/**
 *
 * @returns {boolean}
 */
Dialog.prototype.uploadCheck = function() {
	if( document.getElementById('upload_field').value === '' ) {
		alert("Сначала нажмите 'Обзор...' и выберите файл. Затем нажмите 'Загрузить'.")

		document.getElementById('upload_field').focus();

		return false;
	} else {
		return true;
	}
}

/**
 *
 */
Dialog.prototype.showUploadMessage = function() {
	document.getElementById('uploadMessage').style.display = 'block';
}

/**
 *
 */
Dialog.prototype.hideUploadMessage = function() {
	document.getElementById('uploadMessage').style.display = 'none';
}

/**
 *
 */
Dialog.prototype.cancelUpload = function() {
	window.stop();
	this.hideUploadMessage();
}

/**
 *
 * @param action
 * @param jsobj
 */
Dialog.prototype.colordialog = function( action, jsobj ) {
	var url	= this.Editor.adsess_url + '&u=r&a=colors' + '&x=' + action + '&jsobj=' + jsobj;

  	this.openDialog( url, 'modal', 280, 310 );
}

