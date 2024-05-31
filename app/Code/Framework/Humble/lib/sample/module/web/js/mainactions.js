/*
 * Default basic functionality for entire system, if you choose to use it
 * 
 * Video: https://humbleprogramming.com/pages/BasicJavascript.htmls
 */
&&PROJECT&& = (() => {
    return {
        init: () => {
            //first we call any Javascripts modules initializers
            for (var namespace in &&PROJECT&&) {
                if (&&NAMESPACE&&[namespace].init) {
                    &&NAMESPACE&&[namespace].init();
                }
            }          
        },
        RTC: () => {
            //Then we let the module set up the sockets/WebRTC listeners.  Must have a connection though...
            for (var namespace in &&PROJECT&&) {
                if (&&NAMESPACE&&[namespace].RTC) {
                    &&NAMESPACE&&[namespace].RTC();
                }
            }          
        }
    }
})();
