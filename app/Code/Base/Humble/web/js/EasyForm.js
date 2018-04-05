/**
 * Form wrapper for automatically handling element configurations, both loading and saving
 */
var Form = (function ($) {
    var defaultURL;
    return {
        set: {
            defaultURL: function (arg) {
                defaultURL = arg;
            }
        },
        get: {
            defaultURL: function () {
                return defaultURL;
            }
        },
        insert: function () {
        },
        intercept: function (formRef,id,URL,windowId,callback,preprocess,postprocess) {
            URL = (URL) ? URL : defaultURL;
            //element_id is the mongo ID for the element being configured
            $(formRef).on('submit',{ "form": formRef, "element_id": id, "url": URL, "window_id": windowId, "callback": callback, "preprocess": preprocess, "postprocess": postprocess }, function (event) {
                event.preventDefault();  //cease submission
                if (event.data.preprocess) {
                    event.data.preprocess(this,event);
                }
                var form     = $E(event.data.form.id);
                if (!form) {
                    form = event.data.form[0];
                }
                var ao      = new EasyAjax('/bogus/url');
                var id       = event.data.element_id;
                var URL      = event.data.url;
                var callback = event.data.callback;
                var field    = '';
                var formData = {};
                var name     = ';'
               //console.log(form.elements);
                for (var i in form.elements) {
                    
                    field    = form.elements[i];
                    if ((!field) || (!field.name) || (!field.id) || (typeof(field) === "function")) {
                        continue;
                    }
                    name = (field.name) ? field.name : i;
                    if (typeof(formData[name]) === 'undefined') {
                        formData[name] = '';  //initialize field
                    }
                    formData[name] = ao.getValue(field,formRef.name);
                }
                if (event.data.postprocess) {
                    event.data.postprocess(this,event,formData);
                }
                (new EasyAjax(URL)).add('data',JSON.stringify(formData)).thenfunction (response) {
                    if (callback) {
                        callback(response);
                    } else {
                        var winId = $('#window-id-'+id).val() ? $('#window-id-'+id).val() : windowId;
                        if (winId) {
                           $(Desktop.window.list[winId].content).html(response);
                        } else {
                            //alert("I'm not able to identify what window I am in.  Please make sure you pass the WindowID into form intercept so that I may close the window");
                        }
                    }
                }).post();
            });
        },
        init: function () {
            $.each(document.forms,function (f) {
                Form.intercept(document.forms[f]);
            });
        }
    }
})($);