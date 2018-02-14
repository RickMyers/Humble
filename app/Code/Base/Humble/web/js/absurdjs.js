/**
 * A minimal JS templating mechanism by Krasimir Tsonev
 *
 * @param {type} html
 * @param {type} options
 * @returns {Array}
 */
var AbsurdJS = function(html, options) {
    var re = /<%([^%>]+)?%>/g, reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g, code = 'var r=[];\n', cursor = 0, match;
    var add = function(line, js) {
        js? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') :
            (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
        return add;
    }
    while(match = re.exec(html)) {
        add(html.slice(cursor, match.index))(match[1], true);
        cursor = match.index + match[0].length;
    }
    add(html.substr(cursor, html.length - cursor));
    code += 'return r.join("");';
    return new Function(code.replace(/[\r\t\n]/g, '')).apply(options);
}
var Templater = (function () {
    return {
        sources: {},
        load: function (resource) {
            if (!Templater.sources[resource]) {
                (new EasyAjax(resource)).callback(function (template) {
                    if (template) {
                        Templater.sources[resource] = template;
                    } else {
                        console.log('Failed to retrieve a JS template for ['+resource+']');
                    }
                }).get(false);
            }
            return this;
        },
        parse: function (resource,options) {
            if (Templater.sources[resource]) {
                return AbsurdJS(Templater.sources[resource],options);
            } else {
                console.log('JS Template '+resource+' hasnt been loaded yet');
            }
        }
    }
})();