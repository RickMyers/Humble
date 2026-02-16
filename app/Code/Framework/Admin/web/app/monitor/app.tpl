<div id='system_monitor'>
    <div style="position: absolute; top: 0px; right: 0px">
        <div class="process-search-box" style="display: inline-block; border-radius: 8px; height: 25px; border: 1px solid #333; padding-left: 30px; background-color: ghostwhite; background-image: url(/images/admin/search.png); background-repeat: no-repeat">
            <input class="process-search-field" type="text" style="border: 0px; color: #333; background-color: ghostwhite; width: 230px; height: 20px; position: relative; top: 2px" name="process-search-field" id="process-search-field" placeholder="Search..." value="">
        </div>
    </div>    
    <div id='monitor_status' class='bg-grey-200 text-lg text-black-800'>
        <div class='text-sm'>
            <div class='w-1/2 inline-block p-0 m-0'>
                <div class='text-center bg-gray-400'>
                    Basic Status
                </div>
                <div class='inline-block w-1/4'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Time</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.time }}</div>
                </div>
                <div class='inline-block w-1/4'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Up Time</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.uptime }}</div>
                </div>
                <div class='inline-block w-1/4'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Online</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.users }}</div>
                </div>
                <div class='inline-block w-1/4'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Load Averages</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.load }}</div>
                </div>
            </div>
            <div class='w-1/2 inline-block p-0 m-0'>
                <div class='text-center bg-gray-400'>
                    Tasks Status
                </div>
                <div class='inline-block w-1/5'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>All</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.tasks }}</div>
                </div>
                <div class='inline-block w-1/5'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Running</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.running }}</div>
                </div>        
                <div class='inline-block w-1/5'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Sleeping</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.sleeping }}</div>
                </div>
                <div class='inline-block w-1/5'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Stopped</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.stopped }}</div>
                </div>
                <div class='inline-block w-1/5'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Zombie</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.zombie }}</div>
                </div>
            </div>
        </div>        
        <div class='text-sm'>
            <div class='w-1/3 inline-block p-0 m-0'>
                <div class='text-center bg-gray-400'>
                    CPU Status
                </div>    
                <div class='inline-block w-1/6'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>User</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.cpu_us }}</div>
                </div>
                <div class='inline-block w-1/6'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Kernel</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.cpu_sy }}</div>
                </div>
                <div class='inline-block w-1/6'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Idle</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.cpu_id }}</div>
                </div>
                <div class='inline-block w-1/6'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Hardware</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.cpu_hi }}</div>
                </div>
                <div class='inline-block w-1/6'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>Software</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.cpu_si }}</div>
                </div>
                <div class='inline-block w-1/6'>
                    <div class='font-mono border-1 border-gray-900 bg-gray-200'>VM</div>
                    <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.cpu_vm }}</div>
                </div>
            </div>
            <div class='w-1/3 inline-block p-0 m-0'>                
                <div class='text-center bg-gray-400'>
                     Memory Status
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>Total</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.mem_tot }}</div>
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>Free</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.mem_fre }}</div>
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>In-Use</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.mem_use }}</div>
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>Cache</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.mem_buf }}</div>
                 </div>
            </div>   
            <div class='w-1/3 inline-block p-0 m-0'>                   
                  <div class='text-center bg-gray-400'>
                     Swap Status
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>Total</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.swap_tot }}</div>
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>Free</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.swap_fre }}</div>
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>In-Use</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.swap_use }}</div>
                 </div>
                 <div class='inline-block w-1/4'>
                     <div class='font-mono border-1 border-gray-900 bg-gray-200'>Average</div>
                     <div class='font-sans pl-2 border-1 border-gray-900 bg-gray-100'>{{ system.swap_av }}</div>
                 </div>
            </div>
        </div>           
    </div>
    <div id="monitor_search"></div>
    <div id='monitor_processes' class='bg-yellow-100 scroll-auto' style='overflow: auto'>
        <table class='table table-striped text-xs'>
            <thead class='p-2'>
                <tr>
                    <th class='text-center'>
                        &diam;
                    </th>
                    <th class='text-center'>
                        PID
                    </th>
                    <th class='text-center'>
                        Owner
                    </th>
                    <th class='text-center'>
                        Priority
                    </th>
                    <th class='text-center'>
                        Virtual<br/>Memory
                    </th>
                    <th class='text-center'>
                        Resident<br />
                        Memory
                    </th>
                    <th class='text-center'>
                        Shared<br />
                        Memory
                    </th>
                    <th class='text-center'>
                        Status
                    </th>
                    <th class='text-center'>
                        CPU<br />
                        Percent
                    </th>
                    <th class='text-center'>
                        Memory<br />
                        Percent
                    </th>
                    <th class='text-center'>
                        CPU<br />
                        Time
                    </th>
                    <th class='text-center'>
                        Command
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(process,i) in processes" :key="i" class='p-2'>
                    <td class='text-center p-2'> <button class="bg-red-500 text-white rounded pt-1 pr-2 pl-2 pb-1"> X </button> </td>
                    <td class='text-center p-2'>{{ process.PID }} </td>
                    <td class='text-center p-2'>{{ process.owner }} </td>
                    <td class='text-center p-2'>{{ process.priority }} </td>
                    <td class='text-center p-2'>{{ process.mem_vir }} </td>
                    <td class='text-center p-2'>{{ process.mem_res }} </td>
                    <td class='text-center p-2'>{{ process.mem_shr }} </td>
                    <td class='text-center p-2'>{{ process.status }} </td>
                    <td class='text-center p-2'>{{ process.cpu_prc }} </td>
                    <td class='text-center p-2'>{{ process.mem_prc }} </td>
                    <td class='text-center p-2'>{{ process.time }} </td>
                    <td class='text-center p-2 cursor-pointer' :title="process.extended">{{ process.command }} </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id='monitor_footer' class='bg-slate-800 text-right pr-2 text-white'>
        System Monitor
    </div>
</div>
