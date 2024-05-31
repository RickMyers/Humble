/*
 * Default basic functionality for any module, if you choose to use it
 * 
 * Video: https://humbleprogramming.com/pages/BasicJavascript.htmls
 */
&&PROJECT&&.&&NAMESPACE&& = (function () {
    //local variables and functions here
    return {
        init: () => {
            //first we call any Javascripts modules initializers, like compilation of templates
        },
        RTC:  () => {
            //Then we let the module set up the sockets/WebRTC listeners.  This method will be called after server connects
        },
        finalize: () => {
            //Called on unload of page
        }
    }
})();