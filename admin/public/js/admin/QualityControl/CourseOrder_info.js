
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

window.onload = function() {

    // 初始化列表文件显示功能
    var uploadAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 0,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceListObj = $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');
    initPic();
};

function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}
$(function(){
    //提交
    // $(document).on("click","#submitBtn",function(){
    //     //var index_query = layer.confirm('您确定提交保存吗？', {
    //     //    btn: ['确定','取消'] //按钮
    //     //}, function(){
    //     ajax_form();
    //     //    layer.close(index_query);
    //     // }, function(){
    //     //});
    //     return false;
    // })

});

//业务逻辑部分
var otheraction = {

    seledAll:function(obj){
        var checkAllObj =  $(obj);
        /*
        checkAllObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function(){
            if(!$(this).prop('disabled')){
                $(this).prop('checked', checkAllObj.prop('checked'));
            }
        });
        */
        checkAllObj.closest('#' + DYNAMIC_TABLE).find('.check_item').each(function(){
            if(!$(this).prop('disabled')){
                $(this).prop('checked', checkAllObj.prop('checked'));
            }
        });
        return false;
    },
    seledSingle:function(obj) {// 单选点击
        var checkObj = $(obj);
        var allChecked = true;
        /*
         checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
            if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
                // $(this).prop('checked', checkAllObj.prop('checked'));
                allChecked = false;
                return false;
            }
        });
        */
        checkObj.closest('#' + DYNAMIC_TABLE).find('.check_item').each(function () {
            if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
                // $(this).prop('checked', checkAllObj.prop('checked'));
                allChecked = false;
                return false;
            }
        });
        // 全选复选操选中/取消选中
        /*
        checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
            if (!$(this).prop('disabled') && $(this).val() == ''  ) {
                $(this).prop('checked', allChecked);
                return false;
            }
        });
        */
        checkObj.closest('#' + DYNAMIC_TABLE).find('.check_all').each(function () {
            $(this).prop('checked', allChecked);
        });
        return false;
    }
};
//ajax提交表单
// function ajax_form(){
//     if (!SUBMIT_FORM) return false;//false，则返回
//
//     // 验证信息
//     var id = $('input[name=id]').val();
//     if(!judge_validate(4,'记录id',id,true,'digit','','')){
//         return false;
//     }
//
//
//
//
//     // var work_num = $('input[name=work_num]').val();
//     // if(!judge_validate(4,'工号',work_num,true,'length',1,30)){
//     //     return false;
//     // }
//     //
//     // var department_id = $('select[name=department_id]').val();
//     // var judge_seled = judge_validate(1,'部门',department_id,true,'digit','','');
//     // if(judge_seled != ''){
//     //     layer_alert("请选择部门",3,0);
//     //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
//     //     return false;
//     // }
//
//     // var group_id = $('select[name=group_id]').val();
//     // var judge_seled = judge_validate(1,'部门',group_id,true,'digit','','');
//     // if(judge_seled != ''){
//     //     layer_alert("请选择班组",3,0);
//     //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
//     //     return false;
//     // }
//
//     // var position_id = $('select[name=position_id]').val();
//     // var judge_seled = judge_validate(1,'职务',position_id,true,'digit','','');
//     // if(judge_seled != ''){
//     //     layer_alert("请选择职务",3,0);
//     //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
//     //     return false;
//     // }
//
//     var industry_name = $('input[name=industry_name]').val();
//     if(!judge_validate(4,'行业名称',industry_name,true,'length',1,20)){
//         return false;
//     }
//
//     var simple_name = $('input[name=simple_name]').val();
//     if(!judge_validate(4,'名称简写',simple_name,true,'length',1,20)){
//         return false;
//     }
//
//     var sort_num = $('input[name=sort_num]').val();
//     if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
//         return false;
//     }
//
//
//     // 验证通过
//     SUBMIT_FORM = false;//标记为已经提交过
//     var data = $("#addForm").serialize();
//     console.log(SAVE_URL);
//     console.log(data);
//     var layer_index = layer.load();
//     $.ajax({
//         'type' : 'POST',
//         'url' : SAVE_URL,
//         'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
//         'data' : data,
//         'dataType' : 'json',
//         'success' : function(ret){
//             console.log(ret);
//             if(!ret.apistatus){//失败
//                 SUBMIT_FORM = true;//标记为未提交过
//                 //alert('失败');
//                 err_alert(ret.errorMsg);
//             }else{//成功
//                 // go(LIST_URL);
//
//                 // countdown_alert("操作成功!",1,5);
//                 // parent_only_reset_list(false);
//                 // wait_close_popus(2,PARENT_LAYER_INDEX);
//                 layer.msg('操作成功！', {
//                     icon: 1,
//                     shade: 0.3,
//                     time: 3000 //2秒关闭（如果不配置，默认是3秒）
//                 }, function(){
//                     var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
//                     if(id > 0) reset_total = false;
//                     parent_reset_list_iframe_close(reset_total);// 刷新并关闭
//                     //do something
//                 });
//                 // var supplier_id = ret.result['supplier_id'];
//                 //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
//                 //    SUPPLIER_ID_VAL = supplier_id;
//                 //    $('input[name="supplier_id"]').val(supplier_id);
//                 //}
//                 // save_success();
//             }
//             layer.close(layer_index);//手动关闭
//         }
//     });
//     return false;
// }
