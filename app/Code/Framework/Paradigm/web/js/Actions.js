/*  --------------------------------------------------------------------
 *  Save, Load, New, and Generate actions
 *  --------------------------------------------------------------------*/
Paradigm.actions = (function () {
    //private vars
    var saveWindow          = false;
    var loadWindow          = false;
    var newDiagramWindow    = false;
    var generateWindow      = false;
    var manageWindow        = false;
    var diagramTitle        = '';
    var diagramDescription  = '';
    var currentDiagramId    = '';   //MySQL auto increment Id
    var mongoWorkflowId     = '';   //MongoDB id/name for the workflow
    var generatedWorkflowId = '';   //The Mongo Id this thing will be registered as
    var namespace           = '';
    var major_version       = 0;
    var minor_version       = 0;
    return {
        animate:  (function () {
            var timer       = null;
            var interval    = 15;
            var deg         = 0;
            var element     = null;
            return {
                run: function (thing) {
                    if (thing) {
                        element = $E(thing);
                    }
                    deg = (deg >= 360) ? 0 : deg+3;
                    element.style.transform = 'rotate('+deg+'deg)';
                    timer = window.setTimeout(Paradigm.actions.animate.run,interval)
                },
                stop: function () {
                    if (timer) {
                        window.clearTimeout(timer);
                    }
                },
                set: {
                    interval: function (arg) {
                        interval = arg;
                    },
                    degrees: function (arg) {
                        deg = arg;
                    }
                },
                get: {
                    interval: function () {
                        return interval;
                    },
                    degrees: function () {
                        return deg;
                    }
                }
            }
            })(),
        workflows: {
            manage: function () {
                let win = (manageWindow) ? manageWindow : (mangeWindow = Desktop.semaphore.checkout(true));
                win._static(true)._title('Manage Destinations');
                (new EasyAjax('/paradigm/workflow/exporthome')).add('window_id',win.id).then((response) => {
                    win._open(response);
                }).post();
            }
        },
        get: {
            majorVersion: function () {
                return major_version;
            },
            minorVersion: function () {
                return minor_version;
            },
            newDiagramWindow: function () {
                return newDiagramWindow;
            },
            loadWindow: function () {
                return loadWindow;
            },
            saveWindow: function () {
                return saveWindow;
            },
            currentDiagramId: function () {
                return currentDiagramId;
            },
            mongoWorkflowId: function () {
                return mongoWorkflowId;
            },
            diagramTitle: function () {
                return diagramTitle;
            },
            diagramDescription: function () {
                return diagramDescription;
            },
            namespace: function () {
                return namespace;
            },
            generatedWorkflowId: function () {
                return generatedWorkflowId;
            }
        },
        set: {
            majorVersion: function (id) {
                major_version = id;
            },
            minorVersion: function (id) {
                minor_version = id;
            },
            currentDiagramId: function (id) {
                console.log('I am setting the diagram id to '+id);
                currentDiagramId = id;
            },
            mongoWorkflowId: function (id) {
                mongoWorkflowId = id;
            },
            diagramTitle: function (title) {
                diagramTitle = title;
            },
            diagramDescription: function (desc) {
                diagramDescription = desc;
            },
            namespace: function (ns) {
                namespace = ns;
            },
            generatedWorkflowId: function (id) {
                generatedWorkflowId = id;
            }
        },
        new: function () {
            if (!newDiagramWindow) {
                newDiagramWindow = Desktop.semaphore.checkout();
                Desktop.window.list[newDiagramWindow]._title('New Workflow Diagram');
                Desktop.window.list[newDiagramWindow].open = function (stuff) {
                    //console.log(stuff);
                }
            }
            (new EasyAjax('/paradigm/diagram/new')).add('winId',newDiagramWindow).then((response) => {
                Desktop.window.list[newDiagramWindow]._open(response);
            }).post();
        },
        list: function () {
            if (!loadWindow) {
                loadWindow = Desktop.semaphore.checkout();
                Desktop.window.list[loadWindow]._title('Load Workflow');
                Desktop.window.list[loadWindow].open = function (stuff) {

                }
            }
            (new EasyAjax('/paradigm/workflow/list')).add('winId',loadWindow).then((response) => {
                Desktop.window.list[loadWindow]._open(response);
            }).post();
        },
        details: function () {
            if (!saveWindow) {
                saveWindow = Desktop.semaphore.checkout();
                Desktop.window.list[saveWindow]._title('Save');
                Desktop.window.list[saveWindow].open = function (stuff) {
                    console.log(stuff);
                }
            }
            (new EasyAjax('/paradigm/workflow/details')).add('id',Paradigm.actions.get.currentDiagramId()).add('winId',saveWindow).then((response) => {
                Desktop.window.list[saveWindow]._open(response);
            }).post();
        },
        save: function () {
            diagramTitle        = $('#shortdesc').val();
            diagramDescription  = $('#description').val();
            Paradigm.actions.set.diagramDescription(diagramDescription);
            if (diagramTitle && currentDiagramId) {
                (new EasyAjax('/paradigm/workflow/save')).add('major_version',Paradigm.actions.get.majorVersion()).add('minor_version',Paradigm.actions.get.minorVersion()).add('namespace',Paradigm.actions.get.namespace()).add('workflow',JSON.stringify(Paradigm.elements.list)).add('id',currentDiagramId).add('description',diagramDescription).add('title',diagramTitle).add('image',Paradigm.canvas.toDataURL()).then((response) => {
                    if (response) {
                        Paradigm.actions.set.currentDiagramId(response.trim());
                        Desktop.window.list[saveWindow]._close();
                        Paradigm.actions.lastAction('Saved');
                        Paradigm.console.reply("Saved.",'',1);
                    }
                }).post();
            }
        },
        lastAction: function (message) {
            $('#paradigm-last-action').html(message+' @ '+moment().format('h:mm:s a'));
        },
        quickSave: function () {
            (new EasyAjax('/paradigm/workflow/quicksave')).add('namespace',Paradigm.actions.get.namespace()).add('workflow',JSON.stringify(Paradigm.elements.list)).add('id',currentDiagramId).add('image',Paradigm.canvas.toDataURL()).then((response) => {
                if (response) {
                    currentDiagramId = response.trim();
                    Paradigm.actions.lastAction('Saved');
                    Paradigm.console.reply("Saved.",'',1);
                }
            }).post();
        },
        activate: function () {
            (new EasyAjax('/paradigm/workflow/activate')).add('id',Paradigm.actions.get.currentDiagramId()).then((response) => {
                $('#paradigm-quick-inactivate').css('visibility','visible').css('display','none');
                $('#paradigm-quick-activate').css('visibility','visible').css('display','block');
                Paradigm.console.add(response,'',1);
                Paradigm.actions.lastAction('Workflow Activated');
            }).post();
        },
        inactivate: function () {
            (new EasyAjax('/paradigm/workflow/inactivate')).add('id',Paradigm.actions.get.currentDiagramId()).then((response) => {
                $('#paradigm-quick-activate').css('visibility','visible').css('display','none');
                $('#paradigm-quick-inactivate').css('visibility','visible').css('display','block');
                Paradigm.console.add(response,'',1);
                Paradigm.actions.lastAction('Workflow Inactivated');
            }).post();
        },
        generate: function () {
            if (!generateWindow) {
                generateWindow = Desktop.semaphore.checkout();
            }
            Desktop.window.list[generateWindow]._open('Generating...');
            Paradigm.console.reply('Generating...','',1);
            (new EasyAjax('/paradigm/workflow/generate')).add('namespace',Paradigm.actions.get.namespace()).add('windowId',generateWindow).add('workflow',JSON.stringify(Paradigm.elements.list)).add('id',currentDiagramId).add('image',Paradigm.canvas.toDataURL()).then((response) => {
                Desktop.window.list[generateWindow]._title('Workflow Generation | Paradigm');
                Desktop.window.list[generateWindow].set(response);
            }).post();
        },
        delete: function () {
            if (confirm('Do you really want to delete this workflow?\n\n"'+Paradigm.actions.get.diagramTitle()+'"')) {
                (new EasyAjax('/paradigm/workflow/delete')).add('id',Paradigm.actions.get.currentDiagramId()).then((response) => {
                    window.location.reload();
                }).post();
            }
        },
        import: function () {
            var win = Desktop.semaphore.checkout(true);
            (new EasyAjax('/paradigm/workflow/import')).add('namespace',Paradigm.actions.get.namespace()).add('windowId',win.id).add('workflow',JSON.stringify(Paradigm.elements.list)).add('id',currentDiagramId).add('image',Paradigm.canvas.toDataURL()).then((response) => {
                win._title('Workflow Import | Paradigm');
                win._open(response);
            }).post();
        },
        export: function () {
            var win = Desktop.semaphore.checkout(true);
            (new EasyAjax('/paradigm/workflow/quicksave')).add('namespace',Paradigm.actions.get.namespace()).add('workflow',JSON.stringify(Paradigm.elements.list)).add('id',currentDiagramId).add('image',Paradigm.canvas.toDataURL()).then((response) => {
                if (response) {
                    currentDiagramId = response.trim();
                    Paradigm.console.reply("Saved prior to export.",'',1);
                    (new EasyAjax('/paradigm/workflow/exportlist')).add('windowId',win.id).add('id',currentDiagramId).then((response) => {
                        win._title('Workflow Export | Paradigm');
                        win._open(response);
                    }).post();
                }
            }).post();
        },
        sync: function () {
            var win = Desktop.semaphore.checkout(true);
            (new EasyAjax('/paradigm/workflow/synclist')).add('windowId',win.id).then((response) => {
                win._title('Workflow Sync | Paradigm');
                win._open(response);
            }).post();
        }
    }
})();