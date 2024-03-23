<!DOCTYPE html>
<html>
    <head>
        <title>Paradigm Research | Humble Project</title>
        <link rel="stylesheet" type="text/css" href="/css/common" />
        <link rel="stylesheet" type="text/css" href="/css/features" />
        <link rel="stylesheet" type="text/css" href="/css/engine" />
        <link rel="stylesheet" type="text/css" href="/css/desktop" />
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
;
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
        <script type="text/javascript" src="/js/humble-jquery"></script>
        <script type="text/javascript" src="/js/common"></script>
        <script type="text/javascript">
            var UseTranparentWindows = 'N';
            var useTranparentWindows = 'N'
        </script>
        <script type="text/javascript" src="/js/desktop"></script>
        <script type="text/javascript" src="/js/research"></script>
        <script type="text/javascript" src="/js/widgets"></script>
        <script type="text/javascript">
        var Workflows = (function () {
            var available = { };
            return {
                active: false,
                baseWidth: 1500,
                baseHeight: 890,
                snap: false,
                controls: false,
                timeout: null
            }
        })();
        topctr = 0;
        $(document).ready(function () {
            Desktop.on($E('canvas-container'),'keydown',Paradigm.remove);
            $(window).resize(Paradigm.desktop.resize).resize();
            $(Paradigm.canvas).prop('width',$(Paradigm.container).width()*2);
            $(Paradigm.canvas).prop('height',$(Paradigm.container).height()*2);
        });
        </script>
    </head>
    <body style='margin: 0px; padding: 0px; overflow: hidden; '>
        <div style='position: absolute; visibility: hidden; top: -100px; left: -1000px; float: left;' id='hiddenSizingLayer'></div>
        <div id="paradigm-virtual-desktop" style="width: 100%; height: 100%">
            <div id="paradigm-header" style='position: relative; box-sizing: border-box; white-space: nowrap'>
                <div style='display: block; box-sizing: border-box; padding-left: 15px; background-color: #e3e3e3; height: 100%; border-radius: 0px 0px 0px 0px; min-width: 1000px; width: 100%;  '>
                    <div style="float: left;">
                        <img src='/images/humble/djikstra.png' align="top" style=' float: left; height: 76px; margin-right: 180px' />
                        <div style="font-size: 1em; letter-spacing: 8px; position: absolute; top: 0px; left: 70px; text-shadow: -1px 1px 1px #5A5A5A;">Workflow Editor</div>
                    </div>
                    <div style='display: inline-block; margin-left: auto; margin-right: auto; visibility: visible' id="paradigm-workflow-components">
                    <fieldset style="display: inline-block; margin-left: 20px; padding: 0px 20px 0px 20px; padding: 0px; border-radius: 4px; font-family: sans-serif; font-size: .8em">
                        <legend style="font-family: sans-serif; font-size: .6em">Triggers</legend>
                        <a href="#" onclick="Paradigm.elements.actor.add('stuff');return false">Add Image</a>
                    </fieldset>

                    <fieldset style="display: inline-block; margin-left: 20px; padding: 0px 20px 0px 20px; padding: 0px; border-radius: 4px; font-family: sans-serif; font-size: .8em">
                        <legend style="font-family: sans-serif; font-size: .6em">Connectors</legend>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                            <img class='flowchartGlyph' src='/images/paradigm/clipart/flowchart-arrow.gif' onclick="Paradigm.elements.connector.add()" style='height: 40px; cursor: pointer' /><br />
                                Internal
                        </div>
                        <div style="float: left; width: 55px; height: 60px; text-align: center; font-size: .7em; padding-top: 5px; margin-left: 20px">
                                <img class='flowchartGlyph' src='/images/paradigm/clipart/external.png' style='height: 40px; cursor: pointer' onclick="Workflows.prompt('external')"/><br />
                                External
                        </div>
                    </fieldset>

                    <fieldset style="display: inline-block; margin-left: 20px; padding: 0px 40px 0px 20px; border-radius: 4px; font-family: sans-serif; font-size: .8em">
                        <legend style="font-family: sans-serif; font-size: .6em">Components</legend>
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
                                   </li>
                                   <li class='humble-menu-has-sub '><a  href='#' onclick="return false"><span>Insert</span></a>

                                   </li>
                                   <li class='humble-menu-has-sub '><a href='#' onclick="return false"><span>Console</span></a>
                                   </li>
                                   <li><a draggable='false' href='#' onclick="return false"><table cellspacing='0' cellpadding='0' border='0'><tr><td>x:</td><td id='mouseX' style='text-align: right; width: 25px; font-size: .8em;'>0</td><td style='padding-left: 5px'>y:</td><td id='mouseY' style='text-align: right;  width: 25px; font-size: .8em;'>0</td></tr></table></a></li>
                                   <li><a draggable='false' href='#' onclick="return false">
                                           <table cellspacing='0' cellpadding='0' border='0'>
                                               <tr>
                                                   <td style='color: #f00'>Selected</td><td style='padding-left: 5px'>x:</td><td id='elementX' style='width: 25px; font-size: .8em; text-align: right' ></td>
                                                   <td style='padding-left: 5px'>y:</td><td id='elementY' style='width: 25px; font-size: .8em; text-align: right'></td>
                                                   <td style='padding-left: 5px'>z:</td><td id='elementZ' style='width: 20px; font-size: .8em; text-align: right'></td>
                                               </tr>
                                           </table></a></li>
                                   <li><a draggable='false' href='#' style='text-align: right' onclick="return false"><span>
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
                &copy; Humble Project, 2014-present
            </div>
        </div>
    </body>

</html>
