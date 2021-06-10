
var SUBMIT_FORM = true;//防止多次点击提交
var IS_LAYUI_PAGE = isLayuiIframePage();// 是否Layui的iframe弹出层；true: 是； false:不是
var IS_FRAME_PAGE = isIframePage();// 是否iframe弹出层；true: 是； false:不是

//获取当前窗口索引
var PARENT_LAYER_INDEX = getParentLayerIndex();
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

//业务逻辑部分
// var otheraction = {
//     addBuyer:function(obj){// 增加企业抬头
//         var recordObj = $(obj);
//         // 所属企业
//         var company_id = $('input[name=company_id]').val();
//         var judge_seled = judge_validate(1,'所属企业',company_id,true,'positive_int','','');
//         if(judge_seled != ''){
//             layer_alert("请选择所属企业",3,0);
//             return false;
//         }
//         var hidden_option = 1 | 8192;
//         var url = ADD_INVOICE_BUYER_URL + '?hidden_option=' + hidden_option + '&company_id=' + company_id;
//         consoleLogs([url]);
//         var tishi = "发票抬头";
//         layeriframe(url,tishi,750,450,0);
//         // commonaction.browse_file(url, tishi,750,450, 0);
//         return false;
//     },
//     showInvoice : function(id){// 弹窗显示
//         //获得表单各name的值
//         var data = {};// get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
//         console.log(INFO_INVOICE_BUYER_URL);
//         console.log(data);
//         var url_params = get_url_param(data);// parent.get_url_param(data);
//         var weburl = INFO_INVOICE_BUYER_URL + id + '?' + url_params;
//         console.log(weburl);
//         // go(INFO_INVOICE_BUYER_URL + id);
//         // location.href='/pms/Supplier/show?supplier_id='+id;
//         // var weburl = SHOW_URL + id;
//         // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
//         var tishi = "发票抬头";// SHOW_URL_TITLE;//"查看供应商";
//         layeriframe(weburl,tishi,750,450,0,'',null,2);
//         return false;
//     }
// };

// 新加发票抬头保存成功后调用的方法
// obj:当前表单值对像
// result:保存接口返回的结果
// operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】2[默认]：新加保存成功时
// function adminQualityControlInvoiceBuyeredit(obj, result, operateNum){
//     operateNum = operateNum || 2;
//     consoleLogs(['obj:', obj, 'result:', result, 'operateNum:', operateNum]);
//     switch(operateNum){
//         case 1:
//             break;
//         case 2:
//             // break;
//         default://其它
//             if(obj.open_status == 1){
//                 var invoice_buyer_id = result;
//                 var html = '<label id="invoice_buyer_' + invoice_buyer_id + '"><input type="radio"  name="invoice_buyer_id"  value="' + invoice_buyer_id + '"   />' + obj.gmf_mc + '<a href="javascript:void(0);" onclick="otheraction.showInvoice(' + invoice_buyer_id + ')">查看</a></label>';
//                 $('#invoice_buyer_list').append(html);
//             }
//             break;
//     }
// }

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

    //切换短信模板
    $(document).on("change",'input[name="sms_template_id"]',function(){
        var recordObj = $(this);
        var template_id = recordObj.val();
        console.log('==template_id=', template_id);
        resetTemplate(recordObj, template_id);// 重新显示 所属模块参数
    });
    initTemplate();// 初始化所属短信模板
});
// 初始化所属短信模板
function initTemplate(){
    $('input[name=sms_template_id]').each(function(){
        var redioObj = $(this);
        if(redioObj.is(':checked')){
            var template_id = redioObj.val();
            resetTemplate(redioObj, template_id);// 重新显示 所属模块参数
            return false;
        }
    });
}

