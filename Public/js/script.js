/*验证函数*/
function isQq(str) {
    str = $.trim(str);
    var validate = /^[1-9][0-9]{4,9}$/.test(str);
    if (validate) {
        return true;
    }
    return false;
}

function isTeleNum(str) {
    str = $.trim(str);
    var validate = /^(\+86)?(\s)?(13|14|15|17|18)\d{9}$/.test(str);
    if (validate) {
        return true;
    }
    return false;
}

function isEmail(str) {
    str = $.trim(str);
    var validate = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(str);
    if (validate) {
        return true;
    }
    return false;
}
/*验证函数 END*/

/*表单验证结果处理函数*/
function errorTip(element) {
    $(element).siblings('.form-control-feedback').attr("class","glyphicon glyphicon-remove form-control-feedback").closest('.has-feedback').addClass('has-error');
    $(element).data("errored",true);
    $(element).focus();
}

function defaultTip(element) {
    return $(element).siblings('.form-control-feedback').attr("class", 'glyphicon form-control-feedback').closest('.has-feedback').removeClass('has-error has-warning');
}

function warningTip(element) {
    $(element).siblings('.form-control-feedback').attr("class","glyphicon glyphicon-warning-sign form-control-feedback").closest('.has-feedback').removeClass('has-error has-success').addClass('has-warning');
    $(element).focus();
}
/*表单验证结果处理函数*/

function requestData(formJq,extraObj){
	var arr=formJq.serializeArray();
	var data={};
	for(var i in arr){
		var name=arr[i].name;
		var value=arr[i].value;
		data[name]=value;
	}
	for(var j in extraObj){
		data[j]=extraObj[j];
	}
	return data;
}

//模拟长按事件
function longTouch(jqObj, fn) {
    var isMousedown = false;
    jqObj.each(function(index,el){
    	$(el).on("mousedown", function() {
    	    isMousedown = true;
    	    var _this = this;
    	    setTimeout(function() {
    	        if (isMousedown) {
    	            fn.call(_this);
    	        }
    	    }, 100);
    	});
	    $(el).on("mouseup", function() {
	        isMousedown = false;
	    });
    });
    
}
//模拟长按事件 END

