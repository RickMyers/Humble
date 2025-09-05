    <div id="user_list_header" class="border-b border-black py-1">
        <form name="user_list_form" id="user_list_form" class="w-full" onsubmit="return false">
            <input type="text" name="last_name" id="last_name" value="/" class="w-5/6 border rounded-sm border-black text-sm text-black font-mono"/><button class=" px-3 text-white bg-slate-600 font-mono text-sm" v-on:click="listUsers">List</button>
            <input type="hidden" name="starts_with" id="starts_with" value="" />
            <div style="clear: both"></div>
            <a href='#' v-on:click="startsWith('A')">A</a>&nbsp;
            <a href='#' v-on:click="startsWith('B')">B</a>&nbsp;
            <a href='#' v-on:click="startsWith('C')">C</a>&nbsp;
            <a href='#' v-on:click="startsWith('D')">D</a>&nbsp;
            <a href='#' v-on:click="startsWith('E')">E</a>&nbsp;
            <a href='#' v-on:click="startsWith('F')">F</a>&nbsp;
            <a href='#' v-on:click="startsWith('G')">G</a>&nbsp;
            <a href='#' v-on:click="startsWith('H')">H</a>&nbsp;
            <a href='#' v-on:click="startsWith('I')">I</a>&nbsp;
            <a href='#' v-on:click="startsWith('J')">J</a>&nbsp;
            <a href='#' v-on:click="startsWith('K')">K</a>&nbsp;
            <a href='#' v-on:click="startsWith('L')">L</a>&nbsp;
            <a href='#' v-on:click="startsWith('M')">M</a>&nbsp;
            <a href='#' v-on:click="startsWith('N')">N</a>&nbsp;
            <a href='#' v-on:click="startsWith('O')">O</a>&nbsp;
            <a href='#' v-on:click="startsWith('P')">P</a>&nbsp;
            <a href='#' v-on:click="startsWith('Q')">Q</a>&nbsp;
            <a href='#' v-on:click="startsWith('R')">R</a>&nbsp;
            <a href='#' v-on:click="startsWith('S')">S</a>&nbsp;
            <a href='#' v-on:click="startsWith('T')">T</a>&nbsp;
            <a href='#' v-on:click="startsWith('U')">U</a>&nbsp;
            <a href='#' v-on:click="startsWith('V')">V</a>&nbsp;
            <a href='#' v-on:click="startsWith('W')">W</a>&nbsp;
            <a href='#' v-on:click="startsWith('X')">X</a>&nbsp;
            <a href='#' v-on:click="startsWith('Y')">Y</a>&nbsp;
            <a href='#' v-on:click="startsWith('Z')">Z</a>&nbsp;            
        </form>
    </div>
    <div id="user_list_body" style="overflow: scroll" class="scroll-y bg-slate-100 font-mono text-sm">
      <table class="table table-striped">
      <thead>
        <tr>
          <th scope="row"><button v-on:click="upDirectory">&lt;&lt;</button></th>
          <th>Name</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(user,i) in users" :key="i">
           <td scope="row"></td>  
           <td>{{ user.last_name }}, {{ user.first_name }}</td> 
        <!--   <td><a href="#" v-if="file.directory" class="text-blue-800" v-on:click="expandDirectory(file.name)">{{ file.name }}</a><span v-else>{{ file.name }}</span></td>  
           <td>{{ file.filesize }}</td>  
           <td>{{ file.owner }}</td>  
           <td>{{ file.group }}</td>  
           <td>{{ file.modified }}</td>  -->
        </tr>
       </tbody>
      </table>
    </div>
    <div id="user_list_footer" class="bg-slate-900 pr-2 text-right text-sm text-white">
        User Explorer
    </div>