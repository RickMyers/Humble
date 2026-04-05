<div v-for="(entity,i) in entities" :key="i">
    <div v-for="(field,col) in entity" :key="col"> 
        {{ col }}
    </div>
</div>
