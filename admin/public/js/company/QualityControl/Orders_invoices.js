
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

//业务逻辑部分
var otheraction = {
    addBuyer:function(obj){// 增加企业抬头
        var recordObj = $(obj);
        // 所属企业
        var company_id = $('input[name=company_id]').val();
        var judge_seled = judge_validate(1,'所属企业',company_id,true,'positive_int','','');
        if(judge_seled != ''){
            layer_alert("请选择所属企业",3,0);
            return false;
        }
        var hidden_option = 1 | 8192;
        var url = ADD_INVOICE_BUYER_URL + '?hidden_option=' + hidden_option + '&company_id=' + company_id;
        consoleLogs([url]);
        var tishi = "发票抬头";
        layeriframe(url,tishi,750,450,0);
        // commonaction.browse_file(url, tishi,750,450, 0);
        return false;
    },
    showInvoice : function(id){// 弹窗显示
        //获得表单各name的值
        var data = {};// get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(INFO_INVOICE_BUYER_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = INFO_INVOICE_BUYER_URL + id + '?' + url_params;
        console.log(weburl);
        // go(INFO_INVOICE_BUYER_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = "发票抬头";// SHOW_URL_TITLE;//"查看供应商";
        layeriframe(weburl,tishi,750,450,0,'',null,2);
        return false;
    }
};

// 新加发票抬头保存成功后调用的方法
// obj:当前表单值对像
// result:保存接口返回的结果
// operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】2[默认]：新加保存成功时
function companyQualityControlInvoiceBuyeredit(obj, result, operateNum){
    operateNum = operateNum || 2;
    consoleLogs(['obj:', obj, 'result:', result, 'operateNum:', operateNum]);
    switch(operateNum){
        case 1:
            break;
        case 2:
            // break;
        default://其它
            if(obj.open_status == 1){
                var invoice_buyer_id = result;
                var html = '<label id="invoice_buyer_' + invoice_buyer_id + '"><input type="radio"  name="invoice_buyer_id"  value="' + invoice_buyer_id + '"   />' + obj.gmf_mc + '<a href="javascript:void(0);" onclick="otheraction.showInvoice(' + invoice_buyer_id + ')">查看</a></label>';
                $('#invoice_buyer_list').append(html);
            }
            break;
    }
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

});

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    // if(!judge_validate(4,'记录id',id,true,'digit','','')){
    if(!judge_validate(4,'记录id',id,true,'length',1,200)){
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

    // 所属企业
    var company_id = $('input[name=company_id]').val();
    var judge_seled = judge_validate(1,'所属企业',company_id,true,'positive_int','','');
    if(judge_seled != ''){
        layer_alert("请选择所属企业",3,0);
        return false;
    }

    var invoice_buyer_id = $('input[name=invoice_buyer_id]:checked').val() || '';
    var judge_seled = judge_validate(1,'发票抬头',invoice_buyer_id,true,'positive_int',"","");
    if(judge_seled != ''){
        layer_alert("请选择发票抬头",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
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
                        parent_reset_list_iframe_close(reset_total);// 刷新并关闭
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
