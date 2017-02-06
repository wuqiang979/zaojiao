function onChildPageClick() {
    hideShortCutMenu();
}
function hideShortCutMenu() {
    if (!$("#shortcut").hasClass("hidden")) {
        $("#shortcut").stop().animate({ "height": "0px" }, 200);
        $("#shortcut").addClass("hidden");
    }
}
function showDialog(title, url, width, height) {
    dialog(title, "iframe:" + url, width, height, "iframe");
}
function showsending(b, content) {
    if (b) {
        $("#loading-mask").height($(document).height() - 10);
        $("#loading,#loading-mask").removeClass("hidden");
        $("#loading,#loading-mask").removeClass("hidden");
        if (content != "") $("#loading-msg").html(content);
    } else {
        $("#loading,#loading-mask").addClass("hidden");
    }
}
var ETMTipsTimer = null;
function ShowTips(str) {
    if (ETMTipsTimer != null) {
        clearTimeout(ETMTipsTimer);
        ETMTipsTimer = null;
    }
    if ($("#etmTips").size() > 0) {
        $("#etmTips").remove();
    }
    $("body").append("<div id='etmTips' style='position: absolute;top: 10px;left: 10px;padding: 10px;border: 1px solid #2D3335;color: white;z-index: 10999;display:none;background-color:#384042;' class=''>" + str + "</div>");
    $("#etmTips").css({ "top": ($(window).height() - $("#etmTips").height()) / 2 + "px", "left": ($(window).width() - $("#etmTips").width()) / 2 + "px", "display": "block" });
    ETMTipsTimer = setTimeout(function () {
        $("#etmTips").animate({ "left": (-$("#etmTips").width() - 10) + "px" }, 300, function () {
            $("#etmTips").remove();
        });
    }, 2200);

}

var isCloseMenu = true;
var isClick = false;
var timer;
var isNewUIForJs = false;
//var isFirstMenuClick = false;
function clearSelected() {
    $(".selected").each(function () {
        if (!$(this).hasClass("currentSelected") && !$(this).hasClass("currentThirdMemuSelected")) {
            $(this).removeClass("selected");
        }
    });
}


function doSecSelected($a, isAdd) {
    if (isAdd) {
        $a.addClass("secMemuBg");
    } else {
        $a.removeClass("secMemuBg");
    }
}

//var isFixedScrollbarLeft = false;
function fixScrollbarLeft() {
    //var strPx = $(".nicescroll-rails").last().css("left").replace("px", "");
    //$(".nicescroll-rails").last().css("left", (parseInt(strPx) + 1) + "px");
}

//function removeSecBg() {
//    $(".secMemuBg").removeClass("secMemuBg");
//}

function closeChildMenu() {
    return;
    if (isCloseMenu) {
        $("#menuNormal").removeClass("hidden");
        $("#menuhover").addClass("hidden");
        clearSelected();
        clearTimeout(timer);
        var $divs = $(".childMenuTopBar div");
        $divs.eq(0).html($divs.eq(0).attr("source"));
        $divs.eq(1).html($divs.eq(1).attr("source"));
        $(".d a").addClass("changeinfo");
    }
}

function onSettingState(b) {
    /// <summary>
    /// 修正ie6下 下拉框挡住浮层问题
    /// </summary>
    /// <param name="b"></param>
    if ($.browser.msie && parseInt($.browser.version) == 6) {
        if (b) {
            $("select").each(function () {
                $(this).addClass("hidden").after("<span class='selectIe6Fix'>" + $(this).find("option:selected").text() + "</span>");
            });
        } else {
            $(".selectIe6Fix").each(function () {
                $(this).prev("select").removeClass("hidden");
                $(this).remove();
            });
        }
    }
}

function loadIndex() {
    $("#content").attr("src", urlStr);
    $("#content").attr("url", urlStr);
}

var urlStr = "";
var firstLoadMyIndex = true;


//锁屏
function setLock() {
    var tmpWindowHeight = $(window).height();
    var tmpWindowWidth = $(window).width();
    $("#lockScreen").height(tmpWindowHeight).width(tmpWindowWidth);
    if ($("#lockScreen").css("top") != "0px") {
        $("#lockScreen").css("top", -tmpWindowHeight + "px");
    }
    $("#moveLock").css({ "left": ((tmpWindowWidth - 126) / 2) + "px" });
}

