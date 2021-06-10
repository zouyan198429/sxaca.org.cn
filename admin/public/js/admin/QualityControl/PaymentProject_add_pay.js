
var SUBMIT_FORM = true;//防止多次点击提交
var IS_LAYUI_PAGE = isLayuiIframePage();// 是否Layui的iframe弹出层；true: 是； false:不是
var IS_FRAME_PAGE = isIframePage();// 是否iframe弹出层；true: 是； false:不是

//获取当前窗口索引
var PARENT_LAYER_INDEX = getParentLayerIndex();
//让层自适应iframe
operateBathLayuiIframeSize(PARENT_LAYER_INDEX, [1], 500);// 最大化当前弹窗[layui弹窗时]
//关闭iframe
$(document).on("click",".closeIframe",function(){
    iframeclose(PARENT_LAYER_INDEX, 0, '', 2);
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

window.onload = function() {
    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
//     initPic();
    // 初始化列表文件显示功能
    var uploadAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 1,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceListObj = $('#resource_block');// $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    // initList();
    initPic();
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

//业务逻辑部分
// var otheraction = {
//     selectCompany: function(obj){// 选择商家
//         var recordObj = $(obj);
//         //获得表单各name的值
//         var weburl = SELECT_COMPANY_URL;
//         console.log(weburl);
//         // go(SHOW_URL + id);
//         // location.href='/pms/Supplier/show?supplier_id='+id;
//         // var weburl = SHOW_URL + id;
//         // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
//         var tishi = '选择所属企业';//"查看供应商";
//         console.log('weburl', weburl);
//         layeriframe(weburl,tishi,700,450,0);
//         return false;
//     }
// };
$(function(){
    // 富文本
    KindEditor.create('textarea.kindeditor', {
        basePath: '/dist/lib/kindeditor/',
        allowFileManager : true,
        bodyClass : 'article-content',
        afterBlur : function(){
            this.sync();
        }
    });
    // popSelectInit();// 初始化选择弹窗
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
    // 修改收费金额
    $(document).on("change",'input[name=pay_amount]',function(){
        initRealAmount();
    });
    // 修改优惠金额
    $(document).on("change",'input[name=discount_amount]',function(){
        initRealAmount();
    });
});

// 通过父页面弹出支付页面
function payOrderStaff(paymentRecordId){
    consoleLogs(['-payOrderStaff-paymentRecordId-', paymentRecordId]);
    // 执行父类的支付方法
    window.parent.otheraction.payByIds(null, paymentRecordId);
    // 关闭当前页面
    parent_reset_list();
}

// 改变收费金额或优惠金额后处理，显示实收金额
function initRealAmount(){
    var pay_amount = initNumberVal($('input[name=pay_amount]').val());
    $('input[name=pay_amount]').val(pay_amount);
    console.log('==pay_amount==', pay_amount);
    var discount_amount = initNumberVal($('input[name=discount_amount]').val());
    $('input[name=discount_amount]').val(discount_amount);
    console.log('==discount_amount==', discount_amount);
    var real_amount = numberMathFormat(mathSubtract(pay_amount, discount_amount),2, true, 3);// payment_amount - total_price;
    console.log('==real_amount==', real_amount);
    $(".real_amount").html(real_amount);
    $('input[name=real_amount]').val(real_amount);
}

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
        return false;
    }

    // var work_num = $('input[name=work_num]').val();
    // if(!judge_validate(4,'工号',work_num,true,'length',1,30)){
    //     return false;
    // }
    //
    // var department_id = $('select[name=department_id]').val();
    // var judge_seled = judge_validate(1,'部门',department_id,true,'digit','','');
    // if(judge_seled != ''){
    //     layer_alert("请选择部门",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    // var group_id = $('select[name=group_id]').val();
    // var judge_seled = judge_validate(1,'部门',group_id,true,'digit','','');
    // if(judge_seled != ''){
    //     layer_alert("请选择班组",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    // var position_id = $('select[name=position_id]').val();
    // var judge_seled = judge_validate(1,'职务',position_id,true,'digit','','');
    // if(judge_seled != ''){
    //     layer_alert("请选择职务",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    var pay_amount = $('input[name=pay_amount]').val();
    if(!judge_validate(4,'收费金额',pay_amount,true,'doublepositive','','')){
        return false;
    }

    var discount_amount = $('input[name=discount_amount]').val();
    discount_amount = initNumberVal(discount_amount);
    $('input[name=discount_amount]').val(discount_amount);
    if(!judge_validate(4,'优惠金额',discount_amount,true,'double','','')){
        return false;
    }

    var real_amount = $('input[name=real_amount]').val();
    if(!judge_validate(4,'实收金额',real_amount,true,'doublepositive','','')){
        return false;
    }

    // 实收金额不能小于0【可以 >= 0】
    var real_amount = numberMathFormat(mathSubtract(pay_amount, discount_amount),2, true, 3);// payment_amount - total_price;

    if(mathCompare(real_amount, 0) == -1){// real_amount < 0
        layer_alert("实收金额，不能小于【¥' + 0 + '】",3,0);
        return false;
    }

    var discount_explain = $('textarea[name=discount_explain]').val();
    if(!judge_validate(4,'优惠说明',discount_explain,false,'length',1,500)){
        return false;
    }

    // 对收集字段进行判断
    var trsObj = $('.field_tr');
    // if(trsObj.length <= 0){
    //     layer_alert("请先增加收集字段",3,0);
    //     return false;
    // }
    var tr_num = 1;// 记录序号
    var is_err = false;
    trsObj.each(function(){
        var trObj = $(this);
        var field_name = trObj.data('field_name');
        console.log('==field_name==', field_name);
        var val_type = trObj.data('val_type');
        console.log('==val_type==', val_type);
        var field_id = trObj.data('id');
        console.log('==field_id==', field_id);
        var required_status = trObj.data('required_status');
        console.log('==required_status==', required_status);
        var sel_items = trObj.data('sel_items');
        console.log('==sel_items==', sel_items);

        var field_input_name = "field_" + field_id;
        console.log('==field_input_name==', field_input_name);

        var field_val = '';
        var is_must = false;
        if(required_status == REQUIRED_STATUS_REQUIRED){
            is_must = true;
        }
        console.log('==is_must==', is_must);

        var sel_items_arr = sel_items.split("<br\/>");
        console.log('==sel_items_arr==', sel_items_arr);
        var field_var_reg = getPregByKeyArr(sel_items_arr, [], false);
        console.log('==field_var_reg==', field_var_reg);

        if(val_type != FIELD_VAL_TYPE_CHECKBOX ){// 非复选框
            if(val_type != FIELD_VAL_TYPE_RADIO ) {// 非单选框

                // 输入框
                if(val_type == VAL_TYPE_INPUT){
                    field_val = trObj.find('input[name="' + field_input_name + '"]').val() || '';
                }else if(val_type == VAL_TYPE_TEXTAREA || val_type == VAL_TYPE_RICHTEXT){
                    // 多行输入框 或 富文本
                    field_val = trObj.find('textarea[name="' + field_input_name + '"]').val() || '';

                }
                if(!judge_validate(4, field_name, field_val, is_must, 'length', 1, 7000)){
                    is_err = true;
                    return false;
                }
            }else{
                // 单选框
                field_val = trObj.find('input[name="' + field_input_name + '"]:checked').val() || '';
                // 单选的判断
                var judge_seled = judge_validate(1, field_name, field_val,is_must,'custom', field_var_reg,"");
                if(judge_seled != ''){
                    layer_alert("请选择" + field_name,3,0);
                    //err_alert('<font color="#000000">' + judge_seled + '</font>');
                    is_err = true;
                    return false;
                }
            }

        }else{

            // 复选框
            // 填写终端--判断
            var field_val_str = get_list_checked(trObj.find('.checkbox'), 3,1,'');
            if(is_must && judge_empty(field_val_str)) {//没有选中的
                layer_alert('请选择' + field_name,3,0);
                is_err = true;
                return false;
            }
            // 对值进行判断
            if(is_must && !judgeValStrInPreg(field_val_str, field_var_reg, ',')){
                layer_alert('请选择' + field_name + '正确的值',3,0);
                is_err = true;
                return false;
            }
        }
        tr_num++;// 记录序号
    });
    if(is_err){
        return false;
    }

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
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                // go(LIST_URL);

                // countdown_alert("操作成功!",1,5);
                // parent_only_reset_list(false);
                // wait_close_popus(2,PARENT_LAYER_INDEX);
                layer.msg('操作成功！', {
                    icon: 1,
                    shade: 0.3,
                    time: 3000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
                    var hidden_option = $('input[name=hidden_option]').val() || 0;
                    if( (hidden_option & 8192) != 8192){
                        var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                        if(id > 0) reset_total = false;
                        var paymentRecordId = ret.result;
                        consoleLogs(['-paymentRecordId-', paymentRecordId]);
                        // 通过父页面弹出支付页面
                        payOrderStaff(paymentRecordId);
                        // parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                    }else{

                        eval( 'window.parent.' + PARENT_BUSINESS_FUN_NAME + '(paramsToObj(decodeURIComponent(data), 1), ret.result, 2)');
                        parent_reset_list();// 关闭弹窗
                    }
                    //do something
                });
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

// 初始化，来决定*是显示还是隐藏
// function popSelectInit(){
//
//     $('.select_close').each(function(){
//         let closeObj = $(this);
//         let idObj = closeObj.siblings(".select_id");
//         if(idObj.length > 0 && idObj.val() != '' && idObj.val() != '0'  ){
//             closeObj.show();
//         }else{
//             closeObj.hide();
//         }
//     });
// }
//
// // 清空
// function clearSelect(Obj){
//     let closeObj = $(Obj);
//     console.log('closeObj=' , closeObj);
//
//     var index_query = layer.confirm('确定移除？', {
//         btn: ['确定','取消'] //按钮
//     }, function(){
//         // 清空id
//         let idObj = closeObj.siblings(".select_id");
//         if(idObj.length > 0 ){
//             idObj.val('');
//         }
//         // 清空名称文字
//         let nameObj = closeObj.siblings(".select_name");
//         if(nameObj.length > 0 ){
//             nameObj.html('');
//         }
//         closeObj.hide();
//         layer.close(index_query);
//     }, function(){
//     });
// }
//
// // 获得选中的企业id 数组
// function getSelectedCompanyIds(){
//     var company_ids = [];
//     var company_id = $('input[name=company_id]').val();
//     company_ids.push(company_id);
//     console.log('company_ids' , company_ids);
//     return company_ids;
// }
//
// // 取消
// // company_id 企业id
// function removeCompany(company_id){
//     var seled_company_id = $('input[name=company_id]').val();
//     if(company_id == seled_company_id){
//         $('input[name=company_id]').val('');
//         $('.company_name').html('');
//         $('.company_id_close').hide();
//     }
// }
//
// // 增加
// // company_id 企业id, 多个用,号分隔
// function addCompany(company_id, company_name){
//     $('input[name=company_id]').val(company_id);
//     $('.company_name').html(company_name);
//     $('.company_id_close').show();
// }
