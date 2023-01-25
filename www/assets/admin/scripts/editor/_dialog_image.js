/**
 *
 * @param id
 */
Dialog.prototype.selectImage = function( id ) {
	if( id ) {
		this.imgURL		= imgs_url[ id ];
		this.imgWidth  	= imgs_width[ id ];
		this.imgHeight 	= imgs_height[ id ];
	}

	if( this.hl !== id ) {
		if( this.hl ) {
			document.getElementById( 'img' + this.hl ).style.backgroundColor = '';
		}

		document.getElementById( 'img' + id ).style.backgroundColor = '#F5F9FD';

		this.hl = id;
	}

	if( document.getElementById( 'options' )) {
		document.getElementById('options').disabled = false;
		document.getElementById('options').style.color = '#212021';
	}
}

/**
 *
 * @returns {boolean}
 */
Dialog.prototype.insertImage = function() {
	var file_ext = this.imgURL.substr( this.imgURL.length - 3 );
	
	switch( file_ext ) {
		case 'swf':
			this.Editor.createFlashHTML( this.imgURL, this.imgWidth, this.imgHeight, '', '', '' );
			break;
			
		case 'wmv':
			this.Editor.createWmvHTML( this.imgURL, this.imgWidth, this.imgHeight, '', '', '' );
			break;
			
		default:
			this.Editor.createImageHTML( this.imgURL, this.imgWidth, this.imgHeight, '', '', '' );
			break;
	}
	
	this.Editor.addUndoLevel();
	this.closeTopWindow();
	return false;
}

/**
 *
 */
Dialog.prototype.moreOptions = function() {
	if( document.getElementById('options').disabled === true ) {
		return;
	}

	if( ! this.hl ) {
		alert('Please select an image first.');

		document.getElementById('options').disabled = true;
	} else {
		document.location.replace( this.modURL + '&i=options' + '&image=' + this.imgURL + '&width=' + this.imgWidth +'&height=' + this.imgHeight );
	}
}

/**
 *
 * @param id
 */
Dialog.prototype.openFolder = function( id ) {
	document.location.replace( this.modURL + '&folder=' + id );
}

/**
 *
 * @returns {boolean}
 */
Dialog.prototype.insertZoomImage = function() {
	if( this.hl ) {
		address = imgs_url[ this.hl ];

		this.Editor.doHyperlink( address, '_self', 'Нажмите для увеличения' );		
	}
	
	this.Editor.addUndoLevel();
	this.closeTopWindow();
	return false;
}

