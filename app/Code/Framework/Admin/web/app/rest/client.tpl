<form name="rest_client_form" id="rest_client_form" onsubmit="return false">
    <div id="rest_client_header" class="bg-[#333333] pr-2" style="color: ghostwhite; text-align: right">
        Header
    </div>
    <div id="rest_client_body">
        <div class="">
            To test 
        </div>
        <div class="">
            <div class="">
                <select v-on:change="fetchControllers()" name="namespace" class="">
                    <option value=""> </option>
                </select>
            </div>
            <div class="">
                Namespace
            </div>
        </div>
        <div class="">
            <div class="">
                <select v-on:change="fetchControllerActions()" name="controller" class="">
                    <option value=""> </option>
                </select>                
            </div>
            <div class="">
                Controller
            </div>
        </div>
        <div class="">
            <div class="">
                <select v-on:change="fetchActionParameters()" name="action" class="">
                    <option value=""> </option>
                </select>                
            </div>
            <div class="">
                Action/Method
            </div>
        </div>
        <div class=""> 
        </div>
    </div>
    <div id="rest_client_footer" class="bg-[#333333] pr-2" style="color: ghostwhite; text-align: right""> 
        &copy; Humbleprogramming.com, 2007-Present
    </div>
</form> 
