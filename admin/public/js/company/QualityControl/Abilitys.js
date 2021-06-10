
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
    joinSelected: function(obj){//报名选中的码
        var recordObj = $(obj);
        if (!SUBMIT_FORM) return false;//false，则返回

        // var operateText = '报名';
        // // var index_query = layer.confirm('确定' + operateText + '当前记录？', {
        // //     btn: ['确定','取消'] //按钮
        // // }, function(){
        //     var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
        //     if(ids == ''){
        //         err_alert('请选择需要' + operateText + '的项目');
        //         return false;
        //     }
        // //     layer.close(index_query);
        // // }, function(){
        // // });
        // return false;


        // 判断是否有能力附表
        // 验证通过
        SUBMIT_FORM = false;//标记为已经提交过
        var data = {};
        console.log(COMPANY_EXTEND_URL);
        console.log(data);
        var layer_index = layer.load();
        $.ajax({
            'type' : 'POST',
            'url' : COMPANY_EXTEND_URL,
            'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
            'data' : data,
            'dataType' : 'json',
            'success' : function(ret){
                console.log(ret);
                SUBMIT_FORM = true;
                if(!ret.apistatus){//失败
                    SUBMIT_FORM = true;//标记为未提交过
                    //alert('失败');
                    err_alert(ret.errorMsg);
                }else{//成功
                    // go(LIST_URL);
                    var schedule_num = ret.result['schedule_num'];
                    console.log('schedule_num=',schedule_num);
                    if(schedule_num <= 0){
                        var index_query = layer.confirm('温馨提示：<br/>您还没有上传能力附表!<br/>请上传能力附表后再进行能力验证报名！<br/>感谢您的理解和支持。', {
                            btn: ['上传能力附表','关闭'] //按钮
                        }, function(){
                            layer.close(index_query);
                            var href = COMPANY_SCHEDULE_URL;//
                            layuiGoIframe(href, '能力附表');
                            return false;
                        }, function(){
                        });
                    }else{
                        joinPage();
                    }

                    // countdown_alert("操作成功!",1,5);
                    // parent_only_reset_list(false);
                    // wait_close_popus(2,PARENT_LAYER_INDEX);
                    // layer.msg('操作成功！', {
                    //     icon: 1,
                    //     shade: 0.3,
                    //     time: 3000 //2秒关闭（如果不配置，默认是3秒）
                    // }, function(){
                    //     var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    //     if(id > 0) reset_total = false;
                    //     parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                    //     //do something
                    // });
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
};

function joinPage(){
    var operateText = '报名';

    var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
    if(ids == ''){
        err_alert('请选择需要' + operateText + '的项目');
        return false;
    }
    //获得表单各name的值
    var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
    console.log(JOIN_URL);
    console.log(data);
    var url_params = get_url_param(data);// parent.get_url_param(data)
    var weburl = JOIN_URL + ids + '?' + url_params;
    console.log(weburl);
    // go(SHOW_URL + id);
    // location.href='/pms/Supplier/show?supplier_id='+id;
    // var weburl = SHOW_URL + id;
    // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
    var tishi = "";//"添加/修改供应商";
    tishi = operateText + tishi;
    layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
    return false;
}

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    document.write("        var can_join = false;");
    document.write("        if( item.status == 2 && item.is_joined == 0){");
    document.write("            can_join = true;");
    document.write("        }");
    document.write("        var can_modify = false;");
   document.write("        if( item.status == 1 ){");
    document.write("        can_modify = true;");
    document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
   document.write("            <td>");
   document.write("                <label class=\"pos-rel\">");
   document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( !can_join){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
   document.write("                  <span class=\"lbl\"><\/span>");
   document.write("                <\/label>");
   document.write("            <\/td>");
    document.write("            <td><%=item.id%><\/td>");
     document.write("            <td><%=item.ability_name%><\/td>");
    // document.write("            <td><%=item.created_at_format%><\/td>");
    document.write("            <td><%=item.join_begin_date%> - <%=item.join_end_date%><\/td>");
    document.write("            <td><%=item.status_text%><\/td>");
    document.write("            <td><%=item.is_joined_text%><\/td>");
    document.write("            <td>");
    document.write("                <%if( true){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
