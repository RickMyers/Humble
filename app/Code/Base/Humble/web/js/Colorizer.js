/**
 *  Source Code Colorizer, author: Rick Myers <rickmyers1969@gmail.com>
 *  @param {string} codeSource
 *  @param {string} lang (optional)
 *  @param {string} src  (optional)
*/
var Colorizer = (function (languageFile) {
    var codeBox, src, lang, source, sourceId, languages;
    var isString    = false, inComment = false, classes = false;
    var colors      = { true: "#ddd",  false: "#ccc",    ln: "#333", num: "lightgreen"};
    return {
        init: function () {
            if (!classes) {
                //define the css classes
            }
            if (!languages) {
                this.loadLanguages(languageFile);
            }
        },
        escapeHTML: function(unsafe) {
            return unsafe;
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;");
        },
        loadLanguages: function (lang) {
            (new EasyAjax(((lang) ? lang : languageFile))).callback(function (json) {
                languages = JSON.parse(json);
            }).get(false);
        },
        loadSource: function (me,src,codeBox) {
            (new EasyAjax(src)).callback(function (response) {
                me.colorIt(response,codeBox);
            }).get();  
        },
        colorize: function (id,src,lang) {
            sourceId   = id;
            codeBox    = (sourceId)    ? ($E(sourceId) ? $E(sourceId) : null) : null;
            $(codeBox).css('font-family','Monospace');
            if (codeBox) {
               src        = (src)         ? src  : (codeBox.getAttribute("source")) ? codeBox.getAttribute("source") : null;
               lang       = (lang)        ? lang : (codeBox.getAttribute("lang")) ? codeBox.getAttribute("lang") : null;
               language = languages[lang];
               if (src) {
                    this.loadSource(this,src,codeBox);
               } else {
                    this.colorIt(codeBox.innerHTML,codeBox);
                }
            }
        },
        colorIt: function (code,codeBox) {
            var lines =  code.split("\n");
            var nl   = "<div id='colorizer_code_source_"+codeBox.id+"' style=' width: "+(codeBox.offsetWidth-48)+"px; height: "+(codeBox.offsetHeight-1)+"px; display: inline-block; overflow: auto'>";
            var rows = "<div id='colorizer_code_rows_"+codeBox.id+"' style='float: left; width: 45px; height: "+(codeBox.offsetHeight-1)+"px; overflow: hidden'>";
            codeBox.innerHTML = ""; var rc = []; rc[true]="#ddd"; rc[false]="#ccc";
            var rt = false;
            for (var i=0; i<lines.length; i++) {
                rt   = !rt;
                rows += '<div style="height: 1.2em; margin: 0px; padding: 0px 3px 0px 0px; width: 45px; white-space: nowrap; text-align: right; color: '+colors['num']+'; background-color: '+colors["ln"]+'; padding-right: 3px">'+(i+1)+'</div>';
                nl   += '<div id="'+sourceId+'_'+i+'" style="height: 1.2em; background: '+colors[rt]+'; white-space: pre; clear:both;">'+this.colorSource(lines[i].replace(/\n/g,""))+'</div>';
            }
            rows += '</div>'
            nl += '</div>';
            codeBox.innerHTML = rows+nl;
            if ($E("colorizer_code_source_"+codeBox.id).scrollWidth > 0) {
                var newWidth = ($E("colorizer_code_source_"+codeBox.id).scrollWidth) + "px"
                for (var i=1; i<lines.length; i++) {
                    $E(sourceId+"_"+i).style.width = newWidth;
                }
            }
            $("#colorizer_code_source_"+codeBox.id).on('scroll',function () {
                $E("colorizer_code_rows_"+codeBox.id).scrollTop = this.scrollTop;
            });
        },
        colorSource: function (line) {
            var token="", chr="", dispChar, strChar="", str = ""; var isChar=false;
            var newLine = ((inComment) ? '<span style="color: '+language.comment.color+'">' : "");
            for (var i=0; i<=line.length; i++) {
                if ((!inComment) && language.comment && language.comment.EOL) {
                    if ((line.substr(i,language.comment.EOL.length) == language.comment.EOL)) {
                        newLine+= (token+str+'<span style="color: '+language.comment.color+'">'+this.escapeHTML(line.substr(i))+'</span>');
                        break;
                    }
                }
                if (inComment) {
                    if (line.substr(i,language.comment.end.length) == language.comment.end) {
                        inComment = false; i=i+language.comment.end.length;
                        newLine   += (language.comment.end+"</span>");
                    }
                }
                if (!inComment && language.comment && language.comment.start) {
                    inComment = (line.substr(i,language.comment.start.length) == language.comment.start);
                    if (inComment) {
                        newLine += '<span style="color: '+language.comment.color+'">';
                    }
                }
                dispChar = chr = ((i == line.length) ? " " : line.substr(i,1));
                if ((language["char"][chr]) && (language["char"][chr].swap)) {
                    dispChar = language["char"][chr].swap;
                }                
                if ((!inComment) && (isString) && (chr === strChar)) {
                    str += dispChar;
                    newLine += '<span style="color: #555; font-style: italic">'+token+str+'</span>';
                    isString = false; str = ""; token="";
                } else if ((!inComment) && (isString)) {
                    str += dispChar;
                } else if ((!inComment) && ((chr == '"') || (chr == "'"))) {
                    isString = true;
                    str += strChar = dispChar;
                } else if (inComment) {
                    newLine += chr;
                } else {
                    isChar = (language["chars"].indexOf(chr) != -1);
                    if (token && isChar) {
                        newLine += (language["tokens"][token.trim()]) ? '<span style="color: '+language["tokens"][token.trim()].color+'">'+(language["tokens"][token.trim()].swap?language["tokens"][token.trim()].swap:token)+'</span>' : token;
                        token="";
                    }
                    if (isChar) {
                        newLine += (language["char"][chr]) ? '<span style="color: '+language["char"][chr].color+'">'+dispChar+'</span>' : dispChar;
                    } else {
                        token += chr;
                    }
                }
            }
            if (inComment) {
                newLine += "</span>";
            }
            return newLine;
        }        
   } 
})('/app/Code/Base/Core/web/js/ColorizerLanguages.json');
Colorizer.init();