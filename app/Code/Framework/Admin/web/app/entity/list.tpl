    <table class="zebra-table"> 
        <tr class="text-white bg-[#333333] whitespace-nowrap w-full">
            <th v-for="(header,j) in headers" :key="j" class="p-1 w-32 text-center inline-block overflow-hidden font-mono text-sm">
                {{ header }} 
            </th>
        </tr>
        <tr v-for="(entity,i) in entities" :key="i" class="whitespace-nowrap w-full cursor-pointer" >
            <td v-for="(data,field) in entity" :key="field" class="w-32 text-center inline-block p-1 overflow-hidden font-mono text-sm text-cell" style="border: 1px solid transparent"> 
                {{ data }} 
            </td>
        </tr>
    </table>