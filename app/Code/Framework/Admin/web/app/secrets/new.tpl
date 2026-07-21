<div class='flex flex-col h-full items-center justify-center'>
    <div class='p-4' style="width: 500px; background-color: rgba(00,102,153,.8); color: ghostwhite">
        <form name="new_secret_form" id="new_secret_form" onsubmit="return false">
            <fieldset class='p-2 text-base'><legend>Create A Secret</legend>
                A secret is an encrypted piece of text that can be an API Key, account credential, or other important piece of information that needs to be kept confidential.
                If setting an API Key for use in the mapping.yaml file, use the SM:// protocol to recover and decrypt your secret (Example: <i>api-key: SM://mySecretName</i>)<br /><br />
                <select name="namespace" id="secret_namespace" class="text-black text-base p-1 w-1/2">
                    <option v-for='namespace in namespaces' v-bind:value="namespace.value">{{ namespace.text }} </option>
                </select><br />
                <div class="font-mono text-sm pb-2 text-sm">Namespace</div>
                <input type="text" name="secret_name" value="" class="text-black text-base p-1 w-3/4" />
                <div class="font-mono text-sm pb-2 text-sm">Secret Name</div>
                <input type="text" name="secret_value" value="" class="text-black text-base p-1 2-34" />
                <div class="font-mono text-sm pb-2 text-sm">Secret Value</div>
                <input type="button" value=" Save Secret " name="new_secret_submit" id="new_secret_submit" 
                       class="bg-gray-300 text-base hover:bg-blue-500 text-blue-700 hover:text-white py-1 px-2 border border-blue-500 hover:border-transparent rounded" />
            </fieldset>
        </form>
    </div>
</div>

