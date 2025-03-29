var EasyCalendars = { };
function EasyCalendar (div_id){
    var me		= this;
    this.node		= $E(div_id) ? $E(div_id) : null;
    this.id		= 'c_'+EasyAjax.uniqueId(12)+"_";
    this.larrow		= null;
    this.rarrow		= null;
    this.arrows         = null;
    this.weekday	= null;
    this.weekend	= null;
    this.daynames       = null;
    this.controls	= true;
    this.layout		= null;
    this.months 	= "January,February,March,April,May,June,July,August,September,October,November,December";
    this.monthname      = null;
    this.month 	 	= this.months.split(",");
    this.SECOND 	= 1000;
    this.MINUTE 	= 60 * this.SECOND;
    this.HOUR   	= 60 * this.MINUTE;
    this.DAY    	= 24 * this.HOUR;
    this.WEEK   	= 7  * this.DAY;
    this.thisMonth 	= null;
    this.thisYear  	= null;
    this.xref		= [];
    this.original       = { };
    this.setArrows	= function (larrow,rarrow,classname)	{
        me.larrow = larrow;
        me.rarrow = rarrow;
        me.arrows = classname;
        return this;
    }
    this.dayHandler = function (mm,dd,yyyy)	{
        alert(++mm+"/"+dd+"/"+yyyy);
    }
    this.setNode	= function (node) {
        me.node = node;
        return this;
    }
    this.build		= function ()	{
        var calendar = me;
        var HTML = '<table style="margin-left: auto; margin-right: auto" class="'+ calendar.layout +'" id="'+ calendar.id +'calTB" cellspacing="1">'+
                    '<tr><td colspan="5" id="'+ calendar.id +'monthName" class="'+ calendar.monthname +'"></td>'+
                    '<td colspan="2" class="'+ calendar.monthname +'">';
        if (calendar.controls) {
            HTML += '<img class="'+this.arrows+'" src="'+this.larrow+'" onclick="EasyCalendars[\''+calendar.id+'\'].back(\''+ calendar.id +'\'); return false">&nbsp;<img class="'+this.arrows+'" src="'+this.rarrow+'" href="#" onclick="EasyCalendars[\''+calendar.id+'\'].next(\''+ calendar.id +'\'); return false">';
        }
        HTML += '</td></tr><tr><td class="'+calendar.daynames+'">SUN</td><td class="'+calendar.daynames+'">MON</td><td class="'+calendar.daynames+'">TUE</td><td class="'+calendar.daynames+'">WED</td><td class="'+calendar.daynames+'">THU</td><td class="'+calendar.daynames+'">FRI</td><td class="'+calendar.daynames+'">SAT</td></tr>'+
                '<tr id="'+ calendar.id +'week0" weeknum="0"><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c0"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c1"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c2"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c3"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c4"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c5"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c6"> </td></tr>'+
                '<tr id="'+ calendar.id +'week1" weeknum="1"><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c7"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c8"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c9"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c10"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c11"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c12"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c13"> </td></tr>'+
                '<tr id="'+ calendar.id +'week2" weeknum="2"><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c14"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c15"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c16"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c17"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c18"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c19"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c20"> </td></tr>'+
                '<tr id="'+ calendar.id +'week3" weeknum="3" ><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c21"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c22"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c23"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c24"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c25"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c26"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c27"> </td></tr>'+
                '<tr id="'+ calendar.id +'week4" weeknum="4"><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c28"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c29"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c30"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c31"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c32"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c33"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c34"> </td></tr>'+
                '<tr id="'+ calendar.id +'week5" weeknum="5"><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c35"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c36"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c37"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c38"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c39"> </td><td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekday +'" id="'+ calendar.id +'c40"> </td>'+
                '<td onclick="EasyCalendars[\''+calendar.id+'\'].onDayClick(event)" class="'+ calendar.weekend +'" id="'+ calendar.id +'c41"> &nbsp; </td></tr>'+
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
            this.original[cd.id] = {
                "background-color": $('#'+cd.id).css('background-color'),
                "color": $('#'+cd.id).css('color')
            };
            cd.innerHTML = dayCounter;
            cd.title = '';
            var isoDay = "d_"+yyyy+""+(((mm+1)<10) ? "0" + +(mm+1) : (mm+1))+""+((dayCounter<10) ? "0"+dayCounter : dayCounter);
            me.xref[isoDay] = me.id+"c"+(startDay+(dayCounter-1));
            dayCounter++;
            cal.setTime(cal.getTime()+me.DAY);
            currentMonth = cal.getMonth();
        } while (me.thisMonth == currentMonth);
           // console.log(this.original);
        me.toggleDay('bold');
        return this;
    }
    this.clear		= function ()	{
        for (var i=0; i<41; i++) {
            var cell = $E(me.id+"c"+i);
            if (this.original[cell.id]) {
                cell.innerHTML = '';
            }
        }
        return this;
    }
    this.reset          = function () {
        for (var i=0; i<41; i++) {
            var cell = $E(me.id+"c"+i);
            if (this.original[cell.id]) {
                cell.style.backgroundColor = this.original[cell.id]['background-color'];
                cell.style.color = this.original[cell.id]['color'];

            }
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
    this.toggleDay  = function (bold) {
        var now     = new Date();
        var yyyy    = now.getFullYear();
        var mm      = now.getMonth();
        var dd      = now.getDate();
        var today   = "d_"+yyyy + "" + (((mm+1)<10) ? "0"+(mm+1) : (mm+1)) + "" + ((dd<10) ? "0"+dd : dd);
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
    this.getCellRef = (date) => {
        let dt = date.split('-');
        return $('#'+this.xref['d_'+dt[0]+dt[1]+dt[2]]);
    }
    this.onMonthChange = null;
    EasyCalendars[this.id] = me;
    return this;
}
/* -------------------------------------------------- */
EasyCalendar.prototype.next	= function (id) {
    var calendar = EasyCalendars[id];
    calendar.thisMonth++;
    if (calendar.thisMonth >= 12) {
        calendar.thisYear++;
        calendar.thisMonth = 0;
    }
    calendar.reset().clear();
    calendar.set(calendar.thisMonth, calendar.thisYear);
    if (calendar.onMonthChange)	{
        calendar.onMonthChange(calendar);
    }
    return this;
}
/* -------------------------------------------------- */
EasyCalendar.prototype.back	= function (id) {
    var calendar = EasyCalendars[id];
    calendar.thisMonth--;
    if (calendar.thisMonth < 0) {
        calendar.thisYear--;
        calendar.thisMonth = 11;
    }
    calendar.reset().clear();
    calendar.set(calendar.thisMonth, calendar.thisYear);
    if (calendar.onMonthChange)	{
        calendar.onMonthChange(calendar);
    }
    return this;
}
/* -------------------------------------------------- */
EasyCalendar.prototype.onDayClick = function (evt) {
    evt         = (evt) ? evt : ((window.event) ? event : null);
    var dayId   = EasyAjax.getElementId(evt);
    var day     = $E(dayId).innerHTML.trim();
    var calId   = dayId.substr(0,dayId.lastIndexOf("_"))+"_";
    var cellId  = dayId.substr(dayId.lastIndexOf("_")+1);
    if (day) {
        EasyCalendars[calId].dayHandler(EasyCalendars[calId].thisMonth,day,EasyCalendars[calId].thisYear,cellId);
    }
    return this;
}
