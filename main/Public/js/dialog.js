//
//(function ($) {
//    var isMouseDown = false;
//    var currentElement = null;
//    var dropCallbacks = {};
//    var dragCallbacks = {};
//    var lastMouseX;
//    var lastMouseY;
//    var lastElemTop;
//    var lastElemLeft;
//    var dragStatus = {};
//    $.getMousePosition = function (e) {
//        var posx = 0;
//        var posy = 0;
//        if (!e) var e = window.event;
//        if (e.pageX || e.pageY) {
//            posx = e.pageX;
//            posy = e.pageY;
//        }
//        else if (e.clientX || e.clientY) {
//            posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
//            posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
//        }
//        return { 'x': posx, 'y': posy };
//    };
//    $.updatePosition = function (e) {
//        var pos = $.getMousePosition(e);
//
//        var spanX = (pos.x - lastMouseX);
//        var spanY = (pos.y - lastMouseY);
//
//        var tmpFloatBoxHeight = $("#floatBox").height();
//        var tmpTop = lastElemTop + spanY;
//        if (tmpTop < 0) {
//            tmpTop = 0;
//        }
//        else if ($(window).height() < (tmpTop + tmpFloatBoxHeight)) {
//            tmpTop = $(window).height() - tmpFloatBoxHeight - 10;
//        }
//        
//        var tmpLeft = lastElemLeft + spanX;
//        var tmpFloatBoxWidth = $("#floatBox").width();
//        if ($(window).width() < tmpLeft + tmpFloatBoxWidth) {
//            tmpLeft = $(window).width() - tmpFloatBoxWidth-10;
//        }
//        else if (tmpLeft<0) {
//            tmpLeft = 0;
//        }
//
//        if (tmpTop < 0) {
//            tmpTop = 0;
//        }
//        $(currentElement).css("top", tmpTop+"px");
//        $(currentElement).css("left", tmpLeft+"px");
//    };
//    $(document).mousemove(function (e) {
//
//        if (isMouseDown && dragStatus[currentElement.id] == 'on') {
//            $.updatePosition(e);
//            if (dragCallbacks[currentElement.id] != undefined) {
//                dragCallbacks[currentElement.id](e, currentElement);
//            }
//
//            return false;
//        }
//    });
//    $(document).mouseup(function (e) {
//
//        if (isMouseDown && dragStatus[currentElement.id] == 'on') {
//            isMouseDown = false;
//            if (dropCallbacks[currentElement.id] != undefined) {
//                dropCallbacks[currentElement.id](e, currentElement);
//            }
//
//            return false;
//        }
//    });
////    $(document).mouseup(function (e) {
////        dragStatus[currentElement.id] == 'off';
////        isMouseDown = false;
////    });
////    $(document).mousedown(function (e) {
////        isMouseDown = true;
//////        if (isMouseDown && dragStatus[currentElement.id] == 'on') {
//////            isMouseDown = false;
//////            if (dropCallbacks[currentElement.id] != undefined) {
//////                dropCallbacks[currentElement.id](e, currentElement);
//////            }
//////
//////            return false;
//////        }
////    });
//    $.fn.ondrag = function (callback) {
//        return this.each(function () {
//            dragCallbacks[this.id] = callback;
//        });
//    };
//    $.fn.ondrop = function (callback) {
//        return this.each(function () {
//            dropCallbacks[this.id] = callback;
//        });
//    };
//    $.fn.dragOff = function () {
//        return this.each(function () {
//            dragStatus[this.id] = 'off';
//        });
//    };
//    $.fn.dragOn = function () {
//        return this.each(function () {
//            dragStatus[this.id] = 'on';
//        });
//    };
//    $.fn.easydrag = function (allowBubbling) {
//    return this.each(function () {
//        if (undefined == this.id || !this.id.length) this.id = "easydrag" + (new Date().getTime());
//        dragStatus[this.id] = "on";
//        $(this).css("cursor", "move");
//            $(this).mousedown(function (e) {
//                $(this).css("position", "absolute");
//                $(this).css("z-index", "10000");
//                isMouseDown = true;
//                currentElement = this;
//                var pos = $.getMousePosition(e);
//                lastMouseX = pos.x;
//                lastMouseY = pos.y;
//                lastElemTop = this.offsetTop;
//                lastElemLeft = this.offsetLeft;
//                $.updatePosition(e);
//                if ($.browser.msie && $.browser.version >=9) {
//                    $("#floatBox .list_th").css("margin-top", "-16px");
//                }
//                return allowBubbling ? true : false;
//            });
//        });
//    };
//})(jQuery);


