/**
 *
 * EasySliders, part of the Cloud-IT project.
 *
 * @author: Rick Myers <rick@humbleprogramming.com>
 *
 *
 */
var EasySliders        = [];
function EasySlider(div,len,hgt,optId) {
    EasySlider.Control.init();   //go set the sizer
    var me                  = this;
    var intervalLength      = 0;  //we have to calc this...
    var snap                = false;
    var amount              = 0;
    var maxScale            = 0;
    var inclusive           = false;
    this.ref                = (typeof div === 'string') ? document.getElementById(div) : div;
    if (!this.ref) {
        alert("Slider: "+div+" Not Found");
        return;
    }
    this.ref.style.position = "relative";
    this.slideId            = (optId) ? optId : "_slide_"+new Date().getTime();
    this.divId              = this.ref.id;
    this.defaults           =    {
        gradual:    false,
        increment:    1,
        height:        15,
        width:        300
    };
    this.sliderWidth        = (len) ? len : this.defaults.width;
    this.sliderHeight       = (hgt) ? hgt : this.defaults.height;
    this.gradual            = false;
    this.stops              = [];
    this.pointers           = [];
    this.stopClass          = "";
    this.labelClass         = "";
    this.rangeClass         = "";
    this.slideRanges        = false;
    this.rangeDirection     = "right";
    this.slideClass         = "";
    this.stopText           = "*";
    this.stopImage          = "";
    this.hash               = false;
    this.slider             = null;
    this.slide              = null;
    this.saveLocation       = null;
    this.restoreFlag        = false;
    this.calcIntervalLength = () => {
        var inclusiveOffset = (this.getInclusive()) ? -1 : 1;
        intervalLength = Math.round(this.getSliderWidth()/(this.stops.length+inclusiveOffset));
        return intervalLength;
    };
    this.setAmount      = (amt) => {
        amount = amt;
        return me;
    };
    this.setMaxScale    = (max) => {
        maxScale = max;
        return me;
    };
    this.setAxis        = (start,stop,maxScale,byAmt) => {
        byAmt = (byAmt) ? byAmt : 1;
        if (maxScale) {
            this.setMaxScale(maxScale);
        }
        for (var i=start; i<=stop; i=i+byAmt) {
            this.addStop(this.divId+"_"+i,i);
        }
        return me;
    };
    this.setScale       = (start,stop,stops) =>  {
        this.maxScale   = stop;
        var interval    = Math.round(((stop-start)/(stops))*100)/100;
        for (var i=0; i<stops; i++) {
            this.stops[this.stops.length] = {
                id:       'stopId_'+i,
                returns:  start,
                location: null,
                label:    ''
            };
            start += interval;
        }
        return me;
    };
    this.getInterval    = () => { return intervalLength;   };
    this.getAmount	= () => { return amount;			};
    this.getMaxScale	= () => { return maxScale;			};
    this.getPercent	= () => {
        return Math.round((amount/this.sliderWidth)*100);
    };
    this.getSliderWidth = () => {
        return (parseInt(this.sliderWidth) === this.sliderWidth) ? this.sliderWidth : this.ref.offsetWidth;
    };
    this.getValue       = () => {
        var ret = '';
        if (this.hash) {
            for (var i = 0; i < this.stops.length; i++) {
                ret = (this.getAmount() === this.stops[i].location) ? this.stops[i].returns : ret;
            }
        } else {
            ret = Math.round((amount / this.getSliderWidth()) * maxScale);
        }
        return ret;
    };
    this.setInclusive       = (bool) => { inclusive = bool; return me;    };
    this.getInclusive       = () => { return inclusive;             };
    this.onSlideStart       = null;
    this.onSlide            = null;
    this.onSlideStop        = null;
    this.canClick           = false;
    this.setSnap            = (bool) => { snap = bool; return me;        };
    this.getIntervalLength  = () =>  { return intervalLength;        };
    this.getSnap            = () =>  { return snap;                    };
    this.setSlideRanges     = (bool) => {
        this.slideRanges         = bool;
        return this;
    };
    this.setRangeDirection  = (text) => {
        this.rangeDirection = text;
        return this;
    };
    this.setStopText        = (text) => {
        this.stopText       = text;
        return this;
    };
    this.setStopImage       = (image) => {
        this.stopImage      = image;
        return this;
    };
    this.setRangeClass      = (className) => {
        this.rangeClass     = className;
        return this;
    };
    this.setSlideClass      = (className) => {
        this.slideClass     = className;
        return this;
    }   ; 
    this.setStopClass       = (className) => {
        this.stopClass      = className;
        return this;
    };
    this.setLabelClass      = (className) => {
        this.labelClass     = className;
        return this;
    };
    this.setOnSlideStart    = (handler) => {
        this.onSlideStart   = handler;
        return this;
    };
    this.setOnSlide         = (handler) => {
        this.onSlide        = handler;
        return this;
    };
    this.setCanClick        = (bool) => {
        this.canClick       = bool;
        return this;
    };
    this.setOnSlideStop     = (handler) => {
        this.onSlideStop    = handler;
        return this;
    };
    this.addStop = this.add = (stopId,txt,retVal) => {
        if (retVal) {
            this.hash = true;
        }
        this.stops[this.stops.length] = {
            id:         stopId,
            returns:    ((retVal) ? retVal : null),
            location:   null,
            label:      txt
        };
        return this;
    };
    this.setPointerTitle    = (pointerId,title) => {
        for (var i=0; i<this.pointers.length; i++) {
            if (pointerId === this.pointers[i].id) {
                document.getElementById(this.pointers[i].id).title = title;
            }
        }
        return this;
    };
    /* Will restore the slider to the previous state before sliding began */
    this.restore            = () => {
        this.restoreFlag    = true;
        this.setSliderTo(this.saveLocation,false);
        return this;
    };
    /* Saves the state of a slider before the sliding begins */
    this.save               = () => {
        this.saveLocation = this.getAmount();
        return this;
    };
    this.setPointer         = (whichOne,amount,triggerEvent) => {
        var where = amount/100;  //if percent
        if (this.getMaxScale() || (amount > 100)) {
            where = (amount/this.getMaxScale()); //if not percent
        }
        where = Math.round(document.getElementById(this.slideId).offsetWidth * where);
        this.setAmount(where);
        if (this.slideRanges)  {
            document.getElementById(whichOne+"_range").style.width = where+"px";
            document.getElementById(whichOne+"_range").style.display = "block";
        }
        document.getElementById(whichOne).style.left = (where - (Math.round(document.getElementById(whichOne).offsetWidth / 2)))+"px";
        this.save();
        if (this.onSlideStart) {
            this.onSlideStart(document.getElementById(whichOne), document.getElementById(this.slideId), where);
        }
        if (this.onSlide) {
            this.onSlide(document.getElementById(whichOne), document.getElementById(this.slideId), where);
        }
        if (!(triggerEvent === false)) {
            if (this.onSlideStop) {
                this.onSlideStop(document.getElementById(whichOne), document.getElementById(this.slideId), where);
            }
        }
        return this;
    };
    this.setSliderTo            = (offset,triggerEvent) => {
        var maxScale = (this.getMaxScale()) ? this.getMaxScale() : 100;
        var perc     = Math.round(((offset/this.getSliderWidth()) * maxScale));
        this.setPointer(this.pointers[0].id,perc,triggerEvent);
        return this;
    };
    this.setSliderToValue	= (val,triggerEvent) => {
        var setTo = 0;
        if (typeof val ==="string") {
            for (var i=0; i<this.stops.length; i++) {
                setTo = (this.stops[i].returns === val) ? this.stops[i].location : setTo;
            };
        }
        this.setSliderTo(setTo,triggerEvent);
        return this;
    };
    this.addPointer             = (pointerId,image,rangeColor,style) => {
        this.pointers[this.pointers.length] =     {
            slide: this.ref,
            id:    pointerId,
            image: image,
            style: (style) ? style : '',
            rangeColor: rangeColor,
            ref:    null
        };
        return this;
    };
    this.render                 = () => {
        var html = "";
        var interval = this.calcIntervalLength();
        var slideId  = this.slideId;
        if (this.slideClass)  {
            html += '<div id="'+slideId+'" class="' + this.slideClass + '" style="height: ' + this.sliderHeight + 'px; width: ' + this.sliderWidth + 'px;"></div>';
        } else {
            html += '<div id="'+slideId+'" style="height: ' + this.sliderHeight + 'px; width: ' + this.sliderWidth + 'px; z-index: 1; margin-left: 0px; border: 1px solid #888; background-color: silver; "></div>';
        }
        if (this.slideRanges)  {
            for (var i=0; i<this.pointers.length; i++)  {
                if (this.rangeClass) {
                    html += '<div id="'+this.pointers[i].id+'_range" class="' + this.rangeClass + '" style="height: ' + this.sliderHeight + 'px; width: ' + this.sliderWidth + 'px;  background-color: '+this.pointers[i].rangeColor+'; position: absolute; display: none"></div>';
                } else {
                    if (this.slideClass) {
                        html += '<div id="'+this.pointers[i].id+'_range" class="' + this.slideClass + '" style="height: ' + this.sliderHeight + 'px; width: ' + this.sliderWidth + 'px;  background-color: '+this.pointers[i].rangeColor+'; position: absolute; display: none"></div>';
                    } else {
                        html += '<div id="'+this.pointers[i].id+'_range" style="height: ' + this.sliderHeight + 'px; width: ' + this.sliderWidth + 'px; z-index: 1; margin-left: 0px; border: 1px solid #888; background-color: '+this.pointers[i].rangeColor+'; position: absolute; display: none"></div>';
                    }
                }
            }
        }
        for (var i=0; i<this.stops.length; i++)  {
            var inclusiveOffset = (this.getInclusive()) ? 0 : 1;
            this.stops[i].location = (interval*(i+inclusiveOffset));
            this.stops[i].placement = this.stops[i].location - EasySlider.Control.getLabelOffset(this.stopClass,this.stopText);
            html += '<div id="'+this.stops[i].id+'" style="position: absolute; z-index: 2; padding: 0px; margin: 0px; top: 0px; left: '+this.stops[i].placement+'px"><span class="'+this.stopClass+'">'+this.stopText+'</span></div>';
            html += '<div id="'+this.stops[i].id+'_label" style="position: absolute; z-index: '+(this.pointers.length+1)+'; top: '+(this.sliderHeight+2)+'px; left: '+((interval*(i+inclusiveOffset)) - EasySlider.Control.getLabelOffset(this.labelClass,this.stops[i].label))+'px"><span class="'+this.labelClass+'">'+this.stops[i].label+'</span></div>';
        }
        for (var i=0; i<this.pointers.length; i++) {
            html += '<img id="'+this.pointers[i].id+'" src="'+this.pointers[i].image+'" class="'+this.pointers[i].style+'"style="cursor: pointer; position: absolute; z-index: '+(i+2)+'; top: -2px; left: '+(i*20)+'px;" />';
        }
        this.ref.innerHTML = html;
        if ((this.canClick) && (this.getMaxScale())) {
            document.getElementById(slideId).onclick = EasySlider.Control.handleSlideClick;
        }
        for (var i=0; i<this.pointers.length; i++)  {
            var ref = this.pointers[i].ref = document.getElementById(this.pointers[i].id);
            ref.onSlide         = this.onSlide;
            ref.onSlideStop     = this.onSlideStop;
            ref.setAttribute("slide",slideId);
            ref.setAttribute("slideRange",((this.slideRanges) ? this.pointers[i].id+"_range" : null));
            ref.setAttribute("rangeDirection",((this.slideRanges) ? this.rangeDirection : null));
            ref.setAttribute("slideWidget",slideId);
            if (this.slideRanges) {
                document.getElementById(this.pointers[i].id + "_range").style.top     = document.getElementById(slideId).offsetTop+"px";
                document.getElementById(this.pointers[i].id + "_range").style.left    = document.getElementById(slideId).offsetLeft+"px";
            }
            if (window.addEventListener) {
                ref.addEventListener("mousedown",EasySlider.Control.mouseDown,false);
            } else {
                ref.onmousedown    = EasySlider.Control.mouseDown;
            }
            ref.style.left = -(Math.round(ref.offsetWidth/2))+"px";
        }
        return this;
    };
    return EasySliders[this.slideId] = me;
}
EasySlider.Control = (() => {
    return {
        sizer:      null,
        pageX:      0,
        pageY:      0,
        clickX:     0,
        clickY:     0,
        active:     null,
        slide:      null,
        splitDif:   null,
        zIndex:     0,
        range:      null,
        activeRange: null,
        direction:  null,
        init:               () => {
            if (!document.getElementById("hiddenSizingLayer")) {
                var div     = document.createElement('div');
                div.id      = 'hiddenSizingLayer';
                div.style   = 'position: absolute; top: -100px; left: -100px; width: 1px; overflow: auto; height: 20px';
                document.body.after(div);
            } 
            this.sizer     = document.getElementById("hiddenSizingLayer");

        },
        getLabelOffset:    (className,text) => {
            this.sizer.className = className;
            this.sizer.innerHTML = text;
            return Math.floor(this.sizer.scrollWidth/2);
        },
        adjustSlideRange:   () => {
            var slider = EasySlider.Control.active;
            var range  = EasySlider.Control.activeRange;
            if (!range) {
                return;
            }
            var slide    = EasySlider.Control.slide;
            var splitDif = EasySlider.Control.splitDif;
            var dir      = EasySlider.Control.direction;
            var fromLeft = +slider.offsetLeft + splitDif;
            if (dir === "left") {
                range.style.left = slide.offsetLeft+"px";
                range.style.width = fromLeft+"px";
            } else {
                range.style.left = fromLeft+"px";
                range.style.width = (slide.offsetWidth-fromLeft)+"px";
            }

        },
        mouseDown:      (evt) => {
            evt = (evt) ? evt : ((window.event) ? event : null);
            var s = evt.target;
            if (s) {
                EasySlider.Control.active    = document.getElementById(s.id);
                EasySlider.Control.zIndex    = EasySlider.Control.active.style.zIndex;
                EasySlider.Control.active.style.zIndex = 999;
                EasySlider.Control.slideName = EasySlider.Control.active.getAttribute("slide");
                EasySlider.Control.slide     = document.getElementById(EasySlider.Control.slideName);
                var slider = EasySliders[EasySlider.Control.slideName];
                EasySlider.Control.range     = EasySlider.Control.active.getAttribute("slideRange");
                EasySlider.Control.direction = EasySlider.Control.active.getAttribute("rangeDirection");
                EasySlider.Control.splitDif  = Math.round(EasySlider.Control.active.offsetWidth/2);
                EasySlider.Control.widget    = EasySliders[EasySlider.Control.active.getAttribute("slideWidget")];
                var fromLeft = +EasySlider.Control.active.offsetLeft + EasySlider.Control.splitDif;
                EasySlider.Control.active.style.top = "-4px";
                if (slider.onSlideStart) {
                    slider.onSlideStart(this,document.getElementById(EasySlider.Control.slideName),fromLeft);
                }
                if (window.addEventListener) {
                    EasySlider.Control.pageX = pageXOffset;
                    EasySlider.Control.pageY = pageYOffset;
                    EasySlider.Control.active.removeEventListener("mousedown", EasySlider.Control.mouseDown, false);
                    document.addEventListener("mousemove", EasySlider.Control.mouseMove, false);
                    document.addEventListener("mouseup", EasySlider.Control.mouseUp, false);
                    evt.preventDefault();
                } else {
                    var iebody    = (document.documentElement) ? document.documentElement : document.body;
                    EasySlider.Control.pageX  = iebody.scrollLeft;
                    EasySlider.Control.pageY  = iebody.scrollTop;
                    EasySlider.Control.active.onmousedown    = null;
                    document.onmousemove = EasySlider.Control.mouseMove;
                    document.onmouseup   = EasySlider.Control.mouseUp;
                }
                EasySlider.Control.clickX    = +EasySlider.Control.pageX  + evt.clientX;
                EasySlider.Control.clickY    = +EasySlider.Control.pageY  + evt.clientY;
                EasySlider.Control.dX        = (evt.clientX- EasySlider.Control.active.offsetLeft);
                if ((EasySlider.Control.range) && (EasySlider.Control.direction)) {
                    EasySlider.Control.activeRange = document.getElementById(EasySlider.Control.range);
                    if (EasySlider.Control.activeRange) {
                        EasySlider.Control.activeRange.style.display = "block";
                        EasySlider.Control.adjustSlideRange();
                    }
                }
            }
            return false;
        },
        mouseMove:          (evt) => {
            evt = (evt) ? evt : ((window.event) ? event : null);
            if (EasySlider.Control.active) {
                var newX = (+EasySlider.Control.pageX + +evt.clientX - EasySlider.Control.dX);
                if ((newX < (EasySlider.Control.slide.offsetWidth - EasySlider.Control.splitDif)) && (newX > (EasySlider.Control.splitDif * -1))) {
                    EasySlider.Control.active.style.left = newX + "px";
                    if (EasySlider.Control.active.onSlide) {
                        EasySlider.Control.active.onSlide(EasySlider.Control.active,EasySlider.Control.slide,newX);
                    }
                    EasySlider.Control.widget.setAmount(newX+EasySlider.Control.splitDif);
                    if ((EasySlider.Control.range) && (EasySlider.Control.direction)) {
                        EasySlider.Control.activeRange = document.getElementById(EasySlider.Control.range);
                        if (EasySlider.Control.activeRange) {
                            EasySlider.Control.activeRange.style.display = "block";
                            EasySlider.Control.adjustSlideRange();
                        }
                    }
                }
            }
            return false;
        },
        mouseUp:            (evt) =>  {
            evt = (evt) ? evt : ((window.event) ? event : null);
            if (EasySlider.Control.active) {
                EasySlider.Control.active.style.top = "-2px";
                EasySlider.Control.active.style.zIndex = EasySlider.Control.zIndex;
                var fromLeft = +EasySlider.Control.active.offsetLeft + EasySlider.Control.splitDif;
                var delta = false;
                if (EasySlider.Control.widget.getSnap()) {
                    delta = EasySlider.Control.active.style.left = (Math.round((EasySlider.Control.active.offsetLeft+EasySlider.Control.splitDif)/EasySlider.Control.widget.getInterval()) * EasySlider.Control.widget.getInterval() - EasySlider.Control.splitDif)+"px";
                    fromLeft = +EasySlider.Control.active.offsetLeft + EasySlider.Control.splitDif;
                }
                EasySlider.Control.widget.setAmount(fromLeft);
                if (delta) {
                    if (EasySlider.Control.active.onSlide) {
                        EasySlider.Control.active.onSlide(EasySlider.Control.active,EasySlider.Control.slide,fromLeft);
                    }
                }
                if (EasySlider.Control.active.onSlideStop) {
                    EasySlider.Control.active.onSlideStop(EasySlider.Control.active,EasySlider.Control.slide,fromLeft);
                }

                if (window.addEventListener) {
                    document.removeEventListener("mouseup",EasySlider.Control.mouseUp,false);
                    document.removeEventListener("mousemove",EasySlider.Control.mouseMove,false);
                    EasySlider.Control.active.addEventListener("mousedown",EasySlider.Control.mouseDown,false);
                } else {
                    document.onmousemove     = null;
                    document.onmouseup         = null;
                    EasySlider.Control.active.onmousedown = EasySlider.Control.mouseDown;
                }
                if ((EasySlider.Control.range) && (EasySlider.Control.direction)) {
                    EasySlider.Control.activeRange = document.getElementById(EasySlider.Control.range);
                    if (EasySlider.Control.activeRange) {
                        EasySlider.Control.activeRange.style.display = "block";
                        EasySlider.Control.adjustSlideRange();
                    }
                }
            }
            EasySlider.Control.splitDif  = null;
            EasySlider.Control.active    = null;
            EasySlider.Control.slide     = null;
            return false;
        },
        handleSlideClick:       (evt) => {
            evt = (evt) ? evt : ((window.event) ? event : null);
            var slideId = (evt.target) ? evt.target.id : evt.srcElement.id;
            var offset = evt.clientX  - EasyEdits.getAbsoluteX(document.getElementById(slideId),"BODY");
            EasySliders[slideId].setSliderTo(offset);
            return this;
        }        
    };
})();
