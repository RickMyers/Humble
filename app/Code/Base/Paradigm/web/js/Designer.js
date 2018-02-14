var Designer = (function ($) {
    return {
        canvas: false,
        container: false,
        draw: false,
        dX:         0,      /* distance from mouse click to current element X coord */
        dY:         0,      /* distance from mouse click to current element X coord */
        mouseX:     0,      /* the X coordinate on the canvas of the current mouse position */
        mouseY:     0,      /* the Y coordinate on the canvas of the current mouse position */
        form: {
            letter: 1.2941,
            legal: 1.6471
        },
        image: {
            inject: function (img) {
                Designer.image.hidden.src = $('#form-image').val();;
            },
            hidden: false
        },
        text: {
            inject: function () {

            }
        },
        mouse: {
            down: function () {

            },
            over: function () {

            },
            move: function () {

            },
            out: function () {

            },
            click: {
                single: function () {

                },
                double: function () {

                }
            }
        },
        resize:     function () {
            var nh     = $(window).height();
         //   $('#paradigm-virtual-desktop').height($(window).height()-$('#status-bar').height());
            $('#designer-right-column').height(nh);
            $('#designer-page').height(nh);
            $('#designer-left-column').height(nh);
            $('#designer-right-column').height(nh);
            $('#designer-container').height(nh);
            $('#designer-canvas-container').height(nh - ($E('designer-controls').offsetHeight + $E('designer-footer').offsetHeight + $E('designer-header').offsetHeight));
            Designer.canvas.setAttribute('width',$(Designer.container).width());
            Designer.canvas.setAttribute('height',$(Designer.container).height());
            //have to set the canvas height and width specially...
        },
        energize:   function () {
            Desktop.on(Designer.canvas,'mousedown',Designer.mouse.down);
            Desktop.on(Designer.canvas,'mouseover',Designer.mouse.over);
            Desktop.on(Designer.canvas,'mouseout',Designer.mouse.out);
            Desktop.on(Designer.canvas,'dblclick',Designer.mouse.click.double);
        },
        init:       function () {
            Designer.canvas             = $E('designer-canvas');
            Designer.container          = $E('designer-canvas-container');
            Designer.draw               = Designer.canvas.getContext('2d');
            Designer.rect               = Designer.canvas.getBoundingClientRect();
            Designer.image.hidden      = $E('hidden-image');
            $(Designer.image.hidden).on('load',function () {
                var ratio = Math.round((Designer.image.hidden.offsetHeight/Designer.image.hidden.offsetWidth)*1000)/1000
                $('#form-ratio').val(ratio);
                var w = $(Designer.container).width();
                Designer.draw.drawImage(Designer.image.hidden,0,0,w,w*ratio);
            });
            Designer.energize();
            $(window).resize();
        }
    }
})($);
$(window).ready(Designer.init);
$(window).resize(Designer.resize);
