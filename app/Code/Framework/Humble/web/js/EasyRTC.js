/*
  _|_|_|_|                                _|_|_|    _|_|_|_|_|    _|_|_|
 _|          _|_|_|    _|_|_|  _|    _|  _|    _|      _|      _|
 _|_|_|    _|    _|  _|_|      _|    _|  _|_|_|        _|      _|
 _|        _|    _|      _|_|  _|    _|  _|    _|      _|      _|
 _|_|_|_|    _|_|_|  _|_|_|      _|_|_|  _|    _|      _|        _|_|_|
                                     _|
                                 _|_|
 Spiffy.
 */
'use strict';
var EasyRTC = (function () {
    //variables that can be "elevated" to a semi-global scope relative the object are placed up here
    let EasyRTCs        = { };
    let mediaStream     = { };
    let socket          = false;
    let pc              = { };
    let players         = { };
    let defaults        = {
        "events": {
            "offer": {
                "inbound": "inboundOffer"
            },
            "answer": {
                "inbound": "inboundAnswer"
            },
            "candidate": {
                "inbound": "inboundCandidate"
            },
            "negotiation": {
                "inbound": "inboundNegotiation"
            }
        },
        "configuration": {
            iceServers: [
                {
                    urls: "stun:stun.l.google.com:19302"
                }
            ]
        },
        "options": {
            offerToReceiveAudio: 1,
            offerToReceiveVideo: 1
        },
        "constraints": {
            audio: true,
            video: true
        }
    };
    function output(m) {
        return function (e) {
            console.log('EasyRTC: '+m);
            if (e) {
                console.log(e);
            }
        };
    }
    function scrubEvents(events) {
        events.offer        = (events.offer) ? events.offer : defaults.events.offer;
        events.answer       = (events.answer) ? events.answer : defaults.events.answer;
        events.candidate    = (events.candidate) ? events.candidate : defaults.events.candidate;
        events.negotiation  = (events.negotiation) ? events.negotiation : defaults.events.negotiation;
        return events;
    }
    function scrubConstraints(constraints) {
        constraints.audio = (constraints.audio || constraints.audio === false || constraints.audio === 0) ? constraints.audio : defaults.constraints.audio;
        constraints.video = (constraints.video || constraints.video === false || constraints.video === 0) ? constraints.video : defaults.constraints.video;
        return constraints;
    }
    function scrubOfferOptions(options) {
        options.offerToRecieveAudio = (options.offerToReceiveAudio || options.offerToReceiveAudio === false || options.offerToReceiveAudio === 0) ? options.offerToReceiveAudio  : defaults.options.offerToReceiveAudio;
        options.offerToRecieveVideo = (options.offerToRecieveVideo || options.offerToRecieveVideo === false || options.offerToRecieveVideo === 0) ? options.offerToRecieveVideo  : defaults.options.offerToRecieveVideo;
        return options;
    }
    function scrubConfiguration(config) {
        config.iceServers   = (config.iceServers)   ? config.iceServers  : defaults.configuration.iceServers;
        return config;
    }
    function init(id,configuration,constraints) {
        pc[id] = new RTCPeerConnection(configuration);
        mediaStream[id] = false;
        if (constraints.video.mandatory.sourceId) {
            navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                stream.getTracks().forEach(
                    function(track) {
                        pc[id].addTrack(track,stream);
                    }
                );
                mediaStream[id] = stream;
            }).catch(
                output('Failed to initialize stream')
            );
        }
        return true;
    };
    let RTC     = {
        prepped:        false,
        initialized:    false,
        mediaStream:    false,
        readyFunc:      [],
        defaults:   function () {
            return defaults;
        },
        ready: function (func) {
            //This is an implementation of a poor-mans Promise.  Used this way because ES6 is not guaranteed.
            //When the media stream is ready, it will autoplay.
            let me = this;
            if (func) {
                this.readyFunc[this.readyFunc.length] = function () { func.call(me); };
            }
            if (mediaStream[this.id] !== false) {
                //assert true!
                for (var i=0; i<this.readyFunc.length; i++) {
                    this.readyFunc[i]();
                }
            } else {
                window.setTimeout(function () { me.ready(); },50);
            }
            return (mediaStream[this.id] !== false);
        },
        prep: function () {
            let me = this;
            pc[this.id].ontrack = function (evt) {
                if (!players[me.id]) {
                    players[me.id] = $E(me.id);
                    players[me.id].onloadedmetadata   = function(e) {
                        this.play();
                    };
                }
                players[me.id].srcObject = evt.streams[0];
            } ;
            pc[this.id].oniceconnectionstatechange = function(e) {
            };
            pc[this.id].onnegotiationneeded = function (e) {
                this.createOffer().then(function (offer) {
                    return pc[me.id].setLocalDescription(offer);
                }).then(function () {
                    socket.emit('RTCMessageRelay',{ "message": me.events.negotiation.inbound, "type": "offer", "desc": this.localDescription });
                }).catch(output('Error during negotiation'));

            };
            pc[this.id].onicecandidate = function(e) {
                socket.emit("RTCMessageRelay",{ "message": me.events.candidate.inbound, "id": socket.id, "candidate": e.candidate });
            };
            socket.on(this.events.offer.inbound,function (offer) {
                if (offer.id !== socket.id) {
                    pc[me.id].setRemoteDescription(offer.offer).then(function (answer) {
                        pc[me.id].createAnswer(answer).then(function (response) {
                            pc[me.id].setLocalDescription(response).then(function () {
                                socket.emit('RTCMessageRelay',{ "message": me.events.answer.inbound, "id": socket.id, "answer": response });
                            });
                        }).catch(
                            output('Failed to set local description')
                        );
                    }).catch(
                        output('Failed to set remote description')
                    );
                } else {
                    console.log('ignoring my own offer');
                }
            });
            socket.on(this.events.answer.inbound,function (response) {
                if (response.id !== socket.id) {
                    pc[me.id].setRemoteDescription(response.answer).catch(
                        output('Failed to set remote description')
                    );
                }
            });
            socket.on(this.events.candidate.inbound,function (e) {
                if ((e.id !== socket.id) && (e.candidate)) {
                    pc[me.id].addIceCandidate(e.candidate).catch(
                        output('failed adding candidate!')
                    );
                }
            });
            this.prepped = true;
        },
        play: function () {
            if (!this.prepped) {
                this.prep();
            }
            players[this.id]                    = document.getElementById(this.id);
            if (!players[this.id]) {
                console.log('WebRTC Video Player not found!');
                return;
            }
            players[this.id].srcObject          = mediaStream[this.id];
            players[this.id].onloadedmetadata   = function(e) {
                this.play();
            };
        },
        call: function () {
            let me      = this;
            if (!players[this.id]) {
                this.play();
            }
            pc[this.id].createOffer(this.offerOptions).then(function (offerData) {
                pc[me.id].setLocalDescription(offerData).then(function (data) {
                    socket.emit('RTCMessageRelay',{ "message": me.events.offer.inbound, 'id': socket.id, 'offer': offerData });
                }).catch(
                    output('Failed setting local description')
                );
            });
        },
        hangup: function () {
            this.prepped = false;
            this.readyFunc = [];
            if (mediaStream[this.id]) {
                mediaStream[this.id].getTracks().forEach(function (track) {
                    track.stop();
                });
            }
            if (pc[this.id] && pc[this.id].close) {
                pc[this.id].close();
            }
            if (players[this.id] && players[this.id].srcObject) {
                players[this.id].srcObject = null;
            }
            delete players[this.id];
            delete mediaStream[this.id];
            delete pc[this.id];
            delete EasyRTCs[this.id];
        }
    };
    return {
        /* This method takes the arguments passed and 'scrubs' them, allowing you to run with defaults but only change the part of the configuration you want, and not have to pass in an entire configuration array */
        get: function (identifier,websocket,configuration,constraints,options,events) {
            socket          = (!socket)         ? websocket                         : socket;
            events          = (events)          ? scrubEvents(events)               : defaults.events;
            constraints     = (constraints)     ? scrubConstraints(constraints)     : defaults.constraints;
            offerOptions    = (options)         ? scrubOfferOptions(options)        : defaults.options;
            configuration   = (configuration)   ? scrubConfiguration(configuration) : defaults.configuration;
            return (EasyRTCs[identifier])       ? EasyRTCs[identifier] : (EasyRTCs[identifier] = Object.create(RTC,{"id": { "value": identifier }, "offerOptions": { "value": offerOptions }, "events": { "value": events }, 'initialized': { "value": init(identifier,configuration,constraints) } } ));
        },
        events: function (id) {
            id = (id) ? id : '';
            return {
                "offer": {
                    "inbound": "inbound"+id+"Offer"
                },
                "answer": {
                    "inbound": "inbound"+id+"Answer"
                },
                "candidate": {
                    "inbound": "inbound"+id+"Candidate"
                },
                "negotiation": {
                    "inbound": "inbound"+id+"Negotiation"
                }
            }
        }
    };
})();
