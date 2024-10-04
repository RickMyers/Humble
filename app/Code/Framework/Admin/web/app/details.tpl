<div class="w-50 align-top h-50 inline-block box-border border-2 text-center">
    <div class='pt-1 pb-2 font-sans text-xl inline-block m-auto'>
        User Name: <span class='font-bold'>{{ user_name }}</span>
    </div>    
    <div class="text-sm font-mono mb-2 overflow-hidden" style=" margin: auto; border-radius: 120px; width: 240px; height: 240px; border: 2px solid silver">
        <img id="admin_avatar" onload="tools.image.align(this)" :src="avatar_image" />
    </div>     
    <div class="w-full">    
        <div class="text-base font-sans inline-block m-auto text-center">
            <form name="user_avatar_form" id="user_avatar_form" onsubmit="return false">
                <input type="file" name="user_photo" id="details_user_photo" class="standard-field w-48" />&nbsp;
                <input type="button" v-on:click="updateAvatar()" name="user_avatar_submit" class="bg-gray-300 pt-1 pb-1 pr-2 pl-2" id="user_avatar_submit" value=" Update " />
            </form>
        </div>                
    </div>
</div><div class="w-50 h-50 align-top inline-block box-border border-2">
    <table class="w-full h-full">
        <tr><td>
            <div class="m-auto text-center">
                <form  name="user_password_form" id="user_password_form" onsubmit="return false">
                    <fieldset><legend>Password Update</legend>
                        <div class="pt-2 pb-2 m-auto text-base w-75 border-2 mb-10">
                            You can change your <b>Administrator</b> password here.  This will not change your local user password.
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
                            <input type="button" v-on:click="updatePassword()" name="user_password_submit" class="bg-gray-300 pt-1 pb-1 pr-2 pl-2" id="user_password_submit" value=" Update " />
                        </div>        
                    </fieldset>
                </form>
            </div>
            </td></tr></table>
</div><div class="w-full h-50 box-border border-2 ">
    <form name="user_details_form" id="user_details_form" class="">
        <div class="w-full">
            <div class="w-1/4 border-2 inline-block">
                <div class="text-sm mono">
                    First Name
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" name="first_name" id="details_first_name"  :value="user_name" />
                </div>
            </div><div class="w-1/4 border-2 inline-block">
                <div class="text-sm mono">
                    Middle Name
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="middle_name" id="details_middle_name"  :value="middle_name" />
                </div>
            </div><div class="w-1/4 border-2 inline-block">
                <div class="text-sm mono">
                    Last Name
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="last_name" id="details_last_name"  :value="last_name" />
                </div>
            </div><div class="w-1/4 border-2 inline-block">
                <div class="text-sm mono">
                    Suffix
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="name_suffix" id="details_name_suffix"  :value="suffix" />
                </div>
            </div>
        </div>
        <div class="w-full">
            <div class="w-1/2 border-2 inline-block">
                <div class="text-sm mono">
                    Email
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="email" id="details_email"  :value="email" />
                </div>            
            </div>
            <div class="w-1/4 border-2 inline-block">
                <div class="text-sm mono">
                    Gender
                </div>
                <div class="pl-4 text-lg">
                    <select class="w-full" name="gender" id="details_gender" :value="gender">
                        <option value=""> </option>
                        <option value="M">[M] - Male </option>
                        <option value="F">[F] - Female </option>
                        <option value="O">[O] - Other </option>
                    </select>
                </div>            
            </div>
            <div class="w-1/4 border-2 inline-block">
                <div class="text-sm mono">
                    Date Of Birth
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="date_of_birth" id="details_date_of_birth" placeholder="MM/DD/YYYY"  :value="date_of_birth" />
                </div>            
            </div>            
        </div>
        
        <div class="w-full pt-5">
            <input type="button" v-on:click="saveDetails()" name="user_details_submit" class="bg-gray-300 pt-1 pb-1 pr-2 pl-2" id="user_details_submit" value=" Update Details " />
        </div>        
    </form>    
</div>
