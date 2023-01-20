/*  --------------------------------------------------------------------
 *  The debug and test console controls are here
 *  --------------------------------------------------------------------*/
Paradigm.console = (function () {
    let console_app     = false;
    let commands        = [];
    let messages        = [];
    let console_command = '';
    let command_index   = 0;
    let console_ref     = false;
    let console_heading = "Humble Paradigm Console\nCopyright 2007-Present\nAll rights reserved\n\n$ Message: Paradigm is Online\n";   
    let console_output  = false;
    let console_prompt  = false;
    let console_input   = false;
    let console_text    = '';
    let console_cmd     = false;
    let console_element = false;
    let console_test    = false;
    function updateResponse(response) {
        return ((console_command) ? console_command+'\n' : '')+response;
    }
    return {
        app: function () {
            return console_app;
        },
        ref: function () {
            return console_ref;
        },
        heading: function (){
            return console_heading;
        },
        resize: function () {
            console_app._resize();
        },
        select: function (candidate) {
            if (candidate) {
                console_element = Paradigm.elements.list[candidate];
                Paradigm.console.reply('Selected '+console_element.label+':'+console_element.id+'['+console_element.text+']','',1);
            }
            return candidate;
        },
        init: function (app) {
            Paradigm.console.clear();
            Paradigm.console.output(console_heading,12,6);
            console_output = $E('paradigm_console');
            console_prompt = $E('paradigm_console_prompt');
            console_input  = $E('paradigm_console_input');
            console_cmd    = $E('paradigm_console_cmd');
            app._scroll(false);
            app.resize = function () {
                if (app.content.offsetHeight) {
                    $(console_output).height(app.content.offsetHeight - console_prompt.offsetHeight - console_input.offsetHeight-2);
                    $(console_output).width(app.content.offsetWidth);
                    $(console_input).width(app.content.offsetWidth - console_cmd.offsetWidth-10);
                }
            }
        },
        service: {
            arguments: [],
            url:    '',
            format: '',
            method: 'get',
            init:       function () {
                Paradigm.console.service.arguments = [];
                Paradigm.console.service.format;
                Paradigm.console.service.url = '';
                Paradigm.console.service.method = 'get';
            }
        },
        initialize:       function () {
            if (!console_app) {
                console_app = Desktop.semaphore.checkout(true);
                console_app._title('Console')._scroll(false).close = (function (app) {
                    return function () {
                        app.lastState           = app.state;
                        app.state               = 0;
                        app.frame.style.display = "none";
                        return false;
                    }
                })(console_app);
                (new EasyAjax('/paradigm/console/init')).add('window_id',console_app.id).then(function (response) {
                    console_app.set(response);
                    console_ref = $E('paradigm_console');
                    console_ref.style.contentEditable = false;
                    console_app._resize();
                    console_input.focus();
                }).get();
            }
        },
        view:       function () {
            console_app._reopen();
        },
        active:     false,
        text:       "",
        command:    "",
        update: function (evt) {
            var key = evt.keyCode || evt.charCode || evt.which;

            switch (key) {
                case 40     :  
                    if ((commands.length > 0) && (command_index < commands.length) && (command_index > 0) ) {
                        command_index = command_index-1;
                        $(console_input).val(commands[command_index]);
                    }
                    break;
                case 38     : 
                    if ((commands.length > 0) && (command_index < commands.length) ) {
                        command_index = command_index+1;
                        $(console_input).val(commands[command_index]);
                    } else {
                        command_index = 0;
                    }
                    break;
                case 39     :   
                    //right arrow
                    break;
                case 37     :
                case 8      :   
                    //console_command = console_command.substr(0,console_command.length-1);
                    break;
                case 16     :   //shift
                    break;
                case 17     :   //ctrl
                    break;
                case 18     :   //alt
                    break;
                case 13     :   
                    console_command = commands[commands.length] = $(console_input).val();
                    Paradigm.console.process($(console_input).val());
                    console_input.value = '';
                    console_input.focus();                    
                    break;
                default     :   
                    console_command += String.fromCharCode(key);
                    break;
            }
            $(console_prompt).html(Paradigm.console.cursor.text[0]);
            evt.stopPropagation();
            return true;
        },
        process: function (command) {
            var text    = command.substr(command.indexOf(' ')+1);
            if (command.indexOf(' ') != -1) {
                command = command.substr(0,command.indexOf(' '));
            }
            switch (command.toLowerCase()) {
                case "new"  :
                    switch (text.toLowerCase()) {
                        case 'test' :
                            if (console_element) {
                                (new EasyAjax('/paradigm/test/init')).add('component_id',console_element.id).then(function (response) {
                                    console_test = JSON.parse(response);
                                    console.log(console_test);
                                }).post();
                            } else {
                                Paradigm.console.add(updateResponse("No element selected"),'',1)
                            }
                            break;
                        default :
                            Paradigm.console.add(updateResponse("I don't know how to do that"),'',1);
                            break;
                    } 
                    break;
                case "time" :
                    (new EasyAjax('/paradigm/console/time')).then(function (response) {
                        Paradigm.console.add(updateResponse(response),'',1);
                    }).post();
                    break;
                case "status" :   
                    (new EasyAjax('/paradigm/console/status')).then(function (response) {
                        Paradigm.console.add(updateResponse(response),'',1);
                    }).post();
                    break;
                case "cls"          :
                case "clear"        :   
                    console_text = '';
                    Paradigm.console.reply('Buffer Cleared','',1);
                    break;
                case "inspect"      :
                    break;
                case "whoami"       :
                    (new EasyAjax('/paradigm/console/whoami')).then(function (response) {
                        Paradigm.console.reply(updateResponse(response),'',1);
                    }).post();
                    break;
                case "search"       :   
                    Paradigm.console.add(command + ' '+text+'\n','',1);
                    (new EasyAjax('/paradigm/console/search')).add('term',text).then(function (response) {
                        Paradigm.console.reply(response,'',1);
                    }).post();
                    break;
                case "init"         :
                    Paradigm.console.service.init();
                    Paradigm.console.add('Data cleared','',1);
                    break;
                case "save"         :   
                    Paradigm.actions.save();
                    break;
                case "activate"     :  
                    (new EasyAjax('/paradigm/workflow/activate')).add('workflow',Paradigm.actions.get.mongoWorkflowId()).then(function (response) {
                        Paradigm.console.add(updateResponse(response),'',1);
                    }).post();
                    break;
                case "id"           :   
                    if (Paradigm.actions.get.mongoWorkflowId()) {
                        Paradigm.console.add('Workflow Id: '+Paradigm.actions.get.mongoWorkflowId(),'',1)
                    } else {
                        Paradigm.console.add('Unavailable, please generate the workflow to set the Id','',1)
                    }
                    break;
                case "deactivate"   :
                case "inactivate"   :   
                    (new EasyAjax('/paradigm/workflow/inactivate')).add('workflow',Paradigm.actions.get.mongoWorkflowId()).then(function (response) {
                        Paradigm.console.add(updateResponse(response),'',1);
                    }).post();
                    break;
                case "load"         :   
                    Paradigm.actions.list();
                    break;
                case "target"       :  
                    Paradigm.console.service.url = text;
                    Paradigm.console.add('target=['+text+']','',1);
                    break;
                case "format"       :
                    Paradigm.console.service.format = text;
                    Paradigm.console.add('format=['+text+']','',1);
                    break;
                case "method"       :   
                    Paradigm.console.service.method = text.toLowerCase();
                    Paradigm.console.add('method=['+text.toLowerCase()+']','',1);
                    break;
                case "arg"          :   
                    var sep = text.indexOf('=');
                    Paradigm.console.service.arguments[text.substr(0,sep)]=text.substr(sep+1);
                    Paradigm.console.reply('v=['+text.substr(0,sep)+'='+text.substr(sep+1)+']','',1);
                    break;
                case "show"         :  
                    switch (text.toLowerCase()) {
                        case "target"   :
                            Paradigm.console.reply(Paradigm.console.service.url,'',1);
                            break;
                        case "args" :
                            for (var i in Paradigm.console.service.arguments) {
                                Paradigm.console.reply(i+'='+Paradigm.console.service.arguments[i],'',1);
                            }
                            break;
                        case "format" :
                            Paradigm.console.add(Paradigm.console.service.format,'',1);
                            break;
                    }
                    break;
                case "run"          :   
                    if (Paradigm.console.service.format == 'json') {
                        var ao = new EasyAjax(Paradigm.console.service.url);
                        ao.setQueryString(JSON.stringify(Paradigm.console.service.arguments));
                        ao.then(function (response) {
                            var winId = Desktop.semaphore.checkout();
                            var win   = Desktop.window.list[winId];
                            win.content.style.overflow = 'auto';
                            win._title('OUTPUT | Paradigm');
                            win.set('<textarea width="100%" height="100%">'+response+'</textarea>');
                            win._open();
                        });
                        switch (Paradigm.console.service.method) {
                            case 'get'  :   ao.get();
                                            break;
                            case 'post' :   ao.post();
                                            break;
                            case 'put'  :   ao.put();
                                            break;
                            default     :   Paradigm.console.reply('Unknown method: ['+Paradigm.console.service.method+']','',0);
                                            break;
                        }
                    } else {
                        var ao = new EasyAjax(Paradigm.console.service.url);
                        ao.addRequestParameters(Paradigm.console.service.arguments)
                        ao.then(function (response) {
                            var winId = Desktop.semaphore.checkout();
                            var win   = Desktop.window.list[winId];
                            win._title('OUTPUT | Paradigm');
                            win.content.style.overflow = 'auto';
                            win.set('<textarea width="100%" height="100%">'+response+'</textarea>');
                            win._open();
                        });
                        switch (Paradigm.console.service.method) {
                            case 'get'  :   ao.get();
                                            break;
                            case 'post' :   ao.post();
                                            break;
                            case 'put'  :   ao.put();
                                            break;
                            default     :   Paradigm.console.reply('Unknown method: ['+Paradigm.console.service.method+']','',0);
                                            break;
                        }
                    }
                    break;
                case "generate"     :
                    break;
                case "validate"     :
                    break;
                case "list"         :   
                    switch (text.toLowerCase()) {
                        case "objects"      :
                                                break;
                        case "workflows"    :
                                                break;
                        case "mine"         :   //all workflows I am the owner of
                                                break;
                    }
                    break;
                case "history" :
                    for (var i=0; i<commands.length; i++) {
                        Paradigm.console.reply((''+i).padStart(3,'0')+' '+commands[i]);
                    }
                    break;
                case "sync"         :   //updates the webservice_workflows table with the current workflow_id
                    break;
                case "info"         :   //lists information about the current workflow
                    break;
                default             :   
                    Paradigm.console.add('Unknown command: <span style="color: red; font-style: italic">'+command+'</span>','',1);
                    break;
            }
            console_ref.scrollTo(0,console_ref.scrollHeight);
        },
        capture: function () {
            Desktop.off(console_input,'keydown',Paradigm.remove);
            Desktop.on(console_input,'keydown',Paradigm.console.update);
            return true;
        },
        release: function () {
            Desktop.off(console_input,'keydown',Paradigm.console.update);
            Desktop.on(console_input,'keydown',Paradigm.remove);
            return true;
        },
        cursor: {
            text: ["READY",">Working...",">Loading...",">Saving...",">Printing...",">Generating...",">Initializing..."],
            image: "<img src='/images/paradigm/clipart/blinking_cursor.gif' />"
        },
        clear:  function () {
            console_command = '';            
            $(console_prompt).html(Paradigm.console.cursor.text[0]);
            $(console_ref).html(console_heading+'\n'+Paradigm.console.cursor.image);
            console_text = '';
        },
        add: function (message,token,cursor) {
            cursor = (cursor) ? cursor : 0;
            message = "$ "+message.replace('&text&',token)+"\n";
            if (Paradigm.console.active) {
                var m = {
                    message: message,
                    speed: 8,
                    cursor: cursor
                }
                messages.push(m);
            } else {
                Paradigm.console.active = true;
                Paradigm.console.type(message,8,cursor);
            }
            return token;
        },
        reply: function (message,token,cursor) {
            cursor = (cursor) ? cursor : 0;
            message = "> "+message.replace('&text&',token)+"\n";
            if (Paradigm.console.active) {
                var m = {
                    message: message,
                    speed: 8,
                    cursor: cursor
                }
                messages.push(m);
            } else {
                Paradigm.console.active = true;
                Paradigm.console.type(message,8,cursor);
            }
            return token;
        },
        error: function (message,token,cursor) {
            cursor = (cursor) ? cursor : 0;
            message = "> <span style='color: red'>"+message.replace('&text&',token)+"</span>\n";
            if (Paradigm.console.active) {
                var m = {
                    message: message,
                    speed: 8,
                    cursor: cursor
                }
                messages.push(m);
            } else {
                Paradigm.console.active = true;
                Paradigm.console.type(message,8,cursor);
            }
            return token
        },
        output: function (message,speed,cursor) {
            cursor = (cursor) ? cursor : 0;
            if (Paradigm.console.active) {
                var m = {
                    message: message,
                    speed: speed,
                    cursor: cursor
                }
                messages.push(m);
            } else {
                Paradigm.console.active = true;
                Paradigm.console.type(message,speed,cursor);
            }
            return;
        },
        type: function (message,speed,cursor) {
            cursor      = (cursor) ? cursor : 0;
            speed       = (speed) ? speed: 20;
            var char    = message.substr(0,1);
            message     = message.substr(1);
            if (char=='<') {
                let x = message.indexOf('>');
                if (x != -1) {
                    char += message.substr(0,x);
                    message = message.substr(x);
                }
            }
            var delay   = (char=="\n") ? (speed*10) : speed;
            console_text = console_text+char;
            $(console_prompt).html(Paradigm.console.cursor.text[cursor]);
            $(console_ref).html(console_text+Paradigm.console.cursor.image+'\n');
            if (message) {
                var fun = function () {
                    Paradigm.console.type(message,speed,cursor);
                };
                window.setTimeout(fun,delay);
            } else {
                var m = messages.pop();
                if (m) {
                    Paradigm.console.type(m.message,m.speed,m.cursor);
                } else {
                    Paradigm.console.active = false;
                    $(console_prompt).html(Paradigm.console.cursor.text[0]);
                    $(console_ref).html(console_text+"\n"+ Paradigm.console.cursor.image);
                    $('#paradigm_console_input').focus();
                }
            }
            if (console_ref) {
                console_ref.scrollTo(0,console_ref.scrollHeight);
            }
        }
    }
})();