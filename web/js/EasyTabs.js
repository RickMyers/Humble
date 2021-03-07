/**   ------------------------------------------
 *    Easy Tab Navigation Class
 *
 *    @author: <rick@humblecoding.com>
 *
 *    ------------------------------------------  */
var EasyTabs = [];
/* Required CSS:
     #EscrollContainer	{ width: 580px;  height: 50px; position: relative; overflow: hidden;}
     #EcontrolScroll	{ position: relative;  width: 100%; height: 50px; overflow: hidden; white-space: nowrap; }
*/


function EasyTab(id,tabWidth)
{
    var me             	= this;
    this.sideWidth    	= 18;
    this.node       	= $E(id);
    this.node.style.overflow= "hidden";
    this.lastTab     	= null;
    this.refId        	= "_"+id;
    this.height       	 = "25px";
    this.fontSize    	= "9pt";
    this.color        	= "inherit";
    this.font        	= "sans-serif";
    this.currentTab 	= null;
    this.tabCtr        	= 0;
    this.selectedClass  = "";
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
	this.divXref		= [];
    this.add    		= function (text,handler,tabId,tabWidth)
    {
        var width = (tabWidth) ? (tabWidth+(me.sideWidth*2)) : me.tabWidth;
        var tabWidth = (tabWidth) ? tabWidth : me.midWidth;
        me.tabXref[text] = me.tabs.length;
		me.divXref[tabId] = me.tabs.length;
        var html = "<div onclick='EasyTabs[\""+me.refId+"\"].tabClick("+ me.tabs.length +")' style='cursor: pointer; float: left; overflow: hidden; height: "+me.height+"; width: "+ width +"px'>";
        html += '<table cellspacing="0" cellpadding="0">';
        html += '<tr>';
        if (this.images.unselected) {
            html += '<td><img src="'+me.imageHost+'/'+me.images.unselected.left+'"/></td>';
            html += '<td style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden; background-image: url('+me.imageHost+'/'+me.images.unselected.middle+')" width="'+tabWidth+'">'+text+'</td>';
            html += '<td><img src="'+me.imageHost+'/'+me.images.unselected.right+'"/></td>';
        } else {
            html += '<td class="'+this.unselectedClass+'" style="font-size: '+me.fontSize+'; font-family: '+me.font+'; color: '+me.color+'; text-align: center; white-space: nowrap; overflow: hidden;" width="'+tabWidth+'">'+text+'</td>';
        }
        html += '</tr>';
        html += '</table>';
        html+= "</div>";
        me.node.innerHTML += html;

        me.tabs[me.tabs.length] = {
            "text" : text,
            "width" : width,
            "tabWidth" : tabWidth,
            "handler"    : handler,
            "panelId"    : tabId
        };
        $E(tabId).style.display = "none";
        $E(tabId).style.overflow = "visible";
        return me;
    }
    this.click    = function (tabName)
    {
        if (typeof(tabName)=="string") {
            me.tabClick(me.tabXref[tabName]);
        }
        return me;
    }
    this.show    = function    (whichOne) {
    }
    this.tabClick = function (whichOne)
    {
        var html = '';
        for (var i=0; i<me.tabs.length; i++)
        {
            if (whichOne == i)
            {
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
            }
            else
            {
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
        $E(me.node.id).innerHTML = html;
        if (me.tabs[whichOne].panelId && $E(me.tabs[whichOne].panelId))        {
            for (var j=0; j<me.tabs.length; j++){
                if ($E(me.tabs[j].panelId)) {
                    $E(me.tabs[j].panelId).style.display = "none";
                }
            }
            $E(me.tabs[whichOne].panelId).style.display = "block";
            $E(me.tabs[whichOne].panelId).style.visibility = "visible";
        } else {
            console.log('EasyTabs: '+me.tabs[whichOne].panelId+" Missing Layer");
        }
        if (me.tabs[whichOne].handler) {
            me.tabs[whichOne].handler(me.tabs[whichOne]);
        }
        return me;
    }
    return EasyTabs[this.refId] = me;
}
EasyTab.prototype.activate	= function (divId)
{
    if (typeof(divId)=="string") {
		this.tabClick(this.divXref[divId]);
    }
	return this;
}
