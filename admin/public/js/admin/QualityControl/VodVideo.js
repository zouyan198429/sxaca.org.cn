
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

    // $('.search_frm').trigger("click");// 触发搜索事件
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
    var resourceListObj = $('.resource_list_block');// $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    // 初始化列表文件显示功能--视频
    var uploadVideoAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 1,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceVideoListObj = $('.resource_list_video');// $('#data_list').find('tr');
    initFileShow(uploadVideoAttrObj, resourceVideoListObj, 'resource_show_video', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id_video[]');

    // 初始化列表文件显示功能--附件资料
    var uploadCoursewareAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 1,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceCoursewareListObj = $('.resource_list_courseware');// $('#data_list').find('tr');
    initFileShow(uploadCoursewareAttrObj, resourceCoursewareListObj, 'resource_show_courseware', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id_courseware[]');

    // initList();
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
//业务逻辑部分
var otheraction = {
    down_file:function(resource_url, save_file_name){//下载网页打印机驱动
        // var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
        // //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
        // var url = DOWN_FILE_URL + '?resource_url=' + resource_url + '&save_file_name=' + save_file_name;
        // console.log('下载文件：', url);
        // // PrintOneURL(url);
        // go(url);
        // layer.close(layer_index);//手动关闭
        commonaction.down_file(DOWN_FILE_URL, resource_url, save_file_name);
    },
    iframeModify : function(id){// 弹窗添加/修改
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_MODIFY_DIR_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_MODIFY_DIR_URL + id + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = IFRAME_MODIFY_DIR_URL_TITLE;//"添加/修改供应商";
        var operateText = "添加";
        if(id > 0){
            operateText = "修改";
        }
        tishi = operateText + tishi;
        layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
        return false;
    },
    show : function(id){// 课程管理
        var weburl = COURSE_SHOW_URL + id ;
        console.log(weburl);
        goOpenUrl(weburl,'_blank');
        return false;
    }
};
(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("        <% var show_vod_name = '';");
    document.write("        for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    //document.write("        var can_modify = false;");
   // document.write("        if( item.issuper==0 ){");
    document.write("        can_modify = true;");
    //document.write("        }");
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
    document.write("            <td>");
    document.write("                <%if( show_vod_name != item.vod_name){%>");
    document.write("                <% show_vod_name = item.vod_name; %>");
    document.write("                   <%=item.vod_name%> ");
    document.write("                <%}%>");
    document.write("            <\/td>");
    document.write("            <td><%=item.video_name_level%><\/td>");
    document.write("            <td><%=item.is_video_text%><hr/><%=item.status_online_text%><\/td>");
    document.write("            <td  class=\"resource_list_block\" >");
    document.write("               <span class=\"resource_list\"  style=\"display: none;\"><%=JSON.stringify(item.resource_list)%></span>");
    document.write("               <span  class=\"resource_show\"></span>");
    document.write("            <\/td>");
    document.write("            <td><%=item.video_type_text%><hr/><%=item.video_url%><\/td>");
    document.write("            <td class=\"resource_list_video\" >");
    document.write("               <span class=\"resource_list\"  style=\"display: none;\"><%=JSON.stringify(item.resource_list_video)%></span>");
    document.write("               <span  class=\"resource_show_video\"></span>");
    document.write("            <\/td>");
    document.write("            <td class=\"resource_list_courseware\">");
    document.write("               <span class=\"resource_list\"  style=\"display: none;\"><%=JSON.stringify(item.resource_list_courseware)%></span>");
    document.write("               <span  class=\"resource_show_courseware\"></span>");
    document.write("            <\/td>");
    // document.write("            <td><\/td>");
    document.write("            <td><%=item.created_at%><hr/><%=item.updated_at%><\/td>");
    document.write("            <td><%=item.sort_num%><\/td>");
    document.write("            <td>");
    document.write("                <%if( false){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.is_video == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.iframeModify(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    document.write("                <\/a>");
    document.write("                <%}else{%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( can_modify){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.is_video == 2){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
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
