<div id='new_controller_area' class="flex h-full">
    <span class="text-2xl ">Controller Creation</span><br /><br />
    You can create a new controller using the form below.  Please specify which namespace your controller will belong to, the templater the controller will be using, a name for the controller, and the name of the first action<br /><br /><br />
    <form name="new_controller_form" id="new_controller_form" onsubmit="return false" >
        <select name="controller_namespace" id="controller_namespace" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
        </select><br />
        <div class='new-module-desc'>Namespace</div>
        <select name="controller_templater" id="controller_templater" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
        </select><br />
        <div class='new-module-desc'>Templater</div>
        <input type="text" name="controller_name" id="controller_name" class="p-2 font-sans text-slate-900 w-[600] rounded-sm" />
        <div class='new-module-desc'>Controller Name</div>
        <input type="text" name="controller_description" id="controller_description" class="p-2 font-sans text-slate-900 w-[600] rounded-sm" />
        <div class='new-module-desc'>Controller Description</div>
        <input type="text" name="action_name" id="action_name" class="p-2 font-sans text-slate-900 w-[600] rounded-sm" />
        <div class='new-module-desc'>Name of Action</div>
        <input type="text" name="action_description" id="action_description" class="p-2 font-sans text-slate-900 w-[600] rounded-sm" />
        <div class='new-module-desc'>Action Description</div>
        <input type="button" name="new_component_submit" id="new_component_submit" v-on:click="createComponent()" value="Create" class="text-slate-1000 border border-gray-300 w-[125] focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-full text-lg px-15 py-2.5 me-2 mb-2 bg-white"/>         
    </form>
</div>
