(function($){
	/* custom function to remove duplicate items from an array */
	$.removeDuplicate = function(array) {
		if(!(array instanceof Array)) return;
        label:for(var i = 0; i < array.length; i++ ) {  
			for(var j=0; j < array.length; j++ ) {
				if(j == i) continue;
				if(array[j] == array[i]) {
					array = array.slice(j);
					continue label;
				}
			}
		}
		return array;
	}

	/* new selector to check if the tags submitted are inline elements */
	$.extend($.expr[':'],{
	    inline: function(element) {
	        return (
				$(element).is('a') ||
				$(element).is('em') ||
				$(element).is('font') ||
				$(element).is('span') ||
				$(element).is('strong') ||
				$(element).is('u')
			);
		}
	});
	
	// uEditor class
	var uEditor = function(element, settings) {
		$.extend(this, {
			settings : settings,
			createDOM : function() {
				this.textarea = element;
				this.container = document.createElement("div");
				this.iframe = document.createElement("iframe");
				this.input = document.createElement("input");

				$(this.input).attr({
					type : 'hidden',
					name : $(this.textarea).attr('name'),
					value : $(this.textarea).attr('value') // old textarea value
				});

				$(this.textarea).addClass('uEditorTextarea');
				$(this.textarea).attr('name', $(this.textarea).attr('name') + "uEditorTextarea");
				$(this.textarea).hide();

				$(this.container).addClass(settings.containerClass);
				$(this.iframe).addClass('uEditorIframe');

				this.toolbar = new uEditorToolbar(this);
				$(this.container).append(this.toolbar.itemsList);
				$(this.container).append(this.iframe);
				$(this.container).append(this.input);
				$(this.container).hide();

				this.input.uEditorObject = this;
				$(this.textarea).replaceWith(this.container);
			},
			
			writeDocument : function() {
				/* HTML template into which the HTML Editor content is inserted */
				var documentTemplate = '\
					<html>\
						<head>\
							<style>\
							    body {\
                                	font-family: Arial, Helvetica, sans-serif;\
                                	font-size:14px;\
                                	line-height:20px;\
                                	background: #fff;\
                                	color:#333;\
                                }\
                                a {\
                                	color:#187076 !important;\
                                }\
                                h3 {\
                                	color:#00B3BE;\
                                	font-family:Georgia,Times,serif;\
                                	font-size:20px;\
                                	margin:10px 0;\
                                }\
							</style>\
						</head>\
						<body id="iframeBody">\
							INSERT:CONTENT:END\
						</body>\
					</html>\
				';
				
				documentTemplate = documentTemplate.replace(/INSERT:CONTENT:END/, $(this.input).val());
				this.iframe.contentWindow.document.open();
				this.iframe.contentWindow.document.write(documentTemplate);
				this.iframe.contentWindow.document.close();
			},
			
			convertSPANs : function(replaceSpans) {
				var iframe = this.iframe;
				if (replaceSpans) {
					/* Replace styled spans with their semantic equivalent */
					var spans = $(this.iframe.contentWindow.document).find('span');
					if(spans.length) spans.each(function() {
						var children = $(this).contents();
						var replacementElement = null;
						var parentElement = null;
						
						var style = $(this).attr("style").replace(/\s*/gi,'');
						/* Detect type of span style */
						switch (style) {
							case "font-style:italic;":
								parentElement = replacementElement = iframe.contentWindow.document.createElement("em");
								break;

							case "font-weight:bold;":
								parentElement = replacementElement = iframe.contentWindow.document.createElement("strong");
								break;

							case "font-weight:bold;font-style:italic;":
								emElement = iframe.contentWindow.document.createElement("em");
								strongElement = iframe.contentWindow.document.createElement("strong");
								$(emElement).append(strongElement);
								parentElement = emElement;
								replacementElement = strongElement;
								break;

							case "font-style:italic;font-weight:bold;":
								emElement = iframe.contentWindow.document.createElement("em");
								strongElement = iframe.contentWindow.document.createElement("strong");
								$(emElement).append(strongElement);
								parentElement = emElement;
								replacementElement = strongElement;
								break;

							default:
								break;
						}
						children.each(function() {
							$(replacementElement).append(this);
						});
						$(this).before(parentElement);
						$(this).remove();
					});
				}
				else {
				/* Replace em and strong tags with styled spans */
					$(iframe.contentWindow.document).find('em').each(function() {
						var children = $(this).contents();
						var span = iframe.contentWindow.document.createElement('span');
						$(span).css('font-style', 'italic');
						children.each(function() {
							$(span).append(this);
						});
						$(this).replaceWith(span);
					});

					$(iframe.contentWindow.document).find('strong').each(function() {
						var children = $(this).contents();
						var span = iframe.contentWindow.document.createElement('span');
						$(span).css('font-weight', 'bold');
						children.each(function() {
							$(span).append(this);
						});
						$(this).replaceWith(span);
					});
				}
			},
			
			makeEditable : function() {
				var self = this;
				try {
					this.iframe.contentWindow.document.designMode = "on";
				}
				catch (e) {
					/* setTimeout needed to counteract Mozilla bug whereby you can't immediately change designMode on newly created iframes */
					setTimeout((function(){self.makeEditable()}), 250);
					return false;
				}
				if(!$.browser.msie) this.convertSPANs(false);
				$(this.container).show();
				$(this.textarea).show();
				$(this.iframe.contentWindow.document).mouseup(function() { self.toolbar.checkState(self) });
				$(this.iframe.contentWindow.document).keyup(function() { self.toolbar.checkState(self) });
				$(this.iframe.contentWindow.document).keydown(function(e){ self.detectPaste(e); });

				this.locked = false;
			},
			
			modifyFormSubmit : function() {
				var self = this;
				var form = $(this.container).parents('form');
				form.submit(function() {
					return self.updateuEditorInput();
				});
			},
			
			insertNewParagraph : function(elementArray, succeedingElement) {
				var body = $(this.iframe).contents().find('body');
				var paragraph = this.iframe.contentWindow.document.createElement("p");
				$(elementArray).each(function(){
					$(paragraph).append(this);
				});
				body.append(paragraph);
			},
			
			paragraphise : function() {
				if (settings.insertParagraphs && this.wysiwyg) {
					var bodyNodes = $(this.iframe).contents().find('body').contents();

					/* Remove all text nodes containing just whitespace */
					bodyNodes.each(function() {
						// something like $(this).is('#text')); would be great
						if (this.nodeName.toLowerCase() == "#text" &&
							this.data.search(/^\s*$/) != -1) {
							this.data = '';
						}
					});
					
					var self = this;
					var removedElements = new Array();

					bodyNodes.each(function() {
						if($(this).is(':inline') || this.nodeType == 3) {
							removedElements.push(this);
							$(this).remove();
						}
						else if($(this).is('br')) {
							if(!$(this).is(':last-child')) {
								/* If the current break tag is followed by another break tag  */
								if($(this).next().is('br')) {
									/* Remove consecutive break tags  */
									while($(this).next().is('br')) {
										$(this).remove();
									}
									if (removedElements.length) {
										self.insertNewParagraph(removedElements, this);
										removedElements = new Array();
									}
								}
								/* If the break tag appears before a block element */
								else if (!$(this).is(':inline')  && this.nodeType != 3) {
									$(this).remove();
								}
								else if (removedElements.length) {
									removedElements.push(this.cloneNode(true));
									$(this).remove();
								}
								else {
									$(this).remove();
								}
							}
						}
						else if (removedElements.length) {
							self.insertNewParagraph(removedElements, this);
							removedElements = new Array();
						}

					});

					if (removedElements.length > 0)
					{
						this.insertNewParagraph(removedElements);
					}
				}
			},
			
			switchMode : function() {
				if (!this.locked) {
					this.locked = true;
					
					/* Switch to HTML source */
					if (this.wysiwyg) {
						this.updateuEditorInput();
						$(this.textarea).val($(this.input).val());
						$(this.iframe).replaceWith(this.textarea);
						this.toolbar.disable();
						this.wysiwyg = false;
						this.locked = false;
					}
					/* Switch to WYSIWYG */
					else {
						this.updateuEditorInput();
						$(this.textarea).replaceWith(this.iframe);
						this.writeDocument(this.input.value);
						this.toolbar.enable();
						this.makeEditable();
						this.wysiwyg = true;
					}
				}
			},
			
			detectPaste : function(e) {
				if(!e.ctrlKey || e.keyCode != 86 || this.cleaning) return;
				var self = this;
				setTimeout(function(e){
					self.cleanSource();
				}, 100);
			},
			
			cleanSource : function() {
				this.cleaning = true;
				var html = "";
				var body = $(this.iframe.contentWindow.document).find("body");
				
				if (!$.browser.msie) this.convertSPANs(true);
				
				$.each(settings.undesiredTags, function(tag, action) {
					body.find(tag).each(function() {
						switch(action) {
							case 'remove' :
								$(this).remove();
								break;
							case 'extractContent' :
								var parentTag = $(this);
								parentTag.contents().each(function() {
									parentTag.before(this);
								});
								parentTag.remove();
								break;
							default :
								$(this).remove();
								break;
						}
					});
				});

				if (this.wysiwyg) html = body.html();
				else html = $(this.textarea).val();

				/* Remove leading and trailing whitespace */
				html = html.replace(/^\s*/, "");
				html = html.replace(/\s*$/, "");

				/* remove comments */
				html = html.replace(/<--.*-->/, "");
				
				/* format content inside html tags */
				html = html.replace(/<[^>]*>/g, function(match) {
					/* replace single quotes */
					match = match.replace(/='(.*)' /g, '="$1" ');
					/* check if the atribute is allowed */

					match = match.replace(/ ([^=]+)="?([^"]*)"?/g, function(match, attribute, value){
						if( $.inArray(attribute, settings.allowedAttributes) == -1) return '';
						switch(attribute) {
							case 'id' :
								if($.inArray(value, settings.allowedIDs) == -1) return '';
							case 'class' :
								if($.inArray(value, settings.allowedClasses) == -1) return '';							
							default :
								return match;
						}
					});
					return match.toLowerCase();
				});

				/* Remove style attribute inside any tag */
				html = html.replace(/ style="[^"]*"/g, "");
				/* Replace improper BRs */
				html = html.replace(/<br>/g, "<br />");
				/* Remove BRs right before the end of blocks */
				html = html.replace(/<br \/>\s*<\/(h1|h2|h3|h4|h5|h6|li|p)/g, "</$1");
				/* Shift the <br /> at the end of an inline element just after it */
				html = html.replace(/(<br \/>)*\s*(<\/[^>]*>)/g, "$2$1");
/*
				// Remove BRs alone in tags
				html = html.replace(/<[^\/>]*>(<br \/>)*\s*<\/[^>]*>/g, "$1");
*/	
				/* Replace improper IMGs */
				html = html.replace(/(<img [^>]+[^\/])>/g, "$1 />");
				/* Remove empty tags */
				html = html.replace(/(<[^\/]>|<[^\/][^>]*[^\/]>)\s*<\/[^>]*>/g, "");
				/* Final cleanout for MS Word cruft */
				html = html.replace(/<\?xml[^>]*>/g, "");
				html = html.replace(/<[^ >]+:[^>]*>/g, "");
				html = html.replace(/<\/[^ >]+:[^>]*>/g, "");

				if (this.wysiwyg) $(this.iframe.contentWindow.document).find("body").html(html);
				else $(this.textarea).val(html);
				
				$(this.input).val(html);
				this.cleaning = false;
			},
			
			refreshDisplay : function() {
				if (this.wysiwyg) $(this.iframe.contentWindow.document).find("body").html($(this.input).val());
				else $(this.textarea).val($(this.input).val());
			},
			
			updateuEditorInput : function() {
				if (this.wysiwyg) {
					/* Convert spans to semantics in Mozilla */
					this.paragraphise();
					this.cleanSource();
				}
				else $(this.input).val($(this.textarea).val());
			},
			
			init : function(settings) {
				/* Detects if designMode is available */
				if (typeof(document.designMode) != "string" && document.designMode != "off") return;
				this.locked = true;
				this.cleaning = false;
				this.DOMCache = "";
				this.wysiwyg = true;
				this.createDOM();
				this.writeDocument(); // Fill editor with old textarea content
				this.makeEditable();
				this.modifyFormSubmit();
			}				
		});
		this.init();
	};

	// uEditorToolbar class
	var uEditorToolbar = function(editor) {
		$.extend(this, {
			createDOM : function() {
				var self = this;
				/* Create toolbar ul element */
				this.itemsList = document.createElement("ul");
				$(this.itemsList).addClass("uEditorToolbar");

				/* Create toolbar items */
				$.each(this.uEditor.settings.toolbarItems, function(i, name) {
					if(name == "formatblock") self.addSelect(name);
					else self.addButton(name);
				});
			},
			
			addButton : function(buttonName) {
				var button = $.uEditorToolbarItems[buttonName];
				var menuItem = $(document.createElement("li"));
				var buttonTitle = (typeof(this.uEditor.settings.translation[buttonName]) != 'undefined' ) ?
					this.uEditor.settings.translation[buttonName] : button.label;
				var link = $(document.createElement("a")).attr({
					'title' : buttonTitle,
					'class' : button.className,
					'href' : 'javascript:void(0)'
				});
				button.editor = this.uEditor;
				$(link).data('action', button);
				$(link).data('editor', this.uEditor);
				link.bind('click', button.action);
				link.append(document.createTextNode(buttonTitle));
				menuItem.append(link);
				$(this.itemsList).append(menuItem);
			},

			addSelect : function(selectName) {
				var self = this;
				var select= $.uEditorToolbarItems[selectName];
				var menuItem = $(document.createElement("li")).attr('class', 'uEditorEditSelect');
				var selectElement = $(document.createElement("select")).attr({
					'name' : select.name,
					'class' : select.className
				});
				$(selectElement).data('editor', this.uEditor);
				$(selectElement).change(select.action);

				var legend = $(document.createElement("option"));
				var selectLabel = (typeof(this.uEditor.settings.translation[selectName]) != 'undefined' ) ?
					this.uEditor.settings.translation[selectName] : select.label;
				legend.append(document.createTextNode(selectLabel));
				selectElement.append(legend);
				
				$.each(this.uEditor.settings.selectBlockOptions, function(i, value) {		
					var option = $(document.createElement("option")).attr('value',value);
					option.append(document.createTextNode(self.uEditor.settings.translation[value]));
					selectElement.append(option);
				});

				menuItem.append(selectElement);
				$(this.itemsList).append(menuItem);
			},
			
			disable : function() {
				$(this.itemsList).toggleClass("uEditorSource");
				$(this.itemsList).find('li select').attr('disabled','disabled');
			},

			enable : function() {
				/* Change class to enable buttons using CSS */
				$(this.itemsList).toggleClass("uEditorSource");
				$(this.itemsList).find("select").removeAttr("disabled");
			},
			
			checkState : function(uEditor, resubmit) {
				if (!resubmit) {
					/* Allow browser to update selection before using the selection */
					setTimeout(function(){uEditor.toolbar.checkState(uEditor, true); return true;}, 500);
					return true;
				}

				var selection = null;
				var range = null;
				var parentnode = null;
				
				/* Turn off all the buttons */
				$(uEditor.toolbar.itemsList).find('a').removeClass('on');

				/* IE selections */
				if (uEditor.iframe.contentWindow.document.selection) {
					selection = uEditor.iframe.contentWindow.document.selection;
					range = selection.createRange();
					try {
						parentnode = $(range.parentElement());
					}
					catch (e) {
						return false;
					}
				}
				/* Mozilla selections */
				else {
					try {
						selection = uEditor.iframe.contentWindow.getSelection();
					}
					catch (e) {
						return false;
					}
					range = selection.getRangeAt(0);
					parentnode = $(range.commonAncestorContainer);
				}
				
				while (parentnode.nodeType == 3) { // textNode
					parentnode = parentnode.parent();
				}
				while (!parentnode.is('body')) {
					if(parentnode.is('a')) uEditor.toolbar.setState("link", "on");
					else if(parentnode.is('em'))uEditor.toolbar.setState("italic", "on");
					else if(parentnode.is('strong')) uEditor.toolbar.setState("bold", "on");
					else if(parentnode.is('span') || parentnode.is('p')) {
						if(parentnode.css('font-style') == 'italic') uEditor.toolbar.setState("italic", "on");
						if(parentnode.css('font-weight') == 'bold') uEditor.toolbar.setState("bold", "on");
					}
					else if(parentnode.is('ol')) {
						uEditor.toolbar.setState("orderedlist", "on");
						uEditor.toolbar.setState("unorderedlist", "off");
					}
					else if(parentnode.is('ul')) {
						uEditor.toolbar.setState("orderedlist", "on");
						uEditor.toolbar.setState("unorderedlist", "off");
					}
					else uEditor.toolbar.setState("formatblock", parentnode[0].nodeName.toLowerCase());
					parentnode = parentnode.parent();
				}						
			},

			setState: function(state, status) {
				if (state != "SelectBlock") $(this.itemsList).find('.' + $.uEditorToolbarItems[state].className).addClass('on');
				else $(this.itemsList).find('.' + $.uEditorToolbarItems[state].className).val(status);			
			},
			
			init : function(editor) {
				this.uEditor = editor;
				this.createDOM();
			}
		});
		this.init(editor);
	};

	/* uEditorToolbarItems class, can be extended using $.extend($.uEditorToolbarItems, { (...) } */
	var uEditorToolbarItems = function() {

		/* Defines singleton logic */
		uEditorToolbarItemsClass = this.constructor;
		if(typeof(uEditorToolbarItemsClass.singleton) != 'undefined') return uEditorToolbarItemsClass.singleton;
		else uEditorToolbarItemsClass.singleton = this;

		/* Extends class with items properties, will only be executed once */
		$.extend(uEditorToolbarItemsClass.singleton, {
			bold : {
				className : 'uEditorButtonBold',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					editor.iframe.contentWindow.document.execCommand('bold', false, null);
					editor.toolbar.setState('bold', "on");
				}
			},
			italic : {
				className : 'uEditorButtonItalic',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					editor.iframe.contentWindow.document.execCommand('italic', false, null);
					editor.toolbar.setState('italic', "on");
				}
			},
			link : {
				className : 'uEditorButtonHyperlink',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					if ($(this).hasClass("on"))  {
						editor.iframe.contentWindow.document.execCommand("Unlink", false, null);
						return;
					}
					var selection = $(editor.iframe).getSelection();
					if (selection == "") {
						alert(editor.settings.translation.selectTextToHyperlink);
						return;
					}
					var url = prompt(editor.settings.translation.linkURL, "http://");
					if (url != null) {			
						editor.iframe.contentWindow.document.execCommand("CreateLink", false, url);
						editor.toolbar.setState('link', "on");
					}
				}
			},
			orderedlist : {
				className : 'uEditorButtonOrderedList',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					editor.iframe.contentWindow.document.execCommand('insertorderedlist', false, null);
					editor.toolbar.setState('orderedlist', "on");
				}
			},
			unorderedlist : {
				className : 'uEditorButtonUnorderedList',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					editor.iframe.contentWindow.document.execCommand('insertunorderedlist', false, null);
					editor.toolbar.setState('unorderedlist', "on");
				}
			},
			image : {
				className : 'uEditorButtonImage',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					var imgLoc = prompt(editor.settings.translation.imageLocation, "");
					if (imgLoc != null && imgLoc != "") {
						var alt = prompt(editor.settings.translation.imageAlternateText, "");
						alt = alt.replace(/"/g, "'");
						$(editor.iframe).appendToSelection('img', {
							'src' : imgLoc,
							'alt' : alt
						}, null, true);
					}
				}
			},
			htmlsource : {
				className : 'uEditorButtonHTML',
				action : function() {
					var editor = $.data(this, 'editor');
					editor.switchMode();
				}
			},
			formatblock : {
				className : 'uEditorSelectformatblock',
				action : function(){
					var editor = $.data(this, 'editor');
					if(!editor.wysiwyg) return;
					editor.iframe.contentWindow.document.execCommand('formatblock', false, $(this).val());
				}
			}
		});
	};

	$.uEditorToolbarItems = new uEditorToolbarItems();

    $.fn.extend({
		getSelection : function() {
			if(!this.is('iframe')) return;
	        else iframe = this[0];
			return (iframe.contentWindow.document.selection) ?
	            iframe.contentWindow.document.selection.createRange().text :
	            iframe.contentWindow.getSelection().toString();
		},

		appendToSelection : function(nodeType, attr, contentText, singleTag) {
			if(!this.is('iframe')) return;
	        else iframe = this[0];
			var selection, range;
			if($.browser.msie) {
				var html;
				html = '<' + nodeType;
				$.each(attr, function(label, value) { html += ' ' + label + '="' + value + '"' });
				if(singleTag) html += ' />';
				else {
					html += '>';
					if(contentText && typeof(contentText) != 'undefined') html += contentText;
					html += '</' + nodeType + '>';
				}
				selection = iframe.contentWindow.document.selection;
				range = selection.createRange();
				if($(range.parentElement()).parents('body').is('#iframeBody ')) return;
				range.collapse(false);
				range.pasteHTML(html);
			}
			else {
				selection = iframe.contentWindow.getSelection();
				range = selection.getRangeAt(0);
				range.collapse(false);
				var element = iframe.contentWindow.document.createElement(nodeType);
				$(element).attr(attr);						
				if(contentText && typeof(contentText) != 'undefined') $(element).append(document.createTextNode(contentText));
				range.insertNode(element);
			}
		},
		
		uEditor : function(settings) {
			var defaultTranslation = {
				bold : 'Bold',
				italic : 'Italic',
				link : 'Hyperlink',
				unorderedlist : 'Unordered List',
				orderedlist : 'Ordered List',
				image : 'Insert image',
				htmlsource : 'HTML Source',
				formatblock : 'Change Block Type',
				h1 : "Heading 1",
				h2 : "Heading 2",
				h3 :"Heading 3",
				h4 : "Heading 4",
				h5 : "Heading 5",
				h6 : "Heading 6",
				p : "Paragraph",
				selectTextToHyperlink : "Please select the text you wish to hyperlink.",
				linkURL : "Enter the URL for this link:",
				imageLocation : "Enter the location for this image:",
				imageAlternateText : "Enter the alternate text for this image:"
			};
			
			/* settings for content pasted from a web page */
			var defaultUndesiredTags = {
				'script' : 'remove',
				'meta' : 'remove',
				'link' : 'remove',
				'basefont' : 'remove',
				'noscript' : 'extractContent',
				'nobr' : 'extractContent',
				'object' : 'remove',
				'applet' : 'remove',
				'form': 'extractContent',
				'fieldset': 'extractContent',
				'input' : 'remove',
				'select': 'remove',
				'textarea' : 'remove',
				'button' : 'remove',
				'isindex' : 'remove',
				'label' : 'extractContent',
				'legend' : 'extractContent',
				'div' : 'extractContent',
				'table' : 'extractContent',
				'thead' : 'extractContent',
				'tbody' : 'extractContent',
				'tr' : 'extractContent',
				'td' : 'extractContent',
				'tfoot' : 'extractContent',
				'col' : 'extractContent',
				'colgroup' : 'extractContent',
				'center' : 'extractContent',
				'area' : 'remove',
				'dir' : 'extractContent',
				'frame' : 'remove',
				'frameset' : 'remove',
				'noframes' : 'remove',
				'iframe' : 'remove'
				// there sure is some more elements to be added to the list
			};

			var defaultAllowedAttributes = [
				'class',
				'id',
				'href',
				'title',
				'alt',
				'src'
			];

			settings = $.extend({
				insertParagraphs : true,
				stylesheet : 'uEditorContent.css',
				toolbarItems : ['bold','italic','link','image','orderedlist','unorderedlist','htmlsource','formatblock'],
				selectBlockOptions : ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'],
				undesiredTags : defaultUndesiredTags,
				allowedClasses : new Array(),
				allowedIDs : new Array(),
				allowedAttributes : defaultAllowedAttributes,
				containerClass : 'uEditor',
				translation : defaultTranslation
			}, settings);
			
			settings.undesiredTags = (settings.undesiredTags.length != defaultUndesiredTags.length) ?
				$.removeDuplicate($.merge(settings.undesiredTags, defaultUndesiredTags)) : settings.undesiredTags;

			settings.allowedAttributes = (settings.allowedAttributes.length != defaultAllowedAttributes.length) ?
				$.removeDuplicate($.merge(settings.allowedAttributes, defaultAllowedAttributes)) : settings.allowedAttributes;
			
			return this.each(function(){
				new uEditor(this, settings);
			});
		}
	});
})(jQuery);