
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
    var resourceListObj = $('#resource_block');// $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show_course', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    resetPhone();
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
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

// 通过父页面弹出支付页面
function payOrderStaff(courseOrderId){
    consoleLogs(['-payOrderStaff-courseOrderId-', courseOrderId]);
    // 执行父类的支付方法
    window.parent.otheraction.payByIds(null, courseOrderId);
    // 关闭当前页面
    parent_reset_list();
}
// 每次加入新的员工时，更新图片
function resetPhone(){
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
    var resourceListObj = $('.data_list').find('tr');// $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    initPic();
    layer.close(layer_index);//手动关闭
}
//业务逻辑部分
var otheraction = {
    selectUser: function(obj){// 选择员工
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_USER_URL;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择员工';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,950,500,0);
        return false;
    },
    del : function(obj, parentTag){// 删除-员工
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var trObj = recordObj.closest(parentTag);// 'tr'
            trObj.remove();
            autoCountStaffNum();
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
            autoCountStaffNum();
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

    }

    // seledAll:function(obj){
    //     var checkAllObj =  $(obj);
    //     /*
    //     checkAllObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function(){
    //         if(!$(this).prop('disabled')){
    //             $(this).prop('checked', checkAllObj.prop('checked'));
    //         }
    //     });
    //     */
    //     checkAllObj.closest('#' + DYNAMIC_TABLE).find('.check_item').each(function(){
    //         if(!$(this).prop('disabled')){
    //             $(this).prop('checked', checkAllObj.prop('checked'));
    //         }
    //     });
    //     return false;
    // },
    // seledSingle:function(obj) {// 单选点击
    //     var checkObj = $(obj);
    //     var allChecked = true;
    //     /*
    //      checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
    //         if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
    //             // $(this).prop('checked', checkAllObj.prop('checked'));
    //             allChecked = false;
    //             return false;
    //         }
    //     });
    //     */
    //     checkObj.closest('#' + DYNAMIC_TABLE).find('.check_item').each(function () {
    //         if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
    //             // $(this).prop('checked', checkAllObj.prop('checked'));
    //             allChecked = false;
    //             return false;
    //         }
    //     });
    //     // 全选复选操选中/取消选中
    //     /*
    //     checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
    //         if (!$(this).prop('disabled') && $(this).val() == ''  ) {
    //             $(this).prop('checked', allChecked);
    //             return false;
    //         }
    //     });
    //     */
    //     checkObj.closest('#' + DYNAMIC_TABLE).find('.check_all').each(function () {
    //         $(this).prop('checked', allChecked);
    //     });
    //     return false;
    // }
};

// 初始化答案列表
// data_list 数据对象 {'data_list':[{}]}
// type类型 1 全替换 2 追加到后面 3 返回html
function initAnswer(class_name, data_list, type){
    var htmlStr = resolve_baidu_template(DYNAMIC_BAIDU_TEMPLATE,data_list,'');//解析
    if(type == 3) return htmlStr;
    //alert(htmlStr);
    //alert(body_data_id);
    if(type == 1){
        $('.'+ class_name).find('.' + DYNAMIC_TABLE_BODY).html(htmlStr);
    }else if(type == 2){
        $('.'+ class_name).find('.' + DYNAMIC_TABLE_BODY).append(htmlStr);
    }
}

// 获得参考人员数量
function autoCountStaffNum(){
    var total = 0;
    $('.staff_td').each(function () {
        var departmentObj = $(this);
        var staff_num = departmentObj.find('.data_list').find('tr').length;
        console.log('staff_num',staff_num);
        departmentObj.find('input[name="staff_num[]"]').val(staff_num);
        departmentObj.find('.staff_num').html(staff_num);
        total += parseInt(staff_num);
    });
    $('.subject_num').html(total);

}

// 获得员工id 数组
function getSelectedStaffIds(){
    var staff_ids = [];
    $('.staff_td').find('.data_list').find('input[name="staff_ids[]"]').each(function () {
        var staff_id = $(this).val();
        staff_ids.push(staff_id);
    });
    console.log('staff_ids' , staff_ids);
    return staff_ids;
}

