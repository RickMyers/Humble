    <div v-bind:id='query.area' class="overflow-auto" style="height: 200px">
        <table class="zebra-table w-full"> 
            <tr class="text-white bg-[#333333] whitespace-nowrap w-full">
                <th v-for="(header,j) in query.headers" :key="j" class="p-1 w-32 text-center inline-block overflow-hidden font-mono text-sm">
                    {{ header }} 
                </th>
            </tr>
            <tr v-for="(row,i) in query.resultset" :key="i" class="whitespace-nowrap w-full cursor-pointer zebra-row">
                <td v-for="(data,field) in row" :key="field" v-on:click="editRow(row.id)" class="w-32 text-center inline-block p-1 overflow-hidden font-mono text-sm text-cell" style="border: 1px solid transparent"> 
                    {{ data }} 
                </td>
            </tr>
        </table>        
    </div>
    <div v-bind:id='query.footer' class="p-1 bg-[#333333] text-white">
        <div class="float-left align-middle text-lg">
            Row <span v-bind:innerHTML="query.fromRow"></span> to <span v-bind:innerHTML="query.toRow"></span> of <span v-bind:innerHTML="query.totalRows"></span>
        </div> 
        <div class="float-right align-middle text-lg">
            Page <span v-bind:innerHTML="query.page"></span> of <span v-bind:innerHTML="query.pages"></span> 
        </div>         
        <div class="text-center">
            <button v-on:click='first()' class='p-1 text-mono text-lg'> << </button>
            <button v-on:click='prev()' class='p-1 mr-1 text-mono text-lg'> < </button>
            <input type="text" class="w-12 text-black text-center" style="background-color: lightcyan" v-bind:value="query.page" />
            <button v-on:click='next()' class='p-1 ml-1 text-mono text-lg'> > </button>
            <button v-on:click='last()'  class='p-1 text-mono text-lg'> >> </button>
        </div>
    </div>  
