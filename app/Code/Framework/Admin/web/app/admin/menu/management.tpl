<div id="menu-management-class" class="flex h-full">
    
    <div class="m-auto bg-blue-500 p-2 w-[700] text-justify text-md rounded-sm">
        <span class="text-2xl ">Admin Menu Management</span><br /><br />
        Add new menu categories, menu options, and associate menus selections to categories here<br /><br />
        
    <form name="new_category_form" id="new_category_form" onsubmit="return false" >
        <select name="menu_category" id="menu_category" class="p-2 font-sans text-slate-900 w-[600] rounded-sm">
            <option v-for="category in menu_categories" v-bind:value="category.value">{{ category.text }} </option>
        </select>   
        <div class='font-mono pb-2 text-slate-1000'>Menu Category</div>
    </form>
    <hr class="mt-2 mb-2"/>        
    </div>
</div>

