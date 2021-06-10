
var SUBMIT_FORM = true;//防止多次点击提交
var IS_LAYUI_PAGE = isLayuiIframePage();// 是否Layui的iframe弹出层；true: 是； false:不是
var IS_FRAME_PAGE = isIframePage();// 是否iframe弹出层；true: 是； false:不是

//获取当前窗口索引
var PARENT_LAYER_INDEX = getParentLayerIndex();
//让层自适应iframe
// operateBathLayuiIframeSize(PARENT_LAYER_INDEX, [1], 500);// 最大化当前弹窗[layui弹窗时]
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
    // 修改实收金额
    $(document).on("change",'input[name=payment_amount]',function(){
        var payment_amount = parseFloat($(this).val());// 收款金额
        var pay_method = $('input[name=pay_method]').val();
        var total_price = parseFloat($('input[name=total_price]').val());// 总金额
        if(pay_method != 1){
            err_alert('非现金收款，不可改变实收金额');
            $(this).val(total_price);
            $('input[name=change_amount]').val(0);
            return false;
        }
        if(mathCompare(payment_amount, payment_amount) == -1){// payment_amount < total_price
            err_alert('实收金额，不能小于总金额【¥' + total_price + '】');
            $(this).val(total_price);
            $('input[name=change_amount]').val(0);
            return false;
        }
        var change_amount = numberMathFormat(mathSubtract(payment_amount, total_price),2, true, 3);// payment_amount - total_price;
        $('input[name=change_amount]').val(change_amount);
        $('.change_amount').html('&yen;' + change_amount);
    });

});

window.onload = function() {
    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
//     initPic();
    initLoad();// 页面初始化
    var layer_index = layer.load();

    // 初始化列表文件显示功能
    var uploadAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 0,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceListObj = $('.baguetteBoxOne');// $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    // initList();
    initPic();
    layer.close(layer_index);//手动关闭
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}
// 页面初始化
function initLoad() {

    // 优先现金支付方式--可修改金额、找零---实收【选中、获得焦点】
    $('input[name=payment_amount]').select();// 实收金额自动选中
    $('input[name=payment_amount]').focus();// 实收金额自动获得焦点

    var pay_method = $('input[name=pay_method]').val();

    // 按钮改为显示收款码
    // 生成付款码，用户扫码支付的情况
    $('.count_down_num').html(WAIT_SECOND_NUM);// 显示扫码支付倒计时的秒数
    if(pay_method == 2 || pay_method == 4){
        $('#submitBtn').html('显示收款码');
        $('#submitBtn').show();
        // 用扫码枪扫用户的收款码的情况
    }else if(pay_method == 16 || pay_method == 64){
        $('.auth_code_block').show();
        $('input[name=auth_code]').select();// 实收金额自动选中
        $('input[name=auth_code]').focus();// 实收金额自动获得焦点
        $('#submitBtn').hide();
    }else{
        $('#submitBtn').show();
    }

}
//业务逻辑部分
var otheraction = {

};

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    // if(!judge_validate(4,'记录id',id,true,'digit','','')){
    //     return false;
    // }
    if(!judge_validate(4,'收款学员',id,true,'length',1,3000)){
        return false;
    }
    var total_price = parseFloat($('input[name=total_price]').val());// 总金额
    var payment_amount = parseFloat($('input[name=payment_amount]').val());// 实收金额
    if(mathCompare(payment_amount, total_price) == -1){// payment_amount < total_price
        err_alert('实收金额，不能小于总金额【¥' + total_price + '】');
        return false;
    }

    var pay_method = $('input[name=pay_method]').val();
    var index_query = layer.confirm('您确定操作吗？', {
       btn: ['确定','取消'] //按钮
    }, function(){
        layer.close(index_query);
        ajax_save(id);
    }, function(){
        // 扫码枪扫收付款码支付
        if(pay_method == 16 || pay_method == 64){
            $('.auth_code_block').show();
            $('input[name=auth_code]').val('');
            $('input[name=auth_code]').select();// 实收金额自动选中
            $('input[name=auth_code]').focus();// 实收金额自动获得焦点
            $('#submitBtn').hide();
        }
    });
}

