
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

    $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
    popSelectInit();// 初始化选择弹窗
    // window.location.href 返回 web 主机的域名，如：http://127.0.0.1:8080/testdemo/test.html?id=1&name=test
    autoRefeshList(window.location.href, IFRAME_TAG_KEY, IFRAME_TAG_TIMEOUT);// 根据设置，自动刷新列表数据【每隔一定时间执行一次】
});

//重载列表
//is_read_page 是否读取当前页,否则为第一页 true:读取,false默认第一页
// ajax_async ajax 同步/导步执行 //false:同步;true:异步  需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
// do_num 调用时: 1 初始化页面时[默认];2 初始化页面后的调用
function reset_list_self(is_read_page, ajax_async, reset_total, do_num){
    console.log('is_read_page', typeof(is_read_page));
    console.log('ajax_async', typeof(ajax_async));
    reset_list(is_read_page, false, reset_total, do_num);
    // initList();
}


//业务逻辑部分
var otheraction = {
    selectCompany: function(obj){// 选择商家
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_COMPANY_URL;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择所属企业';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,700,450,0);
        return false;
    },
    sureOrder: function(obj){// 确认--批量
        var recordObj = $(obj);
        var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
        otheraction.sureOrderByIds(obj, ids);
    },
    sureOrderByIds: function(obj, ids) {
        if( ids == ''){
            err_alert('请选择需要操作的数据');
            return false;
        }
        var operateText = '确认';
        var index_query = layer.confirm('确定' + operateText + '所选记录？操作后不可变更！', {
            btn: ['确定','取消'] //按钮
        }, function(){
            // var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            //ajax开启数据
            other_operate_ajax('sure_order', ids, operateText, {});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    finishOrder: function(obj){// 服务完成--批量
        var recordObj = $(obj);
        var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
        otheraction.finishOrderByIds(obj, ids);
    },
    finishOrderByIds: function(obj, ids) {
        if( ids == ''){
            err_alert('请选择需要操作的数据');
            return false;
        }
        var operateText = '服务完成';
        var index_query = layer.confirm('确定' + operateText + '所选记录？操作后不可变更！', {
            btn: ['确定','取消'] //按钮
        }, function(){
            // var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            //ajax开启数据
            other_operate_ajax('finish_order', ids, operateText, {});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    invoiceSelected: function(obj){// 电子发票--批量
        var recordObj = $(obj);
        var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
        otheraction.invoiceByIds(obj, ids);
    },
    invoiceByIds: function(obj, ids) {
        if( ids == ''){
            err_alert('请选择需要操作的数据');
            return false;
        }
        //获得表单各name的值
        var weburl = INVOICE_URL + '?id='+ ids;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '电子发票';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,950,510,5);
        // commonaction.browse_file(weburl, tishi,950,510, 5);
        return false;
    },
    invoiceCancelByIds: function(obj, ids) {
        if( ids == ''){
            err_alert('请选择需要操作的数据');
            return false;
        }
        //获得表单各name的值
        var weburl = INVOICE_CANCEL_URL + '?id='+ ids;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '电子发票全额冲红';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,950,510,5);
        // commonaction.browse_file(weburl, tishi,950,510, 5);
        return false;
    },
    showInvoices: function(obj, order_no, company_id) {
        if( order_no == ''){
            err_alert('请选择需要操作的数据');
            return false;
        }
        //获得表单各name的值
        var weburl = INVOICE_SHOW_URL + '?hidden_option=1&company_id=' + company_id + '&order_no='+ order_no;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '订单号【' + order_no + '】电子发票';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,950,510,0);
        // commonaction.browse_file(weburl, tishi,950,510, 5);
        return false;
    }
};

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
        case 'sure_order'://批量确认
            // operate_txt = "批量开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            reset_total = false;
            ajax_url = SURE_ORDER_URL;// "/pms/Supplier/ajax_del?operate_type=2";
            break;
        case 'finish_order'://批量服务完成
            // operate_txt = "批量开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            reset_total = false;
            ajax_url = FINISH_ORDER_URL;// "/pms/Supplier/ajax_del?operate_type=2";
            break;
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
// 初始化，来决定*是显示还是隐藏
function popSelectInit(){

    $('.select_close').each(function(){
        let closeObj = $(this);
        let idObj = closeObj.siblings(".select_id");
        if(idObj.length > 0 && idObj.val() != '' && idObj.val() != '0'  ){
            closeObj.show();
        }else{
            closeObj.hide();
        }
    });
}

// 清空
function clearSelect(Obj){
    let closeObj = $(Obj);
    console.log('closeObj=' , closeObj);

    var index_query = layer.confirm('确定移除？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        // 清空id
        let idObj = closeObj.siblings(".select_id");
        if(idObj.length > 0 ){
            idObj.val('');
        }
        // 清空名称文字
        let nameObj = closeObj.siblings(".select_name");
        if(nameObj.length > 0 ){
            nameObj.html('');
        }
        closeObj.hide();
        layer.close(index_query);
    }, function(){
    });
}

// 获得选中的企业id 数组
function getSelectedCompanyIds(){
    var company_ids = [];
    var company_id = $('input[name=company_id]').val();
    company_ids.push(company_id);
    console.log('company_ids' , company_ids);
    return company_ids;
}

// 取消
// company_id 企业id
function removeCompany(company_id){
    var seled_company_id = $('input[name=company_id]').val();
    if(company_id == seled_company_id){
        $('input[name=company_id]').val('');
        $('.company_name').html('');
        $('.company_id_close').hide();
    }
}

// 增加
// company_id 企业id, 多个用,号分隔
function addCompany(company_id, company_name){
    $('input[name=company_id]').val(company_id);
    $('.company_name').html(company_name);
    $('.company_id_close').show();
}

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    //document.write("        var can_modify = false;");
   // document.write("        if( item.issuper==0 ){");
    document.write("        can_modify = true;");
    //document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
    document.write("            <td>");
    document.write("                <label class=\"pos-rel\">");
    document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    document.write("                  <span class=\"lbl\"><\/span>");
    document.write("                <\/label>");
    document.write("            <\/td>");
    // document.write("            <td><%=item.id%><\/td>");
    document.write("            <td><%=item.order_no%><hr/><%=item.company_name%><\/td>");
    document.write("            <td><%=item.order_type_text%><hr/><%=item.remarks%><hr/><%=item.invoice_template_name%><\/td>");
    document.write("            <td><%=item.pay_company_name%><hr/><%=item.pay_name%><\/td>");
    document.write("            <td><%=item.total_amount%><hr/>￥<%=item.total_price%><hr/><%=item.invoice_buyer_name%><\/td>");
    document.write("            <td>￥<%=item.total_price_goods%><hr/>￥<%=item.total_price_discount%><hr/><%=item.invoice_status_text%><\/td>");
    document.write("            <td><%=item.order_time%><hr/><%=item.pay_time%><\/td>");
    document.write("            <td><%=item.has_refund_text%><hr/><%=item.refund_time%><hr/><%=item.invoice_result_text%><\/td>");
    document.write("            <td>￥<%=item.refund_price%><hr/>￥<%=item.refund_price_frozen%><\/td>");
    document.write("            <td>￥<%=item.payment_amount%><hr/>￥<%=item.change_amount%><hr/>￥<%=item.check_price%>(<%=item.order_status_text%>)<\/td>");
    document.write("            <td><%=item.sure_time%><hr/><%=item.check_time%><hr/><%=item.cancel_time%><\/td>");
    document.write("            <td>");
    // document.write("                <%if( true){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <%if(item.order_status == 2 ){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.sureOrderByIds(this,<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon   fa fa-check-circle  bigger-60\"> 确认<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <%if(item.order_status == 4 ){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.finishOrderByIds(this,<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon  fa fa-universal-access bigger-60\"> 服务完成<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    document.write("                <%if( (item.order_status & (2 | 4 | 8)) > 0  && item.invoice_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.invoiceByIds(this,<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-vcard-o bigger-60\"> 开电子发票<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( (item.invoice_result & (2 | 4 )) > 0){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.showInvoices(this,'<%=item.order_no%>','<%=item.company_id%>')\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看电子发票<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.invoice_status == 4 ){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.invoiceCancelByIds(this,<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-trash-o bigger-60\"> 发票全额冲红<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%if( can_modify){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
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
