
$.fn.extend({
    etmTabs: function (p) {
        p = $.extend({
            item: {},//直接跳转 Url    Selected Text  ；不刷新页面   Selector  Selected Text
            SearchUrl: "",
            AdvSearchUrl: "",//必须完整路径 除域名
            USeSearchOptions: false,
            RefreshPage: true,//区分 连接 的与 隐藏元素,
            Align: "left",
            RadioItem:{}
        }, p);
        return this.each(function () {
            var hostUrl = "";
            $(this).addClass("hidden").after("<div class='tabs'></div>");
            var $tabs = $(this).next(".tabs");
            $tabs.append("<div class='tabsBottomBar'>&nbsp;</div>");
            if (p.item.length > 0) {
                var array = new Array();
                for (var i = 0; i < p.item.length; i++) {
                    var htmlStr = "";
                    if (p.RefreshPage) {
                        htmlStr = "<a href='" + hostUrl + p.item[i].Url + "' ";
                    } else {
                        array.push(p.item[i].Selector);
                        htmlStr = "<a href='javascript:void(0);' to='" + p.item[i].Selector + "' ";
                    }
                    if (i == 0) {
                        htmlStr += " style='margin-left:10px;' ";
                    }
                    if (p.item[i].Selected) {
                        htmlStr += " class='tabSelected currentTabSelected' ";
                    } else if (!p.RefreshPage) {
                         $(p.item[i].Selector).addClass("hidden");
                    }
                    htmlStr += ">" + p.item[i].Text + "</a>";
                    if (p.item.length - 1 > i) {
                        htmlStr += "|";
                    }
                    $tabs.append(htmlStr);
                }
                $tabs.find("a").addClass("tabItem");
                $tabs.append("<div class='selectedbar'>&nbsp;</div>");
                $tabs.css("text-align", p.Align);

                if ($.browser.msie && $.browser.version == 6) {

                }

                $tabs.find(".tabItem").bind({
                    mouseenter: function () {
                        $tabs.find(".selectedbar").stop();
                        var itemWidth = $(this).width() + 20;
                        var thisLeft = $(this).offset().left;//-10;
                        $tabs.find(".selectedbar").animate({ "left": thisLeft + "px", "width": itemWidth + "px" }, 600, function () { });
                    },
                    mouseleave: function () {
                        var $tabSelected1 = $tabs.find(".tabSelected");
                        $tabs.find(".selectedbar").stop();
                        var itemWidth = $tabSelected1.width() + 20;
                        var thisLeft = $tabSelected1.offset().left;// - 10;
                        $tabs.find(".selectedbar").animate({ "left": thisLeft + "px", "width": itemWidth + "px" }, 300, function () { });
                    },
                    click:function() {
                        if (!p.RefreshPage) {
                            var $this = $(this);
                            $(array.join(",")).addClass("hidden");
                            $($this.attr("to")).removeClass("hidden");

                            $tabs.find(".selectedbar").stop();
                            var itemWidth = $this.width() + 20;
                            var thisLeft = $this.offset().left;//-10;
                            $tabs.find(".tabSelected").removeClass("tabSelected");
                            $this.addClass("tabSelected");
                            $tabs.find(".selectedbar").animate({ "left": thisLeft + "px", "width": itemWidth + "px" }, 100, function () { });
                            if (typeof ReSizeScrollbar != "undefined") {
                                ReSizeScrollbar();
                            }

                            if (typeof afterTabItemSelected != "undefined") {
                                afterTabItemSelected();
                            }
                        }
                        this.blur();
                    }
                });
            }

            if (p.USeSearchOptions) {
                var htmlStrOption = "<div class='searchOption'>";
                //console.log(location.href.toLocaleLowerCase());
                //console.log(p.SearchUrl.toLocaleLowerCase());
                if (location.href.toLocaleLowerCase().indexOf(p.SearchUrl.toLocaleLowerCase()) != -1) {
                    htmlStrOption += "<a href='" + p.SearchUrl + "'>普通搜索<img src='" + hostUrl + "/images/r1.png'/></a>";
                } else {
                    htmlStrOption += "<a href='" + p.SearchUrl + "'>普通搜索<img src='" + hostUrl + "/images/r2.png'/></a>";
                }
                if (location.href.toLocaleLowerCase().indexOf(p.AdvSearchUrl.toLocaleLowerCase()) != -1) {
                    htmlStrOption += "<a href='" + p.AdvSearchUrl + "'>高级搜索<img src='" + hostUrl + "/images/r1.png'/></a>";
                } else {
                    htmlStrOption += "<a href='" + p.AdvSearchUrl + "'>高级搜索<img src='" + hostUrl + "/images/r2.png'/></a>";
                }
                htmlStrOption += "</div>";
                $tabs.append(htmlStrOption);
            } else if (p.RadioItem != null && p.RadioItem.length>0) {
                var htmlStrOption1 = "<div class='searchOption'>";
                for (var j = 0; j < p.RadioItem.length; j++) {
                    if (p.RadioItem[j].Selected) {
                        htmlStrOption1 += "<a href='" + p.RadioItem[j].Url + "'>" + p.RadioItem[j].Text;
                        // + "<img class='opImg' src='" + hostUrl + "/images/r1.png'/></a>";
                    } else {
                        htmlStrOption1 += "<a href='" + p.RadioItem[j].Url + "'>" + p.RadioItem[j].Text ;
                        //+ "<img class='opImg'  src='" + hostUrl + "/images/r2.png'/></a>";
                    }
                   // console.log(p.RadioItem[j].Url);
                   //请求正确之后 就会 生效
                }
                htmlStrOption1 += "</div>";
                $tabs.append(htmlStrOption1);
                $tabs.find(".opImg").each(function() {
                    $(this).css("left", ($(this).parent().width() - 16) / 2 + "px");
                });
            }

            var $tabSelected = $tabs.find(".tabSelected");
            if ($tabSelected.size() > 0) {
                $tabs.find(".selectedbar").width($tabSelected.width() + 20).css({ "left": ($tabSelected.offset().left) + "px" });
            }
        });
    }
});
