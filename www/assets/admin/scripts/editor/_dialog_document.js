/**
 * Insert Link
 * @returns {boolean}
 */
Dialog.prototype.insertLink = function() {
	this.Editor.doHyperlink( document.getElementById('doc_url').value, '_self', '' );
	this.Editor.addUndoLevel();
	this.closeTopWindow();
	return false;
}

/**
 * Initialize
 */
Dialog.prototype.init = function() {
	this.hl = null;
	
	var current_href = this.Editor.curHyperlink;
	
	if( current_href !== '' && current_href != null ) {
		for( var i in doc_url ) {
			if( doc_url[i] === current_href ) {
				this.showInfo( i );
			}                      				
		}
  	}

	this.hideLoadMessage();
}

/**
 *
 * @param id
 */
Dialog.prototype.showInfo = function( id ) {
	if( id ) {
		document.getElementById('doc_name').value 	   = aDocName[id];
		document.getElementById('doc_url').value 	   = aDocURL[id];
		document.getElementById('doc_size').innerHTML  = aDocSize[id];
		document.getElementById('doc_date').innerHTML  = aDocDate[id];
		document.getElementById('doc_group').innerHTML = aDocGroup[id];			
	}
	
	if( this.hl != id ) {
		if( this.hl ) {
			document.getElementById( 'doc' + this.hl + 'row1' ).style.backgroundColor = '';
			document.getElementById( 'doc' + this.hl + 'row2' ).style.backgroundColor = '';
		}
		
		document.getElementById( 'doc' + id + 'row1' ).style.backgroundColor = '#F5F9FD';
		document.getElementById( 'doc' + id + 'row2' ).style.backgroundColor = '#F5F9FD';
		
		this.hl = id;
	}			
}
