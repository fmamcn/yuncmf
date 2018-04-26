/************************************************************* 所有带确认的ajax提交事件 ********************************************************/
/* get执行并返回结果，执行后不带跳转 */
/*没有地方用到*/ 
$(function () {
	$('body').on('click','.rst-btn',function () {
        var $url = this.href;
        $.get($url, function (data) {
            if (data.code == 1) {
                layer.alert(data.msg, {icon: 6});
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* get执行并返回结果，执行后带跳转 */
/*用在好多地方，一般是a标签*/ 
$(function () {
	$('body').on('click','.rst-url-btn',function () {
        var $url = this.href;
        $.get($url, function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                    window.location.href = data.url;
                });
            } else {
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
        }, "json");
        return false;
    });
});
/* 直接跳转 */
/*后台注销按钮*/ 
$(function () {
	$('body').on('click','.confirm-btn',function () {
        var $url = this.href,
            $info = $(this).data('info');
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            window.location.href = $url;
        });
        return false;
    });
});
/* post执行并返回结果，执行后不带跳转 */
/*后台清除缓存按钮*/ 
$(function () {
	$('body').on('click','.confirm-rst-btn',function () {
        var $url = this.href,
            $info = $(this).data('info');
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            $.post($url, {}, function (data) {
                layer.alert(data.msg, {icon: 6});
            }, "json");
        });
        return false;
    });
});
/* get执行并返回结果，执行后带跳转 */
/*用在好多地方，一般是a标签*/ 
$(function () {
	$('body').on('click','.confirm-rst-url-btn',function () {
        var $url = this.href,
            $info = $(this).data('info');
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            $.get($url, function (data) {
                if (data.code==1) {
                    layer.alert(data.msg, {icon: 6}, function (index) {
                        layer.close(index);
                        window.location.href = data.url;
                    });
                } else {
                    layer.alert(data.msg, {icon: 5}, function (index) {
                        layer.close(index);
                    });
                }
            }, "json");
        });
        return false;
    });
});
/*************************************************************************** 所有状态类的ajax提交btn ********************************************************/
/* 审核状态操作 */
$(function () {
	$('body').on('click','.state-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '未审') {
                    var a = '<button class="btn btn-minier btn-danger">未审</button>';
                    $btn.children('div').html(a).attr('title','未审');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">已审</button>';
                    $btn.children('div').html(b).attr('title','已审');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 排序状态操作 */
$(function () {
	$('body').on('click','#btnorder',function () {
        var $url=$(this).attr("href");
        if(!$url){
            $url=$(this).parents('form').attr('action');
        }
        $.post($url, $("input.list_order").serialize(), function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
					window.location.href = data.url;
                    layer.close(index);
                });
            }else{
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
        }, "json");
        return false;
    });
});
/* 启用状态操作 */
$(function () {
	$('body').on('click','.open-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '状态禁止') {
                    var a = '<button class="btn btn-minier btn-danger">禁用</button>';
                    $btn.children('div').html(a).attr('title','已禁用');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">开启</button>';
                    $btn.children('div').html(b).attr('title','已开启');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 显示状态操作 */
$(function () {
	$('body').on('click','.display-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '状态禁止') {
                    var a = '<button class="btn btn-minier btn-danger">隐藏</button>';
                    $btn.children('div').html(a).attr('title','已隐藏');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">显示</button>';
                    $btn.children('div').html(b).attr('title','已显示');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 检测状态操作 */
$(function () {
    $('body').on('click','.notcheck-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '检测') {
                    var a = '<button class="btn btn-minier btn-yellow">检测</button>';
                    $btn.children('div').html(a).attr('title','检测');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-danger">不检测</button>';
                    $btn.children('div').html(b).attr('title','不检测');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 激活状态操作 */
$(function () {
	$('body').on('click','.active-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '未激活') {
                    var a = '<button class="btn btn-minier btn-danger">未激活</button>';
                    $btn.children('div').html(a).attr('title','未激活');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">已激活</button>';
                    $btn.children('div').html(b).attr('title','已激活');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/*************************************************************************** 所有ajaxForm提交 ********************************************************/
/* 通用表单不带检查操作，失败不跳转 */
$(function () {
    $('.ajaxForm').ajaxForm({
        success: complete, // 这是提交后的方法
        dataType: 'json'
    });
});
/* 通用表单不带检查操作，失败跳转 */
$(function () {
    $('.ajaxForm2').ajaxForm({
        success: completego, // 这是提交后的方法
        dataType: 'json'
    });
});
/* 通用含验证码表单不带检查操作，失败不跳转 */
$(function () {
    $('.ajaxFormLogin').ajaxForm({
        success: completeLogin, // 这是提交后的方法
        dataType: 'json'
    });
});
/* 会员增加编辑表单，带检查 */
$(function () {
    $('.memberform').ajaxForm({
        beforeSubmit: checkmemberForm, // 此方法主要是提交前执行的方法，根据需要设置
        success: complete, // 这是提交后的方法
        dataType: 'json'
    });
});
/* admin增加编辑表单，带检查 */
$(function () {
    $('.adminform').ajaxForm({
        beforeSubmit: checkadminForm, // 此方法主要是提交前执行的方法，根据需要设置
        success: complete, // 这是提交后的方法
        dataType: 'json'
    });
});
/* 多选删除操作 */
$(function () {
    $('#alldel').ajaxForm({
        beforeSubmit: checkselectForm, // 此方法主要是提交前执行的方法，根据需要设置，一般是判断为空获取其他规则
        success: completego, // 这是提交后的方法
        dataType: 'json'
    });
});
//失败不跳转
function complete(data) {
    if (data.code == 1) {
        layer.alert(data.msg, {icon: 6}, function (index) {
            layer.close(index);
            window.location.href = data.url;
        });
    } else {
        layer.alert(data.msg, {icon: 5}, function (index) {
            layer.close(index);
        });
    }
}
//失败跳转
function completego(data) {
    if (data.code == 1) {
        layer.alert(data.msg, {icon: 6}, function (index) {
            layer.close(index);
            window.location.href = data.url;
        });
    } else {
        layer.alert(data.msg, {icon: 5}, function (index) {
            layer.close(index);
            window.location.href = data.url;
        });
        return false;
    }
}
//失败不跳转,验证码刷新
function completeLogin(data) {
    if (data.code == 1) {
        window.location.href = data.url;
    } else {
        $("#verify").val('');
        $("#verify_img").click();
        layer.alert(data.msg, {icon: 5});
    }
}
//admin表单检查
function checkadminForm() {
    var admin_username = $.trim($('input[name="admin_username"]').val()); //获取INPUT值
    var myReg = /^[\u4e00-\u9fa5]+$/;//验证中文
    if (admin_username.indexOf(" ") >= 0) {
        layer.alert('登录用户名包含了空格，请重新输入', {icon: 5}, function (index) {
            layer.close(index);
            $('#admin_username').focus();
        });
        return false;
    }
    if (myReg.test(admin_username)) {
        layer.alert('用户名必须是字母，数字，符号', {icon: 5}, function (index) {
            layer.close(index);
            $('#admin_username').focus();
        });
        return false;
    }
    if (!$("#admin_tel").val().match(/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/)) {
        layer.alert('电话号码格式不正确', {icon: 5}, function (index) {
            layer.close(index);
            $('#admin_tel').focus();
        });
        return false;
    }
}
//member手机号表单检查
function checkmemberForm() {
    if (!$("#member_tel").val().match(/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/)) {
        layer.alert('电话号码格式不正确', {icon: 5}, function (index) {
            layer.close(index);
            $('#member_tel').focus();
        });
        return false;
    }
}
//多选表单检查
function checkselectForm() {
    var chk_value = [];
    $('input[id="navid"]:checked').each(function () {
        chk_value.push($(this).val());
    });

    if (!chk_value.length) {
        layer.alert('至少选择一个删除项', {icon: 5});
        return false;
    }
}
/* 全选 */
function CheckAll(form) {
    for (var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];
        if (e.Name != 'chkAll' && e.disabled == false) {
            e.checked = form.chkAll.checked;
        }
    }
}
/*************************************************************************** 所有css操作 ********************************************************/
/* 菜单样式 */
$(function () {
    //插入header-nav
    $('#sidebar2').insertBefore('.page-content');
    $('.navbar-toggle[data-target="#sidebar2"]').insertAfter('#menu-toggler');
    //固定
    $(document).on('settings.ace.two_menu', function (e, event_name, event_val) {
        if (event_name == 'sidebar_fixed') {
            if ($('#sidebar').hasClass('sidebar-fixed')) {
                $('#sidebar2').addClass('sidebar-fixed');
                $('#navbar').addClass('h-navbar');
            }
            else {
                $('#sidebar2').removeClass('sidebar-fixed');
                $('#navbar').removeClass('h-navbar');
            }
        }
    }).triggerHandler('settings.ace.two_menu', ['sidebar_fixed', $('#sidebar').hasClass('sidebar-fixed')]);
});
/* 权限配置 */
$(function () {
    //动态选择框，上下级选中状态变化
    $('input.checkbox-parent').on('change', function () {
        var dataid = $(this).attr("dataid");
        $('input[dataid^=' + dataid + '-]').prop('checked', $(this).is(':checked'));
    });
    $('input.checkbox-child').on('change', function () {
        var dataid = $(this).attr("dataid");
        dataid = dataid.substring(0, dataid.lastIndexOf("-"));
        var parent = $('input[dataid=' + dataid + ']');
        if ($(this).is(':checked')) {
            parent.prop('checked', true);
            //循环到顶级
            while (dataid.lastIndexOf("-") != 2) {
                dataid = dataid.substring(0, dataid.lastIndexOf("-"));
                parent = $('input[dataid=' + dataid + ']');
                parent.prop('checked', true);
            }
        } else {
            //父级
            if ($('input[dataid^=' + dataid + '-]:checked').length == 0) {
                parent.prop('checked', false);
                //循环到顶级
                while (dataid.lastIndexOf("-") != 2) {
                    dataid = dataid.substring(0, dataid.lastIndexOf("-"));
                    parent = $('input[dataid=' + dataid + ']');
                    if ($('input[dataid^=' + dataid + '-]:checked').length == 0) {
                        parent.prop('checked', false);
                    }
                }
            }
        }
    });
});
//模态框状态
$(document).ready(function () {
    $("#myModaledit").hide();
    $("#gb").click(function () {
        $("#myModaledit").hide(200);
    });
    $("#gbb").click(function () {
        $("#myModaledit").hide(200);
    });
    $("#gbbb").click(function () {
        $("#myModaledit").hide(200);
    });
});
$(document).ready(function () {
    $("#myModal").hide();
    $("#gb").click(function () {
        $("#myModal").hide(200);
    });
    $("#gbb").click(function () {
        $("#myModal").hide(200);
    });
    $("#gbbb").click(function () {
        $("#myModal").hide(200);
    });
});
/*************************************************************************** 所有ajax获取编辑数据 ********************************************************/
/* 会员组修改操作 */
$(function () {
	$('body').on('click','.memberedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {member_group_id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editmember_group_id").val(data.member_group_id);
                $("#editmember_group_name").val(data.member_group_name);
                $("#editmember_group_open").val(data.member_group_open);
                $("#editmember_group_toplimit").val(data.member_group_toplimit);
                $("#editmember_group_bomlimit").val(data.member_group_bomlimit);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 友链编辑模态框显示 */
function link_type_edit(a, b, c) {
    $("#myModal").show(300);
    $("#plug_linktype_id").val(a);
    $("#newplug_linktype_name").val(b);
    $("#newplug_linktype_order").val(c);
}
/* 友链编辑 */
$(function () {
    $('body').on('click','.linkedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {plug_link_id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editplug_link_id").val(data.plug_link_id);
                $("#editplug_link_l").val(data.plug_link_l);
                $("#editplug_link_name").val(data.plug_link_name);
                $("#editplug_link_url").val(data.plug_link_url);
                $("#editplug_link_target").val(data.plug_link_target);
                $("#editplug_link_qq").val(data.plug_link_qq);
                $("#editplug_link_order").val(data.plug_link_order);
                $("#editplug_link_typeid").val(data.plug_link_typeid);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* URL跳转规则编辑 */
$(function () {
    $('body').on('click','.reurledit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#reurledit_id").val(data.id);
                $("#reurledit_name").val(data.name);
                $("#reurledit_validate_url").val(data.validate_url);
                $("#reurledit_wait_time").val(data.wait_time);
                $("#reurledit_goto_url").val(data.goto_url);
                $("#reurledit_sort").val(data.sort);
                $("#reurledit_status").val(data.status);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 路由规则编辑 */
$(function () {
    $('body').on('click','.routeedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editroute_id").val(data.id);
                $("#editroute_full_url").val(data.full_url);
                $("#editroute_url").val(data.url);
                if (data.status == 1) {
                    $("#editroute_status").prop("checked",true);
                } else {
                    $("#editroute_status").prop("checked", false);
                }
                $("#editroute_listorder").val(data.listorder);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 广告位编辑 */
$(function () {
	$('body').on('click','.adtypeedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {ad_type_id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#adtype_id_edit").val(data.ad_type_id);
                $("#ad_type_name_edit").val(data.ad_type_name);
                $("#css_edit").val(data.css);
                $("#js_edit").val(data.js);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 来源编辑 */
$(function () {
    $('body').on('click','.sourceedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {source_id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editsource_id").val(data.source_id);
                $("#editsource_name").val(data.source_name);
                $("#editsource_order").val(data.source_order);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
//来源添加动作
function souadd(val) {
    $('#news_source').val(val);
}
/* 标签编辑 */
$(function () {
    $('body').on('click','.flagedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {flag_id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editflag_id").val(data.flag_id);
                $("#editflag_name").val(data.flag_name);
                $("#editflag_type").val(data.flag_type);
                $("#editflag_order").val(data.flag_order);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
//标签添加动作
function flagadd(val) {
    $('#news_flag').val(val);
}
/* 省份编辑 */
$(function () {
    $('body').on('click','.provinceedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#edit_pid").val(data.pid);
                $("#editid").val(data.id);
                $("#edit_name").val(data.name);
                $("#edit_short").val(data.short);
                $("#edit_center").val(data.center);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});

/* 城市编辑 */
$(function () {
    $('body').on('click','.cityedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editid").val(data.id);
                $("#editpid").val(data.pid);
                $("#edit_name").val(data.name);
                $("#edit_area_code").val(data.area_code);
                $("#edit_zip_code").val(data.zip_code);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});

/* 县区编辑 */
$(function () {
    $('body').on('click','.countyedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editid").val(data.id);
                $("#editpid").val(data.pid);
                $("#edit_name").val(data.name);
                $("#edit_area_code").val(data.area_code);
                $("#edit_zip_code").val(data.zip_code);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});

/* 文章分类编辑 */
$(function () {
    $('body').on('click','.article_classifyedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editclassify_id").val(data.classify_id);
                $("#editclassify_name").val(data.classify_name);
                $("#editclassify_description").val(data.classify_description);
                $("#editimg_w").attr('src',data.img_w);
                $("#editimg_h").attr('src',data.img_h);
                $("#editclassify_order").val(data.classify_order);
                $("#file0_edit").val(data.img_w);
                $("#file1_edit").val(data.img_h);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});

/* 视频分类编辑 */
$(function () {
    $('body').on('click','.video_classifyedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editclassify_id").val(data.classify_id);
                $("#editclassify_name").val(data.classify_name);
                $("#editclassify_description").val(data.classify_description);
                $("#editimg_w").attr('src',data.img_w);
                $("#editimg_h").attr('src',data.img_h);
                $("#editclassify_order").val(data.classify_order);
                $("#file0_edit").val(data.img_w);
                $("#file1_edit").val(data.img_h);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});

/* 产品分类编辑 */
$(function () {
    $('body').on('click','.classifyedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#edit_pid").val(data.pid);
                $("#editid").val(data.id);
                $("#edit_name").val(data.name);
                $("#edit_key").val(data.key);
                $("#edit_img_w").attr('src',data.img_w);
                $("#edit_img_h").attr('src',data.img_h);
                $("#file0_edit").val(data.img_w);
                $("#file1_edit").val(data.img_h);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 产品品牌编辑 */
$(function () {
    $('body').on('click','.brandedit-btn',function () {
        var $url = this.href,
            val = $(this).data('id');
        $.post($url, {id: val}, function (data) {
            if (data.code == 1) {
                $("#myModaledit").show(300);
                $("#editclassify_id").val(data.classify_id);
                $("#editclassify_name").val(data.classify_name);
                $("#editclassify_description").val(data.classify_description);
                $("#editimg_w").attr('src',data.img_w);
                $("#editimg_h").attr('src',data.img_h);
                $("#editclassify_order").val(data.classify_order);
                $("#file0_edit").val(data.img_w);
                $("#file1_edit").val(data.img_h);
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});

/*************************************************************************** 单图/多图操作********************************************************/
/* 封面图片 */
$("#file0").change(function () {
    var objUrl = getObjectURL(this.files[0]);
    console.log("objUrl = " + objUrl);
    if (objUrl) {
        $("#img0").attr("src", objUrl);
    }
});
//
function getObjectURL(file) {
    var url = null;
    if (window.createObjectURL != undefined) { // basic
        $("#oldcheckpic").val("nopic");
        url = window.createObjectURL(file);
    } else if (window.URL != undefined) { // mozilla(firefox)
        $("#oldcheckpic").val("nopic");
        url = window.URL.createObjectURL(file);
    } else if (window.webkitURL != undefined) { // webkit or chrome
        $("#oldcheckpic").val("nopic");
        url = window.webkitURL.createObjectURL(file);
    }
    return url;
}
function backpic(picurl) {
    $("#img0").attr("src", picurl);//还原修改前的图片
    $("input[name='file0']").val("");//清空文本框的值
    $("input[name='oldcheckpic']").val(picurl);//替换文本框的值
}
/*************************************************************************** 数据备份还原********************************************************/
/* 数据库备份、优化、修复 */
(function ($) {
    $("a[id^=optimize_]").click(function () {
        $.get(this.href, function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6});
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        });
        return false;
    });
    $("a[id^=repair_]").click(function () {
        $.get(this.href, function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6});
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        });
        return false;
    });

    var $form = $("#export-form"), $export = $("#export"), tables
    $optimize = $("#optimize"), $repair = $("#repair");

    $optimize.add($repair).click(function () {
		var that=this;
        $.post(this.href, $form.serialize(), function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                });
            } else {
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
            setTimeout(function () {
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled', false);
            }, 1500);
        }, "json");
        return false;
    });

    $export.click(function () {
        $export.children().addClass("disabled");
        $export.children().text("正在发送备份请求...");
		var that=this;
        $.post(
            $form.attr("action"),
            $form.serialize(),
            function (data) {
                if (data.code==1) {
                    tables = data.tables;
                    $export.children().text(data.msg + "开始备份，请不要关闭本页面！");
                    backup(data.tab);
                    window.onbeforeunload = function () {
                        return "正在备份数据库，请不要关闭！"
                    }
                } else {
                    layer.alert(data.msg, {icon: 5});
                    $export.children().removeClass("disabled");
                    $export.children().text("立即备份");
                    setTimeout(function () {
                        $('#top-alert').find('button').click();
                        $(that).removeClass('disabled').prop('disabled', false);
                    }, 1500);
                }
            },
            "json"
        );
        return false;
    });

    function backup(tab, status) {
        status && showmsg(tab.id, "开始备份...(0%)");
		var that=this;
        $.get($form.attr("action"), tab, function (data) {
            if (data.code==1) {
                showmsg(tab.id, data.msg);
                if (!$.isPlainObject(data.tab)) {
                    $export.children().removeClass("disabled");
                    $export.children().text("备份完成，点击重新备份");
                    window.onbeforeunload = null;
                }
				if(data.tab !=undefined){
					backup(data.tab, tab.id != data.tab.id);					
				}
            } else {
                updateAlert(data.msg, 'alert-error');
                $export.children().removeClass("disabled");
                $export.children().text("立即备份");
                setTimeout(function () {
                    $('#top-alert').find('button').click();
                    $(that).removeClass('disabled').prop('disabled', false);
                }, 1500);
            }
        }, "json");
    }

    function showmsg(id, msg) {
        $tr=$form.find("input[value=" + tables[id] + "]").closest("tr");
        $tr.find(".green").html("");
        $tr.find(".info").html("");
        $tr.find(".backup").html(msg);
    }
})(jQuery);
/*************************************************************************** 其它********************************************************/
/* textarea字数提示 */
$(function () {
    $('textarea.limited').maxlength({
        'feedback': '.charsLeft',
    });
    $('textarea.limited1').maxlength({
        'feedback': '.charsLeft1',
    });
    $('textarea.limited2').maxlength({
        'feedback': '.charsLeft2',
    });
    $('textarea.limited3').maxlength({
        'feedback': '.charsLeft3',
    });
});
$(function () {
    $("[data-toggle='tooltip']").tooltip();
});
/* 分页代码 */
function ajax_page(page) {
    $.ajax({
        type:"POST",
        data:$('#list-filter').serialize()+'&page='+page,            
        success: function(data,status){
            $("#ajax-data").html(data);
        }
    });
}
/*************************************************************************** 生成安全文件********************************************************/
(function ($) {
	$('body').on('click','#security_generate',function () {
        $(this).children().addClass("disabled");
        $(this).find("span").text("正在生成安全文件...");
        $.get(this.href, function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                    window.location.href = data.url;
                });
            } else {
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
            $(this).children().removeClass("disabled");
            $(this).find("span").text("重新生成安全文件");
        });
        return false;
});
})(jQuery);
/*************************************************************************** 选择列表框change事件********************************************************/
(function ($) {
	$('body').on('change','.ajax_change',function () {		
        var $form = $(this).parents("form");
		$.ajax({
		    url:$form.attr('action'),
			type:"POST",
			data:$form.serialize(),            
			success: function(data,status){
				$("#ajax-data").html(data);
			}
		});	
    });
    $('body').on('click','.range_inputs .applyBtn',function () {
        var reservation=$('#reservation');
        var $form = reservation.parents("form");
        reservation.val($('input[name="daterangepicker_start"]').val()+' - '+$('input[name="daterangepicker_end"]').val());
        $.ajax({
            url:$form.attr('action'),
            type:"POST",
            data:$form.serialize(),
            success: function(data,status){
                $("#ajax-data").html(data);
            }
        });
    });
    })(jQuery);
/*搜索form*/
$(function () {
	$('body').on('click','.ajax-search-form',function () {
		$.ajax({
			type:"POST",
			data:$(this).parents("form").serialize(),            
			success: function(data,status){
				$("#ajax-data").html(data);
			}
		});	
        return false;
    });
});
$(function () {
    /*权限管理*/
	$('body').on('click','.rule-list',function () {
		var $a=$(this),$tr=$a.parents('tr');
		var $pid=$tr.attr('id');
		if($a.find('span').hasClass('fa-minus')){
			$("tr[id^='"+$pid+"-']").attr('style','display:none');
			$a.find('span').removeClass('fa-minus').addClass('fa-plus');
		}else{
			if($("tr[id^='"+$pid+"-']").length>0){
				$("tr[id^='"+$pid+"-']").attr('style','');
				$a.find('span').removeClass('fa-plus').addClass('fa-minus');
			}else{
				var $url = this.href,$id=$a.data('id'),$level=$a.data('level');
				$.post($url,{pid:$id,level:$level,id:$pid}, function (data) {
					if (data) {
						$a.find('span').removeClass('fa-plus').addClass('fa-minus');
						$tr.after(data);
					}else{
						$a.find('span').removeClass('fa-plus').addClass('fa-minus');
					}
				}, "json");
			}
		}
        return false;
    });
});
