Administration = (function () {
                return {
                    add: {
                        package: function () {
                            var val = prompt("Please enter a new documentation package");
                            if (val) {
                                (new EasyAjax('/humble/admin/addpackage')).add('package',val).then(function () {
                                    window.location.reload();
                                }).post();
                            }
                        },
                        category: function () {
                            var val = prompt("Please enter a new documentation package");
                            if (val) {
                                (new EasyAjax('/humble/admin/addcategory')).add('category',val).then(function () {
                                    window.location.reload();
                                }).post();
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
                                            window.location.href = '/index.html?m=The system is now offline';
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
                            (new EasyAjax('/focos/events/open')).add('window_id',win.id).then(function (response) {
                                win.set(response);
                                win._title('Event Viewer');
                            }).get();
                        },
                        fetch: function (page,rows,win) {
                            var data = Pagination.get(win.paginationId);
                            page = page ? page : data.pages.current;
                            rows = rows ? rows : 30;
                            (new EasyAjax('/focos/events/fetch')).add('page',page).add('rows',rows).then(function (response) {
                                if (response) {
                                    Pagination.set(win.paginationId,this.getPagination());
                                    $(win.eventList).html(Templater.load('/templates/focos/eventlist').parse('/templates/focos/eventlist', { "win": win, "rows": JSON.parse(response) } ));
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
                        $('#admin-lightbox').css('display','block');
                        $('#actionStatus').html('Working...');
                        (new EasyAjax(t)).then(function (response) {
                            $('#actionStatus').html(response+"\n\nDone!\n");
                        }).post();
                    },
                    create:     function (directory,pkg) {
                        if (confirm('Would you like to create the path '+directory+' in the '+pkg+' package?')) {
                            (new EasyAjax('/humble/admin/create')).add('package',pkg).add('directory',directory).then(function () {
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
                            $('#admin-lightbox').width($(document).width()).height($(document).height());
                        }).resize();
                    },
                    action: function (action,pkg,module) {
                        var ao = new EasyAjax('/humble/admin/'+action);
                        ao.add('package',pkg);
                        ao.add('module',module);
                        $('#admin-lightbox').css('display','block');
                        $('#actionStatus').html('Working...');
                        ao.then(function (response) {
                            $('#lightbox').html(response);
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
                           $('#actionStatus').html('Working...');
                           ao.then(function (response) {
                               alert(response);
                               window.location.reload();
                           });
                           ao.post();
                       }
                    },
                    uninstall: function (pkg,root,namespace) {
                       if (confirm('This action will disable and uninstall the module.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/humble/utilities/uninstall');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           ao.then(function () {
                               window.location.reload();
                           });
                           ao.post();
                       }
                    },
                    refresh: function (pkg,root,namespace) {
                       if (confirm('This action will refresh the module '+root+'.\n\nThis will update the module as well as copy new images.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/humble/utilities/refresh');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#admin-lightbox').css('display','block');
                           $('#actionStatus').html('Working...');
                           ao.then(function (response) {
                               $('#actionStatus').html(response+"\n\nDone!\n");
                           });
                           ao.post();
                       }
                    },
                    update: function (pkg,root,namespace) {
                       if (confirm('This action will update the module '+root+'.\n\nThis will run any recent update SQL statements.\n\nAre you sure you wish to continue?')) {
                           $('#admin-lightbox-output').html('Working...');
                           $('#admin-lightbox').css('display','block');
                           (new EasyAjax('/humble/utilities/update')).add('namespace',namespace).add('root',root).add('package',pkg).then(function (response) {
                               $('#admin-lightbox-output').html(response);
                               $('#actionStatus').html(response+"\n\nDone!\n");
                           }).post();
                       }
                    },
                    compile: function (pkg,root,namespace) {
                       if (confirm('This action will compile the controllers for module '+root+'.\n\nAre you sure you wish to continue?')) {
                           var ao = new EasyAjax('/humble/utilities/compile');
                           ao.add('namespace',namespace);
                           ao.add('root',root);
                           ao.add('package',pkg);
                           $('#admin-lightbox').css('display','block');
                           $('#actionStatus').html('Working...');
                           ao.then(function (response) {
                               $('#actionStatus').html(response+"\n\nDone!\n");
                           });
                           ao.post();
                       }
                    },
                    clearcache: function (pkg,root,namespace) {
                       if (confirm('This action will clear the cache for the module.\n\nAre you sure you wish to continue?')) {
                            $('#admin-lightbox').css('display','block');
                            $('#actionStatus').html('Working...');
                           (new EasyAjax('/humble/utilities/clear')).add('namespace',namespace).add('root',root).add('package',pkg).then(function (response) {
                                $('#actionStatus').html(response+"\n\nDone!\n");
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
                        open: function (log) {
                            if (!Administration.logs.windows[log]) {
                                Administration.logs.windows[log] = Desktop.semaphore.checkout();
                            }
                            var win = Desktop.window.list[Administration.logs.windows[log]];
                            win._title(log+ ' Log | Humble')._open();
                            log = log.toLowerCase();
                            (new EasyAjax('/humble/admin/log')).add('log',log).add('window_id',win.id).then(function (response) {
                                win.set(response);
                            }).post();
                        },
                        clear: function (log) {
                            if (confirm('Clear the '+log.charAt(0).toUpperCase() + log.slice(1)+' log?')) {
                                (new EasyAjax('/humble/log/clearlog')).add('log',log).then(function (response) {
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