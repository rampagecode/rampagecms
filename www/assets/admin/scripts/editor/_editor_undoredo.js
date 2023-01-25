//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.addUndo = function(l) 
{
	var b;
		
	if( l ) 
	{
		this.undoLevels[ this.undoLevels.length ] = l;
		return true;
	}

	if( this.typingUndoIndex != -1 ) 
	{
		this.undoIndex = this.typingUndoIndex;		 		
	}

	var sNewHTML = this.edit_object.document.body.innerHTML.trim();
		
	if( this.undoLevels[ this.undoIndex ] && sNewHTML != this.undoLevels[ this.undoIndex ].content ) 
	{		
		// Need time to compress
				
		if( this.undoLevels.length > 15 ) // 15 levels undo 
		{
			for( var i = 0; i < this.undoLevels.length - 1; i++ ) 
			{			
				this.undoLevels[ i ] = this.undoLevels[ i +  1 ];
			}

			this.undoLevels.length--;
			this.undoIndex--;
		}		
						
		b = this.undoBookmark;
		
		if( ! b )
		{
			b = this.getBookmark();			
		}

		this.undoIndex++;
		
		this.undoLevels[ this.undoIndex ] = { content : sNewHTML, bookmark : b };

		this.undoLevels.length = this.undoIndex + 1;
		
		return true;		
	}

	return false;
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.getBookmark = function( simple ) 
{
	var rng = this.getRng();
	var doc = this.edit_object.document;
	
	var sp, le, s, e, nl, i, si, ei;
	var trng, sx, sy, xx = -999999999;

	sx = doc.body.scrollLeft + doc.documentElement.scrollLeft;
	sy = doc.body.scrollTop + doc.documentElement.scrollTop;

	if( ! this.is_ie )
	{	
		return { rng : rng, scrollX : sx, scrollY : sy };
	}
	else if( simple )
	{
		return { rng : rng };
	}
	else if( rng.item ) 
	{
		e = rng.item(0);

		nl = doc.getElementsByTagName( e.nodeName );
		
		for( i = 0; i < nl.length; i++ ) 
		{
			if( e == nl[i] ) 
			{
				sp = i;
				break;
			}
		}

		return {
			tag : e.nodeName,
			index : sp,
			scrollX : sx,
			scrollY : sy
		};
	} 
	else 
	{
		trng = rng.duplicate();
		trng.collapse(true);
		sp = Math.abs( trng.move( 'character', xx ));

		trng = rng.duplicate();
		trng.collapse(false);
		le = Math.abs( trng.move( 'character', xx )) - sp;

		return {
			start 	: sp,
			length 	: le,
			scrollX : sx,
			scrollY : sy
		};
	}	

	if( ! this.is_ie ) 
	{
		s = this.getParentElement( rng.startContainer );
		
		for( si = 0; si < s.childNodes.length && s.childNodes[ si ] != rng.startContainer; si++);

		nl = doc.getElementsByTagName( s.nodeName );
		
		for( i = 0; i < nl.length; i++ ) 
		{
			if( s == nl[i] ) 
			{
				sp = i;
				break;
			}
		}

		e = this.getParentElement( rng.endContainer );
		
		for( ei = 0; ei < e.childNodes.length && e.childNodes[ei] != rng.endContainer; ei++ );

		nl = doc.getElementsByTagName( e.nodeName );
		
		for( i = 0; i < nl.length; i++ ) 
		{
			if( e == nl[i] ) 
			{
				le = i;
				break;
			}
		}

		return {
			startTag 	: s.nodeName,
			start 		: sp,
			startIndex 	: si,
			endTag 		: e.nodeName,
			end 		: le,
			endIndex 	: ei,
			startOffset : rng.startOffset,
			endOffset 	: rng.endOffset,
			scrollX 	: sx,
			scrollY 	: sy
		};
	}

	return null;
}
/*
moveToBookmark : function(bookmark) {
	var rng, nl, i;
	var inst = this.instance;
	var doc = inst.getDoc();
	var win = inst.getWin();
	var sel = this.getSel();

	if (!bookmark)
		return false;

	if (tinyMCE.isSafari) {
		sel.setBaseAndExtent(bookmark.startContainer, bookmark.startOffset, bookmark.endContainer, bookmark.endOffset);
		return true;
	}

	if (tinyMCE.isMSIE) {
		if (bookmark.rng) {
			bookmark.rng.select();
			return true;
		}

		win.focus();

		if (bookmark.tag) {
			rng = inst.getBody().createControlRange();

			nl = doc.getElementsByTagName(bookmark.tag);

			if (nl.length > bookmark.index) {
				try {
					rng.addElement(nl[bookmark.index]);
				} catch (ex) {
					// Might be thrown if the node no longer exists
				}
			}
		} else {
			rng = inst.getSel().createRange();
			rng.moveToElementText(inst.getBody());
			rng.collapse(true);
			rng.moveStart('character', bookmark.start);
			rng.moveEnd('character', bookmark.length);
		}

		rng.select();

		win.scrollTo(bookmark.scrollX, bookmark.scrollY);
		return true;
	}

	if (tinyMCE.isGecko && bookmark.rng) {
		sel.removeAllRanges();
		sel.addRange(bookmark.rng);
		win.scrollTo(bookmark.scrollX, bookmark.scrollY);
		return true;
	}

	if (tinyMCE.isGecko) {
//		try {
			rng = doc.createRange();

			nl = doc.getElementsByTagName(bookmark.startTag);
			if (nl.length > bookmark.start)
				rng.setStart(nl[bookmark.start].childNodes[bookmark.startIndex], bookmark.startOffset);

			nl = doc.getElementsByTagName(bookmark.endTag);
			if (nl.length > bookmark.end)
				rng.setEnd(nl[bookmark.end].childNodes[bookmark.endIndex], bookmark.endOffset);

			sel.removeAllRanges();
			sel.addRange(rng);
/*		} catch {
			// Ignore
		}*/
/*
		win.scrollTo(bookmark.scrollX, bookmark.scrollY);
		return true;
	}

	return false;
},
*/

Editor.prototype.startTyping = function()
{
	if( this.typingUndoIndex == -1 ) 
	{
		this.typingUndoIndex = this.undoIndex;
		this.addUndoLevel();					
	}
}				

Editor.prototype.endTyping = function()
{
	if( this.typingUndoIndex != -1 ) 
	{
		this.addUndoLevel();
		this.typingUndoIndex = -1;					
	}
}

/*
Editor.prototype.beginUndoLevel = function()
{
	this.undoRedoLevel = false;
}

Editor.prototype.endUndoLevel = function()
{
	this.undoRedoLevel = true;
	this.addUndoLevel();
}
*/

Editor.prototype.addUndoLevel = function()
{
	if( this.undoRedoLevel ) 
	{
		if( ! this.addUndo() )
		{
			// alert('Warning: Cannot undo');
		}
	}
}
		
Editor.prototype.doUndo = function()
{
	this.endTyping();
	this.undo();	
}

Editor.prototype.doRedo = function()
{
	this.endTyping();
	this.redo();	
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.undo = function() 
{
	if( this.undoIndex > 0 ) 
	{
		this.undoIndex--;
		this.setCode( this.undoLevels[ this.undoIndex ].content );		
		
		//inst.selection.moveToBookmark( this.undoLevels[ this.undoIndex ].bookmark );
	}
}

//=========================================================================
//
//
//
//=========================================================================

Editor.prototype.redo = function() 
{		
	if( this.undoIndex < ( this.undoLevels.length - 1 )) 
	{
		this.undoIndex++;
		this.setCode( this.undoLevels[ this.undoIndex ].content );
		
		//inst.selection.moveToBookmark( this.undoLevels[ this.undoIndex ].bookmark );			
	}	
}



