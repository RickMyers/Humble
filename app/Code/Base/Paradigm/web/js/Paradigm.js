/* -----------------------------------------------------------------------------
 * @title
 *
 *      PARADIGM Workflow Editor and Code Generator
 *
 * @description
 *
 *      Turns ideas into actions
 *
 * @author
 *
 *      Rick Myers [rick@humbleprogramming.com]
 *
 * ----------------------------------------------------------------------------- */
var Paradigm = (function () {
    return {
        /**
         * Configuration options are set in Paradigm.config.js
         */
        config: {

        },
        Editors: {
            
        },
        images: {
            /*
             * Any and all images that are to be injected into the canvas need to be listed here for pre-caching,
             *  otherwise the first rendering will happen before the images are loaded
             */
            "actor": {
               "src": "/images/paradigm/clipart/person1.png",
               "ref": false
            },
            "file": {
               "src": "/images/paradigm/clipart/file_trigger.png",
               "ref": false
            },            
            "system": {
                "src": "/images/paradigm/clipart/cron.png",
                "ref": false
            },
            "webservice": {
                "src": "/images/paradigm/clipart/webservice2.png",
                "ref": false
            },
            "webhook": {
               "src": "/images/paradigm/clipart/webhook_icon.png",
               "ref": false
            },
            "rule": {
                "src": "/images/paradigm/clipart/business-rule.png",
                "ref": false
            },
            "sensor": {
                "src": "/images/paradigm/clipart/sensor3.png",
                "ref": false
            },
            "trigger": {
                "src": "/images/paradigm/clipart/event3.png",
                "ref": false
            },
            "detector": {
                "src": "/images/paradigm/clipart/detector2.png",
                "ref": false
            },
            "exception": {
                "src": "/images/paradigm/clipart/exception.png",
                "ref": false
            }

        },
        cacheImages: function () {
            for (var image in Paradigm.images) {
                Paradigm.images[image].ref = new Image();
                Paradigm.images[image].ref.onload = function () {
                    Paradigm.draw.drawImage(this,10,10,50,50);
                    Paradigm.draw.clearRect(0,0,Paradigm.canvas.width,Paradigm.canvas.height);
                };
                Paradigm.images[image].ref.src = Paradigm.images[image].src;
            }
        },
        scroll: {
            left: function () {
                return $(window).scrollLeft() + ((Paradigm.container) ? Paradigm.container.scrollLeft : 0);
            },
            top: function () {
                return $(window).scrollTop() + (Paradigm.container) ? Paradigm.container.scrollTop : 0;
            }
        },
        /*  --------------------------------------------------------------------
         *
         *  --------------------------------------------------------------------*/
        default: {
            start: {
                x: 10,
                y: 10
            },
            alerts: {
                label: 'Alert'
            },
            rule: {
                label: 'Rule'
            },
            decision: {
                label: 'Decision'
            },
            actor: {
                label: 'Actor',
                image: "/images/paradigm/clipart/person1.png"
            },
            file: {
                label: 'File Trigger',
                image: "/images/paradigm/clipart/file_trigger.png"
            },            
            sensor: {
                label: 'Sensor',
                image: "/images/paradigm/clipart/sensor3.png"
            },
            trigger: {
                label: 'Event',
                image: "/images/paradigm/clipart/event3.png"
            },
            detector: {
                label: 'Detector',
                image: "/images/paradigm/clipart/detector2.png"
            },
            exception: {
                label: 'Exception',
                image: "/images/paradigm/clipart/exception.png"
            },
            system: {
                label: 'System',
                image: "/images/paradigm/clipart/cron.png"
            },
            webservice: {
                label: 'Web Service',
                image: "/images/paradigm/clipart/webservice.png"
            },
            webhook: {
                label: 'Web Hook',
                image: "/images/paradigm/clipart/webhook_icon.png"
            },            
            process: {
                label: 'Process'
            },
            begin: {
                label: 'Start',
                x: 74,
                y: 180
            },
            connector: {
                label: '<->'
            },
            external: {
                label: 'Offpage Conn'
            },
            report: {
                label: 'Report'
            },
            operation: {
                label: 'Program'
            },
            input: {
                label: 'File Input'
            },
            terminus: {
                label: ''
            },
            label: {
                label: ''
            }

        },
        prompts:    {
            decision: {
                title: "Add a new decision",
                image: "/images/paradigm/clipart/decision.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            process: {
                title: "Add a process",
                image: "/images/paradigm/clipart/process.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            alerts: {
                title: "Add an email, alert, etc...",
                image: "/images/paradigm/clipart/event.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            report: {
                title: "Generate a report",
                image: "/images/paradigm/clipart/data.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            actor: {
                title: "Identify the actor or event",
                image: "/images/paradigm/clipart/person1.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            file: {
                title: "Identify the directory of the file(s)",
                image: "/images/paradigm/clipart/file_trigger.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },            
            sensor: {
                title: "Listen for detectors pings, and trigger a workflow based on detection",
                image: "/images/paradigm/clipart/sensor2.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            trigger: {
                title: "A listener for a specific event of a particular name",
                image: "/images/paradigm/clipart/event3.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            detector: {
                title: "Look for values present in the triggering event, possibly triggering other workflows",
                image: "/images/paradigm/clipart/detector2.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            exception: {
                title: "Generates an application exception, which has a return code, a short message, and a longer description",
                image: "/images/paradigm/clipart/exception.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            system: {
                title: "Identify the time/date event that triggers the workflow",
                image: "/images/paradigm/clipart/cron.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            webservice: {
                title: "Identify the inbound service API call that triggers the workflow",
                image: "/images/paradigm/clipart/webservice.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            webhook: {
                title: "Identify the inbound reverse API call (WebHook) that triggers the workflow",
                image: "/images/paradigm/clipart/webhook_icon.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },            
            external: {
                title: "Add a connector to an external workflow",
                image: "/images/paradigm/clipart/external.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            input: {
                title: "Fetch a file input",
                image: "/images/paradigm/clipart/manual_input.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            rule: {
                title: "Create a business rule",
                image: "/images/paradigm/clipart/business-rule.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            },
            operation: {
                title: "Run a program",
                image: "/images/paradigm/clipart/manual_operation.png",
                default: "",
                description: "Fill out the text below and click OK to add a new element to the workflow, or click [Cancel] to abort adding an element"
            }
        },
        objects:    {},     /* list of all objects using the mongo _id */
        windows:    {},     /* preallocated hashmap of windows */
        drag:       false,  /* a flag, if true an item is being dragged */
        resize:     false,  /* set when within 5px of the edge of an element */
        canvas:     null,   /* reference to the drawing canvas  */
        container:  false,   /* reference to the div containing the canvas */
        label:      false,
        draw:       null,   /* the canvase context */
        rect:       null,   /* for storing the bounding client rectangle */
        element:    false,  /* current element index number for the hashmap */
        target:     false,  /* reference to actual object being dragged */
        lastElement: false, /* reference to last element you touched */
        dX:         0,      /* distance from mouse click to current element X coord */
        dY:         0,      /* distance from mouse click to current element X coord */
        mouseX:     0,      /* the X coordinate on the canvas of the current mouse position */
        mouseY:     0,      /* the Y coordinate on the canvas of the current mouse position */

        /*  --------------------------------------------------------------------
         *  Some useful generalized functions
         *  --------------------------------------------------------------------*/
        color:      function () {
            //random color generator
            //var r = Math.round(Math.random()*256);
            //var g = Math.round(Math.random()*256);
            //var b = Math.round(Math.random()*256);
            var r=189; //lets just do silver
            var g=189;
            var b=189;
            return "rgba("+r+","+g+","+b+",0.8)";
        },
        gradient:   function (x,y,w,h) {
            var H = h ? h : w;
            Paradigm.grad = Paradigm.draw.createLinearGradient(x,y,x,y+H);
            Paradigm.grad.addColorStop(0, '#e5e5e5');
            Paradigm.grad.addColorStop(0.7, '#cecece');
            Paradigm.grad.addColorStop(1, '#999999');
            Paradigm.draw.fillStyle = Paradigm.grad;
            return Paradigm.grad;
        },
        roundedRectangle: function (ctx, x, y, w, h, r, color, stroke) {
            if (w < 2 * r) r = w / 2;
            if (h < 2 * r) r = h / 2;
            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.moveTo(x+r, y);
            ctx.arcTo(x+w, y,   x+w, y+h, r);
            ctx.arcTo(x+w, y+h, x,   y+h, r);
            ctx.arcTo(x,   y+h, x,   y,   r);
            ctx.arcTo(x,   y,   x+w, y,   r);
            ctx.closePath();
            ctx.fill();
            if (stroke) {
              ctx.stroke();
            }
        },
        /*  --------------------------------------------------------------------
         *  Some additional useful math routines
         *  --------------------------------------------------------------------*/
        math:   {
            distance: function (p1X,p1Y,p2X,p2Y) {
                return Math.sqrt(Math.pow((p2X-p1X),2) + Math.pow((p2Y - p1Y),2));
            },
            slope:  function (p1X,p1Y,p2X,p2Y) {
                return ((p2Y - p1Y)/(p2X - p1X));
            },
            sign:   function ( p1, p2, p3)  {
                return (p1.x - p3.x) * (p2.y - p3.y) - (p2.x - p3.x) * (p1.y - p3.y);
            },
            triangle: {
                /*
                 * Area of a triangle from three sides
                 */
                area:   function (a,b,c) {
                    var s = (a+b+c)/2;
                    return Math.sqrt(s*(s-a)*(s-b)*(s-c));
                },
                /*
                 * Height of a triangle from 1 side and area
                 */
                height: function (A,b) {
                    return Math.round(A / ((.5)*b));
                }
            }
        },
        /*  --------------------------------------------------------------------
         *  Determines number of lines that will be needed to display the text
         *  in the chosen shape
         *
         *  @TODO:  FIX THIS!  It overrides the element.lines array, and it should
         *  augment the array and not override it
         *  --------------------------------------------------------------------*/
        calculateText:    function (element,baseFontFamily,baseFontSize) {
            //now calculate it
            baseFontFamily  = (baseFontFamily)  ? baseFontFamily : 'Arial';
            baseFontSize    = (baseFontSize)    ? baseFontSize   : 14;
            var color       = (element.lines.color) ? element.lines.color : 'rgb(0,0,0)';
            Paradigm.draw.font = baseFontSize+'px '+baseFontFamily;
            var textWidth   = Paradigm.draw.measureText(element.text).width;
            var breakItUp   = false;
            var text        = [];
            if (textWidth > (element.W-6)) {
                baseFontSize= baseFontSize-1;
                Paradigm.draw.font = baseFontSize+'px '+baseFontFamily;
                textWidth   = Paradigm.draw.measureText(element.text).width;
                if (textWidth > (element.W-6)) {
                    baseFontSize= baseFontSize-1;
                    Paradigm.draw.font = baseFontSize+'px '+baseFontFamily;
                    textWidth   = Paradigm.draw.measureText(element.text).width;
                    breakItUp   = (textWidth > (element.W-6));
                }
            }
            if (breakItUp) {
                var words = element.text.split(' ');
                var line = ''; var templine = '';
                for (var i=0; i< words.length; i++) {
                    templine = line;
                    line = line+' '+words[i];
                    if (Paradigm.draw.measureText(line).width > (element.W-6)) {
                        text[text.length] = templine;
                        line = words[i];
                    };
                }
                text[text.length] = line;
            } else {
                text[text.length] = element.text;
            }
            //BAD!  DO THIS DIFFERENTLY!
            return {
                text: text,
                color: color,
                font: baseFontFamily,
                size: baseFontSize
            };
        },
        /*  --------------------------------------------------------------------
         *  Places the text in the shape, respective of what kind of shape it is
         *  --------------------------------------------------------------------*/
        placeText:  function(element) {
            if (!element.lines.startX) {
                switch (element.type) {
                    case    "diagramlabel"      :  //place labels at top
                    case    "square"            :
                    case    "rectangle"         :
                    case    "image"             :
                    case    "roundedrectangle"  :
                    case    "polygon1"          :
                    case    "polygon2"          :
                    case    "parallelogram"     :
                    case    "trapezoid"         :   element.lines.startX = Math.round(element.W/2);
                                                    element.lines.startY = Math.round(element.H/2) - Math.round((((element.lines.text.length - 1) * element.lines.size)/6));
                                                    break;
                    case    "polygon1"          :   element.lines.startX = Math.round(element.W/2);
                                                    element.lines.startY = Math.round(element.H/3) - Math.round((((element.lines.text.length - 1) * element.lines.size)/6));
                                                    break;
                    case    "triangle"          :   element.lines.startX = 1;
                                                    element.lines.startY = Math.round(element.D/4)+6;
                                                    break;
                    case    "diamond"           :   element.lines.startX = 1;
                                                    element.lines.startY = Math.round(element.D/2);
                                                    break;
                    case    "circle"            :   element.lines.startX = 1;
                                                    element.lines.startY = Math.round(element.lines.size/4);
                                                    break;
                    default                     :
                                                    break;
                }
            } else {
                alert('skipped');
            }
            Paradigm.draw.font = element.lines.size+'px '+element.lines.font;
            Paradigm.draw.fillStyle = (element.lines.color) ? element.lines.color : 'rgb(0,0,0)';
            var x = 0; var y = element.lines.startY; var yOffset = Math.round(((element.lines.text.length-1) * element.lines.size) / 4); var dec = Math.round(element.lines.size/4);
            for (var i=0; i<element.lines.text.length; i++) {
                x = element.lines.startX - Math.round(Paradigm.draw.measureText(element.lines.text[i]).width/2);
                Paradigm.draw.fillText(element.lines.text[i],element.X + x,element.Y + y - yOffset);
                y += element.lines.size;
                yOffset = yOffset - dec;
            }
        },
        /*  --------------------------------------------------------------------
         *  puts the small label in the top left of some symbols
         *  --------------------------------------------------------------------*/
        applyLabel: function (element,x,y) {
            Paradigm.draw.font = '8px Arial';
            Paradigm.draw.fillStyle = 'rgb(0,0,0)';
            Paradigm.draw.fillText(element.label,x,y);
        },
        //place text in a shape, optionaly specify corners or middle and offset from sides
        applyText: function (text,element,where,font,size,xOffset,yOffset) {
            font    = (font) ? font : 'Arial';
            size    = (size) ? size : 10;
            xOffset = (xOffset) ? xOffset : 0;
            yOffset = (yOffset) ? yOffset : 0;
            Paradigm.draw.font = size+'px '+font;
            Paradigm.draw.fillStyle = (element.lines.color) ? element.lines.color : 'rgb(0,0,0)';
            var w   = Paradigm.draw.measureText(text).width;
            var r   = Math.round(w/2);
            switch (where.toLowerCase()) {
                case 't'    :   Paradigm.draw.fillText(text,element.X+(Math.round(element.W/2))-r+xOffset,element.Y+size+yOffset);
                                break;
                case 'b'    :
                                break;
                case 'c'    :
                                break;
                case 'tr'   :   Paradigm.draw.fillText(text,element.X +element.W-w-xOffset,element.Y+size+yOffset);
                                break;
                case 'tl'   :   Paradigm.draw.fillText(text,element.X+xOffset,element.Y+size+yOffset);
                                break;
                case 'br'   :
                                break;
                case 'bl'   :
                                break;
                case 'cr'   :
                                break;
                case 'cl'   :
                                break;
                default     :   break;
            }
        },
        /*
         * Sets the function that determines when a shape will stop taking connections, and is, hence, closed
         *
         */
        closures: function (glyph) {
            switch (glyph.element) {
                case "begin"    :   return  function () { return (glyph.connectors.E.begin || glyph.connectors.S.begin); };
                                    break;
                case "external"  :   return  function () {
                                        return ((this.connectors.N.begin || this.connectors.E.begin || this.connectors.W.begin || this.connectors.S.begin) && (this.connectors.N.end || this.connectors.E.end || this.connectors.W.end || this.connectors.S.end));
                                    };
                                    break;
                case "alerts"   :   return  function () { return false; };
                                    break;
                case "sensor"   :
                case "system"   :
                case "webservice" :
                case "webhook"  :
                case "trigger"  :
                case "file"     :
                case "actor"    :   return  function () { return false; };
                                    break;
                case "process"  :   return  function () { return false; };
                                    break;
                case "decision" :   return  function () { return (this.connectors.E.begin && this.connectors.S.begin) && (this.connectors.N.end || this.connectors.W.end); };
                                    break;
                case "connector":
                case "report"   :
                case "operation":
                case "input"    :
                case "exception":
                case "detector" :
                case "rule"     :   return  function () { return false; };
                                    break;
                case "terminus" :   return  function () { return ((this.connectors.W.end || this.connectors.N.end) && !(this.connectors.W.begin || this.connectors.N.begin)); };
                                    break;
                default         :   return function () {   return true;  };
                                    break;
            }
        },
        /*  --------------------------------------------------------------------
         *  Iterates through the elements in memory and draws them based on what
         *    kind of shape thay are
         *  --------------------------------------------------------------------*/
        redraw:   function () {
            Paradigm.draw.clearRect(0,0,Paradigm.canvas.width,Paradigm.canvas.height);
            var elements = [];
            var element  = false;
            var arrows   = [];
            for (var i=0; i<Paradigm.elements.list.length; i++) {
                elements[+Paradigm.elements.list[i].Z-1] = Paradigm.elements.list[i] ;   //arrange according to z-index levels
            }
            for (i=0; i<elements.length; i++) {
                if (!elements[i]) {
                    continue;
                }
                if (elements[i].active) {
                    element     = elements[i];
                    if (element.lines && !element.lines.text.font) {
                        var font      = (element.font)     ? element.font : 'Arial';
                        var size      = (element.fontSize) ? element.fontSize : 14;
                        element.lines = Paradigm.calculateText(element,font,size);
                    }
                    switch (element.type) {
                        case    "diagramlabel"  :   Paradigm.roundedRectangle(Paradigm.draw, element.X, element.Y, element.W, element.H, 10, 'rgba(222,222,222,.7)', true);
                                                    Paradigm.applyLabel(element,element.X+3,element.Y+18);
                                                    Paradigm.applyText('Version: '+Paradigm.actions.get.majorVersion()+'.'+Paradigm.actions.get.minorVersion(),element,'tr','Arial',9,0,5);
                                                    Paradigm.applyText('Client: '+Paradigm.actions.get.namespace(),element,'t','Arial',9,0,5);
                                                    Paradigm.applyText('Title: '+Paradigm.actions.get.diagramTitle(),element,'tl','Arial',9,0,5);
                                                    element.text = Paradigm.actions.get.diagramDescription();
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "square"        :

                        case    "rectangle"     :   Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.fillRect(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.strokeRect(element.X,element.Y,element.W,element.H);
                                                    Paradigm.applyLabel(element,element.X+2,element.Y+10);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "diamond"       :   var radius = Math.round(element.D/2);
                                                    var p0      = { x: element.X, y: element.Y };
                                                    var p1      = { x: element.X+radius, y: element.Y+radius };
                                                    var p2      = { x: element.X, y: element.Y+element.D };
                                                    var p3      = { x: element.X-radius, y: element.Y+radius };
                                                    Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.moveTo(p0.x, p0.y);
                                                    Paradigm.draw.lineTo(p1.x, p1.y);
                                                    Paradigm.draw.lineTo(p2.x, p2.y);
                                                    Paradigm.draw.lineTo(p3.x, p3.y);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.applyLabel(element,element.X-radius,element.Y+7);
                                                    Paradigm.draw.font = '8px Arial';
                                                    Paradigm.draw.fillText('T',element.X+radius-4,element.Y+radius-6);
                                                    Paradigm.draw.fillText('F',element.X+8,element.Y+element.D);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "triangle"      :   var radius = Math.round(element.D/2);
                                                    var p0 = { x: element.X, y: element.Y };
                                                    var p1 = { x: element.X+radius, y: element.Y+radius };
                                                    var p2 = { x: element.X-radius, y: element.Y+radius };
                                                    Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.moveTo(p0.x, p0.y);
                                                    Paradigm.draw.lineTo(p1.x, p1.y);
                                                    Paradigm.draw.lineTo(p2.x, p2.y);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.applyLabel(element,element.X-radius,element.Y+10);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "arrow"         :   arrows[arrows.length] = element;
                                                    break;
                        case    "image"         :   var image   = Paradigm.images[element.element].ref;
                                                    Paradigm.draw.drawImage(image,element.X,element.Y,element.W,element.H);
                                                    Paradigm.applyLabel(element,element.X-2,element.Y);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "circle"        :   Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H)|| 'rgb(9,9,9)';
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.arc(element.X, element.Y, element.rad, 0, 2*Math.PI, false);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "polygon1"      :   var v_ray = Math.round(element.H/2);
                                                    var h_ray = Math.round(element.W/2);
                                                    Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.moveTo(element.X,element.Y);
                                                    Paradigm.draw.lineTo(element.X + element.W,element.Y);
                                                    Paradigm.draw.lineTo(element.X + element.W,element.Y+v_ray);
                                                    Paradigm.draw.lineTo(element.X + h_ray,element.Y+element.H);
                                                    Paradigm.draw.lineTo(element.X,element.Y+v_ray);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.applyLabel(element,element.X,element.Y+10);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "polygon2"      :   var v_ray = Math.round(element.H/2);
                                                    var h_ray = Math.round(element.W/2);
                                                    Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.moveTo(element.X,element.Y);
                                                    Paradigm.draw.lineTo(element.X+element.W,element.Y-element.offset);
                                                    Paradigm.draw.lineTo(element.X+element.W,element.Y + element.offset+ element.H);
                                                    Paradigm.draw.lineTo(element.X,element.Y + element.H + element.offset);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.applyLabel(element,element.X,element.Y-2);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "trapezoid"     :   Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.moveTo(element.X,element.Y);
                                                    Paradigm.draw.lineTo(element.X + element.W, element.Y);
                                                    Paradigm.draw.lineTo(element.X + element.W - element.offset, element.Y + element.H);
                                                    Paradigm.draw.lineTo(element.X + element.offset, element.Y + element.H);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.applyLabel(element,element.X+4,element.Y+8);
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "parallelogram" :   Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.moveTo(element.X + element.offset, element.Y);
                                                    Paradigm.draw.lineTo(element.X + element.offset + element.W, element.Y);
                                                    Paradigm.draw.lineTo(element.X + element.W, element.Y + element.H);
                                                    Paradigm.draw.lineTo(element.X, element.Y + element.H);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.applyLabel(element,element.X,element.Y+5);
                                                    Paradigm.draw.font = '14px Arial';
                                                    Paradigm.draw.fillStyle = 'rgb(0,0,0)';
                                                    Paradigm.placeText(element);
                                                    break;
                        case    "roundedrectangle" :Paradigm.draw.fillStyle = Paradigm.gradient(element.X,element.Y,element.W,element.H);
                                                    Paradigm.draw.beginPath();
                                                    Paradigm.draw.arc(element.X, element.Y+element.rad, element.rad, .5*Math.PI, 1.5*Math.PI, false);
                                                    Paradigm.draw.lineTo(element.X+element.W,element.Y);
                                                    Paradigm.draw.arc(element.X+element.W, element.Y+element.rad, element.rad, 1.5*Math.PI, 0.5*Math.PI, false);
                                                    Paradigm.draw.lineTo(element.X,element.Y+element.H);
                                                    Paradigm.draw.closePath();
                                                    Paradigm.draw.fill();
                                                    Paradigm.draw.stroke();
                                                    Paradigm.placeText(element);
                                                    break;
                        default                 :   break;
                    }
                }
            }
            Paradigm.landingPad();  //slap the "basket" also called the landing pad out there...
            /*
             * After all the symbols have been drawn, now it's time to draw the arrows connecting them
             */
            var radians = Math.PI/180;
            var deltaA  = 30*radians; //The > arrow sides are +/- 30 degrees off the main stem of the arrow
            var obj, x1L, y1L, x1R, y1R, angleInRadians, op, l_arrow, r_arrow;
            for (i=0; i<arrows.length; i++) {
                element = arrows[i];
                if (element.from.id && Paradigm.objects[element.from.id]) {
                    obj = Paradigm.objects[element.from.id];
                    element.X = obj.connectors[element.from.direction].X;
                    element.Y = obj.connectors[element.from.direction].Y;
                }
                if (element.to.id && Paradigm.objects[element.to.id]) {
                    obj = Paradigm.objects[element.to.id];
                    element.X2 = obj.connectors[element.to.direction].X;
                    element.Y2 = obj.connectors[element.to.direction].Y;
                }
                Paradigm.draw.fillStyle = element.color;
                Paradigm.draw.beginPath();
                op = element.X < element.X2 ? -1 : 1; //this turns the calculation from addition to subtraction depending on which region of the unit circle we are on
                angleInRadians = Math.atan((element.Y - element.Y2) / (element.X - element.X2));
                x1L = (10 * Math.cos(angleInRadians + deltaA));
                y1L = (10 * Math.sin(angleInRadians + deltaA));
                x1R = (10 * Math.cos(angleInRadians - deltaA));
                y1R = (10 * Math.sin(angleInRadians - deltaA));
                l_arrow = {
                    X: Math.round(element.X2 + op * x1L),
                    Y: Math.round(element.Y2 + op * y1L)
                };
                r_arrow = {
                    X: Math.round(element.X2 + op * x1R),
                    Y: Math.round(element.Y2 + op * y1R)
                };
                Paradigm.draw.moveTo(element.X, element.Y);
                Paradigm.draw.lineTo(element.X2, element.Y2);
                Paradigm.draw.lineTo(l_arrow.X, l_arrow.Y);
                Paradigm.draw.moveTo(element.X2, element.Y2);
                Paradigm.draw.lineTo(r_arrow.X, r_arrow.Y);
                Paradigm.draw.closePath();
                Paradigm.draw.stroke();
            }
            if (Paradigm.elements.list[Paradigm.lastElement]) {
                Paradigm.hilite(Paradigm.elements.list[Paradigm.lastElement]);
            }
        },
        /*  --------------------------------------------------------------------
         *  You just clicked on the canvas... this goes through all the elements
         *  within the canvas trying to determine in 3 dimensions which element
         *  you most likely clicked on
         *  --------------------------------------------------------------------*/
        selected: function (evt) {
            var candidates  = [];
            var candidate   = false;
            var eligible    = false;
            var element     = false;
            var zIndex      = -1;
            Paradigm.mouseX -= Paradigm.scroll.left();
            Paradigm.mouseY -= Paradigm.scroll.top();
            for (var i=0; i<Paradigm.elements.list.length; i++) {
                eligible    = false;
                element     = Paradigm.elements.list[i];
                if (element.active) {
                    switch (element.type) {
                        case    "image"         :
                        case    "square"        :
                        case    "rectangle"     :   eligible    = ((Paradigm.mouseX > element.X) && (Paradigm.mouseX < (element.X + element.W)));
                                                    eligible    = eligible && ((Paradigm.mouseY > element.Y) && (Paradigm.mouseY < (element.Y + element.H)));
                                                    break;
                        case    "diamond"       :   var radius  = Math.round(element.D/2);
                                                    var pt      = { x: Paradigm.mouseX, y: Paradigm.mouseY };
                                                    var v1      = { x: element.X, y: element.Y };
                                                    var v2      = { x: element.X+radius, y: element.Y+radius };
                                                    var v3      = { x: element.X-radius, y: element.Y+radius };
                                                    var b1      = (Paradigm.math.sign(pt, v1, v2) > 0.0);
                                                    var b2      = (Paradigm.math.sign(pt, v2, v3) > 0.0);
                                                    var b3      = (Paradigm.math.sign(pt, v3, v1) > 0.0);
                                                    eligible    = (b1 && b2 && b3);
                                                    if (!eligible) {
                                                        v1      = { x: element.X, y: element.Y+element.D };
                                                        v2      = { x: element.X+radius, y: element.Y+radius };
                                                        v3      = { x: element.X-radius, y: element.Y+radius };
                                                        b1      = (Paradigm.math.sign(pt, v1, v2) < 0.0);
                                                        b2      = (Paradigm.math.sign(pt, v2, v3) < 0.0);
                                                        b3      = (Paradigm.math.sign(pt, v3, v1) < 0.0);
                                                        eligible= (b1 && b2 && b3);
                                                    }
                                                    break;
                        case    "triangle"      :   var radius  = Math.round(element.D/2);
                                                    var pt      = { x: Paradigm.mouseX, y: Paradigm.mouseY };
                                                    var v1      = { x: element.X, y: element.Y };
                                                    var v2      = { x: element.X+radius, y: element.Y+radius };
                                                    var v3      = { x: element.X-radius, y: element.Y+radius };
                                                    var b1      = (Paradigm.math.sign(pt, v1, v2) > 0.0);
                                                    var b2      = (Paradigm.math.sign(pt, v2, v3) > 0.0);
                                                    var b3      = (Paradigm.math.sign(pt, v3, v1) > 0.0);
                                                    eligible    = (b1 && b2 && b3);
                                                    break;
                        case    "arrow"         :   var x       = Paradigm.mouseX; var y  = Paradigm.mouseY;
                                                    var x1      = element.X;  var y1 = element.Y;
                                                    var x2      = element.X2; var y2 = element.Y2;
                                                    var a       = Paradigm.math.distance(x,y,x1,y1);
                                                    var b       = Paradigm.math.distance(x1,y1,x2,y2);
                                                    var c       = Paradigm.math.distance(x,y,x2,y2);
                                                    eligible    = ((a <= b) && (c <= b)) || (a<8) || (c<8); //distance within reason or clicked near endpoints
                                                    eligible    = eligible && (Paradigm.math.triangle.height(Paradigm.math.triangle.area(a,b,c),b)<12);
                                                    break;
                        case    "circle"        :   eligible    = (Paradigm.math.distance(element.X,element.Y,Paradigm.mouseX,Paradigm.mouseY) < element.rad);
                                                    break;
                        case    "polygon1"      :   var v_ray   = Math.round(element.H/2);
                                                    var h_ray   = Math.round(element.W/2);
                                                    var pt      = { x: Paradigm.mouseX, y: Paradigm.mouseY };
                                                    var v1      = { x: element.X, y: element.Y };
                                                    var v2      = { x: element.X+element.W, y: element.Y };
                                                    var v3      = { x: element.X+element.W, y: element.Y+v_ray };
                                                    var v4      = { x: element.X+h_ray, y: element.Y+element.H };
                                                    var v5      = { x: element.X, y: element.Y+v_ray };
                                                    var b1      = (Paradigm.math.sign(pt, v1, v2) > 0.0);
                                                    var b2      = (Paradigm.math.sign(pt, v2, v3) > 0.0);
                                                    var b3      = (Paradigm.math.sign(pt, v3, v4) > 0.0);
                                                    var b4      = (Paradigm.math.sign(pt, v4, v5) > 0.0);
                                                    var b5      = (Paradigm.math.sign(pt, v5, v1) > 0.0);
                                                    eligible    = (b1 && b2 && b3 && b4 && b5);
                                                    break;
                        case    "polygon2"      :   var pt      = { x: Paradigm.mouseX, y: Paradigm.mouseY };
                                                    var v1      = { x: element.X, y: element.Y };
                                                    var v2      = { x: element.X+element.W, y: element.Y-element.offset };
                                                    var v3      = { x: element.X+element.W, y: element.Y+element.H };
                                                    var v4      = { x: element.X, y: element.Y+element.H };
                                                    var b1      = (Paradigm.math.sign(pt, v1, v2) > 0.0);
                                                    var b2      = (Paradigm.math.sign(pt, v2, v3) > 0.0);
                                                    var b3      = (Paradigm.math.sign(pt, v3, v4) > 0.0);
                                                    var b4      = (Paradigm.math.sign(pt, v4, v1) > 0.0);
                                                    eligible    = (b1 && b2 && b3 && b4);
                                                    break;
                        case    "trapezoid"     :
                        case    "parallelogram" :   var pt      = { x: Paradigm.mouseX, y: Paradigm.mouseY };
                                                    var v1      = { x: element.X+element.offset, y: element.Y };
                                                    var v2      = { x: element.X+element.W+element.offset, y: element.Y };
                                                    var v3      = { x: element.X+element.W, y: element.Y+element.H };
                                                    var v4      = { x: element.X, y: element.Y+element.H };
                                                    var b1      = (Paradigm.math.sign(pt, v1, v2) > 0.0);
                                                    var b2      = (Paradigm.math.sign(pt, v2, v3) > 0.0);
                                                    var b3      = (Paradigm.math.sign(pt, v3, v4) > 0.0);
                                                    var b4      = (Paradigm.math.sign(pt, v4, v1) > 0.0);
                                                    eligible    = (b1 && b2 && b3 && b4);
                                                    break;
                        case    "roundedrectangle": eligible    = ((Paradigm.mouseX > element.X) && (Paradigm.mouseX < (element.X + element.W)));
                                                    eligible    = eligible && ((Paradigm.mouseY > element.Y) && (Paradigm.mouseY < (element.Y + element.H)));
                                                    eligible    = eligible ||
                                                        (Paradigm.math.distance(element.X,element.Y + element.rad,Paradigm.mouseX,Paradigm.mouseY) < element.rad) ||
                                                        (Paradigm.math.distance(element.X+element.W,element.Y + element.rad,Paradigm.mouseX,Paradigm.mouseY) < element.rad);
                                                    break;
                        default                 :   break;
                    }
                }
                if (eligible) {
                    candidates[candidates.length] = i;
                }
            }
            for (i=0; i<candidates.length; i++) {
                if (Paradigm.elements.list[candidates[i]].Z > zIndex) {
                    candidate   = candidates[i];
                    zIndex      = Paradigm.elements.list[candidates[i]].Z;
                }
            }
            return Paradigm.console.select(candidate);
        },
        /*  --------------------------------------------------------------------
         *  Draws the connector points on the current element
         *  --------------------------------------------------------------------*/
        hilite:     function (element) {
            Paradigm.draw.fillStyle = 'rgb(0,0,0)';
           // console.log(element);
            if (Paradigm.target.type === "arrow") {
                var t = Paradigm.target;
                Paradigm.draw.beginPath();
                Paradigm.draw.arc(t.X + Math.round((t.X2 - t.X)/2), t.Y + Math.round((t.Y2 - t.Y)/2), 3, 0, 2*Math.PI, false);
                Paradigm.draw.closePath();
                Paradigm.draw.fill();
                Paradigm.draw.stroke();
            } else {
                for (var i in element.connectors) {
                    Paradigm.draw.beginPath();
                    Paradigm.draw.arc(element.connectors[i].X, element.connectors[i].Y, 3, 0, 2*Math.PI, false);
                    Paradigm.draw.closePath();
                    Paradigm.draw.fill();
                    Paradigm.draw.stroke();
                }
            }
        },
        remove:         function (evt) {
            evt = (evt) ? evt : (event ? window.event : null);
            if ((evt.target === Paradigm.container) && (evt.keyCode === 46) && (Paradigm.lastElement !== false)) {
                var element = Paradigm.elements.list[Paradigm.lastElement];
                if (element.element === 'begin') {
                    alert("You can't delete the start, no matter how much you might want to");
                    return false;
                }
                element.active = false;
                if (element.type === "arrow") {
                    if (element.from && element.from.id) {
                        var from = Paradigm.objects[element.from.id];
                        from.connectors[element.from.direction].begin = false;
                    }
                    if (element.to && element.to.id) {
                        var to = Paradigm.objects[element.to.id];
                        to.connectors[element.to.direction].end = false;
                    }
                } else {
                    for (var direction in element.connectors) {
                        if (element.connectors[direction].begin) {
                            var obj = Paradigm.objects[element.connectors[direction].begin.id];
                            if (obj) {
                                obj.from.id = false;
                                obj.from.direction = false;
                            }
                        }
                        if (element.connectors[direction].end) {
                            var obj = Paradigm.elements.list[Paradigm.lastElement].connectors[direction];
                            var obj = Paradigm.objects[element.connectors[direction].end.id];
                            if (obj) {
                                obj.to.id = false;
                                obj.to.direction = false;
                            }
                        }
                    }
                    if (element.win) {
                        Desktop.semaphore.checkin(element.win);
                    }
                    //if a window was allocated, we return that through the semaphore
                }
                //Rather than delete it immediately, let's queue the delete for later
//              (new EasyAjax('/paradigm/element/remove')).add('id',element.id).then((response) => {
//                  console.log(response);
//              }).post();
                //then we scrunch the list of elements
                Paradigm.objects[element.id] = false;
                var scrunched = [];
                for (var i=0; i<Paradigm.elements.list.length; i++) {
                    if (Paradigm.elements.list[i].active) {
                        scrunched[scrunched.length] = Paradigm.elements.list[i];
                    }
                }
                Paradigm.elements.list = scrunched;
                Paradigm.lastElement = false;
                //finally we redraw the list of elements
                Paradigm.redraw();
               // }
            }
        },
        /*  --------------------------------------------------------------------
         *
         *  --------------------------------------------------------------------*/
        stats: {
            set: function (id) {
                var element         = Paradigm.elements.list[id];
                $('#elementX').html(element.X);
                $('#elementY').html(element.Y);
                $('#elementZ').html(element.Z);
                $('#elementLabel').val(element.label);
                $('#elementText').val(element.text);
                Paradigm.lastElement = id;
            },
            update: function () {
                if (Paradigm.lastElement !== false) {
                    var element         = Paradigm.elements.list[Paradigm.lastElement];
                    element.label       = $('#elementLabel').val();
                    element.text        = $('#elementText').val();
                    element.lines.startX= false;
                    Paradigm.calculateText(element,14,'Arial');
                    Paradigm.console.type('Updating element stats','');
                    Paradigm.redraw();
                }
            }
        },
        /*  --------------------------------------------------------------------
         *
         *  --------------------------------------------------------------------*/
        mouse: {
            over:      function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
                Desktop.on(Paradigm.canvas,'mousemove',Paradigm.mouse.coords);
            },
            out:       function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
                Desktop.off(Paradigm.canvas,'mousemove',Paradigm.mouse.coords);
            },
            coords:    function (evt) {
                $E('mouseX').innerHTML = Paradigm.mouseX = Math.round((evt.clientX - Paradigm.rect.left + Paradigm.scroll.left() + $E('canvas-container').scrollLeft));
                $E('mouseY').innerHTML = Paradigm.mouseY = Math.round((evt.clientY - Paradigm.rect.top + Paradigm.scroll.top() + $E('canvas-container').scrollTop));
            },
            move:      function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
                var scroll = {
                    left: Paradigm.scroll.left(),
                    top: Paradigm.scroll.top()
                };
                if (Paradigm.drag && (Paradigm.element !== false )) {
                    $('#elementX').html(Paradigm.target.X);
                    $('#elementY').html(Paradigm.target.Y);
                    if (Paradigm.target.type === "arrow") {
                        if ((Paradigm.target.from.id && Paradigm.objects[Paradigm.target.from.id]) && (Paradigm.target.to.id && Paradigm.objects[Paradigm.target.to.id])) {
                            //nop, you are already connected
                        } else if (Paradigm.target.dragStart) {
                            Paradigm.elements.list[Paradigm.element].X  = Paradigm.mouseX - Paradigm.dX - scroll.left;
                            Paradigm.elements.list[Paradigm.element].Y  = Paradigm.mouseY - Paradigm.dY - scroll.top;
                        } else if (Paradigm.target.dragEnd) {
                            Paradigm.elements.list[Paradigm.element].X2 = Paradigm.mouseX - Paradigm.dX2 - scroll.left;
                            Paradigm.elements.list[Paradigm.element].Y2 = Paradigm.mouseY - Paradigm.dY2 - scroll.top;
                        } else {
                            Paradigm.elements.list[Paradigm.element].X  = Paradigm.mouseX - Paradigm.dX - scroll.left;
                            Paradigm.elements.list[Paradigm.element].Y  = Paradigm.mouseY - Paradigm.dY - scroll.top;
                            Paradigm.elements.list[Paradigm.element].X2 = Paradigm.mouseX - Paradigm.dX2 - scroll.left;
                            Paradigm.elements.list[Paradigm.element].Y2 = Paradigm.mouseY - Paradigm.dY2 - scroll.top;
                        }
                    } else {
                        Paradigm.elements.list[Paradigm.element].X = Paradigm.mouseX - Paradigm.dX - scroll.left;
                        Paradigm.elements.list[Paradigm.element].Y = Paradigm.mouseY - Paradigm.dY - scroll.top;
                        Paradigm.elements.connectors.set(Paradigm.target);
                    }
                    Paradigm.redraw(evt);
                }
            },
            down:      function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
                Paradigm.mouse.coords(evt);
                Desktop.off(Paradigm.canvas,"mousedown",Paradigm.mouse.down);
                Desktop.on(Paradigm.canvas,"mousemove",Paradigm.mouse.move);
                Desktop.on(document,"mouseup",Paradigm.mouse.up);
                Paradigm.drag    = true;
                Paradigm.element = Paradigm.selected(evt);
                if (Paradigm.element !== false) {
                    Paradigm.target = Paradigm.elements.list[Paradigm.element];
                    if (Paradigm.target.type === "arrow") {
                        Paradigm.target.D           = Paradigm.math.distance(Paradigm.target.X,Paradigm.target.Y,Paradigm.target.X1,Paradigm.target.Y1);
                        Paradigm.target.dragStart   = ((Paradigm.math.distance(Paradigm.mouseX,Paradigm.mouseY,Paradigm.target.X,Paradigm.target.Y)) < 15);
                        Paradigm.target.dragEnd     = ((Paradigm.math.distance(Paradigm.mouseX,Paradigm.mouseY,Paradigm.target.X2,Paradigm.target.Y2)) < 20);
                        Paradigm.dY2                = Paradigm.mouseY - Paradigm.elements.list[Paradigm.element].Y2;   //distance to top
                        Paradigm.dX2                = Paradigm.mouseX - Paradigm.elements.list[Paradigm.element].X2;   //distance to left;
                    }
                    Paradigm.stats.set(Paradigm.element);
                    var current_idx = Paradigm.elements.list[Paradigm.element].Z;  //current z index
                    for (var i=0; i<Paradigm.elements.list.length; i++) {
                        if (Paradigm.elements.list[i].Z > current_idx) {
                            Paradigm.elements.list[i].Z--;
                        }
                    }
                    Paradigm.elements.list[Paradigm.element].Z = Paradigm.elements.list.length;
                    Paradigm.dY = Paradigm.mouseY - Paradigm.elements.list[Paradigm.element].Y ;   //distance to top
                    Paradigm.dX = Paradigm.mouseX - Paradigm.elements.list[Paradigm.element].X ;    //distance to left;
                    Paradigm.redraw();
                }
            },
            up:        function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
                if (Workflows.snap) {
                    //this didn't work as expected, so I am not allowing snapping (for now)
                    var grid = 10;
                    var offsetX = Paradigm.target.X % grid;
                    var offsetY = Paradigm.target.Y % grid;

                    offsetX = (offsetX < (grid/2)) ? -1*offsetX : grid-offsetX;
                    offsetY = (offsetY < (grid/2)) ? -1*offsetY : grid-offsetY;
                    Paradigm.target.X = Paradigm.target.X + offsetX;
                    Paradigm.target.Y = Paradigm.target.Y + offsetY;
                    var target = Paradigm.target;
                    if (target.connectors) {
                        for (var i in target.connectors) {
                            target.connectors[i].X += offsetX;
                            target.connectors[i].Y += offsetY;
                        }
                    }
                    if (Paradigm.elements.list[Paradigm.lastElement]) {
                        Paradigm.hilite(Paradigm.elements.list[Paradigm.lastElement]);
                    }
                    Paradigm.redraw();
                }
                Desktop.on(Paradigm.canvas,"mousedown",Paradigm.mouse.down);
                Desktop.off(Paradigm.canvas,"mousemove",Paradigm.mouse.move);
                Desktop.off(document,"mouseup",Paradigm.mouse.up);
                if (Paradigm.target.type === "arrow") {
                    if (!(Paradigm.target.from.id && Paradigm.target.to.id)) {
                        Paradigm.elements.connectors.check(Paradigm.target);
                    }
                }
                Paradigm.drag       = false;
                Paradigm.element    = false;
                Paradigm.target     = false;
                Paradigm.resize     = false;
            },
            click:          function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
            },
            doubleClick:    function (evt) {
                evt = (evt) ? evt : ((window.event) ? event : null);
                var element = Paradigm.elements.list[Paradigm.lastElement];
                if (element.type !== 'arrow') {
                    if (!element.win) {
                        element.win = Desktop.semaphore.checkout();
                    }
                    Desktop.window.list[element.win]._title(element.label+' ['+element.text+'] | Paradigm');
                    Desktop.window.list[element.win]._open("<table style='width: 100%; height: 100%'><tr><td align='center' valign='middle'><img src='/images/paradigm/clipart/loading_indicator.gif' /></td></tr></table>");
                    (new EasyAjax('/paradigm/element/configure')).add('namespace',Paradigm.actions.get.namespace()).add('window_id',element.win).add('id',element.id).add('type',element.element).then((response) => {
                        Desktop.window.list[element.win]._open(response);
                    }).post();
                }
            }
        },
        elements: {
            list:   [],
            creating: false,
            connectors: {
                check: function (connector) {
                    if (!(connector.start && connector.end)) {
                        var beginCandidates = [];
                        var endCandidates = [];
                        var candidate, ctr, element, distance;
                        for (var i=0; i<Paradigm.elements.list.length; i++) {
                            element = Paradigm.elements.list[i];
                            if (Paradigm.elements.list[i].type === 'arrow') {
                                continue;
                            }
                            //ok, somehow I have to figure out who a 'candidate' is to connect to, and whether they are already have reached their allowed number of connectoins
                            if (Paradigm.elements.list[i].active) {
                                candidate = false;
                                if (!connector.from.id) {
                                    ctr = 0;
                                    for (var direction in element.connectors) {
                                        if (!candidate) {
                                            distance = Paradigm.math.distance(connector.X,connector.Y,element.connectors[direction].X,element.connectors[direction].Y);
                                            candidate = (distance < 20);
                                            if (candidate) {
                                                beginCandidates[beginCandidates.length] = { 'direction': direction, 'element': element, 'distance': distance};
                                            }
                                        }
                                    }
                                }
                                if (!connector.to.id && !candidate) {
                                    ctr = 0;
                                    for (var direction in element.connectors) {
                                        if (!candidate) {
                                            distance = Paradigm.math.distance(connector.X2,connector.Y2,element.connectors[direction].X,element.connectors[direction].Y);
                                            candidate = (distance < 20);
                                            if (candidate) {
                                                endCandidates[endCandidates.length] = { 'direction': direction, 'element': element, 'distance': distance};
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        distance = 30; candidate = false;
                        for (i=0; i<beginCandidates.length; i++) {
                            if (beginCandidates[i].distance < distance) {
                                distance    = beginCandidates[i].distance;
                                candidate   = beginCandidates[i];
                            }
                        }
                        if (candidate && (!candidate.element.isClosed()) && !(candidate.element.connectors[candidate.direction].begin || candidate.element.connectors[candidate.direction].end)) {
                            //also check to see if they are already connected
                            connector.X = candidate.element.connectors[candidate.direction].X;
                            connector.Y = candidate.element.connectors[candidate.direction].Y;
                            connector.from.id = candidate.element.id;
                            connector.from.direction = candidate.direction;
                            candidate.direction.arrow = connector;
                            candidate.element.connectors[candidate.direction].begin = connector;
                        } else {
                            for (i=0; i<endCandidates.length; i++) {
                                if (endCandidates[i].distance < distance) {
                                    distance    = endCandidates[i].distance;
                                    candidate   = endCandidates[i];
                                }
                            }
                            if (candidate && (!candidate.element.isClosed()) && !(candidate.element.connectors[candidate.direction].begin || candidate.element.connectors[candidate.direction].end)) {
                                connector.X2 = candidate.element.connectors[candidate.direction].X;
                                connector.Y2 = candidate.element.connectors[candidate.direction].Y;
                                connector.to.id = candidate.element.id;
                                connector.to.direction = candidate.direction;
                                candidate.direction.arrow = connector;
                                candidate.element.connectors[candidate.direction].end = connector;
                            }
                        }
                        Paradigm.redraw();
                    }
                },
                set: function (element) {
                    switch (element.type) {
                        case    "square"    :
                        case    "rectangle" :
                        case    "image"     :   for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = Math.round(element.X + (element.W / 2));
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + element.W;
                                                                        element.connectors[i].Y = Math.round(element.Y + (element.H / 2));
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = Math.round(element.Y + (element.H / 2));
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = Math.round(element.X + (element.W / 2));
                                                                        element.connectors[i].Y = element.Y + element.H;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "diamond"   :   var rad = Math.round(element.D/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + rad;
                                                                        element.connectors[i].Y = element.Y + rad;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X - rad;
                                                                        element.connectors[i].Y = element.Y + rad;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y + element.D;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "triangle"  :   var rad = Math.round(element.D /2);
                                                var ray = Math.round(rad/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + rad - ray;
                                                                        element.connectors[i].Y = element.Y + ray;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X - rad + ray;
                                                                        element.connectors[i].Y = element.Y + ray;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y + rad;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "circle"    :   for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'E'    :   element.connectors[i].X = element.X + element.rad;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y+element.rad;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "polygon1"  :   var v_ray = Math.round(element.H/2);
                                                var h_ray = Math.round(element.W/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + element.W;
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y + element.H;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "polygon2"  :   var v_ray = Math.round(element.H/2);
                                                var h_ray = Math.round(element.W/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y - Math.round(element.offset/2);
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + element.W;
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X;
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y + element.H + element.offset;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "parallelogram" :   var v_ray   = Math.round(element.H/2);
                                                var h_ray   = Math.round(element.W/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X + h_ray + element.offset;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + element.W + Math.round(element.offset/2);
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X + Math.round(element.offset/2);
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y + element.H;
                                                                        break;
                                                    }
                                                }
                                                break;
                        case    "trapezoid" :   var v_ray   = Math.round(element.H/2);
                                                var h_ray   = Math.round(element.W/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'E'    :   element.connectors[i].X = element.X + element.W - Math.round(element.offset/2);
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X + Math.round(element.offset/2);
                                                                        element.connectors[i].Y = element.Y + v_ray;
                                                                        break;
                                                        case 'S'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y + element.H;
                                                                        break;
                                                     }
                                                }
                                                break;
                        case "roundedrectangle" : var v_ray   = Math.round(element.H/2);
                                                var h_ray   = Math.round(element.W/2);
                                                for (var i in element.connectors) {
                                                    switch (i) {
                                                        case 'N'    :   element.connectors[i].X = element.X + h_ray;
                                                                        element.connectors[i].Y = element.Y;
                                                                        break;
                                                        case 'W'    :   element.connectors[i].X = element.X - element.rad;
                                                                        element.connectors[i].Y = element.Y + element.rad;
                                                                        break;
                                                    }
                                                }
                        default             :   break;
                    }
                }
            },

            start: {
                add: function () {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;
                    (new EasyAjax('/paradigm/element/create')).add('shape','circle').add('type','begin').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        (new EasyAjax('/paradigm/diagram/update')).add('id',Paradigm.actions.get.currentDiagramId()).add('workflow_id',response).then((response) => {
                            //Paradigm.actions.set.currentDiagram('')
                        }).post();
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'circle',
                            active: true,
                            element: 'begin',
                            color: Paradigm.color(),
                            fillStyle: '',
                            label: Paradigm.default.begin.label,
                            text: Paradigm.console.add('Add [start:&text&][ID:'+response+']','Start',1),
                            lines: {
                                text: [],
                                font: false,
                                size: false
                            },
                            connectors: {
                                'E': { X: '', Y:'', begin: false, end: null},
                                'S': { X: '', Y:'', begin: false, end: null}
                            },
                            X:  Paradigm.default.begin.x,
                            Y:  Paradigm.default.begin.y,
                            rad: 30,
                            W:  60,
                            Z:  z+1,
                            isClosed: null,
                            win: null
                        };
                        //#################################################################################################
                        //###                                !!!VERY IMPORTANT!!!                                       ###
                        //###    THE ID OF THE START GLYPH BECOMES THE INTERNAL NAME OF THE WORKFLOW TO ALL TRIGGERS    ###
                        //#################################################################################################
                        Paradigm.actions.set.mongoWorkflowId(response); // <<-- Workflow Name Being Set
                        Paradigm.console.add('New Workflow: ['+Paradigm.actions.get.mongoWorkflowId()+']','',1);
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            external:    {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','polygon1').add('type','external').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'polygon1',
                            active: true,
                            element: 'external',
                            color: Paradigm.color(),
                            fillStyle: '',
                            label: Paradigm.default.external.label,
                            text: Paradigm.console.add('Add [external: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  60,
                            H:  80,
                            Z:  z+1,
                            isClosed: null,
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            alerts: {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','triangle').add('type','alerts').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'triangle',
                            active: true,
                            element: 'alerts',
                            color: Paradigm.color(),
                            fillStyle: '',
                            label: Paradigm.default.alerts.label,
                            text: Paradigm.console.add('Add [alert: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x+60,
                            Y:  Paradigm.default.start.y,
                            W:  60,
                            H:  60,
                            D:  120,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            actor:  {                
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','actor').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            image: Paradigm.default.actor.image,
                            element: 'actor',
                            label: Paradigm.default.actor.label,
                            text: Paradigm.console.add('Add [actor: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  57,
                            H:  68,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            file:  {                
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','file').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            image: Paradigm.default.file.image,
                            element: 'file',
                            label: Paradigm.default.file.label,
                            text: Paradigm.console.add('Add [fileTrigger: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },            
            system:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','system').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            image: Paradigm.default.system.image,
                            element: 'system',
                            label: Paradigm.default.system.label,
                            text: Paradigm.console.add('Add [system: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            webservice:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','webservice').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            image: Paradigm.default.webservice.image,
                            element: 'webservice',
                            label: Paradigm.default.webservice.label,
                            text: Paradigm.console.add('Add [webservice: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            webhook:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','webhook').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z       = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            image: Paradigm.default.webhook.image,
                            element: 'webhook',
                            label: Paradigm.default.webhook.label,
                            text: Paradigm.console.add('Add [webhook: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },            
            process:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','rectangle').add('type','process').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'rectangle',
                            active: true,
                            element: 'process',
                            color: Paradigm.color(),
                            fillStyle: '',
                            label: Paradigm.default.process.label,
                            text: Paradigm.console.add('Add [process:&text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  120,
                            H:  66,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            decision:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','diamond').add('type','decision').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'diamond',
                            active: true,
                            element: 'decision',
                            text: Paradigm.console.add('Add [decision: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            label: Paradigm.default.decision.label,
                            color: Paradigm.color(),
                            fillStyle: '',
                            X:  Paradigm.default.start.x + 50,
                            Y:  Paradigm.default.start.y,
                            D:  100,
                            W:  100,
                            Z:  z+1,
                            isClosed: null,
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            rule:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','rule').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            element: 'rule',
                            label: Paradigm.default.rule.label,
                            text: Paradigm.console.add('Add [rule: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                color: "white",
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            sensor:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','sensor').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            element: 'sensor',
                            label: Paradigm.default.sensor.label,
                            text: Paradigm.console.add('Add [sensor: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },

            trigger:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','trigger').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            element: 'trigger',
                            label: Paradigm.default.trigger.label,
                            text: Paradigm.console.add('Add [trigger: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  70,
                            H:  70,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            detector:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','detector').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            element: 'detector',
                            label: Paradigm.default.detector.label,
                            text: Paradigm.console.add('Add [detector: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  60,
                            H:  60,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            exception:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','image').add('type','exception').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'image',
                            active: true,
                            element: 'exception',
                            label: Paradigm.default.exception.label,
                            text: Paradigm.console.add('Add [exception: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  80,
                            H:  67,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            connector:  {
                add: function () {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','arrow').add('type','connector').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'arrow',
                            active: true,
                            element: 'connector',
                            label: Paradigm.default.connector.label,
                            text: Paradigm.console.add('Add [arrow:][ID:'+response+']','',1),
                            color: "rgba(0,0,0,0.8)",
                            dragStart: false,
                            dragEnd: false,
                            from: {
                                id: false,
                                direction: false
                            },
                            to: {
                                id: false,
                                direction: false
                            },
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            X2:  140,
                            Y2:   40,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            report:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','parallelogram').add('type','report').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'parallelogram',
                            active: true,
                            element: 'report',
                            label: Paradigm.default.report.label,
                            text: Paradigm.console.add('Add[report: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  100,
                            H:  60,
                            offset: 24,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            operation:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','trapezoid').add('type','operation').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id:   response,
                            type: 'trapezoid',
                            active: true,
                            element: 'operation',
                            label: Paradigm.default.operation.label,
                            text: Paradigm.console.add('Add [program: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y,
                            W:  110,
                            H:  60,
                            offset: 16,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            input:  {
                add: function (text) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','polygon2').add('type','input').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'polygon2',
                            active: true,
                            element: 'input',
                            label: Paradigm.default.input.label,
                            text: Paradigm.console.add('Add [input: &text&][ID:'+response+']',text,1),
                            lines: {
                                text: [],
                                font: false,
                                size: false,
                                startX: false,
                                startY: false
                            },
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'E': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false},
                                'S': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            rad: 0,
                            X:  Paradigm.default.start.x,
                            Y:  Paradigm.default.start.y+5,
                            W:  110,
                            H:  40,
                            offset: 12,
                            Z:  z+1,
                            isClosed: function () {
                                //a function to determine when a shape is closed, as in no more connections are allowed
                                return false;
                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();

                }
            },
            terminus:  {
                add: function () {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','roundedrectangle').add('type','terminus').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.elements.list[z] = {
                            id: response,
                            type: 'roundedrectangle',
                            active: true,
                            element: 'terminus',
                            label: Paradigm.default.terminus.label,
                            text: Paradigm.console.add('Add [terminus: &text&][ID:'+response+']','End',1),
                            connectors: {
                                'N': { X: '', Y:'', begin: false, end: false},
                                'W': { X: '', Y:'', begin: false, end: false}
                            },
                            color: Paradigm.color(),
                            fillStyle: '',
                            X:  Paradigm.default.start.x+15,
                            Y:  Paradigm.default.start.y+15,
                            W:  80,
                            H:  30,
                            lines: {
                                text: [],
                                font: false,
                                size: false
                            },
                            rad: 15,
                            Z:  z+1,
                            isClosed: function () {

                            },
                            win: null
                        };
                        Paradigm.elements.connectors.set(Paradigm.elements.list[z]);
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                    }).post();
                }
            },
            label:  {
                add: function (after) {
                    if (Paradigm.elements.creating) {
                        return;
                    }
                    Paradigm.elements.creating = !Paradigm.elements.creating;                    
                    (new EasyAjax('/paradigm/element/create')).add('shape','roundedrectangle').add('type','label').then((response) => {
                        Paradigm.elements.creating = !Paradigm.elements.creating;
                        if (!response) {
                            alert('Please try again, failed to create element');
                            return;
                        }
                        var z = Paradigm.elements.list.length;
                        Paradigm.objects[response] = Paradigm.label =  Paradigm.elements.list[z] = {
                            id: response,
                            type: 'diagramlabel',
                            active: true,
                            element: 'label',
                            label: '',
                            text: Paradigm.actions.get.diagramDescription(),
                            connectors: {
                            },
                            color: 'rgb(202,202,202)',
                            fillStyle: '',
                            X:  320,
                            Y:  -7,
                            W:  750,
                            H:  80,
                            lines: {
                                text: [],
                                font: false,
                                size: false
                            },
                            rad: 5,
                            Z:  z+1,
                            isClosed: function () {
                                return true;
                            },
                            win: null
                        };
                        Paradigm.elements.list[z].isClosed = Paradigm.closures(Paradigm.elements.list[z]);
                        Paradigm.redraw();
                        if (after) {
                            after();
                        }
                    }).post();
                }
            }
        },
        landingPad: function () {
            Paradigm.roundedRectangle(Paradigm.draw, -2, -5, 150, 120, 5, 'rgba(222,222,222,.4)', true);
        },
        energize:   function () {
            Desktop.on(Paradigm.canvas,'mousedown',Paradigm.mouse.down);
            Desktop.on(Paradigm.canvas,'mouseover',Paradigm.mouse.over);
            Desktop.on(Paradigm.canvas,'mouseout',Paradigm.mouse.out);
            Desktop.on(Paradigm.canvas,'dblclick',Paradigm.mouse.doubleClick);
        },
        init: function () {
            Desktop.init(Desktop.enable);
            Desktop.semaphore.init();
            //Paradigm.console.initialize(); //Until we can get the focus issue worked out
            Paradigm.canvas             = $E('canvas');
            Paradigm.container          = $E('canvas-container');
            Paradigm.draw               = Paradigm.canvas.getContext('2d');
            Paradigm.rect               = Paradigm.canvas.getBoundingClientRect();
            Paradigm.energize();
            Paradigm.cacheImages();
            //suppress the delete key from propagating up and removing current element
            for (var i in Desktop.window.list) {
                if (Desktop.window.list[i].frame) {
                    $(Desktop.window.list[i].frame).on('keydown',function (evt) {
                        if (evt.keyCode === 46) {
                            evt.stopPropagation();
                        }
                    });
                }
            }
        },
        desktop: {
            size: function (multiplier) {
                $(Paradigm.canvas).prop('width',$(Paradigm.container).width()*multiplier);
                $(Paradigm.canvas).prop('height',$(Paradigm.container).height()*multiplier);
                Paradigm.redraw();
            },
            resize: function () {
                $('#paradigm-content').height(window.innerHeight - +$('#paradigm-header').height() - +$('#paradigm-menu').height() - +$('#paradigm-footer').height() );
            },
            init: function () {

            }
        }
    };
})();
window.onload = Paradigm.init;
$(document).ready(function () {
  // $('#paradigmConsole').focus();
});