/*登录页面index*/
function indexJs() {
    $("#topNavWrap").remove();

    var loginItems = $("#login").find("[name]");
    var signItems = $("#signup").find("[name]");


    $(".mzm-btn-toggle").on("click", function(e) {
        var $this = $(this);
        var hrefId = $this.attr("href");
        var hrefElement = $(hrefId);
        setTimeout(function() {
            hrefElement.find("[name]").eq(0).focus();
        });
        var siblingPanelId = $this.parent().siblings('.panel-heading').find(".btn").attr("href");
        var siblingPanel = $(siblingPanelId);
        siblingPanel.find("[name]").each(function(index, el) {
            defaultTip(el);
            $(el).val("");
        });
        $this.removeClass('btn-default').addClass('btn-primary');
        $(".mzm-btn-toggle").not($this).removeClass('btn-primary').addClass('btn-default').attr("data-toggle","collapse").parent().insertAfter($this.parent());
        var clickForId = $this.attr("id").split("Btn").join("");
        if ($("#" + clickForId).hasClass('in')) {
            // e.stopPropagation();
            $this.removeAttr('data-toggle');
            hrefElement.find("[name]").each(function(index, el) {
                $(el).triggerHandler('blur');
            });
            /*表单验证通过*/
            if (hrefElement.find(".glyphicon-remove,.glyphicon-warning-sign").length === 0) {
                if ($(this).attr("id") === "loginBtn") {
                    $.ajax({
                            type: 'POST',
                            data: requestData($("#login"),{sign:"login"})
                        })
                        .done(function(data) {
                            data = JSON.parse(data);
                            code=data.ret_code;
                            switch (code) {
                                case 1000:
                                    console.log("登录成功");
                                    // location.href = "https://www.baidu.com/";
                                    location.href = JSV.PATH_SERVER+'Index/pm';
                                    break;
                                case 1001:
                                    $("#myModal").find(".modal-body").html("验证码错误");
                                    $("#myModal").modal("show");
                                    break;
                                case 1002:
                                    $("#myModal").find(".modal-body").html("用户名与密码不匹配");
                                    $("#myModal").modal("show");
                                    break;
                            }
                            $('#myModal').on('hide.bs.modal', function(){
                                if(code===1001){
                                    errorTip(loginItems.filter("[name=verifyCode]")[0]);
                                }else if(code===1002){
                                    errorTip(loginItems.filter("[name=tel]")[0]);
                                    errorTip(loginItems.filter("[name=pwd]")[0]);
                                }
                            });

                        })
                        .fail(function() {
                            console.log("error");
                            $("#myModal").modal("show");
                        });


                } else {
                    $.ajax({
                            type: 'POST',
                            data: requestData($("#signup"),{sign:"register"})
                        })
                        .done(function(data) {
                            data = JSON.parse(data);
                            code=data.ret_code;
                            switch (code) {
                                case 1000:
                                    console.log("注册成功");
                                    // location.href = "https://www.baidu.com/";
                                    location.href = JSV.PATH_SERVER+'Index/pm';
                                    break;
                                case 1001:
                                    $("#myModal").find(".modal-body").html("验证码错误");
                                    $("#myModal").modal("show");
                                    break;
                                case 1002:
                                    $("#myModal").modal("show");
                                    break;
                                case 1003:
                                    $("#myModal").find(".modal-body").html("用户名已注册");
                                    $("#myModal").modal("show");
                                    break;
                                case 1004:
                                    $("#myModal").find(".modal-body").html("密码格式有误");
                                    $("#myModal").modal("show");
                                    break;
                            }
                            $('#myModal').on('hide.bs.modal', function(){
                                if(code===1001){
                                    errorTip(signItems.filter("[name=verifyCode]")[0]);
                                }else if(code===1002){
                                    errorTip(signItems.filter("[name=tel]")[0]);
                                }else if(code===1003){
                                    errorTip(signItems.filter("[name=tel]")[0]);
                                }else if(code===1004){
                                    errorTip(signItems.filter("[name=pwd]")[0]);
                                    errorTip(signItems.filter("[name=pwd2]")[0]);
                                }
                            });
                        })
                        .fail(function() {
                            console.log("error");
                            $("#myModal").modal("show");
                        });
                }
            }
        }
    });
    $(".form-control-feedback").on("click", function() {
        var $this = $(this);
        if ($this.is('.glyphicon-remove,.iconfont,.glyphicon-warning-sign')) {
            if ( $this.siblings("[name]").is( signItems.filter("[name=pwd]") ) ) {
	            var reSignPsd = $this.parent().nextAll(".input-group").find("[name=pwd2]");
                defaultTip(reSignPsd);
                reSignPsd.val("");
                reSignPsd.off("blur");
            }
            $this.siblings("[name]").val("").focus();
	        reSignPsd && reSignPsd.on("blur", blurValidate);
        }
    });


    signItems.add(loginItems).each(function(index, el) {
        var $this = $(el);
        $this.on("blur", blurValidate);
        $this.on("keyup", keyupValidate);
    });

    function blurValidate() {
        var $this = $(this);
        if ($this.parents(".panel-collapse").hasClass('in')) {
            var val = $.trim($this.val());
            var valLength = val.length;
            if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
                errorTip(this);
                // $this.focus();
            } else if ($this.siblings().hasClass('glyphicon-warning-sign')) {
                warningTip(this);
            } else {
                if ( $this.is( signItems.filter("[name=pwd2]") ) ) {
                    $this.val() === signItems.filter("[name=pwd]").val() || errorTip(this);
                }
            }

            if( $this.attr("name")==="tel" ){
            	isTeleNum(val) || errorTip(this);
            }else if( $this.attr("name")==="pwd" ){
            	if( !/^[A-z]\w{5,14}/.test(val) ){
            		errorTip(this);
            	}
            }

        }
    }

    function keyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        var tName = $this.attr("name");
        /*用户名有效性验证*/
        if ( tName==="tel" ) {
        	if( isTeleNum(val) ){
        		$this.siblings().hasClass('glyphicon-remove') && defaultTip(this);
        	}else if( $this.data("errored") ){
        		errorTip(this);
        	}
        }
        /*用户名有效性验证 END*/

        /*登录密码验证*/
        else if( tName==="pwd" || tName==="pwd2" ){
        	if(  /^[A-z]\w{5,14}/.test(val) ){
        		$this.siblings().hasClass('glyphicon-remove') && defaultTip(this);

        		if( $this.is( signItems.filter("[name=pwd]") ) ){
        			$this.parent().nextAll(".input-group").find("[name=pwd2]").attr("disabled", false);
        			if (pwdStrength(val) === 1) {
        			    $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-shibaibiaoqing");
        			} else if (pwdStrength(val) === 2) {
        			    $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-emoji02");
        			} else {
        			    $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-emojiicon");
        			}
        		}

        	}else if( $this.data("errored") ){
        		errorTip(this);
        	}
        }
        /*登录密码验证 END*/
        else if(tName==="verifyCode"){
            valLength && defaultTip(this);
        }

    }

    function pwdStrength(pwd) {
        var regExp = /^([a-z]+|[A-Z]+|\d+|[^0-9a-zA-Z]+)$/;
        if (regExp.test(pwd)) {
            return 1; //只包含一类字符
        } else {
            regExp = /^[a-z0-9]+$/;
            if (regExp.test(pwd)) {
                return 2; //包含2类字符
            } else {
                return 3; //不止2类字符
            }
        }
    }
}
/*登录页面index END*/

