/**
 *
 * @param n
 */
Dialog.prototype.changeView = function( n ) {
	for( var i = 0; i < 3; i++ ) {
		if( i === n ) {
			document.getElementById( this.screens[i] ).style.visibility = 'visible';			
			document.getElementById( this.buttons[i] ).style.background = '#F7FBFF';
			document.getElementById( this.buttons[i] ).style.fontSize   = '11px';
		} else {
			document.getElementById( this.screens[i] ).style.visibility = 'hidden';			
			document.getElementById( this.buttons[i] ).style.background = '#EFF3F7';
			document.getElementById( this.buttons[i] ).style.fontSize   = '10px';
		}					
	}
}

/**
 *
 * @param action
 * @param color
 */
Dialog.prototype.docolor = function( action, color ) {
	if( color != null ) {
		if( action === 1 ) {
			document.getElementById('table_bordercolor').value = color;
			document.getElementById('tableborderchosencolor').style.backgroundColor = color;						
		}
		
		if( action === 2 ) {
			document.getElementById('table_bgcolor').value = color;
			document.getElementById('tablebgchosencolor').style.backgroundColor = color;						
		}
		
		if( action === 3 ) {
			document.getElementById('tr_bgcolor').value = color;
			document.getElementById('trbgchosencolor').style.backgroundColor = color;
		}
		
		if( action === 4 ) {
			document.getElementById('td_bgcolor').value = color;
			document.getElementById('tdbgchosencolor').style.backgroundColor = color;
		}
		
		if( action === 5 ) {
			document.getElementById('td_border_top_color').value = color;
			document.getElementById('td_border_top_chosen_color').style.backgroundColor = color;
		}

		if( action === 6 ) {
			document.getElementById('td_border_bottom_color').value = color;
			document.getElementById('td_border_bottom_chosen_color').style.backgroundColor = color;
		}
		
		if( action === 7 ) {
			document.getElementById('td_border_right_color').value = color;
			document.getElementById('td_border_right_chosen_color').style.backgroundColor = color;
		}
		
		if( action === 8 ) {
			document.getElementById('td_border_left_color').value = color;
			document.getElementById('td_border_left_chosen_color').style.backgroundColor = color;
		}
		
		this.updateStyle();
	}
}

/**
 * Заполняем значения полей формы на основе свойств таблицы
 */
