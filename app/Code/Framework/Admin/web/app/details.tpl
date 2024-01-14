<form name="user_details_form" id="user_details_form" class="text-center">
    <div class='pt-1 pb-2 font-sans text-xl'>
        User Name: <span class='font-bold'>{{ user_name }}</span>
    </div>
    <div class="text-base font-sans">
        <input type="text" name="first_name" id="details_first_name" class="standard-field" :value="first_name"/>
    </div>
    <div class="text-sm font-mono mb-2">
        First Name
    </div>
    <div class="text-base font-sans">
        <input type="text" name="last_name" id="details_last_name" class="standard-field" v-bind:value="last_name" />
    </div>
    <div class="text-sm font-mono mb-2">
        Last Name
    </div>
    <div class="text-base font-sans">
        <input type="text" name="email" id="details_email" class="standard-field" :value="email"/>
    </div>
    <div class="text-sm font-mono mb-2">
        E-Mail
    </div>
    <div class="text-base font-sans">
        <input type="file" name="user_photo" id="details_user_photo" class="standard-field w-48" />
    </div>
    <div class="text-sm font-mono mb-2">
        Avatar Photo
    </div>    
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
        <input type="button" v-on:click="saveDetails()" name="user_details_submit" class="bg-gray-100 pt-1 pb-1 pr-2 pl-2" id="user_details_submit" value=" Submit " />
    </div>
</form>

