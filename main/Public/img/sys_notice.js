var intervalID;

//提醒消息
function getNoticeData(type) {
    var pageindex = parseInt($("#notice_currentindex").html());
    if (pageindex != 0) {
        if (type == "next") {
            if (parseInt($("#notice_totalPage").html()) <= parseInt($("#notice_currentindex").html())) {
                return false;
            }
            else
                pageindex = pageindex + 1;
        }
        else {
            if (parseInt($("#notice_currentindex").html()) <= 1) {
                return false;
            }
            else
                pageindex = pageindex - 1;
        }
    }
    else
        pageindex = pageindex + 1;

    $.ajaxMethod("MainNoticeList", { "currentindex": $("#span_currentindex").html(), "pageindex": pageindex }, function (result) {
        jsonResult = $.parseJSON(result);
        $("#table_NoticeList").html(jsonResult.strhtml);
        $("#notice_currentindex").html(jsonResult.currentindex);
        $("#notice_totalPage").html(jsonResult.pagecount);
        if ($("#table_NoticeList").height() > 200)
            $("#table_NoticeList").parent().height($("#table_NoticeList").height());
        clearInterval($("#notice_img").attr("flickered"));
        $("#notice_img").attr("flickered", "0");
        $("#notice_img").attr("src", $("#notice_img").attr("img1"));
    }, false, "text");
}

//循环取提醒
function getNoReaded() {
    if ($("#notice_img").attr("flickered") == "0") {
        $.ajaxMethod("MainGetNoReaded", {}, function (result) {
            var count = parseInt(result);
            if (count > 0) {
                //闪烁图标
                intervalID = setInterval("noticeShow()", 500);
                $("#notice_img").attr("flickered", intervalID);
            }
        }, false, "text");
    }
}
getNoReaded();
setInterval("getNoReaded()", 300000);


//闪烁
function noticeShow()
{
    if ($("#notice_img").attr("src") == $("#notice_img").attr("img1")) {
        $("#notice_img").attr("src", $("#notice_img").attr("img2"));
    }
    else {
        $("#notice_img").attr("src", $("#notice_img").attr("img1"));
    }
}
 