Dialog.prototype.init = function() {
	this.buttons = Array( 'tbar1', 'tbar2', 'tbar3' );
	this.screens = Array( 'tab_one', 'tab_two', 'tab_three' );
	this.curBorder;
		
	if( this.Editor.curTable.style.borderCollapse ) {
		if( this.Editor.curTable.style.borderCollapse === "collapse" ) {
			document.getElementById('collapse').checked = true;
		}
	}
	
	//--------------------------------
	// Свойства таблицы
	//--------------------------------
	
	document.getElementById('table_border').value = this.Editor.curTable.getAttribute("BORDER");
	
	this.curBorder = this.Editor.curTable.getAttribute("BORDER");
	
	document.getElementById('table_cellpadding').value = this.Editor.curTable.getAttribute("CELLPADDING");
	document.getElementById('table_cellspacing').value = this.Editor.curTable.getAttribute("CELLSPACING");
	
	if( this.Editor.curTable.getAttribute("WIDTH") !== "" ) {
		document.getElementById('table_width').value = this.Editor.curTable.getAttribute("WIDTH");
	}
	if( this.Editor.curTable.style.width !== "" ) {
		document.getElementById('table_width').value = this.Editor.curTable.style.width;
	}
	if( this.Editor.curTable.getAttribute("HEIGHT") !== "" ) {
		document.getElementById('table_height').value = this.Editor.curTable.getAttribute("HEIGHT");
	}
	if( this.Editor.curTable.style.height !== "" ) {
		document.getElementById('table_height').value = this.Editor.curTable.style.height;
	}
	
	document.getElementById('table_align').value = this.Editor.curTable.getAttribute("ALIGN");
	document.getElementById('tablebgchosencolor').style.backgroundColor = this.Editor.curTable.getAttribute("BGCOLOR");
	document.getElementById('table_bgcolor').value = this.Editor.curTable.getAttribute("BGCOLOR");
	document.getElementById('tableborderchosencolor').style.backgroundColor = this.Editor.curTable.getAttribute("BORDERCOLOR");
	document.getElementById('table_bordercolor').value = this.Editor.curTable.getAttribute("BORDERCOLOR");		
	
	//--------------------------------
	// Cell
	//--------------------------------
	
	document.getElementById('td_width').value = this.Editor.curCell.getAttribute("WIDTH");
	document.getElementById('td_height').value = this.Editor.curCell.getAttribute("HEIGHT");
	document.getElementById('td_align').value = this.Editor.curCell.getAttribute("ALIGN");
	document.getElementById('td_valign').value = this.Editor.curCell.getAttribute("VALIGN");		
	document.getElementById('tdbgchosencolor').style.backgroundColor = this.Editor.curCell.getAttribute("BGCOLOR");
	document.getElementById('td_bgcolor').value = this.Editor.curCell.getAttribute("BGCOLOR");
	
	//--------------------------------
	// Cell borders color
	//--------------------------------
	
	document.getElementById('td_border_top_color').value = this.Editor.curCell.style.borderTopColor;
	document.getElementById('td_border_bottom_color').value = this.Editor.curCell.style.borderBottomColor;
	document.getElementById('td_border_right_color').value = this.Editor.curCell.style.borderRightColor;
	document.getElementById('td_border_left_color').value = this.Editor.curCell.style.borderLeftColor;
	
	document.getElementById('td_border_top_chosen_color').style.backgroundColor = this.Editor.curCell.style.borderTopColor;
	document.getElementById('td_border_bottom_chosen_color').style.backgroundColor = this.Editor.curCell.style.borderBottomColor;
	document.getElementById('td_border_right_chosen_color').style.backgroundColor = this.Editor.curCell.style.borderRightColor;
	document.getElementById('td_border_left_chosen_color').style.backgroundColor = this.Editor.curCell.style.borderLeftColor;

	//--------------------------------
	// Cell borders width
	//--------------------------------
	
	document.getElementById('td_border_top_width').value = parseInt( ''+this.Editor.curCell.style.borderTopWidth  + 0 );
	document.getElementById('td_border_bottom_width').value = parseInt( this.Editor.curCell.style.borderBottomWidth + 0 );
	document.getElementById('td_border_right_width').value = parseInt( ''+this.Editor.curCell.style.borderRightWidth + 0 );
	document.getElementById('td_border_left_width').value = parseInt( this.Editor.curCell.style.borderLeftWidth  + 0 );
			
	//--------------------------------
	// Row
	//--------------------------------
	
	document.getElementById('tr_align').value = this.Editor.curRow.getAttribute("ALIGN");
	document.getElementById('tr_valign').value = this.Editor.curRow.getAttribute("VALIGN");
	document.getElementById('trbgchosencolor').style.backgroundColor = this.Editor.curRow.getAttribute("BGCOLOR");
	document.getElementById('tr_bgcolor').value = this.Editor.curRow.getAttribute("BGCOLOR");	
	
	this.updateStyle();	
	this.changeView(0); 	
	this.hideLoadMessage();	
}

/**
 *
 * @returns {boolean}
 */
