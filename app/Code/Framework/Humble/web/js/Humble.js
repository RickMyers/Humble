//Establishes a namespace
var Humble = (function () {
    var templates   = [];
    var vars        = [];    
    return {
        init: function (callback) {
            var me = this;
            (new EasyAjax('/paradigm/templates/fetch')).then((response) => {
                var tpls = JSON.parse(response);
                if (tpls) {
                    for (var namespace in tpls) {
                        templates[namespace] = {};
                        for (var template in tpls[namespace]) {
                            templates[namespace][template] = tpls[namespace][template];
                        }
                    }
                }
                if (callback) {
                    callback.apply(me);
                }
            }).get();
        },
        singleton: {
            list: function () {
                var args = [];
                for (var j in vars) {
                    args[args.length] = j;
                }
                return args;
            },
            set: function (v,val) {
                vars[v] = val;
            },
            get: function (v) {
                return vars[v];
            },
            show: function () {
                console.log(vars);
            }
        },        
        template:  function (namespace,identifier) {
            let template = templates[namespace] ? (templates[namespace][identifier] ? templates[namespace][identifier] : '')  : '';
            if (!template) {
                console.log('Attempt to fetch template ['+namespace+','+identifier+'] failed, the template was not found');
            }
            return template;
        }
    }
})();

