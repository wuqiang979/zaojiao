
//不能为空

function CheckEmpty(obj, strResult) {
    if ($.trim($("#" + obj).val()) == "") {
        $("#" + strResult).html("不能为空");
        return false;
    }
    else {
        $("#" + strResult).html("");
        return true;
    }
}

//手机号码
function CheckMobile(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str != "" && str.search(/^1\d{10}$/) == -1) {
        $("#" + strResult).html("格式不正确");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}
//固定电话
function CheckPhone(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str!="" && str.search(/^(\d{3,4}-)?\d{7,8}/) == -1) {
        $("#" + strResult).html("格式不正确");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}

//EMail
function CheckEMail(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str != "" && str.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
        $("#" + strResult).html("格式不正确");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}

//数值
function CheckData(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str == "") {
        $("#" + strResult).html("不能为空");
        return false;
    }
    else if (str.search(/^\+?[0-9][0-9]*$/) == -1) {
        $("#" + strResult).html("请输入数值");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}
function CheckFloat(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str == "") {
        $("#" + strResult).html("不能为空");
        return false;
    }
    else if (str.search(/^\d+(\.\d+)?$/) == -1) {
        $("#" + strResult).html("请输入数值");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}

//身份证号码
function CheckIDcard(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str != "" && str.search(/^\d{6}19\d{2}((1[0-2])|0\d)([0-2]\d|30|31)\d{3}[\d|X|x]$/) == -1) {
        $("#" + strResult).html("格式不正确");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}

//日期
function CheckDate(obj, strResult) {
    var str = $.trim($("#" + obj).val());
    if (str != "" && str.search(/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/) == -1) {
        $("#" + strResult).html("格式不正确");
        return false;
    } else {
        $("#" + strResult).html("");
        return true;
    }
}

//键盘只能输入数值和小数
function strNoOnly(evt) {
    evt = (evt) ? evt : ((window.event) ? window.event : "");
    var key = evt.keyCode ? evt.keyCode : evt.which;
    if ((key != 8) && (key != 46) && (key < 48 || key > 57)) {
        if (window.event)
            window.event.returnValue = false;
        else
            evt.preventDefault();
    }
}

//请选择
function CheckSelect(obj,noValue, strResult,strMsg) {
    if ($.trim($("#" + obj).val()) == noValue) {
        $("#" + strResult).html(strMsg);
        return false;
    }
    else {
        $("#" + strResult).html("");
        return true;
    }
}

function SelectChecked(form) {
    for (var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];
        if (e.name != 'selAll' && e.type == 'checkbox')
            e.checked = form.selAll.checked;
    }
}
