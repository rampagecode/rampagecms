/**
 * Add or remove table rows
 * @param action
 */
Editor.prototype.processRow = function( action ) {
	var sDialogUrl = this.adsess_url + '&u=r&a=tab&i=addrow';
	
	if( this.isInside( 'TD' )) {
  		this.getTable();
  		
  		var idx 		= 0
  		var rowidx 		= 0
  		var tr 			= this.curRow
  		var numcells 	= tr.childNodes.length
  		
  		if( action === "choose" ) {
    		this.openDialog( sDialogUrl, 'modal', 275, 150 );
    		return;
  		}
  		
  		if(( action === "" ) || ( action == null )) {
    		return;
  		}
  		
  		if( action === "addabove" ) {
    		while( tr ) {
      			if( tr.tagName === "TR" ) {
        			rowidx++;        			
        			tr = tr.previousSibling;
      			}
    		}
    		
    		rowidx -= 1;
  		} else {
    		if( action === "addbelow" ) {
      			while( tr ) {
        			if( tr.tagName === "TR" ) {
          				rowidx++;
          				tr = tr.previousSibling;
        			}
      			}
    		}
  		}
  		
  		var tbl = this.curTable;
  		
  		if( ! tbl ) {
    		alert("Could not " + action + " row.");
    		return;
  		}
  		
  		if(( action === "addabove" ) || ( action === "addbelow" )) {
    		var r = tbl.insertRow( rowidx );
	    		
    		for( var i = 0; i < numcells; i++ ) {
      			var c = r.appendChild( this.edit_object.document.createElement( "TD" ));
      			
      			if( this.curCell.colSpan ) {
        			c.colSpan = this.curRow.childNodes[i].colSpan;
      			}
      			
      			c.width 	= this.curRow.childNodes[i].width;
      			c.vAlign 	= 'top';
      			c.innerHTML = this.tdInners;
      			
      			if( this.border_visible === 1 ) {
        			this.show_borders();
      			}
    		}
	  	} else {
	    	if( this.curTable.getElementsByTagName('TR').length === 1 ) {
	      		return;
	    	}
	    	
	    	while( tr ) {
	      		if( tr.tagName === "TR" ) {
	        		rowidx++;
	        		tr = tr.previousSibling;
	      		}
	    	}
	    	
	    	rowidx -= 1;
	    	tbl.deleteRow( rowidx );
	    }
    
	    this.curCell 	= null;    
	    this.curRow		= null;
	    this.curTable	= null;
	}
	
  	this.edit_object.focus();   
  	this.addUndoLevel();
}

/**
 * Add or remove a column
 * @param action
 */
Editor.prototype.processColumn = function( action ) {
	var sDialogUrl = this.adsess_url + '&u=r&a=tab&i=addcol';
	
	if( this.isInside( 'TD' )) {
  		if( action === "choose" ) {
    		this.openDialog( sDialogUrl, 'modal', 275, 150 );
    		return;  			
  		}

  		if(( action === "" ) || ( action == null )) {
    		return
  		}
  		
  		this.getTable();
  		
  		// store cell index in a var because the cell will be
  		// deleted when processing the first row
  		
  		var cellidx = this.curCell.cellIndex
  		var tbl 	= this.curTable
  		
  		if( ! tbl ) {
    		alert("Could not " + action + " column.");
    		return;
  		}
  		
  		// now we have the table containing the cell
  		
  		this.add_remove_columns( tbl, cellidx, action );
  	}
  	
  	this.edit_object.focus();
  	this.addUndoLevel();
}

/**
 * Remove columns
 * @param tbl
 * @param cellidx
 * @param action
 */
Editor.prototype.add_remove_columns = function( tbl, cellidx, action ) {
	if( ! tbl.childNodes.length ) {
    	return;
    }
    
    var n = tbl.childNodes.length;
    
    for( var i = 0; i < n; i++ ) {
		if( tbl.childNodes[i].tagName === "TR" ) {
        	var cell = tbl.childNodes[i].childNodes[ cellidx ];
        	
        	if( ! cell ) {
          		break;
          	}
          	
        	if( action === "addleft" ) {
          		cell.parentNode.insertBefore( this.edit_object.document.createElement("TD"), cell );
        	} else {
          		if( action === "addright" ) {
            		cell.parentNode.insertBefore( this.edit_object.document.createElement("TD"), cell.nextSibling );
          		} else {
            		if( cell.rowSpan > 1 ) {
              			i += ( cell.rowSpan - 1 );
            		}
            		
            		if( this.curRow.getElementsByTagName('TD').length === 1 ) {
              			return;
            		}
            		
            		if( cell.colSpan < 2 ) {
              			tbl.childNodes[i].removeChild( cell );
            		} else {
              			cell.colSpan -= 1;
            		}
          		}
        	}
      	} else {
      		this.add_remove_columns( tbl.childNodes[i], cellidx, action );
    	}
  	}
  	
  	this.reprocess_columns();
}

