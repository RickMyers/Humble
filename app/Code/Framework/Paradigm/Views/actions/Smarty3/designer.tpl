<!DOCTYPE html>
<html>
    <head>
        <title>Form Designer Try Number 3</title>
        <link rel="stylesheet" type="text/css" href="/css/common" />
        <link rel="stylesheet" type="text/css" href="/css/bootstrap" />
        <link rel="stylesheet" type="text/css" href="/css/desktop" />
        <link rel="stylesheet" type="text/css" href="/css/designer" />
        <script type="text/javascript" src="/js/jquery"></script>
        <script type="text/javascript" src="/js/common"></script>
        <script type="text/javascript" src="/js/bootstrap"></script>
        <script type="text/javascript" src="/js/desktop"></script>
        <script type="text/javascript" src="/js/designer"></script>
    </head>
    <body>
        <div id="paradigm-virtual-desktop">
            <div id="designer-page">
                <div id="designer-left-column">
                    <center>
                    LEFT COLUMN
                    </center>
                </div>
                <div id="designer-right-column">
                    <center>
                    RIGHT COLUMN
                    </center>
                </div>
                <div id="designer-container">
                    <div id="designer-header">
                        Paradigm Form Designer
                    </div>
                    <div id="designer-controls">
                        <input type="text" name="form-ratio" id="form-ratio" style="padding: 2px; width: 60px; background-color: lightcyan; border-radius: 2px; border: 1px solid #aaf; float: right;" />
                        <input type="text" name="form-image" id="form-image" style="padding: 2px; width: 200px; background-color: lightcyan; border-radius: 2px; border: 1px solid #aaf" value="/images/paradigm/sample_medical_form.jpg" />
                        <input type="button" value=" Set " onclick="Designer.image.inject(this)" />
                    </div>
                    <div id="designer-canvas-container">
                        <canvas id="designer-canvas">
                        </canvas>
                    </div>
                    <div id="designer-footer">
                        &copy; 2007-Present, The Humble Project, all rights reserved
                    </div>
                </div>
            </div>
        </div>
        <div style="position: absolute; top: -100px; left: -100px; width: 1px; height: 1px; overflow: hidden">
        <image id="hidden-image" src="" alt="broken" />
        </div>
    </body>
</html>
