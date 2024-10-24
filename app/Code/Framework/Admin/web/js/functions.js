var Functions = (() => {
                var servicesWindow = false;
                function translate (char) {
                    let diff;
                    if (/[A-Z]/.test (char)) {
                        diff = "𝗔".codePointAt (0) - "A".codePointAt (0);
                    } else {
                        diff = "𝗮".codePointAt (0) - "a".codePointAt (0);
                    }
                    return String.fromCodePoint (char.codePointAt (0) + diff);
                }                    
                function makeItBold(text) {
                    return text.replace(/[A-Za-z0-9]/g, translate)
                }
                return {
                    menu: {
                        management: {
                            open: () => {
                                let win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/admin/menu/form')).add('window_id',win.id).then((response) => {
                                    win._static(true)._title('Menu Management')._open(response);
                                    win._scroll(true).resize((win)=>{
                                        $('#menu_management_area').height(win.content.height());
                                    });
                                }).post();                                
                            }
                        }
                    },
                    code: {
                        explore: (namespace, type, resource) => {
                            let win = Desktop.semaphore.checkout(true);
                            win._title('Code Explorer')._scroll(true)._open();
                            (new EasyAjax('/admin/code/explorer')).add('window_id',win.id).add('namespace',namespace).add('type',type).add('resource',resource).then((response) => {
                                win.set(response);
                                Colorizer.scan(win.content);
                            }).post();
                        }
                    },
                    users: {
                        starts_with: '',
                        display: (starts_with) => {
                            Administration.users.starts_with = (starts_with) ? starts_with : '';
                            var win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/user/display')).add('role_id',$('#user_role').val()).add('page',1).add('rows',15).add('starts_with',Administration.users.starts_with).then((response) => {
                                $('#admin-users-display-area').html(response);
                                Pagination.set('argus-users',this.getPagination());
                            }).post()
                        },
                        home: function () {
                            Argus.status('Loading User Display...');
                            (new EasyAjax('/admin/user/home')).then(function (response) {
                                $('#sub-container').html(response);
                                Argus.status('');
                            }).post();
                        },
                        view: function (user_id) {
                            var win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/user/view')).add('user_id',user_id).add('window_id',win.id).then(function (response) {
                                win._open(response);
                            }).post();                
                        }                        
                    },
                    desktop: {
                        toggle: (() => {
                            let state = false;
                            return () => {
                                if (state) {
                                    $('#desktop-container').css('display','none');
                                    $('#dashboard-container').css('display','block');
                                } else {
                                    $('#dashboard-container').css('display','none');
                                    $('#desktop-container').css('display','block');
                                }
                                state = !state;
                            }
                        })()
                    },
                    password: {
                        change: {
                            open: () => {
                                let win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/admin/password/form')).add('window_id',win.id).then((response) => {
                                    win._static(true)._title('Admin Password Change')._open(response);
                                }).post();                                
                            }
                        }
                    },
                    add: {
                        package: () => {
                            var val = prompt("Please enter a new documentation package");
                            if (val) {
                                (new EasyAjax('/admin/actions/addpackage')).add('package',val).then(() => {
                                    //window.location.reload();
                                }).post();
                            }
                        },
                        category: () => {
                            var val = prompt("Please enter a new documentation category");
                            if (val) {
                                (new EasyAjax('/admin/actions/addcategory')).add('category',val).then(() => {
                                    //window.location.reload();
                                }).post();
                            }
                        },
                        user: {
                            form: () => {
                                let win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/admin/user/form')).add('window_id',win.id).then((response) => {
                                    win._static(true)._title('New General User')._open(response);
                                }).post();
                            }
                        },
                        administrator: {
                            form: () => {
                                let win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/admin/administrator/form')).add('window_id',win.id).then((response) => {
                                    win._static(true)._title('New Administrator')._open(response);
                                }).post();
                            }
                        }
                    },
                    manage: {
                        users: () => {
                             let win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/user/home')).add('window_id',win.id).then((response) => {
                                win._static(true)._scroll(true)._title('Manage Users')._open(response);
                            }).post();
                        }
                    },
                    cadence: {
                        action: function (action) {
                            if (action) {
                                (new EasyAjax('/admin/cadence/'+action)).then((response) => {
                                    response = JSON.parse(response);
                                    alert(response.result+' ['+response.RC+']');
                                }).post();
                            }
                        }
                    },
                    maintenance: {
                        enter: () => {
                            if (confirm('This action will swap the login page for the maintenance page essentially putting the site into maintenance mode.\n\nDo you wish to continue?')) {
                                (new EasyAjax('/admin/actions/maintenance')).add('enable','Y').then((response) => {
                                    alert(response);
                                }).post();
                            }
                        },
                        leave: () => {
                            if (confirm('This action will restore the login page.\n\nDo you wish to continue?')) {
                                (new EasyAjax('/admin/actions/maintenance')).add('enable','N').then((response) => {
                                    alert(response);
                                }).post();
                            }
                        }
                    },
                    change: {
                        state: function (cs) {
                            if (confirm('Would you like to put the site in '+$(cs).val()+' mode?')) {
                                (new EasyAjax('/admin/system/state')).add('state',$(cs).val()).then((response) => {
                                    console.log(response);
                                }).post();
                            }
                        }
                    },
                    api: {
                        tester: () => {
                            var win = (Administration.create.win.api = Administration.create.win.api ? Administration.create.win.api : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("API Test");
                            (new EasyAjax('/admin/test/apitester')).then((response) => {
                                win._open(response);
                            }).get();                              
                        }
                    },
                    module: {
                        import: function (namespace) {
                            let win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/actions/importpage')).add('namespace',namespace).then((response) => {
                                win._open(response)._scroll(true)._title('Import Data');
                            }).post();
                        },
                        export: function (namespace) {
                            if (confirm('Would you like to export (download) data for the module '+namespace+"?")) {
                                window.open('/admin/actions/export?namespace='+namespace);
                            }
                        },
                        install: function (namespace) {
                            let win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/module/install')).add('namespace',namespace).then((response) => {
                                win._open(response)._scroll(true)._title('Install Module');
                            }).post();                            
                        }
                    },
                    secrets: {
                        add: () => {
                            var win = (Administration.create.win.sec = Administration.create.win.sec ? Administration.create.win.sec : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("New Secret");
                            (new EasyAjax('/admin/secrets/form')).add('window_id',win.id).then((response) => {
                                win._open(response);
                            }).get();                              
                        },
                        review: () => {
                            var win = (Administration.create.win.sec = Administration.create.win.sec ? Administration.create.win.sec : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("Review Secret");
                            (new EasyAjax('/admin/secrets/review')).add('window_id',win.id).then((response) => {
                                win._open(response);
                            }).get();                              
                        }
                    },
                    tests: {
                        win: false,
                        home: () => {
                            let win = (Administration.tests.win = Administration.tests.win ? Administration.tests.win : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("Unit Test Harness");
                            (new EasyAjax('/admin/unittests/home')).add('window_id',win.id).then((response) => {
                                win._open(response);
                            }).get();                              
                        }
                    },
                    flags: {
                        toggle: function (flag,status) {
                            if (confirm('Are you sure you want to set '+makeItBold(flag)+' to '+makeItBold(status)+'?')) {
                                (new EasyAjax('/admin/flag/state').add('flag',flag)).add('state',status).then(function (f) {
                                    location.reload();
                                }).post();
                            }
                        }
                    },
                    create: {
                        win: {
                            pak: false,
                            mod: false,
                            com: false,
                            con: false,
                            sec: false,
                            api: false
                        },
                        package: () => {
                            var win = (Administration.create.win.pak = Administration.create.win.pak ? Administration.create.win.pak : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("New Package");
                            (new EasyAjax('/admin/actions/package')).add('window_id',win.id).then((response) => {
                                win._open(response);
                            }).get();
                        },
                        module: () => {
                            var win = (Administration.create.win.mod = Administration.create.win.mod ? Administration.create.win.mod : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("New Module");
                            (new EasyAjax('/admin/actions/module')).add('window_id',win.id).then((response) => {
                                win._open(response);
                            }).get();                            
                        },
                        component: () => {
                            var win = (Administration.create.win.com = Administration.create.win.com ? Administration.create.win.com : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("New Component");
                            (new EasyAjax('/admin/actions/component')).add('window_id',win.id).then((response) => {
                                win._open(response);
                                win._scroll(true).resize((win)=>{
                                    $('#new_component_area').height(win.content.height());
                                });                                
                            }).get();                            
                        },
                        controller: () => {
                            var win = (Administration.create.win.con = Administration.create.win.con ? Administration.create.win.con : Desktop.semaphore.checkout(true))._static(true)._scroll(true)._title("New Controller");
                            (new EasyAjax('/admin/actions/controller')).add('window_id',win.id).then((response) => {
                                win._open(response);
                            }).get();                            
                        }
                    },
                    documentation: {
                        run: () => {
                            if (confirm("Are you sure you want to run the docs?  It could take a while...")) {
                                let win = Desktop.semaphore.checkout(true);
                                win._title('API Generation')._scroll(true)._open();
                                (new EasyAjax('/admin/documentation/generate')).add('window_id',win.id).then((response) => {
                                    win.set(response);
                                }).post();
                            }
                        },
                        review: () => {
                            let win = Desktop.semaphore.checkout(true);
                            win._title('API Generation')._open('<h3>Generating Documentation, please wait (it could be a while...</h3>');
                            (new EasyAjax('/admin/documentation/review')).add('window_id',win.id).then((response) => {
                                win.set(response);
                            }).post();
                        }
                    },
                    services: {
                        directory: {
                            index: () => {
                                servicesWindow = (servicesWindow) ? servicesWindow : Desktop.semaphore.checkout(true);
                                servicesWindow._title("Index of Services")._static(true)._scroll(true)._open();
                                (new EasyAjax('/admin/directory/index')).add('all','Y').then((response) => {
                                    servicesWindow.set(response);
                                    servicesWindow.resize = () => {
                                       $('#service-directory').height(servicesWindow.content.height() - $('#service-header').height() - $('#service-controls').height());
                                    };
                                    servicesWindow._resize();
                                }).post();
                                return false;
                            }
                        }
                    },
                    templates: {
                        clone: () => {
                            if (confirm("Do you wish to clone a copy of the framework component templates so that you may customize them?\n\nIf so, they will be in the 'lib' directory of your main application module.")) {
                                (new EasyAjax('/admin/actions/clone')).then((response) => {
                                    alert(response);
                                }).post();
                            }
                        }
                    },
                    upload: {
                        win: false,
                        form: () => {
                            var win = (Administration.upload.win) ? Administration.upload.win : Administration.upload.win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/upload/form')).then((response) => {
                                win._title('File Upload')._open(response);
                            }).post();
                        }
                    },
                    smtp: {
                        settings: {
                            win: false,
                            open: () =>  {
                                let win = Administration.smtp.settings.win = Administration.smtp.settings.win ? Administration.smtp.settings.win : Desktop.semaphore.checkout(true);
                                win._static(true)._scroll(true)._title('SMTP Settings');
                                (new EasyAjax('/admin/smtp/settings')).then((response) => {
                                    win._open(response);
                                }).get();
                            }
                        }
                    },
                    cache: {
                        win: false,
                        home: () => {
                                let win = Administration.cache.win = Administration.cache.win ? Administration.cache.win : Desktop.semaphore.checkout(true);
                                win._static(true)._scroll(true)._title('Cache Management');
                                (new EasyAjax('/admin/system/cache')).then((response) => {
                                    win._open(response);
                                }).get();
                        }
                    },
                    status: {
                        check: () => {
                            (new EasyAjax('/admin/system/info')).then((response) => {
                                $('#humble_status').html(response);
                            }).post();
                        },
                        save: () => {
                            (new EasyAjax('/admin/system/save')).add('authorization',$('#authorization-enabled').val()).add('logout',$('#system-logout').val()).add('login',$('#system-login').val()).add('sso',$('#sso-enabled').val()).add('landing',$('#system-landing').val()).add('name',$('#system-name').val()).add('version',$('#system-version').val()).add('enabled',$('#system-enabled').prop('checked')).add('installer',$('#system-installer').prop('checked')).then(function(response) {
                                alert(response);
                            }).post();
                        },
                        quiesce: {
                            period:     120,
                            counter:    0,
                            timer:      null,
                            countdown: () => {
                                $('#quiesce-status-countdown').html(Administration.status.quiesce.counter);
                                if (Administration.status.quiesce.counter) {
                                    Administration.status.quiesce.counter--;
                                    Administration.status.quiesce.timer = window.setTimeout(Administration.status.quiesce.countdown,1000);
                                } else {
                                    Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                    window.clearTimeout(Administration.status.quiesce.timer);
                                    $('#quiesce-status-countdown').html('00');
                                    $("#quiesce-box").fadeOut();
                                    (new EasyAjax('/admin/system/offline')).add('value',0).then((response) => {
                                        (new EasyAjax('/admin/system/quiesce')).add('value',0).then((response) => {
                                            window.location.href = '/index.html?message=The system is now offline';
                                        }).post();
                                    }).post();
                                }
                            },
                            start:                             () => {
                                Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                if (confirm("Do you wish to begin shutting down the system?")) {
                                    (new EasyAjax('/admin/system/quiesce')).add('value','1').then((response) => {
                                        $("#quiesce-box").fadeIn();
                                        Administration.status.quiesce.countdown();
                                    }).post();
                                }
                            },
                            cancel: () => {
                                window.clearTimeout(Administration.status.quiesce.timer);
                                (new EasyAjax('/admin/system/quiesce')).add('value','0').then((response) => {
                                    Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                    $('#quiesce-status-countdown').html('00');
                                    $("#quiesce-box").fadeOut();
                                }).post();
                            }
                        },
                    },
                /*    events: {
                        open: () => {
                            var win = Landing.semaphore.checkout(true);
                            win._open();
                            (new EasyAjax('/admin/events/open')).add('window_id',win.id).then((response) => {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        },
                        fetch: function (page,rows,win) {
                            var data = Pagination.get(win.paginationId);
                            page = page ? page : data.pages.current;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/admin/events/fetch')).add('page',page).add('rows',rows).then((response) => {
                                if (response) {
                                    Pagination.set(win.paginationId,this.getPagination());
                                    $(win.eventList).html(Templater.load('/templates/humble/eventlist').parse('/templates/humble/eventlist', { "win": win, "rows": JSON.parse(response) } ));
                                }
                            }).post();
                        },
                        expand: function (win_id,id,name) {
                            var win         = Desktop.window.list[win_id];
                            (new EasyAjax('/admin/event/expand')).add('id',id).add('name',name).then((response) => {
                                $(win.eventViewer).html(response);
                            }).post();
                        }
                    },   */
                    events: {
                        template: false,
                        home: () => {
                            (new EasyAjax('/admin/events/home')).add('page',1).add('rows',30).then((response) => {
                                $('#humble_events').html(response);
                            }).post()
                        },
                        fetch: function (page,rows) {
                            page = page ? page : 1;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/admin/events/fetch')).add('page',page).add('rows',rows).then((response) => {
                                if (response) {
                                    Pagination.set('event-viewer',this.getPagination());
                                    Templater.load('/templates/humble/admineventlist');
                                    if (!Administration.events.template) {
                                        Administration.events.template = Handlebars.compile(Templater.sources['/templates/humble/admineventlist']);
                                    }
                                    $('#humble-event-list').html(Administration.events.template({ "rows": JSON.parse(response) } ));
                                }
                            }).post();
                        },
                        expand: function (id,name) {
                            (new EasyAjax('/admin/event/expand')).add('id',id).add('name',name).then((response) => {
                                $('#humble-event-detail').html(response);
                            }).post();
                        },
                        open: () => {
                            var win = Desktop.semaphore.checkout(true);
                            win._open();
                            (new EasyAjax('/admin/events/open')).add('window_id',win.id).then((response) => {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        }
                    },
                    workflows: {
                        add: {
                            exportTarget: () => {
                                var win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/paradigm/io/target')).add('window_id',win.id).then((response) => {
                                    win._open(response);
                                }).post();
                            },
                            importToken: () => {
                                var win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/paradigm/io/token')).add('window_id',win.id).then((response) => {
                                    win._open(response);
                                }).post();                            
                            }
                        },                        
                        fetch: () => {
                            (new EasyAjax('/admin/workflows/list')).then((response) => {

                            }).post();
                        },
                        generate: () => {
                            (new EasyAjax('/admin/workflows/generate')).then((response) => {

                            }).post();
                        },
                        remove: () => {
                            (new EasyAjax('/admin/workflows/remove')).then((response) => {

                            }).post();
                        },
                        activate: () => {
                            (new EasyAjax('/admin/workflows/activate')).then((response) => {

                            }).post();
                        },
                        deactivate: () => {
                            (new EasyAjax('/admin/workflows/deactivate')).then((response) => {

                            }).post();
                        }
                    },
                    user: {
                        win:    false,
                        details: () => {
                            let win = Administration.user.win = (Administration.user.win) ? Administration.user.win : Desktop.semaphore.checkout(true);
                            (new EasyAjax('/admin/user/details')).then((response) => {
                                win._static(true)._scroll(true)._open(response);
                            }).get();
                        }
                    },
                    users:      {
                        list:   () => {
                            (new EasyAjax('/admin/users/list')).then((response) => {
                                $E('user_list').innerHTML = response;
                            }).post();
                        },
                        remove: function (uid) {
                            var ss = prompt('Please enter the super secret pass phrase');
                            (new EasyAjax('/admin/users/remove')).add('secret',ss).add('uid',uid).then((response) => {
                                $E('user_list').innerHTML = response;
                            }).post();
                        }
                    },
                    globalAction: () => {
                        var action = $E('globalAction')[$E('globalAction').selectedIndex].value;
                        var t;
                        if (action === 'services') {
                            t = '/admin/directory/generate';
                        } else {
                            t = (action==='documentation') ? '/admin/module/documentation' : '/admin/utilities/'+action;
                        }
                        alert(t);
                        $('#admin-lightbox').css('display','block');
                        $('#admin-lightbox-output').html('Working...');
                        (new EasyAjax(t)).then((response) => {
                            $('#admin-lightbox-output').html(response);
                        }).post();
                    },