/**
 * Split cells
 */
Editor.prototype.splitCell = function() {
	var sDialogUrl = this.adsess_url + '&u=r&a=tab&i=unmrgcell'; 
	
	if( this.isInside( 'TD' )) {
    	this.getTable();
    	
    	if(( this.curCell.colSpan < 2 ) && ( this.curCell.rowSpan < 2 )) {
      		alert( this.lng['only_split_merged_cells'] );
    	}
    	
    	if(( this.curCell.colSpan >= 2 ) && ( this.curCell.rowSpan < 2 )) {
      		this.unMergeRight();
    	} 
    	else if(( this.curCell.rowSpan >= 2 ) && ( this.curCell.colSpan < 2 )) {
      		this.unMergeDown()
    	} 
    	else if(( this.curCell.rowSpan >= 2 ) && ( this.curCell.colSpan >= 2 )) {
    		this.openDialog( sDialogUrl, 'modal', 275, 150 );
    	}
  	}  	
}

/**
 * Merge cells
 */
Editor.prototype.mergeCell = function() {
	var sDialogUrl = this.adsess_url + '&u=r&a=tab&i=mrgcell';
	
	if( this.isInside( 'TD' )) {
		this.openDialog( sDialogUrl, 'modal', 275, 150 );
  	}
}

/**
 * Merge cells to the right
 */
Editor.prototype.mergeRight = function() {
	if( this.isInside( 'TD' )) {
    	this.getTable();
    	
    	if( ! this.curCell.nextSibling ) {
      		alert( this.lng['no_cell_right'] );
      		return;
    	}
    	
    	// prevent to merge rows with different row spans
    	if( this.curCell.rowSpan !== this.curCell.nextSibling.rowSpan ) {
      		alert( this.lng['different_row_spans'] );
      		return;
    	}
    	
    	if( this.curCell.nextSibling.innerHTML.toLowerCase() !== this.tdInners ) {
      		if( this.curCell.innerHTML.toLowerCase() === this.tdInners ) {
        		this.curCell.innerHTML = this.curCell.nextSibling.innerHTML;
      		} else {
        		this.curCell.innerHTML += this.curCell.nextSibling.innerHTML;
      		}
    	}
    	
    	this.curCell.setAttribute( "WIDTH", '', 0 );
	    this.curCell.nextSibling.setAttribute( "WIDTH", '', 0 );
	    this.curCell.colSpan += this.curCell.nextSibling.colSpan;
	    this.curRow.removeChild( this.curCell.nextSibling );
	    this.curCell = null;
	    this.curRow	= null;
    	this.curTable = null;
  	}
  	
  	this.edit_object.focus();
  	this.addUndoLevel();
}

/**
 *
 */
Editor.prototype.unMergeRight = function() {
	if( this.isInside( 'TD' )) {
    	this.getTable();
    	
	    if( this.curCell.colSpan < 2 ) {
	    	alert( this.lng['only_split_merged_cells'] );
	    } else {
	    	this.curCell.colSpan = this.curCell.colSpan - 1;
	    	
	      	var newCell = this.curCell.parentNode.insertBefore(
		  		this.edit_object.document.createElement("TD"),
				this.curCell.nextSibling
			);
	      	
	      	newCell.rowSpan = this.curCell.rowSpan;
			
	      	this.curCell.setAttribute("WIDTH", '', 0);
	      	newCell.setAttribute("WIDTH", '', 0);
	      	newCell.innerHTML = this.tdInners;
	      	newCell.vAlign = 'top';
	    }
	    
	    if( this.border_visible === 1 ) {
	  		this.show_borders();
	    }
	    
	    this.curCell = null;
	    this.curRow	 = null;
	    this.curTable = null;
	}
  
  	this.edit_object.focus();
  	this.addUndoLevel();
}

/**
 * Merge with a cell below
 */
