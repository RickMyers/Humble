<div class="w-50 align-top h-50 inline-block bg-black-100 box-border border-2">
    <form name="user_avatar_form" id="user_avatar_form" onsubmit="return false">
        <div class="text-base font-sans">
            <input type="file" name="user_photo" id="details_user_photo" class="standard-field w-48" />
        </div>
        <div class="text-sm font-mono mb-2">
            Avatar Photo
        </div>    
        <div class="">
            <input type="button" v-on:click="saveAvatar()" name="user_avatar_submit" class="bg-gray-100 pt-1 pb-1 pr-2 pl-2" id="user_avatar_submit" value=" Update Avatar " />
        </div>                
    </form>
</div><div class="w-50 h-50 align-top inline-block bg-red-200 box-border border-2">
    <form name="user_password_form" id="user_password_form" onsubmit="return false">
        <div class="text-base font-sans">
            <input type="password" name="current_password" id="details_current_password" class="standard-field" />
        </div>
        <div class="text-sm font-mono mb-2">
            Current Password
        </div>
        <div class="text-base font-sans">
            <input type="password" name="new_password" id="details_new_password" class="standard-field" />
        </div>
        <div class="text-sm font-mono mb-2">
            New Password
        </div>
        <div class="text-base font-sans">
            <input type="password" name="confirm_password" id="details_confirm_password" class="standard-field" />
        </div>
        <div class="text-sm font-mono mb-2">
            Confirm Password
        </div>
        <div class="">
            <input type="button" v-on:click="updatePassword()" name="user_password_submit" class="bg-gray-100 pt-1 pb-1 pr-2 pl-2" id="user_password_submit" value=" Update Password " />
        </div>        
    </form>
</div><div class="w-full h-50 box-border border-2 bg-slate-500">
    <form name="user_details_form" id="user_details_form" class="">
        <div class='pt-1 pb-2 font-sans text-xl inline-block'>
            User Name: <span class='font-bold'>{{ user_name }}</span>
        </div>
        <div class="text-base font-sans inline-block">
            <input type="text" name="first_name" id="details_first_name" class="standard-field" :value="first_name"/>
        </div>
        <div class="text-sm font-mono mb-2 inline-block">
            First Name
        </div>
        <div class="text-base font-sans inline-block">
            <input type="text" name="last_name" id="details_last_name" class="standard-field" v-bind:value="last_name" />
        </div>
        <div class="text-sm font-mono mb-2 inline-block">
            Last Name
        </div>
        <div class="text-base font-sans inline-block">
            <input type="text" name="email" id="details_email" class="standard-field" :value="email"/>
        </div>
        <div class="text-sm font-mono mb-2 inline-block">
            E-Mail
        </div>
        <div class="">
            <input type="button" v-on:click="saveDetails()" name="user_details_submit" class="bg-gray-100 pt-1 pb-1 pr-2 pl-2" id="user_details_submit" value=" Update Details " />
        </div>        
    </form>    
</div>

</form>

