
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
    getSample : function(id){// 弹窗取样
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_SAMPLE_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_SAMPLE_URL + id + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = "取样";
        layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
        return false;
    },
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
    print:function(operate_type, id, title, is_print){// 查看/打印证书 operate_type: 操作类型 1 查看  2 打印
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_PRINT_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_PRINT_URL + id + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";

        var operateText = "查看证书";
        var tishi = '';
        if(operate_type == 2){// 打印
            operateText = "打印证书";
            // tishi = operateText + tishi;
            printInfoPage(id, is_print, weburl, title);
        }else{// 查看
            tishi = operateText + tishi + title;
            layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
        }
        return false;
    },
    grant : function(id, title){
        var operateText = '领取证书-' + title;
        var index_query = layer.confirm('确定' + operateText + '？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            other_operate_ajax('grant', id, operateText, {});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    printInfo: function(id, title){//
        var operateText = '打印证书-' + title;
        var index_query = layer.confirm('确定' + operateText + '？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            other_operate_ajax('print', id, operateText, {});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    downDrive : function(obj){// 下载网页打印机驱动
        var recordObj = $(obj);
        var index_query = layer.confirm('确定下载网页打印机驱动？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            down_drive();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    printSelected: function(obj){// 打印勾选的
        var recordObj = $(obj);
        // var operateText = '打印勾选记录证书';
        // var index_query = layer.confirm('确定' + operateText + '？', {
        //     btn: ['确定','取消'] //按钮
        // }, function(){
            var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            printSelecedRecord(ids);
        //     layer.close(index_query);
        // }, function(){
        // });
        return false;
    },
    printSearch: function(obj){// 打印证书[按条件]
        var recordObj = $(obj);
        var operateText = '打印查询所有条件记录证书';
        var index_query = layer.confirm('确定' + operateText + '？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            searchPrint();// 按条件打印
            layer.close(index_query);
        }, function(){
        });
        return false;
    }
};

// 按条件打印
function searchPrint() {
    //获得表单各name的值
    var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
    console.log(SEARCH_PRINT_URL);
    // console.log(data);
    // var url_params = get_url_param(data);// parent.get_url_param(data)
    // var weburl = SEARCH_PRINT_URL  + '?' + url_params;
    // console.log(weburl);
    // go(SHOW_URL + id);
    // location.href='/pms/Supplier/show?supplier_id='+id;
    // var weburl = SHOW_URL + id;
    // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
    var reset_total = false;// 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    $.ajax({
        'type' : 'POST',
        'url' : SEARCH_PRINT_URL,//'/pms/Supplier/ajax_del',
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
                // reset_list(true, true);
                let ids_arr = ret.result.ids;
                console.log('ids_arr=', ids_arr);
                printByIdsArr(ids_arr);
                console.log(LIST_FUNCTION_NAME);
                eval( LIST_FUNCTION_NAME + '(' + true +', ' + true +', ' + reset_total + ', 2)');
            }
            layer.close(layer_index);//手动关闭
        }
    });
}

// 打印勾选记录的证书
// ids 20,19  选中的记录id
function printSelecedRecord(ids) {
    console.log('ids=', ids);
    if(ids == ''){
        err_alert('请选择需要操作的数据');
        return false;
    }
    var operateText = '打印勾选记录证书';
    var index_query = layer.confirm('确定' + operateText + '？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        var ids_arr = ids.split(",");
        console.log('ids_arr=', ids_arr);
        let need_is_print_ids = [];// 打印需要改变状态的记录id
        $('#data_list').find('tr').each(function () {
            var trObj = $(this);
            var tem_id = trObj.data('id') + '';
            var is_print = trObj.data('is_print') + '';
            console.log('tem_id=', tem_id);
            console.log('is_print=', is_print);
            if(ids_arr.indexOf(tem_id) >= 0 && is_print == 1){// 选中且状态：未打印
                need_is_print_ids.push(tem_id);
            }
        });
        console.log('need_is_print_ids=', need_is_print_ids);
        if(need_is_print_ids.length > 0){// 有需要状态处理的
            var reset_total = false;// 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
            var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
            $.ajax({
                'type' : 'POST',
                'url' : IS_PRINT_URL,//'/pms/Supplier/ajax_del',
                'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
                'data' : {'id': need_is_print_ids.join(',')},
                'dataType' : 'json',
                'success' : function(ret){
                    console.log('ret:',ret);
                    if(!ret.apistatus){//失败
                        //alert('失败');
                        // countdown_alert(ret.errorMsg,0,5);
                        layer_alert(ret.errorMsg,3,0);
                    }else{//成功
                        // reset_list(true, true);
                        printByIdsArr(ids_arr);
                        console.log(LIST_FUNCTION_NAME);
                        eval( LIST_FUNCTION_NAME + '(' + true +', ' + true +', ' + reset_total + ', 2)');
                    }
                    layer.close(layer_index);//手动关闭
                }
            });
        }else{// 直接打印
            printByIdsArr(ids_arr);
        }
        layer.close(index_query);
    }, function(){
    });

}

