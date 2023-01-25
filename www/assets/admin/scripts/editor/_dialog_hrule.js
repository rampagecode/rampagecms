/**
 *
 * @param action
 * @param color
 */
Dialog.prototype.docolor = function( action, color ) {
	if( color != null ) {
		if( action === 1 ) {
			document.hr_form.color.value = color;
			document.getElementById('borderchosencolor').style.backgroundColor = color;
		}
	}
}

/**
 *
 * @returns {boolean}
 */
Dialog.prototype.insertRuler = function() {
	var align = document.hr_form.align.value;
	var color = document.hr_form.color.value;
	var size  = document.hr_form.size.value;
	var width = document.hr_form.width.value;
	var _style = "border: 0 none; ";
	
	if( align !== '' ) {
		var _align = ' align="' + align + '" ';
	} else {
		var _align = ' ';
	}
	
	if( color !== '' ) {
		var _color = ' color="'+color+'"';		
		_style += "background-color: "+color+'; color: '+color+'; ';		
	} else {
		var _color = ' ';
	}
	
	if( size !== '' ) {
		var _size = ' size="'+size+'" ';
		_style += 'height: '+size+'px; ';
	} else {
		var _size = ' ';
	}
	
	if( width !== '' ) {
		var _width = ' width="' + width + document.hr_form.percent2.value + '" ';
	} else {		
		var _width = ' ';	
	}
	
	if( document.all ) {
		code = '<hr'+_align+_color+_size+_width+'style="'+_style+'" noshade="noshade">';		
		this.Editor.create_hr( code );
	} else {
		this.Editor.create_hr( document.hr_form.align.value, document.hr_form.color.value, document.hr_form.size.value, document.hr_form.width.value, document.hr_form.percent2.value );
	}

	this.Editor.addUndoLevel();
	this.closeWindow();
	return false;
}