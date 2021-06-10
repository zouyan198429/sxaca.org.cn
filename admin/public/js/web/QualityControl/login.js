
// 如果登录过期，跳转到登陆页时，让登陆页面在最顶层打开，而非iframe中。
if(self != top){top.location.href=self.location.href;}

var SUBMIT_FORM = true;//防止多次点击提交
var IS_LAYUI_PAGE = isLayuiIframePage();// 是否Layui的iframe弹出层；true: 是； false:不是
var IS_FRAME_PAGE = isIframePage();// 是否iframe弹出层；true: 是； false:不是

//获取当前窗口索引
var PARENT_LAYER_INDEX = getParentLayerIndex();

//让层自适应iframe
operateBathLayuiIframeSize(PARENT_LAYER_INDEX, [1], 500);// 最大化当前弹窗[layui弹窗时]

//关闭iframe
$(document).on("click",".closeIframe",function(){
    iframeclose(PARENT_LAYER_INDEX);
});
//刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_only_reset_list(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
}
//关闭弹窗,并刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_reset_list_iframe_close(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
    operateLayuiIframeSize(PARENT_LAYER_INDEX, 4);// 关闭弹窗
}
//关闭弹窗
function parent_reset_list(){
    operateLayuiIframeSize(PARENT_LAYER_INDEX, 4);// 关闭弹窗
}

$(function(){
    // 获得验证码
    $(document).on("click",".LAY-user-getsmscode",function(){
        var obj = $(this);
        getsmscode(obj);

    });


    //提交
    $(document).on("click",".submitBtn",function(){
        //var index_query = layer.confirm('您确定提交保存吗？', {
        //    btn: ['确定','取消'] //按钮
        //}, function(){
        var obj = $(this);
        var formObj = obj.closest('form');
        let type = $('input[name=form_type]').val();
        console.log('type=' , type);
        switch(type){
            case 'user_login':
                ajax_form_sms(formObj, USER_LOGIN_URL, USER_INDEX_URL);
                break;
            case 'compnay_login':
                ajax_form(formObj, COMPANY_LOGIN_URL, COMPANY_INDEX_URL);
                break;
            default:
        }
        //    layer.close(index_query);
        // }, function(){
        //});
        return false;
    });

    $(document).on("click",".layui-tab-title li",function(){
        var obj = $(this);
        let type = obj.data('type');
        var old_type = $('input[name=form_type]').val();
        // alert(type);
        let formObj = $('.' + type);
        if(old_type != type){
            // 更新验证码
            let imgcodeObj = formObj.find('.' +  CAPTCHA_IMG_CLASS + '');
            let imgSrc = imgcodeObj.attr('src');
            if(imgSrc == '') get_captcha_code(imgcodeObj);
        }
        $('input[name=form_type]').val(type);
    });

    $(document).on("click","." + CAPTCHA_IMG_CLASS,function(){
        var obj = $(this);
        get_captcha_code(obj);
    });

    // 短信登录不用图形验证码
    if(true){
        $('.' +  CAPTCHA_IMG_CLASS + '').first().click();
    }
    // $('.' +  CAPTCHA_IMG_CLASS + '').each(function(){
    //     var obj = $(this);
    //     get_captcha_code(obj);
    // })
});

// 获得手机验证码
function getsmscode(obj){
    var formObj = obj.closest('form');
    let type = $('input[name=form_type]').val();
    console.log('type=' , type);
    // ajax请求发送手机验证码

    var mobile = formObj.find('input[name=mobile]').val();
    var judgemobile =judge_validate(1,'手机号',mobile,true,'mobile');
    if(judgemobile != ''){
        layer_alert(judgemobile,3,0);
        // err_alert('<font color="#000000">' + judgeuser + '</font>');
        return false;
    }
    // 发送手机验证码
    let expire_seconds = CODE_TIME;// 60 * 2;
    send_mobile_code(obj, mobile, SEND_MOBILE_CODE_URL, expire_seconds)
}

// 倒计时
function countdown(obj, expire_seconds) {
    let tishi = "获取验证码";
    if(expire_seconds > 0){
        tishi = '剩余<b style="color:red;">' + expire_seconds + '</b>秒!'
    }
    obj.html(tishi);
    expire_seconds--;
    if(expire_seconds >= 0){
        //延迟一秒执行自己
        setTimeout(function () {
            countdown(obj, expire_seconds);
        }, 1000);
    }else{
        obj.attr("disabled", false);
        obj.html('重新获取验证码');
    }
}


// 获得验证码图片
function get_captcha_code(obj){
    var img_url = COMPANY_GET_CAPTCHA_IMG_URL;
    let type = $('input[name=form_type]').val();
    if(type == 'user_login') img_url = USER_GET_CAPTCHA_IMG_URL;
    var layer_index = layer.load();
    data = {'random':Math.random()};
    $.ajax({
        'type' : 'GET',
        'url' : img_url,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                layer_alert(ret.errorMsg,3,0);
                // err_alert('<font color="#000000">' + ret.errorMsg + '</font>');
            }else{//成功
                // goTop(INDEX_URL);
                // var supplier_id = ret.result['supplier_id'];
                //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                //    SUPPLIER_ID_VAL = supplier_id;
                //    $('input[name="supplier_id"]').val(supplier_id);
                //}
                // save_success();

                var captcha = ret.result;
                console.log(captcha);
                obj.attr('src', captcha.img);
                obj.closest('.' + CAPTCHA_FORM_ITEM).find('input[name=' +  CAPTCHA_KEY_INPUT_NAME + ']').val(captcha.key);

            }
            layer.close(layer_index);//手动关闭
        }
    });
}


