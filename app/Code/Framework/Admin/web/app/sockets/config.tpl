<table style="width: 100%; height: 100%">
    <tr>
        <td>
            <div style="margin-left: auto; margin-right: auto; width: 600px; padding: 30px; background-color: rgba(00,102,153,.8); color: ghostwhite">
                <form name="config_socket_form" id="config_socket_form" onsubmit="return false">
                    <fieldset><legend>Websocket Support</legend>
                        To enable websocket integration, we will be using the Socket.io library.  Fill out the information below and click "Continue" to download and automatically install the socket server.
                        For a tutorial on how to use the sockets, please go here:<br /><br />
                        <a href="https://humbleprogramming.com/pages/Sockets.htmls" target="_BLANK">https://humbleprogramming.com/pages/Sockets.htmls Socket Server</a><br /><br />
                        <input type="text" name="host" id="socket_server_host" value="" class="p-1 border-black font-sans w-[250] border-1 rounded-sm text-black" /><br />
                        <div class="font-mono text-sm pb-4 text-gw">Host [localhost]</div>
                        <input type="text" name="port" id="socket_server_port" value="" class="p-1 border-black font-sans w-[250] border-1 rounded-sm text-black" /><br />
                        <div class="font-mono text-sm pb-4 text-gw">Port</div>
                        <input type="button" value=" Continue " name="config_socket_submit" id="config_socket_submit" class="rounded-xl px-3 py-1 bg-gray-200 text-gray-900" />
                    </fieldset>
                </form>
            </div>
        </td>
    </tr>
</table>