/*项目管理pm*/
function pmJs() {
    $("#topTitle").text("项目管理");
    /*发布项目*/
    var inputs=$("[name]", "#collapseOne");
    inputs.each(function() {
        var $this = $(this);
        $this.on("blur", blurValidate);
        $this.on("keyup", keyupValidate);
    });

    function blurValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
            if ($this.hasClass('datePicker')) {
                return;
            } else {
                errorTip(this);
            }
        } else if ($this.siblings().hasClass('glyphicon-warning-sign')) {
            warningTip(this);
        } else {
            defaultTip(this);
        }
    }

    function keyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        // var valLength = val.length;
        switch ($this.attr("name")) {
            case "money":
                if (!/^[0-9]+(\.)?[0-9]*$/.test(val)) {
                    errorTip(this);
                } else {
                    defaultTip(this);
                };
                break;
            case "requirement":
                if (!/^[\u4e00-\u9fa5a-zA-Z][\u4e00-\u9fa5\w]*$/.test(val)) {
                    errorTip(this);
                } else {
                    defaultTip(this);
                };
                break;
            default:
                if (val) {
                    defaultTip(this);
                }
        }
    }


    //日期选择
    var currYear = (new Date()).getFullYear();
    var opt = {};
    opt.date = { preset: 'date' };
    opt.datetime = { preset: 'datetime' };
    opt.time = { preset: 'time' };
    opt.default = {
        theme: 'android-ics light', //皮肤样式
        display: 'modal', //显示方式 
        mode: 'scroller', //日期选择模式
        dateFormat: 'yyyy-mm-dd',
        lang: 'zh',
        showNow: true,
        nowText: "今天",
        startYear: currYear - 10, //开始年份
        endYear: currYear + 10 //结束年份
    };

    $("input[name=contractDate]", "#collapseOne").mobiscroll($.extend(opt['date'], opt['default'])).on("change",function(){
    	if( $(this).val() ){
    		defaultTip(this);
    	}
    });
    // $("input[name=pmPublishDate]", "#collapseOne").mobiscroll($.extend(opt['date'], opt['default'])).on("change",function(){
    // 	if( $(this).val() ){
    // 		defaultTip(this);
    // 	}
    // });

    //日期选择 END

    /*开发周期单位切换*/
    $("#dateUnitMenu").find("a").on("click", function() {
            $("#dateUnit").text($(this).text());
    })
    /*开发周期单位切换 END*/
    /*提交 取消按钮*/
    $("#pmSubmitBtn").on("click", function() {
        inputs.each(function(index, el) {
            $(el).triggerHandler('blur');
            if ($(el).hasClass('datePicker')) {
                var val = $(el).val();
                val = $.trim(val);
                if (!val) {
                    errorTip(el);
                }
            }

        })
        if ($("#collapseOne").find(".glyphicon-remove,.glyphicon-warning-sign").length === 0) {
            var dateUnit=$("#dateUnit").text();
            switch(dateUnit){
                case "天":
                dateUnit="d";
                break;
                case "星期":
                dateUnit="w";
                break;
                case "月":
                dateUnit="m";
                break;
                case "年":
                dateUnit="y";
                break;
            }
            $.ajax({
                    type: 'POST',
                    data: requestData( $("#publishForm"),{sign:"item",days:inputs.filter("[name=days]").val()+dateUnit} )
                })
                .done(function() {
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                });
                

        }
    })
    $("#pmCancelBtn").on("click", function() {
            var inputs = $("#collapseOne").find("[name]");
            inputs.each(function(index, el) {
                defaultTip(el);
                $(el).off("blur");
                $(el).blur();
                $(el).on("blur", blurValidate);
            });
        });
        /*提交 取消按钮 END*/
    $(".form-control-feedback").on("click", function() {
            var $this = $(this);
            if ($this.is('.glyphicon-remove,.iconfont,.glyphicon-warning-sign')) {
                $this.siblings("[name]").val("").focus();
            }
        })
        /*发布项目 END*/
}
/*项目管理pm END*/

