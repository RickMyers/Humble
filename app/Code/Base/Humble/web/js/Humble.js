//Establishes a namespace
var Humble = (function ($) {
    var templates       = [];
    var defaultModule   = false;
    return {
        init: function () {
            (new EasyAjax('/paradigm/templates/fetch')).callback(function (response) {
                var tpls = JSON.parse(response);
                if (tpls) {
                    for (var namespace in tpls) {
                        templates[namespace] = {};
                        for (var template in tpls[namespace]) {
                            templates[namespace][template] = tpls[namespace][template];
                            //if default, mark as default
                        }
                    }
                }
                console.log(templates);
            }).get(false);
        },
        template:  function (identifier,defaults) {
            var tp      = '';
            defaults    = (defaults) ? true : false;
            identifier  = identifier.split('/');
            tp = templates[identifier[0]] ? (templates[identifier[0]][identifier[1]] ? templates[identifier[0]][identifier[1]] : '')   : '';
            if (!tp && defaults) {
                //now try to find the same template in the default app library
            }
            return tp;
        }
    }
})($);

