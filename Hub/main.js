/*
 _|      _|                                                                  _|    _|  _|    _|  _|_|_|    
 _|_|  _|_|    _|_|      _|_|_|    _|_|_|    _|_|_|    _|_|_|    _|_|        _|    _|  _|    _|  _|    _|  
 _|  _|  _|  _|_|_|_|  _|_|      _|_|      _|    _|  _|    _|  _|_|_|_|      _|_|_|_|  _|    _|  _|_|_|    
 _|      _|  _|            _|_|      _|_|  _|    _|  _|    _|  _|            _|    _|  _|    _|  _|    _|  
 _|      _|    _|_|_|  _|_|_|    _|_|_|      _|_|_|    _|_|_|    _|_|_|      _|    _|    _|_|    _|_|_|    
                                                           _|                                              
                                                       _|_|                                                

    All roads may lead to Rome, but all events go through here....
 */
'use strict';
String.prototype.pad = function (len,char,left) {
    left = (left || left===false) ? false : true;
    char    = ''+((char || (char===0)) ? char : ' ');                           //Force casting as a string
    let ps  = String(this);
    let its = len - ps.length;
    if (its>0) {
        for (let i=0; i<its; i++) {
            ps = (left) ? char+''+ps : ps+''+char;
        }
    }
    return ps;
};
let fs     = require('fs');
let config = JSON.parse(fs.readFileSync('../Humble.project','utf8'));

if (!config) {
    console.log("Unable to process Humble.project file... it is either missing or has issues");
    exit;
}

//let settings    = httpSetup(fs,config);
let express     = require('express');
let app         = express();
let mysql       = require('mysql');
let parser      = require('body-parser');
let http        = require("http").createServer(app).listen(config.hub_port,function () {
//let https       = require("https").createServer({ key:  settings.key,  cert: settings.cert,  ca:  settings.ca },app).listen(3000,function () {
    
    loadHosts();
    //mysqlConnect(config);     
    console.log('If you are seeing this, the server started successfully...');
});
let io              = require('socket.io').listen(http);
let hosts           = { };
let users           = { };                                                      //Tracks Users and Sockets... there are more than one socket assigned to a user if they have the dashboard open in multiple tabs
let users_online    = { };                                                      //Strictly tracks Users
let sockets         = { };
let observers       = { };
let dentist         = false;
let forms           = { };
let conn            = false;
let callers         = { };
let flags           = {
    "patientWaiting": false,
    "patient": false
};

//==============================================================================
function httpSetup(fs,data) {
  //  let key         = (data.ssl.key)  ? fs.readFileSync(data.ssl.root+data.ssl.key)  : '';
   // let cert        = (data.ssl.cert) ? fs.readFileSync(data.ssl.root+data.ssl.cert) : '';
    //let ca          = (data.ssl.ca)   ?  fs.readFileSync(data.ssl.root+data.ssl.ca)  : '';
  //  return { "key": key, "cert": cert, "ca": ca};
}
//==============================================================================
function routeMessage(message,data) {
    if (observers[message]) {
        for (var i in observers[message]) {
            io.to(i).emit(message,data);
        }
    }
}

//------------------------------------------------------------------------------
function countUsersOnline() {
    var num = 0;
    for (var i in users) {
        num++;
    }
    return num;
}
//------------------------------------------------------------------------------
function loadHosts() {
    let data = '';
    if (data = fs.readFileSync('../host_list.json','utf8')) { 
        if (hosts = JSON.parse(data)) {
            console.log("Hosts have been loaded");
        } else {
            console.log("Error reading hosts file");
            process.exit();
        }
    } else {
        console.log('List of external host (host_list.json) had an error, aborting');
        process.exit();
    }
}
//------------------------------------------------------------------------------
function mysqlConnect(data) {
    if (data.mysql) {
        console.log("MySQL Settings have loaded")
        conn = mysql.createConnection({
            host:       data.mysql.host,
            port:       data.mysql.port,
            user:       data.mysql.user,
            database:   data.mysql.database,
            password:   data.mysql.password
        });
        conn.connect(function (err) {
            if (err) {
                console.log(err);
                process.exit()
            } else {
                console.log("MySQL Connection Succeeded");
            }
        });
        conn.end((err) => {
            if (err) {
                console.log(err);
                process.exit()
            } else {
                console.log('Ending MySQL Connection');
            }
        })
    } else {
        console.log("Error loading MySQL settings");
        process.exit();
    }
 }
