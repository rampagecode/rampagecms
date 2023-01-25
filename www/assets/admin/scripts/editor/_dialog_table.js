/**
 *
 * @param action
 * @param color
 */
Dialog.prototype.docolor = function( action, color ) {
	if( color != null ) {
		if( action === 1 ) {
			document.table_form.bordercolor.value = color;
			document.getElementById('borderchosencolor').style.backgroundColor = color;
			this.updateStyle();
		}
		
		if( action === 2 ) {
			document.table_form.bgcolor.value = color;
			document.getElementById('bgchosencolor').style.backgroundColor = color;
			this.updateStyle();
		}
	}
}

/**
 *
 * @returns {boolean}
 */
Dialog.prototype.conjunction = function() {
 	var rows = document.table_form.rows.value;
 	var cols = document.table_form.cols.value;
 	var border = " border=\"" + document.table_form.border.value + "\" ";
 	var bordercolor;
	var bgcolor;
	var width;
	var height;
	var attrs;
	var cellspacing;
	var cellpadding;
	var style;
	var bCollapse;
 	
 	if( document.table_form.bordercolor.value === '' ) {
 		bordercolor = "";
	} else {
		bordercolor = " bordercolor=\"" + document.table_form.bordercolor.value + "\" ";
 	} 
	
	if( document.table_form.cellpadding.value === '' ) {
 		cellpadding = "";
	} else {
		cellpadding = " cellpadding=\"" + document.table_form.cellpadding.value + "\" ";
 	} 
	
	if( document.table_form.cellspacing.value === "" ) {
 		cellspacing = "";
	} else {
		cellspacing = " cellspacing=\"" + document.table_form.cellspacing.value + "\" ";
 	} 
 
 	if( document.table_form.bgcolor.value === "" ) {
 		bgcolor = "";
	} else {
		bgcolor = " bgcolor=\"" + document.table_form.bgcolor.value  + "\" ";
 	} 
 
 	if( document.table_form.width.value === "" ) {
 		width = "";
	} else {
		width = "width=\"" + document.table_form.width.value + document.table_form.percent1.value + "\" ";
 	} 
 
 	if( document.table_form.height.value === "" ) {
 		height = "";
	} else {
		height = "height=\"" + document.table_form.height.value + document.table_form.percent2.value + "\" ";
 	}

	if( document.table_form.collapse.checked === true ) {
		bCollapse = true;
		style = 'style="border-collapse:collapse" ';
	} else {
		bCollapse = false;
	}
	
	attrs = " " + width + height + border + bordercolor + bgcolor + cellpadding + cellspacing + style;

	this.Editor.insertTable(
		rows,
		cols,
		document.table_form.width.value,
		document.table_form.percent1.value,
		document.table_form.height.value,
		document.table_form.percent2.value,
		document.table_form.border.value,
		document.table_form.bordercolor.value,
		document.table_form.bgcolor.value,
		document.table_form.cellpadding.value,
		document.table_form.cellspacing.value,
		bCollapse
	);

	if( document.table_form.border.value === '0' ) {
		this.Editor.show_borders();
		this.Editor.set_button_states();
	}

	this.closeWindow();
	return false;
}

/**
 *
 */
Dialog.prototype.updateStyle = function() {
	var oTbl = document.createElement( 'Table' );
		
	oTbl.cellspacing 	= '0'; 
	oTbl.cellpadding 	= '0'; 
	oTbl.width 			= '90%'; 
	oTbl.height			= '50px';  
	oTbl.align			= 'center';
	
	if( document.table_form.collapse.checked === true ) {
		oTbl.style.borderCollapse = "collapse";
	} else {
		oTbl.style.borderCollapse = "separate";
	}

	oTbl.style.fontSize = '8px';
	oTbl.setAttribute( 'border', document.table_form.border.value );
	oTbl.style.backgroundColor 	 = document.table_form.bgcolor.value;	
	oTbl.borderColor 			 = document.table_form.bordercolor.value;
	oTbl.cellPadding = document.table_form.cellpadding.value;
	oTbl.cellSpacing = document.table_form.cellspacing.value;

	var rows = document.table_form.rows.value;
	var cols = document.table_form.cols.value;
			
	for( i = 0; i < rows; i++ ) {
		var oTR = oTbl.insertRow( i );
		
		for( j = 0; j < cols; j++ ) {
			var oTD = oTR.insertCell( j );
			oTD.innerHTML = '&nbsp;';
		}
	}
			
	this.tabPreviewWnd.document.body.innerHTML = oTbl.outerHTML;
}

/**
 *
 */
Dialog.prototype.init = function() {
	if( this.Editor.is_ie ) {
		this.tabPreviewWnd = document.frames('pasteFrame'); 
	} else {
		this.tabPreviewWnd = document.getElementById('pasteFrame').contentWindow; 
	}
	
	this.tabPreviewWnd.document.body.style.padding = '0px';
	this.tabPreviewWnd.document.body.style.margin  = '8px';
	this.updateStyle();
	
	document.getElementById('borderchosencolor').style.backgroundColor 	= '#000000';
	document.getElementById('bgchosencolor').style.backgroundColor 		= '#ffffff';
	
	this.hideLoadMessage();
}
