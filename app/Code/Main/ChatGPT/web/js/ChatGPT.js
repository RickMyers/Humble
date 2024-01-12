//&&mainmodule&&
//Humble = {};
Humble.chatgpt = (function () {
    //local variables and functions here
    return {
        init: function () {
            //first we call any Javascripts modules initializers
            for (var namespace in Humble) {
                if (chatgpt[namespace].init) {
                    chatgpt[namespace].init();
                }
            }          
        },
        RTC: function () {
            //Then we let the module set up the sockets/WebRTC listeners.  Must have a connection though...
            for (var namespace in Humble) {
                if (chatgpt[namespace].RTC) {
                    chatgpt[namespace].RTC();
                }
            }          
        }
    }
})();