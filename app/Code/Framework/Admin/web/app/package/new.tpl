<div id="new_package_area" class="flex h-full" >
    <div class="m-auto bg-blue-500 p-2 w-[700] text-justify text-md">
        <span class="text-2xl">New Package Creation</span><br /><br />
        All modules are contained within packages.  Should you wish to create a new package for your module, you may do so here<br /><br />
        <form name="new_package_form" id="new_package_form" onsubmit="return false" >
            <input type="text" name="new_package" id="new_package" class="p-2 font-sans text-slate-900 w-[600] rounded-sm"/>
            <div class='font-mono pb-2 text-slate-1000'>Package (Directory) Name</div>            
            <input type="button" name="new_package_submit" id="new_package_submit" v-on:click="createModule()" value="Create" class="text-slate-1000 border border-gray-300 w-[125] focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-full text-lg px-15 py-2.5 me-2 mb-2 bg-white"/>            
        </form>
    </div>
</div>
