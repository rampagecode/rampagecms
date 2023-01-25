/**
 *
 */
var active_content_wnd;

/**
 *
 */
var rval;

/**
 *
 */
var admenu_id;

/**
 *
 * @param id
 * @param content_field_name
 */
Dialog.prototype.openContentManager = function( id, content_field_name ) {
	active_content_wnd = content_field_name;
	var url = this.baseurl + '&u=r&a=wrap&real=content';
	rval = this.openDialog( url, 'modal', 500, 475 );
	rval.onunload = this.update_content_field;
}

/**
 *
 * @param e
 */
Dialog.prototype.update_content_field = function(e) {
	var id 		= '';
	var title 	= '';

	if( rval.document.getElementsByTagName('IFRAME')[0] ) {
		id 		= rval.document.getElementsByTagName('IFRAME')[0].contentDocument.getElementById('sel_text_id').value;
		title 	= rval.document.getElementsByTagName('IFRAME')[0].contentDocument.getElementById('sel_text_title').value;
	}

	if( id && title ) {
		document.getElementsByName( active_content_wnd  + '_id' )[0].value 		= id; 			
		document.getElementsByName( active_content_wnd  + '_title')[0].value 	= title;
	}
}

/**
 *
 */
Dialog.prototype.open_admenu_mgr = function() {
	var url = this.baseurl + '&u=r&a=wrap&real=admenu';
	admenu_id = this.openDialog( url, 'modal', 500, 510 );
	admenu_id.onunload = return_admenu_id;
}

/**
 *
 * @param e
 */
Dialog.prototype.return_admenu_id = function(e) {
	if( admenu_id.frames[0].hl ) {
		document.getElementsByName( 'admin_menu_id' )[0].value = admenu_id.frames[0].hl;			
	}
}


