function init() {
	if( oDialog.Editor.color_swatches === '' ) {
		document.getElementById('web_color_container').style.display = 'block';
	} else {
		var color_swatches = oDialog.Editor.color_swatches;
		var colors = color_swatches.split(',');		
		var colorTable = '<table id="site_colortable" height="30" border="0" cellspacing="1" cellpadding="0" class="colortable" style="background:#EEF2F7">';
		var cellCount = 0;
		
		for( var i = 0; i < colors.length; i++ ) {
			if( cellCount === 0 ) {
				colorTable += '<tr>';
			}
			
			colorTable += '<td class="colorCell" bgcolor="' + colors[i] + '">&nbsp;&nbsp;&nbsp;</td>';
			
			if( cellCount === 18 ) {
				colorTable += '</tr>';
				cellCount = 0;
			} else {
				cellCount ++;
			}
		}
		
		if( cellCount !== 0 ) {
			colorTable += '</tr>';
		}
		
		colorTable += '<tr><td height="10" colspan="19" style="background:#EEF2F7"></td></tr></table>';
		
		document.getElementById('site_color_container').innerHTML = colorTable;		
	}
	
	kids = document.getElementsByTagName('TD');
	
	for( var i = 0; i < kids.length; i++ ) {
		if( kids[i].className == "colorCell" ) {
			kids[i].onmouseover = m_over;
			kids[i].onmouseout = m_out;
			kids[i].onclick = m_click;
		}
	}
	
	document.getElementById('selcolor').focus(); 
	oDialog.hideLoadMessage();
}

function m_click() {
	document.getElementById('selcolor').value = this.bgColor;
	document.getElementById('chosencolor').style.backgroundColor = this.bgColor;
	this.style.border = '1px solid #FFFFFF'
	
	if( curHL ) {
		curHL.style.border = "1px solid #000000"
	}
	
	curHL = this
}

function m_over() {
	if( curHL === this ) return
	
	document.getElementById('rgb').innerHTML = this.bgColor;
	document.getElementById('colordisplay').style.backgroundColor = this.bgColor;
	this.style.border = "1px dashed #FFFFFF";
}

function m_out() {
	if( curHL === this ) return
	
	document.getElementById('rgb').innerText = " ";
	document.getElementById('colordisplay').bgcolor="threedface";
	this.style.border = "1px solid #000000"
}

function end() {
	if( oDialog.parentWindow && oDialog.parentWindow.oDialog ) {
		oDialog.parentWindow.oDialog.docolor( _get_action, document.getElementById('selcolor').value );
	} else {
		oDialog.Editor.docolor( _get_action, document.getElementById('selcolor').value );
	}
	
	oDialog.closeWindow();
	return false;
}

//=================================================================================
// Функции генерации палитры
//=================================================================================

//----------------------------------
// HexRGB Vector
//----------------------------------

var hv = Array( '00', '33', '66', '99', 'cc', 'ff' ); 

/**
 * Increment Color Vector
 * @param x
 * @param v
 * @returns {*}
 */
function inc_v( x, v ) {
	v = v.split(',');
	
	if( ++v[x] > 5 ) {
		if( x !== 0 ) {
			v[x] = 0; v = inc_v( x-1, v.toString());
		}								
	}
	return v;
}

/**
 * Color Vector To 0xRGB Code
 * @param v
 * @returns {string}
 */
function cv2hcc( v ) {
	return hv[v[0]] + hv[v[1]] + hv[v[2]];
}

/**
 * Generate a palette row
 * @param x
 * @param v
 * @returns {string}
 */
function gen_row( x, v ) {
	ps = '';
			
	for( var i = 0; i < 17; i++ ) {
		v = inc_v( x, v );
												
		ps += '<td class="colorCell" bgcolor="#' + cv2hcc( v ) + '">&nbsp;&nbsp;&nbsp;</td>';		
		
		v = v.toString();
	}			
	
	return '<tr>'+ps+'</tr>';
}

/**
 * Generate a whole palette
 * @returns {string}
 */
function palette() {
	var r = '';
	var v = Array( 0, 0, 0 );
	
	for( var i = 0; i < 6; i++ ) {
		r += gen_row( 1, v.toString());
		v = inc_v( 2, v.toString());				
	}			
	
	var v = Array( 3, 0, 0 );
	
	for( var i = 0; i < 6; i++ ) {
		r += gen_row( 1, v.toString());
		v = inc_v( 2, v.toString());		
	}			
		
	return r;
}