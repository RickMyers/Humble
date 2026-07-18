<div v-bind:id="list.tab">
    <div v-bind:id="econtent.title" class="w-full bg-[#333333] p-3 text-xl" style="color: ghostwhite; font-weight: bolder">
        <div class="inline-block w-1/3">
            Entity List
        </div>
        <div class="inline-block w-1/3">
            
        </div>
        <div class="inline-block w-1/3">
            
        </div>
    </div>       
    <div v-bind:id='list.area' class="overflow-auto" style="height: 200px">
        <table class="zebra-table"> 
            <tr class="text-white bg-[#333333] whitespace-nowrap w-full">
                <th v-for="(header,j) in list.headers" :key="j" class="p-1 w-32 text-center inline-block overflow-hidden font-mono text-sm">
                    {{ header }} 
                </th>
            </tr>
            <tr v-for="(entity,i) in list.entities" :key="i" class="whitespace-nowrap w-full cursor-pointer zebra-row"  v-bind:title="entity.TABLE_NAME">
                <td v-for="(data,field) in entity" :key="field" v-on:click="expand(entity.TABLE_NAME)" class="w-32 text-center inline-block p-1 overflow-hidden font-mono text-sm text-cell" style="border: 1px solid transparent"> 
                    {{ data }} 
                </td>
            </tr>
        </table>        
    </div>
    <div v-bind:id='list.footer' class="p-1 bg-[#333333] text-white">
        <div class="float-left align-middle text-lg">
            Row <span v-bind:innerHTML="list.fromRow"></span> to <span v-bind:innerHTML="list.toRow"></span> of <span v-bind:innerHTML="list.totalRows"></span>
        </div> 
        <div class="float-right align-middle text-lg">
            Page <span v-bind:innerHTML="list.page"></span> of <span v-bind:innerHTML="list.pages"></span> 
        </div>         
        <div class="text-center">
            <button v-on:click='entityFirst()' class='p-1 text-mono text-lg'> << </button>
            <button v-on:click='entityPrev()' class='p-1 mr-1 text-mono text-lg'> < </button>
            <input type="text" class="w-12 text-black text-center" style="background-color: lightcyan" v-bind:value="list.page" />
            <button v-on:click='entityNext()' class='p-1 ml-1 text-mono text-lg'> > </button>
            <button v-on:click='entityLast()'  class='p-1 text-mono text-lg'> >> </button>
        </div>
    </div>       
</div>
                
<!-- ########################################################################### -->

<div v-bind:id="econtent.tab">
    <div v-bind:id="econtent.title" class="w-full bg-[#333333] p-3 text-xl" style="color: ghostwhite; font-weight: bolder">
        <div class="inline-block w-1/3">
            Namespace: {{ econtent.namespace }}
        </div>
        <div class="inline-block w-1/3">
            Entity: {{ econtent.table }}
        </div>
        <div class="inline-block w-1/3">
            Action: List Content
        </div>
    </div>    
    <div v-bind:id='econtent.area' class="overflow-auto" style="height: 200px">
        <table class="zebra-table w-full"> 
            <tr class="text-white bg-[#333333] whitespace-nowrap w-full">
                <th v-for="(header,j) in econtent.headers" :key="j" class="p-1 w-32 text-center inline-block overflow-hidden font-mono text-sm">
                    {{ header }} 
                </th>
            </tr>
            <tr v-for="(row,i) in econtent.data" :key="i" class="whitespace-nowrap w-full cursor-pointer zebra-row">
                <td v-for="(data,field) in row" :key="field" v-on:click="editRow(row.id)" class="w-32 text-center inline-block p-1 overflow-hidden font-mono text-sm text-cell" style="border: 1px solid transparent"> 
                    {{ data }} 
                </td>
            </tr>
        </table>        
    </div>
    <div v-bind:id='econtent.footer' class="p-1 bg-[#333333] text-white">
        <div class="float-left align-middle text-lg">
            Row <span v-bind:innerHTML="econtent.fromRow"></span> to <span v-bind:innerHTML="econtent.toRow"></span> of <span v-bind:innerHTML="econtent.totalRows"></span>
        </div> 
        <div class="float-right align-middle text-lg">
            Page <span v-bind:innerHTML="econtent.page"></span> of <span v-bind:innerHTML="econtent.pages"></span> 
        </div>         
        <div class="text-center">
            <button v-on:click='contentFirst()' class='p-1 text-mono text-lg'> << </button>
            <button v-on:click='contentPrev()' class='p-1 mr-1 text-mono text-lg'> < </button>
            <input type="text" class="w-12 text-black text-center" style="background-color: lightcyan" v-bind:value="econtent.page" />
            <button v-on:click='contentNext()' class='p-1 ml-1 text-mono text-lg'> > </button>
            <button v-on:click='contentLast()'  class='p-1 text-mono text-lg'> >> </button>
        </div>
    </div>  
</div>
                
<!-- ########################################################################### -->

<div v-bind:id="edit.tab">
    <form v-bind:id="edit.form" onsubmit="return false">
        <!-- COMMENT: The 'ee' below is for 'Entity Explorer' -->
        <input type="hidden" name="ee_namespace" v-bind:value="econtent.namespace" />
        <input type="hidden" name="ee_entity"    v-bind:value="econtent.table" />
        <div v-bind:id="edit.title" class="w-full bg-[#333333] p-3 text-xl" style="color: ghostwhite; font-weight: bolder">
            <div class="inline-block w-1/3">
                Action: <span style="color: #DD0000">Edit Row {{ edit.id }}</span>
            </div>        
            <div class="inline-block w-2/3">
                Entity: {{ econtent.namespace }}_{{ econtent.table }}
            </div>
        </div>        
        <div v-bind:id='edit.area' class="overflow-auto" style="height: 200px">
            <table class="zebra-table w-full"> 
                <tr v-for="(val,field) in edit.fields" :key="field" class="whitespace-nowrap w-full cursor-pointer zebra-row">
                    <td class="text-right text-bold mr-2 w-1/3">
                        <b>{{ field }}</b>:&nbsp;
                    </td>
                    <td>
                        <input type="text" v-bind:name="field" v-bind:value="screenIt(val)" class="p-1 w-full" style="border: 1px solid #333; background-color: lightcyan" />
                    </td>
                </tr>
            </table>        
        </div>
        <div v-bind:id='edit.footer' class="p-1 bg-[#333333] text-white text-center">
            <input type="submit" value="  SAVE  " class='p-1 text-mono text-lg'/>
        </div>  
    </form>
</div>
