
var SUBMIT_FORM = true;//防止多次点击提交
var IS_LAYUI_PAGE = isLayuiIframePage();// 是否Layui的iframe弹出层；true: 是； false:不是
var IS_FRAME_PAGE = isIframePage();// 是否iframe弹出层；true: 是； false:不是

//获取当前窗口索引
var PARENT_LAYER_INDEX = null;// getParentLayerIndex();
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

    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);

    // window.location.href 返回 web 主机的域名，如：http://127.0.0.1:8080/testdemo/test.html?id=1&name=test
    // autoRefeshList(window.location.href, IFRAME_TAG_KEY, IFRAME_TAG_TIMEOUT);// 根据设置，自动刷新列表数据【每隔一定时间执行一次】
});

//重载列表
//is_read_page 是否读取当前页,否则为第一页 true:读取,false默认第一页
// ajax_async ajax 同步/导步执行 //false:同步;true:异步  需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
// do_num 调用时: 1 初始化页面时[默认];2 初始化页面后的调用
function reset_list_self(is_read_page, ajax_async, reset_total, do_num){
    console.log('is_read_page', typeof(is_read_page));
    console.log('ajax_async', typeof(ajax_async));
    var layer_index = layer.load();
    reset_list(is_read_page, false, reset_total, do_num);
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

    initList();
    initPic();
    layer.close(layer_index);//手动关闭
}
window.onload = function() {
    $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
//     initPic();
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

// 初始化
function initList(){
    // 获得选中的员工id 数组
    var SELECTED_IDS = parent.getSelectedStaffIds();
    console.log('SELECTED_IDS',SELECTED_IDS);
    $('#data_list').find('tr').each(function () {
        var trObj = $(this);
        // console.log(trObj.html());
        var checkedObj = trObj.find('.check_item');
        console.log('checkedObj', checkedObj.length);
        var staff_id = checkedObj.val();
        console.log('staff_id', staff_id);
        if(SELECTED_IDS.indexOf(staff_id) !== -1){// 已选
            trObj.find('.add').hide();
            trObj.find('.del').show();
            checkedObj.prop('disabled',true);
            checkedObj.prop('checked',false);
        }else{// 未选
            trObj.find('.add').show();
            trObj.find('.del').hide();
            checkedObj.prop('disabled',false);
        }

    });
}

//业务逻辑部分
var otheraction = {
    addBatch : function(obj){// 批量增加员工
        // var checkAllObj =  $(obj);
        var index_query = layer.confirm('确定增加选中记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            if(ids == '') err_alert('请选择需要操作的记录');
            parent.addStaff(ids);
            initList();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    addBatchSearch : function(obj){// 批量增加员工- 查询条件
        // var checkAllObj =  $(obj);
        var index_query = layer.confirm('确定添加当前查询条件所有记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            // if(!$('#dynamic-table').find('.check_all').is(':checked')) {
            //     $('#dynamic-table').find('.check_all').trigger("click");
            // }// 触发搜索事件
            // var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            // if(ids == '') err_alert('请选择需要操作的记录');
            // parent.addStaff(ids);
            // initList();
            // layer.close(index_query);
            //获得搜索表单的值
            append_sure_form(SURE_FRM_IDS,FRM_IDS);//把搜索表单值转换到可以查询用的表单中
            $('.search_frm').trigger("click");// 触发搜索事件
            //获得表单各name的值
            var data = get_frm_values(SURE_FRM_IDS);// {}
            console.log(data);
            var layer_index = layer.load();
            $.ajax({
                'async': false,// true,//false:同步;true:异步
                'type' : 'POST',
                'url' : AJAX_SEARCH_IDS_URL,
                'data' : data,
                'dataType' : 'json',
                'success' : function(ret){
                    console.log('ret',ret);
                    if(!ret.apistatus){//失败
                        //alert('失败');
                        err_alert(ret.errorMsg);
                    }else{//成功
                        var ids = ret.result;
                        console.log('ids', ids);
                        //var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
                        if(ids == '') err_alert('请选择需要操作的记录');
                        parent.addStaff(ids);
                        // initList();
                        parent_reset_list();// 关闭弹窗
                    }
                    layer.close(layer_index)//手动关闭
                }
            });
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    add : function(id){// 增加单个试题
        var index_query = layer.confirm('确定增加当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            parent.addStaff(id + '');
            initList();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    del : function(id){// 取消
        var index_query = layer.confirm('确定取消当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            parent.removeStaff(id);
            initList();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
};

//业务逻辑部分
// var otheraction = {
    // open : function(id, open_status){
    //     var operateText = '审核通过';
    //     if(open_status === 4){
    //         operateText = '审核不通过';
    //     }
    //     var index_query = layer.confirm('确定' + operateText + '当前记录？', {
    //         btn: ['确定','取消'] //按钮
    //     }, function(){
    //         other_operate_ajax('open', id, operateText, {'open_status': open_status});
    //         layer.close(index_query);
    //     }, function(){
    //     });
    //     return false;
    // },
    // openSelected: function(obj, open_status){// 开启选中的码
    //     var recordObj = $(obj);
    //     var operateText = '审核通过';
    //     if(open_status === 4){
    //         operateText = '审核不通过';
    //     }
    //     var index_query = layer.confirm('确定' + operateText + '当前记录？', {
    //         btn: ['确定','取消'] //按钮
    //     }, function(){
    //         var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
    //         //ajax开启数据
    //         other_operate_ajax('batch_open', ids, operateText, {'open_status': open_status});
    //         layer.close(index_query);
    //     }, function(){
    //     });
    //     return false;
    // },
    // frozen : function(id, account_status){
    //     var operateText = '解冻';
    //     if(account_status === 2){
    //         operateText = '冻结';
    //     }
    //     var index_query = layer.confirm('确定' + operateText + '当前记录？', {
    //         btn: ['确定','取消'] //按钮
    //     }, function(){
    //         other_operate_ajax('frozen', id, operateText, {'account_status': account_status});
    //         layer.close(index_query);
    //     }, function(){
    //     });
    //     return false;
    // },
    // frozenSelected: function(obj, account_status){// 开启选中的码
    //     var recordObj = $(obj);
    //     var operateText = '解冻';
    //     if(account_status === 2){
    //         operateText = '冻结';
    //     }
    //     var index_query = layer.confirm('确定' + operateText + '当前记录？', {
    //         btn: ['确定','取消'] //按钮
    //     }, function(){
    //         var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
    //         //ajax开启数据
    //         other_operate_ajax('batch_frozen', ids, operateText, {'account_status': account_status});
    //         layer.close(index_query);
    //     }, function(){
    //     });
    //     return false;
    // },
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
    // },
    // iframeImport : function(id){// 弹窗导入
    //     //获得表单各name的值
    //     var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
    //     console.log(IFRAME_IMPORT_URL);
    //     console.log(data);
    //     var url_params = get_url_param(data);// parent.get_url_param(data)
    //     var weburl = IFRAME_IMPORT_URL + id + '?' + url_params;
    //     console.log(weburl);
    //     // go(SHOW_URL + id);
    //     // location.href='/pms/Supplier/show?supplier_id='+id;
    //     // var weburl = SHOW_URL + id;
    //     // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
    //     var tishi = "导入";
    //     layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
    //     return false;
    // }
// };


//操作
// params 其它参数对象  {}
function other_operate_ajax(operate_type, id, operate_txt, params){
    params = params || {};
    if(operate_type == '' || id == ''){
        err_alert('请选择需要操作的数据');
        return false;
    }
    operate_txt = operate_txt || "";
    var data = params;// {};
    var ajax_url = "";
    var reset_total = true;// 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
    switch(operate_type)
    {
        // case 'open'://审核通过/不通过
        //     // operate_txt = "开启";
        //     // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
        //     // 合并对象
        //     objAppendProps(data, {'id':id}, true);
        //     ajax_url = OPEN_OPERATE_URL;// /pms/Supplier/ajax_del?operate_type=1
        //     reset_total = false;
        //     break;
        // case 'batch_open'://批量开启
        //     // operate_txt = "批量开启";
        //     // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
        //     // 合并对象
        //     objAppendProps(data, {'id':id}, true);
        //     reset_total = false;
        //     ajax_url = OPEN_OPERATE_URL;// "/pms/Supplier/ajax_del?operate_type=2";
        //     break;
        // case 'frozen'://冻结/解冻
        //             // operate_txt = "开启";
        //             // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
        //             // 合并对象
        //     objAppendProps(data, {'id':id}, true);
        //     ajax_url = ACCOUNT_STATUS_URL;// /pms/Supplier/ajax_del?operate_type=1
        //     reset_total = false;
        //     break;
        // case 'batch_frozen'://批量冻结/解冻
        //     // operate_txt = "批量开启";
        //     // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
        //     // 合并对象
        //     objAppendProps(data, {'id':id}, true);
        //     reset_total = false;
        //     ajax_url = ACCOUNT_STATUS_URL;// "/pms/Supplier/ajax_del?operate_type=2";
        //     break;
        default:
            break;
    }
    console.log('ajax_url:',ajax_url);
    console.log('data:',data);
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    $.ajax({
        'type' : 'POST',
        'url' : ajax_url,//'/pms/Supplier/ajax_del',
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret:',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                // countdown_alert(ret.errorMsg,0,5);
                layer_alert(ret.errorMsg,3,0);
            }else{//成功
                var msg = ret.errorMsg;
                if(msg === ""){
                    msg = operate_txt+"成功";
                }
                // countdown_alert(msg,1,5);
                layer_alert(msg,1,0);
                // reset_list(true, true);
                console.log(LIST_FUNCTION_NAME);
                eval( LIST_FUNCTION_NAME + '(' + true +', ' + true +', ' + reset_total + ', 2)');
            }
            layer.close(layer_index);//手动关闭
        }
    });
}

// // 获得选中的企业id 数组
// function getSelectedCompanyIds(){
//     var company_ids = [];
//     var company_id = $('input[name=company_id]').val();
//     company_ids.push(company_id);
//     console.log('company_ids' , company_ids);
//     return company_ids;
// }

// 取消
// company_id 企业id
// function removeCompany(company_id){
//     var seled_company_id = $('input[name=company_id]').val();
//     if(company_id == seled_company_id){
//         $('input[name=company_id]').val('');
//         $('.company_name').html('');
//     }
// }

// 增加
// company_id 企业id, 多个用,号分隔
// function addCompany(company_id, company_name){
//     $('input[name=company_id]').val(company_id);
//     $('.company_name').html(company_name);
// }

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    document.write("        var can_modify = false;");
    document.write("        if( item.issuper!=1 ){");
    document.write("        can_modify = true;");
    document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
    // document.write("            <td>");
    // document.write("                <label class=\"pos-rel\">");
    // document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    // document.write("                  <span class=\"lbl\"><\/span>");
    // document.write("                <\/label>");
    // document.write("            <\/td>");
    // document.write("            <td><%=item.id%><\/td>");
    // document.write("            <td><%=item.client_id%><\/td>");

    document.write("            <td><%=item.real_name%>(<%=item.sex_text%>)<\/td>");
    document.write("            <td><%=item.mobile%><\/td>");
	// <%=item.city_name%><hr/>
    //document.write("            <td><%=item.city_name%><\/td>");
    //document.write("            <td><%=item.email%><\/td>");
    //document.write("            <td><%=item.qq_number%><\/td>");
    // document.write("            <td><%=item.user_company_name%><\/td>");
    document.write("            <td>");
    document.write("               <span class=\"resource_list\"  style=\"display: none;\"><%=JSON.stringify(item.resource_list)%></span>");
    document.write("               <span  class=\"resource_show\"></span>");
    document.write("            <\/td>");
    document.write("            <td><%=item.role_num_text%><\/td>");
	// <%=item.position_name%><hr/>
    // document.write("            <td>");
    // document.write("            <%=item.sign_range%>(<%=item.sign_is_food_text%>)");
    // document.write("            <hr\/><%=item.sign_status_text%>");
    // document.write("            <\/td>");
    // document.write("            <td><%=item.is_perfect_text%><hr/><%=item.open_status_text%><\/td>");
    // document.write("            <td><%=item.role_status_text%><hr/><%=item.account_status_text%><\/td>");
    // document.write("            <td><%=item.lastlogintime%><\/td>");<%=item.lastlogintime%><hr/>
    // document.write("            <td><%=item.created_at%><\/td>");
    // document.write("            <td><%=item.updated_at%><\/td>");
    // document.write("            <td><%=item.sort_num%><\/td>");
    document.write("            <td>");
    // document.write("                <%if( false){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <%if(can_modify && item.open_status == 1){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.open(<%=item.id%>, 2)\">");
    // document.write("                    <i class=\"ace-icon bigger-60\">审核通过<\/i>");
    // document.write("                <\/a>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.open(<%=item.id%>, 4)\">");
    // document.write("                    <i class=\"ace-icon bigger-60\">审核不通过<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <%if(can_modify &&  item.account_status == 1){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.frozen(<%=item.id%>, 2)\">");
    // document.write("                    <i class=\"ace-icon bigger-60\"> 冻结<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <%if( can_modify && item.account_status == 2){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.frozen(<%=item.id%>, 1)\">");
    // document.write("                    <i class=\"ace-icon bigger-60\"> 解冻<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%if( can_modify){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info add \" onclick=\"otheraction.add(<%=item.id%>)\">");
    document.write("                <i class=\"ace-icon fa fa-plus bigger-60\"> 选择<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info del pink \" onclick=\"otheraction.del(<%=item.id%>)\">");
    document.write("               <i class=\"ace-icon fa fa-trash-o bigger-60\"> 取消<\/i>");
    document.write("            <\/a>");
    document.write("                <%if( false){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.smsByIds(this, <%=item.id%>, 0, 4, 0, 0)\">");
    document.write("                    <i class=\"ace-icon  fa fa-mobile bigger-60\"> 发送短信<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
