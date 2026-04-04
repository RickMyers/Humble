<div style="overflow: scroll" class="scroll-y bg-slate-100 font-mono text-sm">
    <table class="table table-striped">
        <tr v-for="(service,i) in services" :key="i" class='p-2'>
            <td scope="row">
                <img v-if="service.running"  src="/images/admin/service_stop_icon.png"    
                     class="h-5 cursor-pointer inline-block mr-2" v-on:click="stopService(service.name)" 
                     alt="Stop Service" title="Stop Service" />
                <img v-if="!service.running" src="/images/admin/service_start_icon.png"   
                     class="h-5 cursor-pointer inline-block mr-2" v-on:click="startService(service.name)" 
                     alt="Start Service" title="Start Service" />
                <img v-if="service.running"  src="/images/admin/service_restart_icon.png" 
                     class="h-5 cursor-pointer inline-block mr-2" v-on:click="restartService(service.name)" 
                     alt="Restart Service" title="Restart Service" />
            </td>
            <td scope="row">{{ service.name }}</td>
        </tr>
    </table>
</div>
