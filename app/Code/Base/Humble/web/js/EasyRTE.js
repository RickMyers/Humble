var RTE	= [];
function EasyRTE(frameId,eW,eH,buildControls)
{
	var me 			= this;
	this.base		= "";   //base directory for images
	this.ref		= null;
	this.noControls = (buildControls === false);
	this.scanTimer	= null;
	this.lastLook	= null;
	this.imageLayer = null;
	this.textChangeHandler = null;
	this.blogId		= null;
	this.idField	= null; //when they do first save of new blog entry, an id value is assigned, this retains it. later, make this a JS field, and not an HTML element
	this.folderId	= null;
	this.arguments  = [];
	this.saveHandler = null;
	this.validator	= null;
	this.ao			= null;  //ajax object
	this.editorW 	= (eW) ? eW : 500;  //establish defaults dimensions
	this.editorH 	= (eH) ? eH : 270;
	this.frameId	= (frameId) ? frameId : null;
	this.rteId 		= (frameId) ? frameId : EasyAjax.uniqueId(10); //not the best way of getting a unique id, but i will refactor the whole bloody thing later, and will deal with it then
	RTE[this.rteId] = this;
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
        if (!$E(this.frameId)) {
            alert(this.frameId+' can not be found to turn into an RTE');
        }
		if (!$E(this.frameId).getAttribute("contentEditable")!=true)	{
			$E(this.frameId).setAttribute("contentEditable","true");
			$E(this.frameId).contentEditable = true;
		}
		$E(this.frameId).style.overflow = "auto";
        $E(this.frameId).style.whiteSpace = "normal";
	}
	this.connect	= function ()	{
		me.ref = (me.frameId) ? $E(me.frameId) : null;
        if (me.ref) {
            me.ref.setAttribute("contentEditable","true");
        }
	}
	this.size = function ()	{
		if (me.frame) {
			me.editorW += ''; //make string
			me.editorH += '';
			if (me.editorW)	me.frame.style.width = parseInt(me.editorW)+((me.editorW.indexOf("%")==-1)?"px":"%");
			if (me.editorH) me.frame.style.height = parseInt(me.editorH)+((me.editorH.indexOf("%")==-1)?"px":"%");
		}
	}
	this.toolbar	= function () {
		var toolBar =	'<table style="clear: left" cellspacing="0" cellpadding="0"><tr>';
		//toolBar += '<td><a id="saveBtn_'+me.rteId+'" href="#" onclick="RTE[\''+me.rteId+'\'].save(); return false;"><img style="cursor: pointer" src="'+this.base+'/images/core/rte/rteSaveIcon.png" title="Save Work" /></a></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].boldText()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteBoldIcon.png" title="Bold" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].italicText()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteItalicIcon.png" title="Italic" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].underlineText()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteUnderlineIcon.png" title="Underline" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].struckText()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteStrikeIcon.png" title="Strike-Out" /></td>';
        toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertImage()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteImageIcon.png" title="Insert an image with formatting" /></td>'
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertHyperlink()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteLinkIcon.png" title="Link"  /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertHeadline()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteHeadlineIcon.png" title="Section Headline" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertRule()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteLineIcon.png" title="Horizontal Line" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertQuote()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteQuoteIcon.png" title="Block Quote" /></td>';
		//toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].insertCode()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteCodeIcon.png" title="Image" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].subscript()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteSubscriptIcon.png" title="Subscript" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].superscript()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteSuperscriptIcon.png" title="Superscript" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].increaseFont()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteIncreaseFont.png" title="Increase Font Size" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].decreaseFont()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteDecreaseFont.png" title="Decrease Font Size" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justifyLeft()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteJustifyLeft.png" title="Align Left" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justifyCenter()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteJustifyCenter.png" title="Center Align" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justifyRight()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteJustifyRight.png" title="Align Right" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].orderedList()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteOrderedList.png" title="Ordered List" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].unorderedList()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteUnorderedList.png" title="Unordered List" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].justify()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteJustify.png" title="Justify" /></td>'
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].fontColor()" style="cursor: pointer" src="'+this.base+'/images/core/rte/rteSetFontRed.png" title="Set Font Color to Red" /></td>';
		toolBar += '<td><img onclick="RTE[\''+me.rteId+'\'].highlight()" style="cursor: pointer" src="'+this.base+'/images/core/rte/hilight.png" title="Hilight Selected Text" /></td>';
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
	this.addArgument	= function (argName,argField,isField) {
		me.arguments[me.arguments.length] = { "name": argName, "field": argField, "isField": isField };
	}
	this.insertToolbar = function () {
		var xyz = document.createElement("DIV");
		xyz.setAttribute("id","toolbar"+me.frameId);
		$E(me.frame.id).parentNode.insertBefore(xyz,$E(me.frame.id));
		$E("toolbar"+me.frameId).innerHTML = me.toolbar();
	}
	this.getId	= function () {
		return me.rteId;
	}
	this.getText	= function () {
        var text = me.ref.innerHTML.trim();
		return ((text == '<br>') || (text=='<br />') || (text=='<br/>')) ? "" : text;
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
				for (var i=0; i<me.arguments.length; i++) {
					if ($E(me.arguments[i].isField))
						me.ao.add(me.arguments[i].name,me.EasyAjax.val($E(me.arguments[i].field)));
					else
						me.ao.add(me.arguments[i].name,me.arguments[i].field);
				}
				me.ao.add("text",me.getText().trim());
				me.ao.callback((me.saveHandler) ? me.handleSave : function () { alert("Saved");} );
				me.ao.post();
			}
		} else {
			alert("To use the built in save, a target URL must be set");
		}
	}
	this.boldText 		= function ()	{	document.execCommand("bold",false,null); this.frame.focus();				}
	this.italicText 	= function ()	{	document.execCommand("italic",false,null); this.frame.focus();				}
	this.struckText 	= function ()	{	document.execCommand("strikethrough",false,null); this.frame.focus();		}
	this.underlineText 	= function ()	{	document.execCommand("underline",false,null); this.frame.focus(); 			}
	this.insertHeadline = function ()	{	document.execCommand("formatblock",false,"<h3>");  this.frame.focus();		}
	this.insertQuote 	= function ()	{	document.execCommand("formatblock",false,"<pre>"); this.frame.focus();		}
    this.insertCode 	= function ()	{	document.execCommand("formatblock",false,"<code>"); this.frame.focus();		}
	this.insertRule 	= function ()	{	document.execCommand("insertHorizontalRule",false,null); this.frame.focus();}
	this.orderedList 	= function ()	{	this.frame.focus(); document.execCommand("insertorderedlist",null,null); this.frame.focus();	}
	this.unorderedList 	= function ()	{	this.frame.focus(); document.execCommand("insertunorderedlist",null,null); this.frame.focus();	}
	this.subscript		= function ()	{	document.execCommand("subscript",false,null); this.frame.focus();			}
	this.superscript	= function ()	{	document.execCommand("superscript",false,null); this.frame.focus();			}
	this.justify		= function ()	{	document.execCommand("justifyfull",false,null); this.frame.focus();			}
	this.justifyLeft	= function ()	{	document.execCommand("justifyleft",false,null); this.frame.focus();			}
	this.justifyRight	= function ()	{	document.execCommand("justifyright",false,null); this.frame.focus();		}
	this.justifyCenter	= function ()	{	document.execCommand("justifycenter",false,null); this.frame.focus();		}
	this.fontFamily		= function ()	{	document.execCommand("fontname",false,null); this.frame.focus();			}
	this.fontSize		= function ()	{	document.execCommand("fontsize",false,null); this.frame.focus();			}
	this.fontColor		= function ()   {   document.execCommand("forecolor",false,"red"); }
	this.highlight	 	= function ()	{	document.execCommand("hilitecolor",false,"yellow"); this.frame.focus();		}
	this.indent			= function ()	{	document.execCommand("indent",false,null); this.frame.focus();				}
	this.outdent		= function ()	{	document.execCommand("outdent",false,null); this.frame.focus();				}
	this.increaseFont 	= function ()	{	if (window.addEventListener) document.execCommand("increasefontsize",false,null); else alert("Not supported in IE, use a modern browser to gain access to this feature (Firefox)"); }
	this.decreaseFont 	= function ()	{	if (window.addEventListener) document.execCommand("decreasefontsize",false,null); else alert("Not supported in IE, use a modern browser to gain access to this feature (Firefox)"); }
	this.insertHyperlink = function ()  {
		var url = prompt("Paste the URL to link to","http://");
		if (url) {
			var url = "javascript:EasyRTE.openLink('"+url+"')";
			document.execCommand("createLink",false,url);
		}
	}
    //<iframe width="560" height="315" src="http://www.youtube.com/embed/p6wk1XySBTk" frameborder="0" allowfullscreen></iframe>
	this.insertImage = function (url) {
        alert('hi');
	}
	this.setId	= function (id) {	 me.rteId = id; }
	//for when the iframe already exists, and the id has been passed in as a parameter in the constructor
	if (this.frame)	{
		if (!me.noControls)
			me.insertToolbar();
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