
$.fn.extend({
    etmLinks: function(p) {
        p = $.extend({
            item: {}
        }, p);
        return this.each(function() {
            $(this).html("<div class='etmlinks etmlinksNormalBg'>快速筛选：</div>");
            var $links = $(this).find(".etmlinks");
            $links.hover(function () {
                $(this).addClass("etmlinksHoverBg").removeClass("etmlinksNormalBg");
            }, function () {
                $(this).addClass("etmlinksNormalBg").removeClass("etmlinksHoverBg");
            });
            if (p.item.length > 0) {
                for (var i = 0; i < p.item.length; i++) {
                    var text = p.item[i].Text;
                    if (text != null && text != undefined && text != "") {
                        var htmlStr = "<a ";
                        if (p.item[i].Selected) {
                            htmlStr += "class='etmlink'";
                        }
                        htmlStr += " href='";
                        if (p.item[i].Url != null && p.item[i].Url != undefined && p.item[i].Url != "") {
                            htmlStr += p.item[i].Url;
                        } else {
                            htmlStr += "javascript:void(0)";
                        }
                        htmlStr += "' >" + text + "</a>";
                        if (i < (p.item.length-1)) {
                            htmlStr += "<span>|</span>";
                        }
                        $links.append(htmlStr);
                    }
                    $links.find("a").bind({
                        click: function () {
                            var $this = $(this);
                            $(".etmlink").removeClass("etmlink");
                            $this.addClass("etmlink");
                            this.blur();
                        }
                    });
                }
            }
        });
    }
});
