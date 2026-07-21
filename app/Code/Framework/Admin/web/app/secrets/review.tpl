<table style="width: 100%; height: 100%">
    <tr>
        <td>
            <div style="margin-left: auto; margin-right: auto; width: 500px; padding: 20px; background-color: rgba(00,102,153,.8); color: ghostwhite">
                <form name="fetch_secret_form" id="fetch_secret_form" onsubmit="return false">
                    <fieldset><legend>Fetch A Secret</legend>
                        A secret is an encrypted piece of text that can be an API Key, account credential, or other important piece of information that needs to be kept confidential.
                        If setting an API Key for use in the mapping.yaml file, use the SM:// protocol to recover and decrypt your secret (Example: <i>api-key: SM://mySecretName</i>)<br /><br />

                        <select name="namespace" class="text-black text-base w-3/4 p-1">
                            <option v-for='namespace in namespaces' v-bind:value="namespace.value">{{ namespace.text }} </option>
                        </select><br />
                        <div class="font-mono text-sm pb-2 text-gw">Namespace</div>
                        <select name="id" class='text-black text-base w-3/4 p-1'>
                            <option value=""> </option>
                        </select><br />
                        <div class="font-mono text-sm pb-2 text-gw">Secret Name</div>
                        <input type="text" name="secret_value" value="" class='text-black text-base w-3/4 p-1'/>
                        <input type="button" value=" Update " name="update_secret_submit" 
                               class="bg-gray-300 text-base hover:bg-blue-500 text-blue-700 hover:text-white ml-1 py-1 px-2 border border-blue-500 hover:border-transparent rounded" />
                        <div class="font-mono text-sm pb-2 text-gw">Secret Value</div>
                        <input type="button" value=" Fetch Secret " name="fetch_secret_submit" 
                               class="bg-gray-300 text-base hover:bg-blue-500 text-blue-700 hover:text-white py-1 px-2 border border-blue-500 hover:border-transparent rounded" />
                    </fieldset>
                </form>
            </div>
        </td>
    </tr>
</table>