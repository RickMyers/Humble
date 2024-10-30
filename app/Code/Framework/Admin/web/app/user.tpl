<div class="w-full">
    <div class="w-50 inline-block">
        <div class='pt-1 pb-2 font-sans text-xl inline-block m-auto'>
           User Name: <span class='font-bold'>{{ user_name }}</span>
        </div>    
        <div class="text-sm font-mono mb-2 overflow-hidden" style=" margin: auto; border-radius: 120px; width: 240px; height: 240px; border: 2px solid silver">
            <img onload="tools.image.align(this)" :src="avatar_image" />
        </div> 
        <hr />
        <div class="border-2">
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Password
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="password" id="user_last_name"  :value="password" />
                </div>        
            </div>
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Account Status
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="account_status" id="user_account_status" />
                </div>        
            </div>
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Login Attempts
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="login_attempts" id="user_login_attempts" :value="login_attempts" />
                </div>        
            </div>                
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Reset Token
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="reset_token" id="user_reset_token" />
                </div>        
            </div>        
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Authenticated
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="authenticated" id="user_authenticated" />
                </div>        
            </div>                
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Last Login
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="last_login" id="user_last_login" :value="last_login" />
                </div>        
            </div>            
        </div>         
    </div>
    <div class="w-50 inline-block h-60 bg-red-600 align-top">
        <div class="w-full">
            <div class="w-full border-2">
                <div class="text-sm mono">
                    First Name
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" name="first_name" id="user_first_name"  :value="first_name" />
                </div>
            </div><div class="w-full border-2">
                <div class="text-sm mono">
                    Middle Name
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="middle_name" id="user_middle_name"  :value="middle_name" />
                </div>
            </div><div class="w-full border-2">
                <div class="text-sm mono">
                    Last Name
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="last_name" id="user_last_name"  :value="last_name" />
                </div>
            </div><div class="w-full border-2">
                <div class="text-sm mono">
                    Suffix
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="name_suffix" id="user_name_suffix"  :value="suffix" />
                </div>
            </div>
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Email
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="email" id="user_email"  :value="email" />
                </div>            
            </div>
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Gender
                </div>
                <div class="pl-4 text-lg">
                    <select class="w-full" name="gender" id="user_gender" :value="gender">
                        <option value=""> </option>
                        <option value="M">[M] - Male </option>
                        <option value="F">[F] - Female </option>
                        <option value="O">[O] - Other </option>
                    </select>
                </div>            
            </div>
            <div class="w-full border-2">
                <div class="text-sm mono">
                    Date Of Birth
                </div>
                <div class="pl-4 text-lg">
                    <input type="text" class="w-full" name="date_of_birth" id="user_date_of_birth" placeholder="MM/DD/YYYY"  :value="date_of_birth" />
                </div>            
            </div>            
        </div>

        <div class="w-full pt-5">
            <input type="button" v-on:click="saveDetails()" name="user_user_submit" class="bg-gray-300 pt-1 pb-1 pr-2 pl-2" id="user_user_submit" value=" Update Details " />
        </div>         
    </div>
</div>


