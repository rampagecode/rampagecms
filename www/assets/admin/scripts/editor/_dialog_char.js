function init() {
	var kids = document.getElementById('CharacterTable').getElementsByTagName('TD');
	
	for( var i = 0; i < kids.length; i++ ) {
		kids[i].onmouseover = m_over;
		kids[i].onmouseout = m_out;
		kids[i].onclick = character;
	}
	
	oDialog.hideLoadMessage();
}

function m_over() {
	if( curHL === this ) return
	
	this.style.backgroundColor = "F7FBFF"
	this.style.color = "#314D6B"
}

function m_out() {
	if( curHL === this ) return
	
	this.style.backgroundColor = "EFF3F7"
	this.style.color = "#314D6B"
}

function character() {
	this.style.backgroundColor = "F7FBFF"
	document.getElementById('insert').value = this.title;
	document.getElementById('characterspan').innerHTML = document.getElementById('insert').value;
	
	if( curHL ) {
		curHL.style.backgroundColor = "#EFF3F7";
		curHL.style.color = "#314D6B";
	}

	curHL = this
}

function man_character() {
	document.getElementById('characterspan').innerHTML = document.getElementById('insert').value;
	
	if( curHL ) {
		curHL.style.backgroundColor = "#EFF3F7";
		curHL.style.color = "#314D6B";
	}
	
	curHL = null
}

function finish() {
	var code = document.getElementById('insert').value;
	oDialog.Editor.insert_code( code );
	oDialog.Editor.addUndoLevel();
	oDialog.closeWindow();
	return false;
}