/*部门管理dm*/
function dmJs() {
    $("#topTitle").text("部门管理");
    /*部门添加*/
    var inputs = $("#collapseOne").find("[name]");
    inputs.filter(":enabled").on("blur", blurValidate);
    inputs.filter(":enabled").on("keyup", keyupValidate);

    function blurValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
            errorTip(this);
        }

    }

    function keyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        if ($this.attr("name") === "dmAddStaff") {
            /^([\u4e00-\u9fa5A-z]+)(&[\u4e00-\u9fa5A-z]+)*$/.test(val) ? defaultTip(this) : errorTip(this);
        } else {
            /^[\u4e00-\u9fa5A-z]+$/.test(val) ? defaultTip(this) : errorTip(this);
            if ($this.attr("name") === "dmJob") {
                if ($this.siblings().hasClass('glyphicon-remove')) {
                    $("#collapseOne").find("input[name=dmAddStaff]").attr("disabled", true).val("");
                }
            }
        }

    }


    $("#dmAddBtn").on("click", function() {
        var $this = $(this);
        $("#collapseOne").find("input:enabled").each(function(index, el) {
            $(el).triggerHandler('blur');
        })
        inputs.filter(":enabled").on("blur", blurValidate);
        if ($("#collapseOne").find(".glyphicon-remove,.glyphicon-warning-sign").length === 0) {
            $("#collapseOne").find("[name]").val("");
            $("#dmAddedTable").css("margin-bottom", 0).find("table").empty();
            console.log("验证通过")
                // $.ajax({
                //   url: '/path/to/file',
                //   type: 'POST',
                //   data: {param1: 'value1'},
                // })
                // .done(function() {
                //   console.log("success");
                // })
                // .fail(function() {
                //   console.log("error");
                // })
                // .always(function() {
                //   console.log("complete");
                // });

            $("#dmAddForm").hide(function() {
                $.ajax({
                        url: '/path/to/file',
                        type: 'default GET (Other values: POST)',
                        dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
                        data: { param1: 'value1' },
                    })
                    .done(function(data) {
                        console.log("success");
                        $("#dmAddedShowTable").find("tbody").append();
                        $("#dmAddedShowTable").show();

                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                        $("#dmAddedShowTable").show();
                    });

            });
            $("#dmContinueBtn").on("click", function() {
                $("#dmAddedShowTable").hide().find("tbody").empty();
                $("#dmAddForm").show();
            })

        }
    });
    $("#dmforStaffBtn").on("click", function() {
        var staffInput = $(this).parent().siblings('input');
        if (staffInput.val() && !staffInput.siblings().hasClass('glyphicon-remove')) {
            $("#collapseOne").find("input[name=dmAddStaff]").attr("disabled", false).on("blur", blurValidate).on("keyup", keyupValidate);
        }
    })
    $("#dmAddConfirmBtn").on("click", function() {
            var input = $(this).parent().siblings('input');
            if (!input.attr("disabled") && input.val() && !input.siblings().hasClass('glyphicon-remove')) {
                var dmJob = $("#collapseOne").find("[name=dmJob]");
                var dmAddStaff = $("#collapseOne").find("[name=dmAddStaff]");
                var tdStr = "<td>";
                var staffs = dmAddStaff.val().split("&");
                for (var i = 0; i < staffs.length; i++) {
                    tdStr += "<span class='mzm-man-wrap'>" + staffs[i] + "</span>"
                }
                tdStr += "</td>";
                var trStr = "<tr><th>" + dmJob.val() + "</th>" + tdStr + "</tr>";
                var tr = $(trStr);
                $('#dmAddedTable>table').append(tr).parent().css("margin-bottom", "20px");
                dmJob.val("").off("blur");
                dmAddStaff.val("").off("blur").attr("disabled", "true");
                $("#collapseOne").find("[name=dmAddName]").triggerHandler('blur');
                $("#collapseOne").find("[name=dmAddName]").off("blur");
            }
        })
        /*部门添加 END*/

    /*部门编辑*/

    
    var man = $("#collapseThree").find(".mzm-man-wrap");
    longTouch(man, function() {
            $(this).css("color", "red").find(".glyphicon").removeClass('glyphicon-menu-hamburger').addClass('glyphicon-remove').on("click", function() {
                $(this).parent().remove();
            });
        })
        /*部门编辑 END*/
}
/*部门管理dm END*/