/*                    create:     function (directory,pkg) {
                        if (confirm('Would you like to create the path '+directory+' in the '+pkg+' package?')) {
                            (new EasyAjax('/admin/actions/create')).add('package',pkg).add('directory',directory).then(() => {
                                window.location.reload(true);
                            }).post();
                        }
                    },*/
                    activate:   function (what) {
                        if ($E(what).style.display !== 'block') {
                            $E(what).style.display = 'block';
                        } else {
                            $E(what).style.display = 'none';
                        }
                    },
                    init:   () => {
                        Desktop.init(Desktop.enable);
                        Desktop.semaphore.init();
                        var f = (() => {
                            return function (server) {
                                server = JSON.parse(server);
                                $('#server-memory-load').html(server.memory.used+'/'+server.memory.total+' ['+server.memory.percentage+'%]');
                                $('#server-cpu-load').html((Math.round(server.cpu.load*1000)/1000)+'%');
                                $('#server-tasks').html(server.apache.thread_count+'/'+server.tasks.count);
                            }
                        })();
                        var g = (() => {
                            return function (data) {
                                var cadence = JSON.parse(data);
                                $('#cadence_stopped').css('display',(cadence.running ? 'none' : 'block'));
                                $('#cadence_running').css('display',(cadence.running ? 'block' : 'none'));
                            }
                        })();                        
                        Heartbeat.register('admin',true,'systemStatus',f,1,{});
                        Heartbeat.register('admin',true,'cadenceStatus',g,1,{});
                        Heartbeat.init();
                    },
                    action: function (action,pkg,module) {
                        var ao = new EasyAjax('/admin/actions/'+action);
                        ao.add('package',pkg);
                        ao.add('module',module);
                        $('#admin-lightbox').css('display','block');
                        $('#admin-lightbox-output').html('Working...');
                        ao.then((response) => {
                            $('#admin-lightbox-output').html(response);
                        });
                        ao.post();
                    },
                    install: function (pkg,root,namespace) {
                       if (confirm('This action will install the module.\n\nThis will also re-run any install SQL statements.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/admin/utilities/install');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#admin-lightbox').css('display','block');
                           $('#admin-lightbox-output').html('Working...');
                           ao.then((response) => {
                               alert(response);
                               //window.location.reload();
                           });
                           ao.post();
                       }
                    },
                    uninstall: function (pkg,root,namespace) {
                       if (confirm('This action will disable and uninstall the module.\n\nAre you sure you wish to continue?')) {
                           (new EasyAjax('/admin/utilities/uninstall')).add('namespace',namespace).add('root',root).add('package',pkg).then(() => {
                               window.location.reload();
                           }).post();
                       }
                    },
                    update: function (pkg,root,namespace) {
                       if (confirm('This action will update the module '+root+'.\n\nThis will run any recent update SQL statements.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox-output').html('Working...');
                           $('#admin-lightbox').css('display','block');
                           (new EasyAjax('/admin/utilities/update')).add('namespace',namespace).add('root',root).add('package',pkg).then((response) => {
                               $('#admin-lightbox-output').html(response);
                           }).post();
                       }
                    },
                    compile: function (pkg,root,namespace) {
                       if (confirm('This action will compile the controllers for module '+root+'.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox').css('display','block');
                           $('#admin-lightbox-output').html('Working...');
                           (new EasyAjax('/admin/utilities/compile')).add('namespace',namespace).add('root',root).add('package',pkg).then((response) => {
                               $('#admin-lightbox-output').html(response);
                           }).post();
                       }
                    },
                    clearcache: function (pkg,root,namespace) {
                       if (confirm('This action will clear the cache for the module.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox').css('display','block');
                           $('#admin-lightbox-output').html('Working...');
                           (new EasyAjax('/admin/utilities/clear')).add('namespace',namespace).add('root',root).add('package',pkg).then((response) => {
                               $('#admin-lightbox-output').html(response);
                           }).post();
                       }
                    },
                    enable: function (cb,module,pkg) {
                       (new EasyAjax('/admin/actions/enable')).add('namespace',module).add('package',pkg).add('enabled',((cb.checked) ? "Y" : "N")).then(() => {
                       }).post();
                    },
                    logs: {
                        windows: { },
                        tabs: null,
                        created: false,
                        users: {
                            open: function (win,win_id) {

                                (new EasyAjax('/admin/actions/users')).add('viewing',win).add('window_id',win_id).then((response) => {
                                    $('#log-viewer-body-'+win_id).html(response);
                                }).get();
                            },
                            fetch: function (user_id,user_name) {
                                var win = Desktop.semaphore.checkout(true);
                                win._title(user_name+' Log')._open();
                                (new EasyAjax('/admin/log/users')).add('log','user').add('user_id',user_id).then((response) => {
                                    win.set(response);
                                }).post();
                            }
                        },
                        open: function (log) {
                            if (!Administration.logs.windows[log]) {
                                Administration.logs.windows[log] = Desktop.semaphore.checkout(true);
                                Administration.logs.windows[log]._title(log+ ' Log')._scroll(false)._static(true);
                            }
                            var win = Administration.logs.windows[log]._open();
                            (new EasyAjax('/admin/actions/log')).add('log',log.toLowerCase()).add('window_id',win.id).then((response) => {
                                win.set(response);
                            }).post();
                        },
                        clear: function (log) {
                            if (confirm('Clear the '+log.charAt(0).toUpperCase() + log.slice(1)+' log?')) {
                                (new EasyAjax('/admin/log/clearlog')).add('log',log.toLowerCase()).then((response) => {
                                    alert(response);
                                }).post();
                            }
                        },
                        fetch: function (log,win_id) {
                            var win     = Desktop.window.list[win_id];
                            win.viewing = log;
                            (new EasyAjax('/admin/log/fetch')).add('log',log.toLowerCase()).add('size',100000).then((response) => {
                                $(win.viewer).val(response);
                            }).post();
                        },
                        initialize: () => {
                            if (!Administration.logs.created) {
                                Administration.logs.created = true;
                            }
                            return true;
                        }
                    }
                }
            })();

export let administration = Functions;
