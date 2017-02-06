if ($.cookie == undefined) {
    jQuery.cookie = function (key, value, options) {

        // key and value given, set cookie...
        if (arguments.length > 1 && (value === null || typeof value !== "object")) {
            options = jQuery.extend({}, options);

            if (value === null) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            return (document.cookie = [
                encodeURIComponent(key), '=',
                options.raw ? String(value) : encodeURIComponent(String(value)),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
        return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
    };

}

function flexiGridAjax(methodName, data,successFun) {
    /// <summary>
    /// 
    /// </summary>
    /// <param name="methodName">ajax方法名称</param>
    /// <param name="data"></param>
    $.ajax({
        type: 'Post',
        cache: false,
        dataType: 'text',
        url: "../../Ajax/" + methodName,
        data: data,
        //contentType: 'application/json; charset=utf-8',
        async: true,
        success: successFun
    });

}

var _flexiGridJsAddColumn_ColumnSelectedCallback = null;
var _flexiGridJsAddColumn_ColumnCancelCallback = null;
var _flexiGridJsAddColumn_selector = null;
var flexiGridJsAddColumn = {
    init: function(selector, columnSelectedCallback, columnCancelCallback) {
        /// <summary>
        /// 初始化回调
        /// </summary>
        /// <param name="columnSelectedCallback">列选中显示回调</param>
        /// <param name="columnCancelCallback">列取消选中回调</param>
        _flexiGridJsAddColumn_ColumnSelectedCallback = columnSelectedCallback;
        _flexiGridJsAddColumn_ColumnCancelCallback = columnCancelCallback;
        $(selector).live("click", function (e) {
            e.stopPropagation();
            flexiGridJsAddColumn._show($(this));
        });
        $("body").click(function() {
            $("#jsAddColumnContainer").addClass("hidden");
        });
    },
    _show: function($this) {
        /// <summary>
        /// 显示选择层
        /// </summary>
        $("#jsAddColumnContainer").removeClass("hidden").css({ "top": $this.offset().top + $this.height() + 10 + "px", "left": $this.offset().left + $this.width() / 2 - $("#jsAddColumnContainer").width()/2 + "px" });

    },
    _columnSelected: function (columnName) {
        /// <summary>
        /// 列选中显示回调
        /// </summary>
        /// <param name="columnName"></param>
        if (_flexiGridJsAddColumn_ColumnSelectedCallback != null) {
            try {
                _flexiGridJsAddColumn_ColumnSelectedCallback(columnName);
            } catch (e) {

            } 
        }
    },
    _columnCancel: function (columnName) {
        /// <summary>
        /// 列取消选中回调
        /// </summary>
        /// <param name="columnName"></param>
        if (_flexiGridJsAddColumn_ColumnCancelCallback != null) {
            try {
                _flexiGridJsAddColumn_ColumnCancelCallback(columnName);
            } catch (e) {

            }
        }
    }
};

(function ($) {
	/*
	 * jQuery 1.9 support. browser object has been removed in 1.9 
	 */
    
    function StringBuilder() {
        this._strings = new Array();
    }
    StringBuilder.prototype.append = function (str) {
        this._strings.push(str);
    };
    StringBuilder.prototype.appendArray = function (array) {
        if (array != undefined && array._strings.length > 0) {
            for (var i = 0; i < array._strings.length; i++) {
                this._strings.push(array._strings[i]);
            }
        }
    };

    StringBuilder.prototype.toString = function () {
        return this._strings.join("");
    };
    

    var browser = $.browser;
	
	if (!browser) {
	    function uaMatch(ua) {
	        ua = ua.toLowerCase();

	        var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
				/(webkit)[ \/]([\w.]+)/.exec(ua) ||
				/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
				/(msie) ([\w.]+)/.exec(ua) ||
				ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
				[];

	        return {
	            browser: match[1] || "",
	            version: match[2] || "0"
	        };
	    }

		var matched = uaMatch( navigator.userAgent );
		browser = {};

		if ( matched.browser ) {
			browser[ matched.browser ] = true;
			browser.version = matched.version;
		}

		// Chrome is Webkit, but Webkit is also Safari.
		if ( browser.chrome ) {
			browser.webkit = true;
		} else if ( browser.webkit ) {
			browser.safari = true;
		}
	}

    function KeyValue() {
        this.Key = -1;
        this.Value = -1;
    }

    /*!
     * START code from jQuery UI
     *
     * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
     * Dual licensed under the MIT or GPL Version 2 licenses.
     * http://jquery.org/license
     *
     * http://docs.jquery.com/UI
     */
     
    if(typeof $.support.selectstart != 'function') {
        $.support.selectstart = "onselectstart" in document.createElement("div");
    }
    
    if(typeof $.fn.disableSelection != 'function') {
        $.fn.disableSelection = function() {
            return this.bind( ( $.support.selectstart ? "selectstart" : "mousedown" ) +
                ".ui-disableSelection", function( event ) {
                event.preventDefault();
            });
        };
    }
    
    /* END code from jQuery UI */
    
	$.addFlex = function (t, p) {
		if (t.grid) return false; //return if already exist
		p = $.extend({ //apply default properties
			height: 200, //default height
			width: 'auto', //auto width
			striped: true, //apply odd even stripes
			novstripe: false,
			minwidth: 30, //min width of columns
			minheight: 80, //min height of columns
			resizable: true, //allow table resizing
			url: false, //URL if using data from AJAX
			method: 'POST', //data sending method
			dataType: 'json', //type of data for AJAX, either xml or json
			errormsg: '连接错误',
			usepager: false,
			nowrap: false,//是否内容不换行
			
			page: 1, //current page
			total: 1, //total pages
			useRp: true, //use the results per page select box
			rp: 15, //results per page
			rpOptions: [10, 15, 20, 30, 50,100,200], //allowed per-page values
			title: false,
			idProperty: 'id',
			pagestat: '显示从 {from} 条数据到 {to} 条数据，共 {total} 条数据',
			pagetext: '第',
			outof: ' 共',
			findtext: 'Find',
			params: [], //allow optional parameters to be passed around
			procmsg: '数据加载中, 请稍后 ...',
			query: '',
			qtype: '',
			nomsg: '暂无相关数据',
			minColToggle: 1, //minimum allowed column to be hidden
			showToggleBtn: true, //show or hide column toggle popup
			hideOnSubmit: true,
			autoload: true,
			blockOpacity: 0.5,
			preProcess: false,
			addTitleToCell: false, // add a title attr to cells with truncated contents
			dblClickResize: false, //auto resize column by double clicking
			onDragCol: false,
			onToggleCol: false,
			onChangeSort: false,
			onDoubleClick: false,
			onSuccess: false,
		    singleSelect:true,
		    onError: false,
		    nohresize:true,
			fixColumnWidth: -10,//-80,//毎列宽度修正
			fixAllWidth: -50,//总宽度修正
			onSubmit: false, //using a custom populate function
            __mw: { //extendable middleware function holding object
                datacol: function(p, col, val) { //middleware for formatting data columns
                    var _col = (typeof p.datacol[col] == 'function') ? p.datacol[col](val) : val; //format column using function
                    if(typeof p.datacol['*'] == 'function') { //if wildcard function exists
                        return p.datacol['*'](_col); //run wildcard function
                    } else {
                        return _col; //return column without wildcard
                    }
                }
            },
            getGridClass: function(g) { //get the grid class, always returns g
                return g;
            },
            datacol: {}, //datacol middleware object 'colkey': function(colval) {}
            colResize: true, //from: http://stackoverflow.com/a/10615589
            colMove: true
		}, p);
		$(t).show() //show if hidden
			.prop({
				cellPadding: 0,
				cellSpacing: 0,
				border: 0
			}) //remove padding and spacing
			.removeProp('width'); //remove width properties
	    //create grid class
	    var isinit = 0;
		var g = {
			hset: {},
			rePosDrag: function () {
				var cdleft = 0 - this.hDiv.scrollLeft;
				if (this.hDiv.scrollLeft > 0) cdleft -= Math.floor(p.cgwidth / 2);
				$(g.cDrag).css({
					top: g.hDiv.offsetTop + 1
				});
				var cdpad = this.cdpad;
				var cdcounter=0;
				$('div', g.cDrag).hide();
				$('thead tr:first th:visible', this.hDiv).each(function () {
			


				    var n = $('thead tr:first th:visible', g.hDiv).index(this);
				    var cdpos = parseFloat($('div', this).width());
				    					if (cdleft == 0) cdleft -= Math.floor(p.cgwidth / 2);
				    					cdpos = cdpos + cdleft + cdpad;
				    					if (isNaN(cdpos)) {
				    						cdpos = 0;
				    					}

				    //					if (browser.webkit) {//修正chrome 列
				    //					    cdpos += 1;
				    //					}
				    //
				    //
				    //					var tmpLeft = 0;
				    //					if (!browser.mozilla) {
				    //					    if (browser.webkit) {
				    //					        if (isinit>3) {
				    //					            tmpLeft = cdpos-2;
				    //					        } else {
				    //					            tmpLeft = cdpos;
				    //					        }
				    //					       // tmpLeft = cdpos;
				    //					    } else {
				    //					        tmpLeft = cdpos - cdcounter;
				    //					    }
				    //					} else {
				    //					    tmpLeft = cdpos;
				    //					}
				    
				    					if (browser.msie) {
				    					    if (browser.version <= 10) {
				    					       
				    					        if (browser.version <= 7) {
				    					            cdpos = cdpos;
				    					        } else {
				    					            cdpos = cdpos - 1;
				    					        }
				    					    }
				    					}

				    					if (browser.webkit) {
				    					    cdpos = cdpos -1;
									    }

				    					$('div:eq(' + n + ')', g.cDrag).css({
				    					    //'left': (!(browser.mozilla) ? cdpos - cdcounter : cdpos) + 'px'
				    					    // 'left': tmpLeft + 'px'
				    					    'left': (!(browser.mozilla) ? cdpos + cdcounter : cdpos) + 'px'
				    					}).show();
				    					cdleft = cdpos;
				    					cdcounter++;

//					var cdpos = parseInt($('div', this).width());
//					if (cdleft == 0) cdleft -= Math.floor(p.cgwidth / 2);
//					cdpos = cdpos + cdleft + cdpad;
//					if (isNaN(cdpos)) {
//						cdpos = 0;
//					}
//				    
////					if (browser.webkit) {//修正chrome 列
////					    cdpos += 1;
////					}
////
////
////					var tmpLeft = 0;
////					if (!browser.mozilla) {
////					    if (browser.webkit) {
////					        if (isinit>3) {
////					            tmpLeft = cdpos-2;
////					        } else {
////					            tmpLeft = cdpos;
////					        }
////					       // tmpLeft = cdpos;
////					    } else {
////					        tmpLeft = cdpos - cdcounter;
////					    }
////					} else {
////					    tmpLeft = cdpos;
////					}
//					$('div:eq(' + n + ')', g.cDrag).css({
//					    //'left': (!(browser.mozilla) ? cdpos - cdcounter : cdpos) + 'px'
//					    // 'left': tmpLeft + 'px'
//					    'left': (!(browser.mozilla) ? cdpos + cdcounter : cdpos) + 'px'
//					}).show();
//					cdleft = cdpos;
//					cdcounter++;
				});

			    isinit++;
			},
			fixHeight: function (newH) {
				newH = false;
				if (!newH) newH = $(g.bDiv).height();
				var hdHeight = $(this.hDiv).height();
				$('div', this.cDrag).each(
					function () {
						$(this).height(newH + hdHeight);
					}
				);
				var nd = parseInt($(g.nDiv).height(), 10);
				if (nd > newH) $(g.nDiv).height(newH).width(150);
				else $(g.nDiv).height('auto').width('auto');
				$(g.block).css({
					height: newH,
					marginBottom: (newH * -1)
				});
				var hrH = g.bDiv.offsetTop + newH;
				if (p.height != 'auto' && p.resizable) hrH = g.vDiv.offsetTop;
				$(g.rDiv).css({
					height: hrH
				});
			},
			dragStart: function (dragtype, e, obj) { //default drag function start
                if (dragtype == 'colresize' && p.colResize === true) {//column resize
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					var n = $('div', this.cDrag).index(obj);
					var ow = $('th:visible div:eq(' + n + ')', this.hDiv).width();
                    $(obj).addClass('dragging').siblings().hide();
					$(obj).prev().addClass('dragging').show();
					this.colresize = {
						startX: e.pageX,
						ol: parseFloat(obj.style.left, 10),
						ow: ow,
						n: n
					};
					$('body').css('cursor', 'col-resize');
				} else if (dragtype == 'vresize') {//table resize
					var hgo = false;
					$('body').css('cursor', 'row-resize');
					if (obj) {
						hgo = true;
						$('body').css('cursor', 'col-resize');
					}
					this.vresize = {
						h: p.height,
						sy: e.pageY,
						w: p.width,
						sx: e.pageX,
						hgo: hgo
					};
             
				} else if (dragtype == 'colMove') {//column header drag
                    $(e.target).disableSelection(); //disable selecting the column header
                    if((p.colMove === true)) {
                        $(g.nDiv).hide();
                        $(g.nBtn).hide();
                        this.hset = $(this.hDiv).offset();
                        this.hset.right = this.hset.left + $('table', this.hDiv).width();
                        this.hset.bottom = this.hset.top + $('table', this.hDiv).height();
                        this.dcol = obj;
                        this.dcoln = $('th', this.hDiv).index(obj);
                        this.colCopy = document.createElement("div");
                        this.colCopy.className = "colCopy";
                        this.colCopy.innerHTML = obj.innerHTML;
                        if (browser.msie) {
                            this.colCopy.className = "colCopy ie";
                        }
                        $(this.colCopy).css({
                            position: 'absolute',
                            'float': 'left',
                            display: 'none',
                            textAlign: obj.align
                        });
                        $('body').append(this.colCopy);
                        $(this.cDrag).hide();
                    }
				}
				$('body').noSelect(true);
			},
			dragMove: function (e) {
				if (this.colresize) {//column resize
					var n = this.colresize.n;
					var diff = e.pageX - this.colresize.startX;
					var nleft = this.colresize.ol + diff;
					var nw = this.colresize.ow + diff;
				    if (nw > p.minwidth) {
						$('div:eq(' + n + ')', this.cDrag).css('left', nleft);
						this.colresize.nw = nw;
					}
				} else if (this.vresize) {//table resize
					var v = this.vresize;
					var y = e.pageY;
					var diff = y - v.sy;
					if (!p.defwidth) p.defwidth = p.width;
					if (p.width != 'auto' && !p.nohresize && v.hgo) {
						var x = e.pageX;
						var xdiff = x - v.sx;
						var newW = v.w + xdiff;
						if (newW > p.defwidth) {
							this.gDiv.style.width = newW + 'px';
							p.width = newW;
						}
					}
					var newH = v.h + diff;
					if ((newH > p.minheight || p.height < p.minheight) && !v.hgo) {
						this.bDiv.style.height = newH + 'px';
						p.height = newH;
						this.fixHeight(newH);
					}
					v = null;
				} else if (this.colCopy) {
					$(this.dcol).addClass('thMove').removeClass('thOver');
					if (e.pageX > this.hset.right || e.pageX < this.hset.left || e.pageY > this.hset.bottom || e.pageY < this.hset.top) {
						//this.dragEnd();
						$('body').css('cursor', 'move');
					} else {
						$('body').css('cursor', 'pointer');
					}

				    $(this.colCopy).css({
				        "top": e.pageY + 10+"px",
				        "left": e.pageX + 20+"px",
				        display: 'block',
				        "opacity":" 0.8",
				        "z-index": 100
				    });

//					$(this.colCopy).css({
//					    top: e.pageY + 10,
//					    left: e.pageX + 20,
//					    display: 'none'
//					});
				}
			},
			dragEnd: function () {
			    $(".dragColTip").remove();
				if (this.colresize) {
					var n = this.colresize.n;
					var nw = this.colresize.nw;
//				    if (browser.webkit) { //修正chrome 列
//				        nw += 2;
				    //				    }
				    
            

				    $('th:visible div:eq(' + n + ')', this.hDiv).css('width', nw);
					$('tr', this.bDiv).each(
						function () {
							var $tdDiv = $('td:visible div:eq(' + n + ')', this);
							$tdDiv.css('width', nw);
							g.addTitleToCell($tdDiv);
						}
					);
					this.hDiv.scrollLeft = this.bDiv.scrollLeft;
					$('div:eq(' + n + ')', this.cDrag).siblings().show();
					$('.dragging', this.cDrag).removeClass('dragging');
					this.rePosDrag();
					this.fixHeight();
					this.colresize = false;

					
//					if ($.cookie) {//$.cookies改成$.cookie开启cookie保存列宽
//						var name = p.colModel[n].name;		// Store the widths in the cookies
//						$.cookie('flexiwidths/'+name, nw);
//					}

                    //调整显示隐藏列按钮位置
					var tmpWidth1 = $("table", g.hDiv).width();
					if (tmpWidth1 < $(window).width()) {
					    tmpWidth1=$(window).width();
					}
					var tmpWindowLeft1 = tmpWidth1 - 25;
				    $("#btnShowOrHide").css("left", tmpWindowLeft1 + "px");

					if (typeof ReSizeFlexgridScrollbar != undefined) {
					    ReSizeFlexgridScrollbar();
					}

				    //TODO 保存到数据库
					//var name = p.colModel[n].name;		// Store the widths in the cookies
					//console.log('flexiwidths/' + name, nw);

				    flexiGridAjax("FlexiGridAjax_SetColumnWidth", {
				        "pageamethod": $.trim($("#pageamethod").val()),
				        "columnname": p.colModel[n].etmname,
				        "width": nw
				    });


				} else if (this.vresize) {
				    this.vresize = false;
				    if (typeof ReSizeScrollbar != undefined) {
				        ReSizeScrollbar();
				    }
				} else if (this.colCopy) {
				   // alert(1);
				    //noSelect
                    //TODO 调整列完成
					$(this.colCopy).remove();
					if (this.dcolt !== null) {
						if (this.dcoln > this.dcolt) $('th:eq(' + this.dcolt + ')', this.hDiv).before(this.dcol);
						else $('th:eq(' + this.dcolt + ')', this.hDiv).after(this.dcol);
						this.switchCol(this.dcoln, this.dcolt);
						$(this.cdropleft).remove();
						$(this.cdropright).remove();
						this.rePosDrag();
						if (p.onDragCol) {
							p.onDragCol(this.dcoln, this.dcolt);
						}
					}
					this.dcol = null;
					this.hset = null;
					this.dcoln = null;
					this.dcolt = null;
					this.colCopy = null;
					$('.thMove', this.hDiv).removeClass('thMove');
					$(this.cDrag).show();

				    setTimeout(function() {
				        //保存列位置
				        var columnPostition = "";
				        $("th", g.hDiv).each(function () {
				            columnPostition += $(this).attr("etmname") + ",";
				        });
                        //TODO 保存到数据库
				        //console.log(columnPostition);
				        flexiGridAjax("FlexiGridAjax_SetColumnPosition", {
				            "pageamethod": $.trim($("#pageamethod").val()),
				            "columnposition": columnPostition
				        });
				    }, 500);
				}
				$('body').css('cursor', 'default');
				$('body').noSelect(false);
			},
			toggleCol: function (cid, visible,clearContent) {
			    var ncol = $("th[axis='col" + cid + "']", this.hDiv)[0];
			    if (ncol == undefined) {
			        return;
			    }
				var n = $('thead th', g.hDiv).index(ncol);
				var cb = $('input[value=' + cid + ']', g.nDiv)[0];


				if (visible == null) {
					visible = ncol.hidden;
				}
				if ($('input:checked', g.nDiv).length < p.minColToggle && !visible) {
					return false;
				}
				if (visible) {
					ncol.hidden = false;
					$(ncol).show();
					cb.checked = true;
				    //显示
				} else {
					ncol.hidden = true;
					$(ncol).hide();
					cb.checked = false;
				    //隐藏
				}
			    //console.log(p.url);
			    //console.log(p.url.substring(0, p.url.indexOf("List?method=")));

			    var arry11 = new Array();
			    $('.ndcol1 input', g.nDiv).not($('.ndcol1 input:checked', g.nDiv)).each(function () {
				    arry11.push($(this).parents("tr").eq(0).attr("etmname"));
				    //console.log($(this).parents("tr").eq(0).attr("etmname"));
				});
			    //etmname

			    //TODO 保存到数据库
//			    $.ajax({
//			        type: 'Post',
//			        cache: false,
//			        dataType: 'json',
//			        url: p.url.substring(0, p.url.indexOf("List?method="))+"SetColumns",
//			        data: { "str": arry11.join(","), "pageName": $.trim($("#pagename").val()) },
//			        //contentType: 'application/json; charset=utf-8',
//			        async: true
			    //			    });
//			    console.log(visible ? "true" : "false");
//			    console.log($(cb).parents("tr").eq(0).attr("etmname"));
//			    console.log($(cb).parents("tr").eq(0).find(".ndcol2").html());

			    flexiGridAjax("FlexiGridAjax_SetDisplayColumn", {
			        "pageamethod": $.trim($("#pageamethod").val()),
			        "columnname": $(cb).parents("tr").eq(0).attr("etmname"),
			        "columndisplay": $(cb).parents("tr").eq(0).find(".ndcol2").html(),
			        "hide": (visible ? "0" : "1")
			    });


				$('tbody tr', t).each(
					function () {
					    if (visible) {
					        if (clearContent != null && clearContent == true) {
					            $('td:eq(' + n + ')', this).show();//.html("");
					            $('td:eq(' + n + ')', this).find("div").html("");
					        } else {
					            $('td:eq(' + n + ')', this).show();
					        }
						} else {
							$('td:eq(' + n + ')', this).hide();
						}
					}
				);
			    

			    //重新调整宽度$('thead th', g.hDiv)
				var $checkeds = $('input:checked', g.nDiv);
			    //alert($checkeds.length);
			    if ($checkeds.length == 1) {
			        var index = $checkeds.val();
			        $('thead div', g.hDiv).eq(index).css("width", (p.width + p.fixAllWidth + p.fixColumnWidth) + "px");
			        $('tbody tr', g.bDiv).each(function () {
			            $(this).find("div").eq(index).css("width", (p.width + p.fixAllWidth + p.fixColumnWidth) + "px");
			        });
			    } else if ($checkeds.length > 1) {
			        var $thDiv = $('thead div', g.hDiv);
			        var $tr = $('tbody tr', g.bDiv);
			        var currentSettingTotalWidth = 0;
			        var arry = new Array();
			        
			        $checkeds.each(function () {
			            var keyValue = new KeyValue();
			            keyValue.Key = parseInt($(this).val());
			            keyValue.Value = parseFloat($thDiv.eq(keyValue.Key).attr("initwidth"));
			            currentSettingTotalWidth += keyValue.Value;
			            arry.push(keyValue);
			        });

                    //注释下面的for语句这不按百分比显示列宽
//			        for (var j = 0; j < arry.length; j++) {
//			            var keyValue = arry[j];
//			            var tmpwidth = keyValue.Value / currentSettingTotalWidth * (p.width + p.fixAllWidth) + p.fixColumnWidth;
//			            $thDiv.eq(keyValue.Key).css("width", tmpwidth + "px");
//			            $tr.each(function () {
//			                $(this).find("div").eq(keyValue.Key).css("width", tmpwidth + "px");
//			            });
//			        }
			    }
			    
			    //var ff = $('thead th', g.hDiv);
				//$('input:checked', g.nDiv).each(function () {
				   // $(this).val()
			    //});

			    //调整显示隐藏列按钮位置
			    var tmpWidth11 = $("table", g.hDiv).width();
			    if (tmpWidth11 < $(window).width()) {
			        tmpWidth11 = $(window).width();
			    }
			    var tmpWindowLeft11 = tmpWidth11 - 25;
			    $("#btnShowOrHide").css("left", tmpWindowLeft11 + "px");


				this.rePosDrag();
				if (p.onToggleCol) {
					p.onToggleCol(cid, visible);
				}
				return visible;
			},
			switchCol: function (cdrag, cdrop) { //switch columns
				$('tbody tr', t).each(
					function () {
						if (cdrag > cdrop) $('td:eq(' + cdrop + ')', this).before($('td:eq(' + cdrag + ')', this));
						else $('td:eq(' + cdrop + ')', this).after($('td:eq(' + cdrag + ')', this));
					}
				);
				//switch order in nDiv
				if (cdrag > cdrop) {
					$('tr:eq(' + cdrop + ')', this.nDiv).before($('tr:eq(' + cdrag + ')', this.nDiv));
				} else {
					$('tr:eq(' + cdrop + ')', this.nDiv).after($('tr:eq(' + cdrag + ')', this.nDiv));
				}
				if (browser.msie && browser.version < 7.0) {
					$('tr:eq(' + cdrop + ') input', this.nDiv)[0].checked = true;
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
			},
			scroll: function () {
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				this.rePosDrag();
			},
			addData: function (data) { //parse data
			    //$(g.block).remove();
			    data = $.extend({ rows: [], page: 0, total: 0 }, data);
				if (p.preProcess) {
					data = p.preProcess(data);
				}
				$('.pReload', this.pDiv).removeClass('loading');
				this.loading = false;
				if (!data) {
					$('.pPageStat', this.pDiv).html(p.errormsg);
                    if (p.onSuccess) p.onSuccess(this);
					return false;
				}
				p.total = data.total;
				if (p.total === 0) {
				    $('tr, a, td, div', t).unbind();
				    
				    //$(t).empty();原来的
				    //替换为  没数据时使用   修复选择条件刷新时没数据时滚动条不能动
				    var totalWidth = 0;
				    var tmpCount1 = 0;
				    $("table tr:first th", g.hDiv).each(function () {
				        totalWidth += $(this).find("div").eq(0).width();
				        tmpCount1++;
				    });
				    totalWidth += 170;
				    $(t).html("<tr><td colspan='" + tmpCount1 + "'><div style='width:" + totalWidth + "px;text-align: center;'>暂无相关数据</div></td></tr>");
				    //
				    
					p.pages = 1;
					p.page = 1;
					this.buildpager();
					$('.pPageStat', this.pDiv).html(p.nomsg);
				    $(g.block).css("display","none").html("");
				    if (p.onSuccess) p.onSuccess(this);
				    
				    //修复一开始没数据时滚动条不能动
				    try {
				        if ($(g.bDiv).getNiceScroll().length == 0) {
				            InitFlexgridScrollbar();
				        } else {
				            ReSizeFlexgridScrollbar();
				        }
				    } catch(e) {

				    } 

				   
					return false;
				}
				p.pages = Math.ceil(p.total / p.rp);
			    p.page = data.page;
				this.buildpager();
			    //build new body

			    var tbodyArry = new StringBuilder();
			    tbodyArry.append("<tbody>");
				//var tbody = document.createElement('tbody');
				$.each(data.rows, function (i, row) {
				    var trArray = new StringBuilder();
				    //var tr = document.createElement('tr');
				    trArray.append("<tr ");
				    if (row.name) {
				        trArray.append(" name='" + row.name + "'");
				    }
				    if (row.color) {
				        trArray.append(" style='background:" + row.color + ";'");
				    } else {
				        if (i % 2 && p.striped) {
				            trArray.append(" class='erow'");
				        } 
				    }
				    
				    if (row[p.idProperty]) {
				        trArray.append(" id=row'" + row[p.idProperty] + "'");
				    }
				    trArray.append(">");
				    //if (row.name) tr.name = row.name;
//				    if (row.color) {
//				        $(tr).css('background', row.color);
//				    } else {
//				        if (i % 2 && p.striped) tr.className = 'erow';
//				    }
//				    if (row[p.idProperty]) {
//				        tr.id = 'row' + row[p.idProperty];
//				    }
				   

				    var tdArry;
				    $('thead tr:first th', g.hDiv).each( //add cell
                        function (i,n) {
                            tdArry = new StringBuilder();
                            tdArry.append("<td");
                            var idx = $(this).attr('axis').substr(3);
                            tdArry.append(" align='" + this.align + "' ");
                            var tdContent = "";
                            var hiddenStr = "";
                            if ($(this)[0].hidden) {
                                hiddenStr = "display:none; ";
                            }
                            if (typeof row.cell == 'undefined') {
                                tdContent = row[p.colModel[idx].name];
                                var offs1 = tdContent.indexOf('<BGCOLOR=');
                                if (offs1 > 0) {
                                    tdArry.append(" style='background:" + text.substr(offs1 + 7, 7) + ";" + hiddenStr + "' ");
                                } else {
                                    tdArry.append(" style='" + hiddenStr + "'");
                                }
                                tdArry.append(" abbr='" + $(this).attr('abbr') + "' ");
                            } else {
                                var iHTML = '';
                                if (typeof row.cell[idx] != "undefined") {
                                    iHTML = (row.cell[idx] !== null) ? row.cell[idx] : ''; //null-check for Opera-browser
                                } else {
                                    iHTML = row.cell[p.colModel[idx].name];
                                }
                                var obj = p.__mw.datacol(p, $(this).attr('abbr'), iHTML);//cell实体

                                if (obj.Attrs != null && obj.Attrs.length > 0) {
                                    $(obj.Attrs).each(function (i, n) {
                                        tdArry.append(" " + n.name + "='" + n.value + "' ");
                                    });
                                }
                               
                                tdContent = obj.Content;
                                var offs2 = tdContent.indexOf('<BGCOLOR=');
                                if (offs2 > 0) {
                                    tdArry.append(" style='background:" + text.substr(offs2 + 7, 7) + ";" + hiddenStr + "' ");
                                } else {
                                    tdArry.append(" style='" + hiddenStr + "'");
                                }
                                tdArry.append(" abbr='" + $(this).attr('abbr') + "' ");
                                tdArry.append(">");
                            }
                            


                            //tdArry.append(tdContent);
                            tdArry.append(g.addDiv($(this), i, tdContent));
                           


                            tdArry.append("</td>");
                            trArray.appendArray(tdArry);
                            tdArry = null;
//                            var td = document.createElement('td');
//                            var idx = $(this).prop('axis').substr(3);
//                            td.align = this.align;
//                            // If each row is the object itself (no 'cell' key)
//                            if (typeof row.cell == 'undefined') {
//                                td.innerHTML = row[p.colModel[idx].name];
//                            } else {
//                                // If the json elements aren't named (which is typical), use numeric order
//                                var iHTML = '';
//                                if (typeof row.cell[idx] != "undefined") {
//                                    iHTML = (row.cell[idx] !== null) ? row.cell[idx] : ''; //null-check for Opera-browser
//                                } else {
//                                    iHTML = row.cell[p.colModel[idx].name];
//                                }
//                                var obj = p.__mw.datacol(p, $(this).prop('abbr'), iHTML);//cell实体
//                               
//                                td.innerHTML = obj.Content; //use middleware datacol to format cols
//                                //console.log(td.innerHTML);
//                                if (obj.Attrs != null && obj.Attrs.length > 0) {
//                                    $(obj.Attrs).each(function (i,n) {
//                                        $(td).attr(n.name, n.value);
//                                    });
//                                }
//                            }
//                            // If the content has a <BGCOLOR=nnnnnn> option, decode it.
//                            var offs = td.innerHTML.indexOf('<BGCOLOR=');
//                            if (offs > 0) {
//                                $(td).css('background', text.substr(offs + 7, 7));
//                            }
//
//                            $(td).prop('abbr', $(this).prop('abbr'));
//                            $(tr).append(td);
//
//                            td = null;
                            
                        }
                    );
				    if ($('thead', this.gDiv).length < 1) {//handle if grid has no headers
				        for (idx = 0; idx < row.cell.length; idx++) {
				            tdArry = new StringBuilder();
				            tdArry.append("<td>");
				            if (typeof row.cell[idx] != "undefined") {
				                var tmp = (row.cell[idx] != null) ? row.cell[idx] : '';//null-check for Opera-browser
				                tdArry.append(tmp);
				            } else {
				                tdArry.append(row.cell[p.colModel[idx].name]);
				            }
				            tdArry.append("</td>");
				            trArray.appendArray(tdArry);
				            tdArry = null;
//				            var td = document.createElement('td');
//				            // If the json elements aren't named (which is typical), use numeric order
//				            if (typeof row.cell[idx] != "undefined") {
//				                td.innerHTML = (row.cell[idx] != null) ? row.cell[idx] : '';//null-check for Opera-browser
//				            } else {
//				                td.innerHTML = row.cell[p.colModel[idx].name];
//				            }
//				            $(tr).append(td);
//				            td = null;
				        }
				    }
				    trArray.append("</tr>");
				    tbodyArry.appendArray(trArray);
				    trArray = null;
				    //$(tbody).append(tr);
				    //tr = null;
				});
				$('tr', t).unbind();
				$(t).empty();
				tbodyArry.append("</tbody>");
				//console.log(tbodyArry.toString());
				$(t).append(tbodyArry.toString());
			    //$(t).append(tbody);
			    
				//this.addCellProp();
				this.addRowProp();
				this.rePosDrag();
			    
			    tbodyArry = null;
				//tbody = null;
				data = null;
				i = null;
				if (p.onSuccess) {
					p.onSuccess(this);
				}
				if (p.hideOnSubmit) {
					$(g.block).remove();
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				if (browser.opera) {
					$(t).css('visibility', 'visible');
				}
				if (typeof InitFlexgridScrollbar != "undefined") {
				    InitFlexgridScrollbar();
				}

//				$("thead div", g.hDiv).each(function (k, n) {
//				  //  $(this).width($(this).width() - (2 * k));
//				    // alert(k + "|" + $(this).width());
//				    
//				});

			},
			changeSort: function (th) { //change sortorder
				if (this.loading) {
					return true;
				}
				$(g.nDiv).hide();
				$(g.nBtn).hide();
				if (p.sortname == $(th).attr('abbr')) {
					if (p.sortorder == 'asc') {
						p.sortorder = 'desc';
					} else {
						p.sortorder = 'asc';
					}
				}
				$(th).addClass('sorted').siblings().removeClass('sorted');
				$('.sdesc', this.hDiv).removeClass('sdesc');
				$('.sasc', this.hDiv).removeClass('sasc');
				$('div', th).addClass('s' + p.sortorder);
				p.sortname = $(th).attr('abbr');
				if (p.onChangeSort) {
					p.onChangeSort(p.sortname, p.sortorder);
				} else {
					this.populate();
				}
			},
			buildpager: function () { //rebuild pager based on new properties
				$('.pcontrol input', this.pDiv).val(p.page);
				$('.pcontrol .pageCount', this.pDiv).html(p.pages);
				var r1 = (p.page - 1) * p.rp + 1;
				var r2 = r1 + p.rp - 1;
				if (p.total < r2) {
					r2 = p.total;
				}
				var stat = p.pagestat;
				stat = stat.replace(/{from}/, r1);
				stat = stat.replace(/{to}/, r2);
				stat = stat.replace(/{total}/, p.total);
				$('.pPageStat', this.pDiv).html(stat);
			},
			populate: function () { //get latest data
				if (this.loading) {
					return true;
				}
				if (p.onSubmit) {
					var gh = p.onSubmit();
					if (!gh) {
						return false;
					}
				}
				this.loading = true;
				if (!p.url) {
					return false;
				}
				$('.pPageStat', this.pDiv).html(p.procmsg);
				$('.pReload', this.pDiv).addClass('loading');
			    $(g.block).css({
			        top: g.bDiv.offsetTop,"display":"block"
			    }).html("<div class='loadingimg'><div>");
				if (p.hideOnSubmit) {
					$(this.gDiv).prepend(g.block);
				}
				if (browser.opera) {
					$(t).css('visibility', 'hidden');
				}
				if (!p.newp) {
					p.newp = 1;
				}
				if (p.page > p.pages) {
					p.page = p.pages;
				}
				var param = [{
					name: 'PageIndex',
					value: p.newp
				}, {
					name: 'PageSize',
					value: p.rp
				}, {
					name: 'SortName',
					value: p.sortname
				}, {
					name: 'SortOrder',
					value: p.sortorder
				}, {
					name: 'query',
					value: p.query
				}, {
					name: 'qtype',
					value: p.qtype
				}];
//				alert(etmFormId);
				if (p.params.length) {
				    for (var pi = 0; pi < p.params.length; pi++) {
				        var pTmp = p.params[pi];
				        var tmpName = pTmp.name;
				        if (tmpName != "__VIEWSTATE" && tmpName != "__EVENTVALIDATION" && tmpName != "etmparams") {// && tmpName != "etmparams" && tmpName != "etmparams"
				            param[param.length] = p.params[pi]; //console.log(p.params[pi]);
				        }
				    }
				}
			    
				function checkExistsAndAppendValue(name,value) {
				    var isExists = false;
				    for (var j = 0; j < param.length; j++) {
				        if (param[j].name == name) {
				            param[j].name = param[j].name + "," + value;
				            isExists = true;
				            break;
				        }
				    }
				    return isExists;
				}
//				if (typeof etmFormId != "undefined" && browser.msie && browser.version == 9) {
//				    $("#" + etmFormId + " input").each(function () {
//				        var name = $(this).attr("name");
//				        var value = $(this).val();
//				        if (name != undefined && name.length > 0 && !checkExistsAndAppendValue(name, value)) {
//				            param[param.length] = {
//				                name: name,
//				                value: value
//				            };
//				        }
//				    });
//				    $("#" + etmFormId + " select").each(function () {
//				        var name = $(this).attr("name");
//				        var value = $(this).val();
//				        if (value == null) {
//				            value = $(this).find("option:first").attr("value");
//				            $(this).val(value);
//				        }
//				        if (name != undefined && name.length > 0 && !checkExistsAndAppendValue(name, value)) {
//				            console.log(name + "||" + $(this).val());
//				            param[param.length] = {
//				                name: name,
//				                value: value
//				            };
//				        }
//				    });
//				}

				$.ajax({
					type: p.method,
					url: p.url,
					data: param,
					dataType: p.dataType,
					success: function (data) {
                       // data = $.parseJSON($(data).find("string").text());
					    // g.addData(data);
					        if (data.Tips != null && data.Tips.length > 3) {
					            if ($(".flexigridTips").size() > 0) {
					                $(".flexigridTips").remove();
					            }
					            $(g.pDiv).before("<div class='flexigridTips'>" + data.Tips + "</div>");
					        }
					      
					        //var tmpTimeCount = (new Date()).getTime();
					    // console.log(tmpTimeCount);
					        g.addData(data);
					         //console.log((new Date()).getTime() - tmpTimeCount);
					        //alert((new Date()).getTime() - tmpTimeCount);
					        
					        //刷新滚动条
					        ReSizeFlexgridScrollbar();
//					    } else {
//					        if (str.indexOf("登录") != -1) {
//					            if (confirm(str)) {
//					                var url = window.location.href.substring(0, window.location.href.toLocaleLowerCase().indexOf(window.location.pathname.toLocaleLowerCase())) + "/Login.aspx";
//					                if (parent != null && parent.parent != null) {
//					                    parent.parent.location.href = url;
//					                }
//					                else if (parent != null) {
//					                    parent.location.href = url;
//					                } else {
//					                    location.href = url;
//					                }
//					            } else {
//					                $('.pPageStat', g.pDiv).html(str);
//					            }
//					        } 
//					    }
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						try {
							if (p.onError) p.onError(XMLHttpRequest, textStatus, errorThrown);
						} catch (e) {}
					}
				});
			},
			doSearch: function () {
				p.query = $('input[name=q]', g.sDiv).val();
				p.qtype = $('select[name=qtype]', g.sDiv).val();
				p.newp = 1;
				this.populate();
			},
			changePage: function (ctype) { //change page
				if (this.loading) {
					return true;
				}
				switch (ctype) {
					case 'first':
						p.newp = 1;
						break;
					case 'prev':
						if (p.page > 1) {
							p.newp = parseInt(p.page, 10) - 1;
						}
						break;
					case 'next':
						if (p.page < p.pages) {
							p.newp = parseInt(p.page, 10) + 1;
						}
						break;
					case 'last':
						p.newp = p.pages;
						break;
					case 'input':
						var nv = parseInt($('.pcontrol input', this.pDiv).val(), 10);
						if (isNaN(nv)) {
							nv = 1;
						}
						if (nv < 1) {
							nv = 1;
						} else if (nv > p.pages) {
							nv = p.pages;
						}
						$('.pcontrol input', this.pDiv).val(nv);
						p.newp = nv;
						break;
				}
				if (p.newp == p.page) {
					return false;
				}
				if (p.onChangePage) {
					p.onChangePage(p.newp);
				} else {
					this.populate();
				}
			},
			addDiv:function($th,index,instr) {
			    var tdDivArry = new StringBuilder();
			    tdDivArry.append("<div ");
			    var pth = $th[0];
			    var isCheckBox = false;
			    var isLink = false;
			    // var isEdit = false;
			    var linkClass = "";

			    var tmpClass = "";
			    if (p.nowrap == false) {
			        tmpClass = "wordwrap";
			    }
			    if (pth != null) {
			        if (p.sortname == $th.attr('abbr') && p.sortname) {
			            tmpClass += " sorted";
			        }
			        tdDivArry.append(" class='" + tmpClass + "' ");
			        tdDivArry.append(" style='text-align:" + pth.align + ";width:" + $th.find("div")[0].style.width + "; ");
//			        if (pth.hidden) {
//			            tdDivArry.append("display:none; ");
//			        }
			        tdDivArry.append("' ");//闭合style

			        if ($th.attr('checkbox')) {
			            isCheckBox = true;
			        }
			        // cm.
			        if ($th.attr('edit')) {
			            isCheckBox = true;
			        }
			        if ($th.attr('IsLink')) {
			            isLink = true;
			            linkClass = $th.attr('LinkClass');
			        }

			        var subLength = parseInt($th.attr('SubStringLength'));
			       

			        var tmpTitle = "";
			        if (subLength > 1) {
			            if (instr.indexOf("<a") != -1) {
			                var str = $.trim($("<div/>").html(instr).text());
			                tmpTitle = str;
			                tdDivArry.append(" title='" + str + "'");
			                if (str.length > subLength) {
			                    str = str.substring(0, subLength) + "...";
			                    instr = instr.replace(tmpTitle, str);
			                }
			            } else {
			                tmpTitle = instr;
			                tdDivArry.append(" title='" + tmpTitle + "'");
			                if (instr.length > subLength) {
			                    instr = instr.substring(0, subLength) + "...";
			                }
			               // instr = instr.substring(0, subLength) + "...";
			            }
			        }
			   

			    } else {
			        tdDivArry.append(" class='" + tmpClass + "' ");
			    }
			    tdDivArry.append(">");//闭合tdDiv

			    if (instr == '') {
			        instr = '&nbsp;';
			    }
			    var tmpContent1 = "";
			    if (isCheckBox) {
			        if (instr.indexOf("<input") != -1) {
			            tmpContent1 = instr;
			        } else {
			            tmpContent1 = "<input type='checkbox' class='selectItem' name='selectItem' value='" + instr + "'/>";
			        }
			    } else if (isLink) {
			        tmpContent1 = "<a href='javascript:void(0);' class='" + linkClass + "'>" + instr + "</a>";
			    } else if ($th.attr('OperationButtons') != null && $th.attr('OperationButtons') != undefined && $th.attr('OperationButtons').length > 2 && $th.attr('OperationButtons').indexOf(":") != -1) {
			        var tmp = $th.attr('OperationButtons').split(";");
			        var htmlStr = "";
			        for (var j = 0; j < tmp.length; j++) {
			            var tmp1 = tmp[j].split(":");
			            if (tmp1.length == 2) {
			                htmlStr += "&nbsp;<a href='javascript:void(0);' class='" + tmp1[1] + "' str='" + instr + "'>[" + tmp1[0] + "]</a>";
			            }
			        }
			        tmpContent1 = htmlStr.substring(6);
			    }
			    else {
			        tmpContent1 = instr;
			    }

			    tdDivArry.append(tmpContent1);
			    tdDivArry.append("</div>");
			 
//			    var prnt = $(this).parent()[0];
//			    var pid = false;
//			    if (prnt.id) {
//			        pid = prnt.id.substr(3);
//			    }

			    return tdDivArry.toString();
			}
            ,
			//添加列内容
			addCellProp: function () {
			    $('tbody tr td', g.bDiv).each(function () {
			        var tdDivArry = new StringBuilder();
			        tdDivArry.append("<div ");
			        var n = $('td', $(this).parent()).index(this);
			        var pth = $('th:eq(' + n + ')', g.hDiv).get(0);
			        var isCheckBox = false;
			        var isLink = false;
			        // var isEdit = false;
			        var linkClass = "";

			        var tmpClass = "";
			        if (p.nowrap == false) {
			            tmpClass = "wordwrap";
			        }
			        if (pth != null) {
			            if (p.sortname == $(pth).attr('abbr') && p.sortname) {
			                tmpClass += " sorted";
			            }
			            tdDivArry.append(" class='" + tmpClass + "' ");
			            tdDivArry.append(" style='textAlign:" + pth.align + ";width:" + $('div:first', pth)[0].style.width + "; ");
			            if (pth.hidden) {
			                tdDivArry.append("display:none; ");
			            }
			            tdDivArry.append("' ");//闭合style

			            if ($(pth).attr('checkbox')) {
			                isCheckBox = true;
			            }
			            // cm.
			            if ($(pth).attr('edit')) {
			                isCheckBox = true;
			            }
			            if ($(pth).attr('IsLink')) {
			                isLink = true;
			                linkClass = $(pth).attr('LinkClass');
			            }

			            var subLength = parseInt($(pth).attr('SubStringLength'));


			            var tmpTitle = "";
			            if (subLength > 1) {
			                if (this.innerHTML.indexOf("<a") != -1) {
			                    var str = $.trim($("<div/>").html(this.innerHTML).text());
			                    tmpTitle = str;
			                    tdDivArry.append(" title='" + str + "'");
			                    if (str.length > subLength) {
			                        str = str.substring(0, subLength) + "...";
			                        this.innerHTML = this.innerHTML.replace(tmpTitle, str);
			                    }
			                } else {
			                    tmpTitle = this.innerHTML;
			                    this.innerHTML = this.innerHTML.substring(0, subLength) + "...";
			                }
			            }

			        } else {
			            tdDivArry.append(" class='" + tmpClass + "' ");
			        }
			        tdDivArry.append(">");//闭合tdDiv

			        if (this.innerHTML == '') {
			            this.innerHTML = '&nbsp;';
			        }
			        var tmpContent1 = "";
			        if (isCheckBox) {
			            if (this.innerHTML.indexOf("<input") != -1) {
			                tmpContent1 = this.innerHTML;
			            } else {
			                tmpContent1 = "<input type='checkbox' class='selectItem' name='selectItem' value='" + this.innerHTML + "'/>";
			            }
			        } else if (isLink) {
			            tmpContent1 = "<a href='javascript:void(0);' class='" + linkClass + "'>" + this.innerHTML + "</a>";
			        } else if ($(pth).attr('OperationButtons') != null && $(pth).attr('OperationButtons') != undefined && $(pth).attr('OperationButtons').length > 2 && $(pth).attr('OperationButtons').indexOf(":") != -1) {
			            var tmp = $(pth).attr('OperationButtons').split(";");
			            var htmlStr = "";
			            for (var j = 0; j < tmp.length; j++) {
			                var tmp1 = tmp[j].split(":");
			                if (tmp1.length == 2) {
			                    htmlStr += "&nbsp;<a href='javascript:void(0);' class='" + tmp1[1] + "' str='" + this.innerHTML + "'>[" + tmp1[0] + "]</a>";
			                }
			            }
			            tmpContent1 = htmlStr.substring(6);
			        }
			        else {
			            tmpContent1 = this.innerHTML;
			        }

			        tdDivArry.append(tmpContent1);
			        tdDivArry.append("</div>");


			        var prnt = $(this).parent()[0];
			        var pid = false;
			        if (prnt.id) {
			            pid = prnt.id.substr(3);
			        }
			
			        $(this).empty().append(tdDivArry.toString()).removeProp('width'); //wrap content
//			        var tmpTdDiv = $(this).find("div")[0];
//			        if (pth != null) {
//			            if (pth.process) pth.process(tmpTdDiv, pid);
//			        }
			       // g.addTitleToCell(tmpTdDiv);
			        



					//var tdDiv = document.createElement('div');
//					var n = $('td', $(this).parent()).index(this);
//					var pth = $('th:eq(' + n + ')', g.hDiv).get(0);
//					var isCheckBox = false;
//				    var isLink = false;
//				    // var isEdit = false;
//				    var linkClass = "";
//					if (pth != null) {
//					    if (p.sortname == $(pth).prop('abbr') && p.sortname) {
//							this.className = 'sorted';
//						}
//						$(tdDiv).css({
//							textAlign: pth.align,
//							width: $('div:first', pth)[0].style.width
//						});
//						if (pth.hidden) {
//							$(this).css('display', 'none');
//						}
//						if ($(pth).prop('checkbox')) {
//						    isCheckBox = true;
//						}
//					   // cm.
//						if ($(pth).prop('edit')) {
//						    isCheckBox = true;
//						}
//						if ($(pth).prop('IsLink')) {
//						    isLink = true;
//						    linkClass = $(pth).prop('LinkClass');
//						}
//					    
//						var subLength = parseInt($(pth).prop('SubStringLength'));
//					    
//					
//
//						if (subLength > 1) {
//						    if (this.innerHTML.indexOf("<a") != -1) {
//						        var str = $.trim($("<div/>").html(this.innerHTML).text());
//						        tdDiv.title = str;
//						        if (str.length > subLength) {
//						            str = str.substring(0, subLength) + "...";
//						            this.innerHTML = this.innerHTML.replace(tdDiv.title, str);
//						        }
//						    } else {
//						        tdDiv.title = this.innerHTML;
//						        this.innerHTML = this.innerHTML.substring(0, subLength) + "...";
//						    }
//						}
//						
////						if (this.innerHTML.length > subLength) {
////						    if (this.innerHTML.indexOf("<a") != -1) {
////						        
////						        
////						        //  tdDiv.title = this.innerHTML;
////						    } else {
////						        tdDiv.title = this.innerHTML;
////						    }
////						   
////						    this.innerHTML = this.innerHTML.substring(0, subLength) + "...";
////						}
//					}
//					if (p.nowrap == false) {
//					    //$(tdDiv).css('white-space', 'normal');
//					    $(tdDiv).addClass("wordwrap");
//					}
//					if (this.innerHTML == '') {
//						this.innerHTML = '&nbsp;';
//					}
//
//					if (isCheckBox) {
//					    if (this.innerHTML.indexOf("<input") != -1) {
//					        tdDiv.innerHTML = this.innerHTML;
//					    } else {
//					        tdDiv.innerHTML = "<input type='checkbox' class='selectItem' name='selectItem' value='" + this.innerHTML + "'/>";
//					    }
//					}else if (isLink) {
//					    tdDiv.innerHTML = "<a href='javascript:void(0);' class='" + linkClass + "'>" + this.innerHTML + "</a>";
//					}else if ($(pth).prop('OperationButtons')!=null&&$(pth).prop('OperationButtons')!=undefined&&$(pth).prop('OperationButtons').length > 2&&$(pth).prop('OperationButtons').indexOf(":")!=-1) {
//					    var tmp = $(pth).prop('OperationButtons').split(";");
//					    var htmlStr = "";
//					    for (var j = 0; j < tmp.length; j++) {
//					        var tmp1 = tmp[j].split(":");
//					        if (tmp1.length == 2) {
//					            htmlStr += "&nbsp;<a href='javascript:void(0);' class='" + tmp1[1] + "' str='" + this.innerHTML + "'>[" + tmp1[0] + "]</a>";
//					        }
//					    }
//					    tdDiv.innerHTML = htmlStr.substring(6);
//					}
//					else {
//					    tdDiv.innerHTML = this.innerHTML;
//					}
//				    
//
//
//
//					
//					var prnt = $(this).parent()[0];
//					var pid = false;
//					if (prnt.id) {
//						pid = prnt.id.substr(3);
//					}
//					if (pth != null) {
//						if (pth.process) pth.process(tdDiv, pid);
//					}
//					$(this).empty().append(tdDiv).removeProp('width'); //wrap content
//					
//					g.addTitleToCell(tdDiv);
				});
			},
			getCellDim: function (obj) {// get cell prop for editable event
				var ht = parseInt($(obj).height(), 10);
				var pht = parseInt($(obj).parent().height(), 10);
				var wt = parseFloat(obj.style.width, 10);
				var pwt = parseFloat($(obj).parent().width(), 10);
				var top = obj.offsetParent.offsetTop;
				var left = obj.offsetParent.offsetLeft;
				var pdl = parseFloat($(obj).css('paddingLeft'), 10);
				var pdt = parseFloat($(obj).css('paddingTop'), 10);
				return {
					ht: ht,
					wt: wt,
					top: top,
					left: left,
					pdl: pdl,
					pdt: pdt,
					pht: pht,
					pwt: pwt
				};
			},
			addRowProp: function () {
			    $('tbody tr', g.bDiv).on('click', function (e) {
			        var $checkboxs = $(this).find("input[type='checkbox']");
//			        if ($checkboxs.size() > 0 && $checkboxs.eq(0).prop("disabled")) {
//                        e.stopPropagation();
//			            return;
//			        }
					var obj = (e.target || e.srcElement);
					if (obj.href || obj.type) return true;
					if (e.ctrlKey || e.metaKey) {
						// mousedown already took care of this case
						return;
					}
					$(this).toggleClass('trSelected');
					if (p.singleSelect && ! g.multisel) {
						$(this).siblings().removeClass('trSelected');
					}

					//var $checkboxs = $(this).find("input[type='checkbox']");
					if ($checkboxs.size() > 0) {
					    
					    if (!$checkboxs.eq(0).attr("disabled")) {
					        //$checkboxs.prop("checked", $(this).hasClass("trSelected"));
					        $checkboxs.attr("checked", !$checkboxs.attr("checked"));
					    }
					    
					    

					    if (!$(this).hasClass("trSelected")) {//当前行没选中
					        $(".selectAll", g.hDiv).attr("checked", false);
					    } else{//整个表格是否有未勾选的
					        $(".selectAll", g.hDiv).attr("checked", $('tbody tr', g.bDiv).size() == $('.trSelected', g.bDiv).size());
					    }
					}
					
                    //获取行信息
					if (typeof GetRowInfo != 'undefined') {
					    GetRowInfo($(this), g, p);
					}

				}).on('mousedown', function (e) {
					if (e.shiftKey) {
						$(this).toggleClass('trSelected');
						g.multisel = true;
						this.focus();
						$(g.gDiv).noSelect(true);
					}
					if (e.ctrlKey || e.metaKey) {
						$(this).toggleClass('trSelected');
						g.multisel = true;
						this.focus();
					}
				}).on('mouseup', function (e) {
					if (g.multisel && ! (e.ctrlKey || e.metaKey)) {
						g.multisel = false;
						$(g.gDiv).noSelect(false);
					}
				}).on('dblclick', function () {
					if (p.onDoubleClick) {
						p.onDoubleClick(this, g, p);
					}
				}).hover(function (e) {
					if (g.multisel && e.shiftKey) {
						$(this).toggleClass('trSelected');
					}
				}, function () {});
				if (browser.msie && browser.version < 7.0) {
					$(this).hover(function () {
						$(this).addClass('trOver');
					}, function () {
						$(this).removeClass('trOver');
					});
				}
			},

			combo_flag: true,
			combo_resetIndex: function(selObj)
			{
				if(this.combo_flag) {
					selObj.selectedIndex = 0;
				}
				this.combo_flag = true;
			},
			combo_doSelectAction: function(selObj)
			{
				eval( selObj.options[selObj.selectedIndex].value );
				selObj.selectedIndex = 0;
				this.combo_flag = false;
			},
			//Add title attribute to div if cell contents is truncated
			addTitleToCell: function(tdDiv) {
				if(p.addTitleToCell) {
					var $span = $('<span />').css('display', 'none'),
						$div = (tdDiv instanceof jQuery) ? tdDiv : $(tdDiv),
						div_w = $div.outerWidth(),
						span_w = 0;

					$('body').children(':first').before($span);
					$span.html($div.html());
					$span.css('font-size', '' + $div.css('font-size'));
					$span.css('padding-left', '' + $div.css('padding-left'));
					span_w = $span.innerWidth();
					$span.remove();

					if(span_w > div_w) {
					    $div.attr('title', $div.text());
					} else {
					    $div.removeProp('title');
					}
				}
			},
			autoResizeColumn: function (obj) {
				if(!p.dblClickResize) {
					return;
				}
				var n = $('div', this.cDrag).index(obj),
					$th = $('th:visible div:eq(' + n + ')', this.hDiv),
					ol = parseFloat(obj.style.left, 10),
					ow = $th.width(),
					nw = 0,
					nl = 0,
					$span = $('<span />');
				$('body').children(':first').before($span);
				$span.html($th.html());
				$span.css('font-size', '' + $th.css('font-size'));
				$span.css('padding-left', '' + $th.css('padding-left'));
				$span.css('padding-right', '' + $th.css('padding-right'));
				nw = $span.width();
				$('tr', this.bDiv).each(function () {
					var $tdDiv = $('td:visible div:eq(' + n + ')', this),
						spanW = 0;
					$span.html($tdDiv.html());
					$span.css('font-size', '' + $tdDiv.css('font-size'));
					$span.css('padding-left', '' + $tdDiv.css('padding-left'));
					$span.css('padding-right', '' + $tdDiv.css('padding-right'));
					spanW = $span.width();
					nw = (spanW > nw) ? spanW : nw;
				});
				$span.remove();
				nw = (p.minWidth > nw) ? p.minWidth : nw;
				nl = ol + (nw - ow);
				$('div:eq(' + n + ')', this.cDrag).css('left', nl);
				this.colresize = {
					nw: nw,
					n: n
				};
				g.dragEnd();
			},
			pager: 0
		};
        
        g = p.getGridClass(g); //get the grid class
        
	    //colModel 表头模型  每一列的类型
        if (p.colModel) { //create model if any
            thead = document.createElement('thead');
            var tr = document.createElement('tr');
            for (var i = 0; i < p.colModel.length; i++) {
                var cm = p.colModel[i];
                var th = document.createElement('th');
                $(th).attr('axis', 'col' + i);
                if (cm) {	// only use cm if its defined
//                    if ($.cookie) {//$.cookies改成$.cookie开启cookie保存列宽
//                        var cookie_width = 'flexiwidths/' + cm.name;		// Re-Store the widths in the cookies
//                        if ($.cookie(cookie_width) != undefined) {
//                            cm.width = $.cookie(cookie_width);
//                        }
//                    }
                    if (cm.display != undefined) {
                        th.innerHTML = cm.display;
                        $(th).attr('normalName', cm.display);
                    }

                    if (cm.name && cm.sortable) {
                        $(th).attr('abbr', cm.name);
                    }

                    $(th).attr("etmname", cm.etmname);

                    if (cm.align) {
                        th.align = cm.align;
                    }
                    //alert(cm.width);
                    if (cm.width) {
                       // $(th).width(cm.width);
                        $(th).attr('width', cm.width);
                    }
                    //					if ($(cm).prop('hide')) {
                    //						th.hidden = true;
                    //					}
                    if (cm.hide) {
                        th.hidden = true;
                    }
                    if (cm.process) {
                        th.process = cm.process;
                    }
                    if (cm.checkbox) {
                        $(th).attr('checkbox', cm.checkbox);
                    }
                    if (cm.IsLink) {
                        $(th).attr('IsLink', cm.IsLink);
                        $(th).attr('LinkClass', cm.LinkClass);
                    }
                    if (cm.OperationButtons != null && cm.OperationButtons != undefined && cm.OperationButtons.length > 2) {
                        $(th).attr('OperationButtons', cm.OperationButtons);
                    }
                    if (cm.SubStringLength > 0) {
                        $(th).attr('SubStringLength', cm.SubStringLength);
                    }

                    $(th).attr('IsCanHide', cm.IsCanHide);
                    $(th).attr("IsJsAddColumn", cm.IsJsAddColumn);

                    
                    if (cm.BindId != null && cm.BindId != undefined && cm.BindId.length > 1) {
                        $(th).attr('BindId', cm.BindId);
                        $(th).attr('IsForDDL', cm.IsForDDL);
                        //$(th).prop('IsBindId', true);
                       // $(th).addClass("dropdownStatus");
                      //  alert(1);
                    }
                    $(th).attr('sortable1', cm.sortable);
                    
                    if (cm.sortable || (cm.BindId != null && cm.BindId != undefined && cm.BindId.length > 1)) {
                        $(th).hover(
                            function () {
                                $(this).css({ "cursor": "pointer" });
                            }, function () {
                                $(this).css({ "cursor": "default" });

                            });
                    }

                } else {
                    th.innerHTML = "";
                    $(th).attr('width', 30);
                }
                $(tr).append(th);
            }
            $(thead).append(tr);
            $(t).prepend(thead);

      

        }// end if p.colmodel
		//init divs
		g.gDiv = document.createElement('div'); //create global container
		g.mDiv = document.createElement('div'); //create title container
		g.hDiv = document.createElement('div'); //create header container
		g.bDiv = document.createElement('div'); //create body container
		g.vDiv = document.createElement('div'); //create grip
		g.rDiv = document.createElement('div'); //create horizontal resizer
		g.cDrag = document.createElement('div'); //create column drag
		g.block = document.createElement('div'); //creat blocker
		g.nDiv = document.createElement('div'); //create column show/hide popup
		g.nBtn = document.createElement('div'); //create column show/hide button
		g.iDiv = document.createElement('div'); //create editable layer
		g.tDiv = document.createElement('div'); //create toolbar
		g.sDiv = document.createElement('div');
		g.pDiv = document.createElement('div'); //create pager container
        
        if(p.colResize === false) { //don't display column drag if we are not using it
            $(g.cDrag).css('display', 'none');
        }
        
		if (!p.usepager) {
			g.pDiv.style.display = 'none';
		}
		g.hTable = document.createElement('table');
		g.gDiv.className = 'flexigrid';
		if (p.width != 'auto') {
			g.gDiv.style.width = p.width + isNaN(p.width) ? '' : 'px';
		} 
		//add conditional classes
		if (browser.msie) {
			$(g.gDiv).addClass('ie');
		}
		if (p.novstripe) {
			$(g.gDiv).addClass('novstripe');
		}
		$(t).before(g.gDiv);
		$(g.gDiv).append(t);
		//set toolbar
		if (p.buttons) {
			g.tDiv.className = 'tDiv';
			var tDiv2 = document.createElement('div');
			tDiv2.className = 'tDiv2';
			for (var i = 0; i < p.buttons.length; i++) {
				var btn = p.buttons[i];
				if (!btn.separator) {
					var btnDiv = document.createElement('div');
					btnDiv.className = 'fbutton';
					btnDiv.innerHTML = ("<div><span>") + (btn.hidename ? "&nbsp;" : btn.name) + ("</span></div>");
					if (btn.bclass) $('span', btnDiv).addClass(btn.bclass).css({
						paddingLeft: 20
					});
					if (btn.bimage) // if bimage defined, use its string as an image url for this buttons style (RS)
						$('span',btnDiv).css( 'background', 'url('+btn.bimage+') no-repeat center left' );
						$('span',btnDiv).css( 'paddingLeft', 20 );

					if (btn.tooltip) // add title if exists (RS)
						$('span',btnDiv)[0].title = btn.tooltip;

					btnDiv.onpress = btn.onpress;
					btnDiv.name = btn.name;
					if (btn.id) {
						btnDiv.id = btn.id;
					}
					if (btn.onpress) {
						$(btnDiv).click(function () {
							this.onpress(this.id || this.name, g.gDiv);
						});
					}
					$(tDiv2).append(btnDiv);
					if (browser.msie && browser.version < 7.0) {
						$(btnDiv).hover(function () {
							$(this).addClass('fbOver');
						}, function () {
							$(this).removeClass('fbOver');
						});
					}
				} else {
					$(tDiv2).append("<div class='btnseparator'></div>");
				}
			}
			$(g.tDiv).append(tDiv2);
			$(g.tDiv).append("<div style='clear:both'></div>");
			$(g.gDiv).prepend(g.tDiv);
		}
		g.hDiv.className = 'hDiv';

		// Define a combo button set with custom action'ed calls when clicked.
		if( p.combobuttons && $(g.tDiv2) )
		{
			var btnDiv = document.createElement('div');
			btnDiv.className = 'fbutton';

			var tSelect = document.createElement('select');
			$(tSelect).change( function () { g.combo_doSelectAction( tSelect ) } );
			$(tSelect).click( function () { g.combo_resetIndex( tSelect) } );
			tSelect.className = 'cselect';
			$(btnDiv).append(tSelect);

			for (i=0;i<p.combobuttons.length;i++)
			{
				var btn = p.combobuttons[i];
				if (!btn.separator)
				{
					var btnOpt = document.createElement('option');
					btnOpt.innerHTML = btn.name;

					if (btn.bclass)
						$(btnOpt)
						.addClass(btn.bclass)
						.css({paddingLeft:20})
						;
					if (btn.bimage)  // if bimage defined, use its string as an image url for this buttons style (RS)
						$(btnOpt).css( 'background', 'url('+btn.bimage+') no-repeat center left' );
						$(btnOpt).css( 'paddingLeft', 20 );

					if (btn.tooltip) // add title if exists (RS)
						$(btnOpt)[0].title = btn.tooltip;

					if (btn.onpress)
					{
						btnOpt.value = btn.onpress;
					}
					$(tSelect).append(btnOpt);
				}
			}
			$('.tDiv2').append(btnDiv);
		}


		$(t).before(g.hDiv);
		g.hTable.cellPadding = 0;
		g.hTable.cellSpacing = 0;
		$(g.hDiv).append('<div class="hDivBox"></div>');
		$('div', g.hDiv).append(g.hTable);
		var thead = $("thead:first", t).get(0);
		if (thead) $(g.hTable).append(thead);
		thead = null;
		if (!p.colmodel) var ci = 0;



	    //选择隐藏列
		var tmpWindowLeft = $(window).width() - 25;
		$(g.hDiv).append("<div id='btnShowOrHide' style='background-image:url(\"/images/more.png\"); background-repeat: no-repeat;width:54px;height:28px; z-index:999; position: absolute;top:3px;left:" + tmpWindowLeft + "px;' title='隐藏/显示列'>&nbsp;</div>");
		$("#btnShowOrHide").bind({
		    click: function (e) {
		        e.stopPropagation();
		        var tmpLeft = $(this).offset().left + 24 - 150+1;
		        if ($(g.nDiv).find("tr").size() * 25 > $(g.nDiv).height()) {
		            //tmpLeft -= 10;
		        }
		        //25
		        $(g.nDiv).css({ "left": tmpLeft + "px" }).show();


		    },
		    mouseenter:function() {
		        $(this).css({ "cursor": "pointer" });
		    },
		    mouseleave:function() {
		        $(this).css({ "cursor": "default" });
		    }
		});
		$("body").click(function () {
		    $(g.nDiv).hide();
		});
	
	    var settingTotalWidth = 0;
	    $('thead tr:first th', g.hDiv).each(function () {
	       // console.log(this.width);
		    if (this.width == '') {
		        this.width = 100;
		    }
//		    if ($(this).prop("hide") == "false") {
//		        
	        //		    }
		    if (!$(this)[0].hidden) {
		        settingTotalWidth += parseFloat(this.width);
		    }
		   
	    });

	    $('thead tr:first th', g.hDiv).live({
	        mouseenter: function () {
	            if ($(".thMove").size() > 0) {
	                $(".dragColTip").not($("#dragColTip" + $(this).attr("axis"))).remove();
	                var left = $(this).offset().left + $(this).width() + 1;
	                var top = $(this).offset().top - 12;
	                if (browser.webkit || browser.safari) {
	                    top++;
	                }
	                var htmlStr = "<div class='dragColTip' id='dragColTip" + $(this).attr("axis") + "' style='position: absolute;top: " + top + "px;left: " + left + "px;z-index: 99;'>";
	                htmlStr += "<div style=' position: relative;width: 10px;height: 50px;'>";
	                htmlStr += "<span class='dragColTip1 secMemuBg' style='font-size: 48px;position: absolute;top: 0px;left: -15px;'>〕</span>";
	                htmlStr += "<span class='dragColTip2 secMemuBg' style='font-size: 48px;position: absolute;top: 0px;left: -32px;'>〔</span>";
	                htmlStr += "</div>";
	                htmlStr += "</div>";
	                $("body").append(htmlStr);
	            }
	        },
	        mouseleave: function () {
	            if ($(".thMove").size() > 0) {
	                $(".dragColTip").not($("#dragColTip" + $(this).attr("axis"))).remove();
	            }
	        }
	    });

	    //settingTotalWidth = settingTotalWidth - 20;
		//alert(settingTotalWidth + "||" + p.width);
	    //alert(p.height);


	    


		$('thead tr:first th', g.hDiv).each(function () {
			var thdiv = document.createElement('div');
			if ($(this).attr('abbr')) {
				$(this).click(function (e) {
					if (!$(this).hasClass('thOver')) return false;
					var obj = (e.target || e.srcElement);
					if (obj.href || obj.type) return true;
					g.changeSort(this);
				});
				if ($(this).attr('abbr') == p.sortname) {
				    //this.className = 'sorted';
				    $(this).addClass("sorted");
					thdiv.className = 's' + p.sortorder;
				}
			}
			if (this.hidden) {
				$(this).hide();
			}
			if (!p.colmodel) {
			    $(this).attr('axis', 'col' + ci++);
			}
			
			// if there isn't a default width, then the column headers don't match
			// i'm sure there is a better way, but this at least stops it failing
			if (this.width == '') {
				this.width = 100;
			}
		    var initwidth = this.width;

            //不注释下面这行则表示使用百分比宽度布局
		    //this.width =parseFloat( this.width / settingTotalWidth * (p.width + p.fixAllWidth) + p.fixColumnWidth);
		    

		    $(thdiv).css({
		        //textAlign: this.align,
		        textAlign: "Center",
		        width: this.width + 'px'
		    }).attr("initwidth", initwidth);
		    

		    //alert(this.width+"||"+p.width);
		    
		    if ($(this).attr('checkbox')) {
		        thdiv.innerHTML = "<input type='checkbox' class='selectAll'/>";//全选
		        
//		        $(".selectAll", thdiv).on({
//		            click: function () {
//		                alert(1);
//		               if ($(this).attr("checked")) {
//		                   $(".selectItem", g.bDiv).attr("checked", true);
//		                   $("tbody tr", g.bDiv).addClass("trSelected");
//		               } else {
//		                   $(".selectItem", g.bDiv).attr("checked", false);
//		                   $("tbody tr", g.bDiv).removeClass("trSelected");
//		               }
//		           } 
		        //		        });

//		        $(".selectAll").live({
//		            click:function() {
//		                alert(1);
//		            }
//		        });
		        $(".selectAll", thdiv).click(function (e) {//绑定全选事件
		           // console.log($(this).prop("disabled"));
		            if ($(this).attr("checked")) {
		                $(".selectItem", g.bDiv).each(function () {
		                   
		                    if (!$(this).attr("disabled")) {
		                        $(this).attr("checked", true);
		                        $(this).parents("tr").eq(0).addClass("trSelected");
		                    }
		                });
		                
//		                $(".selectItem", g.bDiv).prop("checked", true);
//		                $("tbody tr", g.bDiv).addClass("trSelected");
		            } else {
		                $(".selectItem", g.bDiv).each(function () {
		                   // console.log($(this).prop("disabled"));
		                    if (!$(this).attr("disabled")) {
		                        $(this).attr("checked", false);
		                        $(this).parents("tr").eq(0).removeClass("trSelected");
		                    }
		                });
//		                 $(".selectItem", g.bDiv).prop("checked", false);
//		                $("tbody tr", g.bDiv).removeClass("trSelected");
		            }
		           
		        });
		    } else if ($(this).attr('OperationButtons') != null && $(this).attr('OperationButtons') != undefined && $(this).attr('OperationButtons').length > 2) {
		        thdiv.innerHTML = "操作";
		    }
		    else {
		        thdiv.innerHTML = this.innerHTML;
		    }

		    //搜索
		    var searchHtml = "";
		    $(this).css("position", "relative");
		    var bindId = $(this).attr('bindid');
		    if (bindId != null && bindId != undefined && bindId.length > 1) {
		        searchHtml=("<span class='btnColumnHeaderSearch1' style='position:absolute;top:0px;right:0px; display:block;height:30px;width:30px; background: url(../../Images/arrowdown1.png) no-repeat scroll right center transparent;' >&nbsp;</span>");//transparent
		    }

		    var tmpThis = this;
		    var tmptimer;
		    $(this).empty().append(thdiv).append(searchHtml).removeProp('width').mousedown(function (e) {
		        if (browser.msie && browser.version <= 8) {
		            if ($(this).find("input").size() == 0) {
		                if (browser.version == 8) {
		                    tmptimer = setTimeout(function () {//down 1s，才运行。
		                        g.dragStart('colMove', e, tmpThis);
		                    }, 400);
		                } else {
		                    g.dragStart('colMove', e, this);
		                }
		            }
		        }
		        else if (browser.msie && browser.version == 9) {//修复ie9 点击列头就变成拖动问题
		            tmptimer = setTimeout(function () {//down 1s，才运行。
		                g.dragStart('colMove', e, tmpThis);
		            }, 400);
		        }
		        else {
		            
		            g.dragStart('colMove', e, this);
		        }
		        
		    }).mouseup(function() {
		        if (browser.msie && (browser.version == 9 || browser.version == 8)) {
		            clearTimeout(tmptimer);
		        }
		    })
		        .hover(function () {
				if (!g.colresize && !$(this).hasClass('thMove') && !g.colCopy) {
					$(this).addClass('thOver');
				}
				if ($(this).attr('abbr') != p.sortname && !g.colCopy && !g.colresize && $(this).attr('abbr')) {
					$('div', this).addClass('s' + p.sortorder);
				} else if ($(this).attr('abbr') == p.sortname && !g.colCopy && !g.colresize && $(this).attr('abbr')) {
					var no = (p.sortorder == 'asc') ? 'desc' : 'asc';
					$('div', this).removeClass('s' + p.sortorder).addClass('s' + no);
				}
				if (g.colCopy) {
					var n = $('th', g.hDiv).index(this);
					if (n == g.dcoln) {
						return false;
					}
					if (n < g.dcoln) {
						$(this).append(g.cdropleft);
					} else {
						$(this).append(g.cdropright);
					}
					g.dcolt = n;
				} else if (!g.colresize) {
					var nv = $('th:visible', g.hDiv).index(this);
					var onl = parseFloat($('div:eq(' + nv + ')', g.cDrag).css('left'), 10);
					var nw = jQuery(g.nBtn).outerWidth();
					var nl = onl - nw + Math.floor(p.cgwidth / 2);
//					if (browser.webkit) {//修正chrome 列 是否点击 不靠边
//					    //   nl += 2 * nv;
//					    // alert(1);
//					    //console.log(nl);
//					    nl += 2 * nv + 12;
//					    alert(nl);
//					}
//					if (browser.webkit) {//修正chrome 
//					    nl = nl -1;
//				    					}
				    

					//$(g.nDiv).hide();
					$(g.nBtn).hide();
					var tmpStr = $(this).attr('BindId');
					if (tmpStr!=null&&tmpStr!=undefined&&tmpStr.length>2) {

//					    $(g.nBtn).css({
//					        'left': nl - 9,
//					        top: g.hDiv.offsetTop
//					    }).show();
					}

					var ndw = parseFloat($(g.nDiv).width(), 10);
//					$(g.nDiv).css({
//					    top: g.bDiv.offsetTop-1
//					});
//					if ((nl + ndw) > $(g.gDiv).width()) {
//						$(g.nDiv).css('left', onl - ndw + 1);
//					} else {
//					    $(g.nDiv).css('left', nl - 20);
//						//$(g.nDiv).css('left', nl);
//					}
					if ($(this).hasClass('sorted')) {
						$(g.nBtn).addClass('srtd');
					} else {
						$(g.nBtn).removeClass('srtd');
					}
				}
			}, function () {
			    $(this).removeClass('thOver');
			    //修正出现可排序图标bug 2014-01-14 11:01
			    if ($(this).attr('abbr') != undefined && $(this).attr('abbr').length > 1 && p.sortname != undefined && p.sortname.length > 1) {
			        if ($(this).attr('abbr') != p.sortname) {
			            $('div', this).removeClass('s' + p.sortorder);
			        } else if ($(this).attr('abbr') == p.sortname) {
			            var no = (p.sortorder == 'asc') ? 'desc' : 'asc';
			            $('div', this).addClass('s' + p.sortorder).removeClass('s' + no);
			        }
			    }
				if (g.colCopy) {
					$(g.cdropleft).remove();
					$(g.cdropright).remove();
					g.dcolt = null;
				}
			}); //wrap content




		    $(".btnColumnHeaderSearch1",this).bind({
		        mouseenter: function () {
		            var $th = $(this).parents("th").eq(0);
		            $th.addClass("canSearch");
		            setTimeout(function() {
		                var $currentTh = $(".canSearch");
		                if ($currentTh.hasClass("thOver")) {
		                    $currentTh.removeClass("thOver");
		                }
		                var $div = $currentTh.find("div");
		                if ($div.hasClass("sasc")) {
		                    $div.removeClass("sasc").addClass("sdesc");
		                } else if ($div.hasClass("sdesc")) {
		                    $div.removeClass("sdesc").addClass("sasc");
		                }
		            }, 1);
	
		        },
		        mouseleave: function () {

		        },
		        click: function (e) {
		           // alert(1);
		            e.stopPropagation();
		            //alert(cm.display);
		            var $th = $(this).parents("th").eq(0);
		            $("#flexigridColumnHeader").addClass("hidden");
		            $("body").click(function () {
		                if ($("#flexigridColumnHeader").size() > 0) {
		                    $("#flexigridColumnHeader").addClass("hidden").html("");
		                }
		            });

		            var thIndex = $(g.hDiv).find("th").index($th);
		            var id = $th.attr('BindId');
		            if (id != null && id != undefined && id.length > 2) {
		                // alert(1);
		                if ($("#flexigridColumnHeader").size() == 0) {
		                    $(".flexigrid").append("<div id='flexigridColumnHeader' style='height:220px;z-index:5;background-color:#F9F9F9; overflow-x: hidden;overflow-y: auto;position: absolute;border-left:1px solid #DAD8D4;border-right:1px solid #DAD8D4;'></div>");
		                }
		                var columnHeight = parseFloat($th.height());
		                var columnWidth = parseFloat($th.width());
		                var tmpLeft = $th.offset().left;
		                var tmpTop = $th.offset().top;
		                var divWidth = parseInt($("#" + id).width()) + 10;

		                if ($th.attr('IsForDDL') == "true") {
		                    var columnName = $th.find("div").html();
		                    var htmlStr = "<table columnName='" + columnName + "' cellspacing='0' cellpadding='0' style='width: 100%;'><tbody>";
		                    var tmpCount = 0;
		                    $('option', "#" + id).each(function () {
		                        tmpCount++;
		                        var text = $(this).html();
		                        if (text.indexOf("请选择") != -1) {
		                            text = "全部";
		                        }
		                        htmlStr += "<tr><td thindex='" + thIndex + "' style='height:25px;line-height:25px;white-space:nowrap;overflow:hidden; padding-left:10px;border-bottom:1px solid #DAD8D4;border-bottom:1px solid #DAD8D4;' value='" + $(this).attr("value") + "'  forid='" + id + "'>" + text + "</td></tr>";
		                    });
		                    htmlStr += "</tbody></table>";
		                    //height:220px;
		                    $("#flexigridColumnHeader").css({ "top": (columnHeight + 2) + "px", "left": tmpLeft + "px", "border-bottom-width": 0 }).width(columnWidth).html(htmlStr).removeClass("hidden");
		                    var totalHeight = 25 * tmpCount;
		                    if (totalHeight < 220) {
		                        $("#flexigridColumnHeader").height(totalHeight + tmpCount);
		                    } else {
		                        $("#flexigridColumnHeader").height(220);
		                    }
		                    $("td", "#flexigridColumnHeader").bind({
		                        click: function () {
		                            var value = $(this).attr("value");
		                            var text = $(this).html();
		                            var $th = $(g.hDiv).find("th").eq($(this).attr("thindex"));
		                            if (text == "全部") {
		                                text = $th.attr('normalName');
		                            }
		                            $("#" + $(this).attr("forid")).val(value);
		                            $th.find("div").html(text);
		                            $("#flexigridColumnHeader").addClass("hidden");
		                            if (typeof beforeRefresh != "undefined") {
		                                beforeRefresh($th.find("div"), id);
		                            }
		                            RefreshGrid();
		                        }
		                    });
		                } else {
		                    var tmpWidth = $("#" + id).width();//+ 30;
		                    //			                    var tmpWidth1 = parseInt($("#" + id).attr("attrwidth"));
		                    //			                    alert(tmpWidth);
		                    //			                    if (tmpWidth1 > tmpWidth) {
		                    //			                        tmpWidth = tmpWidth1;
		                    //			                    }

		                    var tmpHeight = $("#" + id).height();
		                    if (tmpHeight < 26) {
		                        tmpHeight = 26;
		                    }
		                    var htmlStr1 = "<div id='tmpContainer' style='padding-top:5px;text-align:center;'>" + $("#" + id).html() + "</div>";
		                    htmlStr1 += "<div style='width:100%;padding-top:5px; text-align: right;'><input type='button' class='button' style='margin-right:5px;' id='btnTmpOk' value='确定'/> </div>";
		                    $("#flexigridColumnHeader").css({ "top": (columnHeight + 2) + "px", "left": tmpLeft + "px", "border-bottom": "1px solid #DAD8D4" }).height(tmpHeight + 35).width(tmpWidth).html(htmlStr).removeClass("hidden");
		                    $("#flexigridColumnHeader").html(htmlStr1).click(function (ee) {
		                        ee.stopPropagation();
		                    });

		                    //取值
		                    $("#tmpContainer input").each(function () {
		                        var childId = $(this).attr("id");
		                        $(this).val($("#" + childId, "#" + id).val());
		                    });

		                    $("#tmpContainer select").each(function () {
		                        var childId = $(this).attr("id");
		                        $(this).val($("#" + childId, "#" + id).val());
		                    });

		                    $("#flexigridColumnHeader #btnTmpOk").click(function () {
		                        //赋值
		                        $("#tmpContainer input").each(function () {
		                            var childId = $(this).attr("id");
		                            $("#" + childId, "#" + id).val($(this).val());
		                        });

		                        $("#tmpContainer select").each(function () {
		                            var childId = $(this).attr("id");
		                            $("#" + childId, "#" + id).val($(this).val());
		                        });

		                        // alert($("#tmpContainer").html());
		                        // $("#" + id).html($("#tmpContainer").html());
		                        $("#flexigridColumnHeader").addClass("hidden").html("");
		                        if (typeof beforeRefresh != "undefined") {
		                            beforeRefresh($th.find("div"), id);
		                        }
		                        RefreshGrid();
		                    });
		                }
		            }
		            e.stopPropagation();

		        }
		    });


		});
	    


		//set bDiv
		g.bDiv.className = 'bDiv';
		$(t).before(g.bDiv);
		$(g.bDiv).css({
			height: (p.height == 'auto') ? 'auto' : p.height + "px"
		}).scroll(function (e) {
		    g.scroll();
		}).append(t);
		if (p.height == 'auto') {
			$('table', g.bDiv).addClass('autoht');
		}

	    $("table", g.bDiv).css('width', 'auto');


		//add td & row properties
		g.addCellProp();
		g.addRowProp();
        //set cDrag only if we are using it
        if (p.colResize === true) {
            var cdcol = $('thead tr:first th:first', g.hDiv).get(0);
            if(cdcol !== null) {
                g.cDrag.className = 'cDrag';
                g.cdpad = 0;
                g.cdpad += (isNaN(parseInt($('div', cdcol).css('borderLeftWidth'), 10)) ? 0 : parseInt($('div', cdcol).css('borderLeftWidth'), 10));
                g.cdpad += (isNaN(parseInt($('div', cdcol).css('borderRightWidth'), 10)) ? 0 : parseInt($('div', cdcol).css('borderRightWidth'), 10));
                g.cdpad += (isNaN(parseInt($('div', cdcol).css('paddingLeft'), 10)) ? 0 : parseInt($('div', cdcol).css('paddingLeft'), 10));
                g.cdpad += (isNaN(parseInt($('div', cdcol).css('paddingRight'), 10)) ? 0 : parseInt($('div', cdcol).css('paddingRight'), 10));
                g.cdpad += (isNaN(parseInt($(cdcol).css('borderLeftWidth'), 10)) ? 0 : parseInt($(cdcol).css('borderLeftWidth'), 10));
                g.cdpad += (isNaN(parseInt($(cdcol).css('borderRightWidth'), 10)) ? 0 : parseInt($(cdcol).css('borderRightWidth'), 10));
                g.cdpad += (isNaN(parseInt($(cdcol).css('paddingLeft'), 10)) ? 0 : parseInt($(cdcol).css('paddingLeft'), 10));
                g.cdpad += (isNaN(parseInt($(cdcol).css('paddingRight'), 10)) ? 0 : parseInt($(cdcol).css('paddingRight'), 10));
                $(g.bDiv).before(g.cDrag);
                var cdheight = $(g.bDiv).height();
                var hdheight = $(g.hDiv).height();
                $(g.cDrag).css({
                    top: -hdheight + 'px'
                });
                $('thead tr:first th', g.hDiv).each(function() {
                    var cgDiv = document.createElement('div');
                    $(g.cDrag).append(cgDiv);
                    if (!p.cgwidth) {
                        p.cgwidth = $(cgDiv).width();
                    }
                    $(cgDiv).css({
                        height: cdheight + hdheight
                    }).mousedown(function(e) {
                        g.dragStart('colresize', e, this);
                    }).dblclick(function(e) {
                        g.autoResizeColumn(this);
                    });
                    if (browser.msie && browser.version < 7.0) {
                        g.fixHeight($(g.gDiv).height());
                        $(cgDiv).hover(function() {
                            g.fixHeight();
                            $(this).addClass('dragging');
                        }, function() {
                            if(!g.colresize) {
                                $(this).removeClass('dragging');
                            }
                        });
                    }
                });
            }
        }
   
	    //点击选项框事件
	    $(document).on("click", ".selectItem", function() {
	        if ($(this).attr("checked")) {
	            $(this).parents("tr").eq(0).addClass("trSelected");
	            $(".selectAll", g.hDiv).attr("checked", $('tbody tr', g.bDiv).size() == $('.trSelected', g.bDiv).size());
	        } else {
	            $(this).parents("tr").eq(0).removeClass("trSelected");
	            $(".selectAll", g.hDiv).attr("checked", false);
	        }
	    });
//        $(".selectItem", g.bDiv).on("click",function () {
//            alert(1);
//            if ($(this).prop("checked")) {
//                $(this).parents("tr").eq(0).addClass("trSelected");
//                $(".selectAll", g.hDiv).prop("checked", $('tbody tr', g.bDiv).size() == $('.trSelected', g.bDiv).size());
//            } else {
//                $(this).parents("tr").eq(0).removeClass("trSelected");
//                $(".selectAll", g.hDiv).prop("checked", false);
//            }
//        });
	    

		//add strip
		if (p.striped) {
			$('tbody tr:odd', g.bDiv).addClass('erow');
		}

		if (p.resizable && p.height != 'auto') {
			g.vDiv.className = 'vGrip';
			$(g.vDiv).mousedown(function (e) {
				g.dragStart('vresize', e);
			}).html('<span></span>');
			$(g.bDiv).after(g.vDiv);
		}
		if (p.resizable && p.width != 'auto' && !p.nohresize) {
			g.rDiv.className = 'hGrip';
			$(g.rDiv).mousedown(function (e) {
				g.dragStart('vresize', e, true);
			}).html('<span></span>').css('height', $(g.gDiv).height());
			if (browser.msie && browser.version < 7.0) {
				$(g.rDiv).hover(function () {
					$(this).addClass('hgOver');
				}, function () {
					$(this).removeClass('hgOver');
				});
			}
			$(g.gDiv).append(g.rDiv);
		}
	    

	    //调整显示隐藏列按钮位置
		var tmpWidth11 = $("table", g.hDiv).width();
		if (tmpWidth11 < $(window).width()) {
		    tmpWidth11 = $(window).width();
		}
		var tmpWindowLeft11 = tmpWidth11 - 25;
		$("#btnShowOrHide").css("left", tmpWindowLeft11 + "px");
	    
		// add pager
		if (p.usepager) {
			g.pDiv.className = 'pDiv';
			g.pDiv.innerHTML = '<div class="pDiv2"></div>';
			$(g.bDiv).after(g.pDiv);
			var html = ' <div class="pGroup"> <div class="pFirst pButton">首页</div><div class="pPrev pButton">上一页</div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pcontrol">' + p.pagetext + ' <input type="text" class="int" style="width:50px;height:15px;"  value="1" /> 页 <span class="pGoto button">转&nbsp;到</span>' + p.outof + ' <span class="pageCount"> 1 </span> 页 </span></div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pNext pButton">下一页</div><div class="pLast pButton">末页</div> </div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pReload button">刷&nbsp;新</div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pPageStat"></span></div>';
			$('div', g.pDiv).html(html);
			$('.pReload', g.pDiv).click(function () {
				g.populate();
			});
			$('.pFirst', g.pDiv).click(function () {
				g.changePage('first');
			});
			$('.pPrev', g.pDiv).click(function () {
				g.changePage('prev');
			});
			$('.pNext', g.pDiv).click(function () {
				g.changePage('next');
			});
			$('.pLast', g.pDiv).click(function () {
				g.changePage('last');
			});
//			$('.pcontrol input', g.pDiv).keydown(function (e) {
//				if (e.keyCode == 13) { 
//                    g.changePage('input');
//				}
		    //			});
			$('.pGoto', g.pDiv).click(function (e) {
			    g.changePage('input');
			});
			if (browser.msie && browser.version < 7) $('.pButton', g.pDiv).hover(function () {
				$(this).addClass('pBtnOver');
			}, function () {
				$(this).removeClass('pBtnOver');
			});
			if (p.useRp) {
				var opt = '',
					sel = '';
				for (var nx = 0; nx < p.rpOptions.length; nx++) {
					if (p.rp == p.rpOptions[nx]) sel = 'selected="selected"';
					else sel = '';
					opt += "<option value='" + p.rpOptions[nx] + "' " + sel + " >" + p.rpOptions[nx] + "&nbsp;&nbsp;</option>";
				}
				$('.pDiv2', g.pDiv).prepend("<div class='pGroup fixselect'> 显示 <span style='display: inline-block;width:60px;'></span> <select id='rp' name='rp'>" + opt + "</select> 条/页 </div> <div class='btnseparator'></div>");
				$('select', g.pDiv).change(function () {
					if (p.onRpChange) {
						p.onRpChange(+this.value);
					} else {
						p.newp = 1;
						p.rp = +this.value;
						g.populate();
					}
				});
			}
			//add search button
			if (p.searchitems) {
				$('.pDiv2', g.pDiv).prepend("<div class='pGroup'> <div class='pSearch pButton'><span></span></div> </div>  <div class='btnseparator'></div>");
				$('.pSearch', g.pDiv).click(function () {
					$(g.sDiv).slideToggle('fast', function () {
						$('.sDiv:visible input:first', g.gDiv).trigger('focus');
					});
				});
				//add search box
				g.sDiv.className = 'sDiv';
				var sitems = p.searchitems;
				var sopt = '', sel = '';
				for (var s = 0; s < sitems.length; s++) {
					if (p.qtype === '' && sitems[s].isdefault === true) {
						p.qtype = sitems[s].name;
						sel = 'selected="selected"';
					} else {
						sel = '';
					}
					sopt += "<option value='" + sitems[s].name + "' " + sel + " >" + sitems[s].display + "&nbsp;&nbsp;</option>";
				}
				if (p.qtype === '') {
					p.qtype = sitems[0].name;
				}
				$(g.sDiv).append("<div class='sDiv2'>" + p.findtext +
						" <input type='text' value='" + p.query +"' size='30' name='q' class='qsbox' /> "+
						" <select name='qtype'>" + sopt + "</select></div>");
				//Split into separate selectors because of bug in jQuery 1.3.2
				$('input[name=q]', g.sDiv).keydown(function (e) {
					if (e.keyCode == 13) {
						g.doSearch();
					}
				});
				$('select[name=qtype]', g.sDiv).keydown(function (e) {
					if (e.keyCode == 13) {
						g.doSearch();
					}
				});
				$('input[value=Clear]', g.sDiv).click(function () {
					$('input[name=q]', g.sDiv).val('');
					p.query = '';
					g.doSearch();
				});
				$(g.bDiv).after(g.sDiv);
			}
		}
		$(g.pDiv, g.sDiv).append("<div style='clear:both'></div>");
		// add title
		if (p.title) {
			g.mDiv.className = 'mDiv';
			g.mDiv.innerHTML = '<div class="ftitle">' + p.title + '</div>';
			$(g.gDiv).prepend(g.mDiv);
			if (p.showTableToggleBtn) {
				$(g.mDiv).append('<div class="ptogtitle" title="Minimize/Maximize Table"><span></span></div>');
				$('div.ptogtitle', g.mDiv).click(function () {
					$(g.gDiv).toggleClass('hideBody');
					$(this).toggleClass('vsble');
				});
			}
		}
		//setup cdrops
		g.cdropleft = document.createElement('span');
		g.cdropleft.className = 'cdropleft';
		g.cdropright = document.createElement('span');
		g.cdropright.className = 'cdropright';
		//add block
		g.block.className = 'gBlock';
	    $(g.block).html("<div class='loadingimg'><div>");
		var gh = $(g.bDiv).height();
		var gtop = g.bDiv.offsetTop;
		$(g.block).css({
			width: g.bDiv.style.width,
			height: gh,
			background: 'white',
			position: 'relative',
			marginBottom: (gh * -1),
			zIndex: 1,
			top: gtop,
			left: '0px'
		});
	    
 

		$(g.block).fadeTo(0, p.blockOpacity);
		// add column control
		if ($('th', g.hDiv).length) {
			g.nDiv.className = 'nDiv';
			g.nDiv.innerHTML = "<table cellpadding='0' cellspacing='0'><tbody></tbody></table>";
			$(g.nDiv).css({
				marginBottom: (gh * -1),
				display: 'none',
				top: gtop-1
			}).noSelect(true);

		    $("table", g.nDiv).css("width", "150px");
		    var cn = 0;
		    $('tbody', g.nDiv).append('<tr  id="btnReset" etmname="">&nbsp;<td class="ndcol1"><input type="checkbox"  class="togCol"/></td><td class="ndcol2 secMemuBg">恢复默认表格设置</td></tr>');

		    var jsAddColumnContainerHtmlStr = "<div id='jsAddColumnContainer' class='hidden'>";
			$('th div', g.hDiv).each(function () {
				var kcol = $("th[axis='col" + cn + "']", g.hDiv)[0];
				var chk = 'checked="checked"';
				if (kcol.style.display == 'none') {
					chk = '';
				}

				var isCanHide = $(this).parent().attr("IsCanHide") == "true";

				var isJsAddColumn = $(this).parent().attr("isJsAddColumn") == "true";

				$('tbody', g.nDiv).append('<tr  class="' + (!isCanHide ? "trDisable" : "") + (isJsAddColumn?" hidden ":"") + '" etmname="' + $(this).parent().attr("etmname") + '"><td class="ndcol1"><input type="checkbox" ' + chk + ' class="togCol" value="' + cn + '"  ' + (isCanHide ? "" : "disabled='disabled'") + '/></td><td class="ndcol2">' + this.innerHTML + '</td></tr>');
				if (isJsAddColumn) {
				    jsAddColumnContainerHtmlStr += '<span etmname="' + $(this).parent().attr("etmname") + '" index="' + cn + '"><input type="checkbox" ' + chk + ' class="togCol" value="' + cn + '"  />' + this.innerHTML + '</span>';
				}
				cn++;
		
				
			});

			jsAddColumnContainerHtmlStr += "</div>";
			if ($("#jsAddColumnContainer").size() > 0) {
			    $("#jsAddColumnContainer").remove();
			}
			$("body").append(jsAddColumnContainerHtmlStr);

			function setColumnInfo($this) {
			    var index = parseInt($this.attr("index"));//-1;
                if ($this.hasClass("jsAddItemSelected")) {
                    $this.removeClass("jsAddItemSelected");
                    $this.find("input").attr("checked", false);
                    g.toggleCol(index, false, true);
                    flexiGridJsAddColumn._columnCancel($this.attr("etmname"));
                } else {
                    $this.addClass("jsAddItemSelected");
                    $this.find("input").attr("checked", true);
                    g.toggleCol(index, true, true);
                    flexiGridJsAddColumn._columnSelected($this.attr("etmname"));
                }
              
            }
		    $("#jsAddColumnContainer span").click(function() {
		        setColumnInfo($(this));
		    });
		    $("#jsAddColumnContainer input").click(function (e) {
		        e.stopPropagation();
		        var $this = $(this).parent();
		        setColumnInfo($this);
		    });


			$('tbody .trDisable', g.nDiv).find("td").css("opacity", "0.5");
		  
			if (browser.msie && browser.version < 7.0) $('tr', g.nDiv).hover(function () {
				$(this).addClass('ndcolover');
			}, function () {
				$(this).removeClass('ndcolover');
			});
		    //点击
			$('td.ndcol2', g.nDiv).click(function (e) {
			    if ($(this).parents("tr").eq(0).hasClass("trDisable")) {
			        return;
			    }
			    if ($(this).parents("tr").eq(0).attr("id") == "btnReset") {
			        e.stopPropagation();
			        if (confirm("确定要恢复默认的表格设置吗？")) {
			            flexiGridAjax("FlexiGridAjax_Reset", {
			                "pageamethod": $.trim($("#pageamethod").val())
			            }, function(result) {
			                if (result != null && result == "true") {
			                    window.location.href = window.location.href;
			                }
			            });
			            $(this).attr("checked", false);
			            return;
			        }
			    }
			    if ($('input:checked', g.nDiv).length <= p.minColToggle && $(this).prev().find('input')[0].checked) return false;
				return g.toggleCol($(this).prev().find('input').val());
			});

			$('tbody .ndcol2 input', g.nDiv).each(function () {
			    $(this).attr("disabled", "disabled");
			});

			$('input.togCol', g.nDiv).click(function (e) {
			    if ($(this).parents("tr").eq(0).hasClass("trDisable")) {
			        return;
			    }
			    if ($(this).parents("tr").eq(0).attr("id") == "btnReset") {
			        e.stopPropagation();
			        if (confirm("确定要恢复默认的表格设置吗？")) {
			            flexiGridAjax("FlexiGridAjax_Reset", {
			                "pageamethod": $.trim($("#pageamethod").val())
			            }, function (result) {
			                if (result != null && result == "true") {
			                    window.location.href = window.location.href;
			                }
			            });
			        }
			        $(this).attr("checked", false);
			        return;
			    }
				if ($('input:checked', g.nDiv).length < p.minColToggle && this.checked === false) return false;
				$(this).parent().next().trigger('click');
			});
			$(g.gDiv).prepend(g.nDiv);
		    
			if (typeof InitnDivScrollbar != "undefined") {
			    InitnDivScrollbar();
			}

		    $(g.nBtn).addClass('nBtn')
		        .html('<div>&nbsp;</div>');
//			$(g.nBtn).addClass('nBtn')
//				.html('<div>&nbsp;</div>')
//				.prop('title', '隐藏列/显示列')
//				.click(function (e) {
//				    $(g.nDiv).toggle();
//				    if (g.nDiv.hidden) {
//				        $(g.nBtn).removeClass("nBtnClick");
//				    } else {
//				        $(g.nBtn).addClass("nBtnClick");
//				    }
//				    if (typeof ReSizenDivScrollbar != "undefined") {
//				        ReSizenDivScrollbar();
//				    }
//					return true;
//				}
//			);
		    

			if (p.showToggleBtn) {
				$(g.gDiv).prepend(g.nBtn);
			}
		}
	    

		// add date edit layer
		$(g.iDiv).addClass('iDiv').css({
			display: 'none'
		});
		$(g.bDiv).append(g.iDiv);
		// add flexigrid events
		$(g.bDiv).hover(function () {
			//$(g.nDiv).hide();
			$(g.nBtn).hide();
			$(g.nBtn).removeClass("nBtnClick");
		}, function () {
			if (g.multisel) {
				g.multisel = false;
			}
		});
		$(g.gDiv).hover(function () {}, function () {
			//$(g.nDiv).hide();
			$(g.nBtn).hide();
		});

		//add document events
	    $(document).mousemove(function (e) {
		    g.dragMove(e);
	    }).mouseup(function (e) {
		    g.dragEnd();
	    }).hover(function () { }, function () {
		    g.dragEnd();
		});
		//browser adjustments
		if (browser.msie && browser.version < 7.0) {
			$('.hDiv,.bDiv,.mDiv,.pDiv,.vGrip,.tDiv, .sDiv', g.gDiv).css({
				width: '100%'
			});
			$(g.gDiv).addClass('ie6');
			if (p.width != 'auto') {
				$(g.gDiv).addClass('ie6fullwidthbug');
			}
		}
		g.rePosDrag();
		g.fixHeight();
		//make grid functions accessible
		t.p = p;
		t.grid = g;
		// load data
		if (p.url && p.autoload) {
			g.populate();
		}
		return t;
	};
	var docloaded = false;
	$(document).ready(function () {
		docloaded = true;
	});
	$.fn.flexigrid = function (p) {
	    return this.each(function () {
	       // console.log($.cookie);
//	        if ($.cookie == null || $.cookie == undefined) {
//	           
//	           $("body").append(" <script src='../JS/jquery.cookie.js' type='text/javascript'></script>");
//	        }
			if (!docloaded) {
				$(this).hide();
				var t = this;
				$(document).ready(function () {
					$.addFlex(t, p);
				});
			} else {
				$.addFlex(this, p);
			}
		});
	}; //end flexigrid
	$.fn.flexReload = function (p) { // function to reload grid
		return this.each(function () {
			if (this.grid && this.p.url) this.grid.populate();
		});
	}; //end flexReload
	$.fn.flexOptions = function (p) { //function to update general options
		return this.each(function () {
			if (this.grid) $.extend(this.p, p);
		});
	}; //end flexOptions
	$.fn.flexToggleCol = function (cid, visible) { // function to reload grid
		return this.each(function () {
			if (this.grid) this.grid.toggleCol(cid, visible);
		});
	}; //end flexToggleCol
	$.fn.flexAddData = function (data) { // function to add data to grid
		return this.each(function () {
			if (this.grid) this.grid.addData(data);
		});
	};
	$.fn.noSelect = function (p) { //no select plugin by me :-)
		var prevent = (p === null) ? true : p;
		if (prevent) {
			return this.each(function () {
				if (browser.msie || browser.safari) $(this).bind('selectstart', function () {
					return false;
				});
				else if (browser.mozilla) {
					$(this).css('MozUserSelect', 'none');
					$('body').trigger('focus');
				} else if (browser.opera) $(this).bind('mousedown', function () {
					return false;
				});
				else $(this).attr('unselectable', 'on');
			});
		} else {
			return this.each(function () {
				if (browser.msie || browser.safari) $(this).unbind('selectstart');
				else if (browser.mozilla) $(this).css('MozUserSelect', 'inherit');
				else if (browser.opera) $(this).unbind('mousedown');
				else $(this).removeProp('unselectable', 'on');
			});
		}
	}; //end noSelect
  $.fn.flexSearch = function(p) { // function to search grid
	return this.each( function() { if (this.grid&&this.p.searchitems) this.grid.doSearch(); });
  }; //end flexSearch
})(jQuery);