//ajax提交表单
function ajax_form(formObj, login_url, index_url){
    if (!SUBMIT_FORM) return false;//false，则返回
    // 验证信息
    var id = 0;// $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
        return false;
    }
    // 验证信息
    var admin_username = formObj.find('input[name=admin_username]').val();
    var judgeuser =judge_validate(1,'帐号',admin_username,true,'length',6,20);
    if(judgeuser != ''){
        layer_alert(judgeuser,3,0);
        // err_alert('<font color="#000000">' + judgeuser + '</font>');
        return false;
    }
    var admin_password = formObj.find('input[name=admin_password]').val();
    var judgePassword = judge_validate(1,'密码',admin_password,true,'length',6,20);
    if(judgePassword != ''){
        layer_alert(judgePassword,3,0);
        //err_alert('<font color="#000000">' + judgePassword + '</font>');
        return false;
    }

    // 验证码信息
    var captcha_code = formObj.find('input[name=captcha_code]').val();
    var judgecode =judge_validate(1,'验证码',captcha_code,true,'length',4,6);
    if(judgecode != ''){
        judgecode = "请输入完整的验证码";
        layer_alert(judgecode,3,0);
        // err_alert('<font color="#000000">' + judgeuser + '</font>');
        return false;
    }
    ajax_save(formObj, id, login_url, index_url);
}

//ajax提交表单
function ajax_form_sms(formObj, login_url, index_url){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = 0;// $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
        return false;
    }

    var mobile = $('input[name=mobile]').val();
    var judgemobile =judge_validate(1,'手机号',mobile,true,'mobile');
    if(judgemobile != ''){
        layer_alert(judgemobile,3,0);
        // err_alert('<font color="#000000">' + judgeuser + '</font>');
        return false;
    }

    var mobile_vercode = '';
    if(mobile != ''){
        var mobile_vercode = $('input[name=mobile_vercode]').val();
        var judgemobile_vercode =judge_validate(1,'验证码',mobile_vercode,true,'length',4,6);
        if(judgemobile_vercode != ''){
            judgemobile_vercode = "请输入验证码。<br/>如未获取，请点击获取验证码！";
            layer_alert(judgemobile_vercode,3,0);
            // err_alert('<font color="#000000">' + judgeuser + '</font>');
            return false;
        }
    }

    // 验证验证码是否正确--并提交
    mobile_code_verify(formObj, id, mobile, mobile_vercode, SEND_MOBILE_CODE_VERIFY_URL, 2, login_url, index_url);
    // ajax_save(id);
    return false;
}

// 验证通过后，ajax保存
function ajax_save(formObj, id, login_url, index_url){
    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = formObj.serialize();
    console.log(login_url);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : login_url,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);

            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                layer_alert(ret.errorMsg,3,0);
                // err_alert('<font color="#000000">' + ret.errorMsg + '</font>');
            }else{//成功

                goTop(index_url);
                // var supplier_id = ret.result['supplier_id'];
                //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                //    SUPPLIER_ID_VAL = supplier_id;
                //    $('input[name="supplier_id"]').val(supplier_id);
                //}
                // save_success();
            }
            layer.close(layer_index);//手动关闭
        }
    });
    return false;
}

// 发送手机验证码
function send_mobile_code(obj, mobile, send_mobile_url, expire_seconds){
    if (!SUBMIT_FORM) return false;//false，则返回
    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = {'mobile':mobile,'random':Math.random()};
    console.log(send_mobile_url);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : send_mobile_url,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                layer_alert(ret.errorMsg,3,0);
                // err_alert('<font color="#000000">' + ret.errorMsg + '</font>');
            }else{//成功
                SUBMIT_FORM = true;//标记为未提交过
                // let expire_seconds = CODE_TIME;// 60 * 2;
                obj.attr("disabled", true);
                countdown(obj, expire_seconds);

                // goTop(INDEX_URL);
                // var supplier_id = ret.result['supplier_id'];
                //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                //    SUPPLIER_ID_VAL = supplier_id;
                //    $('input[name="supplier_id"]').val(supplier_id);
                //}
                // save_success();
            }
            layer.close(layer_index);//手动关闭
        }
    });
    return false;
}

// 验证手机验证码是否正确
// id 没有写 0
// 操作类型 operate_type 1 仅验证 2 提交表单
function mobile_code_verify(formObj, id, mobile, mobile_vercode, mobile_cod_verify_url, operate_type, login_url, index_url){
    if (!SUBMIT_FORM) return false;//false，则返回
    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = {'mobile':mobile, 'mobile_vercode': mobile_vercode,'random':Math.random()};
    console.log(mobile_cod_verify_url);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : mobile_cod_verify_url,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                layer_alert(ret.errorMsg,3,0);
                // err_alert('<font color="#000000">' + ret.errorMsg + '</font>');
            }else{//成功
                SUBMIT_FORM = true;//标记为未提交过
                // let expire_seconds = CODE_TIME;// 60 * 2;

                switch(operate_type){
                    case 1:// 1 仅验证
                        break;
                    case 2:// 2 提交表单

                        ajax_save(formObj, id, login_url, index_url);
                        break;
                    default:
                }
                // goTop(INDEX_URL);
                // var supplier_id = ret.result['supplier_id'];
                //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                //    SUPPLIER_ID_VAL = supplier_id;
                //    $('input[name="supplier_id"]').val(supplier_id);
                //}
                // save_success();
            }
            layer.close(layer_index);//手动关闭
        }
    });
    return false;
}
