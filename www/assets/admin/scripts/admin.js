var uagent    = navigator.userAgent.toLowerCase();
var is_safari = (( uagent.indexOf('safari') != -1) || (navigator.vendor == "Apple Computer, Inc."));
var is_opera  = ( uagent.indexOf('opera') != -1 );
var is_webtv  = ( uagent.indexOf('webtv') != -1 );
var is_ie     = (( uagent.indexOf('msie') != -1 ) && (!is_opera) && (!is_safari) && (!is_webtv));

function zoom( url, width, height, name, printable ) {
	if( !name ) {
		for( var i = 0; i < 16; i++ ) {
			name += String.fromCharCode( Math.round( Math.random() * 25 ) + 97 );
		}
	}

	width = parseInt( width );
	height = parseInt( height );

	if( is_safari ) {
		width  += 4;
		height += 4;
	}

	var scrollbars = ( screen.availHeight < height ) ? 1 : 0;
	var hwnd = window.open( '', name, 'height='+height+',width='+width+',scrollbars='+scrollbars+',alwaysraised=1' );

	with( hwnd.document )
	{
		write('<html><head><title>Фото</title></head><body style="margin:0;padding:0">');
		write('<img src="'+url+'" border="0">');

		if( printable ) {
			write('<div style="position:absolute;left:0;top:0;">');
			write('<span style="background:#fff;color:#222;font-family:Verdana;font-size: 11px;cursor:pointer;padding:2px;');
			write('text-decoration:underline;" onclick="this.style.display=\'none\';window.print();">Распечатать</span></div>');
		}

		write('</body></html>');
		close();
	}

	hwnd.focus();
}

function toggleViews(...ids) {
	ids.forEach(id => id && toggleView(id));
}

function toggleView( id ) {
	var itm = id && document.getElementById( id );
	if( itm ) {
		if( itm.style.display === "none" ) {
			itm.style.display = "";
		} else {
			itm.style.display = "none";
		}
	}
}

function doConfirm( url, msg ) {
	if ( ! msg ) {
		msg = 'УДАЛИТЬ?!\n\n Нажмите ОК для подтверждения';
	}

	if( window.confirmation ) {
		window.confirmation( msg, confirm => {
			if( confirm ) {
				document.location.href = url;
			}
		});
	}
	else if( confirm( msg )) {
		document.location.href = url;
	}
}

function jumpToSheet( url ) {
	msg = "Все не сохраненные изменения на странице будут потеряны!\n\nПродолжить?";
	num = document.getElementById( 'jumptosheetnum' ).value;

	if( window.confirmation ) {
		window.confirmation( msg, confirm => {
			if( confirm ) {
				document.location.href = url + '&goto=' + num;
			}
		});
	}
	else if( confirm( msg )) {
		document.location.href = url + '&goto=' + num;
	}
}
