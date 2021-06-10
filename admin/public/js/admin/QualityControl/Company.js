
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
    open : function(id, open_status){
        var operateText = '审核通过';
        if(open_status === 4){
            operateText = '审核不通过';
        }
        var index_query = layer.confirm('确定' + operateText + '当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            other_operate_ajax('open', id, operateText, {'open_status': open_status});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    openSelected: function(obj, open_status){// 开启选中的码
        var recordObj = $(obj);
        var operateText = '审核通过';
        if(open_status === 4){
            operateText = '审核不通过';
        }
        var index_query = layer.confirm('确定' + operateText + '当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            //ajax开启数据
            other_operate_ajax('batch_open', ids, operateText, {'open_status': open_status});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    frozen : function(id, account_status){
        var operateText = '解冻';
        if(account_status === 2){
            operateText = '冻结';
        }
        var index_query = layer.confirm('确定' + operateText + '当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            other_operate_ajax('frozen', id, operateText, {'account_status': account_status});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    frozenSelected: function(obj, account_status){// 开启选中的码
        var recordObj = $(obj);
        var operateText = '解冻';
        if(account_status === 2){
            operateText = '冻结';
        }
        var index_query = layer.confirm('确定' + operateText + '当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1,'check_item');// 注意：checkbox有 class : check_item
            //ajax开启数据
            other_operate_ajax('batch_frozen', ids, operateText, {'account_status': account_status});
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    schedule : function(id, company_name){// 查看能力附表
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(SCHEDULE_SHOW_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        // var weburl = SCHEDULE_SHOW_URL + id + '?' + url_params;
        var weburl = SCHEDULE_SHOW_URL + '?company_id=' + id + '&' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-能力附表";
        layeriframe(weburl,tishi,980,535,SHOW_CLOSE_OPERATE);
        return false;
    },
    staff_num : function(id, company_name){// 查看员工
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(STAFF_SHOW_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = STAFF_SHOW_URL  + id ;// + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-员工管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    certificate_schedule_num : function(id, company_name){// 查看能力范围
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(CERTIFICATE_SCHEDULE_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = CERTIFICATE_SCHEDULE_URL + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-能力范围管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_statement_num : function(id, company_name){// 查看机构自我声明
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COMPANY_STATEMENT_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_STATEMENT_URL + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-机构自我声明管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_punish_num : function(id, company_name){// 查看机构处罚
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COMPANY_PUNISH_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_PUNISH_URL + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-机构处罚管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_content : function(id, company_name){// 查看或修改企业简介
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(CERTIFICATE_SCHEDULE_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_CONTENT_EDIT_URL + '?company_id=' + id + "&company_hidden=1" ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-企业简介";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_supervise : function(id, company_name){// 查看或修改企业简介
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COMPANY_SUPERVISE_EDIT_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_SUPERVISE_EDIT_URL + '?company_id=' + id + "&company_hidden=1" ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-监督检查信息";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    grade_config : function(id, company_name){// 查看或修改企业简介
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(GRADE_CONFIG_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = GRADE_CONFIG_URL + '?company_id=' + id + "&company_hidden=1" ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-会员等级配置";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    course_order : function(id, company_name){// 查看或修改企业面授
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COURSE_ORDER_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COURSE_ORDER_URL + '?company_id=' + id + "&company_hidden=1" ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-面授报名";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    laboratory_addr_num : function(id, company_name){// 查看机构实验室地址
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(LABORATORY_ADDR_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = LABORATORY_ADDR_URL + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-机构实验室地址管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    invoice_buy_addr_num : function(id, company_name){// 查看电子发票地址
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(INVOICE_BUYER_ADDR_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = INVOICE_BUYER_ADDR_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-电子发票地址管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_ability_num : function(id, company_name){// 查看能力验证结果地址
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COMPANY_ABILITY_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_ABILITY_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-能力验证结果管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_inspect_num : function(id, company_name){// 查看监督检查地址
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COMPANY_INSPECT_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_INSPECT_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-监督检查管理";
        layeriframe(weburl,tishi,1050,535,5);
        return false;
    },
    company_news_num : function(id, company_name){// 查看企业其它【新闻】
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(COMPANY_NEWS_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_NEWS_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-企业其它管理";
        layeriframe(weburl,tishi,1050,535,5);
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
        case 'open'://审核通过/不通过
            // operate_txt = "开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            ajax_url = OPEN_OPERATE_URL;// /pms/Supplier/ajax_del?operate_type=1
            reset_total = false;
            break;
        case 'batch_open'://批量开启
            // operate_txt = "批量开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            reset_total = false;
            ajax_url = OPEN_OPERATE_URL;// "/pms/Supplier/ajax_del?operate_type=2";
            break;
        case 'frozen'://冻结/解冻
                    // operate_txt = "开启";
                    // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
                    // 合并对象
            objAppendProps(data, {'id':id}, true);
            ajax_url = ACCOUNT_STATUS_URL;// /pms/Supplier/ajax_del?operate_type=1
            reset_total = false;
            break;
        case 'batch_frozen'://批量冻结/解冻
            // operate_txt = "批量开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            reset_total = false;
            ajax_url = ACCOUNT_STATUS_URL;// "/pms/Supplier/ajax_del?operate_type=2";
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
    document.write("            <td>");
    document.write("                <label class=\"pos-rel\">");
    document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    document.write("                  <span class=\"lbl\"><\/span>");
    document.write("                <\/label>");
    document.write("            <\/td>");
    document.write("            <td><%=item.id%><\/td>");
   // document.write("            <td><%=item.city_name%><\/td>");
    document.write("            <td><%=item.company_name%><\/td>");
    document.write("            <td><%=item.industry_name%><hr/><%=item.company_grade_text%><\/td>");
    //document.write("            <td><%=item.extend_info.staff_num%><\/td>");
    document.write("            <td><%=item.company_certificate_no%><\/td>");
    document.write("            <td><%=item.company_contact_name%><hr/><%=item.company_contact_mobile%> <\/td>");
    //document.write("            <td><%=item.company_contact_mobile%><\/td>");
    document.write("            <td><%=item.is_perfect_text%><hr/><%=item.company_grade_continue_text%><\/td>");
    // document.write("            <td><\/td>");
    document.write("            <td><%=item.open_status_text%><hr/><%=item.account_status_text%><\/td>");
    document.write("            <td><%=item.created_at%><hr/><%=item.company_end_time%><\/td>");
    // document.write("            <td><%=item.lastlogintime%><\/td>");
    // document.write("            <td><%=item.created_at%><\/td>");
    // document.write("            <td><%=item.updated_at%><\/td>");
    // document.write("            <td><%=item.sort_num%><\/td>");
    document.write("            <td>");
    document.write("                <%if( false){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if(can_modify && item.open_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.open(<%=item.id%>, 2)\">");
    document.write("                    <i class=\"ace-icon bigger-60\">审核通过<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.open(<%=item.id%>, 4)\">");
    document.write("                    <i class=\"ace-icon bigger-60\">审核不通过<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if(can_modify &&  item.account_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.frozen(<%=item.id%>, 2)\">");
    document.write("                    <i class=\"ace-icon fa fa-lock bigger-60\"> 冻结<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( can_modify && item.account_status == 2){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.frozen(<%=item.id%>, 1)\">");
    document.write("                    <i class=\"ace-icon fa fa-unlock bigger-60\"> 解冻<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    document.write("                <\/a>");
    document.write("                <%if( can_modify){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> <\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
	document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.staff_num(<%=item.id%>,'<%=item.company_name%>')\">");
	document.write("                    <i class=\"ace-icon fa fa-user-circle bigger-60\">员工(<%=item.extend_info.staff_num%>)<\/i>");
	document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.laboratory_addr_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-home bigger-60\">实验室地址(<%=item.extend_info.laboratory_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.schedule(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon  fa fa-cloud-download  bigger-60\">能力附表(<%=item.extend_info.schedule_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.certificate_schedule_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-file-text bigger-60\">能力范围(<%=item.extend_info.certificate_schedule_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn fa fa-search btn-mini btn-info\" onclick=\"otheraction.company_content(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon bigger-60\">简介(<%=item.extend_info.company_content_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn fa fa-hdd-o btn-mini btn-info\" onclick=\"otheraction.company_supervise(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon bigger-60\">监查(<%=item.extend_info.supervise_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.company_statement_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-bullhorn bigger-60\">自我声明(<%=item.extend_info.statement_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.company_punish_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-exclamation-triangle bigger-60\">处罚(<%=item.extend_info.punish_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn fa fa-handshake-o btn-mini btn-info\" onclick=\"otheraction.grade_config(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon bigger-60\">会员等级(<%=item.extend_info.grade_config_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn fa fa-book btn-mini btn-info\" onclick=\"otheraction.course_order(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon bigger-60\">面授报名(<%=item.extend_info.face_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.invoice_buy_addr_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-vcard bigger-60\">电子发票抬头(<%=item.extend_info.invoice_addr_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.company_ability_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-thumbs-up bigger-60\">能力验证结果(<%=item.extend_info.ability_result_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.company_inspect_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil-square bigger-60\">监督检查(<%=item.extend_info.inspect_num%>)<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.company_news_num(<%=item.id%>,'<%=item.company_name%>')\">");
    document.write("                    <i class=\"ace-icon fa fa-newspaper-o bigger-60\">企业其它(<%=item.extend_info.news_num%>)<\/i>");
    document.write("                <\/a>");
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
