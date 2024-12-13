function EasyClock (divId,hour,minute) {

    var me					= this;
    this.refId				= Math.round(Math.random()*10000);
    this.timer				= null;
    this.node				= ($E(divId)) ? $E(divId) : null;
    this.hour				= (hour) ? hour : null;
    this.minute				= (minute) ? minute : null;
    this.second				= null;
    this.interval			= null;
    this.backgroundColor                = "inherit";
    this.foreColor			= "ghostwhite";
    this.font				= "sans-serif";
    this.set				= function (hour,minute){
        me.hour 	= hour;
        me.minute	= minute;
    }
    this.start				= function () {
        window.setTimeout(me.update,550);
    }
    this.update				= function () {
        me.timer = new Date();
        
        var sec = me.timer.getSeconds();
        var min = me.timer.getMinutes();
        var hrs	= me.timer.getHours();
        var tt  = hrs>11 ? "pm" : "am";
        if (sec<10) sec="0"+sec;
        if (min<10) min="0"+min;
        if (hrs>12) { hrs=hrs-12; if (hrs<10) hrs="0"+hrs;}
        var str = hrs+":"+min+":"+sec+"<span style='vertical-align: top; margin-left: 2px; padding: 0px; font-size: 65%; position: relative; top: -2px'>"+ tt +"</span>";
       $('#'+me.node.id).html(str);
       // me.node.innerHTML = str;
        window.setTimeout(me.update,250);
    }
    if ((this.hour) && (this.minute)) {
        me.timer = new Date();
    } else {
        me.timer 	= new Date();
        me.hour		= me.timer.getHours();
        me.minute	= me.timer.getMinutes();
        me.second	= me.timer.getSeconds();
    }
    this.start();

    return me;
}