$(function () {
    // shortcut
    $("#shortcut .item").hover(function () {
        $(this).find(".icon").addClass("styleColor");
        var $img = $(this).find("img");
        $img.attr("src", $img.attr("src").replace(".png", "_hover.png"));
    }, function () {
        $(this).find(".icon").removeClass("styleColor");
        var $img = $(this).find("img");
        $img.attr("src", $img.attr("src").replace("_hover.png", ".png"));
    }).click(function () {
        $(".pagetitle").html($(this).find(".description").html().replace(new RegExp("&nbsp;", "g"), ""));
        var $img = $(this).find("img");
        $img.attr("src", $img.attr("src").replace("_hover.png", ".png"));
        hideShortCutMenu();
    });
    $("#shortcut").click(function (e) {
        e.stopPropagation();
    });
    $("#btnShortCut").click(function (e) {
        e.stopPropagation();
        if ($("#shortcut").hasClass("hidden")) {
            var left = $("#pagetitle").offset().left;
            if (left < 90) {
                left += 1;
            }
            $("#shortcut").css("left", left + "px").width($(window).width() - left).height(0).removeClass("hidden").animate({ "height": "142px" }, 1000, "easeOutBounce");
        }
        else {
            hideShortCutMenu();
        }
    });
    //end shortcut

    $("#langList div:last").css("border-bottom-width", "0px");
    $("#langSelect,#langContainer img").click(function (e) {
        if ($("#langList").hasClass("hidden")) {
            var $langSelect = $("#langSelect").offset();
            $("#langList").css({ "bottom": $(window).height() - $langSelect.top + "px", "left": $langSelect.left + "px" }).removeClass("hidden");
            $("#langContainer img").attr("src", "/Images/ArrowUp1.png");
        }
        else {
            $("#langList").addClass("hidden");
            $("#langContainer img").attr("src", "/Images/ArrowDown2.png");
        }
        e.stopPropagation();
    });
    $("#settingForHover").click(function () {
        $("#langList").addClass("hidden");
        $("#langContainer img").attr("src", "/Images/ArrowDown2.png");
    });
    $("#langList div").hover(
        function () {
            $(this).addClass("menuHover");
        }, function () {
            $(this).removeClass("menuHover");
        }).click(function (e) {
            var lang = $(this).attr("lang");
            if (lang == "zh_HK" || lang == "zh_TW" || lang == "zh_CN") {
                $.ajaxMethod("setlang", { "lang": lang }, function (result) {
                    if (result != null && result.indexOf("true") != -1) {
                        window.location.href = window.location.href;
                    }
                }, false, "text");
            }
            else {
                $("#langList,#settingForHover").addClass("hidden");
                alert('请联系管理员开通多语定制版');
            }
        });


    setTimeout(function () {
        $(".settingNormal2").height($("#settingForHover").height()).width($("#settingForHover").width());
    }, 2000);

    $("#btnReturn1").click(function () {
        $(".settingNormal2").animate({ "left": $("#settingForHover").width() + 20 + "px" }, 300, function () {
            $(".settingNormal2").addClass("hidden");
        });
    });

    $("#btnOption").click(function (e) {
        alert("即将发布");
    });

    $("#btnHelp,#btnOption").click(function (e) {
        alert("即将发布"); //window.parent.showDialog('使用帮助', '/MyIndex/BBSLogin.aspx', '900px', '500px');
    });

    //锁屏
    $("#btnLockScreen").click(function () {
        $("#settingForHover").addClass("hidden");
        $("#lockScreen").removeClass("hidden").css("top", -$(window).height() + "px").animate({ "top": "0px" }, 800, function () {

        });
    });
    function setLockAnimate() {
        var isUp = false;
        var top = parseInt($("#lockScreen").css("top").replace("px", ""));
        if (top > -10 && top < 10) {
            $("#lockScreen").animate({ "top": -$(window).height() + "px" }, 800);
            return;
        } else if (top < 0) {
            top = -top;
            isUp = true;
        }
        if (top > 100) {
            if (isUp) {
                $("#lockScreen").animate({ "top": -$(window).height() + "px" }, 800);
            }
            else {
                $("#lockScreen").animate({ "top": $(window).height() + "px" }, 800);
            }
        }
        else {
            $("#lockScreen").animate({ "top": "0px" }, 100);
        }
    }

    var canMove = false;
    var ry = 0;
    $(document).mousemove(function (e) {
        if (canMove && (e.pageY <= 0 || e.pageY > $(window).height())) {
            canMove = false;
            setLockAnimate();
            // $("#lockScreen").animate({ "top": -$(window).height() + "px" }, 800);
        }
        var tmpTop = e.pageY - ry;
        if (canMove && tmpTop < 0) {
            $("#lockScreen").css({ "top": tmpTop + "px" });
        }
    });
    $("#moveLock").bind({
        mouseenter: function () {
            $(this).addClass("lockHover").removeClass("lockNormal");
        },
        mouseleave: function () {
            $(this).addClass("lockNormal").removeClass("lockHover");
            canMove = false;
        },
        mouseup: function () {
            canMove = false;
            // console.log("mouseup");
            $(this).css('cursor', 'default').addClass("lockNormal").removeClass("lockHover");
            setLockAnimate();
        },
        mousedown: function (e) {
            canMove = true;
            $(this).css('cursor', 'move');
            ry = e.pageY;
        }
    });
    //锁屏


    $("#btnUserCenter").click(function (e) {
        $(".pagetitle").html("个人中心");
        $('#content').attr('src', '/Modules/SysConfig/ManagerCenter.aspx');
        $("#content").attr("url", '/Modules/SysConfig/ManagerCenter.aspx');
        $("#settingForHover,#langList").addClass("hidden");
        // e.stopPropagation();
    });
    $("#btnLogOut3").click(function (e) {
        $.post("/Ajax.asmx/LogOut", "", function () {
            location.href = "/Login.aspx";
        }, "text");

        e.stopPropagation();
    });

    //GetIndexMenu(false);

    resize(true);
    $(window).resize(function () {
        resize(true);
    });

    if ($(".tabs a").size() > 0 || $(".tabs span").size() > 0) {
        if ($(".tabs span").size() > 0) {
            $(".tabs").append("<div class='searchOption'></div><div class='clear'></div>");
            $(".tabs span").size().each(function () {
                var $this = $(this);
                $this.addClass("hidden");
                $(".tabs .searchOption").append("");
                //checked  link
            });
        }

        $(".tabs").append("<div class='tabsBottomBar'>&nbsp;</div>");
        $(".tabs a").bind({
            mouseenter: function () {
                if (!$(this).hasClass("tabSelected") && $(this).parents(".searchOption").size() == 0) {
                    $(this).addClass("tabHover");
                }
            },
            mouseleave: function () {
                if (!$(this).hasClass("tabSelected") && $(this).parents(".searchOption").size() == 0) {
                    $(this).removeClass("tabHover");
                }
            }
        });
    }
    if ($(".fixTopBarColor").size() == 0) {
        $(".childMenuTopBar").append(" <div class='fixTopBarColor'>&nbsp;</div>");
    }
    $(".d a").live("click", function () {
        $(".pagetitle").html("我的信息");
    });

    var isFloatMenuStatus = false;
    //获取是否浮动
    $.ajaxMethod("menustatus", "", function (result) {
        if (result != null && result.length > 2 && result == "true") {
            isFloatMenuStatus = true;
        }
    }, false, "text");

    $("#btnFloatTop,#btnFloatNormal").click(function () {
        var isFloatMenu = "false";
        if ($(this).attr("id") == "btnFloatTop") {//浮动
            isFloatMenu = "true";
        }
        $.ajaxMethod("menu", { "floatmenu": isFloatMenu }, function (result) {
            if (result != null && result.length > 2 && result == "true") {
                if (isFloatMenu == "true") {//设为浮动模式
                    isFloatMenuStatus = true;

                    $(".childMenuTopBar,#menuNormal,#menuhover").addClass("hidden");
                    $("#childMenu").css("border-right-width", "0px");
                    $(".fixIndex").removeClass("hidden");
                    resetIco(true);
                }
                else {//设为停靠模式
                    isFloatMenuStatus = false;
                    $("#floatMenu").addClass("hidden");

                    if ($(".currentSelected").attr("id") != "logo") {
                        $(".childMenuTopBar,#menuNormal").removeClass("hidden");
                        $("#childMenu").css("border-right-width", "2px");
                        if (($.browser.msie && $.browser.version <= 8.0) || $.browser.webkit) {
                            $("#childMenu").css("border-right-width", "1px");
                        }
                        $(".fixIndex").addClass("hidden");
                        var $this = $(".currentSelected");
                        $this.removeClass("menuHover");
                        $this.removeClass("menuHover1");



                        $("#menuNormal").html($this.next("div").html()).removeClass("hidden").attr("index", $(".firstMenu").index($this));
                        $("#menuhover").addClass("hidden").attr("index", $(".firstMenu").index($this));

                        var $divs = $(".childMenuTopBar div");
                        $divs.eq(0).html($this.attr("title1"));
                        $divs.eq(1).html($this.attr("u"));
                        $divs.eq(0).attr("source", $this.attr("title1"));
                        $divs.eq(1).attr("source", $this.attr("u"));

                        $("#menuNormal .thirdMemu").each(function () {
                            // console.log($(this).attr("href") + "||" + $("#content").attr("url"));
                            if ($(this).attr("href") == $("#content").attr("url")) {
                                $(this).addClass("selected currentThirdMemuSelected");


                                var $thirdMenus = $(this).parents(".thirdMenus").eq(0);
                                $thirdMenus.removeClass("hidden");
                                $thirdMenus.prev(".secMemu").find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
                                doSecSelected($thirdMenus.prev(".secMemu"), true);
                                return;
                            }
                        });
                    }
                }
                if ($(".currentSelected").attr("id") != "logo") {
                    $("#content").attr("src", $("#content").attr("url"));
                }
            }
        }, false, "text");
    });

    function getChildMenu($firstMenu) {
        $(".firstMenuContent").html($firstMenu.html());
        $(".firstMenuContent div").remove();
        $(".firstMenuContent span").remove();

        var $nextDiv = $firstMenu.next("div");
        var floatMenuHtmlStr = "";
        var $secMenus = $nextDiv.find(".secMemu");
        var tmpSize = $secMenus.size();
        $secMenus.each(function (i, n) {
            var str = $(this).html();
            if (str.indexOf("<span") != -1) {
                str = str.substring(0, str.indexOf("<span"));
            }
            var tmpClass = "borderBottom";
            if (i == (tmpSize - 1)) {
                tmpClass = "";
            }

            floatMenuHtmlStr += "<tr>";
            var iSAStr = $(this).attr("href");
            if (iSAStr.indexOf("void") >= 0) {
                floatMenuHtmlStr += "<td valign='top' class='floatSecMenu " + tmpClass + "'>" + str + "</td>";
            }
            else {
                floatMenuHtmlStr += "<td valign='top' class='floatSecMenu " + tmpClass + "'>" + "<a class='thirdMemu aNormal' target='content' href='" + iSAStr + "'>" + str + "</a></td>";
            }

            floatMenuHtmlStr += "<td class='" + tmpClass + "'>" + $(this).next("div").html() + "<div class='clear'></div></td>";

            floatMenuHtmlStr += "</tr>";
        });

        $(".otherMenuContent table").html(floatMenuHtmlStr);
        //$(".otherMenuContent table .thirdMemuDisplay").removeClass("thirdMemuDisplay");

        $(".otherMenuContent table .thirdMemu").each(function () {
            // console.log($(this).attr("href") + "||" + $("#content").attr("url"));
            if ($(this).attr("href") == $("#content").attr("url")) {
                $(this).addClass("selected currentThirdMemuSelected");
                return;
            }
        });
        $(".otherMenuContent .secMenuSelectedTips").remove();
    }

    var canCloseChildMenu = false;
    var closeChildMenuTimer;
    function delayCloseChildMenu() {
        if (canCloseChildMenu) {
            $("#floatMenu").addClass("hidden");
            resetIco();
            $(".menuHover").removeClass("menuHover");
            $(".menuHover1").removeClass("menuHover1");
        }
    }

    function resetIco(b) {
        /// <summary>
        ///
        /// </summary>
        /// <param name="b">是否切换成黑色图标</param>
        var $firstMenu = null;
        //        if ($(".menuHover").size() > 0) {
        //            $firstMenu = $(".menuHover").eq(0);
        //        }
        //        else
        if ($(".menuHover1").size() > 0) {
            $firstMenu = $(".menuHover1").eq(0);
        }
        if ($firstMenu != null && isFloatMenuStatus) {
            var $firstMenuBg = $firstMenu.find(".firstMenuBg");
            if ($firstMenuBg.size() > 0) {
                var tmpStr2 = "";
                if (b != undefined && b) {
                    tmpStr2 = "1";
                }
                // $firstMenuBg.css("background", "url(Images/firstmenu/" + tmpStr2 + $firstMenu.attr("ico") + ") no-repeat 13px 6px");
            }
        }
    }

    $("#floatMenu").bind({
        mouseenter: function () {
            canCloseChildMenu = false;
            try {
                clearTimeout(closeChildMenuTimer);
            }
            catch (e) {
            }
            var str = $(".firstMenuContent").html();
            $(".firstMenu,#logo").each(function () {
                var tmpStr1 = $(this).html();
                if (tmpStr1.indexOf("<span") != -1) {
                    tmpStr1 = tmpStr1.substring(0, tmpStr1.indexOf("<span"));
                }
                if (tmpStr1 == str) {
                    if (!$(this).hasClass("currentSelected")) {
                        if (isFloatMenuStatus) {
                            $(this).addClass("menuHover1");
                        }
                        else {
                            $(this).addClass("menuHover");
                        }

                        resetIco(true);
                    }
                }
                else {
                    if ($(this).hasClass("menuHover")) {
                        $(this).removeClass("menuHover");
                    }
                    if ($(this).hasClass("menuHover1")) {
                        $(this).removeClass("menuHover1");
                    }
                }
            });
        },
        mouseleave: function () {
            canCloseChildMenu = true;
            delayCloseChildMenu();
        }
    });

    $(".thirdMemu").live({
        mouseenter: function () {
            var $this = $(this);
            if (!$this.hasClass("currentThirdMemuSelected")) {
                $this.addClass("menuHover");
            }
        },
        mouseleave: function () {
            if (!$(this).hasClass("currentThirdMemuSelected")) {
                $(this).removeClass("menuHover");
                $(this).removeClass("menuHover1");
            }
        },
        click: function (e) {
            var $this = $(this);
            isClick = true;

            if ($(".fixTopBarColor").size() == 0) {
                $(".childMenuTopBar").append(" <div class='fixTopBarColor'>&nbsp;</div>");
            }

            var tmpHtml = $this.html().toLowerCase();
            if (tmpHtml.indexOf("<span") != -1) {
                tmpHtml = tmpHtml.substring(0, tmpHtml.indexOf("<span"));
            }
            $(".pagetitle").html($.trim(tmpHtml));
            $(".topBar").removeClass("hidden");
            $("#content").height($(window).height() - 60);


            $this.removeClass("menuHover");
            $this.removeClass("menuHover1");
            $(".currentThirdMemuSelected").removeClass("currentThirdMemuSelected").removeClass("selected");
            $this.addClass("selected").addClass("currentThirdMemuSelected");
            var firstMenuIndex = parseInt($("#menuNormal").attr("index"));
            var isCurrent = $(".firstMenu").eq(firstMenuIndex).hasClass("currentSelected");

            var $firstMenu = $(".currentSelected");
            var $divs = $(".childMenuTopBar div");
            if ($firstMenu.attr("isIndex") == "true") {
                $divs.eq(0).html($firstMenu.attr("u"));
                $divs.eq(1).html($firstMenu.attr("d"));
                $divs.eq(0).attr("source", $firstMenu.attr("u"));
                $divs.eq(1).attr("source", $firstMenu.attr("d"));
            } else {
                $divs.eq(0).html($firstMenu.attr("title1"));
                $divs.eq(1).html($firstMenu.attr("u"));
                $divs.eq(0).attr("source", $firstMenu.attr("title1"));
                $divs.eq(1).attr("source", $firstMenu.attr("u"));
            }
            isCloseMenu = false;

            resize();

            this.blur();

            $("#content").attr("url", $(this).attr("href"));
            if (isFloatMenuStatus) {
                var str = $(".firstMenuContent").html();
                $(".firstMenuSelectedTips").remove();
                $("#floatMenu").addClass("hidden");
                $(".firstMenu,#logo").each(function () {
                    var tmpHtmlStr = $(this).html();
                    if (tmpHtmlStr.indexOf("<span") != -1) {
                        tmpHtmlStr = tmpHtmlStr.substring(0, tmpHtmlStr.indexOf("<span"));
                    }
                    if (tmpHtmlStr == str) {
                        $(this).addClass("selected currentSelected");
                        // $(this).append("<span class='firstMenuSelectedTips' style='top:9px;'><img src='Images/ArrowLeft.png'></span>");
                    }
                    else {
                        if ($(this).hasClass("selected")) {
                            $(this).removeClass("selected");
                        }
                        if ($(this).hasClass("currentSelected")) {
                            $(this).removeClass("currentSelected");
                        }
                    }
                });
            }
        }
    });



    $("#btnGoIndex,#btnGoIndex1,#btnHome").click(function () {
        $("#childMenu").css("border-right-width", "0px");
        $('.pagetitle').html('我的桌面');
        if (!$("#setting").hasClass("hidden")) {
            $("#setting").addClass("hidden");
        }
        if (!$("#orgList").hasClass("hidden")) {
            $("#orgList").addClass("hidden");
        }


        $(".currentSelected").removeClass("currentSelected").removeClass("selected");
        $(".firstMenuSelectedTips,.fixTopBarColor").remove();

        // $("#logo").addClass("currentSelected").addClass("selected").append("<span class='firstMenuSelectedTips'><img src='Images/ArrowLeft.png' /></span>");

        $("#menuNormal").html($(this).next("div").html());

        var $menus1 = $("#menuNormal .secMemu");
        if ($menus1.size()) {
            var $menu1 = $menus1.eq(0);
            doSecSelected($menus1, true);
            $menu1.find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
            $menu1.next(".thirdMenus").removeClass("hidden");
        }

        var $divs = $(".childMenuTopBar div");
        $divs.eq(0).html($(this).attr("u"));
        $divs.eq(1).html($(this).attr("d"));
        $(".firstMenuSelectedTips").css({ "top": "22px" });

        GetIndexMenu(true, false, true);

        $("#settingForHover,#langList").addClass("hidden");
    });

    var $animateMenu;
    $(".secMemu").live({
        click: function (e) {
            $(".childMenuTopBar,#menuNormal").removeClass("hidden");
            $(".secMemuBg1").removeClass("secMemuBg1");
            var $this3 = $(this);
            if ($this3.find("img").attr("src")) {
                var str = $this3.find("img").attr("src");
                if (str.indexOf("ArrowRight") != -1) {
                    $(".secMemu").each(function () {
                        $(this).find("span").html("<img class='arrowRight' src='Images/ArrowRight.png' />");
                    });
                    $(".secMemuBg").removeClass("secMemuBg");
                    doSecSelected($this3, true);
                    $(".thirdMenus").addClass("hidden");
                    //if ($this3.parent().find(".thirdMenus").size() > 0) {
                    //    $this3.addClass("secMemuBg").find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
                    //}
                    //else {
                    //   // $this3.addClass("secMemuBg").find("span").html("<img class='arrowRight' src='Images/ArrowRight.png' />");
                    //    $this3.addClass("secMemuBg").addClass("secMemuBg1");
                    //  //  alert("ffd");
                    //}

                    if ($this3.next().hasClass("thirdMenus")) {
                        $this3.addClass("secMemuBg").find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
                    }
                    else {
                        $this3.addClass("secMemuBg").addClass("secMemuBg1");
                        var tmpHtml = $this3.html().toLowerCase();
                        if (tmpHtml.indexOf("<span") != -1) {
                            tmpHtml = tmpHtml.substring(0, tmpHtml.indexOf("<span"));
                        }
                        $(".pagetitle").html($.trim(tmpHtml));

                    }

                    var $thirdMenus = $this3.next(".thirdMenus");
                    $animateMenu = $thirdMenus;
                    $thirdMenus.removeClass("hidden");
                    $('#menuhover,#menuNormal').getNiceScroll().onResize();
                    fixScrollbarLeft();

                } else {
                    //                doSecSelected($this3, false);
                    //                $this3.find("span").html("<img class='arrowRight' src='Images/ArrowRight.png' />");
                    //                $this3.next(".thirdMenus").addClass("hidden");
                    //                $('#menuhover,#menuNormal').getNiceScroll().onResize();
                }

                //            var str = $this3.find("img").attr("src");
                //            if (str.indexOf("ArrowRight") != -1) {
                //                doSecSelected($this3, true);
                //                $this3.find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
                //                var $thirdMenus = $this3.next(".thirdMenus");
                //                $animateMenu = $thirdMenus;
                //                $thirdMenus.removeClass("hidden");
                //                $('#menuhover,#menuNormal').getNiceScroll().onResize();
                //
                //            } else {
                //                doSecSelected($this3, false);
                //                $this3.find("span").html("<img class='arrowRight' src='Images/ArrowRight.png' />");
                //                $this3.next(".thirdMenus").addClass("hidden");
                //                $('#menuhover,#menuNormal').getNiceScroll().onResize();
                //            }
                this.blur();

                e.stopPropagation();
            }
            else {
                $(".secMemu").each(function () {
                    $(this).find("span").html("<img class='arrowRight' src='Images/ArrowRight.png' />");
                });
                $(".secMemuBg").removeClass("secMemuBg");
                doSecSelected($this3, true);
                $(".currentThirdMemuSelected").removeClass("currentThirdMemuSelected").removeClass("selected");
                $(".thirdMenus").addClass("hidden");

                $this3.addClass("secMemuBg").addClass("secMemuBg1");

                var tmpHtml = $this3.html().toLowerCase();
                if (tmpHtml.indexOf("<span") != -1) {
                    tmpHtml = tmpHtml.substring(0, tmpHtml.indexOf("<span"));
                }
                $(".pagetitle").html($.trim(tmpHtml));
            }
        },
        mousedown: function () {
            if ($.browser.msie && $.browser.version == 10) {
                $(this).css("background-color", "white");
            }
        },
        mouseenter: function () {
            doSecSelected($(this), true);
        },
        mouseleave: function () {
            if (!$(this).hasClass("secMemuBg1")) {
                if ($(this).find("img").attr("src")) {
                    var str = $(this).find("img").attr("src");
                    if (str.indexOf("ArrowRight") != -1) {
                        doSecSelected($(this), false);
                    }
                }
                else {
                    if ($(this).hasClass("secMemuBg")) {
                        doSecSelected($(this), false);
                    }
                }
            }
        }
    });

    if ($("#menuNormal .secMemu").size() == 1) {
        var $menu = $("#menuNormal .secMemu").eq(0);
        doSecSelected($menu, true);
        $menu.find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
        $menu.next(".thirdMenus").removeClass("hidden");
    }

    //提醒有更新
    function animateupdate() {
        if ($(".sex").attr("src").indexOf("1.png") != -1) {
            $(".sex").attr("src", "/Images/update2.png");
        }
        else {
            $(".sex").attr("src", "/Images/update1.png");
        }
    }

    function clearTimerForUpdate() {
        try {
            clearInterval(animateupdateid);
        }
        catch (e) {
        }
        animateupdateid = null;
    }

    function setTimerForUpdate() {
        animateupdateid = setInterval(animateupdate, 800);
    }
    var animateupdateid;
    if ($(".sex").attr("hasupdate") == "true") {
        setTimerForUpdate();
    }

    //更新提示框
    var delayCloseUpdateId;
    function delayCloseUpdateDiv() {
        $("#hasUpdate,#settingForHover").addClass("hidden");
        $(".settingNormal2").addClass("hidden");
    }

    $("#hasUpdate,#settingForHover").bind({
        mouseenter: function () {
            try {
                clearTimeout(delayCloseUpdateId);
            }
            catch (e) {
            }
            if (!$("#logo").hasClass("currentSelected")) {
                if (isFloatMenuStatus) {
                    $("#logo").addClass("menuHover1");
                }
                else {
                    $("#logo").addClass("menuHover");
                }
            }
        },
        mouseleave: function () {
            if ($(this).attr("id") == "settingForHover") {
                $("#hasUpdate").addClass("hidden");
                if ($("#langList").hasClass("hidden")) {
                    $("#settingForHover").addClass("hidden");
                }
            }
            else {
                $("#hasUpdate,#settingForHover").addClass("hidden");
            }
            $(".settingNormal2").addClass("hidden");
            if ($(this).attr("id") == "hasUpdate") {
                setTimerForUpdate();
            }
            $("#logo").removeClass("menuHover1");
            $("#logo").removeClass("menuHover");
        }
    });

    $(".firstMenu,#logo").bind({
        mouseenter: function (e) {

            if ($("#hasUpdate").attr("hasupdate") == "true") {
                if ($(this).attr("id") == "logo") {
                    $("#hasUpdate").removeClass("hidden");
                    clearTimerForUpdate();
                    $(".sex").attr("src", "/Images/updatehover.png");
                }
                else {
                    $("#hasUpdate").addClass("hidden");
                    if (animateupdateid == undefined || animateupdateid == null) {
                        setTimerForUpdate();
                    }
                }
            }
            else if ($(this).attr("id") == "logo") {
                $("#settingForHover").removeClass("hidden");
                $(".settingNormal2").addClass("hidden");
            }

            if ($(this).attr("id") != "logo") {
                $("#settingForHover").addClass("hidden");
            }

            if (isFloatMenuStatus && $(this).attr("id") != "logo") {
                $("#settingForHover").addClass("hidden");
                $("#floatMenu").removeClass("hidden");
                canCloseChildMenu = false;
                try {
                    clearTimeout(closeChildMenuTimer);
                }
                catch (e) {
                }
                getChildMenu($(this));
                //console.log($("#floatMenu").height());

                var tmpTop = 60;
                var tmpFloatMenuHeight = $("#floatMenu").height();
                var firstMenuBottomTop = $(this).offset().top + $(this).height() + 8 * 2;
                tmpTop = firstMenuBottomTop - tmpFloatMenuHeight / 2;//floatMenu居中
                if (tmpTop < 60) {
                    tmpTop = 60;
                } else if ((tmpTop + tmpFloatMenuHeight) > $(window).height()) {
                    tmpTop = $(window).height() - tmpFloatMenuHeight;
                }
                if (tmpFloatMenuHeight < 110) {//修正只有1行菜单时错位 使浮动菜单底部与当前一级菜单底部在一条线上
                    tmpTop = firstMenuBottomTop - tmpFloatMenuHeight - 1;
                }

                $("#floatMenu").css("top", tmpTop + "px");
            }
            else {
                $("#floatMenu").addClass("hidden");
                canCloseChildMenu = false;
                try {
                    clearTimeout(closeChildMenuTimer);
                }
                catch (e) {
                }

            }

            if (!$(this).hasClass("currentSelected")) {
                if (isFloatMenuStatus) {
                    $(this).addClass("menuHover1");
                }
                else {
                    $(this).addClass("menuHover");
                }
                resetIco(true);
            }
            try {
                clearTimeout(delayCloseUpdateId);
            }
            catch (e) {

            }
        },
        mouseleave: function () {
            resetIco();
            $(this).removeClass("menuHover");
            $(this).removeClass("menuHover1");
            if ($(this).attr("id") == "logo") {
                delayCloseUpdateId = setTimeout(delayCloseUpdateDiv, 100);
            }
            else if ($("#hasUpdate").attr("hasupdate") == "true") {
                if (animateupdateid == undefined || animateupdateid == null) {
                    setTimerForUpdate();
                }
            }

            if (isFloatMenuStatus) {
                if ($(this).attr("id") != "logo") {
                    canCloseChildMenu = true;
                    closeChildMenuTimer = setTimeout(delayCloseChildMenu, 100);
                }
                else {
                    delayCloseChildMenu();
                }
            }
        },
        click: function (e) {
            var $this = $(this);
            clearSelected();


            if (!isFloatMenuStatus) {

                $(".childMenuTopBar,#menuNormal").removeClass("hidden");
                $("#childMenu").css("border-right-width", "2px");
                if (($.browser.msie && $.browser.version <= 8.0) || $.browser.webkit) {
                    $("#childMenu").css("border-right-width", "1px");
                }
                $(".fixIndex").addClass("hidden");
                $this.removeClass("menuHover");
                $this.removeClass("menuHover1");
            }
            if (!isFloatMenuStatus || $(this).attr("id") == "logo") {
                $(".firstMenu,#logo").each(function () {
                    if ($(this).hasClass("selected")) {
                        $(this).removeClass("selected");
                    }
                    if ($(this).hasClass("currentSelected")) {
                        $(this).removeClass("currentSelected");
                    }
                });
                $(".firstMenuSelectedTips").remove();
                $this.addClass("selected").addClass("currentSelected");
            }


            if ($this.attr("isindex") == "true") {
                //                if ($(this).attr("id") == "logo") {
                //                    $("#floatMenu").removeClass("hidden");
                //                    return;
                //                }
                GetIndexMenu(isFloatMenuStatus, true);
                e.stopPropagation();
            } else {
                if (isFloatMenuStatus) {
                    return;
                }
                $("#menuNormal").html($this.next("div").html()).removeClass("hidden").attr("index", $(".firstMenu").index($this));
                $("#menuhover").addClass("hidden").attr("index", $(".firstMenu").index($this));

                var $nextMenu = $("#menuNormal").children().eq(0);

                if (!$nextMenu.next().hasClass("thirdMenus")) {
                    doSecSelected($nextMenu, true);
                    $nextMenu.addClass("secMemuBg1");
                }
                else {


                    var $thirdMenu = $("#menuNormal .thirdMemu").eq(0);
                    //$thirdMenu.addClass("selected").addClass("currentThirdMemuSelected");
                    var $thirdMenus = $thirdMenu.parents(".thirdMenus").eq(0);
                    $thirdMenus.removeClass("hidden");
                    $thirdMenus.prev(".secMemu").find("span").html("<img class='arrowDown' src='Images/ArrowDown.png' />");
                    doSecSelected($thirdMenus.prev(".secMemu"), true);
                }
                //$("#content").attr("src", $thirdMenu.attr("href"));
                //$("#content").attr("src", $this.attr("i"));
                //$("#content").attr("url", $this.attr("i"));
                var iurl = $(this).attr("url1");
                if (iurl != undefined && iurl.length > 5) {
                    $("#content").attr("src", iurl);
                    $("#content").attr("url", iurl);
                }
                //var tmpHtml = $thirdMenu.html().toLowerCase();
                var tmpHtml = $this.html().toLowerCase();
                if (tmpHtml.indexOf("<span") != -1) {
                    tmpHtml = tmpHtml.substring(0, tmpHtml.indexOf("<span"));
                }
                $(".pagetitle").html($.trim(tmpHtml));

                var $divs = $(".childMenuTopBar div");
                $divs.eq(0).html($this.attr("title1"));
                $divs.eq(1).html($this.attr("u"));
                $divs.eq(0).attr("source", $this.attr("title1"));
                $divs.eq(1).attr("source", $this.attr("u"));
                //}
            }

            this.blur();

            resize();

            $("#menuNormal .secMemu").each(function () {
                if (!$(this).next("div").hasClass("thirdMenus") || $(this).next("div").find(".thirdMemu").size() == 0) {
                    $(this).find(".secMenuSelectedTips").remove();
                }
            });

            var $secMenu = $("#menuNormal .secMemu");
            if ($secMenu.size() == 0) {
                $("#childMenu").addClass("hidden");
            } else {
                $("#childMenu").removeClass("hidden");
            }
        }
    });

    var tmpStr = "";
    $("#childMenu").bind({
        mouseenter: function () {
            isCloseMenu = false;
            isClick = false;
            tmpStr = $(".currentSelected").html();
        },
        mouseleave: function () {
            isCloseMenu = true;
            if (!isClick) {
                closeChildMenu();
            }

        }
    });

    //修正鼠标指针无变化
    $("#setting input,#settingForHover input").bind({
        mouseenter: function () { $(this).css({ "cursor": "pointer" }); },
        mouseleave: function () { $(this).css({ "cursor": "default" }); }
    });

    //start
    var isClick = false;

    $(".topBar a").bind({
        mouseenter: function () {
            //  $(".fixTop", this).addClass("hidden");
            //  $(".fixWidth", this).addClass("hidden");
            var $this = $(this);
            var isWebIM = $(this).attr("id") == "webIM";
            //stopTimer();
            if (!isClick) {
                $(this).find("img").eq(0).before("<div>&nbsp;</div>");

                if ($.browser.msie && $.browser.version <= 7) {
                    $(this).attr("title", $(this).find(".fixTop").html());
                } else {
                    var width = "50";
                    var tmpWidth = $(".fixWidth", this).attr("fixwidth");
                    if (tmpWidth != undefined && tmpWidth.length > 1) {
                        width = tmpWidth;
                    }
                    $this.find(".fixTop").removeClass("hidden").css({ "opacity": "0" }).animate({ "opacity": "1" }, isWebIM ? 1000 : 500);
                    $(".fixWidth", this).width(0).removeClass("hidden").addClass("displayblock").animate({ "width": isWebIM ? "180" : width }, 500, function () {
                        if (isWebIM) {
                            $(".task").each(function () {
                                if ($(this).html().indexOf("用户") != -1) {
                                    $(this).remove();
                                }
                            });
                        }
                        if ($this.find(".fixTop").hasClass("hidden")) {
                            $this.find(".fixTop").removeClass("hidden").animate({ "opacity": "1" }, 300);
                        }

                        if (isWebIM && $(".task_container div").size() > 0) {
                            $(".taskbar,.task_container").css("display", "block").width(210).height($(".task_container div").size() * 30);
                            $(".taskbar").width(218).css({ "left": ($(window).width() - 220) + "px", "top": "60px" });
                        }
                    });
                }
            }
        },
        mouseleave: function () {
            var $this;
            //stopTimer();
            if ($.browser.msie && $.browser.version <= 7) {
                if (!isClick) {
                    $(this).find("div").remove();
                }
            } else {
                if (!isClick) {
                    $(".fixTop", this).stop();
                    $(".fixWidth", this).stop();
                    $(this).find("div").remove();
                    $(".fixTop", this).animate({ "opacity": "0" }, 100, function () {
                        $(".fixTop").addClass("hidden");
                    });
                    $(".fixWidth", this).removeClass("displayblock").animate({ "width": "0" }, 300, function () {
                        $(".fixWidth").addClass("hidden");
                    });;
                }
            }
        },
        click: function (e) {
            $(".taskbar,.task_container").css("display", "none");

            if (!$("#setting").hasClass("hidden") || $("#friendList").css("display") == "block") {
                $("#setting").addClass("hidden");
                $("#friendList").css({ "display": "none" });


                $(".fixTop").animate({ "opacity": "0" }, 300, function () {
                    $(".fixTop").addClass("hidden");
                });
                $(".fixWidth").removeClass("displayblock").animate({ "width": "0" }, 300, function () {
                    $(".fixWidth").addClass("hidden");
                });;
                onSettingState(false);
                return;
            }


            isClick = true;
            var id = $(this).attr("id");
            $("#orgList").addClass("hidden");
            switch (id) {
                case "btnInfo":
                    $("#orgList").addClass("hidden");
                    onSettingState($("#setting").hasClass("hidden"));
                    if ($("#setting").hasClass("hidden")) {
                        $("#setting").removeClass("hidden");
                    } else {
                        $("#setting").addClass("hidden");
                    }
                    $("#setting").css("right", ($(window).width() - $(this).offset().left - 90) + "px");
                    $("#btnSelectOrg div").remove();
                    break;
                case "btnNotice":
                    $("#orgList").addClass("hidden");
                    onSettingState($("#noticeList").hasClass("hidden"));
                    if ($("#noticeList").hasClass("hidden")) {
                        $("#noticeList").removeClass("hidden");
                    } else {
                        $("#noticeList").addClass("hidden");
                    }
                    $("#noticeList").css("right", ($(window).width() - $(this).offset().left - 90) + "px");
                    $("#btnSelectOrg div").remove();
                    break;
                case "webIM":
                    var tmpHeight = $("#friendList").height();
                    // $("#friendList").show();
                    //alert($("#friendList").css("display"));
                    $("#friendList").height(0).css({ "display": "block" }).animate({ "height": tmpHeight }, 200);

                    $("#btnSelectOrg div").remove();
                    break;
                case "btnSelectOrg":
                    if ($("#orgList").hasClass("hidden")) {
                        var fixIE8 = 0;
                        if ($.browser.msie && parseInt($.browser.version) == 8) {
                            fixIE8 = -2;
                        }
                        $("#orgList").removeClass("hidden").css("right", $(window).width() - $(this).offset().left - $(this).width() -1 + fixIE8 + "px");
                    } else {
                        $("#orgList").addClass("hidden");
                    }
                    break;
                default:
                    $("#btnSelectOrg div").remove();
            }
            this.blur();
            e.stopPropagation();
        }
    });


    $("#setting,#settingForHover,#noticeList").click(function (e) {
        e.stopPropagation();
    });
    $("body").click(function () {
        hideShortCutMenu();
        isClick = false;
        $(".topBar a div").remove();

        $("#setting,#orgList  ,#settingForHover,#langList").addClass("hidden");

        $("#friendList").css({ "display": "none" });


        $(".fixTop").animate({ "opacity": "0" }, 300, function () {
            $(".fixTop").addClass("hidden");
        });
        $(".fixWidth").removeClass("displayblock").animate({ "width": "0" }, 300, function () {
            $(".fixWidth").addClass("hidden");
        });;

        onSettingState(false);
    });
    //end

    $("#ToSchool").change(function () {
        $.ajaxMethod("ToSchool", { "SchoolGuid": $(this).val() }, function (result) {
            if (result == "1") {
                window.location.href = window.location.href;
            } else {
                alert("切换园区失败");
            }
        }, false, "text");
    });
    $(".btnChangeSkin").click(function () {
        $.ajaxMethod("ChangeSkin", { "Skin": $(this).attr("skin"), "Color": $(this).attr("color"), "Folder": $(this).attr("folder") }, function (result) {
            if (result == "1") {
                window.location.href = window.location.href;
            } else {
                alert("换肤失败");
            }
        }, false, "text");
    });

    $("#btnNewVersion,#btnViewUpdate").click(function () {
        $("#setting,#hasUpdate  ,#settingForHover,#langList").addClass("hidden");
        showDialog("版本更新", "/k_system/update.aspx?navId=2d2a9b34-ad27-44cc-ba74-79840c44a142&type=1", 800, 500);
    });
    $("#content").on("load", function () {
        $(".d a").addClass("changeinfo");
        isClick = false;
        if ($(".flexigrid", window.content.document).size() == 0) {
            var $list = $(".list", window.content.document);
            if ($list.size() > 0) {
                $list.find("tbody tr:odd").each(function () {
                    $(this).find("td").each(function () {
                        if ($(this).attr("alt") != "except") $(this).css({ "background-color": "#F8F8F8" });
                    });
                });
                $list.find("tbody tr").live({
                    mouseenter: function () {
                        $(this).attr("bgg", $(this).find("td").eq(0).css("background-color"));
                        $(this).find("td").css({ "background-color": "#FFFCD3" });
                    },
                    mouseleave: function () {
                        $(this).find("td").css({ "background-color": $(this).attr("bgg") });
                    }
                });
            }
        }


        $("body", window.content.document).click(function () {
            $(".topBar a div").remove();
            $("#notice_currentindex").html("0");
            $("#setting  ,#settingForHover,#langList,#orgList,#noticeList").addClass("hidden");
            $("#friendList").css({ "display": "none" });
            $(".taskbar,.task_container").css("display", "none");
            isClick = false;
            $(".fixTop").animate({ "opacity": "0" }, 300, function () {
                $(".fixTop").addClass("hidden");
            });
            $(".fixWidth").removeClass("displayblock").animate({ "width": "0" }, 300, function () {
                $(".fixWidth").addClass("hidden");
            });;
        });

        var $buttons = $(".button", window.content.document);
        if ($buttons.size() > 0) {
            if ($.browser.msie && $.browser.version <= 7) {
                $buttons.each(function () {
                    if ($(this).attr("type") != undefined && ($(this).attr("type") == "button" || $(this).attr("type") == "submit")) {
                        $(this).addClass("buttonFixHeight");
                    }
                });
            }

            $buttons.bind({
                mouseenter: function () { $(this).css({ "cursor": "pointer" }).removeClass("button").addClass("buttonHover"); },
                mouseleave: function () { $(this).css({ "cursor": "default" }).removeClass("buttonHover").addClass("button"); },
                click: function () { this.blur(); }
            });
        }


        var $buttonList = $(".buttonList", window.content.document);
        if ($buttonList.size() > 0) {

            $buttonList.find("td").bind({
                mouseenter: function () {
                    var $this = $(this);
                    $buttonList.find(".buttonSelect").each(function () {
                        $(this).removeClass("buttonSelect");
                    });
                    $this.addClass("buttonSelect").css({ "cursor": "pointer" });
                },
                mouseleave: function () {
                    $(this).removeClass("buttonSelect").css({ "cursor": "default" });
                }
            });
            $buttonList.find("input").bind({
                mouseenter: function () { $(this).css({ "cursor": "pointer" }); },
                mouseleave: function () { $(this).css({ "cursor": "default" }); }
            });
            $buttonList.bind({
                mouseleave: function () {
                    $(this).find(".buttonSelect").each(function () {
                        $(this).removeClass("buttonSelect");
                    });
                }
            });
        }

        var $lists = $(".list", window.content.document);
        $lists.each(function () {
            $(this).find("thead div").each(function () {
                if ($(this).attr("sort") == "true") {
                    $(this).find("input").bind({
                        mouseenter: function () { $(this).css({ "cursor": "pointer" }); },
                        mouseleave: function () { $(this).css({ "cursor": "default" }); }
                    });
                    $(this).parent().bind({
                        mouseenter: function () { $(this).css({ "cursor": "pointer" }); },
                        mouseleave: function () { $(this).css({ "cursor": "default" }); }
                    });
                }
            });
        });

        resize(true);
        if ($.browser.msie && $.browser.version == 6) {
            //   alert(1);
            //  $("#content").width($(window).width() - 200);
        }
        $("#content").height($(window).height() - 60);
        //        if ($("#content").attr("src").toLocaleLowerCase().indexOf("myindex.aspx") != -1 && $("#content").attr("src").toLocaleLowerCase().indexOf("page=index") != -1) {
        //            alert(3);
        //            $("#content").height($(window).height() + 60);
        //        }
    });
});
jQuery.easing["jswing"] = jQuery.easing["swing"]; jQuery.extend(jQuery.easing, { def: "easeOutQuad", easeOutBounce: function (a, b, c, d, e) { if ((b /= e) < 1 / 2.75) { return d * 7.5625 * b * b + c; } else if (b < 2 / 2.75) { return d * (7.5625 * (b -= 1.5 / 2.75) * b + .75) + c } else if (b < 2.5 / 2.75) { return d * (7.5625 * (b -= 2.25 / 2.75) * b + .9375) + c } else { return d * (7.5625 * (b -= 2.625 / 2.75) * b + .984375) + c } } })