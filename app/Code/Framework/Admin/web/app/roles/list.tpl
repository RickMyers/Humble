<ul>
    <li v-for="role in roles" :key="role.id"> hello {{ role.text }}</li>
    
</ul>
<form name="new_user_role_form" id="new_user_role_form" onsubmit="return false">
    <input type="text" name="new_user_role" id="new_user_role" class="rounded-sm w-[200] border-2" />
    <button class="rounded-lg pl-4 pr-4 pt-1 pb-1" v-on:click="addRole">Add Role</button>
</form>