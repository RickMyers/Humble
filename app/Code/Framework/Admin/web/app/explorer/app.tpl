<div id="explorer_path_area" class="border-b border-black">
    <form name="explorer_path_form" id="explorer_path_form" class="w-full" onsubmit="return false">
        <input type="text" name="explorer_path" id="explorer_path" value="/" class="w-5/6 border rounded-sm border-black text-sm text-black font-mono"/><button class=" px-3 text-white bg-slate-600 font-mono text-sm" v-on:click="listFiles">List</button>
    </form>
</div>
<div id="explorer_file_list">
</div>
<div id="explorer_footer" class="bg-slate-900 pr-2 text-right text-sm text-white">
    File Explorer
</div>
