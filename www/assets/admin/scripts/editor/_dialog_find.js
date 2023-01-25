/**
 * Find and replace dialog
 */
Dialog.prototype.init = function() {
	this.donereplace 	= false;
	this.donesearch 	= false;
	this.reachedbottom 	= false;
	this.do_replaceall 	= false;
	this.matches 		= 0;

	/*
	if( wp_is_ie ) {
		var rng = obj.edit_object.document.selection.createRange();
	} else {
		var rng = obj.edit_object.getSelection()
	}
	*/
	
	this.rng = this.Editor.getRng();
	this.hideLoadMessage();
}

Dialog.prototype.searchtype = function() {
	var matchcase = document.frmSearch.blnMatchCase.checked ? 4 : 0;
	var matchword = document.frmSearch.blnMatchWord.checked ? 2 : 0;

	return matchcase + matchword;
}

Dialog.prototype.findtext = function( start ) {
	if( start ) {
		oDialog.do_replaceall = false;
	}
	
	this.reachedbottom = false;
	
	if( document.frmSearch.strSearch.value.length < 1 ) {
    	alert( "Введите текст в поле 'Искать:'." );
  	} else {
    	var searchval = document.frmSearch.strSearch.value;
		var msg1 = "Поиск достиг конца документа.\n";
		var msg2 = "Нажмите OK чтобы начать поиск сначала.\n";
		var msg3 = "Нажмите Отмена чтобы изменить поисковые слова.";
		var param = null

		if( document.frmSearch.blnMatchWord.checked ) {
			searchval = ' ' + searchval + ' ';
		}

		if( document.frmSearch.blnMatchCase.checked ) {
			param = true;
		}

		if( this.Editor.edit_object.find( searchval, param )) {
			this.donesearch = true;
		} else {
			this.reachedbottom = true;

			var startfromtop = false;
			var message;

			if( ! this.do_replaceall ) {
				message = msg1 + msg2 + msg3;
			} else {
				message = msg1 + "Найдено " + this.matches + " результатов.\n" + msg2 + msg3;
				message = message.replace( '##matches##', this.matches );
				this.matches = 0;
			}

			if( window.confirmation ) {
				window.confirmation( message, confirm => {
					if( confirm ) {
						this.clearSelection();
						this.findtext(); // start again
					} else {
						this.donesearch = false;
					}
				});
			} else {
				if( confirm( message )) {
					this.clearSelection();
					this.findtext(); // start again
				} else {
					this.donesearch = false;
				}
			}
		}
	}
}

Dialog.prototype.clearSelection = function() {
	this.Editor.edit_object.document.execCommand( 'selectall', false, null );
	this.Editor.edit_object.getSelection().removeAllRanges();
}

Dialog.prototype.replacetext = function( start ) {
	if( document.frmSearch.strSearch.value.length < 1 ) {
    	alert( "Введите текст в поле 'Искать:'." );
		return;
	}
	
	this.do_replaceall = false;
	
	if( ! this.donereplace && this.donesearch ) {
		this.Editor.insert_code( document.frmSearch.strReplace.value );
		this.donereplace = true;
		
		if( start ) {
			this.Editor.addUndoLevel();
		}		
	} else {
		this.findtext();
		this.donereplace = false;				
	}
}

Dialog.prototype.replaceall = function( start ) {
	if( start ) {
		this.matches = 0; 
		this.reachedbottom = false; 		
	}
		
	if( document.frmSearch.strSearch.value.length < 1 ) {
    	alert( "Введите текст в поле 'Искать:'." );
		return;
	}
	
	this.do_replaceall = true;
	
	if( ! this.reachedbottom ) {
		this.replacetext();
		this.matches += 1;
		this.replaceall();
	} else {
		this.Editor.addUndoLevel();
		return true;		
	}
}
