function DigitalClock (divId,hour,minute) {

    var me					= this;
    this.refId				= Math.round(Math.random()*10000);
    this.timer				= null;
    this.node				= ($E(divId)) ? $E(divId) : null;
    this.hour				= (hour) ? hour : null;
    this.minute				= (minute) ? minute : null;
    this.second				= null;
    this.interval			= null;
    this.backgroundColor	= "inherit";
    this.foreColor			= "inherit";
    this.font				= "inherit";
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
        var tt = "am";
        if (sec<10) sec="0"+sec;
        if (min<10) min="0"+min;
        if (hrs>12) { hrs=hrs-12; if (hrs<10) hrs="0"+hrs; tt="pm"; }
        var str = hrs+":"+min+":"+sec+"<span style='vertical-align: top; margin-left: 2px; padding:0px; font-size: 75%'>"+ tt +"</span>";
        me.node.innerHTML = '<span style="background-color: '+me.backgroundColor+'; color: '+me.foreColor+'; font-family: '+me.font+'">'+str+'</span>';
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
var DynamicCalendars = { };
function DynamicCalendar (div_id){
    var me			= this;
    this.node		= $E(div_id) ? $E(div_id) : null;
    this.id			= 'c_'+new Date().getTime()+"_";
    this.larrow		= null;
    this.rarrow		= null;
    this.weekday	= null;
    this.weekend	= null;
    this.daynames   = null;
    this.controls	= true;
    this.layout		= null;
    this.months 	= "January,February,March,April,May,June,July,August,September,October,November,December";
    this.monthname  = null;
    this.month 	 	= this.months.split(",");
    this.SECOND 	= 1000;
    this.MINUTE 	= 60 * this.SECOND;
    this.HOUR   	= 60 * this.MINUTE;
    this.DAY    	= 24 * this.HOUR;
    this.WEEK   	= 7  * this.DAY;
    this.thisMonth 	= null;
    this.thisYear  	= null;
    this.xref		= [];
    this.setArrows	= function (larrow,rarrow)	{
        me.larrow = larrow;
        me.rarrow = rarrow;
        return this;
    }
    this.dayHandler = function (mm,dd,yyyy)	{
        alert(mm+"/"+dd+"/"+yyyy);
    }
    this.setNode	= function (node) {
        me.node = node;
        return this;
    }
    this.build		= function ()	{
        var calendar = me;
       // var rarrow 	= (calendar.rarrow) ? '<img  height="30" src="'+calendar.rarrow+'" border="0" />' : "&gt;&gt;";
        //var larrow 	= (calendar.larrow) ? '<img  height="30" src="'+calendar.larrow+'" border="0" />' : "&lt;&lt;";
        var HTML = '<table style="margin-left: auto; margin-right: auto" class="'+ calendar.layout +'" id="'+ calendar.id +'calTB" cellspacing="1">'+
                    '<tr><td colspan="5" id="'+ calendar.id +'monthName" class="'+ calendar.monthname +'"></td>'+
                    '<td colspan="2" class="'+ calendar.monthname +'" align="right">';
        if (calendar.controls) {
            HTML += '<img style="height: 16px; cursor: pointer" src="/images/core/left-arrow-white.png" onclick="DynamicCalendars[\''+calendar.id+'\'].back(\''+ calendar.id +'\'); return false">&nbsp;<img style="height: 16px; cursor: pointer" src="/images/core/right-arrow-white.png" href="#" onclick="DynamicCalendars[\''+calendar.id+'\'].next(\''+ calendar.id +'\'); return false">';
        }
        HTML += '</td></tr><tr><td class="'+calendar.daynames+'">SUN</td><td class="'+calendar.daynames+'">MON</td><td class="'+calendar.daynames+'">TUE</td><td class="'+calendar.daynames+'">WED</td><td class="'+calendar.daynames+'">THU</td><td class="'+calendar.daynames+'">FRI</td><td class="'+calendar.daynames+'">SAT</td></tr>'+
                '<tr id="'+ calendar.id +'week0" weeknum="0"><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c0"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c1"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c2"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c3"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c4"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c5"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c6"> </td></tr>'+
                '<tr id="'+ calendar.id +'week1" weeknum="1"><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c7"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c8"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c9"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c10"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c11"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c12"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c13"> </td></tr>'+
                '<tr id="'+ calendar.id +'week2" weeknum="2"><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c14"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c15"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c16"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c17"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c18"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c19"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c20"> </td></tr>'+
                '<tr id="'+ calendar.id +'week3" weeknum="3" ><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c21"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c22"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c23"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c24"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c25"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c26"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c27"> </td></tr>'+
                '<tr id="'+ calendar.id +'week4" weeknum="4"><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c28"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c29"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c30"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c31"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c32"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c33"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c34"> </td></tr>'+
                '<tr id="'+ calendar.id +'week5" weeknum="5"><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c35"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c36"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c37"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c38"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c39"> </td><td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c40"> </td>'+
                '<td onclick="DynamicCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c41"> </td></tr>'+
                '</table>';
        $(calendar.node).html(HTML);
        return this;
    }
    this.set		= function (mm,yyyy){
        var cal      	= new Date(me.month[parseInt(mm)]+" 1, "+yyyy+" 12:00:00");
        var startDay    = cal.getDay();
        var currentMonth= null;
        var dayCounter  = 1;
        this.monthname = this.months[mm];
        me.toggleDay('normal');
        me.xref = [];
        me.thisMonth = mm;
        me.thisYear = yyyy;
        $E(me.id+"monthName").innerHTML = me.month[parseInt(mm)]+" "+yyyy;
        do {
            var cd = $E(me.id+"c"+(startDay+(dayCounter-1)));
            cd.innerHTML = dayCounter;
            cd.title = '';
            var isoDay = "d_"+yyyy+""+(((mm+1)<10) ? "0" + +(mm+1) : (mm+1))+""+((dayCounter<10) ? "0"+dayCounter : dayCounter);
            me.xref[isoDay] = me.id+"c"+(startDay+(dayCounter-1));
            dayCounter++;
            cal.setTime(cal.getTime()+me.DAY);
            currentMonth = cal.getMonth();
        } while (me.thisMonth == currentMonth)
        me.toggleDay('bold')
        return this;
    }
    this.clear		= function ()	{
        for (var i=0; i<41; i++) {
            var cell = $E(me.id+"c"+i);
            cell.innerHTML = "";
            cell.style.backgroundColor = "";
        }
        return this;
    }
    this.setDayHandler	= function (handler){
        this.dayHandler = handler;
        return this;
    }
    this.showControls	= function (arg) {
        this.controls = arg;
        return this;
    }
    this.getYear = function () {
        return this.thisYear;
    }
    this.toggleDay = function (bold) {
        var now = new Date();
        var yyyy = now.getFullYear();
        var mm	= now.getMonth();
        var dd = now.getDate();
        var today = "d_"+yyyy + "" + (((mm+1)<10) ? "0"+(mm+1) : (mm+1)) + "" + ((dd<10) ? "0"+dd : dd);
        if (me.xref[today]) {
            $E(me.xref[today]).style.fontWeight = bold;
            $E(me.xref[today]).style.backgroundColor = "rgba(240,240,240,.3)";
        }
    }
    this.setWeekend = function (classname) {
        this.weekend = classname;
        return this;
    }
    this.setWeekday = function (classname) {
        this.weekday = classname;
        return this;
    }
    this.setDayNames = function (classname) {
        this.daynames = classname;
        return this;
    }
    this.setMonthName = function (classname) {
        this.monthname = classname;
        return this;
    }
    this.onMonthChange = null;
    DynamicCalendars[this.id] = me;
    return this;
}
/* -------------------------------------------------- */
DynamicCalendar.prototype.next	= function (id) {
    var calendar = DynamicCalendars[id];
    calendar.thisMonth++;
    if (calendar.thisMonth >= 12) {
        calendar.thisYear++;
        calendar.thisMonth = 0;
    }
    calendar.clear();
    calendar.set(calendar.thisMonth, calendar.thisYear);
    if (calendar.onMonthChange)	{
        calendar.onMonthChange(calendar);
    }
    return this;
}
/* -------------------------------------------------- */
DynamicCalendar.prototype.back	= function (id) {
    var calendar = DynamicCalendars[id];
    calendar.thisMonth--;
    if (calendar.thisMonth < 0) {
        calendar.thisYear--;
        calendar.thisMonth = 11;
    }
    calendar.clear();
    calendar.set(calendar.thisMonth, calendar.thisYear);
    if (calendar.onMonthChange)	{
        calendar.onMonthChange(calendar);
    }
    return this;
}
/* -------------------------------------------------- */
DynamicCalendar.prototype.onDayClick = function (evt) {
    evt         = (evt) ? evt : ((window.event) ? event : null);
    var dayId   = EasyAjax.getElementId(evt);
    var day     = $E(dayId).innerHTML.trim();
    var calId   = dayId.substr(0,dayId.lastIndexOf("_"))+"_";
    var cellId  = dayId.substr(dayId.lastIndexOf("_")+1);
    if (day) {
        DynamicCalendars[calId].dayHandler(DynamicCalendars[calId].thisMonth,day,DynamicCalendars[calId].thisYear,cellId);
    }
    return this;
}
var Animate = (function () {
    var timer       = null;
    var interval    = 15;
    var deg         = 0;
    var element     = null;
    return {
        run: function (thing) {
            if (thing) {
                element = $E(thing);
            }
            deg = (deg >= 360) ? 0 : deg+3;
            element.style.transform = 'rotate('+deg+'deg)';
            timer = window.setTimeout(Animate.run,interval)
        },
        stop: function () {
            if (timer) {
                window.clearTimeout(timer);
            }
        },
        set: {
            interval: function (arg) {
                interval = arg;
            },
            degrees: function (arg) {
                deg = arg;
            }
        },
        get: {
            interval: function () {
                return interval;
            },
            degrees: function () {
                return deg;
            }
        }
    }
})();