// 重新显示 所属短信模板
function resetTemplate(obj, template_id) {
    var judge_seled = judge_validate(1,'所属短信模板',template_id,true,'digit','','');
    if(judge_seled != ''){
        // layer_alert("请选择所属短信模板",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }
    var infoJsonStr = obj.closest('.template_block').find('.template_info').html();
    console.log('==infoJsonStr=', infoJsonStr);
    var infoObj = JSON.parse(infoJsonStr);
    console.log('==infoObj==', infoObj);
    var template_type_text = infoObj.template_type_text || '';// 模板类型
    $('.template_type_text').html(template_type_text);
    var template_code = infoObj.template_code || '';// 模板ID
    $('.template_code').html(template_code);
    var sign_name = infoObj.sign_name || '';// 签名名称
    $('.sign_name').html(sign_name);
    var template_content = infoObj.template_content || '';// 模板内容
    $('.template_content').html(template_content);
    var params_list = infoObj.params_list || [];// 模板参数
    console.log('==params_list==', params_list);
    // $('.aaa').html(aaaa);
    // data_list 数据对象 {'data_list':[{}]}
    var data_list = {'data_list': params_list};
    var htmlStr = resolve_baidu_template(BAIDU_TEMPLATE_SMS_PARAMS_NAME, data_list, '');//解析
    $('.data_list').html(htmlStr);

    // var ajax_url = RESET_MODULE_PARAMS_URL;
    // var data = {'module_id': module_id};
    // console.log('ajax_url:',ajax_url);
    // console.log('data:',data);
    // var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    // $.ajax({
    //     'type' : 'POST',
    //     'url' : ajax_url,//'/pms/Supplier/ajax_del',
    //     'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
    //     'data' : data,
    //     'dataType' : 'json',
    //     'success' : function(ret){
    //         console.log('ret:',ret);
    //         if(!ret.apistatus){//失败
    //             //alert('失败');
    //             // countdown_alert(ret.errorMsg,0,5);
    //             layer_alert(ret.errorMsg,3,0);
    //         }else{//成功
    //             // var msg = ret.errorMsg;
    //             // if(msg === ""){
    //             //     msg = operate_txt+"成功";
    //             // }
    //             // // countdown_alert(msg,1,5);
    //             // layer_alert(msg,1,0);
    //             // // reset_list(true, true);
    //             // console.log(LIST_FUNCTION_NAME);
    //             // eval( LIST_FUNCTION_NAME + '(' + true +', ' + true +', ' + reset_total + ', 2)');
    //             var result = ret.result;
    //             var data_list = result.data_list;
    //             initModuleList(result);
    //         }
    //         layer.close(layer_index);//手动关闭
    //     }
    // });
}

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    // var id = $('input[name=id]').val();
    // // if(!judge_validate(4,'记录id',id,true,'digit','','')){
    // if(!judge_validate(4,'记录id',id,true,'length',1,200)){
    //     return false;
    // }

    var ids = $('input[name=ids]').val() || '';
    if(!judge_validate(4,'操作的记录',ids, false,'length',1,100000)){
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

    var sms_operate_type = $('input[name=sms_operate_type]').val();// 操作类型 1 发送短信  ; 2测试发送短信
    if(!judge_validate(4,'操作类型',sms_operate_type,true,'custom',/^[12]$/,"")){
        return false;
    }

    var sms_template_id = $('input[name=sms_template_id]:checked').val() || '';
    var judge_seled = judge_validate(1,'所属短信模板',sms_template_id,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择所属短信模板",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var sms_mobile_field = $('input[name=sms_mobile_field]:checked').val() || '';
    if(sms_operate_type == 1){// 1 发送短信
        var judge_seled = judge_validate(1,'接收短信手机号字段',sms_mobile_field,true,'length',1,50);
        if(judge_seled != ''){
            layer_alert("请选择接收短信手机号字段",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            return false;
        }
    }

    // 操作来源： 1、按条件[id 传 0]；2：选中的 id,多个用逗号分隔； 4：单条记录的[id 传 对应的id]
    var sms_operate_no = $('input[name="sms_operate_no"]').val();
    if(!judge_validate(4,'操作来源',sms_operate_no,true,'custom',/^[1248]$/,"")){
        return false;
    }

    var sms_mobile = $('input[name=sms_mobile]').val();
    if(sms_operate_type == 2) {//  2测试发送短信
        if(!judge_validate(4,'短信测试手机号',sms_mobile,true,'mobile','','')){
            return false;
        }
    }

    // 遍历参数，如果有需要输入的一定要有值
    // 可用参数进行判断--可以为空
    var tr_num = 1;
    var tr_has_err = false;
    $('.staff_td').find('.data_list').find('tr').each(function () {
        var trObj = $(this);
        var param_name = trObj.data('param_name');//;
        var row_text = "第" + tr_num + '条记录【' + param_name +  '】：';

        // 参数类型1日期时间、2固定值、4手动输入-发送时、8字段匹配
        var sms_param_type = trObj.find('input[name="sms_param_type[]"]').val();
        if(!judge_validate(4,row_text + '参数类型',sms_param_type,true,'custom',/^[1248]$/,"")){
            tr_has_err = true;
            return false;
        }

        if(sms_param_type == 4 || (sms_param_type == 8 && sms_operate_type == 2)){// 4手动输入-发送时、8字段匹配 --测试时
            var sms_param_val = trObj.find('input[name="sms_param_val[]"]').val();
            if(!judge_validate(4,row_text + '手动输入值',sms_param_val,true,'length',1,50)){
                tr_has_err = true;
                return false;
            }
        }

        tr_num++;
    });
    if(tr_has_err){
        return false;
    }

    var index_query = layer.confirm('您确定操作吗？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        // 操作类型 1 发送短信  ; 2测试发送短信
        if(sms_operate_type == 2){
            ajax_save(1);
        }else{// 如果是其它页面发送短信，则调用父页面方法，并关闭当前弹窗
            var data = $("#addForm").serialize();
            // console.log(SAVE_URL);
            consoleLogs(['== data ==', data]);
            parent.ajax_send_sms(data, window);// , PARENT_LAYER_INDEX
        }
        layer.close(index_query);
    }, function(){
    });

    return false;
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
}

(function() {
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_sms_params_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    %>");
    document.write("    <tr data-param_name=\"<%=item.param_name%>\">");
    document.write("        <td><%=item.param_name%><\/td>");
    document.write("        <td><%=item.param_code%><input type=\"hidden\" name=\"sms_param_code[]\" value=\"<%=item.param_code%>\"><\/td>");
    document.write("        <td><%=item.param_type_text%><input type=\"hidden\" name=\"sms_param_type[]\" value=\"<%=item.param_type%>\"><\/td>");
    document.write("        <td>");
    document.write("            <%if( (item.param_type == 4) || (item.param_type == 8 && SMS_OPERATE_TYPE == 2) ){%>");
    document.write("            <input type=\"text\" name=\"sms_param_val[]\" value=\"\"  placeholder=\"请输入内容\"  style=\"width:100px; \">");
    document.write("            <%}else{%>");
    document.write("            <input type=\"text\" name=\"sms_param_val[]\" value=\"\"  placeholder=\"请输入内容\"  style=\"width:100px;display: none; \">");
    document.write("            <%}%>");
    document.write("        <\/td>");
    document.write("        <td><%=item.date_time_format%><\/td>");
    document.write("        <td><%=item.fixed_val%><\/td>");
    document.write("    <\/tr>");
    document.write("    <%");
    document.write("    }%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
