Administration = (function () {
                return {
                    add: {
                        package: function () {
                            var val = prompt("Please enter a new documentation package");
                            if (val) {
                                (new EasyAjax('/core/admin/addpackage')).add('package',val).callback(function () {
                                    window.location.reload();
                                }).post();
                            }
                        },
                        category: function () {
                            var val = prompt("Please enter a new documentation package");
                            if (val) {
                                (new EasyAjax('/core/admin/addcategory')).add('category',val).callback(function () {
                                    window.location.reload();
                                }).post();
                            }
                        }
                    },
                    status: {
                        check: function () {
                            (new EasyAjax('/core/system/status')).callback(function (response) {
                                $('#humble_status').html(response);
                            }).post();
                        },
                        save: function () {
                            (new EasyAjax('/core/system/save')).add('authorization',$('#authorization-enabled').val()).add('logout',$('#system-logout').val()).add('login',$('#system-login').val()).add('sso',$('#sso-enabled').val()).add('landing',$('#system-landing').val()).add('name',$('#system-name').val()).add('version',$('#system-version').val()).add('enabled',$('#system-enabled').prop('checked')).add('installer',$('#system-installer').prop('checked')).callback(function(response) {
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
                                    (new EasyAjax('/core/system/offline')).add('value',0).callback(function (response) {
                                        (new EasyAjax('/core/system/quiesce')).add('value',0).callback(function (response) {
                                            window.location.href = '/index.html?m=The system is now offline';
                                        }).post();
                                    }).post();
                                }
                            },
                            start:                             function () {
                                Administration.status.quiesce.counter = Administration.status.quiesce.period;
                                if (confirm("Do you wish to begin shutting down the system?")) {
                                    (new EasyAjax('/core/system/quiesce')).add('value','1').callback(function (response) {
                                        $("#quiesce-box").fadeIn();
                                        Administration.status.quiesce.countdown();
                                    }).post();
                                }
                            },
                            cancel: function () {
                                window.clearTimeout(Administration.status.quiesce.timer);
                                (new EasyAjax('/core/system/quiesce')).add('value','0').callback(function (response) {
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
                            (new EasyAjax('/focos/events/open')).add('window_id',win.id).callback(function (response) {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        },
                        fetch: function (page,rows,win) {
                            var data = Pagination.get(win.paginationId);
                            page = page ? page : data.pages.current;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/focos/events/fetch')).add('page',page).add('rows',rows).callback(function (response) {
                                if (response) {
                                    Pagination.set(win.paginationId,this.getPagination());
                                    $(win.eventList).html(Templater.load('/templates/focos/eventlist').parse('/templates/focos/eventlist', { "win": win, "rows": JSON.parse(response) } ));
                                }
                            }).post();
                        },
                        expand: function (win_id,id,name) {
                            var win         = Desktop.window.list[win_id];
                            (new EasyAjax('/core/event/expand')).add('id',id).add('name',name).callback(function (response) {
                                $(win.eventViewer).html(response);
                            }).post();
                        }
                    },   */
                    events: {
                        template: false,
                        home: function () {
                            (new EasyAjax('/core/events/home')).add('page',1).add('rows',30).callback(function (response) {
                                $('#humble_events').html(response);
                            }).post()
                        },
                        fetch: function (page,rows) {
                            page = page ? page : 1;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/core/events/fetch')).add('page',page).add('rows',rows).callback(function (response) {
                                if (response) {
                                    Pagination.set('event-viewer',this.getPagination());
                                    Templater.load('/templates/core/admineventlist');
                                    if (!Administration.events.template) {
                                        Administration.events.template = Handlebars.compile(Templater.sources['/templates/core/admineventlist']);
                                    }
                                    $('#humble-event-list').html(Administration.events.template({ "rows": JSON.parse(response) } ));
                                }
                            }).post();
                        },
                        expand: function (id,name) {
                            (new EasyAjax('/core/event/expand')).add('id',id).add('name',name).callback(function (response) {
                                $('#humble-event-detail').html(response);
                            }).post();
                        },
                        open: function () {
                            var win = Desktop.semaphore.checkout(true);
                            win._open();
                            (new EasyAjax('/core/events/open')).add('window_id',win.id).callback(function (response) {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        }
                    },
                    workflows: {
                        fetch: function () {
                            (new EasyAjax('/core/workflows/list')).callback(function (response) {

                            }).post();
                        },
                        generate: function () {
                            (new EasyAjax('/core/workflows/generate')).callback(function (response) {

                            }).post();
                        },
                        remove: function () {
                            (new EasyAjax('/core/workflows/remove')).callback(function (response) {

                            }).post();
                        },
                        activate: function () {
                            (new EasyAjax('/core/workflows/activate')).callback(function (response) {

                            }).post();
                        },
                        deactivate: function () {
                            (new EasyAjax('/core/workflows/deactivate')).callback(function (response) {

                            }).post();
                        }
                    },
                    users:      {
                        list:   function () {
                            (new EasyAjax('/core/users/list')).callback(function (response) {
                                $E('user_list').innerHTML = response;
                            }).post();
                        },
                        remove: function (uid) {
                            var ss = prompt('Please enter the super secret pass phrase');
                            (new EasyAjax('/core/users/remove')).add('secret',ss).add('uid',uid).callback(function (response) {
                                $E('user_list').innerHTML = response;
                            }).post();
                        }
                    },
                    globalAction: function () {
                        var action = $E('globalAction')[$E('globalAction').selectedIndex].value;
                        var t;
                        if (action === 'services') {
                            t = '/core/directory/generate';
                        } else {
                            t = (action==='documentation') ? '/core/module/documentation' : '/core/utilities/'+action;
                        }
                        $('#lightbox').css('display','block');
                        $('#actionStatus').html('Working...');
                        (new EasyAjax(t)).callback(function (response) {
                            $('#actionStatus').html(response+"\n\nDone!\n");
                        }).post();
                    },
                    create:     function (directory,pkg) {
                        if (confirm('Would you like to create the path '+directory+' in the '+pkg+' package?')) {
                            (new EasyAjax('/core/admin/create')).add('package',pkg).add('directory',directory).callback(function () {
                                window.location.reload(true);
                            }).post();
                        }
                    },
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
                        new EasyEdits('/web/edits/newuser.json','newuser');
                        new EasyEdits('/web/edits/newmodule.json','newmodule');
                        new EasyEdits('/web/edits/newcomponent.json','newcomponent');
                        new EasyEdits('/web/edits/newcontroller.json','newcontroller');
                        new EasyEdits('/web/edits/newpackage.json','newpackage');
                        $(window).resize(function () {
                            $('#widgets-column').height($(window).height() - $E('navigation-bar').offsetHeight - $E('humble-footer').offsetHeight);
                            $(document).css('overflow','hidden');
                            $('#apps-column').height($(window).height() - $E('navigation-bar').offsetHeight - $E('humble-footer').offsetHeight);
                            $('#apps-column').css('overflow','auto');
                            $('#modules_list').width($(window).width() - $E('widgets-column').offsetWidth - $E('humble-creation-forms').offsetWidth -40);
                        });
                        $(window).resize();
                        var win = Desktop.semaphore.checkout(true);
                    },
                    action: function (action,pkg,module) {
                        var ao = new EasyAjax('/core/admin/'+action);
                        ao.add('package',pkg);
                        ao.add('module',module);
                        $('#lightbox').css('display','block');
                        $('#actionStatus').html('Working...');
                        ao.callback(function (response) {
                            $('#lightbox').html(response);
                        });
                        ao.post();
                    },
                    install: function (pkg,root,namespace) {
                       if (confirm('This action will install the module.\n\nThis will also re-run any install SQL statements.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/core/utilities/install');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#lightbox').css('display','block');
                           $('#actionStatus').html('Working...');
                           ao.callback(function (response) {
                               alert(response);
                               window.location.reload();
                           });
                           ao.post();
                       }
                    },
                    uninstall: function (pkg,root,namespace) {
                       if (confirm('This action will disable and uninstall the module.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/core/utilities/uninstall');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           ao.callback(function () {
                               window.location.reload();
                           });
                           ao.post();
                       }
                    },
                    refresh: function (pkg,root,namespace) {
                       if (confirm('This action will refresh the module '+root+'.\n\nThis will update the module as well as copy new images.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/core/utilities/refresh');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#lightbox').css('display','block');
                           $('#actionStatus').html('Working...');
                           ao.callback(function (response) {
                               $('#actionStatus').html(response+"\n\nDone!\n");
                           });
                           ao.post();
                       }
                    },
                    update: function (pkg,root,namespace) {
                       if (confirm('This action will update the module '+root+'.\n\nThis will run any recent update SQL statements.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/core/utilities/update');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#lightbox').css('display','block');
                           $('#actionStatus').html('Working...');
                           ao.callback(function (response) {
                               $('#actionStatus').html(response+"\n\nDone!\n");
                           });
                           ao.post();
                       }
                    },
                    compile: function (pkg,root,namespace) {
                       if (confirm('This action will compile the controllers for module '+root+'.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/core/utilities/compile');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#lightbox').css('display','block');
                           $('#actionStatus').html('Working...');
                           ao.callback(function (response) {
                               $('#actionStatus').html(response+"\n\nDone!\n");
                           });
                           ao.post();
                       }
                    },
                    clearcache: function (pkg,root,namespace) {
                       if (confirm('This action will clear the cache for the module.\n\nAre you sure you wish to continue?')) {
                            $('#lightbox').css('display','block');
                            $('#actionStatus').html('Working...');
                           (new EasyAjax('/core/utilities/clear')).add('namespace',namespace).add('root',root).add('package',pkg).callback(function (response) {
                                $('#actionStatus').html(response+"\n\nDone!\n");
                           }).post();
                       }
                    },
                    enable: function (cb,module,pkg) {
                       (new EasyAjax('/core/admin/enable')).add('namespace',module).add('package',pkg).add('enabled',((cb.checked) ? "Y" : "N")).callback(function () {
                       }).post();
                    },
                    logs: {
                        windows: { },
                        tabs: null,
                        created: false,
                        open: function (log) {
                            if (!Administration.logs.windows[log]) {
                                Administration.logs.windows[log] = Desktop.semaphore.checkout();
                            }
                            var win = Desktop.window.list[Administration.logs.windows[log]];
                            win._title(log+ ' Log | Humble')._open();
                            log = log.toLowerCase();
                            (new EasyAjax('/core/admin/log')).add('log',log).add('window_id',win.id).callback(function (response) {
                                win.set(response);
                            }).post();
                        },
                        clear: function (log) {
                            if (confirm('Clear the '+log.charAt(0).toUpperCase() + log.slice(1)+' log?')) {
                                (new EasyAjax('/core/log/clearlog')).add('log',log).callback(function (response) {
                                    alert(response);
                                }).post();
                            }
                        },
                        fetch: function (log,win_id) {
                            var win = Desktop.window.list[win_id];
                            win.viewing = log;
                            (new EasyAjax('/core/log/fetch')).add('log',log.toLowerCase()).add('size',100000).callback(function (response) {
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