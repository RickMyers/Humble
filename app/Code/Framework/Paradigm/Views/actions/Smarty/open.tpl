<!--
 _____               _ _              _____         _
|  _  |___ ___ ___ _| |_|___ _____   |   __|___ ___|_|___ ___
|   __| .'|  _| .'| . | | . |     |  |   __|   | . | |   | -_|
|__|  |__,|_| |__,|___|_|_  |_|_|_|  |_____|_|_|_  |_|_|_|___|
                        |___|                  |___|
by Rick Myers.

Copyright humbleprogramming.com, all rights reserved

-->
<!DOCTYPE html>
<html>
    <head>
        <title>Paradigm | Humble Project</title>
        <link rel="stylesheet" type="text/css" href="/css/common" />
        <link rel="stylesheet" type="text/css" href="/css/features" />
        <link rel="stylesheet" type="text/css" href="/css/engine" />
        <link rel="stylesheet" type="text/css" href="/css/widgets" />
        <style type='text/css'>
            div {
               /* box-sizing: border-box*/
            }
            #humble-app-cssmenu_header ul,
            #humble-app-cssmenu_header li,
            #humble-app-cssmenu_header span,
            #humble-app-cssmenu_header a {
              margin: 0;
              padding: 0;
              position: relative;
            }
            #humble-app-cssmenu_header {
              height: 29px;
              border-radius: 0px 0px 0 0;
              background: #141414;
              background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAxCAIAAACUDVRzAAAAA3NCSVQICAjb4U/gAAAALElEQVQImWMwMrJi+v//PxMDw3+m//8ZoPR/qBgDEhuXGLoeYswhXg8R5gAAdVpfoJ3dB5oAAAAASUVORK5CYII=) 100% 100%;
              background: linear-gradient(to bottom, #32323a 0%, #141414 100%);
              border-bottom: 2px solid #0fa1e0;
            }
            #humble-app-cssmenu_header:after,
            #humble-app-cssmenu_header ul:after {
              content: '';
              display: block;
              clear: both;
            }
            #humble-app-cssmenu_header a {
              background: #141414;
              background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAxCAIAAACUDVRzAAAAA3NCSVQICAjb4U/gAAAALElEQVQImWMwMrJi+v//PxMDw3+m//8ZoPR/qBgDEhuXGLoeYswhXg8R5gAAdVpfoJ3dB5oAAAAASUVORK5CYII=) 100% 100%;
              background: linear-gradient(to bottom, #32323a 0%, #141414 100%);
              color: #ffffff;
              display: inline-block;
              font-family: Helvetica, Arial, Verdana, sans-serif;
              font-size: 12px;
              line-height: 29px;
              padding: 0 20px;
              text-decoration: none;
            }
            #humble-app-cssmenu_header ul {
              list-style: none;
            }
            #humble-app-cssmenu_header > ul {
              float: left;
            }
            #humble-app-cssmenu_header > ul > li {
              float: left;
            }
            #humble-app-cssmenu_header > ul > li:hover:after {
              content: '';
              display: block;
              width: 0;
              height: 0;
              position: absolute;
              left: 50%;
              bottom: 0;
              border-left: 10px solid transparent;
              border-right: 10px solid transparent;
              border-bottom: 10px solid #0fa1e0;
              margin-left: -10px;
            }
            #humble-app-cssmenu_header > ul > li:first-child > a {
              border-radius: 5px 0 0 0;
            }
            #humble-app-cssmenu_header > ul > li:last-child > a {
              border-radius: 0 5px 0 0;
            }
            #humble-app-cssmenu_header > ul > li.active a {
              box-shadow: inset 0 0 3px #000000;
              background: #070707;
              background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAxCAIAAACUDVRzAAAAA3NCSVQICAjb4U/gAAAALklEQVQImWNQU9Nh+v//PxMDw3+m//8ZkNj/mRgYIHxy5f//Z0BSi18e2TwS5QG4MGB54HL+mAAAAABJRU5ErkJggg==) 100% 100%;
              background: linear-gradient(to bottom, #26262c 0%, #070707 100%);
            }
            #humble-app-cssmenu_header > ul > li:hover > a {
              background: #070707;
              background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAxCAIAAACUDVRzAAAAA3NCSVQICAjb4U/gAAAALklEQVQImWNQU9Nh+v//PxMDw3+m//8ZkNj/mRgYIHxy5f//Z0BSi18e2TwS5QG4MGB54HL+mAAAAABJRU5ErkJggg==) 100% 100%;
              background: linear-gradient(to bottom, #26262c 0%, #070707 100%);
              box-shadow: inset 0 0 3px #000000;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub {
              z-index: 1;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub:hover > ul {
              display: block;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub ul {
              display: none;
              position: absolute;
              width: 200px;
              top: 100%;
              left: 0;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub ul li {
              margin-bottom: -1px;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub ul li a {
              background: #0fa1e0;
              border-bottom: 1px dotted #6fc7ec;
              filter: none;
              font-size: 11px;
              display: block;
              line-height: 120%;
              padding: 10px;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub ul li:hover a {
              background: #0c7fb0;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub .humble-menu-has-sub:hover > ul {
              display: block;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub .humble-menu-has-sub ul {
              display: none;
              position: absolute;
              left: 100%;
              top: 0;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub .humble-menu-has-sub ul li a {
              background: #0c7fb0;
              border-bottom: 1px dotted #6db2d0;
            }
            #humble-app-cssmenu_header .humble-menu-has-sub .humble-menu-has-sub ul li a:hover {
              background: #095c80;
            }
            #lightbox {
                position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background-color: rgba(0,0,0,.4); z-index: 999; display: none
            }
            #new-element-prompt {
                width: 600px; height: 300px; padding: 10px; border: 1px solid #cecece; border-radius: 10px; margin-left: auto; margin-right: auto; background-color: white
            }
            .paradigm-quick-icon {
                height: 28px; visibility: hidden; cursor: pointer
            }
        </style>
        <script type="module" sync>
            import { Desktop } from '/mjs/paradigm/DesktopModule.js';
            window.Desktop = Desktop;
        </script>            
        <script type="text/javascript" src="/js/humble-jquery"></script>
        <script type="text/javascript" src="/js/common"></script>
    
        <script type="text/javascript">
            var UseTranparentWindows = 'N';
            var ace_editors          = [];

        </script>
        <script type="text/javascript" src="/js/engine"></script>
        <script type="text/javascript" src="/js/widgets"></script>
        <script type="text/javascript" src="/web/js/Colorizer.js"></script>
        <script type="text/javascript" src="/web/js/ace/ace.js"></script>
        <script type="text/javascript" src="/web/js/ckeditor4/ckeditor.js"></script>
        <script type="text/javascript">

        {assign var=tab_id value=$system->browserTabId()}
        EasyAjax.always.add('browser_tab_id','{$tab_id}')
        EasyAjax.always.add('csrf_buster','{$system->csrfBuster($tab_id)}');
        Form.set.defaultURL('/workflow/elements/save');
        var Workflows = (function () {
            var available = { };
            var diagram   = false;
            return {
                active: false,
                baseWidth: 1500,
                baseHeight: 890,
                snap: false,
                controls: false,
                
                activeDiagram: () => {
                    return diagram.id;
                },
                snapToGrid: function (cb) {
                    Workflows.snap = cb.checked;
                },
                stop: function (evt) {
                    evt = (evt) ? evt : event;
                    if (window.addEventListener) {
                        evt.stopPropagation();
                    } else {
                        evt.cancelBubble = true;
                    }
                },
                add: function (id,namespace,title,description,version,saved,modified,generated,firstname,lastname,preview) {
                    available[id] = {
                        "id": id,
                        "namespace": namespace,
                        "title": title,
                        "description": description,
                        "version": version,
                        "saved": saved,
                        "modified": modified,
                        "generated": generated,
                        "firstname": firstname,
                        "lastname": lastname,
                        "preview": preview
                    }
                },
                remove: function (workflow_id) {
                    Desktop.stopPropagation(window.event);
                    if (confirm('Are you sure you want to delete that workflow?')) {
                        (new EasyAjax('/paradigm/workflow/delete')).add('id',workflow_id).then(function (response){
                            Workflows.fetch($('#workflow-namespace-list').val(),1);
                        }).post();
                    }
                },
                checkForEnter: function (evt) {
                    evt = (evt) ? evt : (event ? window.event : null);
                    if (evt.keyCode == 13) {
                        Workflows.addElement();
                    }
                },
                updateText: function (evt) {
                    evt = (evt) ? evt : (event ? window.event : null);
                    if (Paradigm.lastElement !== false) {
                        var element         = Paradigm.elements.list[Paradigm.lastElement];
                        element.text        = $('#elementText').val();
                        element.lines.startX= false;
                        Paradigm.calculateText(element,14,'Arial');
                        Paradigm.redraw();
                    }
                },
                updateLabel: function (evt) {
                    evt = (evt) ? evt : (event ? window.event : null);
                    if (Paradigm.lastElement !== false) {
                        var element         = Paradigm.elements.list[Paradigm.lastElement];
                        element.label       = $('#elementLabel').val();
                        Paradigm.redraw();
                    }
                },
                addElement: function () {
                    switch ($('#new-element-type').val()) {
                        case    "decision"  :
                            Paradigm.elements.decision.add($('#new-element-text').val());
                            break;
                        case    "process"  :
                            Paradigm.elements.process.add($('#new-element-text').val());
                            break;
                        case    "adapter"  :
                            Paradigm.elements.adapter.add($('#new-element-text').val());
                            break;                            
                        case    "file"  :
                            Paradigm.elements.file.add($('#new-element-text').val());
                            break;                            
                        case    "actor"  :
                            Paradigm.elements.actor.add($('#new-element-text').val());
                            break;
                        case    "system"  :
                            Paradigm.elements.system.add($('#new-element-text').val());
                            break;
                        case    "webservice"  :
                            Paradigm.elements.webservice.add($('#new-element-text').val());
                            break;
                        case    "webhook"  :
                            Paradigm.elements.webhook.add($('#new-element-text').val());
                            break;                            
                        case    "sensor"  :
                            Paradigm.elements.sensor.add($('#new-element-text').val());
                            break;
                        case    "trigger"  :
                            Paradigm.elements.trigger.add($('#new-element-text').val());
                            break;
                        case    "detector"  :
                            Paradigm.elements.detector.add($('#new-element-text').val());
                            break;
                        case    "exception"  :
                            Paradigm.elements.exception.add($('#new-element-text').val());
                            break;
                        case    "external"  :
                            Paradigm.elements.external.add($('#new-element-text').val());
                            break;
                        case    "report"  :
                            Paradigm.elements.report.add($('#new-element-text').val());
                            break;
                        case    "operation"  :
                            Paradigm.elements.operation.add($('#new-element-text').val());
                            break;
                        case    "alerts"  :
                            Paradigm.elements.alerts.add($('#new-element-text').val());
                            break;
                        case    "input"  :
                            Paradigm.elements.input.add($('#new-element-text').val());
                            break;
                        case    "rule"  :
                            Paradigm.elements.rule.add($('#new-element-text').val());
                            break;
                        default     :
                            alert("WTF?: "+$('#new-element-type').val())
                            break;
                    }
                    $("#lightbox").fadeOut();
                },
                prompt: function (whichOne) {
                    if (Workflows.active) {
                        if (Paradigm.prompts[whichOne]) {
                            var data = Paradigm.prompts[whichOne];
                            $('#lightbox').fadeIn();
                            $('#element-prompt').html(Workflows.promptTemplate.replace(/&TITLE&/g,data.title).replace(/&IMAGE&/,data.image).replace(/&DESCRIPTION&/,data.description));
                            $('#new-element-type').val(whichOne);
                            window.setTimeout(function () { $('#new-element-text').focus(); },300);
                        } else {
                            alert(whichOne);
                        }
                    }
                },
                promptTemplate: "<div style='background-color: navy; padding: 5px 0px 5px 5px; color: white; height: 100%'>&TITLE&</div>"+
                                "<br /><br /><img src='&IMAGE&' style='float: left; margin-right: 10px; max-height: 100px' /> <br /><br />"+
                                "&DESCRIPTION&<br /><br />" +
                                "<div style='clear: both'>"+
                                "<br /><br /><input onkeydown='Workflows.checkForEnter(event)' type='text' name='new-element-text' id='new-element-text' style='background-color: lightcyan; width: 420px; border: 1px solid #aaf; padding: 3px; border-radius: 3px' />"+
                                "<div style='clear: both'></div>"+
                                "<div style='font-family: monospace; font-size: .8em; letter-spacing: 2px'> Text for new element</div>"+
                                "<input type='hidden' name='new-element-type' id='new-element-type' />"+
                                "</div>",
                detail: function (index) {
                    $E('workflow-preview').style.width='100%';
                    $E('workflow-preview').src = (available[index].preview) ? (available[index].preview) : '/images/paradigm/clipart/not_available.jpg';
                    $E('workflow-title').innerHTML = available[index].title;
                    $E('workflow-version').innerHTML = available[index].version;
                    $E('workflow-creator').innerHTML = available[index].firstname+' '+available[index].lastname;
                    $E('workflow-saved').innerHTML = available[index].saved;
                    $E('workflow-modified').innerHTML = available[index].modified;
                    $E('workflow-generated').innerHTML = available[index].generated;
                    $E('workflow-description').innerHTML = available[index].description;
                },
                clear: function () {
                    available = []; //clears the array
                },
                enable: function () {
                    Workflows.active = true;
                    $('.insert_glyph').prop('disabled',false).css('color','white');
                    $('.insert_glyph').css('color','white');
                    $('.insert_glyph span:first-child').css('opacity','1');
                    $('#paradigm-save-option').prop('disabled',false).css('color','white');
                    $('#paradigm-generate-option').prop('disabled',false).css('color','white');
                    $('#paradigm-delete-option').prop('disabled',false).css('color','white');
                    $('#paradigm-import-option').prop('disabled',false).css('color','white');
                    $('#paradigm-export-option').prop('disabled',false).css('color','white');
                    $('#paradigm-print-option').prop('disabled',false).css('color','white');
                    $('#paradigm-workflow-components').css('visibility','visible');
                },
                load: function (id,namespace) {
                    Paradigm.actions.set.currentDiagramId(id);
                    Paradigm.actions.set.namespace(namespace);
                    (new EasyAjax('/paradigm/workflow/load')).add('id',id).then((response) => {
                        diagram = JSON.parse(response);
                        diagram.workflow = (diagram.workflow) ? JSON.parse(diagram.workflow) : { };
                        for (var i in diagram.workflow) {
                               diagram.workflow[i].isClosed = Paradigm.closures(diagram.workflow[i]);
                               Paradigm.objects[diagram.workflow[i].id] = diagram.workflow[i]
                               if (typeof diagram.workflow[i].fillStyle == "object") {
                                   diagram.workflow[i].fillStyle = Paradigm.gradient(diagram.workflow[i].X,diagram.workflow[i].Y,diagram.workflow[i].W,diagram.workflow[i].H);
                               }
                        }
                        //console.log(diagram);
                        Paradigm.elements.list = diagram.workflow;
                        Paradigm.actions.set.generatedWorkflowId(diagram.generated_workflow_id);
                        Paradigm.actions.set.mongoWorkflowId(diagram.workflow_id);
                        Paradigm.actions.set.majorVersion(diagram.major_version);
                        Paradigm.actions.set.minorVersion(diagram.minor_version);
                        Paradigm.actions.set.diagramTitle(diagram.title);
                        Paradigm.actions.set.diagramDescription(diagram.description);
                        if (!Workflows.controls) {
                            $('#paradigm-quick-save').on("click",Paradigm.actions.details).css('visibility','visible').fadeIn();
                            $('#paradigm-quick-generate').on("click",Paradigm.actions.generate).css('visibility','visible').fadeIn();
                            $('#paradigm-quick-activate').on("click",Paradigm.actions.inactivate);
                            $('#paradigm-quick-inactivate').on("click",Paradigm.actions.activate);
                            if (diagram.active=='Y') {
                                $('#paradigm-quick-activate').css('visibility','visible');
                            } else {
                                $('#paradigm-quick-activate').css('display','none');
                                $('#paradigm-quick-inactivate').css('display','block').css('visibility','visible').fadeIn();
                            }
                            Workflows.controls = true;
                        }
                        (diagram.generated_workflow_id ? $('#generated-icon').css('visibility','visible') : $('#generated-icon').css('visibility','hidden') );
                        Desktop.window.list[Paradigm.actions.get.loadWindow()]._close();
                        Paradigm.redraw();
                        Workflows.enable();
                        if (diagram.active === 'Y') {
                            $('#paradigm-quick-inactivate').css('visibility','visible').css('display','none');
                            $('#paradigm-quick-activate').css('visibility','visible').css('display','block');
                            Paradigm.console.add('This workflow is active','',1);
                        } else {
                            $('#paradigm-quick-inactivate').css('visibility','visible').css('display','block');
                            $('#paradigm-quick-activate').css('visibility','visible').css('display','none');
                            Paradigm.console.add('This workflow is inactive','',1);
                        }
                    }).post();
                },
                fetch: function (ns,page) {
                    page = (page) ? page : 1;
                    if (ns) {
                        Workflows.clear();
                        (new EasyAjax('/paradigm/workflow/inventory')).add('page',page).add('namespace',ns).then((response) => {
                            var workflows = eval('('+response+')');
                            var workflow;
                            var data = workflows.data;
                            Workflows.pagination.pages = +data.pages;
                            Workflows.pagination.page  = +data.page;
                            Workflows.pagination.rows  = +data.rows;
                            Workflows.pagination.from  = +data.fromRow;
                            Workflows.pagination.to    = +data.toRow;
                            $('#workflow-open-page-number').val(data.page+' of '+data.pages);
                            for (var i in workflows.diagrams) {
                                workflow = workflows.diagrams[i];
                                Workflows.add(workflow.id,
                                    workflow.namespace,
                                    workflow.title,
                                    workflow.description,
                                    workflow.major_version+'.'+workflow.minor_version,
                                    workflow.saved,
                                    workflow.modified,
                                    workflow.generated,
                                    workflow.first_name,
                                    workflow.last_name,
                                    workflow.image
                                );
                            }
                            Workflows.render();
                        }).post();
                    }
                },
                render: function () {
                    var HTML = '';
                    for (var i in available) {
                        HTML +='<div onclick="Workflows.load('+available[i].id+',\''+available[i].namespace+'\')" onmouseover="Workflows.detail(\''+available[i].id+'\'); this.style.backgroundColor=\'#f1f1f1\'" onmouseout="this.style.backgroundColor=\'white\';" style="cursor: pointer; padding: 10px 5px; border-bottom: 1px solid #888">';
                        HTML += available[i].title+'<div style="float: right; margin-top: 10px; font-size: .8em; cursor: pointer; letter-spacing: 2px; color: #c33" onclick="Workflows.remove('+available[i].id+'); return false">delete</div></div>';
                    }
                    $('#available-workflows').html(HTML);
                },
                pagination: {
                    pages: 0,
                    page: 0,
                    rows: 0,
                    from: 0,
                    to: 0
                },
                previous: function () {
                    if (Workflows.pagination.page > 1) {
                        Workflows.pagination.page = Workflows.pagination.page-1;
                        Workflows.fetch(Paradigm.actions.get.namespace(),Workflows.pagination.page);
                    }
                },
                next: function () {
                    if (Workflows.pagination.page < Workflows.pagination.pages) {
                        Workflows.pagination.page = Workflows.pagination.page+1;
                        Workflows.fetch(Paradigm.actions.get.namespace(),Workflows.pagination.page);
                    }
                },
                first: function () {
                    Workflows.fetch(Paradigm.actions.get.namespace(),1);
                },
                last: function () {
                    Workflows.fetch(Paradigm.actions.get.namespace(),Workflows.pagination.pages);
                },
                component: {
                    load: function (element,data) {
                    },
                    save: function (element) {
                        while (element.parentNode && (element.parentNode.nodeName != 'FORM')) {
                            element=element.parentNode;
                        }
                        if (element.parentNode.nodeName == 'FORM') {
                            var form = element.parentNode;
                            alert($('#threshold-configuration').val());
                            var x = JSON.stringify(form.elements);
                        }
                    }
                },
                timeout: null
            }
        })();
        topctr = 0;
        $(document).ready( () => {
            //Desktop.on($E('canvas-container'),'keydown',Paradigm.remove);
            Desktop.init();
            Paradigm.container = $E('canvas-container');
            Desktop.on(document,'keydown',Paradigm.remove);
            $('.insert_glyph').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('.insert_glyph span:first-child').css('opacity','.3');
            $('#paradigm-save-option').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('#paradigm-generate-option').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('#paradigm-delete-option').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('#paradigm-print-option').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('#paradigm-import-option').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('#paradigm-export-option').prop('disabled',true).css('color','rgba(90,90,90,.3)');
            $('#lightbox').height(window.innerHeight);
            //$('#paradigm-quick-generate').on('click',Paradigm.actions.generate);
            $(window).resize(Paradigm.desktop.resize).resize();
            window.setTimeout(function () {
                $(Paradigm.canvas).prop('width',$(Paradigm.container).width()*2);
                $(Paradigm.canvas).prop('height',$(Paradigm.container).height()*2);
                Paradigm.console.initialize();
            },1200);
        });
        </script>
    </head>
    <body style='margin: 0px; padding: 0px; overflow: hidden; '>
        <div style='position: absolute; visibility: hidden; top: -100px; left: -1000px; float: left;' id='hiddenSizingLayer'></div>
        <div id='lightbox' class='lightbox'>
            <table style='width: 100%; height: 100%'>
                <tr>
                    <td>
                        <div id='new-element-prompt' class='new-element-prompt' style="position: relative">
                            <div id='element-prompt' style='position: relative'>

                            </div>
                            <div style="bottom: 0px; width: 100%; position: absolute">
                                <input type='button' value="  Ok  "  onclick="Workflows.addElement()" style="float: right; margin-right: 15px"/>
                                <input type='button' onclick="$('#lightbox').fadeOut()" value=" Cancel " />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div id="paradigm-virtual-desktop" style="width: 100%; height: 100%">
            <div id="paradigm-header" style='position: relative; box-sizing: border-box; white-space: nowrap'>
                <div style='display: block; box-sizing: border-box; padding-left: 15px; background-color: #e3e3e3; height: 100%; border-radius: 0px 0px 0px 0px; min-width: 1000px; width: 100%;  '>
                    <div style="float: left;">
                        <img src='/images/humble/djikstra.png' align="top" style=' float: left; height: 76px; margin-right: 240px' />
                        <div style="font-size: 3em; color: #333; letter-spacing: 8px; position: absolute; top: 0px; left: 75px; text-shadow: -1px 1px 1px #5A5A5A;">HUMBLE</div>
                        <div style="font-size: 1em; color: #555; letter-spacing: 6px; position: absolute; top: 55px; left: 80px; text-shadow: -1px 1px 1px #5A5A5A;">Workflow Editor</div>
                    </div>
                    <div style='display: inline-block; margin-left: auto; margin-right: auto; visibility: hidden' id="paradigm-workflow-components">
                    <fieldset style="display: inline-block; position: relative; top: 2px; margin-left: 20px; padding: 0px 20px 0px 20px; padding: 0px; border-radius: 4px; font-family: sans-serif; font-size: .8em">
                        <legend style="font-family: sans-serif; font-size: .6em">Triggers</legend>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px" title='A recurring event, based on time'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/cron.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('system')" /><br />
                            Time
                        </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px" title='When somebody does something, such as form submission'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/person1.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('actor')" /><br />
                            Actor
                        </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px;" title='An exposed webservice has been called'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/webservice2.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('webservice')" /><br />
                            Service
                        </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px;" title='A Reverse API (WebHook) Call'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/webhook_icon.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('webhook')" /><br />
                            WebHook
                        </div>                        
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px;" title='A controller action created the event'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/event3.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('trigger')" /><br />
                            Action
                        </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-right: 20px" title='A file arrives in a directory'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/file_trigger.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('file')" /><br />
                            File
                        </div>                        
                        <!--div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-right: 20px" title='Triggered by the presence of specific data in a request'>
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/sensor2.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('sensor')" /><br />
                            Sensor
                        </div-->
                    </fieldset>

                    <fieldset style="display: inline-block; position: relative;  top: 2px; margin-left: 20px; padding: 0px 20px 0px 20px; padding: 0px; border-radius: 4px; font-family: sans-serif; font-size: .8em">
                        <legend style="font-family: sans-serif; font-size: .6em">Connectors</legend>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; ">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/flowchart-arrow.gif' onclick="Paradigm.elements.connector.add()" style='height: 40px; cursor: pointer' /><br />
                                Internal
                        </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                                <img class='flowchartGlyph' src='/images/paradigm/clipart/external.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('external')"/><br />
                                External
                        </div>
                    </fieldset>

                    <fieldset style="display: inline-block; white-space: nowrap;  position: relative; top: 2px; margin-left: 20px; padding: 0px 40px 0px 20px; border-radius: 4px; font-family: sans-serif; font-size: .8em">
                        <legend style="font-family: sans-serif; font-size: .6em">Components</legend>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/process.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('process')" /><br />
                            Process
                       </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/jsadapter.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('adapter')" /><br />
                            Adapter
                       </div>                        
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/decision.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('decision')" /><br />
                            Decision
                        </div>
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/business-rule.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('rule')" /><br />
                            Rule
                        </div>
                        <!--div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/detector2.png' style=' height: 40px; cursor: pointer' onclick="Workflows.prompt('detector')" /><br />
                            Detector
                        </div-->
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/event.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('alerts')"  /><br />
                            Notification
                        </div>
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/exception.png' style=' height: 40px; cursor: pointer' onclick="Workflows.prompt('exception')" /><br />
                            Exception
                        </div>
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/data.png' style='height: 40px; cursor: pointer'  onclick="Workflows.prompt('report')"/><br />
                            Report
                        </div>
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/manual_operation.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('operation')"/><br />
                            Program
                        </div>
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/manual_input.png' style='height: 40px; width: 60px; cursor: pointer' onclick="Workflows.prompt('input')" /><br />
                            File Input
                        </div>
                        <div style="float: left; width: 60px; height: 60px; text-align: center; font-size: .7em;  margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/terminus.png' style='height: 30px; margin-top: 15px; width: 60px; cursor: pointer'  onclick="Paradigm.elements.terminus.add()" /><br />
                            Terminus
                        </div>
                        <div style="clear: both"></div>
                    </fieldset>
                    </div>
                    <div style="clear: both"></div>
                </div>
            </div>
            <div id="paradigm-menu" style='position: relative; box-sizing: border-box; white-space: nowrap'>
                <div style="float: right; text-align: right; width: 100%; background-color: #e3e3e3; position: relative ">
                        <div id="humble-app-header" style='white-space: nowrap; position: relative'>
                            <form name="paradigm-controls-form" id="paradigm-controls-form" style='white-space: nowrap; position: relative'>
                            <div id='humble-app-cssmenu_header' style='white-space: nowrap; position: relative'>

                                <ul style='white-space: nowrap; position: relative'>
                                   <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Actions</span></a>
                                      <ul>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="Paradigm.actions.new(); return false"><span>New</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="Paradigm.actions.list(); return false"><span>Open</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a id='paradigm-save-option' disabled='disabled' href='#' onclick="Paradigm.actions.details(); return false"><span>Save</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a id='paradigm-generate-option'  disabled='disabled' href='#' onclick="Paradigm.actions.generate(); return false"><span>Generate</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a id='paradigm-delete-option'  disabled='disabled' href='#' onclick="Paradigm.actions.delete(); return false"><span>Delete</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span><hr /></span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Workflow...</span></a>
                                             <ul>
                                                <li class='humble-menu-has-sub '><a id='paradigm-import-option'  disabled='disabled' href='#' onclick="Paradigm.actions.import(); return false"><span>Import</span></a>
                                                </li>
                                                <li class='humble-menu-has-sub '><a id='paradigm-export-option'  disabled='disabled' href='#' onclick="Paradigm.actions.export(); return false"><span>Export</span></a>
                                                </li>
                                                {if ($permissions->getAdmin() == "Y")}
                                                <li class='humble-menu-has-sub '><a href='#' onclick="Paradigm.actions.sync(); return false"><span>Sync</span></a>
                                                </li>
                                                <li class='humble-menu-has-sub '><a href='#' onclick="Paradigm.actions.workflows.manage(); return false"><span>Manage...</span></a>
                                                </li>                                                
                                                {/if}
                                            </ul>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span><hr /></span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a id='paradigm-print-option'  disabled='disabled' href='#' onclick="return false"><span>Print</span></a>
                                         </li>
                                      </ul>
                                   </li>
                                   <li class='humble-menu-has-sub '><a  href='#' onclick="return false"><span>Insert</span></a>
                                       <ul>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick=";return false"><span><img src='/images/paradigm/clipart/flowchart-arrow.gif' style='height: 16px; float: right; margin-right: 2px' />Connector</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick=";return false"><span><img src='/images/paradigm/clipart/connector.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px; opacity: inherit' />Start</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('system');return false"><span><img src='/images/paradigm/clipart/cron.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />System</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('actor');return false"><span><img src='/images/paradigm/clipart/person1.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Actor</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('file');return false"><span><img src='/images/paradigm/clipart/file_trigger.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />File Trigger</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('webhook');return false"><span><img src='/images/paradigm/clipart/webhook.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />WebHook</span></a>
                                        </li>                                        
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('process');return false"><span><img src='/images/paradigm/clipart/process.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Process</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('decision');return false"><span><img src='/images/paradigm/clipart/decision.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Decision</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('rule');return false"><span><img src='/images/paradigm/clipart/business-rule.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Business Rule</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('sensor');return false"><span><img src='/images/paradigm/clipart/sensor2.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Sensor</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('detector');return false"><span><img src='/images/paradigm/clipart/detector2.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Detector</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('alerts');return false"><span><img src='/images/paradigm/clipart/event.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Notification/Alert</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('external');return false"><span><img src='/images/paradigm/clipart/external.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Service</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('report');return false"><span><img src='/images/paradigm/clipart/data.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Report</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('operation');return false"><span><img src='/images/paradigm/clipart/manual_operation.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />External Program</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Workflows.prompt('input');return false"><span><img src='/images/paradigm/clipart/manual_input.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />File Input</span></a>
                                        </li>
                                        <li class='humble-menu-has-sub'><a class='insert_glyph' href='#' onclick="Paradigm.elements.terminus.add();return false"><span><img src='/images/paradigm/clipart/terminus.png' style='height: 16px; float: right; margin-left: 5px; margin-right: 2px' />Terminus</span></a>
                                        </li>
                                       </ul>
                                   </li>
                                   <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Console</span></a>
                                      <ul>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="Paradigm.console.view(); Paradigm.console.resize(); return false"><span>View</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span><hr /></span></a></li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Activate</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Deactivate</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Generate</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Commands...</span></a>
                                         </li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span><hr /></span></a></li>
                                         <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Hide</span></a>
                                         </li>
                                      </ul>
                                   </li>
                                   <li><img class="paradigm-quick-icon" id="paradigm-quick-save" src="/images/paradigm/clipart/save-icon.png" style="height: 28px;" /></li>
                                   <li><img class="paradigm-quick-icon" id="paradigm-quick-generate" src="/images/paradigm/clipart/generate-icon.png" style="height: 28px" /></li>
                                   <li><img class="paradigm-quick-icon" title="This workflow is active, click to make inactivate" id="paradigm-quick-activate" src="/images/paradigm/clipart/power_button_on.png" style="height: 28px;" /><img title="This workflow is inactive.  Click to make active" class="paradigm-quick-icon" id="paradigm-quick-inactivate" src="/images/paradigm/clipart/power_button_off.png" style="height: 28px; display: none" /></li>
                                   <li><a draggable='false' href='#' style=' text-align: right' onclick="return false"><span><img title="This Workflow Has Been Generated" id="generated-icon" style='visibility: hidden; position: relative; top: 3px;' height="16" src="/images/paradigm/clipart/green-check.png" /></span></a></li>
                                   <li><a draggable='false' href='#' onclick="return false"><table cellspacing='0' cellpadding='0' border='0'><tr><td>x:</td><td id='mouseX' style='text-align: right; width: 25px; font-size: .8em;'>0</td><td style='padding-left: 5px'>y:</td><td id='mouseY' style='text-align: right;  width: 25px; font-size: .8em;'>0</td></tr></table></a></li>
                                   <li><a draggable='false' href='#' onclick="return false">
                                           <table cellspacing='0' cellpadding='0' border='0'>
                                               <tr>
                                                   <td style='color: #f00'>Selected</td><td style='padding-left: 5px'>x:</td><td id='elementX' style='width: 25px; font-size: .8em; text-align: right' ></td>
                                                   <td style='padding-left: 5px'>y:</td><td id='elementY' style='width: 25px; font-size: .8em; text-align: right'></td>
                                                   <td style='padding-left: 5px'>z:</td><td id='elementZ' style='width: 20px; font-size: .8em; text-align: right'></td>
                                               </tr>
                                           </table></a></li>
                                   <li><a draggable='false' href='#' onclick="return false"><span>Label: <input onkeyup="Workflows.updateLabel(event)" onkeydown='Workflows.stop(event)' onkeypress='Workflows.stop(event)' type='text' id='elementLabel' name="elementLabel" style='width: 130px; padding: 2px 3px; border: 0px; font-size: .8em; ' /></span></a></li>
                                   <li><a draggable='false' href='#' onclick="return false"><span>Text: <input onkeyup="Workflows.updateText(event)" onkeydown='Workflows.stop(event)' type='text' id='elementText' name="elementText" style='width: 130px; padding: 2px 3px; border: 0px; font-size: .8em; ' /></span></a></li>
                                   <li><a draggable='false' href='#' style='text-align: right' onclick="return false"><span>
                                       <table cellspacing="5">
                                           <tr>
                                               <td><div onclick="Workflows.size(1)" style="height: 12px; width: 26px; border: 1px solid white; cursor: pointer"></div></td>
                                               <td><div onclick="Workflows.size(2)" style="height: 18px; width: 26px; border: 1px solid white; cursor: pointer"></div></td>
                                               <td><div onclick="Workflows.size(4)" style="height: 24px; width: 26px; border: 1px solid white; cursor: pointer"></div></td>
                                           </tr>
                                       </table></span></a>
                                    </li>
                                    <li><a nohref><span><input type="checkbox" onclick="Workflows.snapToGrid(this)" name="paradigm-snap-to-grid" id="paradigm-snap-to-grid" /> Snap</span></a></li>
                                </ul>

                              </div>
                            </form>
                        </div>
                    </div>
                <div style="clear: both"></div>
            </div>
            <div id="paradigm-content" style='position: relative; box-sizing: border-box'>
                <div style="position: relative; overflow: auto; width: 100%; height: 100%; box-sizing: border-box" tabindex='99' id='canvas-container'>
                    <canvas style=" background-image: url(/images/paradigm/bg_graph.png); display: block" id="canvas"></canvas>
                </div>
            </div>
            <div style='font-family: sans-serif; background-color: #333; color: white; font-size: 10px; box-sizing: border-box; position: relative' id="paradigm-footer">
                <div style='float: right; margin-right: 10px; color: ghostwhite' id='paradigm-last-action'>
                </div>
                &copy; Humble Project, 2007-present
            </div>
        </div>
    </body>

</html>