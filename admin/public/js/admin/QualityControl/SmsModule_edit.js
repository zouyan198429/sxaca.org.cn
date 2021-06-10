
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

    //切换参数类型
    $(document).on("change",'select[name="param_type[]"]',function(){
        var recordObj = $(this);
        var param_type = recordObj.val();
        console.log('==param_type=', param_type);
        var trObj = recordObj.closest('tr');
        initParamType(trObj, param_type);
    });

    // 初始化选择参数类型数据
    initSelectParamType();
});

// 处理第一行参数，选择不同的参数类型时
// trObj 每一行对象
// 参数类型的值
function initParamType(trObj, param_type) {
    var formatObj = trObj.find('input[name="date_time_format[]"]');
    var fixedObj = trObj.find('input[name="fixed_val[]"]');
    console.log('==formatObj=', formatObj);
    console.log('==fixedObj=', fixedObj);


    if(param_type == 1){// 日期时间
        // formatObj.val('');
        formatObj.attr('readonly', false);
        formatObj.show();
        fixedObj.val('');
        fixedObj.attr('readonly', true);
        fixedObj.hide();
    }else if(param_type == 2){// 固定值
        formatObj.val('');
        formatObj.attr('readonly', true);
        formatObj.hide();
        // fixedObj.val('');
        fixedObj.attr('readonly', false);
        fixedObj.show();
    }else if(param_type == 4){// 手动输入
        formatObj.val('');
        formatObj.attr('readonly', true);
        formatObj.hide();
        fixedObj.val('');
        fixedObj.attr('readonly', true);
        fixedObj.hide();
    }else if(param_type == 8){// 字段匹配
        formatObj.val('');
        formatObj.attr('readonly', true);
        formatObj.hide();
        fixedObj.val('');
        fixedObj.attr('readonly', true);
        fixedObj.hide();
    }else{
        formatObj.val('');
        formatObj.attr('readonly', true);
        formatObj.hide();
        fixedObj.val('');
        fixedObj.attr('readonly', true);
        fixedObj.hide();
    }
}

// 初始化选择参数类型数据
function initSelectParamType(){
    $('.staff_td').find('.data_list').find('tr').each(function () {
        var trObj = $(this);
        var param_type = trObj.find('select[name="param_type[]"]').val();
        initParamType(trObj, param_type);
    });
}

//业务逻辑部分
var otheraction = {
    del : function(obj, parentTag){// 删除-员工
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var trObj = recordObj.closest(parentTag);// 'tr'
            trObj.remove();
            // autoCountStaffNum();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    batchDel:function(obj, parentTag, delTag) {// 批量删除--员工
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除选中记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var hasDel = false;
            recordObj.closest(parentTag).find('.check_item').each(function () {
                if (!$(this).prop('disabled') && $(this).val() != '' &&  $(this).prop('checked') ) {
                    // $(this).prop('checked', checkAllObj.prop('checked'));
                    var trObj = $(this).closest(delTag);// 'tr'
                    trObj.remove();
                    hasDel = true;
                }
            });
            if(!hasDel){
                err_alert('请选择需要操作的数据');
            }
            // autoCountStaffNum();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    moveUp : function(obj, parentTag){// 上移
        var recordObj = $(obj);
        var current = recordObj.closest(parentTag);//获取当前<tr>  'tr'
        var prev = current.prev();  //获取当前<tr>前一个元素
        console.log('index', current.index());
        if (current.index() > 0) {
            current.insertBefore(prev); //插入到当前<tr>前一个元素前
        }else{
            layer_alert("已经是第一个，不能移动了。",3,0);
        }
        return false;
    },
    moveDown : function(obj, parentTag){// 下移
        var recordObj = $(obj);
        var current = recordObj.closest(parentTag);//获取当前<tr>'tr'
        var next = current.next(); //获取当前<tr>后面一个元素
        console.log('length', next.length);
        console.log('next', next);
        if (next.length > 0 && next) {
            current.insertAfter(next);  //插入到当前<tr>后面一个元素后面
        }else{
            layer_alert("已经是最后一个，不能移动了。",3,0);
        }
        return false;
    },
    seledAll:function(obj, parentTag){
        var checkAllObj =  $(obj);
        /*
        checkAllObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function(){
            if(!$(this).prop('disabled')){
                $(this).prop('checked', checkAllObj.prop('checked'));
            }
        });
        */
        checkAllObj.closest(parentTag).find('.check_item').each(function(){
            if(!$(this).prop('disabled')){
                $(this).prop('checked', checkAllObj.prop('checked'));
            }
        });
    },
    seledSingle:function(obj, parentTag) {// 单选点击
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
        checkObj.closest(parentTag).find('.check_item').each(function () {
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
        checkObj.closest(parentTag).find('.check_all').each(function () {
            $(this).prop('checked', allChecked);
        });

    },
    addParams:function(obj) {// 增加一行参数
        var btnObj = $(obj);
        $('.staff_td').find('.data_list').append($('#param_tr').find('tbody').html());
        // 初始化选择参数类型数据
        initSelectParamType();
    }
};
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

    var module_name = $('input[name=module_name]').val();
    if(!judge_validate(4,'模块名称',module_name,true,'length',1,30)){
        return false;
    }

    var remarks = $('textarea[name=remarks]').val();
    if(!judge_validate(4,'备注说明',remarks,false,'length',2,1000)){
        return false;
    }

    // 可用参数进行判断--可以为空
    var tr_num = 1;
    var tr_has_err = false;
    var params_code_arr = [];
    $('.staff_td').find('.data_list').find('tr').each(function () {
        var trObj = $(this);
        var row_text = "第" + tr_num + '条记录：';
        var param_name = trObj.find('input[name="param_name[]"]').val();
        if(!judge_validate(4, row_text + '参数名称',param_name,true,'length',1,30)){
            tr_has_err = true;
            return false;
        }
        var param_code = trObj.find('input[name="param_code[]"]').val();
        if(!judge_validate(4,row_text + '参数代码',param_code,true,'length',1,50)){
            tr_has_err = true;
            return false;
        }
        if(params_code_arr.indexOf(param_code) >= 0) {//存在
            layer_alert(row_text + "参数代码已重复",3,0);
            tr_has_err = true;
            return false;
        }
        params_code_arr.push(param_code);

        var param_type = trObj.find('select[name="param_type[]"]').val();
        var judge_seled = judge_validate(1,row_text + '参数类型',param_type,true,'custom',/^[1248]$/,"");
        if(judge_seled != ''){
            layer_alert("请选择" + row_text + "参数类型",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            tr_has_err = true;
            return false;
        }
        if(param_type == 1){// 日期时间
            var date_time_format = trObj.find('input[name="date_time_format[]"]').val();
            if(!judge_validate(4,row_text + '日期时间格式化',date_time_format,true,'length',1,20)){
                tr_has_err = true;
                return false;
            }
        }
        if(param_type == 2){// 固定值--可以为空
            var fixed_val = trObj.find('input[name="fixed_val[]"]').val();
            if(!judge_validate(4,'固定值',fixed_val,false,'length',1,500)){
                tr_has_err = true;
                return false;
            }
        }
        tr_num++;
    });
    if(tr_has_err){
        return false;
    }


    var open_status = $('input[name=open_status]:checked').val() || '';
    var judge_seled = judge_validate(1,'开启状态',open_status,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择开启状态",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var sort_num = $('input[name=sort_num]').val();
    if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
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
