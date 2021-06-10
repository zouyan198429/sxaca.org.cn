
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

    // var type_name = $('input[name=type_name]').val();
    // if(!judge_validate(4,'类型名称',type_name,true,'length',1,20)){
    //     return false;
    // }
    //
    // var sort_num = $('input[name=sort_num]').val();
    // if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
    //     return false;
    // }
    let has_err = false; // 内部 是否有错 true:有错  false:没有错
    let hasVals = false;
    $('#samples_list').find('tr').each(function(){
        let trObj = $(this);
        let project_name = trObj.data('project_name');
        console.log('project_name=', project_name);
        let samples_num = trObj.data('samples_num');
        console.log('samples_num=', samples_num);
        let join_item_id = trObj.data('join_item_id');
        console.log('join_item_id=', join_item_id);
        let samples_num_txt = '取样编号';
        if(samples_num >= 2){
            samples_num_txt = '补测取样编号' ;// + samples_num - 1;
        }
        // 判断每一个输入框
        let input_has_val = false;// 是否有值
        let has_empty_val = false;// 是否已经开始有空值
        let input_num = 0;
        trObj.find('input[name="items_samples_' + join_item_id + '_' + samples_num + '[]"]').each(function () {
            let inputObj = $(this);
            let input_val = inputObj.val();
            console.log('input_val=', input_val);
            input_num++;

            var readonly = inputObj.attr('readonly');
            if(readonly == 'readonly') {// 只读时
                input_has_val = true;
                return false;// break;
            }

            let isMust = false;
            if(input_num == 1) isMust = true;
            var judge_seled = judge_validate(1,project_name + samples_num_txt + input_num,input_val,isMust,'digit','','');
            // 第一个必填 ，其它的可填, 但是要按顺序填
            // 值格式有错 或  前面已经有空值，但是后面又有值
            let input_err_str = '请按顺序填写【' + project_name + samples_num_txt + input_num + '】';
            if( judge_seled != '' || (has_empty_val && trim(input_val) !== '')){
                if((has_empty_val && trim(input_val) !== '')){
                    input_err_str = '请按顺序填写【' + project_name + samples_num_txt + '】';
                }
                layer_alert(input_err_str,3,0);
                //err_alert('<font color="#000000">' + judge_seled + '</font>');
                console.log('judge_seled=', judge_seled);
                has_err = true;
                return false;
            }
            if(trim(input_val) === ''){
                has_empty_val = true;
            }
            if(trim(input_val) !== ''){
                input_has_val = true;
            }
         });
        if(has_err){
            return false;
        }
        // 没有值
        if(!input_has_val){
            layer_alert("请按顺序填写【" + project_name + samples_num_txt + "】",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            has_err = true;
            return false;
        }
        hasVals = true;
    });
    // 内部有错--已经弹出提示
    if(has_err){
        return false;
    }
    // 没有输入值
    if(!hasVals){
        layer_alert('请输入样品编号',3,0);
        return false;
    }

    var index_query = layer.confirm('请仔细检查各项信息，谨防填选错误！<br/>提交后不能修改！', {
        btn: ['确认提交','返回检查'] //按钮
    }, function(){
        ajax_save(id);
        layer.close(index_query);
    }, function(){
    });

}

// 验证通过后，ajax保存
function ajax_save(id) {
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
