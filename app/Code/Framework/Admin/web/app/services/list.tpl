<div style="overflow: scroll" class="scroll-y bg-slate-100 font-mono text-sm">
    <table class="table table-striped">
        <tr v-for="(service,i) in services" :key="i" class='p-2'>
            <td scope="row">{{ service.service }}</td>
        </tr>
    </table>
</div>
