var ModalDialogClass = function() {
    /**
     * @type {HTMLDialogElement[]}
     */
    var dialogs = [];

    /**
     *
     * @type {number}
     */
    var zIndexBase = 999;

    /**
     * @returns {Window}
     */
    function rootWindow() {
        var win = window;

        while( win !== win.parent ) {
            win = win.parent;
        }

        return win;
    }

    /**
     *
     * @param {string} url
     * @param {int} width
     * @param {int} height
     * @returns {HTMLDialogElement}
     */
    function createDialog( url, width, height ) {
        url += '&nocache=' + (new Date().getTime());

        var dialog = document.createElement('dialog');
        dialog.style.backgroundColor = "#EEF2F7";
        dialog.style.boxShadow = '5px 5px 5px 0px rgba(0,0,0,0.3)';
        dialog.style.borderRadius = '12px';
        dialog.style.border = '2px solid #adb6ce';
        dialog.style.margin = '0';
        dialog.style.padding = '0';
        dialog.style.position = 'absolute';
        dialog.style.zIndex = zIndexBase + dialogs.length;
        dialog.style.left = (rootWindow().innerWidth / 2 - width / 2) + 'px';
        dialog.style.top = (rootWindow().innerHeight / 2 - height / 2) + 'px';

        dialog.addEventListener('close', function() {
            dialog.remove();
        });

        var iframe = document.createElement('iframe');
        iframe.style.width = width + 'px';
        iframe.style.height = height + 'px';
        iframe.src = url;
        iframe.style.backgroundColor = '#EEF2F7';
        iframe.style.border = '0 none';
        iframe.addEventListener('load', function () {
            var title = iframe.contentDocument.getElementsByTagName('title');
            if( title.length > 0 ) {
                headerTitle.innerText = title[0].innerText;
                iframe.contentDocument.body.style.backgroundColor = '#EEF2F7';
            } else {
                var frame = iframe.contentDocument.getElementsByTagName('iframe');
                if( frame.length > 0 ) {
                    var title = frame[0].contentDocument.getElementsByTagName('title');
                    if( title.length > 0 ) {
                        headerTitle.innerText = title[0].innerText;
                        iframe.contentDocument.body.style.backgroundColor = '#EEF2F7';
                    } else {
                        frame[0].addEventListener('load', function () {
                            var title = frame[0].contentDocument.getElementsByTagName('title');
                            if( title.length > 0 ) {
                                headerTitle.innerText = title[0].innerText;
                                iframe.contentDocument.body.style.backgroundColor = '#EEF2F7';
                            }
                        });
                    }
                }
            }
        });

        var closeButton = document.createElement('button');
        closeButton.innerHTML = 'X';
        closeButton.style.float = 'right';
        closeButton.style.cursor = 'pointer';
        closeButton.style.width = '16px';
        closeButton.style.height = '16px';
        closeButton.style.overflow = 'hidden';
        closeButton.style.fontSize = '8px';
        closeButton.style.textAlign = 'center';
        closeButton.style.fontFamily = 'Verdana';
        closeButton.style.border = '1px solid #adb6ce';
        closeButton.style.color = '#304f6b';
        closeButton.style.outline = 'none';
        closeButton.addEventListener('click', function () {
            dialogs.pop().close();
        });

        var headerTitle = document.createElement('span');
        headerTitle.innerHTML = "&nbsp;";

        var doc = rootWindow().document;

        var header = document.createElement('div');
        header.style.userSelect = 'none';
        header.style.cursor = 'grab';
        header.style.backgroundColor = '#bdcee3';
        header.style.padding = '8px';
        header.style.color = '#304f6b';
        header.addEventListener('mousedown', function (e) {
            if( e.target === closeButton ) {
                return;
            }

            header.style.cursor = 'grabbing';

            var rect = header.getBoundingClientRect();
            var coords = {
                top: rect.top + pageYOffset,
                left: rect.left + pageXOffset
            };

            var shiftX = e.pageX - coords.left;
            var shiftY = e.pageY - coords.top;

            moveAt(e);

            function moveAt(e) {
                var maxLeft = rootWindow().innerWidth - width - 16;
                var maxTop = rootWindow().innerHeight - height - 16 - rect.height;
                var left = Math.min( maxLeft, Math.max( 8, e.pageX - shiftX ));
                var top = Math.min( maxTop, Math.max( 8, e.pageY - shiftY ));

                dialog.style.left = left + 'px';
                dialog.style.top = top + 'px';
            }

            function mouseUp(e) {
                doc.removeEventListener('mousemove', moveAt );
                header.removeEventListener('mouseup', mouseUp );
                header.style.cursor = 'grab';
            }

            doc.addEventListener('mousemove', moveAt );
            header.addEventListener('mouseup', mouseUp );
            header.addEventListener('mouseout', mouseUp );
        });

        header.appendChild( headerTitle );
        header.appendChild( closeButton );
        dialog.appendChild( header );
        dialog.appendChild( iframe );

        return dialog;
    }

    /**
     *
     * @param {HTMLDialogElement} dialog
     */
    function showDialog( dialog ) {
        rootWindow().document.body.appendChild( dialog );
        dialogs.push( dialog );
        dialog.showModal();
    }

    function hideLastOne() {
        if( dialogs.length > 0 ) {
            dialogs[ dialogs.length - 1 ].style.display = 'none';
        }
    }

    function showLastOne() {
        if( dialogs.length > 0 ) {
            dialogs[ dialogs.length - 1 ].style.display = 'block';
        }
    }

    return {
        /**
         *
         * @returns {HTMLDialogElement}
         */
        showModal: function( url, width, height ) {
            var dialog = createDialog( url, width, height );
            showDialog( dialog );
            return dialog;
        },

        /**
         *
         */
        closeTopDialog: function() {
            dialogs.pop().close();
        },

        /**
         *
         * @returns {null|WindowProxy}
         */
        topDialogWindow: function() {
            if( dialogs.length > 0 ) {
                return dialogs[ dialogs.length - 1 ].getElementsByTagName('iframe')[0].contentWindow;
            }

            return null;
        },

        /**
         *
         * @returns {null|WindowProxy}
         */
        prevDialogWindow: function() {
            if( dialogs.length > 1 ) {
                return dialogs[ dialogs.length - 2 ].getElementsByTagName('iframe')[0].contentWindow;
            }

            return null;
        }
    };
}

if( typeof ModalDialog !== 'object' && typeof HTMLDialogElement === 'function' ) {
    ModalDialog = ModalDialogClass();
}
