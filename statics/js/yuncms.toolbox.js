if (!window.yuncms_toolbox) {
	// initialization yuncms_toolbox
	var yuncms_toolbox = window.yuncms_toolbox = {};

	// return client width
	yuncms_toolbox.getWidth	= window.innerWidth | document.body.clientWidth;

	// return client height
	yuncms_toolbox.getHeight = window.innerHeight | document.body.clientHeight;

	// return a div document object based on params
	yuncms_toolbox.divMaker	= function(attribute, style, parentObj) {
		var obj = document.createElement('div');
		for (var key in attribute) {
			if (key == 'class') {
				obj.setAttribute('class', attribute[key]);
				obj.setAttribute('className', attribute[key]);	// compatible IE
			} else {
				obj.setAttribute(key, attribute[key]);
			}
		}
		for (var key in style) {
			obj.style[key] = style[key];
		}
		if (!parentObj) {
			parentObj = document.body;
		}
		parentObj.appendChild(obj);
		return obj;
	};

	// event bind
	yuncms_toolbox.bind = function bind(obj, action, func) {
		if (window.addEventListener) {
			obj.addEventListener( action, function(event) {
				func(obj, event);
			}, false);
		} else if (window.attachEvent) { // compatible IE
			obj.attachEvent('on' +action, function(event) {
				func(obj, event);
			});
		}
	};

	// event unbind
	yuncms_toolbox.unbind = function(obj, action, func) {
		if (window.removeEventListener) {
			obj.removeEventListener(action, func , false);
		} else if (window.detachEvent) { // compatible IE
			obj.detachEvent(action, func);
		}
	};

	// a dragger lib class
	yuncms_toolbox.drag = function(dragObj, moveObj) {
		var isDrag = false;
		var x = 0, y = 0;
		dragObj.style.cursor = 'move';
		// drag mouse
		var _mousemove = function(obj, event) {
			if (!isDrag) {
				return
			}
			moveObj.style.left	= x +  event.clientX + 'px';
			moveObj.style.top	= y +  event.clientY + 'px';
			parseInt(moveObj.style.top) < 0 && (moveObj.style.top = '0');
			yuncms_toolbox.bind(document.body, 'mouseup', _mouseup);
			return false;
		};
		// release mouse
		var _mouseup = function() {
			if (!isDrag) {
				return
			}
			yuncms_toolbox.unbind(document.body, 'mousemove', _mousemove);
			yuncms_toolbox.unbind(document.body, 'mouseup', _mouseup);
			isDrag = false;
			return false;
		};
		var _mousedown = function(obj, event) {
			if (isDrag) {
				return;
			}
			isDrag = true;
			x	= parseInt(moveObj.style.left) - event.clientX;
			y	= parseInt(moveObj.style.top)  - event.clientY;
			yuncms_toolbox.bind(document.body, 'mousemove', _mousemove);
			yuncms_toolbox.bind(document.body, 'mouseup', _mouseup);
		};
		// mouse down
		yuncms_toolbox.bind(dragObj, 'mousedown', _mousedown);
	};

	// get client width
	yuncms_toolbox.getWidth	= function() {
		var width	= window.innerWidth;
		if (width == undefined) { // compatible IE
			width	= document.documentElement.clientWidth;
		}
		return width;
	};

	// get client height
	yuncms_toolbox.getHeight = function() {
		var height	= window.innerHeight;
		if (height == undefined) { // compatible IE
			height	= document.documentElement.clientHeight;
			height	= ((window.screen.height - 100) < height) ? window.screen.height - 100 : height;
		}
		return height;
	};

	// tools window
	yuncms_toolbox.toolWin	= function() {
		var self = this;
		self.isopen = false;
		var yuncmsToolbar		=  null;
		var yuncmsToolbarBody	= null;
		var openStatus			= true;
		var cmdButton	= [
			{'class':'ico1', 'title':'\u8f6c\u8f7d', 'event':'reproduce','condition':'true'},
			{'class':'ico2', 'title':'\u7f16\u8f91', 'event':'edit', 'condition':'yuncms_toolbox.isMySite && typeof(contentid) !="undefined"'},
			{'class':'ico4', 'title':'\u5220\u9664', 'event':'delete', 'condition':'yuncms_toolbox.isMySite && typeof(contentid) !="undefined"'},
			{'class':'ico7', 'title':'\u7f16\u8f91', 'event':'visualedit', 'condition':'yuncms_toolbox.isMySite && typeof(pageid) !="undefined"'},
			{'class':'ico5', 'title':'\u7ba1\u7406', 'event':'admin','condition':'true'},
			{'class':'ico6', 'title':'\u9000\u51fa', 'event':'logout','condition':'true'}
		];
		self.open = function() {
			if (self.isopen) {
				return false;
			}
			self.isopen = true;
			var a, btn, logo, width;
			// build UI
			width	= yuncms_toolbox.getWidth();
			yuncmsToolbar	= yuncms_toolbox.divMaker({"class":"yuncms-toolbar"}, {'top':'20px', 'left':(width-120+'px')});
			logo = yuncms_toolbox.divMaker({"class":"yuncms-toolbar-logo"}, {}, yuncmsToolbar);
			yuncmsToolbarBody = yuncms_toolbox.divMaker({"class":"yuncms-toolbar-body"}, {}, yuncmsToolbar);
			var yuncmsToolbarFoot = yuncms_toolbox.divMaker({"class":"yuncms-toolbar-foot"}, {}, yuncmsToolbar);
			yuncms_toolbox.divMaker({"class":"yuncms-toolbar-bg"}, {}, yuncmsToolbarBody);
			yuncmsToolbarFoot.innerHTML	 = '<a id="yuncms_openstatus" class="yuncms-toolbar-size-switch-drop yuncms-toolbar-open-status" href="javascript:void(0);" onclick="yuncms_toolbox.toolWin.sizeToggle()" target="_self"></a>';
			yuncmsToolbarFoot.innerHTML += '<a class="yuncms-toolbar-size-switch-close" href="javascript:void(0);" onclick="yuncms_toolbox.toolWin.close();" target="_self"></a>';
			yuncmsToolbarFoot.innerHTML += '<div class="yuncms-toolbar-shadow-radius"></div>';
			// prevent default dragging
			logo.ondragstart=function (){return false;};
			// build button
			for (var i in cmdButton) {
				btn	= cmdButton[i];
				if (!eval(btn['condition'])) {
					continue;
				}
				a = document.createElement('a');
				a.setAttribute('href'		, "javascript:void((function(){yuncms_toolbox_domain_admin='"+yuncms_toolbox.adminUrl+"';yuncms_toolbox_ver=2;yuncms_toolbox_cmd='"+btn['event']+"';if(typeof(yuncms_toolbox)!='undefined'){yuncms_toolbox.ready(yuncms_toolbox_cmd);return}var%20e=document.createElement('script');e.setAttribute('src',yuncms_toolbox_domain_admin+'statics/js/yuncms.toolbox.js');e.setAttribute('charset','utf-8');document.body.appendChild(e)})())");
				a.setAttribute('class'		, 'yuncms-toolbar-btn yuncms-toolbar-' + btn['class']);
				a.setAttribute('className'	, 'yuncms-toolbar-btn yuncms-toolbar-' + btn['class']);
				a.setAttribute('title'		, btn['title']);
				a.setAttribute('id'			, 'yuncms_toolbox_menu_' + btn['event']);
				a.setAttribute('onclick'	, 'yuncms_toolbox.ready("' + btn['event'] + '");return false;');
				a.setAttribute('target'		, '_self');
				a.innerHTML = '<div style="display:none;">'+btn['title']+'</div>';
				yuncmsToolbarBody.appendChild(a);
			}
			yuncms_toolbox.drag(logo, yuncmsToolbar);
		};
		self.sizeToggle = function() {
			var displayValue, btns = yuncmsToolbarBody.getElementsByTagName('a');
			if (openStatus) {
				document.getElementById('yuncms_openstatus').setAttribute('class', 'yuncms-toolbar-size-switch-drop yuncms-toolbar-min-status');
				document.getElementById('yuncms_openstatus').setAttribute('className', 'yuncms-toolbar-size-switch-drop yuncms-toolbar-min-status');
				displayValue = 'none';
				openStatus = false;
			} else {
				document.getElementById('yuncms_openstatus').setAttribute('class', 'yuncms-toolbar-size-switch-drop yuncms-toolbar-open-status');
				document.getElementById('yuncms_openstatus').setAttribute('className', 'yuncms-toolbar-size-switch-drop yuncms-toolbar-open-status');
				displayValue = 'block';
				openStatus = true;
			}
			for (var a in btns) {
				if (typeof (btns[a]) == 'object') {
					 btns[a].style.display = displayValue;
				}
			}
		};
		self.close = function() {
			document.body.removeChild(yuncmsToolbar);
			self.isopen = false;
		};
	};

	// main window
	yuncms_toolbox.mainWin	= function() {
		var self = this;
		self.isopen = false;
		self.miniwin = false;
		var messageboxContainer = null;
		var messageboxHd	= null;
		var messageboxBd	= null;
		var messageboxFt	= null;
		var messageMainWin	= null;
		var closeRefresh	= false;
		var option	= {};
		self.open = function(o) {
			if (self.isopen) {
				return false;
			}
			self.isopen = true;
			var headContent,headTitle,sizeControl,sizeControlItem,a,ifm,left;
			option = o || {};
			option.width	= option.width || 850;
			option.height	= option.height || 400;
			option.title	= option.title || '';
			if (option.refresh) closeRefresh = true;
			left = (yuncms_toolbox.getWidth() - option.width) / 2;
			if (left < 120) {
				left = 0;
			} else {
				left += 'px';
			}
			messageboxContainer = yuncms_toolbox.divMaker({'class':'yuncms-messagebox'}, {'width':(option.width+12)+'px','top':0, 'left':left});
			messageboxHd	= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-head'}, {}, messageboxContainer);
			messageboxBd	= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-body'}, {}, messageboxContainer);
			messageboxFt	= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-foot'}, {}, messageboxContainer);
			headContent		= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-head-content'}, {}, messageboxHd);
			headTitle		= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-head-title'}, {}, headContent);
			headTitle.innerHTML	+= '<div class="yuncms-messagebox-head-ico"></div>';
			headTitle.innerHTML	+= '<h2>' + option.title + '</h2>';
			sizeControl		= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-size-control'}, {}, messageboxHd);
			sizeControlItem	= ['minsize','closepanel'];
			for (var i=0; i < sizeControlItem.length; i++) {
				a = document.createElement('a');
				a.setAttribute('class', 'yuncms-messagebox-head-' + sizeControlItem[i]);
				a.setAttribute('className', 'yuncms-messagebox-head-' + sizeControlItem[i]);
				a.setAttribute('href', 'javascript:;');
				a.setAttribute('target', '_self');
				sizeControl.appendChild(a);
				yuncms_toolbox.bind(a, 'click' , yuncms_toolbox.messageBox[sizeControlItem[i]]);
				yuncms_toolbox.bind(a, 'mousedown', function(){return false;});
				a.ondragstart = function() {return false;}
				a.cancelBubble = true;
				a = undefined;
			}
			yuncms_toolbox.divMaker({'class':'yuncms-messagebox-head-left'}, {}, messageboxHd);
			yuncms_toolbox.divMaker({'class':'yuncms-messagebox-head-right'}, {}, messageboxHd);
			messageMainWin	= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-body-content'}, {'width':(option.width+'px'), 'height':(option.height-40+'px')}, messageboxBd);
			if (option.url) {
				var ifm	= document.createElement('iframe');
				ifm.src	= option.url;
				ifm.style.width		= '100%';
				ifm.style.height	= '100%';
				ifm.frameBorder		= 0;
				var refreshCount = 0;
				ifm.onload = function() {
					refreshCount += 1;
					if (refreshCount == 7) {
						yuncms_toolbox.messageBox.closepanel();
					}
				}
				messageMainWin.appendChild(ifm);
			}
			if (typeof (option.content) == 'object') {
				messageMainWin.appendChild(option.content);
			}
			if (typeof (option.content) == 'string') {
				messageMainWin.innerHTML = option.content;
			}				
			yuncms_toolbox.divMaker({'class':'yuncms-messagebox-foot-center'},{}, messageboxFt);
			yuncms_toolbox.divMaker({'class':'yuncms-messagebox-foot-left'},{}, messageboxFt);
			yuncms_toolbox.divMaker({'class':'yuncms-messagebox-foot-right'},{}, messageboxFt);
			yuncms_toolbox.drag(messageboxHd, messageboxContainer);
			setInterval(function(){_isClose();}, 1000);
		};
		self.minsize = function() {
			if (!self.miniwin) {
				messageboxBd.style.display	= 'none';
				self.miniwin = true;
			} else {
				messageboxBd.style.display	= 'block';
				self.miniwin = false;
			}
			return false;
		};
		self.fullsize = function() {
			//	娌¤繖鍔熻兘
		};
		self.closepanel = function() {
			document.body.removeChild(messageboxContainer);
			self.isopen = false;
			closeRefresh && location.reload();
			return false;
		};
		var _isClose = function() {
			if (location.hash == "#close") {
				self.closepanel();
				location.hash = '';
			}
		}
	};

	// load css
	var _loadCSS = function() {
		var cssUrl	= yuncms_toolbox.adminUrl + 'statics/css/yuncms-toolbox.css';
		try {
			window.document.head.innerHTML += '<link type="text/css" rel="stylesheet" href="' + cssUrl + '" />';
		} catch (e) {	// compatible IE
			window.document.createStyleSheet(cssUrl);
		}
	};

	// get meta:keywords
	var _getTags = function() {
		var metas = document.getElementsByTagName('meta'),l;
		if (!metas || !(l = metas.length)) {
			return '';
		}
		for(var i=0, l=metas.length, meta; i<l, meta=metas[i]; i++) {
			if (meta.name.toLowerCase() == 'keywords') {
				var tags = meta.content.split(/[,|\s]/);
				for (var i=0,l=tags.length,item;i<l,item=tags[i];i++) {
					if (Math.ceil(item.replace(/[^\x00-\xff]/gm, '__').length) > 16) {
						delete(tags[i]);
					}
				}
				return tags.join(' ');
			}
		}
		return '';
	}

	// action a click
	var _jump = function(url) {
		var a = document.createElement('a');
		a.setAttribute('href', url);
		a.setAttribute('target', '_blank');
		document.body.appendChild(a);
		try {
			a.click();
		} catch (e) {
			try {
				var e = document.createEvent('MouseEvents');
				e.initEvent( 'click', true, true );
				a.dispatchEvent(e);
			} catch (e) {
				location.href = url;
			}
		}
	}

	// initialization
	yuncms_toolbox.ready = function(cmd) {
		if (typeof (yuncms_toolbox_ver) == 'undefined' || yuncms_toolbox_ver != 2) {
			alert('\u60a8\u7684\u7f51\u7f16\u5de5\u5177\u680f\u8fc7\u65e7,\n\u8bf7\u91cd\u65b0\u4e0b\u8f7d');
			return;
		}
		var start = function() {
			yuncms_toolbox.adminUrl	= yuncms_toolbox_domain_admin;
			var temp_arr = yuncms_toolbox.adminUrl.split('.');
			temp_arr.shift();
			yuncms_toolbox.domain	= temp_arr.join('.').replace(/\/+/, '');
			yuncms_toolbox_domain_admin = undefined;
			yuncms_toolbox.isMySite = (function(){var d = /([^:]*)/.exec(yuncms_toolbox.domain)[0],r = new RegExp(d);return r.test(location.host);})()
			_loadCSS();
			try {
				yuncms_toolbox.toolWin = new yuncms_toolbox.toolWin();
			}
			catch (e) {}
			if (!yuncms_toolbox.toolWin.isopen) {
				yuncms_toolbox.toolWin.open();
			}
		}
		var reproduce = function() {
			try {
				yuncms_toolbox.messageBox = new yuncms_toolbox.mainWin();
			}
			catch (e) {}
			if (!yuncms_toolbox.messageBox.isopen) {
				var url = yuncms_toolbox.adminUrl;
				url += 'admin.php?app=system&controller=toolbox&action=add';
				url += '&source=' + encodeURIComponent(window.location.href);
				url += '&sourcetitle=' + encodeURIComponent(window.document.title);
				url += '&tags=' + encodeURIComponent(_getTags().replace(/,/g, ' '));
				var height = yuncms_toolbox.getHeight() || 400;
				height -= 32;
				yuncms_toolbox.messageBox.open({
					'width'	: 900,
					'height': height,
					'title'	: '\u4e00\u952e\u8f6c\u8f7d',
					'url'	: url
				});
			}
		}
		var edit = function() {
			try {
				yuncms_toolbox.messageBox = new yuncms_toolbox.mainWin();
			}
			catch (e) {}
			if (!yuncms_toolbox.messageBox.isopen) {
				var url	 = yuncms_toolbox.adminUrl;
				url		+= 'admin.php?app=system&controller=content&action=miniedit';
				url		+= '&contentid=' + (contentid || '') + '&url=' + location.href;
				var height = yuncms_toolbox.getHeight() || 400;
				height -= 32;
				yuncms_toolbox.messageBox.open({
					'width'	: 900,
					'height': height,
					'title'	: '\u7f16\u8f91\u5185\u5bb9',
					'url'	: url,
					'refresh': true
				});
			}
		}
		var del = function() {
			try {
				yuncms_toolbox.messageBox = new yuncms_toolbox.mainWin();
			}
			catch (e) {}
			if (!yuncms_toolbox.messageBox.isopen) {
				var contentIfm, statusBar, okBtn, canelBtn;
				contentIfm	= document.createElement('div');
				contentIfm.style.textAlign	= 'center';
				contentIfm.innerHTML	= '<p style="padding: 12px 0; font-size: 16px;">\u786e\u5b9a\u8981\u5220\u9664\u8fd9\u7bc7\u6587\u7ae0\u4e48?</p>';
				statusBar	= yuncms_toolbox.divMaker({'class':'yuncms-messagebox-body-statusbar'}, {}, contentIfm);
				canelBtn	= document.createElement('a');
				canelBtn.setAttribute('class', 'yuncms-messagebox-body-statusbar-cancel');
				canelBtn.setAttribute('className', 'yuncms-messagebox-body-statusbar-cancel');
				canelBtn.href	= 'javascript:;';
				canelBtn.innerHTML = '\u53d6\u6d88';
				statusBar.appendChild(canelBtn);
				okBtn	= document.createElement('input');
				okBtn.setAttribute('class', 'yuncms-messagebox-body-statusbar-ok');
				okBtn.setAttribute('className', 'yuncms-messagebox-body-statusbar-ok');
				okBtn.type	= 'button';
				okBtn.value	= '\u786e\u5b9a';
				okBtn.style.cursor	= 'pointer';
				statusBar.appendChild(okBtn);
				yuncms_toolbox.bind(okBtn, 'click', function() {
					var ifm = document.createElement('iframe');
					ifm.src	= yuncms_toolbox.adminUrl + 'admin.php?app=system&controller=content&action=delete&contentid='+contentid;
					ifm.style.display	= 'none';
					document.body.appendChild(ifm);
					yuncms_toolbox.bind(ifm, 'load', function() {
						location.href='http://'+location.host;
					});
				});
				yuncms_toolbox.bind(canelBtn, 'click', function() {
					yuncms_toolbox.messageBox.closepanel();
				});
				yuncms_toolbox.messageBox.open({
					'width'	: 240,
					'height': 120,
					'title'	: '\u662f\u5426\u5220\u9664?',
					'content': contentIfm
				});
			}
		}
		var visualedit = function() {
			_jump(yuncms_toolbox.adminUrl+'admin.php?app=page&controller=page&action=view&pageid='+pageid);
		}
		var admin = function() {
			_jump(yuncms_toolbox.adminUrl);
		}
		var logout = function() {
			var ifm = document.createElement('iframe');
			ifm.src	= yuncms_toolbox.adminUrl + 'admin.php?app=system&controller=admin&action=logout';
			ifm.style.display	= 'none';
			document.body.appendChild(ifm);
			yuncms_toolbox.bind(ifm, 'load', function() {
				document.location.reload();
			});
		}
		switch (cmd) {
		case 'start':
			start();
			if (yuncms_toolbox.isMySite) {
				if (window.ENV) {
					_jump(yuncms_toolbox.adminUrl+'admin.php?app=special&controller=online&action=design&contentid='+ENV.contentid+'&pageid='+ENV.pageid);
				} else if (window.contentid) {
					edit();
				} else if (window.pageid) {
					_jump(yuncms_toolbox.adminUrl+'admin.php?app=page&controller=page&action=view&pageid='+pageid);
				}
			} else {
				reproduce();
			}
			break;
		case 'reproduce':
			reproduce();
			break;
		case 'edit':
			edit();
			break;
		case 'delete':
			del();
			break;
		case 'visualedit': 
			visualedit();
			break;
		case 'admin':
			admin();
			break;
		case 'logout':
			logout();
			break;
		}
	};
}
window.yuncms_toolbox.ready(yuncms_toolbox_cmd);