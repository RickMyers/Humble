//Establishes a namespace
var Humble = (() => {
    var templates   = [];
    var vars        = [];    
    return {
        init:(callback) => {
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
                for (var i in Humble) {
                    console.log(Humble[i]);
                    if (Humble[i].init) {
                        Humble[i].init();
                    }
                }
            }).get();
        },
        singleton: {
            list: () => {
                var args = [];
                for (var j in vars) {
                    args[args.length] = j;
                }
                return args;
            },
            set: (v,val) => {
                vars[v] = val;
            },
            get: (v) => {
                return vars[v];
            },
            show: () => {
                console.log(vars);
            }
        },        
        template:  (namespace,identifier) => {
            let template = templates[namespace] ? (templates[namespace][identifier] ? templates[namespace][identifier] : '')  : '';
            if (!template) {
                console.log('Attempt to fetch template ['+namespace+','+identifier+'] failed, the template was not found');
            }
            return template;
        }
    }
})();

