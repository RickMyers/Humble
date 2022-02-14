/*  --------------------------------------------------------------------
 *  The debug and test console controls are here
 *  --------------------------------------------------------------------*/
Paradigm.console = (function () {
    var console_app = false;
    return {
        app: function () {
            return console_app;
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
                console_app._title('Console').close = (function (app) {
                    return function () {
                        app.lastState = app.state;
                        app.state = 0;
                        app.frame.style.display = "none";
                        return false;
                    }
                })(console_app);
                (new EasyAjax('/paradigm/console/init')).then(function (response) {
                    Paradigm.console.ref        = $E('paradigmConsole');
                    Paradigm.console.ref.style.contentEditable = false;
                    console_app._resize();
                }).get();
            }
        },
        view:       function () {
            console_app._reopen();
        },
        ref:        null,
        messages:   [],
        active:     false,
        heading:    "Humble Paradigm Console\nCopyright 2007-Present\nAll rights reserved\n\n$ Message: Paradigm is Online\n",
        text:       "",
        command:    "",
        commands:   [],
        currentCommand: 0,
        update: function (evt) {
            var key = evt.keyCode || evt.charCode || evt.which;
            switch (key) {
                case 38     :   Paradigm.console.command = '';
                                if ((Paradigm.console.commands.length > 0) && (Paradigm.console.currentCommand < Paradigm.console.commands.length) ) {
                                    Paradigm.console.currentCommand = Paradigm.console.currentCommand-1;
                                    Paradigm.console.command = Paradigm.console.commands[Paradigm.console.currentCommand];
                                }
                                break;
                case 40     :   Paradigm.console.command = '';
                                if ((Paradigm.console.commands.length > 0) && (Paradigm.console.currentCommand < Paradigm.console.commands.length) ) {
                                    Paradigm.console.currentCommand = Paradigm.console.currentCommand+1;
                                    Paradigm.console.command = Paradigm.console.commands[Paradigm.console.currentCommand];
                                }
                                break;
                case 39     :   //right arrow
                                break;
                case 37     :   //left arrow
                                //break;
                case 8      :   Paradigm.console.command = Paradigm.console.command.substr(0,Paradigm.console.command.length-1);
                                break;
                case 16     :   //shift
                                break;
                case 17     :   //ctrl
                                break;
                case 18     :   //alt
                                break;
                case 13     :   Paradigm.console.process(Paradigm.console.command);
                                break;
                default     :   Paradigm.console.command += String.fromCharCode(key);
                                break;
            }
            $(Paradigm.console.ref).html(Paradigm.console.text+"\n" + Paradigm.console.cursor.text[0] +"\n" + Paradigm.console.command +Paradigm.console.cursor.image);
            evt.preventDefault();
            evt.stopPropagation();
            return false;
        },
        process: function (command) {
            var text    = command.substr(command.indexOf(' ')+1);
            if (command.indexOf(' ') != -1) {
                command = command.substr(0,command.indexOf(' '));
            }
            Paradigm.console.commands.push(command);
            switch (command.toLowerCase()) {
                case "time"         :   (new EasyAjax('/paradigm/console/time')).then(function (response) {
                                            Paradigm.console.add(response,'',1);
                                        }).post();
                                        break;
                case "status"       :   (new EasyAjax('/paradigm/console/status')).then(function (response) {
                                            Paradigm.console.add(response,'',1);
                                        }).post();
                                        break;
                case "cls"          :
                case "clear"        :   Paradigm.console.text = '';
                                        Paradigm.console.reply('Buffer Cleared','',1);
                                        break;
                case "inspect"      :
                                        break;
                case "whoami"       :   (new EasyAjax('/paradigm/console/whoami')).then(function (response) {
                                            Paradigm.console.reply(response,'',1);
                                        }).post();
                                        break;
                case "search"       :   Paradigm.console.add(command + ' '+text+'\n','',1);
                                        (new EasyAjax('/paradigm/console/search')).add('term',text).then(function (response) {
                                            Paradigm.console.reply(response,'',1);
                                        }).post();
                                        break;
                case "init"         :   Paradigm.console.service.init();
                                        Paradigm.console.add('Data cleared','',1);
                                        break;
                case "save"         :   Paradigm.actions.save();
                                        break;
                case "activate"     :   (new EasyAjax('/paradigm/workflow/activate')).add('workflow',Paradigm.actions.get.mongoWorkflowId()).then(function (response) {
                                            Paradigm.console.add(response,'',1);
                                        }).post();
                                        break;
                case "id"           :   if (Paradigm.actions.get.mongoWorkflowId()) {
                                            Paradigm.console.add('Workflow Id: '+Paradigm.actions.get.mongoWorkflowId(),'',1)
                                        } else {
                                            Paradigm.console.add('Unavailable, please generate the workflow to set the Id','',1)
                                        }
                                        break;
                case "deactivate"   :
                case "inactivate"   :   (new EasyAjax('/paradigm/workflow/inactivate')).add('workflow',Paradigm.actions.get.mongoWorkflowId()).then(function (response) {
                                            Paradigm.console.add(response,'',1);
                                        }).post();
                                        break;
                case "load"         :   Paradigm.actions.list();
                                        break;
                case "target"       :   Paradigm.console.service.url = text;
                                        Paradigm.console.add('target=['+text+']','',1);
                                        break;
                case "format"       :   Paradigm.console.service.format = text;
                                        Paradigm.console.add('format=['+text+']','',1);
                                        break;
                case "method"       :   Paradigm.console.service.method = text.toLowerCase();
                                        Paradigm.console.add('method=['+text.toLowerCase()+']','',1);
                                        break;
                case "arg"          :   var sep = text.indexOf('=');
                                        Paradigm.console.service.arguments[text.substr(0,sep)]=text.substr(sep+1);
                                        Paradigm.console.reply('v=['+text.substr(0,sep)+'='+text.substr(sep+1)+']','',1);
                                        break;
                case "show"         :   switch (text.toLowerCase()) {
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
                case "run"          :   if (Paradigm.console.service.format == 'json') {
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
                case "list"         :   switch (text.toLowerCase()) {
                                            case "objects"      :
                                                                    break;
                                            case "workflows"    :
                                                                    break;
                                            case "mine"         :   //all workflows I am the owner of
                                                                    break;
                                        }
                                        break;
                case "sync"         :   //updates the webservice_workflows table with the current workflow_id
                                        break;
                case "info"         :   //lists information about the current workflow
                                        break;
                default             :   Paradigm.console.add('Unknown command: <i>'+command+'</i>','',1);
                                        break;
            }
            Paradigm.console.command = '';
        },
        capture: function () {
            Desktop.off(Paradigm.console.app().content,'keydown',Paradigm.remove);
            Desktop.on(Paradigm.console.app().content,'keypress',Paradigm.console.update);
        },
        release: function () {
            Desktop.off(Paradigm.console.app().content,'keypress',Paradigm.console.update);
            Desktop.on(Paradigm.console.app().content,'keydown',Paradigm.remove);
        },
        cursor: {
            text: ["READY",">Working...",">Loading...",">Saving...",">Printing...",">Generating...",">Initializing..."],
            image: "<img src='/images/paradigm/clipart/blinking_cursor.gif' />"
        },
        clear:  function () {
            $(Paradigm.console.ref).html(Paradigm.console.heading+Paradigm.console.cursor.text[0]+Paradigm.console.cursor.image);
            Paradigm.console.text = '';
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
                Paradigm.console.messages.push(m);
            } else {
                Paradigm.console.active = true;
                Paradigm.console.type(message,8,cursor);
            }
            return token;
        },
        reply: function (message,token,cursor) {
            cursor = (cursor) ? cursor : 0;
            message = ">"+message.replace('&text&',token)+"\n";
            if (Paradigm.console.active) {
                var m = {
                    message: message,
                    speed: 8,
                    cursor: cursor
                }
                Paradigm.console.messages.push(m);
            } else {
                Paradigm.console.active = true;
                Paradigm.console.type(message,8,cursor);
            }
            return token;
        },
        error: function (message,token,cursor) {
            cursor = (cursor) ? cursor : 0;
            message = "$ <span style='color: red'>"+message.replace('&text&',token)+"</span>\n";
            if (Paradigm.console.active) {
                var m = {
                    message: message,
                    speed: 8,
                    cursor: cursor
                }
                Paradigm.console.messages.push(m);
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
                Paradigm.console.messages.push(m);
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
            var cursorT = Paradigm.console.cursor.text[+cursor];
            message     = message.substr(1);
            var delay   = (char=="\n") ? (speed*10) : speed;
            Paradigm.console.text = Paradigm.console.text+char;
            $(Paradigm.console.ref).html(Paradigm.console.text+Paradigm.console.cursor.image+'\n'+cursorT);
            if (message) {
                var fun = function () {
                    Paradigm.console.type(message,speed,cursor);
                };
                window.setTimeout(fun,delay);
            } else {
                var m = Paradigm.console.messages.pop();
                if (m) {
                    Paradigm.console.type(m.message,m.speed,m.cursor);
                } else {
                    Paradigm.console.active = false;
                    $(Paradigm.console.ref).html(Paradigm.console.text+"\n" + Paradigm.console.cursor.text[0] +"\n" + Paradigm.console.cursor.image);
                    $('#paradigmConsole').focus();
                }
            }
        },
        init: function () {
            Paradigm.console.clear();
        }
    }
})();