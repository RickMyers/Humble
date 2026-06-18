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
    var me             	= this;
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
            this.reference           = (typeof ref === "string") ? document.getElementById(ref) : ref;
        }
        for (let i=0; i<this.tabs.length; i++) {
            $(this.tabs[i].ref).height($(this.reference).height());
        }        
    }
    this.add    	= (text,handler,tabId,tabWidth) =>  {
        var width         = (tabWidth) ? (tabWidth+(me.sideWidth*2)) : me.tabWidth;
        var tabWidth      = (tabWidth) ? tabWidth : me.midWidth;
        let createTab     = !tabId;
        let tab           = (!tabId ? document.createElement('div') : ((typeof tabId === "string") ? document.getElementById(tabId) : tabId));
        if (createTab) {
            me.node.after(tab);
        }
        me.tabXref[text]  = me.tabs.length;
        var html = "<div onclick='EasyTabs[\""+me.refId+"\"].tabClick("+ me.tabs.length +")' style='cursor: pointer; float: left; overflow: hidden; height: "+me.height+"; width: "+ width +"px'>";
        html += '<table cellspacing="0" cellpadding="0"><tr>';
        if (this.images.unselected) {
            html += '<td><img src="'+me.imageHost+'/'+me.images.unselected.left+'"/></td>';
            html += '<td style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+me.imageHost+'/'+me.images.unselected.middle+')" width="'+tabWidth+'">'+text+'</td>';
            html += '<td><img src="'+me.imageHost+'/'+me.images.unselected.right+'"/></td>';
        } else {
            html += '<td class="'+this.unselectedClass+'" style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
        }
        html += '</tr></table></div>';
        me.node.innerHTML += html;
        me.tabs[me.tabs.length] = {
            "ref"       : tab,
            "loaded"    : false,
            "text"      : text,
            "width"     : width,
            "tabWidth"  : tabWidth,
            "handler"   : handler
        };
        tab.style.display  = "none";
        tab.style.overflow = "visible";
        return me;
    }
    this.click    =  (tabName) => {
        if (typeof tabName === "string") {
            me.tabClick(me.tabXref[tabName]);
        }
        return me;
    }
    this.tabClick = (whichOne) => {
        var html = '';
        for (var i=0; i<me.tabs.length; i++) {
            if (whichOne == i) {
                html += "<div onclick='EasyTabs[\""+me.refId+"\"].tabClick("+ i +")' style='cursor: pointer; float: left; overflow: hidden; height: "+me.height+"; width: "+ me.tabs[i].width +"px'>"
                html += '<table cellspacing="0" cellpadding="0">';
                html += '<tr>';
                if (this.images.selected) {
                    html += '<td><img src="'+me.imageHost+'/'+me.images.selected.left+'"/></td>';
                    html += '<td style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+me.imageHost+'/'+me.images.selected.middle+')" width="'+me.tabs[i].tabWidth+'">'+me.tabs[i].text+'</td>';
                    html += '<td><img src="'+me.imageHost+'/'+me.images.selected.right+'"/></td>';
                } else {
                    html += '<td class="'+this.selectedClass+'" style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
                }
                html += '</tr>';
                html += '</table>';
                html+= "</div>";
            } else {
                html += "<div onclick='EasyTabs[\""+me.refId+"\"].tabClick("+ i +")' style='cursor: pointer; float: left; overflow: hidden; height: "+me.height+"; width: "+ me.tabs[i].width +"px'>"
                html += '<table cellspacing="0" cellpadding="0">';
                html += '<tr>';
                if (this.images.unselected) {
                    html += '<td><img src="'+me.imageHost+'/'+me.images.unselected.left+'"/></td>';
                    html += '<td style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+me.imageHost+'/'+me.images.unselected.middle+')" width="'+me.tabs[i].tabWidth+'">'+me.tabs[i].text+'</td>';
                    html += '<td><img src="'+me.imageHost+'/'+me.images.unselected.right+'"/></td>';
                } else {
                    html += '<td class="'+this.unselectedClass+'" style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
                }
                html += '</tr>';
                html += '</table>';
                html += "</div>";
            }
        }
        me.node.innerHTML = html;
        for (var j=0; j<me.tabs.length; j++){
            if (me.tabs[j].ref) {
                if (j !== whichOne) {
                    me.tabs[j].ref.style.display = "none";
                }
            }
        }
        me.tabs[whichOne].ref.style.display     = "block";
        me.tabs[whichOne].ref.style.visibility  = "visible";
        if (me.tabs[whichOne].handler) {
            if ((me.refreshTab) || (!me.tabs[whichOne].loaded)) {
                me.tabs[whichOne].handler(me.tabs[whichOne]);
            }
        }
        me.currentTab = whichOne;
        return me;
    }
    return EasyTabs[this.refId] = me;
}