//------------------------------------------------------------------------------
function mysqlQuery(query,after) {
    if (query) {
        conn.query((err,rows)=>{
            if (err) {
                console.log(err);
            } else {
                after(rows);
            }
        });
    }
}
//------------------------------------------------------------------------------
app.use(parser.urlencoded({extended: true}));
app.use(parser.json());
app.get('/', function (req,res) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "X-Requested-With");
    res.sendFile(__dirname+'/index.html');
});

//------------------------------------------------------------------------------
//This listens for the dashboard to send an event by POSTing data, rather than
//  using sockets.  An alternative could be to use an actual socket, but then
//  we'd have to manage socket state, rather than remain "stateless"
app.post('/emit', function (req,res) {
    console.log('Received data via EMIT @ '+(new Date()).toLocaleString());
    console.log(req.body);
    var data = req.body;
	if (data && data.event && (data.event == 'RTCUserMessage')) {
        if (data.user_id && users[data.user_id]) {
            console.log(users[data.user_id]);
            for (var socket in users[data.user_id]) {
                console.log('Sending to '+socket);
                io.to(socket).emit(data.message,data);
            }
            
        }		
	} else if (data && data.event) {
        var e = data.event;
        delete data.event;
        if (data.uid) {
            for (var user_id in users) {
                if (data.uid == user_id) {
                    for (var socket_id in users[user_id]) {
                        console.log('Sending '+user_id+' on socket '+socket_id+' the message '+e+' @ '+(new Date()).toLocaleString());
                        io.to(socket_id).emit(e,data);                          //Sends the event and data to a specific person
                    }
                }
            }
        } else {
			console.log('Emitting data to client @ '+(new Date()).toLocaleString());
            io.emit(e,data);                                                    //Global broadcast
        }
    } else {
        console.log('I did not emit the event');
    }
});

app.use(function (req, res, next) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
    res.setHeader('Access-Control-Allow-Credentials', true);
    // Pass to next layer of middleware
    next();
});

