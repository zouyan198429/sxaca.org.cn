
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
    paySelected: function(obj){// 缴费--批量
        var recordObj = $(obj);
        var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
        otheraction.payByIds(obj, ids);
    },
    payByIds: function(obj, ids) {
        if( ids == ''){
            err_alert('请选择需要操作的数据');
            return false;
        }
        //获得表单各name的值
        var weburl = PAY_URL + '?course_order_id='+ ids;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '缴费';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,700,450,5);
        return false;
    }
};
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
    document.write("            <td><%=item.course_name%><\/td>");
    // document.write("            <td><%=item.invoice_template_name%><%=item.company_name%>(<%=item.company_grade_text%>)<\/td>");
    
	// <hr/><%=item.cancel_num%>
    //document.write("            <td><%=item.finish_num%><\/td>");
	// <%=item.joined_class_num%><hr/>
    document.write("            <td><%=item.contacts%>-<%=item.tel%><\/td>");
    document.write("            <td>￥<%=item.price%><\/td>");
	document.write("            <td><%=item.join_num%><\/td>");
	document.write("            <td>￥<%=item.price_total%><\/td>");    
	// <hr/><%=item.join_class_status_text%>
    document.write("            <td><%=item.company_status_text%><\/td>");
    document.write("            <td><%=item.order_time%><\/td>");
	document.write("            <td><%=item.pay_status_text%><\/td>");
	// document.write("            <td><%=item.pay_time%><\/td>");
    document.write("            <td>");
    document.write("                <%if( true){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if(can_modify &&  item.company_status != 4 &&  item.pay_status != 4){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.payByIds(this,<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-jpy bigger-60\"> 缴费<\/i>");
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
