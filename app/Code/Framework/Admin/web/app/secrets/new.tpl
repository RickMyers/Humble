<div class='flex flex-col h-full items-center justify-center'>
    <div style="width: 100%; color: ghostwhite; padding: 5px 20px">
        <form name="add_secret_form" id="add_secret_form" onsubmit="return false">
            <fieldset class='p-2 text-base'><legend>Add Secret</legend>
                <select name="namespace" class="text-black text-base p-1 w-1/2">
                    <option v-for='namespace in namespaces' v-bind:value="namespace.value">{{ namespace.text }} </option>
                </select><br />
                <div class="font-mono text-sm pb-2 text-sm">Namespace</div>
                <input type="text" name="secret_name" value="" class="text-black text-base p-1 w-3/4" />
                <div class="font-mono text-sm pb-2 text-sm">Secret Name</div>
                <input type="text" name="secret_value" value="" class="text-black text-base p-1 w-3/4" />
                <div class="font-mono text-sm pb-2 text-sm">Secret Value</div>
                <input type="button" value=" Save Secret " name="add_secret_submit" 
                       class="bg-gray-300 text-base hover:bg-blue-500 text-blue-700 hover:text-white py-1 px-2 border border-blue-500 hover:border-transparent rounded" />
            </fieldset>
        </form>
    </div>
</div>

