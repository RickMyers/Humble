<div class='flex flex-col h-full items-center justify-center'>
    <div class='p-4' style="width: 500px; background-color: rgba(00,102,153,.8); color: ghostwhite">
        <form name="new_secret_form" id="new_secret_form" onsubmit="return false">
            <fieldset class='p-2 text-base'><legend>Create A Secret</legend>
                A secret is an encrypted piece of text that can be an API Key, account credential, or other important piece of information that needs to be kept confidential.
                If setting an API Key for use in the mapping.yaml file, use the SM:// protocol to recover and decrypt your secret (Example: <i>api-key: SM://mySecretName</i>)<br /><br />
                <select name="namespace" id="secret_namespace" class="text-black text-lg">
                    <option v-for='namespace in namespaces' v-bind:value="namespace.value">{{ namespace.text }} </option>
                </select><br />
                <div class="font-mono text-sm pb-2 text-gw">Namespace</div>
                <input type="text" name="secret_name" id="new_secret_name" value="" class="text-black text-lg" />
                <div class="font-mono text-sm pb-2 text-gw">Secret Name</div>
                <input type="text" name="secret_value" id="new_secret_value" value="" class="text-black text-lg" />
                <div class="font-mono text-sm pb-2 text-gw">Secret Value</div>
                <input type="button" value=" Save Secret " name="new_secret_submit" id="new_secret_submit" class="bg-slate-300 hover:bg-slate-100 text-lg pr-2 pl-2 pt-1 pb-1" />
            </fieldset>
        </form>
    </div>
</div>

