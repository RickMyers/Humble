function $import(jsScriptPath)  { var script = document.createElement("script"); script.type = "text/javascript"; script.src = jsScriptPath; document.getElementsByTagName("head")[0].appendChild(script); }
function $E(objID)              { if (typeof(objID)=="string") return document.getElementById(objID);	else return objID; }
/* Main AJAX Class  ************************************************************************************ */
function EasyAjax(targetUrl) {
    var me = this;
    if (!targetUrl) {
        alert("ERROR: The object must be constructed using a valid URL ("+targetUrl+")");
        return null;
    }
    this.suppressAlerts = false;
    this.keepAlive      = false;
    this.targetUrl	= targetUrl;
    this._contentType   = "application/x-www-form-urlencoded";
    this.queryString	= "";
    this.xmlHttp	= null;
    this.showResponse	= false;
    this.formData       = false;
    this.async		= true;
    this.isIE		= false;
    this.isMoz		= false;
    this.vars           = { };
    this.queryString    = '';
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
    } else {
        this.isMoz = true;
        this.xmlHttp = new XMLHttpRequest();
        this.xmlHttp.overrideMimeType("text/plain");
    }
    this.xmlHttp.onreadystatechange = function() {
        EasyAjax.ajaxHandler(me);
    };
    this.callbackFunction = [];
    this.getXhr = function () {
        return this.xmlHttp;
    };
    this.getPagination = function () {
        return JSON.parse(this.xmlHttp.getResponseHeader('pagination'));
    }
    var vars = EasyAjax.always.get()
    for (var name in vars) {
        this.add(name,vars[name]);
    }
    this.contentType =  (contentType) => {
        if (contentType) {
            this._contentType = contentType;
            return this;
        }
        return this._contentType;
    }
    return this;
}
//-------------------------------------------------------------------------
EasyAjax.always = (function () {
    var vars = [];
    return {
        add: function (name,val) {
            vars[name] =  val;
        },
        get: function () {
            return vars;
        },
        show: function () { console.log(vars); }
    }
})();    
/* ------------------------------- */
/*  Hack for old versions of IE    */
/* ------------------------------- */
EasyAjax.getElementId	= function (evt) {
    var objectID = "";
    if (document.addEventListener) {
	objectID = evt.target.id ? evt.target.id : evt.currentTarget.id;
    } else {
        if (evt.srcElement.id != "") {
            objectID = evt.srcElement.id;
        } else if (evt.srcElement.parentElement.id != "") {
            objectID = evt.srcElement.parentElement.id;
        } else if (evt.srcElement.parentElement.parentElement.id != "") {
            objectID = evt.srcElement.parentElement.parentElement.id;
        }
    }
    return objectID;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getTargetURL= function() {
    return this.targetUrl;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.then = function(f) {
    this.callbackFunction[this.callbackFunction.length] = f;
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.add = function(paramName, paramValue) {
    this.vars[paramName] = paramValue;
	if (this.queryString && (this.queryString.length > 0)) {
            this.queryString += "&";
	}
	this.queryString += encodeURI(paramName) + "=" + encodeURIComponent(paramValue);
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.addModel     = function (model) {
    for (var i in model) {
        if ((i.substr(0,1)!=='$') && (i.substr(0,1)!=='_')) {
            if ((typeof(model[i]) !== 'object') && (typeof(model[i]) !== 'function')) {
                this.add(i,model[i]);
            }
        }
    }
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.addFiles     = function(key,fileField) {
    if (fileField && fileField.files) {
        if (!this.formData) {
            this.formData = new FormData();
        }
        for (var i=0; i<fileField.files.length; i++) {
            this.formData.append(key,fileField.files[i],fileField.files[i].name);
        }
    }
    return this;
};
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
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.addArray             = function (array) {
    for (var i in array) {
        this.add(i,array[i]);
    }
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.setQueryString	= function (qs) {
    this.queryString = qs;
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getQueryString = function() {
    return this.queryString;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getAsync = function() {
    return this.async;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.setAsync = function(async) {
    this.async = async;
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.get = function(async) {
    if (this.targetUrl) {
        async = (async === false) ? false : true;
        var fullGetUrl = this.targetUrl + (this.targetUrl.indexOf("?") >= 0 ? "&" : "?") + this.queryString + "&cachebust=" + new Date().getTime();
        this.xmlHttp.open("GET", fullGetUrl, async);
        this.xmlHttp.setRequestHeader('HTTP_X_REQUESTED_WITH','xmlhttprequest')
        this.xmlHttp.send(null);
        if (!this.async && (this.callbackFunction.length)) {
            if ((!this.isIE) && (!this.completed))	{
                for (var i=0; i<this.callbackFunction.length; i++)
                this.callbackFunction[i](this.getResponse());
            }
            if (!this.keepAlive) {
                delete this; //garbage collection
            }
        }
    }
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.post = function(async) {
    if (this.targetUrl) {
        async = (async === false) ? false : true;
        this.xmlHttp.open("POST", this.targetUrl, async);
        this.xmlHttp.setRequestHeader('HTTP_X_REQUESTED_WITH','xmlhttprequest');
        this.xmlHttp.setRequestHeader("Content-type", this.contentType());
        if (this.formData) {
            for (var i in this.vars) {
                this.formData.append(i,this.vars[i]);
            }
            this.xmlHttp.send(this.formData);
        } else {
            this.xmlHttp.send(this.queryString);
        }
    }
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.put = function(async) {
    async = (async === false) ? false : true;
    this.xmlHttp.open("PUT", this.targetUrl, async);
    this.xmlHttp.setRequestHeader('HTTP_X_REQUESTED_WITH','xmlhttprequest');
    this.xmlHttp.setRequestHeader("Content-type", this.contentType());
    if (this.formData) {
        for (var i in this.vars) {
            this.formData.append(i,this.vars[i]);
        }
        this.xmlHttp.send(this.formData);
    } else {
        this.xmlHttp.send(JSON.stringify(this.vars));
    }
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.delete = function(async) {
    async = (async === false) ? false : true;
    this.xmlHttp.open("DELETE", this.targetUrl, async);
    this.xmlHttp.setRequestHeader('HTTP_X_REQUESTED_WITH','xmlhttprequest');
    this.xmlHttp.setRequestHeader("Content-type", this.contentType());
    if (this.formData) {
        for (var i in this.vars ) {
            this.formData.append(i,this.vars[i]);
        }
        this.xmlHttp.send(this.formData);
    } else {
        this.xmlHttp.send(this.queryString);
    }
    return this;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.suppress = function (boolean) {
    this.suppressAlerts = (boolean === true) ? true : false;
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.notifications = function (type,notifications) {
    if (notifications && notifications.length) {
        for (var i = 0; i<notifications.length; i++) {
            switch (type) {
                case "warnings" :
                    window.console.warn(notifications[i]);
                    break;
                case "errors" :
                    window.console.error(notifications[i]);
                    break;
                default : 
                    window.console.log(notifications[i]);
                    break;
            }            
        }
    }
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getResponse = function() {
    if (!this.suppressAlerts) {
        var alerts = JSON.parse(this.xmlHttp.getResponseHeader('Alerts'));
        if (alerts) {
            for (var i=0; i<alerts.length; i++) {
                alert(alerts[i].replace(/<br>/g,'\n'));
            }
        }
        this.notifications('messages',JSON.parse(this.xmlHttp.getResponseHeader('Messages')));
        this.notifications('warnings',JSON.parse(this.xmlHttp.getResponseHeader('Warnings')));
        this.notifications('errors',JSON.parse(this.xmlHttp.getResponseHeader('Errors')));
    }
    return this.xmlHttp.responseText;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.ignoreResponse = function() {
    this.callbackFunction = [];
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.executeJavascript = function (html) {
    var HTMLCode = (html) ? html : this.getResponse();
    while (HTMLCode.toLowerCase().indexOf("<script") !== -1)	{
        var startPos 	= HTMLCode.toLowerCase().indexOf("<script");
        var endPos	= HTMLCode.toLowerCase().indexOf("</script");
        var dynamicJS 	= HTMLCode.substr(startPos,endPos-startPos);
        dynamicJS 	= dynamicJS.substr(dynamicJS.indexOf(">")+1);
        HTMLCode 	= HTMLCode.substr(endPos);
        HTMLCode 	= HTMLCode.substr(HTMLCode.indexOf(">")+1);
        try {
            eval(dynamicJS);
        } catch (ex) {
            window.console.warn("An error occurred trying to evaluate code, the error follows");
            window.console.error(ex);
            window.console.error(dynamicJS);
        }
    }
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.getValue	= function (field,formName) {
    var fieldValue = ''; var fieldName = field;
    if (typeof(field) === "string") {
        field = $E(field);
    }
    if (field)	{
        try {
            if (field.type) {
                switch (field.type.toLowerCase()) {
                    case "hidden" :
                    case "password" :
                    case "textarea" :
                    case "text"	:
                    	fieldValue = field.value;
                        break;
                    case "checkbox" :
                        if (field.checked) {
                            fieldValue = field.value;
                        }
                        break;
                    case "radio" :
                    	var parent = field.parentNode;
                        while ((parent.nodeName !== "FORM") && (parent.nodeName !== "BODY")) {
                            parent = parent.parentNode;
                        }
                        if (parent.nodeName !== "FORM") {
                            alert("No form found");
                        } else {
                            var rbCtr   = 0;
                            var rbArray = $E(parent.getAttribute("id")).elements[field.name];
                            if (rbArray.length) {
                                while ((rbCtr < rbArray.length) && (!fieldValue)) {
                                    fieldValue = rbArray[rbCtr].checked ? rbArray[rbCtr].value : "";
                                    rbCtr++;
                                }
                            } else {
                                if (field.checked) {
                                    fieldValue = field.value
                                }
                            }
                        }
                        break;
                    case "select-one" :
                        if (field.getAttribute("combo") === "yes")	{
                            fieldValue = $E(field.id+"_combo").getAttribute("comboValue");
                            //if (!fieldValue) {
                            //	fieldValue = $E(field.id+"_combo").value;
                            // }
                        } else {
                            fieldValue = field[field.selectedIndex].value;
                        }
                        break;
                    case "file" :
                        break;
                    default :
                        break;
                }
            } else {
                if (field.length) {
                    for (var i=0; i<field.length; i++) {
                        if (field[i].checked) {
                            fieldValue = field[i].value;
                        }
                    }
                }
            }
            if (!fieldValue && field.getAttribute && field.getAttribute("default")) {
                fieldValue = field.getAttribute("default");
            }
	} catch (ex) { console.log("Exception occurred in retrieving a value VIA EasyAjax. "+ex+": "+field.id); };
    } else {
        if ((!field) && (formName)) {
            field = $E(formName)[fieldName];
        }
        if (field)	{
            var cbCtr = 0;
            while ((cbCtr < field.length) && (!fieldValue))	{
                fieldValue = field[cbCtr].checked ? field[cbCtr].value : "";
                cbCtr++;
            }
        }
    }
    return fieldValue;
};
/* ----------------------------------------------------------------- */
EasyAjax.prototype.packageEdits = function (edits) {
    EasyEdits.packageEdits(this,edits);
    return this;
}
/* ----------------------------------------------------------------- */
EasyAjax.prototype.packageForm = function(formName,edits) {
    var form = $E(formName); var name = '';
    if (form) {
        for (var j=0; j < form.length; j++) {
            var field = form[j];
            if ((!field.id) || (!field.name)) {
                continue;
            }
            name = (field.name) ? field.name : field.id;
            if (field.type == 'file') {
                this.addFiles(name,$E(field.id));
            } else {
                var val = this.getValue(field);
                if (val !== null) {
                    this.add(name, val);
                }
            }
        }
    }
    return this;
};
EasyAjax.prototype.addForm = EasyAjax.prototype.packageForm;            //renaming
/* Private methods ************************************************************************************ */
EasyAjax.ajaxHandler = function(cleanAjaxObj) {
    if ((cleanAjaxObj.xmlHttp.readyState === 4) || (cleanAjaxObj.xmlHttp.readyState === "complete")) {
        if (!cleanAjaxObj.completed) {
            for (var i=0; i<cleanAjaxObj.callbackFunction.length; i++) {
                cleanAjaxObj.callbackFunction[i](cleanAjaxObj.getResponse());
            }
        }
    }
};
EasyAjax.getCookie = function (cookieName){
    var results = document.cookie.match(cookieName + '=(.*?)(;|$)');
    return (results) ? (unescape(results[1])) : null;
};
EasyAjax.setCookie = function (cookieName,value){
    var date = new Date();
    date.setTime(date.getTime()+(60*24*60*60*1000)); //expire in 60 days
    document.cookie = cookieName += "="+value+"; expires="+date.toGMTString();
};
EasyAjax.getPassedParms = function() {
    var parms   = window.location.href.substr(window.location.href.indexOf("?")+1).split("&");
    var pa      = [];
    var p       = null;
    for (var i=0; i<parms.length; i++)	{
        p = parms[i].split('=');
        pa[p[0]] = p[1];
    }
    return pa;
};
EasyAjax.evalJavascript = function (HTML) {
    while (HTML.toLowerCase().indexOf("<script") !== -1)	{
        var startPos 	= HTML.toLowerCase().indexOf("<script");
        var endPos	= HTML.toLowerCase().indexOf("</script");
        var dynamicJS 	= HTML.substr(startPos,endPos-startPos);
        dynamicJS 	= dynamicJS.substr(dynamicJS.indexOf(">")+1);
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
};
EasyAjax.uniqueId = function (len) {
    len         = (len) ? len : 6;
    var found   = false;
    var id      = '';
    while (!found) {
        var alpha   = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ';
        for (var i=0; i<len; i++) {
            id += alpha.substr(Math.floor(Math.floor(Math.random()*52)),1);
        }
        found = ($E(id)) ? false : true;
    }
    return id;
};