Editor.prototype.mergeDown = function() {
	if( this.isInside( 'TD' )) {
    	this.getTable();

    	var numrows = this.curTable.getElementsByTagName('TR').length;
    	var topRowIndex = this.curRow.rowIndex;
    	
    	if( numrows - ( topRowIndex + this.curCell.rowSpan) <= 0 ) {
      		alert( this.lng['different_column_spans'] );
      		return;
    	}
    	
    	if( ! this.curRow.nextSibling ) {
      		alert( this.lng['no_cell_below'] );
      		return;
    	}
    	
    	var bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex ];
    	var bottomRow 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ];

		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 1 ];
		}
		
		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 2 ];
		}
		
		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 3 ];
		}
		
		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 4 ];
		}						

		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 5 ];
		}
		
		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 6 ];
		}
		
		if( bottomCell === undefined ) {
			bottomCell 	= this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan ].childNodes[ this.curCell.cellIndex - 7 ];
		}						
		
    	// prevent merging rows with different col spans
    	if( this.curCell.colSpan !== bottomCell.colSpan ) {
      		alert( this.lng['different_column_spans'] );
      		return;
    	}

    	if( bottomCell.innerHTML.toLowerCase() !== this.tdInners ) {
      		if( this.curCell.innerHTML.toLowerCase() === this.tdInners ) {
        		this.curCell.innerHTML = bottomCell.innerHTML;
      		} else {
        		this.curCell.innerHTML += bottomCell.innerHTML;
      		}
    	}
    	
    	this.curCell.setAttribute( "HEIGHT", '', 0 );
    	this.curCell.rowSpan += bottomCell.rowSpan;
    	bottomRow.removeChild( bottomCell );
		
    	this.curCell		= null;
    	this.curRow		= null;
    	this.curTable	= null;
  	}
	
  	this.edit_object.focus();
  	this.addUndoLevel();
}

/**
 *
 */
Editor.prototype.unMergeDown = function() {
	if( this.isInside( 'TD' )) {
    	this.getTable();
    	
    	if( this.curCell.rowSpan < 2 ) {
      		alert( this.lng['only_split_merged_cells'] );
      		return;
    	}
    	
    	var topRowIndex = this.curCell.parentNode.rowIndex;
    	var newCell = this.curRow.parentNode.childNodes[ topRowIndex + this.curCell.rowSpan - 1 ]
			.appendChild( this.edit_object.document.createElement("TD") );
    	
	    newCell.innerHTML 	= this.tdInners;
	    newCell.vAlign 		= this.curCell.vAlign;
	    newCell.colSpan 	= this.curCell.colSpan;
		
    	this.curCell.rowSpan -= 1;
    	
    	if( this.border_visible === 1 ) {
      		this.show_borders();
    	}
		
	    this.curCell	 = null;
	    this.curRow	 = null;
	    this.curTable = null;
  	}                    	
  	
  	this.edit_object.focus();
  	this.addUndoLevel();
}

/**
 *
 */
Editor.prototype.reprocess_columns = function() {
	var nocolumns 	 = 0;
	var tableRows 	 = this.curTable.getElementsByTagName('TR');
	var tableColumns = tableRows[0].getElementsByTagName('TD');
  	
  	for( var i = 0; i < tableColumns.length; i++ ) {
    	if( tableColumns[i].getAttribute('colSpan') >= 2 ) {
        	nocolumns += tableColumns[i].getAttribute('colSpan');
      	} else {
        	nocolumns += 1;
      	}
  	}

  	var tdwidth = 100 / nocolumns;
  	var tableCells = this.curTable.getElementsByTagName('TD');
  	
  	// resize the columns, insert spacers into cells with no inner html and fix text alignment
  	
  	var n = tableCells.length;
  	
  	for( var i = 0; i < n; i++ ) {
		if( tableCells[i].getAttribute( 'colSpan' ) < 2 ) {
        	tableCells[i].width = tdwidth + '%';
      	}
      	
      	if( tableCells[i].innerHTML === '' ) {
        	tableCells[i].innerHTML = this.tdInners;
      	}
      	
      	if(( tableCells[i].getAttribute('vAlign') === '') || (tableCells[i].getAttribute('vAlign') == null )) {
        	tableCells[i].vAlign = 'top';
      	}
  	}
  	
  	if( this.border_visible === 1 ) {
    	this.show_borders();
  	}
}

