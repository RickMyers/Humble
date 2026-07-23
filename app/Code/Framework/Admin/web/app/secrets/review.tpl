<div style="margin-left: auto; margin-right: auto; width: 100%; padding: 5px 20px">
    <form name="fetch_secret_form" id="fetch_secret_form" onsubmit="return false">
        <fieldset><legend>Review Secrets</legend>
            <select name="namespace" class="text-black text-base w-3/4 p-1">
                <option v-for='namespace in namespaces' v-bind:value="namespace.value"> {{ namespace.text }} </option>
            </select><br />
            <div class="font-mono text-sm pb-2 text-gw">Namespace</div>
            <select name="id" class='text-black text-base w-3/4 p-1'>
                <option value=""> </option>
            </select><br />
            <div class="font-mono text-sm pb-2 text-gw">Secret Name</div>
            <div class="w-3/4" style="background-color: lightcyan" style="position: relative">
                <img title="Toggle Password Visibility" src="/images/admin/hide_show.png" style="height: 30px; position: absolute; right: 0px; top: 0px; z-index: 9; margin-right: 5px; cursor: pointer" />
                <input type="password" name="secret_value" value="" class='text-black text-base p-1 w-full' style="position: relative; z-index: 1"/>
            </div>
            <input type="button" value=" Update " name="update_secret_submit" 
                   class="bg-gray-300 text-base hover:bg-blue-500 text-blue-700 hover:text-white my-2 ml-1 py-1 px-2 border border-blue-500 hover:border-transparent rounded" />
            <div class="font-mono text-sm pb-2 text-gw">Secret Value</div>
        </fieldset>
    </form>
</div>