/*个人管理um*/
function umJs() {
    $("#topTitle").text("个人管理");

    /*表单验证结果函数*/
    function errorTip(element) {
        $(element).siblings('.form-control-feedback').removeClass('glyphicon-ok glyphicon-warning-sign').addClass('glyphicon-remove').on("click", function() {
            $(this).siblings('input').val("").focus();
        }).closest('.has-feedback').addClass('has-error');
        $(element).focus();
    }

    function defaultTip(element) {
        return $(element).siblings('.form-control-feedback').removeClass('glyphicon-remove glyphicon-warning-sign').closest('.has-feedback').removeClass('has-error has-warning');
    }

    function warningTip(element) {
        $(element).siblings('.form-control-feedback').removeClass('glyphicon-ok glyphicon-remove').addClass('glyphicon-warning-sign').on("click", function() {
            $(this).siblings('input').val("").focus();
        }).closest('.has-feedback').removeClass('has-error has-success').addClass('has-warning');
        $(element).focus();
    }
    /*表单验证结果函数*/

    /*更换头像*/
    $("#uploadFileBtn").change(function() {
        var $file = $(this);
        var fileObj = $file[0];
        var windowURL = window.URL || window.webkitURL;
        var dataURL;
        var $img = $("#avatarPreview");
        if (fileObj && fileObj.files && fileObj.files[0]) {
            dataURL = windowURL.createObjectURL(fileObj.files[0]);
            $img.attr('src', dataURL);
        } else {
            dataURL = $file.val();
            var imgObj = $img[0];
            imgObj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
            imgObj.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = dataURL;
        }
    });
    /*更换头像 END*/

    

    /*资料编辑*/
    var editInputs = $("#collapseOne").find("[name]");
    editInputs.on("blur", editBlurValidate);
    editInputs.on("keyup", editKeyupValidate);

    function editBlurValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
            errorTip(this);
        }
    }

    function editKeyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var tName = $this.attr("name");
        if (tName === "umNickname") {
            /^[\u4e00-\u9fa5\w]+$/.test(val) ? defaultTip(this) : errorTip(this);
        } else if (tName === "umTeleNum") {
            isTeleNum(val) ? defaultTip(this) : errorTip(this);
        } else if (tName === "umQQ") {
            isQq(val) ? defaultTip(this) : errorTip(this);
        }
    }
    $("#umEditSubmitBtn").on("click", function() {
        editInputs.each(function(index, el) {
            $(el).triggerHandler('blur');
        });
        if ($("#collapseOne").find(".glyphicon-remove,.glyphicon-warning-sign").length === 0) {
            // console.log("验证通过");
            // $.ajax({
            //   url: '/path/to/file',
            //   type: 'POST',
            //   data: {param1: 'value1'},
            // })
            // .done(function() {
            //   console.log("success");
            // })
            // .fail(function() {
            //   console.log("error");
            // })
            // .always(function() {
            //   console.log("complete");
            // });
        }
    });
    /*资料编辑 END*/
}
/*个人管理um END*/

