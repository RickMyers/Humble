    async function getClipboardContents() {
        const clipboardItems = await navigator.clipboard.read();
        for (const clipboardItem of clipboardItems) {
            for (const type of clipboardItem.types) {
                const blob = await clipboardItem.getType(type);
            }
        }        
    }
  
    function _arrayBufferToBase64( buffer ) {
        var binary = '';
        var bytes = new Uint8Array( buffer );
        var len = bytes.byteLength;
        for (var i = 0; i < len; i++) {
            binary += String.fromCharCode( bytes[ i ] );
        }
        return window.btoa( binary );
    }
    (() => {
        $('#dashboard_bug_screenshot_attach').on('click',function (evt) {
            navigator.permissions.query({ name: "clipboard-read" }).then(result => {
                if (result.state == "granted" || result.state == "prompt") {
                     navigator.clipboard.read().then(clipboardItems => {
                        for (const clipboardItem of clipboardItems) {
                            for (const type of clipboardItem.types) {
                                const blob = clipboardItem.getType(type).then(function (image) {
                                    const blob = new Blob([image],{ type: 'image/png' });
                                    blob.arrayBuffer().then(data => {
                                        $E('dashboard_bug_screenshot').src = "data:image/png;base64,"+_arrayBufferToBase64(data);
                                    });
                                });
                            }
                        }
                    });
                } else {
                    alert('Didnt get permission');
                };
            });
        });          
    })();
