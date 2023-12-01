<table style="width: 100%; height: 100%">
    <tr><td>
        <div style="width: 600px; border-radius: 10px; background-color: steelblue; padding: 10px; border: 1px solid #333; color: ghostwhite; margin-right: auto; margin-left: auto">
            <form name="humble_upload_form" id="humble_upload_form" onsubmit="return false">
                <fieldset><legend>File Upload Instructions</legend>
                    <p>
                        This utility will allow you to upload an arbitrary file to the server, the max size is 82 megabytes.  You can also optionally change the default destination below.<br/>
                    </p>
                    <input type="file" name="filename" id="humble_upload_filename" style="padding: 2px; color: #333; background-color: lightcyan; width: 350px; font-size: 1em" /><br />
                    <div style="font-family: monospace; letter-spacing: 2px; font-size: .9em; margin-bottom: 20px">File To Upload</div>
                    <input type="text" name="destination" id="humble_upload_destination" style="padding: 2px; color: #333; background-color: lightcyan; width: 350px; font-size: 1em" value="/var/www/uploads" /><br />
                    <div style="font-family: monospace; letter-spacing: 2px; font-size: .9em; margin-bottom: 20px">File Destination</div>
                    <input type="button" value=" Upload " style="" id="humble_upload_button" />
                    <br /><br /><br />
                </fieldset>
            </form>
        </div>
    </td></tr>
</table>
<script type="text/javascript">
  
    $('#humble_upload_button').on('click',function (evt) {
        var win = Desktop.window.list[Desktop.whoami('humble_upload_destination')];
        win.splashScreen('<table style="width: 100%; height: 100%"><tr><td style="text-align: center; font-size: 3em; color: white"> Uploading File... </td></tr></table>');
        (new EasyAjax('/humble/upload/file')).packageForm('humble_upload_form').then((response) => {
            win.splashScreen('');
            alert(response); 
            Administration.upload.win._close();
        }).post();
    });
</script>