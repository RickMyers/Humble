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
function EasyEdits(source, ref, overrides)
{
    var me		= this;
    this.hasContent	= false;
//    this.requiredColor	= "#ee3333";
//    this.requiredColor	= "#ffebc9";
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
    this.changeHandlers	= {};
    this.isCombo	= [];
    this.cbradios       = {};
    this.sendHandler	= null;
    this.sent		= false;
    this.overrides      = (overrides) ? overrides : [];
    this.formXref       = {};
    this.form           = null;
    this.defaults = {
        "required": {
            "background-color": "#ffebc9",
            "classes":          false,
            "style":            false
        },
        "optional": {
            "background-color": "lightcyan",
            "classes":          false,
            "style":            false
        }
    };
    if (document.addEventListener) {
        document.addEventListener("keypress", EasyEdits.storeKey, false);
    } else {
        document.onkeypress	= EasyEdits.storeKey;
    }
    /* ------------------------------------------------------------------------- */
    this.process = function (easy,json) {
        if (json)	{
            this.editsJSON  = json;
            this.edits      = eval("("+ easy.editsJSON +")");
        }
    }    
    /* ------------------------------------------------------------------------- */    
    this.reload	=	function (source)    {
        if (source) {
            this.load(source);
        }
    }
    this.parse = (response,overrides) => {
        if (response)	{
            for (var i in overrides) {
                let f = new RegExp(i,'gi');
                response = response.replace(f,overrides[i]);
            }                
            this.editsJSON	= response;
            try {
                this.edits	= eval("("+ me.editsJSON +")");
            } catch (ex) {
                console.error(ex);
            }
            this.execute();
        }        
    }
    /* ------------------------------------------------------------------------- */    
    this.load = (JSONsource) => {
        if (JSONsource)	{
            let me          = this;
            this.source	= JSONsource;
            (new EasyAjax(JSONsource)).then((response) => {
                me.parse(response,me.overrides);

            }).get();
        }
    };
    /* ------------------------------------------------------------------------- */        
    this.clear	= () => {
        if (this.formNode) {
            this.formNode.innerHTML = "";
        }
        this.currentZoom = 100;
    };
    /* ------------------------------------------------------------------------- */            
    this.new  = (form,alias,options) => {
        this.edits = {
            "form":     {},
            "fields":   []
        };
        this.edits.form.id = form;
        if (options) {
            let i = false;
            for (i in options) {
                this.edits.form[i] = options[i];            
            }
        }
        return Edits[alias] = this;
    };
    /* ------------------------------------------------------------------------- */     
    this.element  = (field) => {
        return $('#'+this.edits.form.id+' [name='+field+']').get()[0];
    }
    /* ------------------------------------------------------------------------- */     
    this.add            = (field,options) => {
        var field = {
            "name": field,
            "active": true,
            'type': $(this.element(field)).attr('type')
        };
        if (options) {
            let i = '';
            for (i in options) {
                field[i] = options[i];
            }
        }
        this.edits.fields[this.edits.fields.length] = field;
        return this;
    };
    /* ------------------------------------------------------------------------- */
    this.registerError  = (field,edit,message,force) => {
        if (this.messages.indexOf(message) == -1) {
            this.messages[this.messages.length] = message;
            this.errors++;
            this.message	+= (this.errors+") "+message+"\n");
            field.style.backgroundColor	= "red";
            if (edit.type == "combo") {
                this.formXref[field.easyKey+'_combo'].style.backgroundColor = "red";
            }
            edit.inerror				= true;
            this.flagged				= true;
            if (force) {
                this.criticals++;
            } else {
                this.warnings++;
            }
           // field.value = "";
        }        
    }
    /* ------------------------------------------------------------------------- */    
    this.validate 	= () =>  {
        this.hasContent = false;
        var easyFields  = {};
        var formField,easyField,fieldVal   = null;
        var defaultBackgroundColor = this.defaults.optional['background-color'];
        for (var i=0; i<this.edits.fields.length; i++) {
            easyFields[this.edits.fields[i].easyKey] = this.edits.fields[i];    //Reverse caching the edit fields by name instead of index for later lookup
        }
        for (var i=0; i<this.edits.fields.length; i++) {
            formField = this.formXref[this.edits.fields[i].easyKey];
            easyField   = this.edits.fields[i];            
            if (this.edits.fields[i].active) {
                try {
                    if (formField.disabled) {
                        continue;
                    }
                } catch (ex) {
                    alert('disabled field');
                }
                if (formField)	{
                    let longname = easyField.longname ? easyField.longname : easyField.easyKey;
                    fieldVal = this.getValue(this.form,formField,easyFields[formField.easyKey]);
                    if (fieldVal) {
                        this.hasContent = true;
                    }
                    var action 		= easyField.force;
                    this.flagged	= false;
                    if (easyField.required)	{
                        if ((fieldVal=="") || (fieldVal == null))	{
                            var inerror = true;
                            if (easyField.eitheror) {
                                var fields = easyField.eitheror.split(",");
                                inerror = (!(this.getValue(this.form,this.formXref[fields[0]]) || this.getValue(this.form,this.formXref[fields[1]])));
                            }
                            if (inerror) {
                                if (easyField.force) {
                                    this.registerError(formField,easyField,(easyField.message ? easyField.message : longname+" is Required"),action);
                                } else {
                                    this.registerError(formField,easyField,(easyField.message ? easyField.message : longname+" is Recommended"),action);
                                }
                            }
                        }
                    }
                    if (easyField.nozero) {
                        if (fieldVal === "0") {
                            this.registerError(formField,easyField,longname+" is not allowed to be zero (0)",action);
                        }
                    }
                    if (easyField.verify) {
                        if (fieldVal !== $(this.formXref[easyField.verify]).val()) {
                            this.registerError(formField,easyField,longname+" did not match",action);
                        }
                    }
                    if ((fieldVal === "") && (easyField.defaultvalue) && (!this.flagged)) {
                        formField.value = easyField.defaultvalue;
                    }
                    if ((easyField.range) && (!this.flagged)) {
                        var range	= easyField.range.split("..");
                        if ((+parseFloat(fieldVal) < +parseFloat(range[0]) || (+parseFloat(fieldVal) > +parseFloat(range[1])))) {
                            this.registerError(formField,easyField,longname+" not within allowable Range ("+ range[0] +","+ range[1] +")",action);
                        }
                    }
                    if ((easyField.values) && (!this.flagged)) {
                        var values	= (easyField.values.split(","));
                        if (values.indexOf(fieldVal) === -1) {
                            this.registerError(formField,easyField,longname+" Contains an Invalid Value, valid values are "+ easyField.values,action);
                        }
                    }
                    if (easyField.minlength) {
                        if ((fieldVal.length < easyField.minlength) && (fieldVal.length !== 0))	{
                            this.registerError(formField,easyField,longname +" is only "+ fieldVal.length +" characters long.  A minimum of "+ easyField.minlength +" characters are Required",action);
                        }
                    }
                    if (easyField.maxlength) {
                        if (fieldVal.length > easyField.maxlength) {
                            formField.value = fieldVal.substr(0,easyField.maxlength);
                        }
                    }
                    if (easyField.eitheror)	{
                        var fields = easyField.eitheror.split(",");
                        if (this.getValue(this.form,this.formXref[fields[0]]) && this.getValue(this.form,this.formXref[fields[1]])) {
                            this.registerError(formField,easyField,easyFields[fields[0]].longname+" and "+easyFields[fields[1]].longname +" are mutually exclusive, both can not be chosen",action);
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
                           checked = checked || this.formXref[fields[x]].checked;
                        }
                        if (!checked) {
                            var msg = "You must select one of the following: ";
                            for (var x in fields) {
                                msg += easyFields[fields[x]].longname+', '
                            }
                            this.registerError(formField,easyField,msg.substr(0,msg.length-2),action);
                        }
                    }
                    if ((easyField.format) && (!this.flagged)) {
                        if (formField.value) {
                            var regEx = new RegExp(easyField.format)
                            if (!regEx.test(fieldVal)) {
                                this.registerError(formField,easyField,longname +" Format Error. "+ easyField.formaterr,action);
                            }
                        }
                    }
                    if ((easyField.swap) && (!this.flagged)) {
                    }
                    if ((typeof(easyField.onsubmit)=="function")) {
                    }
                } else {
                    alert("Form is missing a field: "+easyField.field);
                }
            }
        }
        var doitAnyway	= false;
        if (this.errors !== 0) {
            if (this.criticals > 0)	{
                alert("The following Critical Errors have occurred:\n\n"+this.message);
            } else if (this.warnings > 0)	{
                doitAnyway	= confirm("Warning!\n\n" + this.message);
            } else if (this.failures > 0) {
                alert("Warning, some non-critical warning messages were encountered:\n\n"+this.message);
            }
            var resetFocus = false;
            for (var i=0; i<this.edits.fields.length; i++) {
                var easyField = this.edits.fields[i];
                if (easyField.inerror) {
                    this.formXref[easyField.easyKey].style.backgroundColor = (this.edits.fields[i].required) ? this.defaults.required['background-color'] : this.defaults.optional['background-color'];
                    easyField.inerror = false;
                    if (!resetFocus) {
                        this.formXref[easyField.easyKey].focus();
                        resetFocus = true;
                    }
                    if (easyField.type == "combo") {
                        //change to have it inherit properties again from the select box
                        this.formXref[easyField.easyKey+'_combo'].style.backgroundColor = defaultBackgroundColor;
                    }
                }
            }
        }
        var doit	= true;
        if (this.criticals != 0) {
            doit = false;
        } else {
            if (this.warnings > 0) {
                doit	= doitAnyway;
            }
        }
        this.reset();
        this.resetCombos();
        return doit;
    }    
    /* ------------------------------------------------------------------------- */        
    this.reset		= function ()    {
        this.resetErrors();
    }
    /* ------------------------------------------------------------------------- */    
    this.packageEdits = (ao) => {
        var field   = null;
        for (var i=0; i<this.edits.fields.length; i++) {
            if (this.edits.fields[i].active) {
                field = this.formXref[this.edits.fields[i].easyKey];
                ao.add(field.name,this.getValue(this.form,field,easy.edits.fields[i]));
            }
        }
    }    
    /* ------------------------------------------------------------------------- */    
    this.resetErrors = () => {
        this.errors		= 0;
        this.criticals		= 0;
        this.warnings		= 0;
        this.failures		= 0;
        this.message		= "";
        this.messages		= [];
        this.flagged		= false;
    }    
    /* ------------------------------------------------------------------------- */    
    this.resetCombos = function () {
        if (this.edits && this.edits.fields) {
            for (var i=0; i<this.edits.fields.length; i++)	{
                if (this.edits.fields[i].type === "combo") {
                    EasyEdits.setCombo(this.formXref[this.edits.fields[i].easyKey],this.formXref[this.edits.fields[i].easyKey+"_combo"]);
                }
            }
        }
    };    
    /* ------------------------------------------------------------------------- */        
    this.setFormNode	= function (node)    {
        this.formNode = node;
    }
    /* ------------------------------------------------------------------------- */        
    this.enable	= () => {
        for (var i=0; i<this.edits.fields.length; i++)	{
            var formField = this.formXref[this.edits.fields[i].easyKey];
            if ((this.edits.fields[i].type === "text") || (this.edits.fields[i].type === "textarea")) {
                formField.disabled = false;
            } else {
                formField.disabled = false;
            }
            formField.style.backgroundColor = this.defaults.optional['background-color'];
            if (this.edits.fields[i].type.toLowerCase() === "combo")	{
                var comboField                   = this.formXref[formField.easyKey+"_combo"];
                comboField.readOnly              = false;
                comboField.style.backgroundColor = this.defaults.optional['background-color'];
            }
        }
        this.manageDependencies(false);
    };
    /* ------------------------------------------------------------------------- */        
    this.disable	= ()  =>  {
        for (var i=0; i<this.edits.fields.length; i++)	{
            var formField = this.formXref[this.edits.fields[i].easyKey];
            if ((this.edits.fields[i].type === "text") || (this.edits.fields[i].type === "textarea")) {
                formField.disabled = true;
            } else {
                formField.disabled = true;
            };
            formField.style.backgroundColor = "ghostwhite";
            if (this.edits.fields[i].type.toLowerCase() === "combo")	{
                var comboField	= this.formXref[this.edits.fields[i].easyKey+"_combo"];
                comboField.readOnly = true;
                comboField.style.backgroundColor = "ghostwhite";
            };
        }
        this.manageDependencies(true); //true = disable=true?
    };    
    /* ------------------------------------------------------------------------- */    
    this.manageDependencies = (disable) => {
        for (var i=0; i<this.edits.fields.length; i++)	{
            if (this.edits.fields[i].dependencies) {
                var dependencies = this.edits.fields[i].dependencies.split(",");
                if (this.formXref[this.edits.fields[i].easyKey].checked) {
                    for (var k=0; k<dependencies.length; k++) {
                        this.formXref[dependencies[k]].disabled = disable;
                        this.formXref[dependencies[k]].style.backgroundColor = (disable) ? "ghostwhite" : this.defaults.optional['background-color'];
                        if (this.formXref[dependencies[k]].getAttribute("combo")) {
                            this.formXref[dependencies[k]+'_combo'].disabled = disable;
                            this.formXref[dependencies[k]+'_combo'].style.backgroundColor = this.formXref[dependencies[k]].style.backgroundColor;
                        }
                    }
                } else {
                    for (var k=0; k<dependencies.length; k++) {
                        this.formXref[dependencies[k]].disabled = true;
                        this.formXref[dependencies[k]].style.backgroundColor = "ghostwhite";
                        if (this.formXref[dependencies[k]].getAttribute("combo")) {
                            this.formXref[dependencies[k]+'_combo'].disabled= true;
                            this.formXref[dependencies[k]+'_combo'].style.backgroundColor = "ghostwhite";
                        }
                    }
                }
            }
        }
    }    
    /* ------------------------------------------------------------------------- */        
    this.zoomUp		= function ()    {
        EasyEdits.zoom(me,this.ratio)
    }
    /* ------------------------------------------------------------------------- */        
    this.zoomDown		= function ()    {
        EasyEdits.zoom(me,this.ratio*-1)
    }
    /* ------------------------------------------------------------------------- */        
    this.getJSON = function()    {
        return this.editsJSON;
    }
    /* ------------------------------------------------------------------------- */        
    this.send	= function (targetURL)    {
        var target      = (targetURL) ? targetURL : this.edits.form.action;
        var getMethod   = (this.edits.form.method.toLowerCase() == "get");
        var response    = '';
        if (target)	{
            var ao = new EasyAjax(target);
            ao.setQueryString(this.packageEdits(ao));
            if (this.sendHandler) {
                ao.then = this.sendHandler;
            } else {
                ao.then(function (response) {  });
            }
            response = (getMethod) ? ao.get() : ao.post();
        } else	{
            alert("No target action");
        }
        return response;        
    }
    /* ------------------------------------------------------------------------- */        
    this.setValue = function (variable,value) {
        if (this.executed) {
            $('#variable').val(value);
        } else {
            this.values[variable] = value;
        }
    }
    /* ------------------------------------------------------------------------- */        
    this.submit	= function (targetURL)    {
        me.form.action = (targetURL) ? targetURL : me.form.action;
        $E(me.form.id).submit();
    }
    /* ------------------------------------------------------------------------- */        
    this.fetch	= function (JSONsource,callback)	{
        var async = callback ? true : false;
        var me    = this;
        if (JSONsource)	{
            (new EasyAjax(JSONsource)).then((response) => {
                me.editsJSON = response
                if (callback) {
                    callback.apply(me,[response]);
                }
            }).post(async);
        }
    }
    /* ------------------------------------------------------------------------- */        
    this.execute	= () => {
        let easy = this;
        //draw if necessary
        if (this.edits.form.defaults) {
            this.defaults.required["background-color"] = this.edits.form.defaults.required['background-color'] || this.defaults.required['background-color'];
            this.defaults.required.classname           = this.edits.form.defaults.required.classname           || this.defaults.required.classname;
            this.defaults.required.style               = this.edits.form.defaults.required.style               || this.defaults.required.style;
            this.defaults.optional["background-color"] = this.edits.form.defaults.optional['background-color'] || this.defaults.optional['background-color'];
            this.defaults.optional.classname           = this.edits.form.defaults.optional.classname           || this.defaults.optional.classname;
            this.defaults.optional.style               = this.edits.form.defaults.optional.style               || this.defaults.optional.style;
        }
        if ((this.edits.form.drawme) && (!document.getElementById(this.edits.form.id)))	{
            var formHTML = '<form id="'+ this.edits.form.id +'" name="'+ this.edits.form.id +'" method="'+ this.edits.form.method +'" action="'+ this.edits.form.action +'" style="'+ this.edits.form.style +'">';
            for (var i=0; i<this.edits.fields.length; i++) {
                formHTML += EasyEdits.generateFormElementHTML(this.edits.fields[i]);
            }
            formHTML += '</form>';
            if (this.formNode) {
                document.getElementById(this.formNode).innerHTML = formHTML;
            } else {
                document.body.innerHTML += formHTML;
            }
        }
        if (this.edits.form.onenter) {
            switch (this.edits.form.onenter.toLowerCase()) {
                case 'send' :
                    break;
                case 'submit' :
                    break;
                default:
                    break;
            }
        }
        //setup widgets (if any)
        if (this.edits.form.widgets){
            for (var widget in this.edits.form.widgets)	{
                switch (widget)  {
                    case	"calendar" 	:   
                        widget = this.edits.form.widgets[widget];
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
                        widget = this.edits.form.widgets[widget];
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
        this.form = this.edits.form.ref     = document.getElementById(this.edits.form.id);

        if (!this.form) {
            alert('Edits: '+this.edits.form.id+' Not Found');
            return false;
        }
        if (this.edits.form.action) {
            this.form.action = this.edits.form.action;
        }
        if (this.edits.form.method) {
            this.form.method = this.edits.form.method;
        }
        if (this.edits.form.onchange) {
            this.form.onchange = this.edits.form.onchange;                               //event delegation onchange handler
        }
        if (this.edits.form.sumclass && this.edits.form.sumfield) {
            this.form.setAttribute("sumclass",this.edits.form.sumclass);
            this.form.setAttribute("sumfield",this.edits.form.sumfield);
        }
        //form field level processing
        var formField	= null;
        var easyField   = null;
        var easyKey     = "";    
        var whereAt	= "";
        var isCombo	= false;
        
        for (var i=0; i<this.form.elements.length; i++) {
            this.formXref[((this.form.elements[i].id) ? this.form.elements[i].id : this.form.elements[i].name)] = this.form.elements[i];
        }
        for (var i=0; i<this.edits.fields.length; i++) {
            whereAt	= "";
            isCombo	= false;
            if (this.edits.fields[i].active)		{
                //Here, switch to using the form element and name
                easyKey                     = this.edits.fields[i].name || this.edits.fields[i].id;
                if (!easyKey) {
                    alert('An EasyEdit field definition is missing a name or id attribute, see console');
                    console.log(this.edits.fields[i]); 
                    continue;
                }
                formField                   = this.formXref[easyKey];
                if (!formField) {
                    alert('Edit field not found in form, see console')
                    console.log('Missing formfield');
                    console.log(this.edits.fields[i]);
                    continue;
                }
                easyField                    = this.edits.fields[i];                //shorthand reference to the edits json definition
                easyField.easyKey            = easyKey;                
                formField.easyKey            = easyKey;
                formField.form               = this.edits.form.id;
                this.edits.fields[i].ref     = formField;                           //reference to the actual field in the form

                this.changeHandlers[easyKey] = [];
                if (formField.onchange) {
                    this.changeHandlers[easyKey][this.changeHandlers[easyKey].length] = formField.onchange; //if there is already an onchange event, add it to the list of handlers
                    formField.onchange                                                = null;  
                } try {
                    easyField.ref	= formField;
                    easyField.inerror	= false;
                    if (easyField.learning && easyField.learning === true) {
                        easyField.type = (easyField.type === 'select') ? 'combo' : easyField.type;
                    }
                    if ((!formField.disabled) && (easyField.type !== "button") ) {
                        formField.style.backgroundColor = this.defaults.optional['background-color'];
                    }
                    if (easyField.required)	{
                        whereAt = "required";
                        //#a10f0a
                        //formField.style.border	= "1px solid "+this.requiredColor;
                        formField.style.backgroundColor	= this.defaults.required['background-color'];
                        formField.setAttribute("required","Y");
                    }
                    /* -- mask Overriding Style			-- */
                    if (easyField.style && easyField.style.trim())	{
                        whereAt = "style";
                        var styles = easyField.style.split(";");
                        for (var ii=0; ii<styles.length; ii++) {
                            var pair = styles[ii].split(":");
                            if (pair[0].trim()) {
                                if (pair[0].indexOf("-") !== -1) {
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
                    if ((easyField.classname) || (this.edits.form.classname)) {
                        whereAt = "className";
                        formField.className	= easyField.classname ? easyField.classname : this.edits.form.classname;
                    }
                    if (easyField.type === "combo") {
                        whereAt           = 'combo';
                        isCombo           = true;
                        formField.onclick = EasyEdits.resetLastKey;
                        formField.onfocus = EasyEdits.throwFocusAway;
                        let combo = $("#"+this.edits.form.id+" [name='"+formField.easyKey+"_combo']").get()[0];
                        if (!combo) {
                            let c = document.createElement('input');
                            c.name = formField.easyKey+'_combo';
                            c.type = 'text';
                            c.value = '';
                            formField.after(c);
                        }
                        formField.combo   = $("#"+this.edits.form.id+" [name='"+formField.easyKey+"_combo']").get()[0]
                        if (!formField.combo) {
                            alert('Combination Field not found... looking for '+formField.easyKey+"_combo");
                        }
                        formField.tabIndex = 99;
                        formField.setAttribute("combo","yes");
                        if (easyField.removemask) {
                            formField.setAttribute("removeMask","yes");
                        } else {
                            formField.setAttribute("removeMask","no");
                        }
                        $(formField).on("change",( (field,combo) => {
                            //watch out, this is a closure...
                            return () => {
                                field.setAttribute('combovalue',$(field).val());
                                combo.setAttribute('combovalue',$(field).val());
                                $(combo).val($(field).val());
                            }
                        })(formField,formField.combo));
                        formField.combo.style.backgroundColor = EasyEdits.getCSSValue(formField, "backgroundColor");
                        formField.combo.style.margin          = EasyEdits.getCSSValue(formField, "margin");
                        formField.combo.style.padding         = EasyEdits.getCSSValue(formField, "padding");
                        formField.combo.style.display         = "none";
                        formField.combo.setAttribute("comboPair",easyKey);
                        $(formField.combo).on("change",((field,combo)=> {
                            return () => {
                                $(field).val($(combo).val());
                                combo.setAttribute('combovalue',$(combo).val());
                            }
                        })(formField,formField.combo))
                    }
                    formField.isCombo  = isCombo;
                    $(formField).on('change',((field,combo) => {
                        return (evt) => {
                            if (field.isCombo) {
                                if (field.selectedIndex >= 0) {
                                    value = field[field.selectedIndex].text;
                                    combo.setAttribute("combovalue", field[field.selectedIndex].value);
                                    $(combo).val(field[field.selectedIndex].value)
                                    //combo.onchange(evt,true);               //calledFromComboPair=true
                                }
                                
/*                              if (window.addEventListener) {
                                    //evt.stopPropagation();
                                } else {
                                    //evt.cancelBubble = true;
                                }*/

                            }
                            for (var jj = 0; jj<this.changeHandlers[formField.easyKey].length; jj++) {
                                this.changeHandlers[formField.easyKey][jj](evt);
                            }

                        }
                    })(formField,formField.combo));
                    if (easyField.onchange)	{
                        let me = formField;
                        if (!this.changeHandlers[formField.easyKey]) {
                            this.changeHandlers[formField.easyKey] = [];
                        }
                        this.changeHandlers[formField.easyKey][this.changeHandlers[formField.easyKey].length] = easyField.onchange;
                        $(formField).on('change',(evt) => {
                            for (let i in easy.changeHandlers[me.easyKey]) {
                                this.changeHandlers[me.easyKey][i](evt);
                            }
                        });
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
                    if ((easyField.type === 'checkbox') && (easyField.cbradio)) {
                        formField.setAttribute('cbradio',easyField.cbradio);
                        console.log('setting radio group '+easyField.cbradio);
                        console.log(this.cbradios);
                        if (!this.cbradios[easyField.cbradio]) {
                            this.cbradios[easyField.cbradio] = {};
                        }
                        this.cbradios[easyField.cbradio][easyField.easyKey] = formField;
                        let cbgroup = this.cbradios[easyField.cbradio];
                        $(formField).on('click',(evt) => {
                            for (var i in cbgroup) {
                                if (cbgroup[i] !== evt.target) {
                                    cbgroup[i].checked = false;
                                } 
                            }
                        });
                    }
                    if (easyField.onmouseover) {
                        $(formField).on("mouseover",easyField.onmouseover)
                    }
                    if (easyField.onmouseout) {
                        $(formField).on("mouseout",easyField.onmouseout);
                    }
                    if (easyField.onmousedown) {
                        $(formField).on("mousedown",easyField.onmousedown);
                    }
                    /* -- Template Matching 			-- */
                    if (easyField.onclick) {
                        $(formField).on("click",easyField.onclick);
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
                    if (easyField.onenterkey) {
                        var f = ((callback) => {
                            return (evt) => {
                                if (evt.keyCode === 13) {
                                    callback(evt);
                                    evt.stopPropagation();
                                }
                            }
                        })(easyField.onenterkey);
                        (isCombo) ? (combo.onkeypress = f) : (formField.onkeypress = f);
                    }
                    if (easyField.onfill) {
                        if (typeof(easyField.onfill)=="string")	{
                            formField.setAttribute("nextField",easyField.onfill);                            
                            let nextField = this.formXref[formField.getAttribute("nextField")];
                            formField.onfill = function () {
                                nextField.focus();
                            }
                        } else {
                            formField.onfill = easyField.onfill;
                        }
                    }
                    if (easyField.activate) {
                        $(easyField.ref).on("keyup",easyField.activate);
                    }
                    if (easyField.mask)	{
                        $(easyField.ref).on("keyup", function (evt) {
                            evt = (evt) ? evt : ((window.event) ? event : null);
                            if ((evt==null) ||  ((evt.keyCode != 39) && (evt.keyCode != 37) && (evt.keyCode != 46) && (evt.keyCode != 8))) {
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
                        if (isCombo) {
                            formField.combo.readOnly = true;
                        }
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
                        if (isCombo) {
                            formField.combo.disabled = true;
                        }                    
                        formField.disabled = true
                    } else {
                        if (isCombo) {
                            formField.combo.disabled = false;
                        }                        
                        formField.disabled = false;
                    }
                    /* -- Creating a rollover title			-- */
                    if (!formField.title) {
                        if (easyField.title) {
                            formField.title	= easyField.title;
                            if (isCombo) {
                                formField.combo.title = easyField.title;
                            }
                        } else {
                            formField.title	= longname;
                            if (isCombo) {
                                formField.combo.title = longname;
                            }
                        }
                    }
                    /* -- Population of field				-- */
                    if ((typeof(easyField.populator) !== "undefined") && (easyField.populator)) {
                        if (typeof(easyField.populator) === "string") {
                            let ef = easyField;
                            let ff = formField;
                            (new EasyAjax(easyField.populator)).then(function(response) {
                                if (response) {
                                    EasyEdits.populateSelectBox(ff, JSON.parse(response),false);
                                }
                            }).get();
                        } else if (typeof(easyField.populator) === "object") {
                            let ef = easyField;
                            (new EasyAjax(easyField.populator.url)).then(function(response) {
                                if (response) {
                                    EasyEdits.populateSelectBox(ef.id, JSON.parse(response),ef.populator.fieldmap);
                                }
                            }).get();
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
                        //FIX THIS!
                        whereAt          = "dependencies";
                        formField.setAttribute("dependencies",easyField.dependencies);
                        var status       = !this.formXref[formField.easyKey].checked;
                        var dependencies = easyField.dependencies.split(",");
                        for (var j=0; j<dependencies.length; j++) {
                            $(this.formXref[dependencies[j]]).attr('disabled',status);
                            if (this.formXref[dependencies[j]].getAttribute("combo")) {
                                this.formXref[dependencies[j]+'_combo'].disabled = status;
                            }
                        }

                        if (easyField.type == "radio")	{
                            var rbset = this.form.elements[easyField.group];
                            for (var i=0; i<rbset.length; i++) {
                                var depElem       = formField.easyKey; 
                                var dependencies  = easyField.dependencies.split(",");
                                formField.onclick = (evt) => {
                                    evt = (evt) ? evt : event ? event : null;
                                    var status = !document.getElementById(depElem).checked;
                                    for (var k=0; k<rbset.length; k++) {
                                        var rbi = rbset[k];
                                        if (rbi.id == depElem) {
                                            for (var j=0; j<dependencies.length; j++) {
                                                this.formXref[dependencies[j]].disabled = status;
                                                if (this.formXref[dependencies[j]].getAttribute("combo")) {
                                                    this.formXref[dependencies[j]+'_combo'].disabled = status;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            formField.tt = formField.onclick;                   //convert this to some kind of event handler array... this is super cheesey
                            formField.onclick = (evt) => {
                                evt = (evt) ? evt : window.event ? window.event : null;                                
                                var field = easy.formXref[evt.target.easyKey];
                                if (this.tt) {
                                    this.tt(evt);
                                }
                                if (evt) {
                                    var dependencies = field.getAttribute("dependencies").split(",");
                                    var status = !field.checked;
                                    for (var j=0; j<dependencies.length; j++) {
                                        easy.formXref[dependencies[j]].disabled = status;
                                        if (easy.formXref[dependencies[j]].getAttribute("combo")) {
                                            easy.formXref[dependencies[j]+'_combo'].disabled = status;
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
                    $(formField).on('keyup',(evt) => {
                        var maxchars	= this.getAttribute("maxchars");
                        var maxlines	= this.getAttribute("maxlines");
                        if ((this.value.length >= maxchars)) {
                            this.style.backgroundColor = "#ffeeee";
                            this.value = this.value.substr(0,maxchars);
                        } else {
                            this.style.backgroundColor = this.defaults.optional['background-color'];
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
                    $(window).on("resize", () => { easy.resetCombos(); });
                    EasyEdits.setCombo(formField, formField.combo);
                }
                if ((easyField.onfocus) || (this.edits.form.onfocus)) {
                    EasyEdits.on(formField,"focus",((easyField.onfocus) ? easyField.onfocus : this.edits.form.onfocus));
                }
                if ((easyField.onblur) || (this.edits.form.onblur))	{
                    EasyEdits.on(formField,"blur", ((easyField.onblur) ? easyField.onblur : this.edits.form.onblur));
                }
                if ((easyField.onkeyup) || (this.edits.form.onkeyup))	{
                    EasyEdits.on(formField,"keyup",((easyField.onkeyup) ? easyField.onkeyup : this.edits.form.onkeyup));
                }
                if ((easyField.onkeydown) || (this.edits.form.onkeydown))	{
                    EasyEdits.on(formField,"keydown", ((easyField.onkeydown) ? easyField.onkeydown : this.edits.form.onkeydown));
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
                    easyField.baseFS		= document.getElementById(formField.id).currentStyle["fontSize"];
                    easyField.baseFF		= document.getElementById(formField.id).currentStyle["fontFamily"];
                }
                if (easyField.value) {
                    if ((easyField.type === "select") || (isCombo)) {
                        if (typeof(easyField.value) === "object") {
                            formField.length = 0;
                            for (var ij = 0; ij < easyField.value.length; ij++) {
                                formField[formField.length] = new Option(easyField.value[ij].text, easyField.value[ij].value);
                            }
                        } else {
                            alert("Was expecting an object, got " + typeof(easyField.value) + ' ['+easyField.value+']');
                        }
                    } else {
                        formField.value = easyField.value;
                    }
                }
            } catch (ex) {
                console.log(easyKey+": "+whereAt+":  "+ ex+ " ["+ex.lineNumber+"]");
            }
        } else {
            //Here as well, switch to using form and name
            //var field = $E(this.edits.fields[i].id);
            var field   = this.formXref[this.edits.fields[i].easyKey];
            if (!this.edits.fields[i].active) {
                if (field) {
                    field.parentNode.removeChild(field);
                }
            } else {
                if (!field) {
                    console.log('Missing field follows:');
                    console.log(this.edits.fields[i]);
                    if (this.edits.fields[i].optional === true) {
                            //nop
                    } else {
                        alert("A Field is missing: "+this.edits.fields[i].id);
                    }
                }
            }
        }
    }
    /* ------------------------------------------------------------------------- */    
    this.getValue = (form,field,easyField) => {
        var val	= "";
        try {
            var type = (easyField) ? easyField.type : field.type;
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
                        val = $(this.formXref[field.easyKey+"_combo"]).val() ? $(this.formXref[field.easyKey+"_combo"]).val() : field.getAttribute("combovalue");
                    } else {
                        val = field[field.selectedIndex].value;
                    }
                    if ((field.getAttribute("removeMask") == "yes") && (field.getAttribute("combo"))) {
                        var combo       = this.formXref[field.easyKey+"_combo"];
                        var newval      = '';
                        var template    = combo.getAttribute("template");
                        var parseVal    = combo.value;
                        if (template) {
                            var mask    = template.split('');
                            var chars   = parseVal.split('');
                            var tokens  = "*#A^H";
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
    /* ------------------------------------------------------------------------- */ 
        if (this.edits.form.onload) {
            this.edits.form.onload();
        }
        this.resetCombos();
        this.executed = true;
        for (var i in this.values) {
            $('#'+i).val(this.values[i]);
        }
    }
    
    if (source) {
        this.load(source);
    }
    if (ref) {
        Edits[ref] = me;
    }
    return this;
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.lastKey = null; //last key they pressed.
    /* ------------------------------------------------------------------------- */    
EasyEdits.storeKey = function (evt){
    evt = (evt) ? evt : ((window.event) ? event : null);
    EasyEdits.lastKey = evt.keyCode;
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.getCSSValue = function (field,name) {
    return (window.getComputedStyle) ? document.defaultView.getComputedStyle(field,null)[name] : field.currentStyle[name];
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.setCombo = (formField,combo) => {
    if (combo) {
        combo.style.display		= "none";
        var ref = (window.getComputedStyle) ? document.defaultView.getComputedStyle(formField,null) : formField.currentStyle;
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
        //23 : 19
        var ow	= (document.addEventListener) ? 23 : 19;
        var oh	= 2;
        combo.style.padding	= "0px";
        combo.style.width	= (parseInt(formField.offsetWidth)-ow)+"px";
        combo.style.height	= (parseInt(formField.offsetHeight)-oh)+"px";
    }
};
    /* ------------------------------------------------------------------------- */    
EasyEdits.on = (obj,event,handler) => {
    if (typeof obj === 'string') {
        obj = document.getElementById(obj);
    }
    $(obj).on(event,handler);
};
    /* ------------------------------------------------------------------------- */    
EasyEdits.off = (obj,event,handler) => {
    if (typeof obj === 'string') {
        obj = document.getElementById(obj);
    }
    $(obj).off(event,handler);
};
    /* ------------------------------------------------------------------------- */    
EasyEdits.getAbsoluteX	= (element,maxNode) => {
    maxNode = (maxNode) ? maxNode : "DIV";
    var aX = element.offsetLeft;
    while (element.offsetParent && element.offsetParent.nodeName != maxNode) {
        element = element.offsetParent;
        aX += element.offsetLeft + ((element.scrollLeft) ? element.scrollLeft : 0);
    }
    return aX;
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.getAbsoluteY	= (element,maxNode) => {
    maxNode = (maxNode) ? maxNode : "DIV";
    var aY = element.offsetTop;
    while (element.offsetParent && (element.offsetParent.nodeName != maxNode)) {
        element = element.offsetParent;
        aY += element.offsetTop + ((element.scrollTop) ? element.scrollTop : 0);
    }
    return aY;
}
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
EasyEdits.setFormNode	= function (easy,node) {
    easy.formNode = node;
}
    /* ------------------------------------------------------------------------- */    
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
                $E($E(selectBox).id + "_combo").setAttribute("combovalue",f_v) ;
            }
        }
    } else {
        alert("I was supposed to populate a drop down box, but I didnt find it");
    }
}
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
EasyEdits.throwFocusAway = function (evt) {
    if (EasyEdits.lastKey == 9) 	{
        evt = (evt) ? evt : ((window.event) ? event : null);
        var evtId = EasyEdits.getElementId(evt);
        $E(evtId+"_combo").focus();
    }
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.resetLastKey = function (evt){
    EasyEdits.lastKey = 0;
}
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
EasyEdits.copies	= function (orig,pad,len) {
    orig = ''+orig;  //render a string;
    if (orig.length < len) {
        while (orig.length < len) {
            orig = pad+''+orig;
        }
    }
    return orig;
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.monthsList	= function (evt) {
    var month = "January,February,March,April,May,June,July,August,September,October,November,December".split(",");
    //FIX THIS!
    var sb    = $E(this.id);
    for (var m = 0; m < month.length; m++)	{
        var mm = +m+1; //this makes it sequenced from 01, but might want it to be 00 indexed...
        mm = ((mm+'').length ==1) ? "0"+mm : mm;
        sb[sb.length] = new Option(month[m],mm);
    }
}
    /* ------------------------------------------------------------------------- */    
EasyEdits.stateList	= function (evt) {
    //FIX THIS!
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
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
EasyEdits.showTimePicker	= function (field,evt)
{
    var node = EasyEdits.timepicker.getNode();
    node.style.left = EasyEdits.getAbsoluteX(field) + "px";

    node.style.top	= (+EasyEdits.getAbsoluteY(field) + +field.offsetHeight +2)+ "px";
    TimePicker.field = field;
    node.show();
}
EasyEdits.calendar		= null;
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
EasyEdits.hideCalendar = function ()
{
    if (EasyEdits.calendar)
        EasyEdits.calendar.getNode().style.display = "none";
}
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
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
    /* ------------------------------------------------------------------------- */    
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