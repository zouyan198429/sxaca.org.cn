
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
    //提交
    $(document).on("click","#submitBtn",function(){
        //var index_query = layer.confirm('您确定提交保存吗？', {
        //    btn: ['确定','取消'] //按钮
        //}, function(){
        ajax_form();
        //    layer.close(index_query);
        // }, function(){
        //});
        return false;
    });

    $(document).on("click","#" + CAPTCHA_IMG_ID,function(){
        get_captcha_code();
    });
    get_captcha_code();
});
// 获得验证码图片
function get_captcha_code(){
    var layer_index = layer.load();
    data = {'random':Math.random()};
    $.ajax({
        'type' : 'GET',
        'url' : GET_CAPTCHA_IMG_URL,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
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
                $("#" + CAPTCHA_IMG_ID ).attr('src', captcha.img);
                $('input[name=' +  CAPTCHA_KEY_INPUT_NAME + ']').val(captcha.key);

            }
            layer.close(layer_index);//手动关闭
        }
    });
}


//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回
    // 验证信息
    var admin_username = $('input[name=admin_username]').val();
    var judgeuser =judge_validate(1,'用户名',admin_username,true,'length',6,20);
    if(judgeuser != ''){
        layer_alert(judgeuser,3,0);
        // err_alert('<font color="#000000">' + judgeuser + '</font>');
        return false;
    }
    var admin_password = $('input[name=admin_password]').val();
    var judgePassword = judge_validate(1,'密码',admin_password,true,'length',6,20);
    if(judgePassword != ''){
        layer_alert(judgePassword,3,0);
        //err_alert('<font color="#000000">' + judgePassword + '</font>');
        return false;
    }

    // 验证码信息
    var captcha_code = $('input[name=captcha_code]').val();
    var judgecode =judge_validate(1,'验证码',captcha_code,true,'length',4,6);
    if(judgecode != ''){
        judgecode = "请输入完整的验证码";
        layer_alert(judgecode,3,0);
        // err_alert('<font color="#000000">' + judgeuser + '</font>');
        return false;
    }

    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = $("#addForm").serialize();
    console.log(LOGIN_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : LOGIN_URL,
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
                goTop(INDEX_URL);
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