// 取消
// staff_id 试题id
function removeStaff(staff_id){
    $('.staff_td').find('.data_list').find('input[name="staff_ids[]"]').each(function () {

        var tem_staff_id = $(this).val();
        if(staff_id == tem_staff_id){
            $(this).closest('tr').remove();
            return ;
        }
    });
    autoCountStaffNum();
}

// 增加
// staff_id 试题id, 多个用,号分隔
function addStaff( staff_id){
    console.log('addStaff', staff_id);
    if(staff_id == '') return ;
    // 去掉已经存在的记录id
    var selected_ids = getSelectedStaffIds();
    var staff_id_arr = staff_id.split(",");
    //差集
    var diff_arr = staff_id_arr.filter(function(v){ return selected_ids.indexOf(v) == -1 });
    staff_id = diff_arr.join(',');
    if(staff_id == '') return ;
    var course_id = $('input[name=id]').val();

    var data = {};
    data['course_id'] = course_id;
    data['user_ids'] = staff_id;
    consoleLogs([data]);
    var layer_index = layer.load();
    $.ajax({
        'async': false,// true,//false:同步;true:异步
        'type' : 'POST',
        'url' : AJAX_USER_ADD_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                var staff_list = ret.result;
                console.log('staff_list', staff_list);
                var data_list = {
                    'data_list': staff_list,
                };
                // 解析数据
                initAnswer('staff_td', data_list, 2);
                resetPhone();
                autoCountStaffNum();
            }
            layer.close(layer_index)//手动关闭
        }
    });
}

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
        return false;
    }
    // 收款开通类型
    // if(!judge_list_checked('data_list',1)) {//没有选中的
    //     layer_alert('请选择学员！',3,0);
    //     return false;
    // }
    var staff_num = 0;
    consoleLogs(['--tr length-', $('.data_list').find('tr').length]);
    var staff_has_err = false;
    $('.data_list').find('tr').each(function(){
        var trObj = $(this);
        var is_joined = trObj.data('is_joined');
        consoleLogs(['--is_joined-', is_joined]);
        var real_name = trObj.data('real_name');
        consoleLogs(['--real_name-', real_name]);
        var staff_ids = trObj.find("input[name='staff_ids[]']").val() || '';
        consoleLogs(['--staff_ids-', staff_ids]);
        var certificate_company = trObj.find("input[name='certificate_company[]']").val() || '';
        consoleLogs(['--certificate_company-', certificate_company]);
        var resourceObj = trObj.find("input[name='resource_id[]']");
        if( (is_joined & 1) == 1 ){
            staff_has_err = true;
            layer_alert(real_name + '已报名，不可重复报名，请移除！',3,0);
            return false;
        }
        if(!judge_validate(4,real_name + '-证书所属单位',certificate_company,true,'length',1,100)){
            staff_has_err = true;
            return false;
        }
        // 判断是否有证件照
        consoleLogs(['--resourceObj.length-', resourceObj.length]);
        if(resourceObj.length <= 0 ){
            staff_has_err = true;
            layer_alert(real_name + '没有证件照，不可报名，请先上传证件照！',3,0);
            return false;
        }

        staff_num++;
    });
    if(staff_has_err) return false;
    if(staff_num <= 0 ){
        layer_alert('请选择学员！',3,0);
        return false;
    }

    var contacts = $('input[name=contacts]').val();
    if(!judge_validate(4,'联络人员',contacts,true,'length',1,50)){
        return false;
    }

    var tel = $('input[name=tel]').val();
    if(!judge_validate(4,'联络人电话',tel,true,'length',5,30)){
        return false;
    }

    // var invoice_buyer_id = $('input[name=invoice_buyer_id]:checked').val() || '';
    // var judge_seled = judge_validate(1,'发票抬头',invoice_buyer_id,false,'digit',"","");
    // if(judge_seled != ''){
    //     layer_alert("请选择发票抬头",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    ajax_save(id);
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
                layer.msg('报名成功！', {
                    icon: 1,
                    shade: 0.3,
                    time: 3000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    if(id > 0) reset_total = false;
                    var courseOrderId = ret.result;
                    consoleLogs(['-courseOrderId-', courseOrderId]);
                    // 跳转到支付页
                    // var weburl = PAY_URL + '?course_order_id='+ courseOrderId;
                    // go(weburl);

                    // 通过父页面弹出支付页面
                    payOrderStaff(courseOrderId);

                    // parent_reset_list_iframe_close(reset_total);// 刷新并关闭
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

(function() {
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    var now_staff = item.now_staff;");
    document.write("    can_modify = true;");
    document.write("    var can_add = true;");
    document.write("   if( (item.is_joined & 1) == 1){  ");// 有正在进行的
    document.write("      var can_add = false; ");
    document.write("    } ");
    document.write("    %>");
    document.write("    <tr <%if( !can_add ){%> style=\"color:red;font-weight:bold;\" <%}%> data-is_joined=\"<%=item.is_joined%>\" data-real_name=\"<%=item.real_name%>\"  >");
    document.write("        <td>");
    document.write("            <label class=\"pos-rel\">");
    document.write("                <input onclick=\"otheraction.seledSingle(this , \'.table2\')\" type=\"checkbox\" class=\"ace check_item\" value=\"<%=item.id%>\">");
    document.write("                <span class=\"lbl\"><\/span>");
    document.write("            <\/label>");
    document.write("            <input type=\"hidden\" name=\"staff_ids[]\" value=\"<%=item.id%>\" <%if( !can_add ){%> disabled <%}%> \/>");
    // document.write("            <input type=\"hidden\" name=\"staff_history_ids[]\" value=\"<%=item.staff_history_id%>\"\/>");
    // document.write("            <input type=\"hidden\" name=\"department_ids[]\" value=\"<%=item.department_id%>\"\/>");
    // document.write("            <input type=\"hidden\" name=\"department_names[]\" value=\"<%=item.department_name%>\"\/>");
    // document.write("            <input type=\"hidden\" name=\"group_ids[]\" value=\"<%=item.group_id%>\"\/>");
    // document.write("            <input type=\"hidden\" name=\"group_names[]\" value=\"<%=item.group_name%>\"\/>");
    // document.write("            <input type=\"hidden\" name=\"position_ids[]\" value=\"<%=item.position_id%>\"\/>");
    // document.write("            <input type=\"hidden\" name=\"position_names[]\" value=\"<%=item.position_name%>\"\/>");
    document.write("        <\/td>");
    document.write("        <td><%=item.real_name%>（<%=item.sex_text%>）<\/td>");
    // document.write("        <td><\/td>");
    document.write("        <td><input type=\"text\" name=\"certificate_company[]\" value=\"<%=item.user_company_name%>\" placeholder=\"请输入证书所属单位\"  <%if( !can_add ){%> disabled <%}%> \/><\/td>");
    document.write("        <td>");
    document.write("          <span class=\"resource_list\"  style=\"display: none;\"><%=JSON.stringify(item.resource_list)%></span>");
    document.write("          <span  class=\"resource_show\"></span>");
    document.write("        <\/td>");
    document.write("        <td><%=item.mobile%><\/td>");
    document.write("        <td><%=item.id_number%><\/td>");
    document.write("        <td><%=item.is_joined_text%><\/td>");
    document.write("        <td>");
    // document.write("            <%if( now_staff == 2 || now_staff == 4){%>");
    // document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.edit(this, \'tr\', <%=item.staff_id%>)\">");
    // document.write("                <i class=\"ace-icon fa fa-pencil bigger-60 pink\"> 更新[员工已更新]<\/i>");
    // document.write("            <\/a>");
    // document.write("            <%}%>");
    // document.write("            <%if( now_staff == 1){%>");
    // document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.del(this, \'tr\')\">");
    // document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60 wrong\"> 删除[员工已删]<\/i>");
    // document.write("            <\/a>");
    // document.write("            <%}%>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.del(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60\"> 移除<\/i>");
    document.write("            <\/a>");
    document.write("        <\/td>");
    document.write("    <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
