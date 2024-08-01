var RTE	= [];
function EasyRTE(frameId,eW,eH,buildControls)
{
    var me 		= this;
    this.base		= "";   //base directory for images
    this.ref		= null;
    this.noControls     = (buildControls === false);
    this.scanTimer	= null;
    this.lastLook	= null;
    this.imageLayer     = null;
    this.textChangeHandler = null;
    this.blogId		= null;
    this.idField	= null; //when they do first save of new blog entry, an id value is assigned, this retains it. later, make this a JS field, and not an HTML element
    this.folderId	= null;
    this.arguments	= [];
    this.argumentList   = [];
    this.saveHandler    = null;
    this.validator	= null;
    this.ao		= null;  //ajax object
    this.editorW 	= (eW) ? eW : 500;  //establish defaults dimensions
    this.editorH 	= (eH) ? eH : 270;
    this.frameId	= (frameId) ? frameId : null;
    this.rteId 		= (frameId) ? frameId : "rte_"+Math.round(Math.random()*10000); //not the best way of getting a unique id, but i will refactor the whole bloody thing later, and will deal with it then
    RTE[this.rteId]     = this;
    this.frame 		= (this.frameId) ? $E(this.frameId) : null;
    this.setIdField = function (idField) {
        me.idField = idField;
    }
    this.setImageHandler    = function (func) {
        if (func) {
            this.insertImage = func;
        }
    }
    this.updateIdValue = function () {
        alert("Saved");
        if ($E(me.idField))
            if (!($E(me.idField).value))
                $E(me.idField).value = me.ao.getResponse().trim();
    }
    this.refreshWindow = function (ref)	{
        alert("Saved");
        Desktop.refreshWindow(ref.folderId);
    }
    if (this.frameId) {
        if (!$E(this.frameId).getAttribute("contentEditable")!=true)	{
            $E(this.frameId).setAttribute("contentEditable","true");
            $E(this.frameId).contentEditable = true;
        }
        $E(this.frameId).style.overflow = "auto";
    }
    this.connect	= function ()	{
        me.ref = (me.frameId) ? $E(me.frameId) : null;
    }
    this.size = function ()	{
        if (me.frame) {
            me.editorW += ''; //make string
            me.editorH += '';
            if (me.editorW)	me.frame.style.width = parseInt(me.editorW)+((me.editorW.indexOf("%")==-1)?"px":"%");
            if (me.editorH) me.frame.style.height = parseInt(me.editorH)+((me.editorH.indexOf("%")==-1)?"px":"%");
        }
    }
    this.scanForChange	= function () {
        if (me.lastLook != me.getText()) {
            me.lastLook = me.getText();
            me.textChangeHandler();
        }
        else
            window.setTimeout("RTE['"+me.rteId+"'].scanForChange()",333);
    }
    this.onTextChange = function (handler)	{
        me.lastLook = me.getText();
        me.textChangeHandler = handler;
        window.setTimeout("RTE['"+me.rteId+"'].scanForChange()",333);
    }
    this.toolbar	= function () {
        var toolBar =	'<table style="clear: left" cellspacing="0" cellpadding="0"><tr>';
        toolBar += '<td><a id="saveBtn_'+me.rteId+'" href="#" onclick="RTE[\''+me.rteId+'\'].save(); return false;"><img style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteSaveIcon.png" title="Save Work" /></a></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].boldText()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteBoldIcon.png" title="Bold" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].italicText()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteItalicIcon.png" title="Italic" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].underlineText()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteUnderlineIcon.png" title="Underline" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].struckText()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteStrikeIcon.png" title="Strike-Out" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertImage()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteImageIcon.png" title="Insert an image with formatting" /></td>'
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertHyperlink()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteLinkIcon.png" title="Link"  /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertHeadline()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteHeadlineIcon.png" title="Section Headline" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertRule()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteLineIcon.png" title="Horizontal Line" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertQuote()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteQuoteIcon.png" title="Block Quote" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertCode()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteCodeIcon.png" title="Image" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].subscript()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteSubscriptIcon.png" title="Subscript" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].superscript()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteSuperscriptIcon.png" title="Superscript" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].increaseFont()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteIncreaseFont.png" title="Increase Font Size" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].decreaseFont()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteDecreaseFont.png" title="Decrease Font Size" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertMedia()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteMediaIcon.png" title="Media Link" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justifyLeft()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteJustifyLeft.png" title="Align Left" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justifyCenter()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteJustifyCenter.png" title="Center Align" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justifyRight()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteJustifyRight.png" title="Align Right" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].orderedList()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteOrderedList.png" title="Ordered List" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].unorderedList()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteUnorderedList.png" title="Unordered List" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justify()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteJustify.png" title="Justify" /></td>'
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].preview()" style="cursor: pointer" id="preview_'+me.rteId+'" src="'+this.base+'/images/humble/rte/rtePreviewIcon.gif" title="Preview the entry" /></td>'
        toolBar += '<td><img style="cursor: pointer" id="publish_'+me.rteId+'" onclick="RTE[\''+me.rteId+'\'].publish()" src="'+this.base+'/images/humble/rte/rtePublishIcon.png" title="Publish the entry" /></a></td>'
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].fontColor()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/rteSetFontRed.png" title="Set Font Color to Red" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].highlight()" style="cursor: pointer" src="'+this.base+'/images/humble/rte/highlight.gif" title="Hilight Selected Text" /></td>';
        toolBar += '</tr>';
        toolBar += '</table>';
        toolBar += '<table border="1" style="display: none" id="colorChart_'+me.rteId+'">';
        toolBar += '<tr>';
        toolBar += '<td class="ccCell" style="background-color: black" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: silver" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: gray" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: white" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '</tr>';
        toolBar += '<tr>';
        toolBar += '<td class="ccCell" style="background-color: maroon" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: red" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: purple" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: fuchsia" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '</tr>';
        toolBar += '<tr>';
        toolBar += '<td class="ccCell" style="background-color: green" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: lime" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: olive" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: yellow" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '</tr>';
        toolBar += '<tr>';
        toolBar += '<td class="ccCell" style="background-color: navy" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: blue" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: teal" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '<td class="ccCell" style="background-color: aqua" onclick="EasyRTE.setFontColor(\''+me.rteId+'\',this)"></td>';
        toolBar += '</tr>';
        toolBar += '</table>';
        return toolBar;
    }
    this.suppress = function (val)	{
        $E("preview_"+me.rteId).style.display = (val) ? "none" : "block";
    if ($E("publish_"+me.rteId)) {
        $E("publish_"+me.rteId).style.display = (val) ? "none" : "block";
    }
    }
    this.suppressPublish	= function () {
    if ($E("publish_"+me.rteId)) {
        $E("publish_"+me.rteId).style.display = "none";
    }
    }
    this.addArgument	= function (argName,argField,isField) {
        me.argumentList[me.argumentList.length] = { "name": argName, "field": argField, "isField": isField };
    }
    this.insertToolbar = function () {
        var xyz = document.createElement("DIV");
        xyz.setAttribute("id","toolbar"+me.frameId);
        $E(me.frame.id).parentNode.insertBefore(xyz,$E(me.frame.id));
        $E("toolbar"+me.frameId).innerHTML = me.toolbar();
    }
    this.create = function (divId,eW,eH) {
        me.editorW	= (eW) ? eW : me.editorW;
        me.editorH	= (eH) ? eH : me.editorH;
        divId           = (divId) ? divId : me.frameId;
        me.frameId	= 'theEditor_'+ me.rteId;
        
        var editorHTML = '<iframe contentEditable="true" designMode="On" src="/test.html" id="'+ me.frameId +'" name="'+ me.frameId +' width="100%" height="100%"></iframe>';
        $E(divId).innerHTML  = editorHTML;
        me.frame = $E(me.frameId);
        //if (!me.noControls)
          //  me.insertToolbar();
        me.connect();
    }
    this.formatBlogEntry	= function (blogText) {
        var HTML = '<div style="background-color: #d4d9dd; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%"><center>';
        HTML += '<div style="text-align: left;  background-image: url(images/bg1.gif); font-family: arial; font-size: 9pt; background-repeat: repeat-y; background-color: ghostwhite; width: 655px;  overflow: auto; padding: 20px">';
        HTML +="<span class='blogTitle'>"+ me.getTitle() +"</span><br />";
        HTML +="<span class='blogStamp'> datestamp published by <a href=\"mailto:me.com\">You</a></span><br /><br />";
        HTML += blogText;
        HTML += "<br /><a href='#' onclick='return false'>Comments</a>";
        HTML += "<br /><hr class='blogRule' />";
        HTML += '</div></center></div>';
        return HTML;
    }
    this.previewHandler = null;
    this.setPreviewHandler = function (handler)	{
        this.previewHandler = handler;
    }
    this.preview	= function () {
        if (this.previewHandler)
            this.previewHandler(this);
        else {
            var preview = Desktop.window.list['cloud-it-preview'];
            preview._open();
            $(preview.content).html(me.formatBlogEntry(me.getText()));
        }
    }
    this.getId	= function () {
        return me.rteId;
    }
    this.getFolderId	= function () {
        return me.folderId;
    }
    this.getTitle	= function () {
        return $E("blogTitle_"+me.folderId).value;
    }
    this.getText	= function () {
        return me.ref.innerHTML;
    }
    this.setFolderId = function (val) {
        me.folderId = val;
    }
    this.setText	= function (text) {
        me.ref.innerHTML = text+"&nbsp;";
        me.lastLook = me.ref.innerHTML;
    }
    this.setTarget	= function (URL) {
        if (URL === null)
            $E("saveBtn_"+me.rteId).style.display = "none";
        else if (URL) { me.ao = new EasyAjax(URL); }
    }
    this.setReturnHandler	= function (handler) {
        if (typeof(handler) == "function")
                me.saveHandler = handler;
    }
    this.handleSave	= function () {
        me.saveHandler(me);
    }
    this.save		= function () {
        var go = true;
        if (me.ao) {
            if (me.validator)
                    go = me.validator.validate();
            if (go)	{
                for (var i=0; i<me.argumentList.length; i++) {
                    if ($E(me.argumentList[i].isField)) {
                        me.ao.ARP(me.argumentList[i].name,me.EasyAjax.val($E(me.argumentList[i].field)));
                    } else {
                        me.ao.ARP(me.argumentList[i].name,me.argumentList[i].field);
                    }
                }
                me.ao.ARP("text",me.getText().trim());
                me.ao.SCF((me.saveHandler) ? me.handleSave : function () { alert("Saved");} );
                me.ao.post();
            }
        } else {
            alert("To use the built in save, a target URL must be set");
        }
    }
    this.publish = function () {
        if (confirm("Publish this entry? (After publishing, you can't edit again, only delete)")) {
            var ao = new EasyAjax("/miniblogs.php?action=publishEntry");
            for (var i=0; i<me.argumentList.length; i++) {
                if ($E(me.argumentList[i].field)) {
                    ao.ARP(me.argumentList[i].name,EasyAjax.val($E(me.argumentList[i].field)));
                }
            }
            ao.ARP("text",me.getText());
            ao.SCF(function () {
                    Desktop.refreshWindow(me.folderId);
            });
            ao.post();
        }
    }
    this.boldText	= function ()	{	document.execCommand("bold",false,null); this.frame.focus();				}
    this.italicText 	= function ()	{	document.execCommand("italic",false,null); this.frame.focus();				}
    this.struckText 	= function ()	{	document.execCommand("strikethrough",false,null); this.frame.focus();		}
    this.underlineText 	= function ()	{	document.execCommand("underline",false,null); this.frame.focus(); 			}
    this.insertHeadline = function ()	{	document.execCommand("formatblock",false,"<h3>");  this.frame.focus();		}
    this.insertQuote 	= function ()	{	document.execCommand("formatblock",false,"<pre>"); this.frame.focus();		}
    this.insertCode 	= function ()	{	document.execCommand("formatblock",false,"<code>"); this.frame.focus();		}
    this.insertRule 	= function ()	{	document.execCommand("insertHorizontalRule",false,null); this.frame.focus();}
    this.orderedList 	= function ()	{	this.frame.focus(); document.execCommand("insertorderedlist",null,null); this.frame.focus();	}
    this.unorderedList 	= function ()	{	this.frame.focus(); document.execCommand("insertunorderedlist",null,null); this.frame.focus();	}
    this.subscript	= function ()	{	document.execCommand("subscript",false,null); this.frame.focus();			}
    this.superscript	= function ()	{	document.execCommand("superscript",false,null); this.frame.focus();			}
    this.justify	= function ()	{	document.execCommand("justifyfull",false,null); this.frame.focus();			}
    this.justifyLeft	= function ()	{	document.execCommand("justifyleft",false,null); this.frame.focus();			}
    this.justifyRight	= function ()	{	document.execCommand("justifyright",false,null); this.frame.focus();		}
    this.justifyCenter	= function ()	{	document.execCommand("justifycenter",false,null); this.frame.focus();		}
    this.fontFamily	= function ()	{	document.execCommand("fontname",false,null); this.frame.focus();			}
    this.fontSize	= function ()	{	document.execCommand("fontsize",false,null); this.frame.focus();			}
    this.fontColor	= function ()   {       document.execCommand("forecolor",false,"red"); }
    this.highlight 	= function ()	{	document.execCommand("hilitecolor",false,"yellow"); this.frame.focus();		}
    this.indent		= function ()	{	document.execCommand("indent",false,null); this.frame.focus();				}
    this.outdent	= function ()	{	document.execCommand("outdent",false,null); this.frame.focus();				}
    this.increaseFont 	= function ()	{	if (window.addEventListener) document.execCommand("increasefontsize",false,null); else alert("Not supported in IE, use a modern browser to gain access to this feature (Firefox)"); }
    this.decreaseFont 	= function ()	{	if (window.addEventListener) document.execCommand("decreasefontsize",false,null); else alert("Not supported in IE, use a modern browser to gain access to this feature (Firefox)"); }
    this.insertHyperlink = function () {
        var url = prompt("Paste the URL to link to","http:\/\/");
        if (url)
        {
            var url = "javascript:EasyRTE.openLink('"+url+"')";
            document.execCommand("createLink",false,url);
        }
    }
    this.insertMedia = function ()	{
        if (window.addEventListener) {
            var video = prompt("Paste YouTube embed text here","<object...");
            if (video) {
                //use pasteHTML for IE, insertHTML for moz
                video = Aura.tools.extractVideo(video.trim())
                if (video) {
                    var ao = new EasyAjax('/features/media/embed');
                    ao.ARP("videoAttributes",video);
                    ao.SCF(function () {
                        document.execCommand("inserthtml", false, ao.getResponse());
                    });
                    ao.post();
                    
                }
            }
        } else {
            alert("This feature is only supported for modern browsers, not Internet Explorer or derivatives.  http:\/\/www.getfirefox.com for a modern browser.");
        }
    }
    this.insertImage = function (url) {
        alert('TBD');
    }
    this.setId	= function (id) {	 me.rteId = id; }
    //for when the iframe already exists, and the id has been passed in as a parameter in the constructor
    if (this.frame)	{
        if (!me.noControls) {
            me.insertToolbar();
        }
        me.connect();
        me.size();
    }
    return this;
}
EasyRTE.openLink = function (URL) {
    window.open(URL,'','scrollbars,location,status,toolbar,menu,resizable');
}
EasyRTE.setFontColor = function (rteId,cc) {
    document.execCommand("forecolor",false,cc.style.backgroundColor);
    $E("colorChart_"+rteId).style.display="none";
}
