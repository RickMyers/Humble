<div id="new_component_area" class="flex h-full" >
    <div class="m-auto bg-blue-500 p-2 w-[700] text-justify text-md">
        <span class="text-2xl ">Component Creation</span><br /><br />
        You can build a new component using the form below.  Please specify which namespace your component will be bound to, the type of component, and the name of the new component<br /><br /><br />
        <form name="new_component_form" id="new_component_form" onsubmit="return false" >
            <input type='text' name='short_description' id='short_description' class="p-2 font-sans text-slate-900 w-[600] rounded-sm" /><br />
            <div class='font-mono pb-2 text-slate-1000'>Short Description</div>
            <textarea name='long_description' id='long_description' class="p-2 font-sans text-slate-900 w-[600] rounded-sm"></textarea><br />
            <div class='font-mono pb-2 text-slate-1000'>Long Description</div>
            <select name="component_namespace" id="component_namespace" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
                <option v-for="namespace in namespaces" v-bind:value="namespace.value">{{ namespace.text }} </option>
            </select>
            <div class='font-mono pb-2 text-slate-1000'>Namespace</div>
            <select name="component_type" id="component_type" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
                <option value=''>Please select type of component to create</option>
                <option value='models'>Model Class</option>
                <option value='entities'>Entity Class</option>
                <option value='helpers'>Helper Class</option>
            </select>
            <div class='font-mono pb-2 text-slate-1000'>Component Type</div>

            <div style='margin-bottom: 20px; font-size: 1.1em'>
                <input type='checkbox' value='Y' id='generates_events' name='generates_events' /> Event Generator
            </div>

            <select name="component_package" id="component_package" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
            </select>
            <div class='font-mono pb-2 text-slate-1000'>Documentation Package (taxonomy)</div>
            <select name="component_category" id="component_category" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
            </select>
            <div class='font-mono pb-2 text-slate-1000'>Documentation Category (taxonomy)</div>
            <input type="text" name="component_name" id="component_name" class="p-2 font-sans text-slate-900 w-[600] rounded-sm" /><br />
            <div class='font-mono pb-2 text-slate-1000'>Component Name</div>
            <input type="button" name="new_component_submit" id="new_component_submit" v-on:click="createComponent()" value="Build" class="text-slate-1000 border border-gray-300 w-[125] focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-full text-lg px-15 py-2.5 me-2 mb-2 bg-white"/> 
            <!--input type='text' name='component_package_combo' id='component_package_combo' />
            <input type='text' name='component_category_combo' id='component_category_combo' /-->
        </form>
    </div>
</div>

