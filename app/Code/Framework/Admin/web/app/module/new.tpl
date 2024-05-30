<div id='new_module_area' class="flex h-full" >
    <div class="m-auto bg-blue-500 p-2 w-[700] text-justify text-md">
        <span class="text-2xl">Module Creation</span><br /><br />
        You can create a new module using the form below.  Please specify which package your module will belong to, the unique namespace that will be used to identify the
        components that namespace will be controlling, a name for your module, and a prefix that will be used to identify any tables you will be creating<br /><br /><br />
        <form name="new_module_form" id="new_module_form" onsubmit="return false" >
            <select name="package" id="package" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
                <option v-for="package in packages" v-bind:value="package.value"> {{ package.text }} </option>
            </select>   
            <div class='font-mono pb-2 text-slate-1000'>Package</div>
            <input type="text" name="namespace" id="namespace" class="p-2 font-sans text-slate-900 w-[600] rounded-sm" /><br />
            <div class='font-mono pb-2 text-slate-1000'>Namespace</div>
            <input type="text" name="module" id="module" class="p-2 font-sans text-slate-900 w-[600] rounded-sm"/><br />
            <div class='font-mono pb-2 text-slate-1000'>Module Name</div>
            <input type="button" name="new_module_submit" id="new_module_submit" v-on:click="createModule()" value="Create" class="text-slate-1000 border border-gray-300 w-[125] focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-full text-lg px-15 py-2.5 me-2 mb-2 bg-white"/>                 
        </form>
    </div>
</div>
