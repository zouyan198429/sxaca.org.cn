
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
    formatList();
}

// 格式化每一列数据
function formatList() {
    console.log('格式化每一列数据！');
    $('#' + DYNAMIC_TABLE).find('tr').each(function(){
        let trObj = $(this);

        // 状态
        let tdObjStatus = trObj.find('.status_text');
        console.log('tdObjStatus = ', tdObjStatus);
        if(tdObjStatus.length > 0){
            let status = tdObjStatus.html();
            console.log('status = ', status);
            switch(status){
                case '待开始':
                    tdObjStatus.addClass('aaa1');
                    break;
                case '报名中':
                    tdObjStatus.addClass('aaa2');
                    break;
                case '进行中':
                    tdObjStatus.addClass('aaa3');
                    break;
                case '已结束':
                    tdObjStatus.addClass('aaa4');
                    break;
                case '已取消':
                    tdObjStatus.addClass('aaa5');
                    break;
                default:
                    break;
            }
        }

    });
}

//业务逻辑部分
var otheraction = {
    admin : function(id){// 项目管理
        var weburl = ABILITYS_ADMIN_URL + id ;
        console.log(weburl);
        goOpenUrl(weburl,'_blank');
        return false;
    },
    export_join: function(id){// 导出报名企业
        var index_query = layer.confirm('确定导出当前记录报名企业信息？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            // var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            // console.log('ids',ids);
            // if( ids==''){
            //     err_alert('请选择需要操作的数据');
            //     return false;
            // }
            // //获得表单各name的值
            // var data = get_frm_values(SURE_FRM_IDS);// {}
            // data['is_export'] = 1;
            // data['ids'] = ids;
            // console.log(EXPORT_EXCEL_URL);
            // console.log(data);
            // var url_params = get_url_param(data);
            // var url = EXPORT_EXCEL_URL + '?' + url_params;
            var url = EXPORT_JOIN_EXCEL_URL + id ;
            console.log(url);
            go(url);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    iframePublish: function(id, ability_name){// 弹窗公布结果
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_PUBLISH_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_PUBLISH_URL + id + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = "公布结果-" + ability_name;//"添加/修改供应商";
        layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
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
    document.write("        var can_modify = false;");
   document.write("        if( item.status == 1 ){");
    document.write("        can_modify = true;");
    document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
   // document.write("            <td>");
   // document.write("                <label class=\"pos-rel\">");
   // document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
  //  document.write("                  <span class=\"lbl\"><\/span>");
  //  document.write("                <\/label>");
  //  document.write("            <\/td>");
    document.write("            <td><%=item.id%><\/td>");
    // document.write("            <td>技术领域<\/td>");
    document.write("            <td><%=item.ability_name%><\/td>");
    // document.write("            <td>方法标准<\/td>");
   // document.write("            <td><%=item.estimate_add_num%><\/td>");
    document.write("            <td><%=item.join_num%><\/td>");
    document.write("            <td><%=item.join_begin_date%> - <%=item.join_end_date%><\/td>");
    //document.write("            <td><p><%=item.project_standards_text%><\/p><\/td>");
    document.write("            <td><%=item.submit_items_text%><\/td>");
    document.write("            <td><%=item.duration_minute%>天<\/td>");
    document.write("            <td class='status_text'><%=item.status_text%><\/td>");
    document.write("            <td><%=item.first_submit_num%><hr/><%=item.repair_submit_num%><\/td>");
    document.write("            <td><%=item.first_success_num%><hr/><%=item.repair_success_num%><\/td>");
    document.write("            <td><%=item.first_fail_num%><hr/><%=item.repair_fail_num%><\/td>");
    document.write("            <td><%=item.is_publish_text%><hr/><%=item.publish_time%><\/td>");
    document.write("            <td><%=item.created_at%><\/td>");
    // document.write("            <td><%=item.updated_at%><\/td>");
    // document.write("            <td><%=item.sort_num%><\/td>");
    document.write("            <td>");
    document.write("                <%if( true){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.admin(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 管理<\/i>");
    document.write("                <\/a>");
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
