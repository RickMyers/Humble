
/**
   _|_|_|                  _|
 _|          _|_|      _|_|_|    _|_|
 _|        _|    _|  _|    _|  _|_|_|_|
 _|        _|    _|  _|    _|  _|
   _|_|_|    _|_|      _|_|_|    _|_|_|

   _|_|_|            _|                      _|
 _|          _|_|    _|    _|_|    _|  _|_|      _|_|_|_|    _|_|    _|  _|_|
 _|        _|    _|  _|  _|    _|  _|_|      _|      _|    _|_|_|_|  _|_|
 _|        _|    _|  _|  _|    _|  _|        _|    _|      _|        _|
   _|_|_|    _|_|    _|    _|_|    _|        _|  _|_|_|_|    _|_|_|  _|
 *
 *  Highly flexible Code Colorizer
 *
 *  @author: Rick Myers <rickmyers1969@gmail.com>
 *  @param {string} languageFile Default Language File
*/
var Colorizer = (function (languageFile) {
    var Colorizers  = [];
    var Alphabet    = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    var Colors      = {
        true:   "#ddd",
        false:  "#ccc",
        ln:     "#333",
        num:    "lightgreen"
    };
    function id(chars) {
        var id = '';
        for (var i=0; i<chars; i++) {
            id += Alphabet.substr(Math.floor(Math.random()*(Alphabet.length-1)),1);;
        }
        return id;
    }
    let Prototype = {
        language:   false,
        languages:  false,
        source:     false,
        code:       false,
        isString:   false,
        inComment:  false,
        box:        false,
        rows:       false,
        init: function() {
            this.codeBox = (typeof this.codeBox === 'string') ? $E(this.codeBox) : this.codeBox;
            if (!this.languages) {
                var me = this;
                (new EasyAjax(((this.lexicon) ? this.lexicon : languageFile))).then(function (json) {
                    me.languages = JSON.parse(json);
                    me.language  = me.languages[me.type];
                }).get();
            }
            if (!this.code) {
                (new EasyAjax(this.source)).then(function (response) {
                    me.code = response;
                }).get();
            }
            return this.run();
        },
        run: function () {
            var me    = this;
            if ((this.language !== false) && (this.languages !== false) && (this.code !== false)) {
                var lines =  me.code.split("\n");
                var nl    = "<div id='colorizer_code_source_"+this.id+"' style=' width: "+(this.codeBox.offsetWidth-58)+"px; height: "+(this.codeBox.offsetHeight-1)+"px; display: inline-block; vertical-align: top; overflow: auto'>";
                var rows  = "<div id='colorizer_code_rows_"+this.id+"' style='float: left; width: 45px; height: 100%; overflow: hidden'>";
                this.codeBox.innerHTML = "";
                var rt = false;
                for (var i=0; i<lines.length; i++) {
                    rt   = !rt;
                    rows += '<div style="height: 1.2em; margin: 0px; padding: 0px 3px 0px 0px; width: 45px; white-space: nowrap; text-align: right; color: '+Colors['num']+'; background-color: '+Colors["ln"]+'; padding-right: 3px">'+(i+1)+'</div>';
                    nl   += '<div class="'+this.id+'" style="height: 1.2em; background: '+Colors[rt]+'; white-space: pre; clear:both;">'+this.colorSource(lines[i].replace(/\n/g,""))+'</div>';
                }
                this.codeBox.innerHTML = rows+'</div>'+nl+'</div>';
                this.box    = $E("colorizer_code_source_"+this.id);
                this.rows   = $E("colorizer_code_rows_"+this.id);
                if (this.scroll) {
                    var size = (this.scroll.indexOf('%') !== -1) ? Math.round(this.box.scrollHeight * (parseInt(this.scroll)/100)) : this.scroll;
                    $(this.codeBox).height(size);
                    $(this.rows).height(size);
                    $(this.box).height(size);
                }
                if (this.box.scrollWidth > 0) {
                    $(this.box).width(this.codeBox.offsetWidth-this.rows.offsetWidth-2);
                    $('.'+this.id).width(this.box.scrollWidth);
                }
                $(this.box).on('scroll',function () {
                    me.rows.scrollTop = this.scrollTop;
                });
            } else {
                window.setTimeout(function () { me.run(); }, 50);
            }
            return this;
        },
        colorSource: function (line) {
            var token="", chr="", dispChar, strChar="", str = ""; var isChar=false;
            var newLine = ((this.inComment) ? '<span style="color: '+this.language.comment.color+'">' : "");
            for (var i=0; i<=line.length; i++) {
                if ((!this.inComment) && this.language.comment && this.language.comment.EOL) {
                    if ((line.substr(i,this.language.comment.EOL.length) === this.language.comment.EOL)) {
                        newLine+= (token+str+'<span style="color: '+this.language.comment.color+'">'+this.escapeHTML(line.substr(i))+'</span>');
                        break;
                    }
                }
                if (this.inComment) {
                    if (line.substr(i,this.language.comment.end.length) === this.language.comment.end) {
                        this.inComment = false; i=i+this.language.comment.end.length;
                        newLine   += (this.language.comment.end+"</span>");
                    }
                }
                if (!this.inComment && this.language.comment && this.language.comment.start) {
                    this.inComment = (line.substr(i,this.language.comment.start.length) === this.language.comment.start);
                    if (this.inComment) {
                        newLine += '<span style="color: '+this.language.comment.color+'">';
                    }
                }
                dispChar = chr = ((i === line.length) ? " " : line.substr(i,1));
                if ((this.language["char"][chr]) && (this.language["char"][chr].swap)) {
                    dispChar = this.language["char"][chr].swap;
                }
                if ((!this.inComment) && (this.isString) && (chr === strChar)) {
                    str += dispChar;
                    newLine += '<span style="color: #555; font-style: italic">'+token+str+'</span>';
                    this.isString = false; str = ""; token="";
                } else if ((!this.inComment) && (this.isString)) {
                    str += dispChar;
                } else if ((!this.inComment) && ((chr === '"') || (chr === "'"))) {
                    this.isString = true;
                    str += strChar = dispChar;
                } else if (this.inComment) {
                    newLine += chr;
                } else {
                    isChar = (this.language["chars"].indexOf(chr) !== -1);
                    if (token && isChar) {
                        newLine += (this.language["tokens"][token.trim()]) ? '<span style="color: '+this.language["tokens"][token.trim()].color+'">'+(this.language["tokens"][token.trim()].swap?this.language["tokens"][token.trim()].swap:token)+'</span>' : token;
                        token="";
                    }
                    if (isChar) {
                        newLine += (this.language["char"][chr]) ? '<span style="color: '+this.language["char"][chr].color+'">'+dispChar+'</span>' : dispChar;
                    } else {
                        token += chr;
                    }
                }
            }
            if (this.inComment) {
                newLine += "</span>";
            }
            return newLine;
        }
    };
    return {
        render: function (codeBox) {
            var options = {
                "id"     : {
                    "value": id(9)
                },
                "lexicon": {
                    "value": codeBox.getAttribute('lexicon')
                },
                "type": {
                    "value": codeBox.getAttribute('lang')
                },
                "source": {
                    "value": codeBox.getAttribute('source')
                },
                "scroll": {
                    "value": codeBox.getAttribute('widgetScroll')
                },
                "codeBox": {
                    "value": codeBox
                }
            };
            Colorizers[options.id.value] = Object.create(Prototype,options);
            return Colorizers[options.id.value].init();
        },
        scan: function (node) {
            if (node.getAttribute && node.getAttribute('widget') && (node.getAttribute('widget')==='codeBox')) {
                Colorizer.render(node);
            }
            if (node.childNodes && node.childNodes.length) {
                for (var i in node.childNodes) {
                    Colorizer.scan(node.childNodes[i]);
                }
            }

        }
   };
})('/pages/js/ColorizerLanguages.json'); //Default colorizer lexicon