// 根据id数组打印证书
function printByIdsArr(ids_arr) {
    for (var i=0 ; i< ids_arr.length ; i++) {
        var tem_id = ids_arr[i];// id
        let weburl  = IFRAME_PRINT_URL + tem_id;
        PrintCertificateURL(weburl, PRINT_INT_ORIENT, PRINT_INT_PAGE_WIDTH, PRINT_INT_PAGE_HEIGHT, PRINT_STR_PAGE_NAME);
    }

}

function printInfoPage(id, is_print, weburl, tishi){
    if(is_print == 1){// 需要先修改状态
        otheraction.printInfo(id, tishi, weburl);
    }else{// 直接打印
        var index_query = layer.confirm('确定打印证书：' + tishi + '？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            PrintCertificateURL(weburl, PRINT_INT_ORIENT, PRINT_INT_PAGE_WIDTH, PRINT_INT_PAGE_HEIGHT, PRINT_STR_PAGE_NAME);
            layer.close(index_query);
        }, function(){
        });
    }

}



//下载网页打印机驱动
function down_drive(){
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
    var url = DOWN_DRIVE_URL ;
    console.log('下载网页打印机驱动：', url);
    // PrintOneURL(url);
    go(url);
    layer.close(layer_index);//手动关闭
}

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
        case 'print':// 打印证书
                    // operate_txt = "开启";
                    // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
                    // 合并对象
            objAppendProps(data, {'id':id}, true);
            ajax_url = IS_PRINT_URL;// /pms/Supplier/ajax_del?operate_type=1
            reset_total = false;
            break;
        case 'grant':// 领取证书
            // operate_txt = "开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            ajax_url = IS_GRANT_URL;// /pms/Supplier/ajax_del?operate_type=1
            reset_total = false;
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
                if(operate_type == 'print'){// 是打印操作
                    var printurl = IFRAME_PRINT_URL + id;
                    // 打印页面
                    PrintCertificateURL(printurl, PRINT_INT_ORIENT, PRINT_INT_PAGE_WIDTH, PRINT_INT_PAGE_HEIGHT, PRINT_STR_PAGE_NAME);
                }
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
    document.write("        var can_modify = false;");
   document.write("        if( true || (item.status == 16 || item.status == 64) ){");// 可以打印证书
    document.write("        can_modify = true;");
    document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr data-id=\"<%=item.id%>\" data-is_print=\"<%=item.is_print%>\">");
   document.write("            <td>");
   document.write("                <label class=\"pos-rel\">");
   document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
   document.write("                  <span class=\"lbl\"><\/span>");
   document.write("                <\/label>");
   document.write("            <\/td>");
    // document.write("            <td><%=item.id%><\/td>");
    document.write("            <td><%=item.ability_code%><\/td>");
   document.write("            <td><%=item.company_name%><\/td>");
    document.write("            <td><%=item.contacts%><\/td>");
    document.write("            <td><%=item.mobile%><hr/><%=item.tel%><\/td>");
    document.write("            <td><%=item.join_time%><\/td>");
    // document.write("            <td><\/td>");
    document.write("            <td><%=item.items_num%><hr/><%=item.passed_num%><\/td>");
    // document.write("            <td><\/td>");
    document.write("            <td><%=item.status_text%>(<%=item.retry_no_text%>)<hr/><%=item.is_sample_text%><\/td>");
    document.write("            <td><%=item.first_submit_num%><hr/><%=item.repair_submit_num%><\/td>");
    document.write("            <td><%=item.is_print_text%><hr/><%=item.is_grant_text%><\/td>");
    // document.write("            <td><%=item.created_at%><\/td>");
    // document.write("            <td><%=item.updated_at%><\/td>");
    // document.write("            <td><%=item.sort_num%><\/td>");
    document.write("            <td>");
    document.write("                <%if( true){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看报名项目<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    // document.write("                <%if( (item.status == 1  || item.status == 2 || item.status == 4 || item.status == 8 ) && ( (item.retry_no == 0 && item.first_submit_num <= 0) || (item.retry_no == 1 && item.repair_submit_num <= 0)  )){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.getSample(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-eyedropper bigger-60\"> 取样<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <%if( item.status == 16  && item.is_print == 1){%>");
    document.write("                <%if( (item.status == 16 || item.status == 64) ){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.print(1,<%=item.id%>,'<%=item.company_name%>-<%=item.ability_code%>',<%=item.is_print%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-eye bigger-60\"> 查看证书<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.print(2,<%=item.id%>,'<%=item.company_name%>-<%=item.ability_code%>',<%=item.is_print%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-print bigger-60\"> 打印证书<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.status == 16  && item.is_print == 2 && item.is_grant == 1 ){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.grant(<%=item.id%>,'<%=item.company_name%>-<%=item.ability_code%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-address-card-o bigger-60\"> 领取证书<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    // document.write("                <%if( can_modify){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    // document.write("                <\/a>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    document.write("                <%if( true){%>");
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
