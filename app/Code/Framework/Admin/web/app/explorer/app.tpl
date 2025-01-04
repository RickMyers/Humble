    <div id="explorer_path_area" class="border-b border-black py-1">
        <form name="explorer_path_form" id="explorer_path_form" class="w-full" onsubmit="return false">
            <input type="text" name="explorer_path" id="explorer_path" value="/" class="w-5/6 border rounded-sm border-black text-sm text-black font-mono"/><button class=" px-3 text-white bg-slate-600 font-mono text-sm" v-on:click="listFiles">List</button>
        </form>
    </div>
    <div id="explorer_file_list" style="overflow: scroll" class="scroll-y bg-slate-100 font-mono text-sm">
      <table class="table table-striped">
      <thead>
        <tr>
            <th scope="row"><button v-on:click="upDirectory">&lt;&lt;</button></th>
          <th>Permissions</th>
          <th>Name</th>
          <th>Filesize</th>
          <th>Owner</th>
          <th>Group</th>
          <th>Modified</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(file,i) in files" :key="i">
           <td scope="row"></td>  
           <td>{{ file.permissions }}</td> 
           <td><a href="#" v-if="file.directory" v-on:click="expandDirectory(file.name)">{{ file.name }}</a><span v-else>{{ file.name }}</span></td>  
           <td>{{ file.filesize }}</td>  
           <td>{{ file.owner }}</td>  
           <td>{{ file.group }}</td>  
           <td>{{ file.modified }}</td>  
        </tr>
       </tbody>
      </table>
    </div>
    <div id="explorer_footer" class="bg-slate-900 pr-2 text-right text-sm text-white">
        File Explorer
    </div>
