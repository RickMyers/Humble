function $import(jsScriptPath) { var script = document.createElement("script"); script.type = "text/javascript"; script.src = jsScriptPath; document.getElementsByTagName("head")[0].appendChild(script); }
function $E(objID) {	if (typeof(objID)=="string") return document.getElementById(objID);	else return objID; }
function $$E(objID){ if (typeof(objID)=="string") { if (document.getElementById(objID)) return document.getElementById(objID).style; } else return objID;}
/* Main AJAX Class  ************************************************************************************ */
var E = function (id) {
    if (EasyElements[id] != undefined) {
        return ($E(EasyElements[id].id) ? $E(EasyElements[id].id) : new EasyElement(undefined) )
    } else {
        return new EasyElement[id];
    }
}
window.console = (window.console) ? window.console : { active: false, log: function (what) { if (window.console.active) alert(what); }, warn: function (what) { if (window.console.active) alert(what); }, error: function (what) { if (window.console.active) alert(what); }, info: function (what) { if (window.console.active) alert(what); } }
window.console.active = false;
function EasyAjax(targetUrl) {
	var me = this;
	if (!targetUrl) {
		alert("ERROR: The object must be constructed using a valid URL ("+targetUrl+")");
        return null;
	}
        this.keepAlive          = false;
	this.targetUrl		= targetUrl;
	this.queryString	= "";
	this.xmlHttp		= null;
	this.showResponse	= false;
	this.async			= true;
	this.isIE			= false;
	this.isMoz			= false;
	this.allowFirebug	= false;
	this.arguments		= [];
	if (navigator.appName.indexOf("Microsoft") >= 0) {
		this.isIE = true;
		var strName = "Msxml2.XMLHTTP";
		if (navigator.appVersion.indexOf("MSIE 5.5") >= 0) {
			strName = "Microsoft.XMLHTTP";
		}
		try {
			this.xmlHttp = new ActiveXObject(strName);
		} catch (e) {
		}
	} else if ((navigator.appName.indexOf("Netscape") >= 0) || (navigator.appName.indexOf("Opera") >= 0)) {
		this.isMoz = true;
		this.xmlHttp = new XMLHttpRequest();
		this.xmlHttp.overrideMimeType("text/plain");
	}
	this.xmlHttp.onreadystatechange = function() {
		EasyAjax.ajaxHandler(me);
	}
	this.callbackFunction = function() { }
    return this;
}
EasyAjax.vars	= [];
EasyAjax.allowFirebug = true;
EasyAjax.firebugRedirect = null;
EasyAjax.alwaysAdd	= function (name,val) {
	EasyAjax.vars[EasyAjax.vars.length] = {
		"name": name,
		"value": val
	}
}
//-------------------------------------------------------------------------
EasyAjax.getCookie = function (cookieName){
  var results = document.cookie.match(cookieName + '=(.*?)(;|$)');

  if (results)
    return (unescape(results[1]));
  else
    return null;
}
//-------------------------------------------------------------------------
EasyAjax.setCookie = function (cookieName,value){
    var date = new Date();
	date.setTime(date.getTime()+(60*24*60*60*1000)); //expire in 60 days
	document.cookie = cookieName += "="+value+"; expires="+date.toGMTString();
}
/* ------------------------------- */
EasyAjax.getElementId	= function (evt){
	var objectID = "";

	if (document.addEventListener)
		objectID = evt.target.id ? evt.target.id : evt.currentTarget.id;
	else
	{
		if (evt.srcElement.id != "")
			objectID = evt.srcElement.id;
		else if (evt.srcElement.parentElement.id != "")
			objectID = evt.srcElement.parentElement.id;
		else if (evt.srcElement.parentElement.parentElement.id != "")
			objectID = evt.srcElement.parentElement.parentElement.id;
	}
	return objectID;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getTargetURL= function() {
	return this.targetUrl;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.callback = function(f) {
	this.callbackFunction = f;
    return this;
}
EasyAjax.prototype.SCF = EasyAjax.prototype.setCallbackFunction; //shorthand
/* ----------------------------------------------------------------- */
EasyAjax.prototype.add = function(paramName, paramValue) {
	if (this.queryString.length > 0) {
		this.queryString += "&";
	}
	this.queryString += encodeURI(paramName) + "=" + encodeURIComponent(paramValue);
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.addRequestParameters     = function (args) { //takes in an array of json style objects
    for (var key in args) {
        if (args.hasOwnProperty(key)) {
            if (typeof(args[key])=="object") {
                for (var j in args[key]) {
                    this.add(j,args[key][j])
                }
            } else {
                this.add(key,args[key]);
            }
        }
    }
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.setQueryString	= function (qs) {
	this.queryString = qs;
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getQueryString = function() {
	return this.queryString;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getAsync = function() {
	return this.async;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.setAsync = function(async) {
	this.async = async;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.get = function() {
	if (!EasyAjax.allowFirebug)
		if (window.console)
			if (window.console.firebug)
				EasyAjax.firebugRedirect();
	for (var i=0; i<EasyAjax.vars.length; i++)
		this.add(EasyAjax.vars[i].name,EasyAjax.vars[i].value);
	var fullGetUrl = this.targetUrl + (this.targetUrl.indexOf("?") >= 0 ? "&" : "?") + this.queryString + "&cachebust=" + new Date().getTime();
	this.xmlHttp.open("GET", fullGetUrl, this.async);
	this.xmlHttp.send(null);
	if (!this.async && (this.callbackFunction != null)) {
		if ((!this.isIE) && (!this.completed))	{
			this.callbackFunction(this.getResponse());
                        if (!this.keepAlive) {
                            delete this; //garbage collection
                        }
		}
	}
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.post = function() {
	if (!EasyAjax.allowFirebug)
		if (window.console)
			if (window.console.firebug)
				EasyAjax.firebugRedirect();
	for (var i=0; i<EasyAjax.vars.length; i++)
		this.add(EasyAjax.vars[i].name,EasyAjax.vars[i].value);
	this.xmlHttp.open("POST", this.targetUrl, this.async);
	this.xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	this.xmlHttp.setRequestHeader("Content-length", this.queryString.length);
	this.xmlHttp.setRequestHeader("Connection", "close");
	this.xmlHttp.send(this.queryString);
	if (!this.async && (this.callbackFunction != null)) {
		if ((!this.isIE) && (!this.completed))	{
			this.callbackFunction(this.getResponse());
                        if (!this.keepAlive) {
                            delete this; //garbage collection
                        }
		}
	}
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getResponse = function() {
	return this.xmlHttp.responseText;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.ignoreResponse = function() {
	this.callbackFunction = null;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.executeJavascript = function (html) {

	var HTMLCode = (html) ? html : this.getResponse();

	while (HTMLCode.toLowerCase().indexOf("<script")!=-1)	{
		var startPos 	= HTMLCode.toLowerCase().indexOf("<script");
		var endPos	 	= HTMLCode.toLowerCase().indexOf("</script");
		var dynamicJS 	= HTMLCode.substr(startPos,endPos-startPos);
		dynamicJS 		= dynamicJS.substr(dynamicJS.indexOf(">")+1);
		HTMLCode 		= HTMLCode.substr(endPos);
		HTMLCode 		= HTMLCode.substr(HTMLCode.indexOf(">")+1);
		try {
			eval(dynamicJS);
		} catch (ex) {
			window.console.warn("An error occurred trying to evaluate code, the error follows");
			window.console.error(ex);
			window.console.error(dynamicJS);
		}
	}
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getValue	= function (field,formName)
{
	var fieldValue = ""; var fieldName = field;
	if (typeof(field)=="string")
		field = $E(field);
	if (field)
	{
		try {
			switch (field.type.toLowerCase())
			{
				case "text"			:	fieldValue = field.value; break;
				case "hidden"		:	fieldValue = field.value; break;
				case "password"		:	fieldValue = field.value; break;
				case "textarea"		:	fieldValue = field.value; break;
				case "checkbox"		:	if (field.checked) { fieldValue = field.value; } break;
				case "radio"		:	var parent = field.parentNode;
										while ((parent.nodeName != "FORM") && (parent.nodeName != "BODY"))
										{
											parent = parent.parentNode;
										}
										if (parent.nodeName != "FORM")
											alert("No form found");
										else
										{
											var cbArray = $E(parent.getAttribute("name"))[field.name]; var cbCtr = 0;
											while ((cbCtr < cbArray.length) && (!fieldValue))
											{
												fieldValue = cbArray[cbCtr].checked ? cbArray[cbCtr].value : "";
												cbCtr++;
											}
										}
										break;
				case "select-one"	:	if (field.getAttribute("combo")=="yes")
										{
											fieldValue = $E(field.id+"_combo").getAttribute("comboValue");
											if (!fieldValue)
												fieldValue = $E(field.id+"_combo").value;
										}
										else
										{
											fieldValue = field[field.selectedIndex].value;
										}
										break;
				default				:	break;
			}
			if (!fieldValue && field.getAttribute("default"))
				fieldValue = field.getAttribute("default");
		} catch (ex) { window.console("Exception occurred in retrieving a value VIA EasyAjax. "+ex+": "+field.id)}
	} else {
		if ((!field) && (formName))
			field = $E(formName)[fieldName];
		if (field)	{
			var cbCtr = 0;
			while ((cbCtr < field.length) && (!fieldValue))	{
				fieldValue = field[cbCtr].checked ? field[cbCtr].value : "";
				cbCtr++;
			}
		}
	}
	return fieldValue;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.packageForm = function(formName) {
	var form = $E(formName);
	if (form) {
		for (var j = 0; j < form.length; j++) {
			var field = form[j];
			if ((!field.id) || (!field.name))
				continue;
			this.add(field.name, this.getValue(field));
		}
    }
    return this
}
/* Private methods ************************************************************************************ */
EasyAjax.ajaxHandler = function(cleanAjaxObj) {
	if ((cleanAjaxObj.xmlHttp.readyState == 4) || (cleanAjaxObj.xmlHttp.readyState == "complete")) {
        if (!cleanAjaxObj.completed) {
            cleanAjaxObj.callbackFunction(cleanAjaxObj.getResponse());
        }
	}
}
EasyAjax.getPassedParms = function() {
	var parm = window.location.href.substr(window.location.href.indexOf("?")+1);
	parms = parm.split("&"); var pa = [];
	for (var i=0; i<parms.length; i++)	{
		var p = parms[i].split('=');
		pa[p[0]] = p[1];
	}
	return pa;
}
EasyAjax.evalJavascript = function (HTML) {
	while (HTML.toLowerCase().indexOf("<script")!=-1)
	{
		var startPos 	= HTML.toLowerCase().indexOf("<script");
		var endPos	 	= HTML.toLowerCase().indexOf("</script");
		var dynamicJS 	= HTML.substr(startPos,endPos-startPos);
		dynamicJS 		= dynamicJS.substr(dynamicJS.indexOf(">")+1);
		HTML            = HTML.substr(endPos);
		HTML            = HTML.substr(HTML.indexOf(">")+1);
		try	{
			eval(dynamicJS);
		} catch (ex) {
			window.console.warn("An error occurred trying to evaluate code, the error follows");
			window.console.error(ex);
			window.console.error(dynamicJS);
		}
	}
}
EasyAjax.uniqueId = function (len) {
    len         = (len) ? len : 6;
    var found   = false;
    var id      = null;
    while (!found) {
        var alpha   = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ';
        id = '';
        for (var i=0; i<len; i++) {
            id += alpha.substr(Math.floor(Math.floor(Math.random()*52)),1);
        }
        found = ($E(id)) ? false : true;
    }
    return id;
}
EasyAjax.execute    = function (url,layer,args,callAfter,get) {
    var ao = new EasyAjax(url);
    for (var key in args) {
        if (args.hasOwnProperty(key)) {
            if (typeof(args[key])=="object") {
                for (var j in args[key]) {
                    ao.add(j,args[key][j])
                }
            } else {
                ao.add(key,args[key]);
            }
        }
    }
    ao.thenfunction () {
        if (typeof(layer) == 'string') {
            $E(layer).innerHTML = ao.getResponse();
        } else if (typeof(layer) == 'function') {
            layer(ao.getResponse());
        } else if (typeof(layer) == 'object') {
            layer.innerHTML = ao.getResponse();
        }
        ao.executeJavascript();
        if (callAfter) {
            callAfter.apply($J);
            callAfter();
        }
    });
    (get) ? ao.get() : ao.post();
}