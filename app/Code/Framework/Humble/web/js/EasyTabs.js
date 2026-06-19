/**   ------------------------------------------
 *    Easy Tab Navigation Class
 *
 *    @author: <rick@humbleprogramming.com>
 *
 *    ------------------------------------------  */
var EasyTabs = [];
/* Required CSS:
     #EscrollContainer	{ width: 580px;  height: 50px; position: relative; overflow: hidden;}
     #EcontrolScroll	{ position: relative;  width: 100%; height: 50px; overflow: hidden; white-space: nowrap; }
*/
function EasyTab(id,tabWidth,refreshTab)
{
    this.sideWidth    	= 18;
    this.node       	= document.getElementById(id);
    this.node.style.overflow= "hidden";
    this.lastTab     	= null;
    this.refId        	= "_"+id;
    this.height       	= "25px";
    this.fontSize    	= "9pt";
    this.color        	= "inherit";
    this.font        	= "sans-serif";
    this.currentTab 	= 0;
    this.reference      = null;                                                 //Tab id or object reference to scale other tabs to
    this.tabCtr        	= 0;
    this.selectedClass  = "";
    this.refreshTab     = (typeof refreshTab === "undefined") ? true : refreshTab;
    this.unselectedClass = "";
    this.imageHost   	= "/web/images/tabs/"; // "/" or "http://mysite.com/images"
    this.tabs        	= [];
    this.images        	= {
        selected:	{
            left:    	"easyTabS-L.gif",
            middle:    	"easyTabS-M.gif",
            right:   	"easyTabS-R.gif"
        },
        unselected: {
            left:    	"easyTabU-L.gif",
            middle:    	"easyTabU-M.gif",
            right:    	"easyTabU-R.gif"
        }
    }

    this.tabWidth    	= tabWidth;
    this.midWidth    	= tabWidth-(this.sideWidth*2);
    this.tabXref    	= [];
    this.setReference   = (ref) => {
        this.reference           = (typeof ref === "string") ? document.getElementById(ref) : ref;
        return this;
    }
    this.scaleTo        = (ref) => {
        if (ref) {
            this.reference           = (typeof ref === "string") ? document.getElementById(ref) : this.tabs[ref].ref;
        }
        for (let i=0; i<this.tabs.length; i++) {
            $(this.tabs[i].ref).height($(this.reference).height());
        }        
    }
    this.add    	= (text,handler,tabId,tabWidth) =>  {
        var width         = (tabWidth) ? (tabWidth+(this.sideWidth*2)) : this.tabWidth;
        var tabWidth      = (tabWidth) ? tabWidth : this.midWidth;
        let createTab     = !tabId;
        let tab           = (!tabId ? document.createElement('div') : ((typeof tabId === "string") ? document.getElementById(tabId) : tabId));
        if (createTab) {
            this.node.after(tab);
        }
        this.tabXref[text]  = this.tabs.length;
        var html = "<div onclick='EasyTabs[\""+this.refId+"\"].tabClick("+ this.tabs.length +")' style='cursor: pointer; float: left; overflow: hidden; height: "+this.height+"; width: "+ width +"px'>";
        html += '<table cellspacing="0" cellpadding="0"><tr>';
        if (this.images.unselected) {
            html += '<td><img src="'+this.imageHost+'/'+this.images.unselected.left+'"/></td>';
            html += '<td style="font-size: '+this.fontSize+'; font-family: '+this.font+'; color: '+this.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+this.imageHost+'/'+this.images.unselected.middle+')" width="'+tabWidth+'">'+text+'</td>';
            html += '<td><img src="'+this.imageHost+'/'+this.images.unselected.right+'"/></td>';
        } else {
            html += '<td class="'+this.unselectedClass+'" style="font-size: '+this.fontSize+'; font-family: '+this.font+'; color: '+this.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
        }
        html += '</tr></table></div>';
        this.node.innerHTML += html;
        let seq = this.tabs.length;
        this.tabs[seq] = {
            "seq"       : seq, 
            "ref"       : tab,
            "loaded"    : false,
            "text"      : text,
            "width"     : width,
            "tabWidth"  : tabWidth,
            "handler"   : handler
        };
        tab.style.display  = "none";
        tab.style.overflow = "visible";
        return this;
    }
    this.click    =  (tabName) => {
        if (typeof tabName === "string") {
            this.tabClick(this.tabXref[tabName]);
        }
        return this;
    }
    this.tabClick = (whichOne) => {
        this.currentTab = whichOne;
        var html = '';        
        for (var i=0; i<this.tabs.length; i++) {
            if (whichOne == i) {
                html += "<div onclick='EasyTabs[\""+this.refId+"\"].tabClick("+ i +")' style='cursor: pointer; float: left; overflow: hidden; height: "+this.height+"; width: "+ this.tabs[i].width +"px'>"
                html += '<table cellspacing="0" cellpadding="0">';
                html += '<tr>';
                if (this.images.selected) {
                    html += '<td><img src="'+this.imageHost+'/'+this.images.selected.left+'"/></td>';
                    html += '<td style="font-size: '+this.fontSize+'; font-family: '+this.font+'; color: '+this.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+this.imageHost+'/'+this.images.selected.middle+')" width="'+this.tabs[i].tabWidth+'">'+this.tabs[i].text+'</td>';
                    html += '<td><img src="'+this.imageHost+'/'+this.images.selected.right+'"/></td>';
                } else {
                    html += '<td class="'+this.selectedClass+'" style="font-size: '+this.fontSize+'; font-family: '+this.font+'; color: '+this.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
                }
                html += '</tr>';
                html += '</table>';
                html+= "</div>";
            } else {
                html += "<div onclick='EasyTabs[\""+this.refId+"\"].tabClick("+ i +")' style='cursor: pointer; float: left; overflow: hidden; height: "+this.height+"; width: "+ this.tabs[i].width +"px'>"
                html += '<table cellspacing="0" cellpadding="0">';
                html += '<tr>';
                if (this.images.unselected) {
                    html += '<td><img src="'+this.imageHost+'/'+this.images.unselected.left+'"/></td>';
                    html += '<td style="font-size: '+this.fontSize+'; font-family: '+this.font+'; color: '+this.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+this.imageHost+'/'+this.images.unselected.middle+')" width="'+this.tabs[i].tabWidth+'">'+this.tabs[i].text+'</td>';
                    html += '<td><img src="'+this.imageHost+'/'+this.images.unselected.right+'"/></td>';
                } else {
                    html += '<td class="'+this.unselectedClass+'" style="font-size: '+this.fontSize+'; font-family: '+this.font+'; color: '+this.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
                }
                html += '</tr>';
                html += '</table>';
                html += "</div>";
            }
        }
        this.node.innerHTML = html;
        for (var j=0; j<this.tabs.length; j++){
            if (this.tabs[j].ref) {
                this.tabs[j].ref.style.display = "none";
            }
        }
        this.tabs[whichOne].ref.style.display     = "block";
        this.tabs[whichOne].ref.style.visibility  = "visible";
        if (this.tabs[whichOne].handler) {
            if ((this.refreshTab) || (!this.tabs[whichOne].loaded)) {
                this.tabs[whichOne].handler(this.tabs[whichOne]);
            } else {
                console.log('skipping reload of tab '+whichOne);
            }
        }
        return this;
    }
    return EasyTabs[this.refId] = this;
}

