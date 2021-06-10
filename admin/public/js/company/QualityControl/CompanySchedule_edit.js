
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
    importExcel:function(obj) {// 导入EXCEL
        var recordObj = $(obj);

        // 所属企业
        // var company_id = $('input[name=company_id]').val();
        // var judge_seled = judge_validate(1,'所属企业',company_id,true,'positive_int','','');
        // if(judge_seled != ''){
        //     layer_alert("请选择所属企业",3,0);
        //     return false;
        // }

        // go(IMPORT_EXCEL_URL);
        $('.import_file').trigger("click");// 触发搜索事件
        return false;
    }
    // ,
    // selectCompany: function(obj){// 选择商家
    //     var recordObj = $(obj);
    //     //获得表单各name的值
    //     var weburl = SELECT_COMPANY_URL;
    //     console.log(weburl);
    //     // go(SHOW_URL + id);
    //     // location.href='/pms/Supplier/show?supplier_id='+id;
    //     // var weburl = SHOW_URL + id;
    //     // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
    //     var tishi = '选择所属企业';//"查看供应商";
    //     console.log('weburl', weburl);
    //     layeriframe(weburl,tishi,700,450,0);
    //     return false;
    // }
};

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
    // });

    // 单独图片上传/导入文件
    $(document).on("change",".import_file" ,function(){// change
        var fileObj = this;
        if (fileObj.files.length == 0) {
            return false;
        }
        // 所属企业
        // var company_id = $('input[name=company_id]').val();
        // var judge_seled = judge_validate(1,'所属企业',company_id,true,'positive_int','','');
        // if(judge_seled != ''){
        //     fileObj.value = ''; //虽然file的value不能设为有字符的值，但是可以设置为空值
        //     layer_alert("请选择所属企业",3,0);
        //     return false;
        // }

        // 获得文件名
        var filePath = $(this).val();
        console.log('filePath=', filePath);
        var fileName = getUpFileName(filePath);// getFileName(filePath);
        console.log('fileName=', fileName);

        var index_query = layer.confirm('确定上传选择文件吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            upLoadFileSingle(fileObj, IMPORT_EXCEL_URL, 5, { 'name' : fileName}, finishUpfile);
            layer.close(index_query);

        }, function(){
            fileObj.value = ''; //虽然file的value不能设为有字符的值，但是可以设置为空值
        });
    });

});
// 上传成功后的回调
function finishUpfile(ret){
    let reset_total = true;
    parent_reset_list_iframe_close(reset_total);// 刷新并关闭
}
//
// //ajax提交表单
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
//     // var type_name = $('input[name=type_name]').val();
//     // if(!judge_validate(4,'标签名称',type_name,true,'length',1,20)){
//     //     return false;
//     // }
//     //
//     // var sort_num = $('input[name=sort_num]').val();
//     // if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
//     //     return false;
//     // }
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
//     }
// }
//
// // 增加
// // company_id 企业id, 多个用,号分隔
// function addCompany(company_id, company_name){
//     $('input[name=company_id]').val(company_id);
//     $('.company_name').html(company_name);
// }
