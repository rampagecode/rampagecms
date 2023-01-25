//=========================================================================
//
//
//
//=========================================================================


Editor.prototype.on_mouse_down_tab = function( srcElement ) 
{
  	// document.getElementById(this.id+"_load_message").style.display ='block'
  
	if( srcElement.className != 'tbuttonUp' )
	{
  		srcElement.className = 'wpTButtonMouseDown'
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.showDesign = function() 
{
	if( this.html_mode == true ) 
	{
    	if( document.getElementById( this.id + '_designTab' )) 
    	{
      		document.getElementById( this.id + "_load_message" ).style.display = 'block'
			
			oCurrentEditor = this;
      		
      		setTimeout( "oCurrentEditor.on_enter_tab_one()", 1 );
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.on_enter_tab_one = function() 
{
	if( this.html_mode == true ) 
	{
    	var tab_one = document.getElementById( this.id + '_tab_one' );
    	
    	if( this.is_ie ) 
    	{
      		tab_one.style.display = 'block'
    	} 
    	else 
    	{
      		document.getElementById( this.id + '_editFrame' ).style.width = '100%'
			
      		tab_one.style.visibility = "visible"
      		tab_one.style.height = ''
    	}
    	
		document.getElementById( this.id + '_tab_two').style.display = "none"
		document.getElementById( this.id + '_tab_three').style.display = "none"
		
    	if( document.getElementById( this.id + '_designTab' ))
    	{
      		document.getElementById( this.id + '_designTab').className = "wpTButtonUp"
      	}

    	if( document.getElementById( this.id + '_sourceTab' ))
    	{
      		document.getElementById( this.id + '_sourceTab').className = "wpTButtonDown"
      	}

    	if( document.getElementById( this.id + '_previewTab' ))
    	{
      		document.getElementById( this.id+'_previewTab' ).className = "wpTButtonDown"
      	}

    	this.send_to_edit_object()
    	
    	this.html_mode 	 = false
    	this.preview_mode = false
  	}
  	
  	document.getElementById( this.id + "_load_message" ).style.display = 'none';
  	
  	if( this.is_ie )
  	{
    	this.edit_object.focus()
    }
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.showCode = function() 
{
	if( this.html_mode == false || this.preview_mode == true ) 
	{
    	if( document.getElementById( this.id + '_sourceTab' )) 
    	{
      		document.getElementById( this.id + "_load_message" ).style.display = 'block'
      		
			oCurrentEditor = this;
      		
      		setTimeout( "oCurrentEditor.on_enter_tab_two()", 1 );			      		
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.on_enter_tab_two = function() 
{
	if( this.html_mode == false || this.preview_mode == true ) 
	{
    	this.hide_menu();
    	
    	var tab_one = document.getElementById( this.id + '_tab_one' )
    	
    	if( this.is_ie ) 
    	{
      		tab_one.style.display = 'none'
    	} 
    	else 
    	{
      		document.getElementById( this.id + '_editFrame' ).style.width = '0px'
      		
	      	tab_one.style.visibility = "hidden"
	      	tab_one.style.height = '0px'
    	}
    	
    	document.getElementById( this.id + '_tab_two' ).style.display = "block"
    	document.getElementById( this.id + '_tab_three' ).style.display = "none"
    	
    	this.html_edit_area.style.visibility = "visible"
    	
    	if( document.getElementById( this.id + '_designTab' ))
    	{
       		document.getElementById( this.id + '_designTab').className = "wpTButtonDown"
       	}

    	if( document.getElementById( this.id + '_sourceTab' ))
    	{
      		document.getElementById( this.id + '_sourceTab' ).className = "wpTButtonUp"
      	}

    	if( document.getElementById( this.id + '_previewTab' ))
    	{
      		document.getElementById( this.id + '_previewTab' ).className = "wpTButtonDown"
      	}

    	this.html_mode = true
    	
    	if( this.preview_mode == false ) 
    	{
      		this.send_to_html()
    	}
    	
    	this.preview_mode = false
  	}
  	
  	document.getElementById( this.id + "_load_message" ).style.display = 'none'
  	
  	this.html_edit_area.focus()
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.showPreview = function() 
{
	if( this.preview_mode == false ) 
	{
    	if( document.getElementById( this.id + '_previewTab' )) 
    	{
      		document.getElementById( this.id + "_load_message" ).style.display = 'block'
      		      					
			oCurrentEditor = this;
      		
      		setTimeout( "oCurrentEditor.on_enter_tab_three()", 1 );			      		
			
    	}
  	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.on_enter_tab_three = function() 
{
	if( this.preview_mode == false ) 
	{
    	this.hide_menu();
    	
    	var tab_one = document.getElementById( this.id + '_tab_one' )
    	
    	if( this.is_ie ) 
    	{
      		tab_one.style.display = 'none'
    	} 
    	else 
    	{
      		document.getElementById( this.id + '_editFrame' ).style.width = '0px'
      		
      		tab_one.style.visibility = "hidden"
      		tab_one.style.height = '0px'
    	}
    	
    	document.getElementById( this.id + '_tab_two' ).style.display = "none"
    	document.getElementById( this.id + '_tab_three' ).style.display = "block"
    	
    	if( document.getElementById( this.id + '_designTab' ))
    	{
       		document.getElementById( this.id + '_designTab').className = "wpTButtonDown"
       	}

    	if( document.getElementById( this.id + '_sourceTab' ))
    	{
      		document.getElementById( this.id + '_sourceTab').className = "wpTButtonDown"
      	}

    	if( document.getElementById( this.id + '_previewTab' ))
    	{
      		document.getElementById( this.id + '_previewTab').className = "wpTButtonUp"
      	}

    	if( this.html_mode == false ) 
    	{
      		this.send_to_html()
    	}
    	
    	this.html_mode 	  = true
    	this.preview_mode = true
    	
    	this.send_to_preview()
  	}
  	
  	document.getElementById( this.id + "_load_message" ).style.display = 'none'
  	
  	this.previewFrame.focus()
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.send_to_preview = function( dontFocus ) 
{
	this.previewFrame.document.open( 'text/html', 'replace' )
	this.previewFrame.document.write( this.getPreviewCode() )
	this.previewFrame.document.close()
	
	if( ! dontFocus ) 
	{
		this.previewFrame.focus()
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getPreviewCode = function() 
{
	if( this.html_mode == false ) 
	{
    	this.send_to_html()
  	}
  	
  	var str = this.baseURL;
  	
  	if( this.stylesheet != '' ) 
  	{
    	var num = this.stylesheet.length;
    	
    	for( var i = 0; i < num; i++ ) 
    	{
      		str += '<link rel="stylesheet" href="' + this.stylesheet[i] + '" type="text/css">'
    	}
  	}
  	
  	return str + this.html_edit_area.value
}

//=========================================================================
//
// Увеличение и уменьшение размеров окна редактора
//
//=========================================================================


Editor.prototype.resizeWindow = function( plus, buttonsender, step_inc, step_dec )
{
	// Изменяем высоту окна дизайна
	
	h = parseInt( document.getElementById( this.id + "_editFrame" ).height );
	
	document.getElementById( this.id + "_editFrame" ).height = plus ? h + step_inc : ( h - step_dec > 0 ? h - step_dec : h );
	
	// Изменяем высоту окна HTML-кода
	
	h = parseInt( document.getElementById( this.id ).style.height );
	
	document.getElementById( this.id ).style.height = plus ? h + step_inc : ( h - step_dec > 0 ? h - step_dec : h );
	
	// Изменяем высоту окна просмотра
	
	h = parseInt( document.getElementById( this.id + "_previewFrame" ).style.height );
	
	document.getElementById( this.id + "_previewFrame" ).style.height = plus ? h + step_inc : ( h - step_dec > 0 ? h - step_dec : h );

	// Возвращаем цвет нажатой кнопке 
		
	buttonsender.className = 'wpTButtonDown';	
}


