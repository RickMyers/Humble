/*
 *
 * A Javascript Carousel (part of the Cloud-IT project)
 *
 * Author: Rick Myers
 *
 * Copyright, 2010, all rights reserved.
 *
 *
 */
var Carousels = [];
function Carousel(id) {
    this.items	 = [];
    this.regions = [];
    this.spacer  = 0;
    this.radian  = 0.0174532925;
    this.timer	 = null;
    this.vector  = 5;
    this.axis	 = 0;
    this.baseLayer = 100;
    this.delta	 = 0;
    this.speed   = 50;
    this.rotating = false;
    this.pathHeight = null;
    this.pathWidth	= null;
    this.applyFog	= false;
    this.slice  = 0;  //
    this.sliceX = 0;
    this.jump	= 1;
    if (document.getElementById(id)) {
        this.id		 = id;
        this.ref	 = document.getElementById(id);
        this.ref.style.position = "relative";
    }
    else return null;
    return Carousels[id] = this;
}
Carousel.prototype.add	= function (what,reflect) {
    var idx = this.items.length;
    this.items[idx] = {
        uri: what,
        id: 'image_'+idx,
        ref: null,
        degree: 0,
        posX: 0,
        reflection: reflect,
        posY: 0
    }
    var image  = new Image();
    image.src   = what;
    image.id    = 'image_'+idx
    image.style = 'height: 100px;';
    this.ref.appendChild(image);
    this.items[idx].ref = document.getElementById('image_'+idx)
    return this;
}
Carousel.prototype.inject = function (element,reflect) {
    var tileId = "cr_"+(new Date()).getTime()
    var HTML = "<div style='position: relative' id='"+tileId+"'>"+element+"</div>";
    this.ref.innerHTML += HTML;
    this.add(tileId,reflect);
    return this;
}
Carousel.prototype.jump = function (whichOne) {
    this.jump = whichOne;
}
Carousel.prototype.spin	= function () {
	if (!this.rotating) {
            this.sliceX = Math.abs(Math.round(this.slice/this.delta)) * this.jump;
	}
	this.rotating = true;
	for (var i=0; i<this.items.length; i++) {
            this.items[i].degree = this.items[i].degree + this.delta;
            if (this.items[i].degree > 360)
                this.items[i].degree = this.items[i].degree - 360;
            else if (this.items[i].degree < 0)
                this.items[i].degree = this.items[i].degree + 360;
            var zOffset		= Math.abs(((this.items[i].degree > 180) ? 360 - this.items[i].degree : this.items[i].degree));
            var direction	= (this.items[i].degree > 180) ? -1 : 1;   //zMultiplier
            var arcDeg		= Math.ceil(((zOffset>90) ? 180 - zOffset : zOffset)%90);
            this.items[i].ref.style.zIndex  =  this.axis + this.baseLayer + (direction * arcDeg);
            this.items[i].ref.style.left    = (this.items[i].posX = (this.center.X+ Math.round(Math.cos(this.items[i].degree * this.radian)*this.pathWidth)-Math.round(this.items[i].ref.offsetWidth/2)))+"px";
            this.items[i].ref.style.top     = (this.items[i].posY = this.center.Y + Math.round(Math.sin(this.items[i].degree * this.radian)*this.pathHeight))+"px";
	}
	this.sliceX = this.sliceX - 1;
	if (this.sliceX>0) {
            var me = this;
            var tt = function () {
                me.spin();
            }
            this.timer = window.setTimeout(tt,this.speed)
	} else {
            this.rotating 	= false;
            this.sliceX 	= this.slice;
            this.jump 		= 1;
	}
	return this;
}
Carousel.prototype.stop = function () {
    if (this.timer)
            window.clearTimeout(this.timer);
    this.rotating = false;
    return this;
}
Carousel.prototype.reverse = function () {
    this.delta = (-1*this.delta);
    return this;
}
Carousel.prototype.spinLeft = function (){
    if (this.delta < 0) {
        this.reverse();
    }
    this.spin();
    return this;
}
Carousel.prototype.spinRight = function (){
    if (this.delta > 0) {
        this.reverse();
    }
    this.spin();
    return this;
}
Carousel.prototype.fog	= function (){
    var fog = "";
    var it = Math.round(this.items.length/4)-1;
    for (var i=1; i<=4; i++) {
        var zIndex = this.baseLayer + (i*it)-1
        fog += '<div id="carouselFog_'+i+'" style="width: 100%; height: 100%; z-index: '+zIndex+'; opacity: .05; background-color: black; position: absolute; top: 0px; left: 0px;"></div>';
    }
    this.ref.innerHTML = fog+this.ref.innerHTML;
}
Carousel.prototype.build = function (){
    if (this.applyFog) {
    	this.fog();
    }
    for (var i=0; i<this.items.length; i++) {
        if (document.getElementById(this.items[i].id))
            this.items[i].ref =  document.getElementById(this.items[i].id);
    }
    this.spacer		= Math.round(360/this.items.length);
    this.delta		= Math.round((this.spacer/this.vector)*100)/100;
    this.axis		= Math.ceil(this.items.length/2)+1;
    this.center     = {
        X:      Math.round(this.ref.offsetWidth/2),
        Y:      Math.round(this.ref.offsetHeight/8)
    }
    this.pathWidth	= Math.round((this.center.X)*.667);
    this.pathHeight =  Math.round((this.center.Y/2));
    for (var i=0; i<this.items.length; i++) {
        this.items[i].degree = (i+1)*this.spacer;
        this.items[i].ref.style.position = "absolute";
        this.items[i].ref.style.left     = (this.items[i].posX	= this.center.X + Math.round(Math.cos(this.items[i].degree * this.radian)*this.pathWidth))+"px";
        this.items[i].ref.style.top	 = (this.items[i].posY	= this.center.Y + Math.round(Math.sin(this.items[i].degree * this.radian)*this.pathHeight))+"px";
    }
    this.sliceX = this.slice = Math.round(360/this.items.length);
    return this;
}

