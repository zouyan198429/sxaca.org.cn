
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
    //其它 textarea 点击事件
    $(document).on("click",".project_standard_name",function(){
        var obj = $(this);
        var value = obj.val();
        var readonly = obj.attr('readonly');
        console.log('value=', value);
        console.log('readonly=', readonly);
        if(readonly == 'readonly'){// 只读时
            var otherCheckObj = obj.closest('div').find(".otherCheckbox");
            if(!otherCheckObj.is(':checked')){// 没有选中
                layer_alert("如果要指定其他方法标准！<br/>请先勾选‘其他’，再输入内容！",3,0);
            }
        }

        return false;
    });
    //其它 复选框 点击事件
    $(document).on("change",".otherCheckbox",function(){
        var otherCheckObj = $(this);
        var isChecked = otherCheckObj.is(':checked');
        console.log('isChecked=', isChecked);
        var textareaObj = otherCheckObj.closest('div').find(".project_standard_name");
        var textareaVal = textareaObj.val();
        console.log('textareaVal=', textareaVal);

        if(isChecked) {// 选中
            textareaObj.removeAttr('readonly');
        }
        if(!isChecked && textareaVal != ''){
            var index_query = layer.confirm('您确定取消吗？<br/>取消后输入框内容将清空！', {
                btn: ['确定','取消'] //按钮
            }, function(){
                textareaObj.attr('readonly', 'readonly');
                textareaObj.val('');
                layer.close(index_query);
            }, function(){
                console.log('点了取消按钮');
                otherCheckObj.prop('checked', true);
                // textareaObj.removeAttr('readonly');
            });
        }else if(!isChecked){
            textareaObj.attr('readonly', 'readonly');
        }

        return false;
    });
});

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    // var id = $('input[name=id]').val();
    // if(!judge_validate(4,'记录id',id,true,'digit','','')){
    //     return false;
    // }
    var ids = $('input[name=ids]').val();
    if(!judge_validate(4,'记录ids',ids,true,'length',1,100)){
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

    // 判断是否有选中方法标准
    let ids_arr = ids.split(","); //字符分割
    for (var i = 0;i < ids_arr.length ; i++ )
    {
        let tem_id = ids_arr[i];
        let ability_name = $('#ability_name_' + tem_id).html();

        // 下面是复选的方式--判断
        let seled_project_standard = get_list_checked('project_standard_' + tem_id, 1, 1);
        // if(!judge_list_checked('project_standard_' + tem_id,2)) {//没有选中的
        //     layer_alert("请选择【" + ability_name + '】方法标准',3,0);
        //     return false;
        // }
        console.log('seled_project_standard=', seled_project_standard);

        if(seled_project_standard == ''){
                layer_alert("请选择【" + ability_name + '】方法标准',3,0);
                return false;
        }
        if(seled_project_standard.split(",").indexOf('0') >= 0){
            var project_standard_name = $('textarea[name=project_standard_name_' + tem_id + ']').val();
            if(!judge_validate(4,"【" + ability_name + '】方法标准【其它】内容',project_standard_name,true,'length',3,1000)){
                return false;
            }

        }else{
            // $('textarea[name=project_standard_name_' + tem_id + ']').val('');
        }

        // 下面是单选的方式--判断

        // var project_standard_id = $('input[name=project_standard_id_' + tem_id + ']:checked').val() || '';
        // var judge_seled = judge_validate(1, ability_name + '方法标准', project_standard_id,true,'digit','','');
        // if(judge_seled != ''){
        //     layer_alert("请选择【" + ability_name + '】方法标准',3,0);
        //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
        //     return false;
        // }

        // 其它
        // if(project_standard_id == 0){
        //     var project_standard_name = $('textarea[name=project_standard_name_' + tem_id + ']').val();
        //     if(!judge_validate(4,"【" + ability_name + '】其它方法标准内容',project_standard_name,true,'length',3,1000)){
        //         return false;
        //     }
        // }else{
        //     // $('textarea[name=project_standard_name_' + tem_id + ']').val('');
        // }

    }

    var contacts = $('input[name=contacts]').val();
    if(!judge_validate(4,'姓名',contacts,true,'length',1,30)){
        return false;
    }

    var mobile = $('input[name=mobile]').val();
    if(!judge_validate(4,'手机',mobile,true,'mobile','','')){
        return false;
    }

    var tel = $('input[name=tel]').val();
    if(!judge_validate(4,'联系电话',tel,false,'length',1,30)){
        return false;
    }
    var index_query = layer.confirm('请仔细检查各项报名信息，谨防填选错误！<br/>提交后不能修改！', {
        btn: ['确认提交','返回检查'] //按钮
    }, function(){
        ajax_save(ids);
        layer.close(index_query);
    }, function(){
    });
}

// 验证通过后，ajax保存
function ajax_save(ids){

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
                    var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    // if(id > 0) reset_total = false;
                    if(ids != '') reset_total = false;
                    parent_reset_list_iframe_close(reset_total);// 刷新并关闭
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