Dialog.prototype.apply = function() {
	if( document.getElementById('collapse').checked === true ) {
		this.Editor.curTable.style.borderCollapse = "collapse";
	} else {
		this.Editor.curTable.style.borderCollapse = "separate";
	}
	
	//--------------------------------
	//  Cell 
	//--------------------------------
	
	this.Editor.curCell.setAttribute("BGCOLOR", document.getElementById('td_bgcolor').value, 0);
	this.Editor.curCell.setAttribute("VALIGN", document.getElementById('td_valign').value, 0);
	this.Editor.curCell.setAttribute("ALIGN", document.getElementById('td_align').value, 0);
	this.Editor.curCell.setAttribute("WIDTH", document.getElementById('td_width').value,0);	
	this.Editor.curCell.setAttribute("HEIGHT", document.getElementById('td_height').value,0);

	//--------------------------------
	// Cell borders width
	//--------------------------------	
		
	var w_top = parseInt( document.getElementById('td_border_top_width').value );
	var w_bottom = parseInt( document.getElementById('td_border_bottom_width').value );
	var w_right = parseInt( document.getElementById('td_border_right_width').value );
	var w_left = parseInt( document.getElementById('td_border_left_width').value );

	if( w_top 	 !== '' ) this.Editor.curCell.style.borderTopWidth = w_top + 'px';
	if( w_bottom !== '' ) this.Editor.curCell.style.borderBottomWidth = w_bottom + 'px';
	if( w_right  !== '' ) this.Editor.curCell.style.borderRightWidth = w_right + 'px';
	if( w_left 	 !== '' ) this.Editor.curCell.style.borderLeftWidth = w_left + 'px';
	
	//--------------------------------
	// Cell borders color
	//--------------------------------	
	
	var c_top = document.getElementById('td_border_top_color').value;
	var c_bottom = document.getElementById('td_border_bottom_color').value;
	var c_right = document.getElementById('td_border_right_color').value;
	var c_left = document.getElementById('td_border_left_color').value;
	
	if( c_top 	 !== '' ) this.Editor.curCell.style.borderTopColor = c_top;
	if( c_bottom !== '' ) this.Editor.curCell.style.borderBottomColor = c_bottom;
	if( c_right  !== '' ) this.Editor.curCell.style.borderRightColor = c_right;
	if( c_left 	 !== '' ) this.Editor.curCell.style.borderLeftColor = c_left;
		
	//--------------------------------
	// Row 
	//--------------------------------
	
	this.Editor.curRow.setAttribute("BGCOLOR", document.getElementById('tr_bgcolor').value, 0);
	this.Editor.curRow.setAttribute("VALIGN", document.getElementById('tr_valign').value, 0);
	this.Editor.curRow.setAttribute("ALIGN", document.getElementById('tr_align').value, 0);
	
	//--------------------------------
	//  Table 
	//--------------------------------
	
	this.Editor.curTable.setAttribute("BGCOLOR", document.getElementById('table_bgcolor').value, 0);
	this.Editor.curTable.setAttribute("BORDER", document.getElementById('table_border').value, 0);
	this.Editor.curTable.setAttribute("CELLSPACING", document.getElementById('table_cellspacing').value, 0);
	this.Editor.curTable.setAttribute("CELLPADDING", document.getElementById('table_cellpadding').value, 0);	
	this.Editor.curTable.setAttribute("WIDTH", document.getElementById('table_width').value, 0);
	
	if( this.Editor.curTable.style.width ) {
		this.Editor.curTable.style.width = document.getElementById('table_width').value;
	}
	
	this.Editor.curTable.setAttribute("HEIGHT", document.getElementById('table_height').value, 0);
	
	if( this.Editor.curTable.style.height ) {
		this.Editor.curTable.style.height = document.getElementById('table_height').value;
	}
	
	this.Editor.curTable.setAttribute("ALIGN", document.getElementById('table_align').value, 0);
	this.Editor.curTable.setAttribute("BORDERCOLOR", document.getElementById('table_bordercolor').value, 0);	
	
	//--------------------------------
	// Column
	//--------------------------------
	
	var cellidx = this.Editor.curCell.cellIndex
	var rows = this.Editor.curTable.getElementsByTagName('TR')
	var n = rows.length
	
	for( var i = 0; i < n; i++ ) {
		if( rows[i].childNodes[cellidx] ) {
			if( rows[i].childNodes[cellidx] !== this.Editor.curCell ) {
				if( rows[i].childNodes[cellidx].width ) {
					if( rows[i].childNodes[cellidx].rowSpan === this.Editor.curCell.rowSpan ) {
						rows[i].childNodes[cellidx].setAttribute( "WIDTH", document.getElementById('td_width').value, 0 );
					}
				}
			}	
		}
	}
	
	//--------------------------------
	// Row
	//--------------------------------
	
	var cells = this.Editor.curRow.getElementsByTagName('TD')
	var n = cells.length
	
	for( var i = 0; i < n; i++ ) {
		if( cells[i] !== this.Editor.curCell ) {
			if( cells[i].height ) {
				if( cells[i].colSpan === this.Editor.curCell.colSpan ) {
					cells[i].setAttribute("HEIGHT", document.getElementById('td_height').value,0);
				}
			}
		}	
	}
	
	this.Editor.edit_object.focus();
	
	if( document.getElementById('table_border').value === '0' ) {
		this.Editor.show_borders();		
	} 
	else if(( this.Editor.border_visible === true )
		 && ( this.curBorder === 0 )
		 && ( document.getElementById('table_border').value !== this.curBorder ))
	{
		this.Editor.hide_borders();
		this.Editor.show_borders();
	}
	                          
	this.Editor.addUndoLevel();
	this.closeWindow();
	return false;
}

