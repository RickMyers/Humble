/**
 *
 * Manages the heartbeat, which is a periodic poll to the server.  As elements
 * are added to the page, they can add their own poll to the periodic poll, since
 * this is much more efficient.
 *
 * FORM:
 *
 *      Heartbeat.register(namespace,element id or TRUE,virtual function name, callback, period);
 *
 * SAMPLE:
 *
 *      Heartbeat.register('humble',TRUE,'systemCheck',function (response) { console.log(response); }, 2);
 *
 *
 * @type Heartbeat_L4.HeartbeatAnonym$0|Function
 *
 */
var Heartbeat = (function ($) {
    var period      = 5000;    //default to a heartbeat every 5 seconds...but it won't fire unless something requires it
    var pulseTimer  = null;     //timer reference
    var beats       = {};       //keeps a record
    var refs        = {};       //hash array of what is currently being polled back to the server
    var count       = 0;
    var indicator   = false;
    return  {
        responses: {

        },
        init: function (default_period,heartbeat_indicator) {
            default_period = (default_period) ? default_period : period;
            pulseTimer     = window.setTimeout(Heartbeat.pulse,default_period);
            indicator      = heartbeat_indicator ? document.getElementById(heartbeat_indicator) : false;
            if (indicator) {
                $(indicator).fadeOut();
            }
        },
        stop: function () {
            window.clearTimeout(pulseTimer);
        },
        restart: function () {
            
        },
        register: function (namespace,element,resource,callback,interval,arguments) {
            /**
             * NAMESPACE: The namespace of the module that contains the virtual function to be invoked
             * ELEMENT:  A reference to an ID on an element that will signal whether to keep performing
             *           this heart beat. If the element is no longer there, then remove this ID from the
             *           hash array, unless the value passed is a boolean TRUE. TRUE means that this heartbeat
             *           should be performed regardless
             * RESOURCE: The virtual function that will be invoked.  The results of the resource call will
             *           be directed to the callback
             * CALLBACK: A javascript function that will receive the result of the resource as its only argument
             * PERIOD:   (optional), this is an INTEGER multiple of the period.  So if the value is "2", you will
             *           multiply the period by this to get the frequency of execution.  A value of "2" with a period
             *           of 15000 means that this resource will be invoked every 30 seconds.   A value of 3 with a
             *           period of 15000 means that this resource will be invoked every 45 seconds... etc.
             */
            if (refs[resource]) {
                //this poll is currently active, don't add a second instance
                return;
            }
            var id      = EasyAjax.uniqueId(13);                                //Every "beat" needs a unique id
            interval    = (interval)  ? interval : 1;                           //Set interval or default to 1
            namespace   = (namespace) ? namespace : 'humble';                   //don't have namespace? use the default
            if (element !== true) {                                             //if element is true, then always run
                element = (typeof element === 'string') ? document.getElementById(element) : element;
            }
            /*
             * A mechanism for passing custom variables along with the heartbeat 
             * 
             */
            if (arguments) {
                for (var j in arguments) {
                    Humble.singleton.set(arguments[j],'');                      //Store them once...
                }
            }
            beats[id] = {
                "namespace": namespace,
                "element":  element,
                "resource": resource,
                "callback": callback,
                "interval": interval,
                "arguments": (arguments ? arguments : [])
            }
            
        },
        skip:   function () {
            Heartbeat.reset();
        },
        reset: function () {
            pulseTimer = window.setTimeout(Heartbeat.pulse,period);
            if (indicator) {
                $('#'+indicator.id).fadeOut();
            }
        },
        pulse: function () {
            /*
             * if "element" is present, add the resource to the heartbeat pulse, else
             * drop the resource from the heart beat list, since it was likely removed
             * unless the value of element is TRUE, which means "do this regardless"...
             *
             */
            var transport = {};                                                 //list of things to update sent to the server
            var ctr       = 0;                                                  //how many things do we need to update?
            var args      = [];
            count++;
            for (var i in beats) {
                try {
                    //If this beat is tied to a page element, like a stock ticker,
                    //if the element isn't in the page anymore, delete the beat
                    if ((beats[i].element) && (beats[i].element !== true) && (!document.getElementById(beats[i].element.id))) {
                        delete refs[beats[i].resource];                         //deregister that this poll is active
                        delete beats[i];                                        //whatever you were updating is no longer on the page, so bye bye!
                        continue;
                    }
                } catch (ex) {
                    console.log(beats[i]);
                }
                if (count >= 100) {
                    count = 0;                                                  //not necessary to do more math than needed
                }
                if (count % beats[i].interval !== 0) {
                    continue;                                                   //not time for you yet
                }
                ctr++;
                transport[i] = {
                    "id":   i,
                    "namespace": beats[i].namespace,
                    "resource": beats[i].resource
                }
                if (beats[i].arguments) {
                    for (var variable in beats[i].arguments ) {
                        args[args.length] = beats[i].arguments[variable];
                    }
                }
            }
            if (ctr>0) {
                if (indicator) {
                    $('#'+indicator.id).fadeIn();                               //We have work to do so show beat indicator
                }
                var opts = { };
                for (var j in args) {
                    opts[args[j]] = Humble.singleton.get(args[j]);
                }
                (new EasyAjax('/humble/system/poll')).add('beats',JSON.stringify(transport)).add('arguments',JSON.stringify(opts)).then(function (response) {
                    try {
                        var responses = JSON.parse(response);
                        if (responses) {
                            for (var i in responses) {
                                Heartbeat.responses[i] = responses[i];
                                if (beats[i].callback) {
                                    beats[i].callback(responses[i]);
                                }
                            }
                        }
                    } catch (ex) {
                        console.log(ex);
                    }
                    Heartbeat.reset();
                }).post();
            } else {
                Heartbeat.reset();
            }
        },
        period: function (val) {
            if (val) {
                period = val;
            } else {
                return period;
            }
        }
    }
})($);