// 验证通过后，ajax保存
function ajax_save(id){

    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = $("#addForm").serialize();
    console.log(SAVE_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : SAVE_URL,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            // console.log(ret);
            consoleLogs([ret]);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                var pay_method = $('input[name=pay_method]').val();
                // 扫码枪扫收付款码支付
                if(pay_method == 16 || pay_method == 64){
                    //alert('失败');
                    err_alert(ret.errorMsg);
                }else{
                    //alert('失败');
                    err_alert(ret.errorMsg);
                }

            }else{//成功
                // go(LIST_URL);

                // countdown_alert("操作成功!",1,5);
                // parent_only_reset_list(false);
                // wait_close_popus(2,PARENT_LAYER_INDEX);
                var order_no = ret.result['order_no'];
                var pay_config_id = ret.result['pay_config_id'];
                var pay_method = ret.result['pay_method'];
                var params = ret.result['params'];
                var code_url = params['code_url'] || '';
                var pay_order_no = params['pay_order_no'] || '';
                if(code_url.length <= 0){
                    if(pay_method == 16 || pay_method == 64){// 扫码枪支付
                        // 每秒去查询一下付款码付款情况
                        barcodePay(order_no,  pay_order_no);// 扫条形码收款
                        SUBMIT_FORM = true;//标记为未提交过
                    }else{
                        paySuccessFun({result:1}, {order_no:order_no, pay_order_no:pay_order_no});// 支付成功
                    }
                }else{
                    scanPay(code_url, order_no,  pay_order_no);// 扫码支付-- 生成收款二维码
                    SUBMIT_FORM = true;//标记为未提交过

                }
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

// 扫码支付-- 生成收款二维码
function scanPay(code_url, order_no, pay_order_no) {

    console.log('--code_url--', code_url);
    showQRCodeTable('qrcode', code_url, 250, 250);// 显示付款二维码
    $('.qrcode_block').show();// 显示 付款码
    $('#submitBtn').hide();// 隐藏按钮
    // 每秒去查询一下付款码付款情况
    loopQueryResult(order_no, pay_order_no);

}

// 扫码支付-- 扫条形码收款
function barcodePay(order_no, pay_order_no) {

    // console.log('--code_url--', code_url);
    // showQRCodeTable('qrcode', code_url, 250, 250);// 显示付款二维码
    // $('.qrcode_block').show();// 显示 付款码
   // $('#submitBtn').hide();// 隐藏按钮
    // 每秒去查询一下付款码付款情况
    loopQueryResult(order_no, pay_order_no);

}
// 支付成功，关闭弹层，并刷新列表
// ret 查询订单支付接口返回的对象
// paramObj 对象 {order_no:order_no, pay_order_no:pay_order_no}
var paySuccessFun = function (ret, paramObj) {
    var order_no = getAttrVal(paramObj, 'order_no', true, '');
    var pay_order_no = getAttrVal(paramObj, 'pay_order_no', true, '');
    var close_loop = getAttrVal(paramObj, 'close_loop', true, {});

    SUBMIT_FORM = false;//标记为未提交过
    var result_num =  ret.result || 3;// 1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
    consoleLogs(['==result_num=', result_num]);
    if(result_num == 1){
        console.log('==支付成功==', close_loop);
        close_loop.is_close = true; // -- 一般用这个控制开关
        layerMsg('支付成功！订单号【' + order_no + '】', 1, 0.3, 3000, function(){
            var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
            // if(id > 0) reset_total = false;
            reset_total = false;
            window.parent.parent_reset_list_iframe_close(reset_total);// 刷新并关闭
            //do something
        });
    }else if(result_num == 2){
        payFailOverTime(order_no, pay_order_no);
    }
};

// ret 接口返回的对象
// paramObj 对象 {order_no:order_no, pay_order_no:pay_order_no}
var payFailFun = function payFail(ret, paramObj) {
    var close_loop = getAttrVal(paramObj, 'close_loop', true, {});
    // 有错误时，先关闭自动执行代码
    console.log('==支付错误==', close_loop);
    close_loop.is_close = true; // -- 一般用这个控制开关

    // 弹出错误提示
    layerMsg(ret.errorMsg, 5, 0.3, 3000, function () {
        parent_reset_list();// 关闭当前弹窗
    });
};

// 支付失败，关闭弹层--超时
function payFailOverTime(order_no, pay_order_no) {
    layerMsg("操作失败！请重新发起支付！", 5, 0.3, 3000, function () {
        parent_reset_list();// 关闭当前弹窗
    });
}

// 生成的付款码，支付定时查询支付情况
// pay_order_no 我方订单号
function loopQueryResult(order_no, pay_order_no) {
    loopDoingFun(WAIT_SECOND_NUM, 1000, function (intervalId, close_loop, loopedSec, loop_num) {
        console.log('===每次循环的方法开始==1=');
        console.log('=1==intervalId===', intervalId);
        console.log('=1==close_loop===', close_loop);
        console.log('=1==loopedSec===', loopedSec);
        console.log('=1==loop_num===', loop_num);
        // if(loop_num >= 20) {// 执行次数关闭
            // clearInterval(intervalId);
            // close_loop.is_close = true; // -- 一般用这个控制开关
        // }
        var data = {order_no:order_no, pay_order_no:pay_order_no};
        data.loop_num = loop_num;
        data.close_loop = close_loop;
        // ajax请求，去查询支付是否成功
        ajaxQuery({
            async: false, //false:同步;true:异步[默认]
            ajax_url: AJAX_QUERY_ORDER_WX_URL,//  请求的url 默认''
            data: data,// 数据对象 {} 或表单序列化的字符串 $("#addForm").serialize();  格式： aa=1&b=2... ; 默认 ：｛｝
            ajax_type: 'POST',//  请求的类型 默认POST ;  GET
            headers:{},//  加入请求头的对象 默认 {}
            dataType: 'json',//   返回数据类型 默认'json'
            show_loading: false,//  请求时，是否显示遮罩 true:显示，false:不显示
            // successFun : AJAX_SUCESS_FUNCTION,//   ajax操作成功执行的函数,默认 AJAX_SUCESS_FUNCTION ，参数  ret, paramObj； 可重新写一个方法
            paramObj: {//      ajax操作成功执行的函数默认方法 ajaxSuccessFun的参数 格式如下： 或 自定义方法--则此参数自己按自己需要定义对象 {}
               apiSuccessObj: {// 操作成功的对象-- 具体参数请看 ajaxAPIOperate 方法
                   operate_txt:'操作成功',// 操作名称--完整的句子 如 审核通过成功！ ；默认 '操作成功'
                   operate_num: 8, // 操作的编号; 默认 0 ； 1弹出显示成功的文字及确定按钮； 2 执行刷新列表操作;
                                   //      4 弹出倒计时的3秒的窗口，并可以指定一个执行函数; 8 其它指定的自定义函数
                                   //      16 : 在4的基础上，指定执行函数关闭弹窗并刷新列表【-适合弹层新加和修改页】
                   alert_icon_num : 1, // operate_num 有1 时 是成功还是失败； 0失败1成功2询问3警告 [默认]4对5错
                   reset_total : false,// operate_num 有2 和 16 时：是否重新从数据库获取总页数 true:重新获取,false不重新获取【默认】
                   countDownFun: '',// operate_num 有4 和 16时：倒计时后，同时要执行的函数 参数 ret ，参数二 paramObj {}；；默认 ''--不执行
                   countDownFunParams: {},// operate_num 有4 和 16时：自定义函数的第二个参数对象 默认 {}
                   countDownFunTime: 3000,// operate_num 有4 和 16时：倒计时；默认 3000-3秒
                   countDownFunIcon: 1,// operate_num 有4 和 16时：0-6 图标 0：紫红叹号--出错警示 ；1：绿色对勾--成功【默认】；2：无图标 ；3：淡黄问号；4：灰色小锁图标；
                   //  5：红色哭脸--         ； 6：绝色笑脸
                   customizeFun: paySuccessFun, // operate_num 有8时：自定义的要执行的函数 参数 ret ，参数二 paramObj {}；默认 ''--不执行
                   customizeFunParams: data// operate_num 有8时：自定义函数的第二个参数对象 默认 {}
               },
               apiFailObj: {// 操作失败的对象-- 具体参数请看 ajaxAPIOperate 方法
                   operate_txt:'操作失败',// 操作名称--完整的句子 如 审核通过成功！ ；默认 '操作成功'
                   operate_num: 8, // 操作的编号; 默认 0 ； 1弹出显示成功的文字及确定按钮； 2 执行刷新列表操作;
                                   //      4 弹出倒计时的3秒的窗口，并可以指定一个执行函数; 8 其它指定的自定义函数
                                   //      16 : 在4的基础上，指定执行函数关闭弹窗并刷新列表【-适合弹层新加和修改页】
                   alert_icon_num : 3, // operate_num 有1 时 是成功还是失败； 0失败1成功2询问3警告 [默认]4对5错
                   reset_total : false,// operate_num 有2 和 16 时：是否重新从数据库获取总页数 true:重新获取,false不重新获取【默认】
                   countDownFun: '',// operate_num 有4 和 16时：倒计时后，同时要执行的函数 参数 ret ，参数二 paramObj {}；；默认 ''--不执行
                   countDownFunParams: {},// operate_num 有4 和 16时：自定义函数的第二个参数对象 默认 {}
                   countDownFunTime: 3000,// operate_num 有4 和 16时：倒计时；默认 3000-3秒
                   countDownFunIcon: 5,// operate_num 有4 和 16时：0-6 图标 0：紫红叹号--出错警示 ；1：绿色对勾--成功【默认】；2：无图标 ；3：淡黄问号；4：灰色小锁图标；
                   //  5：红色哭脸--         ； 6：绝色笑脸
                   customizeFun: payFailFun, // operate_num 有8时：自定义的要执行的函数 参数 ret ，参数二 paramObj {}；默认 ''--不执行
                   customizeFunParams: data// operate_num 有8时：自定义函数的第二个参数对象 默认 {}
               }
            }
        });

    }, function (intervalLoopId, close_loop, do_sec_num, do_num) {
        console.log('===每分钟循环的方法开始==2=');
        console.log('=2==intervalLoopId===', intervalLoopId);
        console.log('=2==close_loop===', close_loop);
        console.log('=2==do_sec_num===', do_sec_num);
        console.log('=2==do_num===', do_num);
        // if(do_num >= 10) {// 执行次数关闭
            // clearInterval(intervalLoopId);
            // close_loop.is_close = true; // -- 一般用这个控制开关
        // }
        // 更新倒计时
        $('.count_down_num').html(do_sec_num);
        // 如果时间到了，还没有成功
        if(do_sec_num <= 0){
            close_loop.is_close = true; // -- 一般用这个控制开关
            payFailOverTime(order_no, pay_order_no);// 支付时间到了--超时
        }
    }, 1000);
}