/**
 *
 */
Dialog.prototype.updateStyle = function() {
	//--------------------------------
	// Table
	//--------------------------------
			
	document.getElementById('tbl').borderColor = document.getElementById('table_bordercolor').value;
	
	if( document.getElementById('collapse').checked === true ) {
		document.getElementById('tbl').style.borderCollapse = "collapse";
	} else {
		document.getElementById('tbl').style.borderCollapse = "separate";
	}
	
	document.getElementById('tbl').setAttribute("border",document.getElementById('table_border').value);
	document.getElementById('tbl').cellPadding = document.getElementById('table_cellpadding').value; 
	document.getElementById('tbl').cellSpacing = document.getElementById('table_cellspacing').value;
	document.getElementById('tbl').style.backgroundColor = document.getElementById('table_bgcolor').value;
	document.getElementById('tbl').setAttribute("align",document.getElementById('table_align').value);	
	
	//--------------------------------	
	// Row
	//--------------------------------
	
	document.getElementById('tbl_tr').setAttribute("vAlign",document.getElementById('tr_valign').value);
	document.getElementById('tbl_tr').setAttribute("align",document.getElementById('tr_align').value);
	document.getElementById('tbl_tr').style.backgroundColor = document.getElementById('tr_bgcolor').value;
	
	//--------------------------------
	// Cell
	//--------------------------------		
	
	document.getElementById('tbl_td').setAttribute("vAlign",document.getElementById('td_valign').value);
	document.getElementById('tbl_td').setAttribute("align",document.getElementById('td_align').value);
	document.getElementById('tbl_td').style.backgroundColor = document.getElementById('td_bgcolor').value;

	//--------------------------------
	// Cell borders
	//--------------------------------		

	document.getElementById('tbl_td').style.borderTopColor = document.getElementById('td_border_top_color').value;
	document.getElementById('tbl_td').style.borderBottomColor = document.getElementById('td_border_bottom_color').value;
	document.getElementById('tbl_td').style.borderRightColor = document.getElementById('td_border_right_color').value;
	document.getElementById('tbl_td').style.borderLeftColor = document.getElementById('td_border_left_color').value;

	//--------------------------------
	// Cell width
	//--------------------------------		

	document.getElementById('tbl_td').style.borderTopWidth = document.getElementById('td_border_top_width').value;
	document.getElementById('tbl_td').style.borderBottomWidth = document.getElementById('td_border_bottom_width').value;
	document.getElementById('tbl_td').style.borderRightWidth = document.getElementById('td_border_right_width').value;
	document.getElementById('tbl_td').style.borderLeftWidth = document.getElementById('td_border_left_width').value;
}	
