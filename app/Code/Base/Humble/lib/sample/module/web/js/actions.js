//&&mainmodule&&
//&&project&& = {};
&&project&&.&&namespace&& = (function () {
    //local variables and functions here
    return {
        init: {
            //first we call any Javascripts modules initializers
            for (var namespace in &&project&&) {
                if (&&namespace&&[namespace].init) {
                    &&namespace&&[namespace].init();
                }
            }          
            //Then we let the module set up the sockets/WebRTC listeners.  Must have a connection though...
            for (var namespace in &&project&&) {
                if (&&namespace&&[namespace].RTC) {
                    &&namespace&&[namespace].RTC();
                }
            }          
        }    
    }
})();