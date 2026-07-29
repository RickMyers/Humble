<form name="rest_client_form" id="rest_client_form" onsubmit="return false">
    <div id="rest_client_header" class="bg-[#333333] pr-2 text-xl" style="color: ghostwhite; text-align: right">
        <br />
        Rest Client
        <br /><br />
    </div>
    <div id="rest_client_body" class="pl-6">
        <div class="py-3 font-mono text-base">
            This application allows you test your exposed services.  Please select from below the module Namespace, Controller containing the action, and then the Action to test.
            A list of parameters will show up available for you to assign values.  You may also add any additional parameters you may need to complete your testing.  Hit the run
            button on the bottom of the app to give it a go...
        </div>
        <div class="float-left pr-6">
            <div class="">
                <div class="">
                    <select v-on:change="fetchControllers($event)" name="namespace" class="p-1 w-64">
                        <option value=""> </option>
                        <option v-for="(result,i) in namespaces" v-bind:value="result.namespace" :key="i" class=""> {{ result.namespace }} </option>
                    </select>
                </div>
                <div class="text-mono text-base tracking-wide pb-2">
                    Namespace
                </div>
            </div>
            <div class="">
                <div class="">
                    <select v-on:change="fetchControllerActions($event)" name="controller" class="p-1 w-64">
                        <option value=""> </option>
                        <option v-for="(controller,j) in controllers" v-bind:value="controller" :key="j" class=""> {{ controller }} </option>
                    </select>                
                </div>
                <div class="text-mono text-base tracking-wide pb-2">
                    Controller
                </div>
            </div>
            <div class="">
                <div class="">
                    <select v-on:change="fetchActionParameters($event)" name="action" class="p-1 w-64">
                        <option value=""> </option>
                        <option v-for="(action,k) in actions" v-bind:value="action.name" v-bind:title="action.description" :key="k" class=""> {{ action.name }} </option>
                    </select>                
                </div>
                <div class="text-mono text-base tracking-wide pb-2">
                    Action/Method
                </div>
            </div>
        </div>
        <div class="inline-block w-1/3 pl-4 pt-2">
            <div class="">
                <div class="">
                    <input type="radio" name="mime_type" value="application/x-www-form-urlencoded" checked="checked"/> Default
                    <input type="radio" name="mime_type" value="multipart/form-data" /> Multipart
                    <input type="radio" name="mime_type" value="application/json" /> JSON
                </div>
                <div class="text-mono text-base tracking-wide pb-2">
                    Mime Type
                </div>
            </div> 
            <div class="">
                <div class="text-mono text-base tracking-wide pb-2">
                    <input type="radio" name="execution_mode" value="Test" checked="checked"/> TEST
                    <input type="radio" name="execution_mode" value="Live" /> LIVE
                </div>
                <div class="">
                    Mode
                </div>
            </div>               
        </div>
        <div style="clear: both"></div>
        <div class="">
        <table class="zebra-table"> 
            <tr class="text-white bg-[#333333] whitespace-nowrap w-full">
                <th v-for="(header,j) in headers" :key="j" class="p-1 w-32 text-center inline-block overflow-hidden font-mono text-sm">
                    {{ header }} 
                </th>
            </tr>
            <tr v-for="(parameter,i) in parameters" :key="i" class="whitespace-nowrap w-full cursor-pointer zebra-row">
                <td v-for="(data,field) in parameter" :key="k" class="w-32 text-center inline-block p-1 overflow-hidden font-mono text-sm text-cell" style="border: 1px solid transparent"> 
                    {{ data }} 
                </td>
            </tr>
        </table>               
            
            
        </div>
    </div>
    <div id="rest_client_footer" class="bg-[#333333] pr-2" style="color: ghostwhite; text-align: right""> 
       <br /><br /> &copy; Humbleprogramming.com, 2007-Present
    </div>
</form> 
