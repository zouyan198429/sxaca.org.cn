
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
    })

});

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

    var template_name = $('input[name=template_name]').val();
    if(!judge_validate(4,'发票模板名称',template_name,true,'length',1,50)){
        return false;
    }

    var merge_goods = $('input[name=merge_goods]:checked').val() || '';
    var judge_seled = judge_validate(1,'是否合并开票',merge_goods,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择是否合并开票",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var open_status = $('input[name=open_status]:checked').val() || '';
    var judge_seled = judge_validate(1,'开启状态',open_status,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择开启状态",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var xmmc = $('input[name=xmmc]').val();
    if(!judge_validate(4,'项目名称',xmmc,true,'length',1,50)){
        return false;
    }

    var append_name = $('input[name=append_name]:checked').val() || '';
    var judge_seled = judge_validate(1,'是否拼接商品名称',append_name,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择是否拼接商品名称",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var spbm = $('input[name=spbm]').val();
    if(!judge_validate(4,'商品编码',spbm,true,'length',1,30)){
        return false;
    }

    var zxbm = $('input[name=zxbm]').val();
    if(!judge_validate(4,'自行编码',zxbm,false,'length',1,30)){
        return false;
    }

    var ggxh = $('input[name=ggxh]').val();
    if(!judge_validate(4,'规格型号',ggxh,false,'length',1,30)){
        return false;
    }

    var dw = $('input[name=dw]').val();
    if(!judge_validate(4,'计量单位',dw,false,'length',1,30)){
        return false;
    }

    var sl = $('input[name=sl]').val();
    if(!judge_validate(4,'税率',sl,true,'doublepositive','','')){
        return false;
    }

    var yhzcbs = $('input[name=yhzcbs]:checked').val() || '';
    var judge_seled = judge_validate(1,'优惠政策标识',yhzcbs,true,'custom',/^([01])$/,"");
    if(judge_seled != ''){
        layer_alert("请选择优惠政策标识",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var lslbs = $('input[name=lslbs]:checked').val() || '';
    var judge_seled = judge_validate(1,'零税率标识',lslbs,false,'custom',/^([123])$/,"");
    if(judge_seled != ''){
        layer_alert("请选择零税率标识",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var zzstsgl = $('input[name=zzstsgl]').val();
    if(!judge_validate(4,'增值税特殊管理',zzstsgl,false,'length',1,50)){
        return false;
    }

    // var sort_num = $('input[name=sort_num]').val();
    // if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
    //     return false;
    // }

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
