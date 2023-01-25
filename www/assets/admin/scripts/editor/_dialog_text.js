/**
 *
 * @param id
 * @param title
 */
Dialog.prototype.selectText = function( id, title ) {
	if( this.hl !== id ) {
		if( this.hl ) {
			document.getElementById( 'txt' + this.hl ).style.backgroundColor = '';
		}

		document.getElementById( 'txt' + id ).style.backgroundColor = '#F5F9FD';

		this.hl 	  = id;
		this.hl_title = title;
	}
			
	if( document.getElementById( 'ok' )) {
		document.getElementById('ok').disabled = false;
		document.getElementById('ok').style.color = '#212021';
	}			
}

/**
 *
 * @param id
 */
Dialog.prototype.openFolder = function( id ) {
	document.location.replace( this.mod_url + '&folder=' + id );
}

/**
 *
 * @returns {null|*|boolean}
 */
Dialog.prototype.get_content_id = function() {
	if( this.hl ) {
		document.getElementById('sel_text_id').value = this.hl;
		document.getElementById('sel_text_title').value = this.hl_title;
	}

	this.closeParentWindow();
}

/**
 *
 * @param base_url
 * @param id
 */
Dialog.prototype.openContentWindow = function( base_url, id ) {
	if( this.ModalDialog ) {
		this.ModalDialog.showModal( base_url + '&u=r&a=content&i=edit&x=' + id, 700, 525 );
		return;
	}

	var winName 	= 'contentWnd' + id;
	var winHeight 	= 553;
	var winWidth 	= 700;
	var winURL		= base_url + '&u=r&a=content&i=edit&x=' + id;
	var wnd = window.open( winURL, winName, 'width=' + winWidth + ',height=' + winHeight + ',resizable=no,scrollbars=no' );
	wnd.focus();
}
