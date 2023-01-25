/**
 *
 * @returns {boolean}
 */
Dialog.prototype.insertImage = function() {
	this.Editor.createImageHTML(
		document.image_form.imagename.value,
		document.image_form.iwidth.value,
		document.image_form.iheight.value,
		document.image_form.ialign.value,
		document.image_form.alt.value,
		document.image_form.border.value,
		document.image_form.mtop.value + 'px '
			+ document.image_form.mright.value + 'px '
			+ document.image_form.mbottom.value + 'px '
			+ document.image_form.mleft.value + 'px '
	);

	this.Editor.addUndoLevel();
	this.closeTopWindow();
	return false;
}

/**
 *
 */
Dialog.prototype.loadSettings = function() {
	if( this.imgAlign !== '' ) {
		document.image_form.ialign.value = this.imgAlign;
	}


	if( this.tagThumb !== '' ) {
		document.getElementById('stylepreview').innerHTML = this.tagThumb + document.getElementById('stylepreview').innerHTML;		
	}
	
	this.updateStyle();
}

/**
 *
 */
Dialog.prototype.resetDimensions = function() {
	if( this.imgRealWidth && this.imgRealHeight ) {
		document.image_form.iwidth.value 	= this.imgRealWidth;
		document.image_form.iheight.value 	= this.imgRealHeight;
	}
}

/**
 *
 */
Dialog.prototype.updateStyle = function() {
	document.getElementById('wrap').align = document.image_form.ialign.value;
	
	if( document.image_form.mtop.value 		=== '') 	document.image_form.mtop.value 		= '0';
	if( document.image_form.mbottom.value 	=== '') 	document.image_form.mbottom.value 	= '0';
	if( document.image_form.mleft.value 	=== '') 	document.image_form.mleft.value	 	= '0';
	if( document.image_form.mright.value 	=== '') 	document.image_form.mright.value 	= '0';
	
	document.getElementById('wrap').style.marginTop 	= document.image_form.mtop.value;
	document.getElementById('wrap').style.marginBottom 	= document.image_form.mbottom.value;
	document.getElementById('wrap').style.marginLeft 	= document.image_form.mleft.value;
	document.getElementById('wrap').style.marginRight 	= document.image_form.mright.value;		
}
