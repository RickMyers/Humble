/**
 *
 * Sliders, part of the Cloud-IT project.
 *
 * @author: Rick Myers <rick@humblecoding.com>
 *
 *
 */
var Sliders        = [];
function Slider(divId,len,hgt,optId) {
    sliderControl.init();   //go set the sizer
    var me                  = this;
    var intervalLength      = 0;  //we have to calc this...
    var snap                = false;
    var amount              = 0;
    var maxScale            = 0;
    var inclusive           = false;
    this.ref                = $E(divId);
    if (!this.ref) {
        alert("Slider: "+divId+" Not Found");
        return;
    }
    this.ref.style.position = "relative";
    this.slideId            = (optId) ? optId : "_slide_"+new Date().getTime();
    this.divId              = divId;
    this.defaults           =    {
                                    gradual:    false,
                                    increment:    1,
                                    height:        15,
                                    width:        300
                                }
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
	this.hash				= false;
	this.slider 			= null;
	this.slide				= null;
    this.saveLocation       = null;
    this.restoreFlag        = false;
    this.calcIntervalLength = function () {
        var inclusiveOffset = (this.getInclusive()) ? -1 : 1;
        intervalLength = Math.round(this.getSliderWidth()/(this.stops.length+inclusiveOffset));
        return intervalLength;
    }
    this.setAmount            = function (amt) {
        amount = amt;
        return me;
    }
    this.setMaxScale        = function (max) {
        maxScale = max;
        return me;
    }
    this.setAxis            = function (start,stop,maxScale,byAmt) {
        byAmt = (byAmt) ? byAmt : 1;
        if (maxScale) {
            this.setMaxScale(maxScale);
        }
        for (var i=start; i<=stop; i=i+byAmt) {
            this.addStop(this.divId+"_"+i,i);
        }
        return me;
    }
    this.setScale            = function (start,stop,stops)  {
        this.maxScale=stop;
        var interval = Math.round(((stop-start)/(stops))*100)/100;
        for (var i=0; i<stops; i++) {
            this.stops[this.stops.length] = {
                                            id:     'stopId_'+i,
                                            returns: start,
                                            location: null,
                                            label:     ''
                                        };
            start += interval;
        }
        return me;
    }
    this.getInterval        = function ()  { return intervalLength;   }
    this.getAmount			= function ()  { return amount;			}
    this.getMaxScale		= function ()  { return maxScale;			}
    this.getPercent			= function ()  {
        return Math.round((amount/this.sliderWidth)*100);
    }
    this.getSliderWidth     = function () {
        return (parseInt(this.sliderWidth) == this.sliderWidth) ? this.sliderWidth : this.ref.offsetWidth;
    }
    this.getValue            = function () {
		var ret = '';
		if (this.hash) {
			for (var i = 0; i < this.stops.length; i++) {
				ret = (this.getAmount() == this.stops[i].location) ? this.stops[i].returns : ret;
            }
        } else{
			ret = Math.round((amount / this.getSliderWidth()) * maxScale);
        }
        return ret;
    }


    this.setInclusive       = function (bool) { inclusive = bool; return me;    }
    this.getInclusive       = function ()     { return inclusive;             }
    this.onSlideStart       = null;
    this.onSlide            = null;
    this.onSlideStop        = null;
    this.canClick           = false;
    this.setSnap            = function (bool) { snap = bool; return me;        }
    this.getIntervalLength  = function ()     { return intervalLength;        }
    this.getSnap            = function ()     { return snap;                    }
    return Sliders[this.slideId] = me;
}
Slider.prototype.setSlideRanges    = function (bool) {
    this.slideRanges         = bool;
    return this;
}
Slider.prototype.setRangeDirection    = function (text) {
    this.rangeDirection         = text;
    return this;
}
Slider.prototype.setStopText    = function (text) {
    this.stopText         = text;
    return this;
}
Slider.prototype.setStopImage    = function (image) {
    this.stopImage         = image;
    return this;
}
Slider.prototype.setRangeClass    = function (className) {
    this.rangeClass        = className;
    return this;
}
Slider.prototype.setSlideClass    = function (className) {
    this.slideClass         = className;
    return this;
}
Slider.prototype.setStopClass    = function (className) {
    this.stopClass         = className;
    return this;
}
Slider.prototype.setLabelClass    = function (className) {
    this.labelClass     = className;
    return this;
}
Slider.prototype.setOnSlideStart    = function (handler) {
    this.onSlideStart    = handler;
    return this;
}
Slider.prototype.setOnSlide        = function (handler) {
    this.onSlide         = handler;
    return this;
}
Slider.prototype.setCanClick    = function (bool) {
    this.canClick        = bool;
    return this;
}
Slider.prototype.setOnSlideStop    = function (handler) {
    this.onSlideStop     = handler;
    return this;
}
Slider.prototype.addStop = Slider.prototype.add    = function (stopId,txt,retVal) {
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
}
Slider.prototype.setPointerTitle = function (pointerId,title) {
    for (var i=0; i<this.pointers.length; i++) {
        if (pointerId == this.pointers[i].id) {
            $E(this.pointers[i].id).title = title;
        }
    }
}
/* Will restore the slider to the previous state before sliding began */
Slider.prototype.restore = function () {
    this.restoreFlag = true;
    this.setSliderTo(this.saveLocation,false);
}
/* Saves the state of a slider before the sliding begins */
Slider.prototype.save = function () {
    this.saveLocation = this.getAmount();
}
Slider.prototype.setPointer = function (whichOne,amount,triggerEvent) {
    var where = amount/100;  //if percent
    if (this.getMaxScale() || (amount > 100)) {
        where = (amount/this.getMaxScale()); //if not percent
    }
    where = Math.round($E(this.slideId).offsetWidth * where);
    this.setAmount(where);
    if (this.slideRanges)  {
        $E(whichOne+"_range").style.width = where+"px"
        $E(whichOne+"_range").style.display = "block";
    }
    $E(whichOne).style.left = (where - (Math.round($E(whichOne).offsetWidth / 2)))+"px";
    this.save();
    if (this.onSlideStart) {
        this.onSlideStart($E(whichOne), $E(this.slideId), where);
    }
    if (this.onSlide) {
        this.onSlide($E(whichOne), $E(this.slideId), where);
    }
    if (!(triggerEvent === false)) {
        if (this.onSlideStop) {
            this.onSlideStop($E(whichOne), $E(this.slideId), where);
        }
    }

}
Slider.prototype.setSliderTo    = function (offset,triggerEvent) {
	var maxScale = (this.getMaxScale()) ? this.getMaxScale() : 100;
    var perc = Math.round( ((offset/this.getSliderWidth()) * maxScale));

    this.setPointer(this.pointers[0].id,perc,triggerEvent);

}
Slider.prototype.setSliderToValue	= function (val,triggerEvent) {
	var setTo = 0;
	if (typeof(val)=="string")
		for (var i=0; i<this.stops.length; i++) {
			setTo = (this.stops[i].returns == val) ? this.stops[i].location : setTo;
        }
	this.setSliderTo(setTo,triggerEvent);
}
Slider.prototype.addPointer    = function (pointerId,image,rangeColor,style) {
    this.pointers[this.pointers.length] =     {
                                                slide: this.ref,
                                                id:        pointerId,
                                                image:    image,
                                                style:    (style) ? style : '',
                                                rangeColor: rangeColor,
                                                ref:    null
                                            }
    return this;
}
Slider.prototype.render    = function () {
    var html = "";
    var interval = this.calcIntervalLength();
    var slideId = this.slideId;
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
		this.stops[i].placement = this.stops[i].location - sliderControl.getLabelOffset(this.stopClass,this.stopText);
        html += '<div id="'+this.stops[i].id+'" style="position: absolute; z-index: 2; padding: 0px; margin: 0px; top: 0px; left: '+this.stops[i].placement+'px"><span class="'+this.stopClass+'">'+this.stopText+'</span></div>';
        html += '<div id="'+this.stops[i].id+'_label" style="position: absolute; z-index: '+(this.pointers.length+1)+'; top: '+(this.sliderHeight+2)+'px; left: '+((interval*(i+inclusiveOffset))-sliderControl.getLabelOffset(this.labelClass,this.stops[i].label))+'px"><span class="'+this.labelClass+'">'+this.stops[i].label+'</span></div>';
    }
    for (var i=0; i<this.pointers.length; i++) {
        html += '<img id="'+this.pointers[i].id+'" src="'+this.pointers[i].image+'" class="'+this.pointers[i].style+'"style="cursor: pointer; position: absolute; z-index: '+(i+2)+'; top: -2px; left: '+(i*20)+'px;" />';
    }
    this.ref.innerHTML = html;
    if ((this.canClick) && (this.getMaxScale())) {
        $E(slideId).onclick = sliderControl.handleSlideClick;
    }
    for (var i=0; i<this.pointers.length; i++)  {
        var ref = this.pointers[i].ref = $E(this.pointers[i].id);
        ref.onSlide         = this.onSlide;
        ref.onSlideStop     = this.onSlideStop;
        ref.setAttribute("slide",slideId);
        ref.setAttribute("slideRange",((this.slideRanges) ? this.pointers[i].id+"_range" : null));
        ref.setAttribute("rangeDirection",((this.slideRanges) ? this.rangeDirection : null));
        ref.setAttribute("slideWidget",slideId);
        if (this.slideRanges) {
            $E(this.pointers[i].id + "_range").style.top     = $E(slideId).offsetTop+"px";
            $E(this.pointers[i].id + "_range").style.left     = $E(slideId).offsetLeft+"px";
        }
        if (window.addEventListener) {
            ref.addEventListener("mousedown",sliderControl.mouseDown,false);
        } else {
            ref.onmousedown    = sliderControl.mouseDown;
        }
        ref.style.left = -(Math.round(ref.offsetWidth/2))+"px";
    }
    return this
}
//-------------------------------------------------------------------------------------------------
var sliderControl = {
    sizer:        null,
    pageX:        0,
    pageY:        0,
    clickX:        0,
    clickY:        0,
    active:        null,
    slide:        null,
    splitDif:    null,
    zIndex:     0,
    range:        null,
    activeRange: null,
    direction:  null,
    init:        function () {
        if (!$E("hiddenSizingLayer")) {
            alert("The sizing layer is missing");
            //document.body.innerHTML += '<div id="hiddenSizingLayer" style="width: 0px; overflow: auto; visibility: hidden; position: absolute; top: -40px; white-space: nowrap"></div>';
        } else {
            this.sizer     = $E("hiddenSizingLayer");
        }
    },
    getLabelOffset:        function (className,text)   {
        this.sizer.className = className;
        this.sizer.innerHTML = text;
        return Math.floor(this.sizer.scrollWidth/2);
    },
    adjustSlideRange: function () {
        var slider = sliderControl.active;
        var range  = sliderControl.activeRange;
        if (!range) {
            return;
        }
        var slide  = sliderControl.slide;
        var splitDif = sliderControl.splitDif;
        var dir      = sliderControl.direction;
        var fromLeft = +slider.offsetLeft + splitDif;
        if (dir == "left") {
            range.style.left = slide.offsetLeft+"px";
            range.style.width = fromLeft+"px";
        } else {
            range.style.left = fromLeft+"px";
            range.style.width = (slide.offsetWidth-fromLeft)+"px";
        }

    },
    mouseDown:    function (evt) {
        evt = (evt) ? evt : ((window.event) ? event : null);
        if (this.id) {
            sliderControl.active    = $E(this.id);
            sliderControl.zIndex    = sliderControl.active.style.zIndex;
            sliderControl.active.style.zIndex = 999;
            sliderControl.slideName     = sliderControl.active.getAttribute("slide");
            sliderControl.slide     = $E(sliderControl.slideName);
            var slider = Sliders[sliderControl.slideName];
            sliderControl.range     = sliderControl.active.getAttribute("slideRange");
            sliderControl.direction = sliderControl.active.getAttribute("rangeDirection");
            sliderControl.splitDif  = Math.round(sliderControl.active.offsetWidth/2);
            sliderControl.widget    = Sliders[sliderControl.active.getAttribute("slideWidget")];
            var fromLeft = +sliderControl.active.offsetLeft + sliderControl.splitDif;
            sliderControl.active.style.top = "-4px";
            if (slider.onSlideStart) {
                slider.onSlideStart(this,$E(sliderControl.slideName),fromLeft);
            }
            if (window.addEventListener) {
                sliderControl.pageX = pageXOffset;
                sliderControl.pageY = pageYOffset;
                sliderControl.active.removeEventListener("mousedown", sliderControl.mouseDown, false);
                document.addEventListener("mousemove", sliderControl.mouseMove, false);
                document.addEventListener("mouseup", sliderControl.mouseUp, false);
                evt.preventDefault();
            } else {
                var iebody    = (document.documentElement) ? document.documentElement : document.body
                sliderControl.pageX  = iebody.scrollLeft;
                sliderControl.pageY  = iebody.scrollTop;
                sliderControl.active.onmousedown    = null;
                document.onmousemove = sliderControl.mouseMove;
                document.onmouseup   = sliderControl.mouseUp;
            }
            sliderControl.clickX    = +sliderControl.pageX  + evt.clientX;
            sliderControl.clickY    = +sliderControl.pageY  + evt.clientY;
            sliderControl.dX        = (evt.clientX- sliderControl.active.offsetLeft);
            if ((sliderControl.range) && (sliderControl.direction)) {
                sliderControl.activeRange = $E(sliderControl.range);
                if (sliderControl.activeRange) {
                    sliderControl.activeRange.style.display = "block";
                    sliderControl.adjustSlideRange();
                }
            }
        }
        return false;
    },
    mouseMove:    function (evt) {
        evt = (evt) ? evt : ((window.event) ? event : null);
        if (sliderControl.active) {
            var newX = (+sliderControl.pageX + +evt.clientX - sliderControl.dX);
            if ((newX < (sliderControl.slide.offsetWidth - sliderControl.splitDif)) && (newX > (sliderControl.splitDif * -1))) {
                sliderControl.active.style.left = newX + "px";
                if (sliderControl.active.onSlide) {
                    sliderControl.active.onSlide(sliderControl.active,sliderControl.slide,newX);
                }
                sliderControl.widget.setAmount(newX+sliderControl.splitDif);
                if ((sliderControl.range) && (sliderControl.direction)) {
                    if (sliderControl.activeRange = $E(sliderControl.range)) {
                        sliderControl.activeRange.style.display = "block";
                        sliderControl.adjustSlideRange();
                    }
                }
            }
        }
        return false;
    },
    mouseUp:        function (evt)  {
        evt = (evt) ? evt : ((window.event) ? event : null);
        if (sliderControl.active) {
            sliderControl.active.style.top = "-2px";
            sliderControl.active.style.zIndex = sliderControl.zIndex;
            var fromLeft = +sliderControl.active.offsetLeft + sliderControl.splitDif;
            var delta = false;
            if (sliderControl.widget.getSnap()) {
                console.log('snapping');
                delta = sliderControl.active.style.left = (Math.round((sliderControl.active.offsetLeft+sliderControl.splitDif)/sliderControl.widget.getInterval()) * sliderControl.widget.getInterval() - sliderControl.splitDif)+"px";
                console.log(delta);
                fromLeft = +sliderControl.active.offsetLeft + sliderControl.splitDif;
            }
            sliderControl.widget.setAmount(fromLeft);
            if (delta) {
                if (sliderControl.active.onSlide) {
                    sliderControl.active.onSlide(sliderControl.active,sliderControl.slide,fromLeft);
                }
            }
            if (sliderControl.active.onSlideStop) {
                sliderControl.active.onSlideStop(sliderControl.active,sliderControl.slide,fromLeft);
            }

            if (window.addEventListener) {
                document.removeEventListener("mouseup",sliderControl.mouseUp,false);
                document.removeEventListener("mousemove",sliderControl.mouseMove,false);
                sliderControl.active.addEventListener("mousedown",sliderControl.mouseDown,false);
            } else {
                document.onmousemove     = null;
                document.onmouseup         = null;
                sliderControl.active.onmousedown = sliderControl.mouseDown;
            }
            if ((sliderControl.range) && (sliderControl.direction)) {
                if (sliderControl.activeRange = $E(sliderControl.range)) {
                    sliderControl.activeRange.style.display = "block";
                    sliderControl.adjustSlideRange();
                }
            }
        }
        sliderControl.splitDif  = null;
        sliderControl.active    = null;
        sliderControl.slide     = null;
        return false;
    },
    handleSlideClick:        function (evt) {
        evt = (evt) ? evt : ((window.event) ? event : null);
        var slideId = (evt.target) ? evt.target.id : evt.srcElement.id;
        var offset = evt.clientX  - EasyEdits.getAbsoluteX($E(slideId),"BODY");
        Sliders[slideId].setSliderTo(offset);
    }
}
