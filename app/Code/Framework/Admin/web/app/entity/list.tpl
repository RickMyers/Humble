<div v-for="(header,j) in headers">
    {{ header }}
</div>
<div v-for="(entity,i) in entities" :key="i"> 
    <div v-for="(field,data) in entity" :key="data">
        {{ field }}/// {{ data }}
    </div>
</div>