(function (document) {
    $.fn.Drag = function() {
        var M = false;
        var Rx, Ry;
        var t = $(this);
        $(this).find("iframe").load(function () {
            $(this).contents().find("body").click(function () {
                M = false;
            });
        });
        $("#floatBoxBg").live("click", function () {
            M = false;
        });
        t.mousedown(function(event) {
            Rx = event.pageX - (parseInt(t.css("left")) || 0);
            Ry = event.pageY - (parseInt(t.css("top")) || 0);
            t.css("position", "absolute").css('cursor', 'move');//.fadeTo(20, 0.5);
            M = true;
            if ($.browser.msie && $.browser.version >= 9) {
                t.find(".list_th").css("margin-top", "-16px");
            }
        })
            .mouseup(function(event) {
                M = false;
                //t.fadeTo(20, 1);
//                if ($("#floatBox").css("top").indexOf("-")) {
//                    $("#floatBox").css("top", "0px");
//                }
            });
        $(document).mousemove(function(event) {
            if (M) {

                var tmpFloatBoxHeight = t.height();
                var tmpTop = event.pageY - Ry;
                if (tmpTop < 0) {
                    tmpTop = 0;
                    M = false;
                }
                else if ($(window).height() < (tmpTop + 30)) {
                    tmpTop = $(window).height() - 30 - 0;
                    M = false;
                }
//                else if ($(window).height() < (tmpTop + tmpFloatBoxHeight)) {
//                    tmpTop = $(window).height() - tmpFloatBoxHeight - 0;
//                    M = false;
//                }

                var tmpLeft = event.pageX - Rx;
                var tmpFloatBoxWidth = t.width();
                if ($(window).width() < tmpLeft + tmpFloatBoxWidth) {
                    tmpLeft = $(window).width() - tmpFloatBoxWidth - 0;
                  //  M = false;
                }
                else if (tmpLeft < (80-tmpFloatBoxWidth)) {
                    tmpLeft = 80 - tmpFloatBoxWidth;
                    // M = false;
                }
//                else if (tmpLeft < 0) {
//                    tmpLeft = 0;
//                   // M = false;
//                }

                if (tmpTop < 0) {
                    tmpTop = 0;
                }

               // t.css({ top: event.pageY - Ry, left: event.pageX - Rx });
                t.css({ top: tmpTop, left: tmpLeft });
            }
        });
    };
})(document);

function newGuid() {
    var guid = "";
    for (var i = 1; i <= 32; i++) {
        var n = Math.floor(Math.random() * 16.0).toString(16);
        guid += n;
        if ((i == 8) || (i == 12) || (i == 16) || (i == 20))
            guid += "-";
    }
    return guid;
}
function dialog(title, content, width, height, cssName) {
  
    dialog(title, content, width, height, cssName, "iframe");
}

