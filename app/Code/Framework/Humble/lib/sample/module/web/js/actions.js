//&&mainmodule&&
//&&PROJECT&& = {};
&&PROJECT&&.&&NAMESPACE&& = (function () {
    //local variables and functions here
    return {
        init: function () {
            //first we call any Javascripts modules initializers
            for (var namespace in &&PROJECT&&) {
                if (&&NAMESPACE&&[namespace].init) {
                    &&NAMESPACE&&[namespace].init();
                }
            }          
        },
        RTC: function () {
            //Then we let the module set up the sockets/WebRTC listeners.  Must have a connection though...
            for (var namespace in &&PROJECT&&) {
                if (&&NAMESPACE&&[namespace].RTC) {
                    &&NAMESPACE&&[namespace].RTC();
                }
            }          
        }
    }
})();