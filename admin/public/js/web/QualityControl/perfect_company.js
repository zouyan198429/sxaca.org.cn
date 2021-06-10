
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

window.onload = function() {
    var layer_index = layer.load();
    initPic();
    layer.close(layer_index);//手动关闭
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

$(function(){
    //执行一个laydate实例
    // 开始日期
    var startConfig = {
        elem: '.ratify_date' //指定元素
        ,type: 'date'
        ,value: BEGIN_TIME// '2018-08-18' //必须遵循format参数设定的格式
        // ,min: get_now_format('Y-m-d')//'2017-1-1'
        //,max: get_now_format()//'2017-12-31'
        ,calendar: true//是否显示公历节日
        ,ready: function(date){// 控件在打开时触发
            console.log(date); //得到初始的日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
        ,done: function(value, date, endDate){// 控件选择完毕后的回调
            console.log(value); //得到日期生成的值，如：2017-08-18
            console.log(date); //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
            console.log(endDate); //得结束的日期时间对象，开启范围选择（range: true）才会返回。对象成员同上。
            //更新结束日期的最小日期
            insEnd.config.min = {
                year:date.year,
                month:date.month-1, //关键
                date: date.date,
                hours: date.hours,
                minutes: date.minutes,
                seconds : date.seconds
            };
            //自动弹出结束日期的选择器
            insEnd.config.elem[0].focus();
        }
    };
    // 有结束时间
    if(judge_date(END_TIME)){
        startConfig.max = END_TIME;
        console.log('END_TIME', END_TIME);
        console.log('startConfig', startConfig);
    }

    var insStart = laydate.render(startConfig);

    // 最晚开始日期
    var endConfig = {
        elem: '.valid_date' //指定元素
        ,type: 'date'
        ,value: END_TIME// '2018-08-18' //必须遵循format参数设定的格式
        // ,min: get_now_format('Y-m-d')//'2017-1-1'
        //,max: get_now_format()//'2017-12-31'
        ,calendar: true//是否显示公历节日
        ,ready: function(date){// 控件在打开时触发
            console.log(date); //得到初始的日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
        ,done: function(value, date, endDate){// 控件选择完毕后的回调
            console.log(value); //得到日期生成的值，如：2017-08-18
            console.log(date); //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
            console.log(endDate); //得结束的日期时间对象，开启范围选择（range: true）才会返回。对象成员同上。
            //更新开始日期的最大日期
            insStart.config.max = {
                year:date.year,
                month:date.month-1, //关键
                date: date.date,
                hours: date.hours,
                minutes: date.minutes,
                seconds : date.seconds
            };
        }
    };
    // 开始时间
    if(judge_date(BEGIN_TIME)){
        endConfig.min = BEGIN_TIME;
        console.log('BEGIN_TIME', BEGIN_TIME);
        console.log('endConfig', endConfig);
    }
    var insEnd = laydate.render(endConfig);

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
    // 注册服务协议
    $(document).on("click",".reg_agree_info",function(){

        var tishi = '注册服务协议';
        layeriframe(REG_AGREE_URL,tishi,950,510,0);
    });
    $(document).on("click",".company_is_legal_persion",function(){
        toggle_legal_persion();
    });
    toggle_legal_persion();
});
// 是否显示 独立法人 勾选内容
function toggle_legal_persion() {
    // 是否独立法人
    var company_is_legal_persion = get_list_checked('company_is_legal_persion',2,1);
    // 独立法人
    if(company_is_legal_persion != '') {
        $(".company_is_legal_persion_item").show();
    }else{
        $(".company_is_legal_persion_item").hide();
    }
}
//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
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

    // var type_name = $('input[name=type_name]').val();
    // if(!judge_validate(4,'标签名称',type_name,true,'length',1,20)){
    //     return false;
    // }
    //
    // var sort_num = $('input[name=sort_num]').val();
    // if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
    //     return false;
    // }

    var company_name = $('input[name=company_name]').val();
    if(!judge_validate(4,'单位名称',company_name,true,'length',1,100)){
        return false;
    }

    var company_credit_code = $('input[name=company_credit_code]').val();
    if(!judge_validate(4,'统一社会信用代码',company_credit_code,true,'length',1,50)){
        return false;
    }

    // 是否独立法人
    var company_is_legal_persion = get_list_checked('company_is_legal_persion',2,1);
    // 独立法人
    if(company_is_legal_persion != '') {
        var company_legal_credit_code = $('input[name=company_legal_credit_code]').val();
        if(!judge_validate(4,'主体机构统一社会信用代码',company_legal_credit_code,false,'length',1,50)){
            return false;
        }

        var company_legal_name = $('input[name=company_legal_name]').val();
        if(!judge_validate(4,'主体机构',company_legal_name,false,'length',1,50)){
            return false;
        }
    }

    var city_id = $('select[name=city_id]').val();
    var judge_seled = judge_validate(1,'城市',city_id,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择城市",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var company_type = $('input[name=company_type]:checked').val() || '';
    var judge_seled = judge_validate(1,'企业类型',company_type,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择企业类型",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var company_prop = $('select[name=company_prop]').val();
    var judge_seled = judge_validate(1,'企业性质',company_prop,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择企业性质",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var addr = $('input[name=addr]').val();
    if(!judge_validate(4,'通讯地址',addr,true,'length',1,100)){
        return false;
    }

    var zip_code = $('input[name=zip_code]').val();
    if(!judge_validate(4,'邮编',zip_code,false,'length',5,10)){
        return false;
    }

    var fax = $('input[name=fax]').val();
    if(!judge_validate(4,'传真',fax,false,'length',5,30)){
        return false;
    }

    var email = $('input[name=email]').val();
    if(!judge_validate(4,'企业邮箱',email,true,'email')){
        return false;
    }

    var company_legal = $('input[name=company_legal]').val();
    if(!judge_validate(4,'法人代表',company_legal,true,'length',1,30)){
        return false;
    }

    // 判断是否上传图片
    var uploader = $('#myUploader').data('zui.uploader');
    var files = uploader.getFiles();
    var filesCount = files.length;

    var imgObj = $('#myUploader').closest('.resourceBlock').find(".upload_img");

    if( (!judge_list_checked(imgObj,3)) && filesCount <=0 ) {//没有选中的
        layer_alert('请选择要上传的营业执照！',3,0);
        return false;
    }

    var company_peoples_num = $('select[name=company_peoples_num]').val();
    var judge_seled = judge_validate(1,'单位人数',company_peoples_num,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择单位人数",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var company_industry_id = $('select[name=company_industry_id]').val();
    var judge_seled = judge_validate(1,'所属行业',company_industry_id,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择所属行业",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var company_certificate_no = $('input[name=company_certificate_no]').val();
    if(!judge_validate(4,'资质认定证书编号',company_certificate_no,true,'length',4,30)){
        return false;
    }

    // 开始时间
    var begin_date = $('input[name=ratify_date]').val();
    if(!judge_validate(4,'批准日期',begin_date,true,'date','','')){
        return false;
    }

    // 结束时间
    var end_date = $('input[name=valid_date]').val();
    if(!judge_validate(4,'有效期至',end_date,true,'date','','')){
        return false;
    }

    if( end_date !== ''){
        if(begin_date == ''){
            layer_alert("请选择批准日期",3,0);
            return false;
        }
        if( !judge_validate(4,'有效期至必须',end_date,true,'data_size',begin_date,5)){
            return false;
        }
    }


    var laboratory_addr = $('input[name=laboratory_addr]').val();
    if(!judge_validate(4,'实验室地址',laboratory_addr,false,'length',1,200)){
        return false;
    }

    var company_contact_name = $('input[name=company_contact_name]').val();
    if(!judge_validate(4,'联系人',company_contact_name,true,'length',1,30)){
        return false;
    }

    var company_contact_mobile = $('input[name=company_contact_mobile]').val();
    if(!judge_validate(4,'联系人手机',company_contact_mobile,true,'mobile','','')){
        return false;
    }

    var company_contact_tel = $('input[name=company_contact_tel]').val();
    if(!judge_validate(4,'固定电话',company_contact_tel,false,'length',6,20)){
        return false;
    }

    // 是否已阅读并同意  注册服务协议
    var read_and_agree = get_list_checked('read_and_agree',2,1);
    if(read_and_agree == '') {
        layer_alert("请阅读并同意《注册服务协议》",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;

    }

    var admin_username = $('input[name=admin_username]').val() || '';
    console.log('admin_username',  admin_username);
    if(!judge_validate(4,'用户名',admin_username,false,'length',6,20)){
        return false;
    }
    var admin_password = $('input[name=admin_password]').val() || '';
    var sure_password = $('input[name=sure_password]').val() || '';
    if( id<=0 || admin_password != '' || sure_password != ''){

        // var admin_password = $('input[name=admin_password]').val();
        if(!judge_validate(4,'密码',admin_password,true,'length',6,20)){
            return false;
        }
        // var sure_password = $('input[name=sure_password]').val();
        if(!judge_validate(4,'确认密码',sure_password,true,'length',6,20)){
            return false;
        }

        if(admin_password !== sure_password){
            layer_alert('确认密码和密码不一致！',5,0);
            return false;
        }
    }


    // 上传图片
    if(filesCount > 0){
        var layer_index = layer.load();
        uploader.start();
        var intervalId = setInterval(function(){
            var status = uploader.getState();
            console.log('获取上传队列状态代码',uploader.getState());
            if(status == 1){
                layer.close(layer_index);//手动关闭
                clearInterval(intervalId);
                if(commonaction.isUploadSuccess(uploader)){// 都上传成功
                    ajax_save(id);
                }
            }
        },1000);
    }else{
        ajax_save(id);
    }

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
                    var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    if(id > 0) reset_total = false;
                    // parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                    goTop(LOG_OUT_URL);
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