function dialog(title, content, width, height, cssName, contenttype) {
    var dialogId = "d" + newGuid();
    var fixTitle = "margin-top: -16px;";
    if ($.browser.msie && $.browser.version >= 9) {
        fixTitle = "margin-top: -15px;";
    }
    var tmpZIndex = 10001;
    if ($(".floatBox").size() > 0) {
        $(".floatBox").each(function() {
            var thisZIndex = parseInt($(this).css("z-index"));
            if (tmpZIndex <= thisZIndex) {
                tmpZIndex = thisZIndex + 1;
            }
        });
    }
    var temp_float = "";
    if ($("#floatBoxBg").size() == 0) {
        temp_float = "<div id=\"floatBoxBg\" style=\"height:" + $(document).height() + "px;filter:alpha(opacity=0);opacity:0;\"></div>";
    }

    temp_float += "<div id=\"" + dialogId + "\" class=\"floatBox\" style='background:#F4F4F4;z-index:" + tmpZIndex + ";'>";
    temp_float += "<div class=\"list_th\" style='" + fixTitle + "text-align: left; padding-left:10px;_height: 45px;_overflow: hidden; '><h4 style='_padding-top: 16px;'></h4><span style='position: absolute;right: 10px;top: 0;_top:0px;font-weight: bold;_font-size: 15px;;' class='close'>关闭</span></div>";
    temp_float += "<div class=\"content\"></div>";
    temp_float += "</div>";
    $("body").append(temp_float);
    if ($.browser.msie && $.browser.version == 7) {
        var $h = $("#" + dialogId).find(".list_th");
        $h.css({ "margin-top": "0px", "height": "32px", "line-height": "5px" });
        $h.find(".close").css({ "line-height": "32px", "top": "0px" });
    }
    $("#" + dialogId + " .list_th").mousedown(function() {
        var tmpZIndex1 = parseInt($("#" + dialogId).css("z-index"));
        if ($(".floatBox").size() > 0) {
            $(".floatBox").each(function () {
                var thisZIndex = parseInt($(this).css("z-index"));
                if (tmpZIndex1 <= thisZIndex) {
                    tmpZIndex1 = thisZIndex + 1;
                }
            });
            $("#" + dialogId).css("z-index", tmpZIndex1);
        }
    });
    $("#" + dialogId + " .list_th span").click(function () {
        if ($(".floatBox").size() == 1) {
            $("#floatBoxBg").animate({ opacity: "0" }, "fast", function () {
                $(this).hide();
            });
        }

        //$("#floatBox").animate({top:($(document).scrollTop()-(height=="auto"?300:parseInt(height)))+"px"},"fast",function(){$(this).hide();}); 
        $("#" + dialogId).remove();
        if (typeof diglogClose != 'undefined') {
            diglogClose();
        }
    });

    $("#" + dialogId + " .close").hover(
        function() {
            $(this).css({ "cursor": "pointer" });
        }, function() {
            $(this).css({ "cursor": "default" });

        });


    $("#" + dialogId + " .list_th h4").html(title);
    //contentType=content.substring(0,content.indexOf(":"));

    content = content.substring(content.indexOf(":") + 1, content.length);
    switch (contenttype) {
        case "url":
            var content_array = content.split("?");
            $("#" + dialogId + " .content").ajaxStart(function() {
                $(this).html("loading...");
            });
            $.ajax({
                type: content_array[0],
                url: content_array[1],
                data: content_array[2],
                error: function() {
                    $("#" + dialogId + " .content").html("error...");
                },
                success: function(html) {
                    $("#" + dialogId + " .content").html(html);
                }
            });
            break;
        case "text":
            $("#" + dialogId + " .content").html(content).height((parseInt(height.replace("px", ""))) - 50).css({ "overflow-y": "auto" });
            break;
        case "id":
            $("#" + dialogId + " .content").html($("#" + content + "").html());
            break;
        case "iframe":
        default:
            var tmp = 0;
            if (typeof isNewUI != "undefined" && isNewUI) {
                tmp = 15;
            }
            $("#" + dialogId + " .content").html("<iframe src='' width=\"100%\" height=\"" + (parseInt(height) - 36 + tmp) + "px" + "\" scrolling=\"no\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\"></iframe>");
            $("#" + dialogId + " .content iframe").attr("src", content);

            break;
    }


    $("#floatBoxBg").show();
    $("#floatBoxBg").animate({ opacity: "0.5" }, "fast");
    $("#" + dialogId).attr("class", "floatBox " + cssName);
    //$("#floatBox").css({ display: "block", left: (($(document).width()) / 2 - (parseInt(width) / 2)) + "px", top: ($(document).scrollTop() + 60) + "px", width: width, height: height });
    var floatTop = ($(document).scrollTop() + 60);
    height = height + "";
    var floatTmpHeight = parseInt(height.replace("px"));
    if (location.href.toLowerCase().indexOf("main.aspx")) {
        floatTop = 0;
        if ($(window).height() > floatTmpHeight) {

            floatTop = ($(window).height() - floatTmpHeight) / 2;
        }
        if (floatTmpHeight > $(window).height()) {
            height = ($(window).height() - 10) + "px";
            $("#" + dialogId + " .content iframe").height($(window).height() - 10 - 30);
        }
    }

    $("#" + dialogId).css({ display: "block", left: (($(document).width()) / 2 - (parseInt(width) / 2)) + "px", top: floatTop + "px", width: width, height: height });
    if (typeof diglogOpen != 'undefined') {
        diglogOpen();
    }
    if (typeof newInit != 'undefined') {
        newInit();
    }


    //$("#floatBox").easydrag();
    $("#" + dialogId).Drag();

    //$("#floatBox").css({display:"block",left:(($(document).width())/2-(parseInt(width)/2))+"px",top:($(document).scrollTop()-(height=="auto"?300:parseInt(height)))+"px",width:width,height:height});
    //$("#floatBox").animate({top:($(document).scrollTop()+100)+"px"},"fast"); 

}

function BoxClose(bgShow) {
    if (!bgShow)
        $("#floatBoxBg").hide();

    if ($(".floatBox").size() > 0) {
        var $top = null;
        var tmpZIndex1 = 9;
        $(".floatBox").each(function () {
            var thisZIndex = parseInt($(this).css("z-index"));
            if (tmpZIndex1 <= thisZIndex) {
                tmpZIndex1 = thisZIndex + 1;
                $top = $(this);
            }
        });
        if ($top != null) {
            $top.remove();
        }
    }
    // $("#"+dialogId).hide();
}
function GetTopWin() {
    var $oldTop = null;
    if ($(".floatBox").size() > 0) {
        var $top = null;
        var tmpZIndex1 = 9;
        $(".floatBox").each(function () {
            var thisZIndex = parseInt($(this).css("z-index"));
            if (tmpZIndex1 <= thisZIndex) {
                tmpZIndex1 = thisZIndex + 1;
                $oldTop = $top;
                $top = $(this);
            }
        });
    }
    if ($oldTop != null && $oldTop.find("iframe")[0])
        return $oldTop.find("iframe")[0].contentWindow;
    else
        return null;
}