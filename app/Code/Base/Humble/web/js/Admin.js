var Administration = (function () {
                var servicesWindow = false;
                return {
                    add: {
                        package: function () {
                            var val = prompt("Please enter a new documentation package");
                            if (val) {
                                (new EasyAjax('/humble/admin/addpackage')).add('package',val).then(function () {
                                    //window.location.reload();
                                }).post();
                            }
                        },
                        category: function () {
                            var val = prompt("Please enter a new documentation category");
                            if (val) {
                                (new EasyAjax('/humble/admin/addcategory')).add('category',val).then(function () {
                                    //window.location.reload();
                                }).post();
                            }
                        }
                    },
                    change: {
                        state: function (cs) {
                            if (confirm('Would you like to put the site in '+$(cs).val()+' mode?')) {
                                (new EasyAjax('/humble/system/state')).add('state',$(cs).val()).then(function (response) {
                                    console.log(response);
                                }).post();
                            }
                        }
                    },
                    api: {
                        tester: function () {
                            var win = (Administration.create.win.api = Administration.create.win.api ? Administration.create.win.api : Desktop.semaphore.checkout(true))._static(true)._title("API Test");
                            (new EasyAjax('/humble/test/apitester')).then(function (response) {
                                win._open(response);
                            }).get();                              
                        }
                    },
                    module: {
                        import: function (namespace) {
                            let win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/humble/admin/importpage')).then(function (response) {
                                win._open(response)._title('Import Data');
                            }).post();
                        },
                        export: function (namespace) {
                            if (confirm('Would you like to export (download) data for the module '+namespace+"?")) {
                                window.open('/humble/admin/export?namespace='+namespace);
                            }
                        },
                        install: function (namespace) {
                            let win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/humble/admin/installpage')).then(function (response) {
                                win._open(response)._title('Install Module');
                            }).post();                            
                        }
                    },
                    secrets: {
                        add: function () {
                            var win = (Administration.create.win.sec = Administration.create.win.sec ? Administration.create.win.sec : Desktop.semaphore.checkout(true))._static(true)._title("New Secret");
                            (new EasyAjax('/humble/secrets/form')).then(function (response) {
                                win._open(response);
                            }).get();                              
                        },
                        review: function () {
                            var win = (Administration.create.win.sec = Administration.create.win.sec ? Administration.create.win.sec : Desktop.semaphore.checkout(true))._static(true)._title("New Secret");
                            (new EasyAjax('/humble/secrets/review')).then(function (response) {
                                win._open(response);
                            }).get();                              
                        }
                    },
                    tests: {
                        win: false,
                        home: function () {
                            let win = (Administration.tests.win = Administration.tests.win ? Administration.tests.win : Desktop.semaphore.checkout(true))._static(true)._title("Unit Test Harness");
                            (new EasyAjax('/humble/unittests/home')).add('window_id',win.id).then(function (response) {
                                win._open(response);
                            }).get();                              
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
                        package: function () {
                            var win = (Administration.create.win.pak = Administration.create.win.pak ? Administration.create.win.pak : Desktop.semaphore.checkout(true))._static(true)._title("New Package");
                            (new EasyAjax('/humble/admin/package')).then(function (response) {
                                win._open(response);
                            }).get();
                        },
                        module: function () {
                            var win = (Administration.create.win.mod = Administration.create.win.mod ? Administration.create.win.mod : Desktop.semaphore.checkout(true))._static(true)._title("New Module");
                            (new EasyAjax('/humble/admin/module')).then(function (response) {
                                win._open(response);
                            }).get();                            
                        },
                        component: function () {
                            var win = (Administration.create.win.com = Administration.create.win.com ? Administration.create.win.com : Desktop.semaphore.checkout(true))._static(true)._title("New Component");
                            (new EasyAjax('/humble/admin/component')).then(function (response) {
                                win._open(response);
                            }).get();                            
                        },
                        controller: function () {
                            var win = (Administration.create.win.con = Administration.create.win.con ? Administration.create.win.con : Desktop.semaphore.checkout(true))._static(true)._title("New Controller");
                            (new EasyAjax('/humble/admin/controller')).then(function (response) {
                                win._open(response);
                            }).get();                            
                        }
                    },
                    documentation: {
                        run: function () {
                            if (confirm("Are you sure you want to run the docs?  It could take a while...")) {
                                let win = Desktop.semaphore.checkout(true);
                                win._title('API Generation')._open();
                                (new EasyAjax('/humble/documentation/generate')).add('window_id',win.id).then(function (response) {
                                    win.set(response);
                                }).post();
                            }
                        },
                        review: function () {
                            let win = Desktop.semaphore.checkout(true);
                            win._title('API Generation')._open('<h3>Generating Documentation, please wait (it could be a while...</h3>');
                            (new EasyAjax('/humble/documentation/review')).add('window_id',win.id).then(function (response) {
                                win.set(response);
                            }).post();
                        }
                    },
                    services: {
                        directory: {
                            index: function () {
                                servicesWindow = (servicesWindow) ? servicesWindow : Desktop.semaphore.checkout(true);
                                servicesWindow._title("Index of Services")._static(true)._open();
                                (new EasyAjax('/humble/directory/index')).add('all','Y').then(function (response) {
                                    servicesWindow.set(response);
                                    servicesWindow.resize = function () {
                                       console.log(servicesWindow.content.height())
                                       $('#service-directory').height(servicesWindow.content.height() - $('#service-header').height() - $('#service-controls').height());
                                    };
                                    servicesWindow._resize();
                                }).post();
                                return false;
                            }
                        }
                    },
                    templates: {
                        clone: function () {
                            if (confirm("Do you wish to clone a copy of the framework component templates so that you may customize them?\n\nIf so, they will be in the 'lib' directory of your main application module.")) {
                                (new EasyAjax('/humble/admin/clone')).then(function (response) {
                                    alert(response);
                                }).post();
                            }
                        }
                    },
                    upload: {
                        win: false,
                        form: function () {
                            var win = (Administration.upload.win) ? Administration.upload.win : Administration.upload.win = Desktop.semaphore.checkout(true);
                            (new EasyAjax('/humble/upload/form')).then(function (response) {
                                win._title('File Upload')._open(response);
                            }).post();
                        }
                    },
                    status: {
                        check: function () {
                            (new EasyAjax('/humble/system/status')).then(function (response) {
                                $('#humble_status').html(response);
                            }).post();
                        },
                        save: function () {
                            (new EasyAjax('/humble/system/save')).add('authorization',$('#authorization-enabled').val()).add('logout',$('#system-logout').val()).add('login',$('#system-login').val()).add('sso',$('#sso-enabled').val()).add('landing',$('#system-landing').val()).add('name',$('#system-name').val()).add('version',$('#system-version').val()).add('enabled',$('#system-enabled').prop('checked')).add('installer',$('#system-installer').prop('checked')).then(function(response) {
                                alert(response);
                            }).post();
                        },
                        quiesce: {
                            period:     120,
                            counter:    0,
                            timer:      null,
                            countdown: function () {
                                $('#quiesce-status-countdown').html(Administration.status.quiesce.counter);
                                if (Administration.status.quiesce.counter) {
                                    Administration.status.quiesce.counter--;
                                    Administration.status.quiesce.timer = window.setTimeout(Administration.status.quiesce.countdown,1000);
                                } else {
                                    Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                    window.clearTimeout(Administration.status.quiesce.timer);
                                    $('#quiesce-status-countdown').html('00');
                                    $("#quiesce-box").fadeOut();
                                    (new EasyAjax('/humble/system/offline')).add('value',0).then(function (response) {
                                        (new EasyAjax('/humble/system/quiesce')).add('value',0).then(function (response) {
                                            window.location.href = '/index.html?message=The system is now offline';
                                        }).post();
                                    }).post();
                                }
                            },
                            start:                             function () {
                                Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                if (confirm("Do you wish to begin shutting down the system?")) {
                                    (new EasyAjax('/humble/system/quiesce')).add('value','1').then(function (response) {
                                        $("#quiesce-box").fadeIn();
                                        Administration.status.quiesce.countdown();
                                    }).post();
                                }
                            },
                            cancel: function () {
                                window.clearTimeout(Administration.status.quiesce.timer);
                                (new EasyAjax('/humble/system/quiesce')).add('value','0').then(function (response) {
                                    Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                    $('#quiesce-status-countdown').html('00');
                                    $("#quiesce-box").fadeOut();
                                }).post();
                            }
                        },
                    },
                /*    events: {
                        open: function () {
                            var win = Landing.semaphore.checkout(true);
                            win._open();
                            (new EasyAjax('/humble/events/open')).add('window_id',win.id).then(function (response) {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        },
                        fetch: function (page,rows,win) {
                            var data = Pagination.get(win.paginationId);
                            page = page ? page : data.pages.current;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/humble/events/fetch')).add('page',page).add('rows',rows).then(function (response) {
                                if (response) {
                                    Pagination.set(win.paginationId,this.getPagination());
                                    $(win.eventList).html(Templater.load('/templates/humble/eventlist').parse('/templates/humble/eventlist', { "win": win, "rows": JSON.parse(response) } ));
                                }
                            }).post();
                        },
                        expand: function (win_id,id,name) {
                            var win         = Desktop.window.list[win_id];
                            (new EasyAjax('/humble/event/expand')).add('id',id).add('name',name).then(function (response) {
                                $(win.eventViewer).html(response);
                            }).post();
                        }
                    },   */
                    events: {
                        template: false,
                        home: function () {
                            (new EasyAjax('/humble/events/home')).add('page',1).add('rows',30).then(function (response) {
                                $('#humble_events').html(response);
                            }).post()
                        },
                        fetch: function (page,rows) {
                            page = page ? page : 1;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/humble/events/fetch')).add('page',page).add('rows',rows).then(function (response) {
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
                            (new EasyAjax('/humble/event/expand')).add('id',id).add('name',name).then(function (response) {
                                $('#humble-event-detail').html(response);
                            }).post();
                        },
                        open: function () {
                            var win = Desktop.semaphore.checkout(true);
                            win._open();
                            (new EasyAjax('/humble/events/open')).add('window_id',win.id).then(function (response) {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        }
                    },
                    workflows: {
                        add: {
                            exportTarget: function () {
                                var win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/paradigm/io/target')).add('window_id',win.id).then(function (response) {
                                    win._open(response);
                                }).post();
                            },
                            importToken: function () {
                                var win = Desktop.semaphore.checkout(true);
                                (new EasyAjax('/paradigm/io/token')).add('window_id',win.id).then(function (response) {
                                    win._open(response);
                                }).post();                            
                            }
                        },                        
                        fetch: function () {
                            (new EasyAjax('/humble/workflows/list')).then(function (response) {

                            }).post();
                        },
                        generate: function () {
                            (new EasyAjax('/humble/workflows/generate')).then(function (response) {

                            }).post();
                        },
                        remove: function () {
                            (new EasyAjax('/humble/workflows/remove')).then(function (response) {

                            }).post();
                        },
                        activate: function () {
                            (new EasyAjax('/humble/workflows/activate')).then(function (response) {

                            }).post();
                        },
                        deactivate: function () {
                            (new EasyAjax('/humble/workflows/deactivate')).then(function (response) {

                            }).post();
                        }
                    },
                    users:      {
                        list:   function () {
                            (new EasyAjax('/humble/users/list')).then(function (response) {
                                $E('user_list').innerHTML = response;
                            }).post();
                        },
                        remove: function (uid) {
                            var ss = prompt('Please enter the super secret pass phrase');
                            (new EasyAjax('/humble/users/remove')).add('secret',ss).add('uid',uid).then(function (response) {
                                $E('user_list').innerHTML = response;
                            }).post();
                        }
                    },
                    globalAction: function () {
                        var action = $E('globalAction')[$E('globalAction').selectedIndex].value;
                        var t;
                        if (action === 'services') {
                            t = '/humble/directory/generate';
                        } else {
                            t = (action==='documentation') ? '/humble/module/documentation' : '/humble/utilities/'+action;
                        }
                        alert(t);
                        $('#admin-lightbox').css('display','block');
                        $('#admin-lightbox-output').html('Working...');
                        (new EasyAjax(t)).then(function (response) {
                            $('#admin-lightbox-output').html(response);
                        }).post();
                    },
/*                    create:     function (directory,pkg) {
                        if (confirm('Would you like to create the path '+directory+' in the '+pkg+' package?')) {
                            (new EasyAjax('/humble/admin/create')).add('package',pkg).add('directory',directory).then(function () {
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
                    init:   function () {
                        Desktop.init(Desktop.enable);
                        Desktop.semaphore.init();
                       // new EasyEdits('/web/edits/newmodule.json','newmodule');
                      //  new EasyEdits('/web/edits/newcomponent.json','newcomponent');
                       // new EasyEdits('/web/edits/newcontroller.json','newcontroller');
                      //  new EasyEdits('/web/edits/newpackage.json','newpackage');
                        $(window).resize(function () {
                            $('#widgets-column').height($(window).height() - $E('navigation-bar').offsetHeight - $E('humble-footer').offsetHeight);
                            $(document).css('overflow','hidden');
                            $('#apps-column').height($(window).height() - $E('navigation-bar').offsetHeight - $E('humble-footer').offsetHeight);
                            $('#apps-column').css('overflow','auto');
                            $('#modules_list').width($(window).width() - $E('widgets-column').offsetWidth - 20);
                            $('#admin-lightbox').width($(document).width()).height($(document).height());
                        }).resize();
                    },
                    action: function (action,pkg,module) {
                        var ao = new EasyAjax('/humble/admin/'+action);
                        ao.add('package',pkg);
                        ao.add('module',module);
                        $('#admin-lightbox').css('display','block');
                        $('#admin-lightbox-output').html('Working...');
                        ao.then(function (response) {
                            $('#admin-lightbox-output').html(response);
                        });
                        ao.post();
                    },
                    install: function (pkg,root,namespace) {
                       if (confirm('This action will install the module.\n\nThis will also re-run any install SQL statements.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/humble/utilities/install');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#admin-lightbox').css('display','block');
                           $('#admin-lightbox-output').html('Working...');
                           ao.then(function (response) {
                               window.location.reload();
                           });
                           ao.post();
                       }
                    },
                    uninstall: function (pkg,root,namespace) {
                       if (confirm('This action will disable and uninstall the module.\n\nAre you sure you wish to continue?')) {
                           (new EasyAjax('/humble/utilities/uninstall')).add('namespace',namespace).add('root',root).add('package',pkg).then(function () {
                               window.location.reload();
                           }).post();
                       }
                    },
                    update: function (pkg,root,namespace) {
                       if (confirm('This action will update the module '+root+'.\n\nThis will run any recent update SQL statements.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox-output').html('Working...');
                           $('#admin-lightbox').css('display','block');
                           (new EasyAjax('/humble/utilities/update')).add('namespace',namespace).add('root',root).add('package',pkg).then(function (response) {
                               $('#admin-lightbox-output').html(response);
                           }).post();
                       }
                    },
                    compile: function (pkg,root,namespace) {
                       if (confirm('This action will compile the controllers for module '+root+'.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox').css('display','block');
                           $('#admin-lightbox-output').html('Working...');
                           (new EasyAjax('/humble/utilities/compile')).add('namespace',namespace).add('root',root).add('package',pkg).then(function (response) {
                               $('#admin-lightbox-output').html(response);
                           }).post();
                       }
                    },
                    clearcache: function (pkg,root,namespace) {
                       if (confirm('This action will clear the cache for the module.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox').css('display','block');
                           $('#admin-lightbox-output').html('Working...');
                           (new EasyAjax('/humble/utilities/clear')).add('namespace',namespace).add('root',root).add('package',pkg).then(function (response) {
                               $('#admin-lightbox-output').html(response);
                           }).post();
                       }
                    },
                    enable: function (cb,module,pkg) {
                       (new EasyAjax('/humble/admin/enable')).add('namespace',module).add('package',pkg).add('enabled',((cb.checked) ? "Y" : "N")).then(function () {
                       }).post();
                    },
                    logs: {
                        windows: { },
                        tabs: null,
                        created: false,
                        users: {
                            open: function (win,win_id) {
                                (new EasyAjax('/humble/admin/users')).add('viewing',win).add('window_id',win_id).then(function (response) {
                                    $('#log-viewer-body-'+win_id).html(response);
                                }).get();
                            },
                            fetch: function (user_id,user_name) {
                                var win = Desktop.semaphore.checkout(true);
                                win._title(user_name+' Log')._open();
                                (new EasyAjax('/humble/log/users')).add('log','user').add('user_id',user_id).then(function (response) {
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
                            (new EasyAjax('/humble/admin/log')).add('log',log.toLowerCase()).add('window_id',win.id).then(function (response) {
                                win.set(response);
                            }).post();
                        },
                        clear: function (log) {
                            if (confirm('Clear the '+log.charAt(0).toUpperCase() + log.slice(1)+' log?')) {
                                (new EasyAjax('/humble/log/clearlog')).add('log',log.toLowerCase()).then(function (response) {
                                    alert(response);
                                }).post();
                            }
                        },
                        fetch: function (log,win_id) {
                            var win = Desktop.window.list[win_id];
                            win.viewing = log;
                            (new EasyAjax('/humble/log/fetch')).add('log',log.toLowerCase()).add('size',100000).then(function (response) {
                                $(win.viewer).val(response);
                            }).post();
                        },
                        initialize: function () {
                            if (!Administration.logs.created) {
                                Administration.logs.created = true;
                            }
                            return true;
                        }
                    }
                }
            })();
