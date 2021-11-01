//---------------------------------------------------------------------------------------
//
// 	EasyEdits.
//
//	Author: 	Rick Myers 		<rick@humbleprogramming.com>
//
//  This software is not freeware, shareware, or released under any open source license.
//    Since this is a "Work In Progress", use is by permission only.  Contact Rick for
//    permission and terms of use.
//
//---------------------------------------------------------------------------------------
var Edits	= []; //manages multiple instances of edits on a single page
function EasyEdits(source, ref)
{
    var me		= this;
    this.hasContent	= false;
//	this.requiredColor	= "#ee3333";
    this.requiredColor	= "#ffebc9";
    this.edits		= [];
    this.values         = [];
    this.editsJSON	= "";
    this.errors		= 0;
    this.criticals	= 0;
    this.warnings	= 0;
    this.failures	= 0;
    this.message	= "";
    this.messages	= [];
    this.flagged	= false;
    this.source		= "";
    this.formNode	= "";
    this.executed       = false;
    this.ratio		= 5
    this.currentZoom	= 100;
    this.changeHandlers	= [];
    this.isCombo	= [];
    this.sendHandler	= null;
    this.sent		= false;
    if (document.addEventListener) {
        document.addEventListener("keydown", EasyEdits.storeKey, false);
    } else {
        document.onkeydown	= EasyEdits.storeKey;
    }
    this.process	= function (json)    {
        EasyEdits.process(me,json);
        EasyEdits.execute(me);
    }
    this.reload	=	function (source)    {
        if (source) {
            EasyEdits.load(source,me);
        }
    }
    this.execute	= function ()  {
        EasyEdits.execute(me);
    }
    this.clear	= function ()    {
        if (this.formNode) {
            this.formNode.innerHTML = "";
        }
        this.currentZoom = 100;
    }
    this.validate	= function ()    {
        return EasyEdits.validate(me);
    }
    this.reset		= function ()    {
        EasyEdits.resetErrors(me);
    }
    this.setFormNode	= function (node)    {
        this.formNode = node;
    }
    this.enable	= function ()    {
        EasyEdits.enable(me);
    }
    this.disable	= function ()    {
        EasyEdits.disable(me);
    }
    this.zoomUp		= function ()    {
        EasyEdits.zoom(me,this.ratio)
    }
    this.zoomDown		= function ()    {
        EasyEdits.zoom(me,this.ratio*-1)
    }
    this.getJSON = function()    {
        return this.editsJSON;
    }
    this.send	= function (targetURL)    {
        EasyEdits.send(me,targetURL);
    }
    this.setValue = function (variable,value) {
        if (this.executed) {
            $('#variable').val(value);
        } else {
            this.values[variable] = value;
        }
    }
    this.submit	= function (targetURL)    {
        me.form.action = (targetURL) ? targetURL : me.form.action;
        $E(me.form.id).submit();
    }
    this.fetch	= function (JSONsource,callback)	{
        var async = callback ? true : false;
        var me    = this;
        if (JSONsource)	{
            (new EasyAjax(JSONsource)).then(function (response) {
                me.editsJSON = response
                if (callback) {
                    callback.apply(me,[response]);
                }
            }).post(async);
        }
    }
    if (source) {
        EasyEdits.load(source,me);
    }
    if (ref) {
        Edits[ref] = me;
    }
    return me;
}
/* ------------------------------------------------ */
EasyEdits.lastKey = null; //last key they pressed.
/* ------------------------------------------------ */
EasyEdits.process = function (easy,json) {
    if (json)	{
        easy.editsJSON	= json;
        easy.edits      = eval("("+ easy.editsJSON +")");
    }
}
/* ------------------------------------------------ */
EasyEdits.load = function (JSONsource,easy){
    if (JSONsource)	{
        easy.source	= JSONsource;
        (new EasyAjax(JSONsource)).then(function(response)	{
            if (response)	{
                    easy.editsJSON	= response;
                try {
                    easy.edits	= eval("("+ easy.editsJSON +")");
                } catch (ex) {
                    console.error(ex);
                    console.error(JSONsource);
                    console.error(easy.editsJSON);
                }
                EasyEdits.execute(easy);
            }
        }).get();
    }
}
/* ------------------------------------------------ */
EasyEdits.storeKey = function (evt){
    evt = (evt) ? evt : ((window.event) ? event : null);
    EasyEdits.lastKey = evt.keyCode;
}
/* ------------------------------------------------ */
EasyEdits.getCSSValue = function (field,name) {
	return (window.getComputedStyle) ? document.defaultView.getComputedStyle($E(field),null)[name] : $E(field).currentStyle[name];
}
/* ------------------------------------------------ */
EasyEdits.execute	= function (easy){
    //draw if necessary
    if ((easy.edits.form.drawme)&&(!$E(easy.edits.form.id)))	{
        var formHTML = '<form id="'+ easy.edits.form.id +'" name="'+ easy.edits.form.id +'" method="'+ easy.edits.form.method +'" action="'+ easy.edits.form.action +'" style="'+ easy.edits.form.style +'">';
        for (var i=0; i<easy.edits.fields.length; i++) {
            formHTML += EasyEdits.generateFormElementHTML(easy.edits.fields[i]);
        }
        formHTML += '</form>';
        if (easy.formNode) {
            $E(easy.formNode).innerHTML = formHTML;
        } else {
            document.body.innerHTML += formHTML;
        }
    }
    if (easy.edits.form.onenter) {
        switch (easy.edits.form.onenter.toLowerCase()) {
            case 'send' :
                break;
            case 'submit' :
                break;
            default:
                break;
        }
    }
    //setup widgets (if any)
    if (easy.edits.form.widgets){
        for (var widget in easy.edits.form.widgets)	{
            switch (widget)  {
                case	"calendar" 	:   
                    widget = easy.edits.form.widgets[widget];
                    EasyEdits.calendar = new DynamicCalendar();
                    $E(widget.layer).style.position = "absolute";
                    EasyEdits.calendar.setNode(widget.layer);
                    EasyEdits.calendar.setArrows(widget.arrows.left,widget.arrows.right,widget.arrows.height);
                    EasyEdits.calendar.weekend 		= widget.weekend;
                    EasyEdits.calendar.weekday 		= widget.weekday;
                    EasyEdits.calendar.monthname 	= widget.month;
                    EasyEdits.calendar.build();
                    EasyEdits.calendar.set(new Date().getMonth(),new Date().getFullYear());
                    EasyEdits.calendar.hide();
                    break;
                case	"timepicker"	:   
                    widget = easy.edits.form.widgets[widget];
                    EasyEdits.timepicker = new TimePicker(widget.layer);
                    $E(widget.layer).style.position = "absolute";
                    $E(widget.layer).style.width = "120px";
                    EasyEdits.timepicker.hide();
                    break;
                default			:   
                    break;
            }
        }
    }
    //form specific processing
    var form		= $E(easy.edits.form.id);
    if (easy.edits.form.action) {
        form.action = easy.edits.form.action;
    }
    if (easy.edits.form.method) {
        form.method = easy.edits.form.method
    }
    if (easy.edits.form.onchange) {
        form.onchange = easy.edits.form.onchange;
    }
    if (easy.edits.form.sumclass && easy.edits.form.sumfield) {
        form.setAttribute("sumclass",easy.edits.form.sumclass);
        form.setAttribute("sumfield",easy.edits.form.sumfield);
    }
    //form field level processing
    var defaultBackgroundColor = (easy.edits.form.fieldcolor) ? easy.edits.form.fieldcolor : "lightcyan";
    var formField	= null;
    for (var i=0; i<easy.edits.fields.length; i++) {
        var whereAt		= "";
        var isCombo 	= false;
        if ($E(easy.edits.fields[i].id) && easy.edits.fields[i].active)		{
            formField			= $E(easy.edits.fields[i].id);
            easy.edits.fields[i].ref    = formField;
            var easyField		= easy.edits.fields[i];
            easyField.jId               = '#'+easyField.id;
            easy.changeHandlers[easyField.id] = [];
            if (formField.onchange) {
                easy.changeHandlers[easyField.id][easy.changeHandlers[easyField.id].length] = formField.onchange;
            } try {
                easyField.ref		= formField;
                easyField.inerror	= false;
                if ((!formField.disabled) && (formField.type!="button")) {
                    formField.style.backgroundColor = defaultBackgroundColor;
                }
                if (easyField.required)	{
                    whereAt = "required";
                    //#a10f0a
                    //formField.style.border	= "1px solid "+easy.requiredColor;
                    formField.style.backgroundColor	= easy.requiredColor;
                    formField.setAttribute("required","Y");
                }
                /* -- mask Overriding Style			-- */
                if (easyField.style && easyField.style.trim())	{
                    whereAt = "style";
                    var styles = easyField.style.split(";");
                    for (var ii=0; ii<styles.length; ii++) {
                        var pair = styles[ii].split(":");
                        if (pair[0]) {
                            if (pair[0].indexOf("-")!=-1) {
                                var pre 	= pair[0].substr(0,pair[0].indexOf("-"));
                                var cap		= pair[0].substr(pair[0].indexOf("-")+1,1).toUpperCase();
                                var post 	= pair[0].substr(pair[0].indexOf("-")+2);
                                pair[0]		= pre+cap+post;
                            }
                            formField.style[pair[0].trim()] = pair[1].trim();
                        }
                    }
                }
                /* -- Setting Class						-- */
                if ((easyField.classname) || (easy.edits.form.classname)) {
                    whereAt = "className";
                    formField.className	= easyField.classname ? easyField.classname : easy.edits.form.classname;
                }
                if (easyField.type === "combo") {
                    isCombo = true;
                    formField.onclick = EasyEdits.resetLastKey;
                    formField.onfocus = EasyEdits.throwFocusAway;
                    var combo = $E(formField.id+"_combo");
                    if (!combo) {
                        alert('Combination Field not found... looking for '+formField.id+"_combo");
                    }
                    formField.tabIndex = 99;
                    formField.setAttribute("combo","yes");
                    if (easyField.removemask) {
                        formField.setAttribute("removeMask","yes");
                    } else {
                        formField.setAttribute("removeMask","no");
                    }
                    $(formField).on("change",(function (formField) {
                        return function () {
                            formField.setAttribute('comboValue',$(formField).val());
                        }
                    })(formField,combo));
                    combo.style.backgroundColor = EasyEdits.getCSSValue(easyField.id, "backgroundColor");
                    combo.style.margin = EasyEdits.getCSSValue(easyField.id, "margin");
                    combo.style.padding = EasyEdits.getCSSValue(easyField.id, "padding");
                    combo.style.display = "none";
                    combo.setAttribute("comboPair",easyField.id);
                    combo.onchange = function (evt) {	
                        evt = (evt) ? evt : ((window.event) ? event : null);
                        evt.target.setAttribute("comboValue",evt.target.value);
                        $E(evt.target.getAttribute("comboPair")).onchange(evt,true);
                    }
                }
                formField.isCombo = isCombo;
                formField.onchange = function (evt,calledFromComboPair)	{
                    evt = (evt) ? evt : ((window.event) ? event : null);
                    var isCombo = (this.getAttribute && (this.getAttribute("combo")=="yes"));
                    if (isCombo){
                        if (!calledFromComboPair){
                            if ($E(this.id).selectedIndex >= 0) {
                                $E(this.id + "_combo").value = $E(this.id)[$E(this.id).selectedIndex].text;
                                $E(this.id + "_combo").setAttribute("comboValue", $E(this.id)[$E(this.id).selectedIndex].value);
                                $('#'+this.id + "_combo").trigger('change');
                            }
                        }
                    }
                    for (var jj = 0; jj<easy.changeHandlers[this.id].length; jj++) {
                        easy.changeHandlers[this.id][jj](evt);
                    }
                    //this stops a potentially devastating cyclic reference between the drop down box and the combo text box, becareful about removing it...
                    if (isCombo) {
                        if (window.addEventListener) {
                            //evt.stopPropagation();
                        } else {
                            //evt.cancelBubble = true;
                        }
                    }
                }
                if (easyField.onchange)	{
                    if (!easy.changeHandlers[easyField.id]) {
                        easy.changeHandlers[easyField.id] = [];
                    }
                    easy.changeHandlers[easyField.id][easy.changeHandlers[easyField.id].length] = easyField.onchange;
                }
                if (easyField.sumfield && easyField.sumclass){
                    formField.setAttribute("sumclass",easyField.sumclass);
                    formField.setAttribute("sumfield",easyField.sumfield);
                }
                if (easyField.valuerange) {
                    var range = easyField.valuerange.split("..");
                    formField.length = 0;
                    formField[formField.length] = new Option('','');
                    for (var rCtr= +range[0]; rCtr<= +range[1]; rCtr++)	{
                        if (easyField.rangewidth) {
                            rCtr = EasyEdits.copies(rCtr,"0",easyField.rangewidth);
                        }   
                        formField[formField.length] = new Option(rCtr,rCtr);
                    }
		}
                if (easyField.onmouseover) {
                    $(easyField.jId).on("mouseover",easyField.onmouseover)
                }
                if (easyField.onmouseout) {
                    $(easyField.jId).on("mouseout",easyField.onmouseout);
                }
                if (easyField.onmousedown) {
                    $(easyField.jId).on("mousedown",easyField.onmousedown);
                }
                /* -- Template Matching 			-- */
                if (easyField.onclick) {
                    $(easyField.jId).on("click",easyField.onclick);
                }
                if (easyField.spellcheck){
                    formField.setAttribute("spellcheck","true");
                }
                if (easyField.maxlength) {
                    formField.maxLength	= easyField.maxlength;
                }
                if (easyField.remove) {
                    formField.setAttribute("remove",easyField.remove);
                }
                if (easyField.onfill) {
                    if (typeof(easyField.onfill)=="string")	{
                        formField.onfill = function () {
                            $E(this.getAttribute("nextField")).focus();
                        }
                        formField.setAttribute("nextField",easyField.onfill);
                    } else {
                        formField.onfill = easyField.onfill;
                    }
                }
                if (easyField.activate) {
                        $(easyField.jId).on("keydown",easyField.activate);
                }
                if (easyField.mask)	{
                    $(easyField.jId).on("keyup", function (evt) {
                        evt = (evt) ? evt : ((window.event) ? event : null);
                        if ((evt==null) ||  ((evt.keyCode != 39) && (evt.keyCode != 37) && (evt.keyCode!=46) && (evt.keyCode!=8))) {
                            var template    = this.getAttribute("template").split('');
                            var fieldVal    = this.value.split('');
                            var results     = [];
                            var tokens      = "*#A^H";
                            var hex         = "0123456789ABCDEF";
                            var tCtr        =  0;
                            var fCtr        =  0;
                            var lCtr        =  0;
                            while (fCtr<fieldVal.length) {
                                lCtr++;
                                if (lCtr>150) {
                                    fCtr = fieldVal.length;
                                }
                                if (tokens.indexOf(template[tCtr])!=-1)	{
                                    var cc = fieldVal[fCtr];
                                    switch (template[tCtr])	{
                                        case "#" :
                                            if (!(isNaN(parseInt(cc)) && (cc != ".") && (cc != "-"))) {
                                                results[results.length] = cc;
                                            }
                                            break;
                                        case "A" :
                                            if (!(!isNaN(parseInt(cc)))) {
                                                results[results.length] = cc;
                                            }
                                            break;
                                        case "^" :
                                            if (isNaN(parseInt(cc))) {
                                                var token	= template[tCtr+1];
                                                var pre = results[results.length-1];
                                                results[results.length-1] = 0;
                                                results[results.length] = pre;
                                                results[results.length] = token;
                                                tCtr = tCtr + 1;
                                            } else {
                                                results[results.length] = cc;
                                            }
                                            break;
                                        case "9" :	//go back and make sure up to last mask that it is zero prefixed
                                            break;
                                        case "H" :
                                            cc=(cc+'').toUpperCase();
                                            if (hex.indexOf(cc)!=-1) {
                                                results[results.length] = cc;
                                            }
                                            break;
                                        case "*" :
                                            results[results.length] = cc;
                                            break;
                                        default  :
                                            break;
                                    }
                                    fCtr = fCtr+1;
                                    tCtr = tCtr+1;
                                } else {
                                    if (template[tCtr] == fieldVal[fCtr]) {
                                        results[results.length] = fieldVal[fCtr];
                                        fCtr = fCtr + 1;
                                    } else {
                                        results[results.length] = template[tCtr];
                                    }
                                    tCtr = tCtr + 1;
                                }
                            }
                            this.value = results.join('');
                        }
                        results = [];
                        if (this.maxLength && this.onfill) {
                            if (this.value.length == this.maxLength) {
                                if ((evt.keyCode != 9) && (evt.keyCode != 16)) {
                                    this.onfill();
                                }
                            }
                        }
                        return false;
                    });
                    formField.setAttribute("template",easyField.mask);
                }
                if (easyField.readonly === true) {
                   // if (isCombo) {
                   //     $E(this.id + "_combo").readOnly = true;
                  //  }
                    formField.readOnly = true;
                }
                if (easyField.widget) {
                    switch (easyField.widget) {
                        case "datepicker" :
                            $(formField).datepicker()
                            break;
                        default :
                            break;
                    }
                }
                if (easyField.placeholder) {
                    formField.setAttribute('placeholder',easyField.placeholder);
                }
                if (easyField.disabled === true) {
                   // if (isCombo) {
                    //    alert(this.id);
                    //    $E(this.id + "_combo").disabled = true;
                    //}                    
                    formField.disabled = true
                } else {
                   // if (isCombo) {
                   //     $E(this.id + "_combo").disabled = false;
                   // }                        
                    formField.disabled = false;
                }
                /* -- Creating a rollover title			-- */
                if (!formField.title) {
                    if (easyField.title) {
                        formField.title	= easyField.title;
                        if (isCombo) {
                            $E(formField.id+"_combo").title = easyField.title;
                        }
                    } else {
                        formField.title	= easyField.longname;
                        if (isCombo) {
                            $E(formField.id+"_combo").title = easyField.longname;
                        }
                    }
                }
                /* -- Population of field				-- */
                if ((typeof(easyField.populator) !== "undefined") && (easyField.populator)) {
                    if (typeof(easyField.populator) === "string") {
                        (new EasyAjax(easyField.populator)).then(function(response) {
                            if (response) {
                                EasyEdits.populateSelectBox(this.field.id, JSON.parse(response),false);
                            }
                        }).get().field = easyField;  //graft an arbitrary field onto the edit ajax call
                    } else if (typeof(easyField.populator) === "object") {
                        (new EasyAjax(easyField.populator.url)).then(function(response) {
                            if (response) {
                                EasyEdits.populateSelectBox(this.field.id, JSON.parse(response),this.field.populator.fieldmap);
                            }
                        }).get().field = easyField;
                    } else {
                        easyField.populator();
                    }
		}
                /* -- Validation of field				-- */
                if ((typeof(easyField.validator) != "undefined") && (easyField.validator)) {
                    if (typeof(easyField.validator)=="function") {
                        formField.onblur	= easyField.validator;
                    } else if (typeof(easyField.validator)=="string")	{
                        formField.onblur = function (evt) {
                            (new EasyAjax(easyField.validator)).then(function(response) {
                                var enteredValue = JSON.parse(response);
                                if (!enteredValue.isValid) {
                                    evt.target.focus();
                                    alert("The value entered is not valid, please try again");
                                }
                            }).get(false);
                        }
                    }
                }
                if (easyField.dependencies)	{
                    whereAt = "dependencies";
                    formField.setAttribute("dependencies",easyField.dependencies);
                    var status = !$E(easyField.id).checked;
                    var dependencies = easyField.dependencies.split(",");
                    for (var j=0; j<dependencies.length; j++) {
                    if (status) {
                        $('#'+dependencies[j]).attr('disabled',true);
                    }
                    if ($E(dependencies[j]).getAttribute("combo")) {
                        $E(dependencies[j]+"_combo").disabled = status;
                    }
                }

                if (easyField.type == "radio")	{
                    var rbset = form.elements[easyField.group];
                    for (var i=0; i<rbset.length; i++) {
                        var rb = rbset[i]; var depElem = easyField.id; var dependencies = easyField.dependencies.split(",");
                        $E(rb.id).onclick = function (evt) {
                        evt = (evt) ? evt : event ? event : null;
                        var status = !$E(depElem).checked;
                            for (var k=0; k<rbset.length; k++) {
                                var rbi = rbset[k];
                                if (rbi.id == depElem) {
                                    for (var j=0; j<dependencies.length; j++) {
                                        $E(dependencies[j]).disabled = status;
                                        if ($E(dependencies[j]).getAttribute("combo")) {
                                                $E(dependencies[j]+"_combo").disabled = status;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    formField.tt = formField.onclick;
                    formField.onclick = function (evt)	{
                        evt = (evt) ? evt : window.event ? window.event : null;
                        if (this.tt) {
                                this.tt(evt);
                        }
                        if (evt) {
                            var dependencies = this.getAttribute("dependencies").split(",");
                            var status = !this.checked;
                            for (var j=0; j<dependencies.length; j++) {
                                $E(dependencies[j]).disabled = status;
                                if ($E(dependencies[j]).getAttribute("combo")) {
                                        $E(dependencies[j]+"_combo").disabled = status;
                                }
                            }
                        }
                    }
                }
            }
            if (easyField.maxchars) {
                whereAt = "maxchars";
                formField.setAttribute("maxchars",easyField.maxchars);
                formField.setAttribute("maxlines",((easyField.maxlines) ? easyField.maxlines : null));
                $(formField).on('keyup',function ()	{
                    var maxchars	= this.getAttribute("maxchars");
                    var maxlines	= this.getAttribute("maxlines");
                    if ((this.value.length >= maxchars)) {
                        this.style.backgroundColor = "#ffeeee";
                        this.value = this.value.substr(0,maxchars);
                    } else {
                        this.style.backgroundColor = defaultBackgroundColor;
                    }
                    this.title = (maxchars - this.value.length) + " letters left"
                    if (maxlines) {
                        var lines = this.value.split("\n");
                        if (lines.length > maxlines) {
                            lines = lines.slice(0,maxlines)
                            this.value = lines.join('\n');
                            this.style.backgroundColor = "#ffeeee";
                        }
                    }
                });
            }
            if (isCombo) {
                $(window).on("resize", function () { EasyEdits.resetCombos(easy); });
                EasyEdits.setCombo(formField, $E(easyField.id+"_combo"));
            }
            if ((easyField.onfocus) || (easy.edits.form.onfocus)) {
                Desktop.on(formField,"focus",((easyField.onfocus) ? easyField.onfocus : easy.edits.form.onfocus));
            }
            if ((easyField.onblur) || (easy.edits.form.onblur))	{
                Desktop.on(formField,"blur", ((easyField.onblur) ? easyField.onblur : easy.edits.form.onblur));
            }
            if ((easyField.onkeyup) || (easy.edits.form.onkeyup))	{
                Desktop.on(formField,"keyup",((easyField.onkeyup) ? easyField.onkeyup : easy.edits.form.onkeyup));
            }
            if ((easyField.onkeydown) || (easy.edits.form.onkeydown))	{
                Desktop.on(formField,"keydown", ((easyField.onkeydown) ? easyField.onkeydown : easy.edits.form.onkeydown));
            }
            easyField.baseX		= formField.offsetLeft;
            easyField.baseY		= formField.offsetTop;
            easyField.baseW		= formField.offsetWidth;
            easyField.baseH		= formField.offsetHeight;
            if (window.getComputedStyle) {
                var ref = document.defaultView.getComputedStyle(formField,null);
                easyField.baseFS		= ref["fontSize"];
                easyField.baseFF		= ref["fontFamily"];
            } else {
                easyField.baseFS		= $E(formField.id).currentStyle["fontSize"];
                easyField.baseFF		= $E(formField.id).currentStyle["fontFamily"];
            }
            if (easyField.value) {
                if ((easyField.type === "select") || (this.isCombo)) {
                    if (typeof(easyField.value) === "object") {
                        formField.length = 0;
                        for (var ij = 0; ij < easyField.value.length; ij++) {
                            formField[formField.length] = new Option(easyField.value[ij].text, easyField.value[ij].value);
                        }
                    } else {
                        alert("Was expecting an object, got " + typeof(easyField.value));
                    }
                } else {
                    formField.value = easyField.value;
                }
            }
        } catch (ex) {
            console.log(easyField.id+": "+whereAt+":  "+ ex);
	}
    } else {
        var field = $E(easy.edits.fields[i].id);
        if (!easy.edits.fields[i].active) {
            if (field) {
                field.parentNode.removeChild(field);
            }
	} else {
            if (!field) {
                if (easy.edits.fields[i].optional === true) {
                        //nop
                } else {
                    alert("A Field is missing: "+easy.edits.fields[i].id);
                }
            }
        }
    }
}
    if (easy.edits.form.onload) {
	easy.edits.form.onload();
    }
    EasyEdits.resetCombos(easy);
    easy.executed = true;
    for (var i in easy.values) {
        $('#'+i).val(easy.values[i]);
    }

}
/* ------------------------------------------------ */
EasyEdits.setCombo = function (formField,combo) {
    if (combo) {
        combo.style.display		= "none";
        var ref = (window.getComputedStyle) ? document.defaultView.getComputedStyle(formField,null) : $E(formField.id).currentStyle;

        for (var i=0; i<ref.length; i++) {
            if (ref[ref[i]] !== undefined) {
                if (ref[i].substr(0,1)==='-') {
                    continue;
                }
                combo.style[ref[i]] = ref[ref[i]];
            }
        }
        combo.style.borderWidth = '0px';
        combo.style.position	= "absolute";
        combo.style.left	= (parseInt(EasyEdits.getAbsoluteX(formField))+1)+"px";
        combo.style.top		= (parseInt(EasyEdits.getAbsoluteY(formField))+1)+"px";
        combo.style.display	= "block";
        var ow	= (document.addEventListener) ? 23 : 19;
        var oh	= 2;
        combo.style.padding	= "0px";
        combo.style.width	= (parseInt($E(formField.id).offsetWidth)-ow)+"px";
        combo.style.height	= (parseInt($E(formField.id).offsetHeight)-oh)+"px";
    }
}
/* ------------------------------------------------ */
EasyEdits.resetCombos = function (easy) {
    if (easy.edits && easy.edits.fields) {
        for (var i=0; i<easy.edits.fields.length; i++)	{
            if (easy.edits.fields[i].type == "combo") {
                EasyEdits.setCombo($E(easy.edits.fields[i].id),$E(easy.edits.fields[i].id+"_combo"));
            }
        }
    }
}
/* ------------------------------------------------ */
EasyEdits.resetErrors = function (easy) {
    easy.errors			= 0;
    easy.criticals		= 0;
    easy.warnings		= 0;
    easy.failures		= 0;
    easy.message		= "";
    easy.messages		= [];
    easy.flagged		= false;
}
/* ------------------------------------------------ */
EasyEdits.getAbsoluteX	= function (element,maxNode) {
    maxNode = (maxNode) ? maxNode : "DIV";
    var aX = element.offsetLeft;
    while (element.offsetParent && element.offsetParent.nodeName != maxNode) {
        element = element.offsetParent;
        aX += element.offsetLeft + ((element.scrollLeft) ? element.scrollLeft : 0);
    }
    return aX;
}
/* ------------------------------------------------ */
EasyEdits.getAbsoluteY	= function (element,maxNode)
{
    maxNode = (maxNode) ? maxNode : "DIV";
    var aY = element.offsetTop;
    while (element.offsetParent && (element.offsetParent.nodeName != maxNode)) {
        element = element.offsetParent;
        aY += element.offsetTop + ((element.scrollTop) ? element.scrollTop : 0);
    }
    return aY;
}
/* ------------------------------------------------ */
EasyEdits.manageDependencies = function (easy,disable) {
    for (var i=0; i<easy.edits.fields.length; i++)	{
        if (easy.edits.fields[i].dependencies) {
            var dependencies = easy.edits.fields[i].dependencies.split(",");
            if ($E(easy.edits.fields[i].id).checked) {
                for (var k=0; k<dependencies.length; k++) {
                    $E(dependencies[k]).disabled = disable;
                    $E(dependencies[k]).style.backgroundColor = (disable) ? "ghostwhite" : defaultBackgroundColor;
                    if ($E(dependencies[k]).getAttribute("combo")) {
                        $E($E(dependencies[k]).id+"_combo").disabled = disable;
                        $E($E(dependencies[k]).id+"_combo").style.backgroundColor = $E(dependencies[k]).style.backgroundColor;
                    }
                }
            } else {
                for (var k=0; k<dependencies.length; k++) {
                    $E(dependencies[k]).disabled = true;
                    $E(dependencies[k]).style.backgroundColor = "ghostwhite";
                    if ($E(dependencies[k]).getAttribute("combo")) {
                        $E($E(dependencies[k]).id+"_combo").disabled= true;
                        $E($E(dependencies[k]).id+"_combo").style.backgroundColor = "ghostwhite";
                    }
                }
            }
        }
    }
}
/* ------------------------------------------------ */
EasyEdits.enable	= function (easy) {
    for (var i=0; i<easy.edits.fields.length; i++)	{
        var formField = $E(easy.edits.fields[i].id)
        if ((easy.edits.fields[i].type == "text") || (easy.edits.fields[i].type == "textarea")) {
            formField.readOnly = false;
        } else {
            formField.disabled = false;
        }
        formField.style.backgroundColor = defaultBackgroundColor;
        if (easy.edits.fields[i].type.toLowerCase() == "combo")	{
            var comboField	= $E(easy.edits.fields[i].id+"_combo");
            comboField.readOnly = false;
            comboField.style.backgroundColor = defaultBackgroundColor;
        }
    }
    EasyEdits.manageDependencies(easy,false);
}
/* ------------------------------------------------ */
EasyEdits.disable	= function (easy) {
    for (var i=0; i<easy.edits.fields.length; i++)	{
        var formField = $E(easy.edits.fields[i].id)
        if ((easy.edits.fields[i].type == "text") || (easy.edits.fields[i].type == "textarea")) {
            formField.readOnly = true;
        } else {
            formField.disabled = true;
        }
        formField.style.backgroundColor = "ghostwhite";
        if (easy.edits.fields[i].type.toLowerCase() == "combo")	{
            var comboField	= $E(easy.edits.fields[i].id+"_combo");
            comboField.readOnly = true;
            comboField.style.backgroundColor = "ghostwhite";
        }
    }
    EasyEdits.manageDependencies(easy,true);
}
/* ------------------------------------------------ */
EasyEdits.getValue = function (form,field,easyField) {
    var val	= "";
    //if (!field.type) {
      //  return val; //you aren't a form element
   // }
    
    try {
        var type = (easyField) ? easyField.type : field.type;
        //console.log(field.id+','+type);
        switch (type.toLowerCase()) {
            case "text"			:
            	val = field.value;
                break;
            case "hidden"		:	
                val = field.value;
                break;
            case "textarea"		:	
                val = field.value;
                break;
            case "checkbox" 	:	
                if (field.checked) {
                    val = (field.value) ? field.value : "true";
                }
		break;
            case "rte"          :
                if (RTE[field.id]) {
                    val = RTE[field.id].getText();
                }
                break;
            case "password"		:	
                val = field.value;
		break;
            case "radio"		:   
                var cbArray = form.elements[field.name]; var cbCtr = 0;
                while ((cbCtr < cbArray.length) && (!val))	{
                        val = cbArray[cbCtr].checked ? cbArray[cbCtr].value : "";
                        cbCtr++;
                }
                break;
            case "combo"        :
            case "select"       :
            case "select-one"	:
                if (field.getAttribute && field.getAttribute("combo")) {
                    val = ($E(field.id+"_combo").value) ? $E(field.id+"_combo").value : $E(field.id+"_combo").getAttribute("comboValue");
                } else {
                    val = field[field.selectedIndex].value;
                }
                if ((field.getAttribute("removeMask") == "yes") && (field.getAttribute("combo"))) {
                    var newval = '';
                    var template = $E(field.id + "_combo").getAttribute("template");
                    var parseVal = $E(field.id + "_combo").value;
                    if (template) {
                        var mask = template.split('');
                        var chars = parseVal.split('');
                        var tokens = "*#A^H";
                        for (var cc = 0; cc < chars.length; cc++) {
                            if (tokens.indexOf(mask[cc]) != -1)	{
                                newval = newval + '' + chars[cc];
                            }
                        }
                        val = newval;
                    }
                }
                break;
            default			:
                break;
        }
    } catch (ex) {
        console.log(field);
        console.log(ex);
    }
    if (typeof(val) == "string") {
            val = val.trim();
    }
    return val;
}
/* ------------------------------------------------ */
EasyEdits.registerError = function (easy,field,edit,message,force) {
    if (easy.messages.indexOf(message) == -1) {
        easy.messages[easy.messages.length] = message;
        easy.errors++;
        easy.message	+= (easy.errors+") "+message+"\n");
        field.style.backgroundColor	= "red";
        if (edit.type == "combo") {
            $E(field.id+"_combo").style.backgroundColor = "red";
        }
        edit.inerror				= true;
        easy.flagged				= true;
        if (force) {
            easy.criticals++;
        } else {
            easy.warnings++;
        }
       // field.value = "";
    }
}
/* ------------------------------------------------ */
EasyEdits.packageEdits = function (ao,easy) {
    var form    = $E(easy.form.id);
    var field   = null;
    for (var i=0; i<easy.fields.length; i++) {
        if (easy.fields[i].active) {
            field = (easy.fields[i].name) ? easy.fields[i].name : easy.fields[i].id;
            ao.add(field,EasyEdits.getValue(form,$E(easy.fields[i].id),easy.fields[i]));
        }
    }
}
/* ------------------------------------------------ */
EasyEdits.send	= function (easy,URL) {
    var target      = (URL) ? URL : easy.edits.form.action;
    var getMethod   = (easy.edits.form.method.toLowerCase() == "get");
    var response    = '';
    if (target)	{
        var ao = new EasyAjax(target);
        ao.setQueryString(EasyEdits.packageEdits(easy,ao));
        if (easy.sendHandler) {
            ao.SCF = (typeof(easy.sendHandler) == "string") ? eval(easy.sendHandler) : easy.sendHandler;
        } else {
            ao.then(function () {  });
        }
        response = (getMethod) ? ao.get() : ao.post();
    } else	{
        alert("No target action");
    }
    return response;
}
/* ------------------------------------------------ */
EasyEdits.validate 	= function (easy)
{
    var form        = $E(easy.edits.form.id);
    var fieldValues = [];
    var easyFields  = [];
    easy.hasContent = false;
    for (var i=0; i<easy.edits.fields.length; i++) {
        easyFields[easy.edits.fields[i].id] = easy.edits.fields[i];  //pre-load hash table
    }
    var defaultBackgroundColor = (easy.edits.form.fieldcolor) ? easy.edits.form.fieldcolor : "lightcyan";
    for (var i=0; i<easy.edits.fields.length; i++) {
        if (easy.edits.fields[i].active) {
            try {
                if ($E(easy.edits.fields[i].id).disabled) {
                    continue;
                }
            } catch (ex) {
                alert('disabled field');
            }
            var formField   = $E(easy.edits.fields[i].id);
            var easyField   = easy.edits.fields[i];
            if (formField)	{
                var fieldVal = "";
                if (fieldValues[formField.name]) {
                    fieldVal 	= fieldValues[formField.name];
                } else {
                    fieldVal	= fieldValues[formField.name] = EasyEdits.getValue(form,formField,easyField);
                }
                if (fieldVal) {
                    easy.hasContent = true;
                }
                var action 		= easyField.force;
                easy.flagged	= false;
                easyFields[easyField.id] = easyField;
                if (easyField.required)	{
                    if ((fieldVal=="") || (fieldVal == null))	{
                        var inerror = true;
                        if (easyField.eitheror) {
                            var fields = easyField.eitheror.split(",");
                            inerror = (!(EasyEdits.getValue(form,$E(fields[0])) || EasyEdits.getValue(form,$E(fields[1]))));
                        }
                        if (inerror) {
                            if (easyField.force) {
                                EasyEdits.registerError(easy,formField,easyField,(easyField.message ? easyField.message : easyField.longname+" is Required"),action);
                            } else {
                                EasyEdits.registerError(easy,formField,easyField,(easyField.message ? easyField.message : easyField.longname+" is Recommended"),action);
                            }
                        }
                    }
                }
                if (easyField.nozero) {
                    if (fieldVal === "0") {
                        EasyEdits.registerError(easy,formField,easyField,easyField.longname+" is not allowed to be zero (0)",action);
                    }
                }
                if (easyField.verify) {
                    if (fieldVal !== $E(easyField.verify).value) {
                        EasyEdits.registerError(easy,formField,easyField,easyField.longname+" did not match",action);
                    }
                }
                if ((fieldVal === "") && (easyField.defaultvalue) && (!easy.flagged)) {
                    formField.value = easyField.defaultvalue;
                }
                if ((easyField.range) && (!easy.flagged)) {
                    var range	= easyField.range.split("..");
                    if ((+parseFloat(fieldVal) < +parseFloat(range[0]) || (+parseFloat(fieldVal) > +parseFloat(range[1])))) {
                        EasyEdits.registerError(easy,formField,easyField,easyField.longname+" not within allowable Range ("+ range[0] +","+ range[1] +")",action);
                    }
                }
                if ((easyField.values) && (!easy.flagged)) {
                    var values	= (easyField.values.split(","));
                    if (values.indexOf(fieldVal) === -1) {
                        EasyEdits.registerError(easy,formField,easyField,easyField.longname+" Contains an Invalid Value, valid values are "+ easyField.values,action);
                    }
                }
                if (easyField.minlength) {
                    if ((fieldVal.length < easyField.minlength) && (fieldVal.length !== 0))	{
                        EasyEdits.registerError(easy,formField,easyField,easyField.longname +" is only "+ fieldVal.length +" characters long.  A minimum of "+ easyField.minlength +" characters are Required",action);
                    }
                }
                if (easyField.maxlength) {
                    if (fieldVal.length > easyField.maxlength) {
                        formField.value = fieldVal.substr(0,easyField.maxlength);
                    }
                }
                if (easyField.eitheror)	{
                    var fields = easyField.eitheror.split(",");
                    if (EasyEdits.getValue(form,$E(fields[0])) && EasyEdits.getValue(form,$E(fields[1]))) {
                        EasyEdits.registerError(easy,formField,easyField,easyFields[fields[0]].longname+" and "+easyFields[fields[1]].longname +" are mutually exclusive, both can not be chosen",action);
                    }
                }
                if (easyField.anyof) {
                    var fields = easyField.anyof.split(",");
                }
                if (easyField.atleastone) {
                    var fields = []; var checked = false;
                    if (typeof(easyField.atleastone)=="object") {
                        fields = easyField.atleastone;
                    } else {
                        fields = easyField.atleastone.split(',');
                    }
                    for (var x in fields) {
                       checked = checked || $E(fields[x]).checked;
                    }
                    if (!checked) {
                        var msg = "You must select one of the following: ";
                        for (var x in fields) {
                            msg += easyFields[fields[x]].longname+', '
                        }
                        EasyEdits.registerError(easy,formField,easyField,msg.substr(0,msg.length-2),action);
                    }
                }
                if ((easyField.format) && (!easy.flagged)) {
                    if (formField.value) {
                        var regEx = new RegExp(easyField.format)
                        if (!regEx.test(fieldVal)) {
                                EasyEdits.registerError(easy,formField,easyField,easyField.longname +" Format Error. "+ easyField.formaterr,action);
                        }
                    }
                }
                if ((easyField.swap) && (!easy.flagged)) {
                }
                if ((typeof(easyField.onsubmit)=="function")) {
                }
            } else {
                alert("Form is missing a field: "+easyField.field);
            }
	}
    }
    var doitAnyway	= false;
    if (easy.errors !== 0) {
        if (easy.criticals > 0)	{
            alert("The following Critical Errors have occurred:\n\n"+easy.message);
        } else if (easy.warnings > 0)	{
            doitAnyway	= confirm("Warning!\n\n" + easy.message);
        } else if (easy.failures > 0) {
            alert("Warning, some non-critical warning messages were encountered:\n\n"+easy.message);
        }
        var resetFocus = false;
        for (var i=0; i<easy.edits.fields.length; i++) {
            var easyField = easy.edits.fields[i];
            if (easyField.inerror) {
                $E(easyField.id).style.backgroundColor = (easy.edits.fields[i].required) ? easy.requiredColor : defaultBackgroundColor;
                easyField.inerror = false;
                if (!resetFocus) {
                    $E(easyField.id).focus();
                    resetFocus = true;
                }
                if (easyField.type == "combo") {
                    $E(easyField.id+"_combo").style.backgroundColor = defaultBackgroundColor;
                }
            }
        }
    }
    var doit	= true;
    if (easy.criticals != 0) {
        doit = false;
    } else {
        if (easy.warnings > 0) {
            doit	= doitAnyway;
        }
    }
    easy.reset();
    EasyEdits.resetCombos(easy);
    return doit;
}
/* ------------------------------------------------ */
EasyEdits.zoom = function (easy,ratio) {
    if (easy.currentZoom >= 10)
        easy.currentZoom += ratio;
    var zoomRatio = (easy.currentZoom/100);

    for (var i=0; i<easy.edits.fields.length; i++)  {
        if (easy.edits.fields[i].active)
        {
            var formField = easy.edits.fields[i].ref;
            if (formField.parentNode.nodeName != "TD")
                if (formField.style.position != "absolute")
                    formField.style.position = "absolute";
            formField.style.top				= parseInt(easy.edits.fields[i].baseY * zoomRatio)+"px";
            formField.style.left 			= parseInt(easy.edits.fields[i].baseX * zoomRatio)+"px";
            formField.style.height			= parseInt(easy.edits.fields[i].baseH * zoomRatio)+"px";
            formField.style.width			= parseInt(easy.edits.fields[i].baseW * zoomRatio)+"px";
            formField.style.fontSize		= parseInt(parseFloat(easy.edits.fields[i].baseFS) * zoomRatio) + "px";
            if (easy.edits.fields[i].type.toLowerCase() == "combo")
            {
                var combo = $E(formField.id+"_combo");
                combo.style.top				= (EasyEdits.getAbsoluteY(formField)+2)+"px";
                combo.style.left 			= (EasyEdits.getAbsoluteX(formField)+2)+"px";
                combo.style.height			= parseInt((easy.edits.fields[i].baseH * zoomRatio)-4)+"px";
                combo.style.width			= parseInt((easy.edits.fields[i].baseW * zoomRatio)-19)+"px";
                combo.style.fontSize		= parseInt(parseFloat(easy.edits.fields[i].baseFS) * zoomRatio) + "px";
            }
        }
    }
}
/* ------------------------------------------------ */
EasyEdits.setFormNode	= function (easy,node) {
    easy.formNode = node;
}
/* ------------------------------------------------ */
EasyEdits.populateSelectBox = function (selectBox, contents, map, leaveCombo) {
    map       = (map) ? map : false;
    leaveCombo = (leaveCombo) ? true : true; //Override for now... fixes a problem with some highly dynamic forms
    contents  = (typeof(contents)==='string') ? JSON.parse(contents) : contents;
    selectBox = (typeof(selectBox)=="string") ? $E(selectBox) : selectBox;
    if (selectBox)	{
        if (selectBox.getAttribute("combo") == "yes") {
            if ($E($E(selectBox).id + "_combo")) {
                if (!(leaveCombo===true)) {
                    //$E($E(selectBox).id + "_combo").value = "";
                }
            }
        }
        if (selectBox.childNodes.length > 0) {
            var tn = selectBox.childNodes.length;
            for (var n=0; n<tn; n++) {
                selectBox.removeChild(selectBox.childNodes[0]);
            }
        }
        var isIE        = !window.addEventListener;
        var saveLabel   = "";
        var text        = '';
        var value       = '';
        var label       = '';
        var f_t         = false;
        var f_v         = false;
        var oOption     = null;
        var oGroup      = null;
        for (var jk in contents) {
            label   = (contents[jk].label) ? contents[jk].label : false;
            text    = (contents[jk].text && !map) ? contents[jk].text : '';
            value   = (contents[jk].value && !map) ? contents[jk].value : '';
            title   = (contents[jk].title && !map) ? contents[jk].title : '';
            if (!text && map) {
                if (typeof(map.text)==="object") {
                    text = '';
                    for (var jkk in map.text) {
                        text += contents[jk][map.text[jkk]]+' ';
                    }
                } else {
                    text = contents[jk][map.text];
                }
            };
            if (!value && map) {
                if (typeof(map.value)==="object") {
                    value = '';
                    for (var jkk in map.value) {
                        value += contents[jk][map.value[jkk]];
                    }
                } else {
                    value = contents[jk][map.value];
                }

            }
            if (f_t === false) {
                f_t = text;
                f_v = value;
            }
            if ((label) && (label != saveLabel))	{
                if (oGroup) {
                    selectBox.appendChild(oGroup);
                }
                oGroup = document.createElement('OPTGROUP');
                saveLabel = oGroup.label = label;
            }
            oOption = document.createElement('OPTION');
            oOption.value = value;
            if (title) {
                oOption.title = title;
            }
            if (contents[jk].style) {
                oOption.style = contents[jk].style;
            }
            if (isIE) {
                oOption.innerText = text;
            } else {
                oOption.text = text;
            }
            if (oGroup) {
                oGroup.appendChild(oOption);
            } else {
                selectBox.appendChild(oOption);
            }
        }
        if (oGroup) {
            selectBox.appendChild(oGroup);
        }
        if (selectBox.getAttribute("combo") == "yes") {
            if ($E($E(selectBox).id + "_combo") && (!(leaveCombo===true))) {
                //$E($E(selectBox).id + "_combo").value = f_t;
                $E($E(selectBox).id + "_combo").setAttribute("comboValue",f_v) ;
            }
        }
    } else {
        alert("I was supposed to populate a drop down box, but I didnt find it");
    }
}
/* ------------------------------- */
EasyEdits.getElementId	= function (evt) {
    var objectID = "";

    if (document.addEventListener) {
        objectID = evt.target.id ? evt.target.id : evt.currentTarget.id;
    } else {
        if (evt.srcElement.id !== "") {
            objectID = evt.srcElement.id;
        } else if (evt.srcElement.parentElement.id !== "") {
            objectID = evt.srcElement.parentElement.id;
        } else if (evt.srcElement.parentElement.parentElement.id !== "") {
            objectID = evt.srcElement.parentElement.parentElement.id;
        }
    }
    return objectID;
}
/* ------------------------------------------------ */
EasyEdits.throwFocusAway = function (evt) {
    if (EasyEdits.lastKey == 9) 	{
        evt = (evt) ? evt : ((window.event) ? event : null);
        var evtId = EasyEdits.getElementId(evt);
        $E(evtId+"_combo").focus();
    }
}
/* ------------------------------------------------ */
EasyEdits.resetLastKey = function (evt){
    EasyEdits.lastKey = 0;
}
/* ------------------------------------------------ */
EasyEdits.generateFormElementHTML	= function (edit) {
	var elementHTML = "";
	edit.value = (edit.value) ? edit.value : "";
	switch (edit.type.toLowerCase()) {
		case "button"	:
            elementHTML = "<input type='button' name='"+ edit.id +"' id='"+ edit.id +"' value='"+ edit.value +"' />";
			break;
		case "text" 	:
            elementHTML = "<input type='text' name='"+edit.id+"' id='"+edit.id+"' value='"+ edit.value +"' />";
			break;
		case "select"	:
            elementHTML = "<select combo='' name='"+edit.id+"' id='"+edit.id+"' ><option value=''></option></select>";
			break;
		case "combo"	:
            elementHTML = "<select tabindex='99' onclick='EasyEdits.resetLastKey()' onfocus='EasyEdits.throwFocusAway(event)' onchange='EasyEdits.setComboBox(event)' combo='yes' name='"+edit.id+"' id='"+edit.id+"' ><option value=''></option></select>";
			elementHTML += '<input class="comboBox" type="text" name="'+edit.id+'_combo" id="'+edit.id+'_combo" style="border: 0px;" />';
			break;
		case "radio"	:
            elementHTML = "<input type='radio' name='"+edit.group+"' id='"+edit.id+"' />";
			break;
		case "textarea"	:
            elementHTML = "<textarea name='"+edit.id+"' id='"+edit.id+"' >"+ edit.value +"</textarea>";
			break;
		case "password"	:
            elementHTML = "<input type='password' name='"+edit.id+"' id='"+edit.id+"' />";
			break;
		case "checkbox"	:
            elementHTML = "<input type='checkbox' name='"+edit.id+"' id='"+edit.id+"' value='"+edit.value+"' />";
			break;
		default			:	break;
	}
	return elementHTML;
}
/* ------------------------------------------------ */
EasyEdits.validDOB = function (evt) {
	evt = (evt) ? evt : ((window.event) ? event : null);
	if (this.value.trim() !== "") {
		var dateFields = this.value.split('/');
		var badDate = false;
		var curYear = new Date().getFullYear();
		badDate = (!dateFields[0] || !dateFields[1] || !dateFields[2])
		if (!badDate) { badDate = (parseInt(dateFields[0], 10) > 12) };
		if (!badDate) { badDate = (parseInt(dateFields[1], 10) < 1) || (parseInt(dateFields[1], 10) > 31 ) };
		if (parseInt(dateFields[2]) < 100) {
			if (parseInt(dateFields[2]) < 18)	{
				dateFields[2] += "";
				if (dateFields[2].length === 1) {
					dateFields[2] = "0"+""+dateFields[2];
                }
				dateFields[2] = "20"+""+dateFields[2];
			} else {
				dateFields[2] = "19"+""+dateFields[2];
            }
		}
		if (!badDate) {
			this.value = dateFields[0]+"/"+dateFields[1]+"/"+dateFields[2];
        }
		if (!badDate) { badDate = (parseInt(dateFields[2], 10) < 1900) || (parseInt(dateFields[2], 10) > curYear) };
		if (badDate) {
			alert("The date you have entered ("+ this.value +") is invalid.  Please enter a valid date in MM/DD/YYYY format.");
			var thing = (evt.currentTarget.id) ? evt.currentTarget.id : evt.srcElement.id;
			$E(thing).focus();
			return false;
		}
	}
	return true;
}
/* ------------------------------------------------ */
EasyEdits.copies	= function (orig,pad,len) {
    orig = ''+orig;  //render a string;
    if (orig.length < len) {
        while (orig.length < len) {
            orig = pad+''+orig;
        }
    }
    return orig;
}
/* ------------------------------------------------ */
EasyEdits.monthsList	= function (evt) {
    var month = "January,February,March,April,May,June,July,August,September,October,November,December".split(",");
    var sb    = $E(this.id);
    for (var m = 0; m < month.length; m++)	{
        var mm = +m+1; //this makes it sequenced from 01, but might want it to be 00 indexed...
        mm = ((mm+'').length ==1) ? "0"+mm : mm;
        sb[sb.length] = new Option(month[m],mm);
    }
}
/* ------------------------------------------------ */
EasyEdits.stateList	= function (evt) {
    var sr = $E(this.id);
    var stateCodes	= new Array("AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VT","VA","WA","WV","WI","WY","PR","VI","MP","GU","AS","PW");
    sr.length = stateCodes.length+1;
    sr[0].text = "";
    sr[0].value = "";
    sr[0].selected = true;
    for (var i=0; i<stateCodes.length; i++)	{
        sr[i+1].text 	= stateCodes[i];
        sr[i+1].value 	= stateCodes[i];
    }
}
/* ------------------------------------------------ */
EasyEdits.setSelectBoxByText	= function (selectBox,txt) {
	if (typeof(selectBox)=="string") {
		selectBox = $E(selectBox);
    }
	var found=false; var ctr = 0;
	while ((!found) && (ctr<selectBox.length))	{
		found = selectBox[ctr].selected = (txt == selectBox[ctr].text);
		ctr++;
	}
}
/* ------------------------------------------------ */
EasyEdits.getChildNodes	= function (nodes){
	var nodeList = [];
	for (var i=0; i<nodes.childNodes.length; i++)	{
		nodeList[nodeList.length] = nodes.childNodes[i];
		if (nodes.childNodes[i].childNodes) {
			if (nodes.childNodes[i].childNodes.length > 0) {
				nodeList.append(EasyEdits.getChildNodes(nodes.childNodes[i]));
            }
        }
	}
	return nodeList;
}
/* ------------------------------------------------ */
EasyEdits.sum		= function (evt) {
	evt = (evt) ? evt : ((window.event) ? event : null);
	var sumClass = this.getAttribute("sumclass");
	var sumField = this.getAttribute("sumfield");
	var nodeList = [];
	for (var i=0; i<this.childNodes.length; i++) {
		if (this.childNodes[i].childNodes.length > 0) {
			nodeList.append(EasyEdits.getChildNodes(this.childNodes[i]));
        }
    }
	var sum = 0;
	for (var i=0; i<nodeList.length; i++) {
		if ((nodeList[i].className) && (nodeList[i].className == sumClass)) {
			sum += +nodeList[i].value;
        }
    }
	$E(sumField).value = sum;
}
EasyEdits.timepicker	= null;
EasyEdits.calendar		= null;
/* ------------------------------------------------ */
EasyEdits.showTimePicker	= function (field,evt)
{
	var node = EasyEdits.timepicker.getNode();
	node.style.left = EasyEdits.getAbsoluteX(field) + "px";

	node.style.top	= (+EasyEdits.getAbsoluteY(field) + +field.offsetHeight +2)+ "px";
	TimePicker.field = field;
	node.show();
}
EasyEdits.calendar		= null;
/* ------------------------------------------------ */
EasyEdits.showCalendar	= function (field,evt)
{
	var node = EasyEdits.calendar.getNode();
	var control = evt.target || evt.srcElement;
	node.style.left = EasyEdits.getAbsoluteX(field) + "px";

	node.style.top	= (+EasyEdits.getAbsoluteY(field) + +field.offsetHeight +2)+ "px";
	node.show();
	var handler = new EasyEdits.dayHandler()
	handler.setField(field);
	EasyEdits.calendar.setDayHandler(handler.fire);
}
/* ------------------------------------------------ */
EasyEdits.hideCalendar = function ()
{
    if (EasyEdits.calendar)
        EasyEdits.calendar.getNode().style.display = "none";
}
/* ------------------------------------------------ */
EasyEdits.dayHandler = function () {
    var fld = null;
    this.setField = function (field) {
        fld = field;
    }
    this.fire = function (mm,dd,yyyy) {
        fld.value = (+mm+1)+"/"+dd+"/"+yyyy;
        fld.onkeyup();
        fld.onchange();
        fld.focus();
        EasyEdits.hideCalendar();
    }
}
/* ------------------------------------------------ */
EasyEdits.timeHandler = function () {
    var fld = null;
    this.setField = function (field) {
        fld = field;
    }
    this.fire = function (mm,dd,yyyy) {
        fld.value = (+mm+1)+"/"+dd+"/"+yyyy;
        fld.onkeyup();
        fld.onchange();
        fld.focus();
        EasyEdits.hideCalendar();
    }
}
/* ------------------------------------------------ */
EasyEdits.validDate = function (evt) {
    if (this.value.trim() !== "") 	{
        var token       = this.value.substr(2,1);
        var dateFields  = this.value.split(token);
        var badDate     = (!dateFields[0] || !dateFields[1] || !dateFields[2])
        if (!badDate) {
            badDate     = (parseInt(dateFields[0], 10) > 12);
        }
        if (!badDate) {
            badDate     = (parseInt(dateFields[1], 10) < 1) || (parseInt(dateFields[1], 10) > 31);
        }
        if (parseInt(dateFields[2]) < 100) {
                //curYear % 2000
            if (parseInt(dateFields[2])<11) {
                dateFields[2] += "";
                if (dateFields[2].length == 1) {
                    dateFields[2] = "0"+""+dateFields[2];
                }
                dateFields[2] = "20"+""+dateFields[2];
            } else {
                dateFields[2] = "19"+""+dateFields[2];
            }   
        }
        if (!badDate) {
            this.value = dateFields[0]+token+dateFields[1]+token+dateFields[2];
        }
    }
    return true;
}