//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
io.on('connection', function (socket) {
    io.to(this.id).emit('registerUserId');

    //--------------------------------------------------------------------------
    // Basic connection events
    //--------------------------------------------------------------------------
    socket.on('disconnect', function () {
        let user_id = +sockets[this.id];
        //Removing the user from tracking
        if (user_id === 2) {
            io.emit('rickHasLeft');
        }
        if (users[user_id] && users[user_id][this.id]) {
            console.log('Dropping socket '+this.id+' from user '+user_id);
            delete users[user_id][this.id];
            if (Object.getOwnPropertyNames(users[user_id]).length == 0) {
                delete users[user_id];
                delete users_online[user_id];
                console.log('Completely removing '+user_id);
            }
        }
        if (sockets[this.id]) {
            delete sockets[this.id];
        }
        //Remove listeners tied to the user
        for (var listener in observers) {
            if (observers[listener][this.id]) {
                delete observers[listener][this.id];
            }
        }
        io.emit('userListUpdate',{ "type": 'logoff', "user_id": user_id, "users_online": countUsersOnline(), "users": users_online });
    });
    
    socket.on('logUserOff', function (data) {
        var i,socket_id;
        for (var i in users) {
            if (i==data.uid) {
                for (socket_id in users[i]) {
                     io.to(socket_id).emit('logout');
                }
            }
        }
        console.log('~~~~~~~~');

//      console.log(users[data.uid]);
//      console.log(data);
    });
    
    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    socket.on('userStatus',function (data) {
        io.emit('userListUpdate',{ "type": 'refresh', "user_id": +sockets[this.id], "users_online": countUsersOnline(), "users": users_online }); 
    });
    
    //--------------------------------------------------------------------------
    // We are going to xref the socket to the user, and vice-versa, basically a
    // double-linked list
    //--------------------------------------------------------------------------
    socket.on('logUserIn',function (data) {
        let socket_id = this.id;
        sockets[socket_id] = data.user_id;
        if (users[data.user_id]) {
            console.log('Adding socket '+socket_id+' to user '+data.user_id+' (because they are connected in another tab)');
            users[data.user_id][socket_id] = [];
        } else {
            users[data.user_id] = { };
            users[data.user_id][socket_id] = [];
        }
        var https = require('http');
        //NEED TO GET THE RIGHT SERVER TO TALK TO!
        var options = {
            socket_id: socket_id,
            host: hosts.user_data, 
            port: '80', 
            path: '/dashboard/user/info?uid='+data.user_id,
            rejectUnauthorized: false,
            requestCert: true,
            agent: false            
        };
        var request = https.request(options,function (res) {
            var resdata = '';
            res.on('data', function (chunk) {
                resdata += chunk;
            });
            res.on('end', function () {
                if (resdata) {
                        console.log(resdata);
                        resdata = JSON.parse(resdata);
                        users[data.user_id][options.socket_id][users[data.user_id][options.socket_id].length] = resdata;
                        users_online[data.user_id] = resdata;
                        io.emit('userListUpdate',{ "type": 'logon', "user_id": data.user_id, "users_online": countUsersOnline(), "users": users_online });  
                        console.log('User '+data.user_id+' has connected');
                }
            });
        });
        request.on('error',function (e) {
           console.log(e.message); 
        });
        request.end();

        if (+data.user_id === 2) {
            io.emit('rickIsOnline');
        }
    });

    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    socket.on('showUsers',function () {
        let ctr = 0;
        console.log('');
        console.log('Active User Lists');
        console.log("Socket".pad(40)+"User ID".pad(20));
        console.log("=".pad(60,'='));
        for (var i in sockets) {
            console.log(i.pad(40)+''+sockets[i].pad(20));
            ctr++;
        }
        console.log("Currently "+ctr+" users are logged in");
    });

    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    socket.on('showListeners', function () {

    });

    //--------------------------------------------------------------------------
    // Instead of doing 'broadcast' messages, we are going to let clients register
    // which messages they are interested in listening for, and then
    //--------------------------------------------------------------------------
    socket.on('registerListeners',function (listeners) {
        console.log(this.id+' registering listeners');
        for (var i in listeners) {
            if (!observers[listeners[i]]) {
                observers[listeners[i]] = { };
            }
            observers[listeners[i]][this.id] = true;
        }
        console.log(observers);
    });

    //--------------------------------------------------------------------------
    // Generic message relay
    //--------------------------------------------------------------------------
    socket.on('messageRelay', function (data) {
        console.log('Message Relay: '+data.message);
		console.log(data);
        var message = data.message;
        delete data.message;
        io.emit(message,data);
    });

    // socket.on('removeListeners',removeListeners);

    //--------------------------------------------------------------------------
    // Specifically send to a particular user
    //--------------------------------------------------------------------------
    socket.on('RTCUserMessage',function (data) {
        if (data.user_id && users[data.user_id]) {
            console.log(users[data.user_id]);
            for (var socket in users[data.user_id]) {
                console.log('Sending to '+socket);
                io.to(socket).emit(data.message,data);
            }
            
        }
    });

    //--------------------------------------------------------------------------
    // Broadcast to someone listening for a specific event
    //--------------------------------------------------------------------------
    socket.on('RTCMessageRelay', function (data) {
        console.log('RTC message: '+data.message);
        io.emit(data.message,data);
    });

    //--------------------------------------------------------------------------
    // Text chat events
    //--------------------------------------------------------------------------
    socket.on('chatMessage', function (msg) {
        io.emit('chat message',msg);
    });

});

