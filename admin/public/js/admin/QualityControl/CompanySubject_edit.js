
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
    // 富文本
    KindEditor.create('textarea.kindeditor', {
        basePath: '/dist/lib/kindeditor/',
        allowFileManager : true,
        bodyClass : 'article-content',
        afterBlur : function(){
            this.sync();
        }
    });
    popSelectInit();// 初始化选择弹窗
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

    $(document).on("change","input[name=subject_type]",function(){
        var subject_type = $(this).val();
        console.log('subject_type', subject_type);
        initAnswerList();// 重新格式化答案列表
        return false;
    });

    initAnswer(ANSWER_DATA_LIST, 1);// 初始化答案列表
});

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
    add : function(){// 增加答案

        // 1单选；2多选；4判断
        var subject_type = $('input[name=subject_type]:checked').val() || '';
        if([1,2].indexOf(parseInt(subject_type)) < 0) {// 不存在
            layer_alert("请选择单选或多选才能进行此操作！",3,0);
            return false;
        }
        var data_list = {
            'data_list' : DEFAULT_DATA_LIST
        };
        initAnswer(data_list, 2);// 初始化答案列表
        return false;
    },
    del : function(obj){// 删除
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var trObj = recordObj.closest('tr');
            trObj.remove();
            initAnswerList();// 重新格式化答案列表
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    moveUp : function(obj){// 上移
        var recordObj = $(obj);
        var current = recordObj.closest('tr');//获取当前<tr>
        var prev = current.prev();  //获取当前<tr>前一个元素
        console.log('index', current.index());
        if (current.index() > 0) {
            current.insertBefore(prev); //插入到当前<tr>前一个元素前
            initAnswerList();// 重新格式化答案列表
        }else{
            layer_alert("已经是第一个答案，不能移动了。",3,0);
        }
        return false;
    },
    moveDown : function(obj){// 下移
        var recordObj = $(obj);
        var current = recordObj.closest('tr');//获取当前<tr>
        var next = current.next(); //获取当前<tr>后面一个元素
        console.log('length', next.length);
        console.log('next', next);
        if (next.length > 0 && next) {
            current.insertAfter(next);  //插入到当前<tr>后面一个元素后面
            initAnswerList();// 重新格式化答案列表
        }else{
            layer_alert("已经是最后一个答案，不能移动了。",3,0);
        }
        return false;
    }
};
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

    // 所属企业
    var company_id = $('input[name=company_id]').val();
    var judge_seled = judge_validate(1,'所属企业',company_id,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择所属企业",3,0);
        return false;
    }


    // 试题分类
    if(!judge_list_checked('seledTypeNo',2)) {//没有选中的
        layer_alert('请选择试题分类！',3,0);
        return false;
    }

    var subject_type = $('input[name=subject_type]:checked').val() || '';
    var judge_seled = judge_validate(1,'试题类型',subject_type,true,'custom',/^([1248]|16|32)$/,"");
    if(judge_seled != ''){
        layer_alert("请选择试题类型",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var title = $('textarea[name=title]').val();
    if(!judge_validate(4,'题目',title,true,'length',0,7000)){
        return false;
    }

    // 判断题-
    if(subject_type == 4){
        var answer = $('input[name=answer]:checked').val() || '';
        var judge_seled = judge_validate(1,'判断答案',answer,true,'custom',/^[12]$/,"");
        if(judge_seled != ''){
            layer_alert("请选择判断答案",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            return false;
        }

    }else if( [1,2].indexOf(parseInt(subject_type)) >= 0){// 单选/多选
        var trsObj = $('#data_list').find('tr');
        if(trsObj.length <= 0){
            layer_alert("请先增加选项",3,0);
            return false;
        }
        var is_err = false;
        var has_answer = false;
        trsObj.each(function(){
            var trObj = $(this);
            var colum = trObj.find('.colum').html();
            var answer_content = trObj.find('input[name="answer_content[]"]').val();
            if(!judge_validate(4,'答案' + colum + '',answer_content,true,'length',1,1000)){
                is_err = true;
                return false;
            }
            switch(subject_type)
            {
                case 1://1单选；
                case '1':
                    console.log('1单选');
                    if(trObj.find('input[name="answer_val"]').is(':checked')){
                        has_answer = true;
                    }
                    break;
                case 2://2多选
                case '2':
                    console.log('2多选');
                    if(trObj.find('input[name="check_answer_val[]"]').is(':checked')){
                        has_answer = true;
                    }
                    break;
                default:
                    console.log('其它' + subject_type);
                    break;
            }
        });
        if(is_err){
            return false;
        }
        // 没有选择答案
        if(!has_answer){
            layer_alert("请标记正确答案",3,0);
            return false;
        }

    }


    var analyse_answer = $('textarea[name=analyse_answer]').val();
    if(!judge_validate(4,'试题分析',analyse_answer,false,'length',0,200000)){
        return false;
    }

    var open_status = $('input[name=open_status]:checked').val() || '';
    var judge_seled = judge_validate(1,'开启状态',open_status,true,'custom',/^[124]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择开启状态",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var sort_num = $('input[name=sort_num]').val();
    if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
        return false;
    }


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
    return false;
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



// 重新格式化答案列表
function initAnswerList(){
    var tbodyObj = $('#data_list');
    // 1单选；2多选；4判断
    var subject_type = $('input[name=subject_type]:checked').val() || '';
    console.log('subject_type', subject_type);
    $('.answer_judge').hide();
    $('.answer_many').hide();
    $('.hand_sure_answer').hide();
    $('.hand_judge_answer').hide();
    if(subject_type == '4'){// 判断
        $('.answer_judge').show();
        // $('.answer_many').hide();
    }else if( [1,2].indexOf(parseInt(subject_type)) >= 0){// 单选或多选
       // $('.answer_judge').hide();
        $('.answer_many').show();
    }else if(subject_type == '32'){// 填空题[确切答案]
        $('.hand_sure_answer').show();
    }else if(subject_type == '16'){// 填空题[人工批阅]
        $('.hand_judge_answer').show();
    // }else{
    //     $('.answer_judge').hide();
    //     $('.answer_many').hide();
    }
    var key = 'A'.charCodeAt();
    console.log('key');
    var val = 1;
    tbodyObj.find('tr').each(function () {
        var trObj = $(this);
        var colum = String.fromCharCode(key);
        console.log('colum',colum );
        trObj.find('.colum').html(colum);
        trObj.find('input[name=answer_val]').val(val);
        trObj.find('.check_answer').val(val);
        switch(subject_type)
        {
            case 1://1单选；
            case '1':
                console.log('1单选');
                trObj.find('input[name=answer_val]').show();
                trObj.find('.check_answer').hide();
                break;
            case 2://2多选
            case '2':
                console.log('2多选');
                trObj.find('input[name=answer_val]').hide();
                trObj.find('.check_answer').show();
                break;
            default:
                console.log('其它' + subject_type);
                break;
        }
        key++;
        val *= 2;
    });
}
// 初始化答案列表
// data_list 数据对象 {'data_list':[{}]}
// type类型 1 全替换 2 追加到后面
function initAnswer(data_list, type){
    var htmlStr = resolve_baidu_template(DYNAMIC_BAIDU_TEMPLATE,data_list,'');//解析
    //alert(htmlStr);
    //alert(body_data_id);
    if(type == 1){
        $('#'+DYNAMIC_TABLE_BODY).html(htmlStr);
    }else{
        $('#'+DYNAMIC_TABLE_BODY).append(htmlStr);
    }
    initAnswerList();// 重新格式化答案列表
}

(function() {
    document.write("");
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    can_modify = true;");
    document.write("    %>");
    document.write("    <tr>");
    document.write("        <td>");
    document.write("            <input type=\"hidden\" name=\"answer_id[]\" value=\"<%=item.id%>\"\/>");
    document.write("            <span class=\"colum\"><\/span>、<input type=\"text\" name=\"answer_content[]\" class=\"inp wlong\" value=\"<%=item.answer_content%>\" placeholder=\"请输入答案\"\/>");
    document.write("        <\/td>");
    document.write("        <td align=\"center\">");
    document.write("            <input type=\"radio\" name=\"answer_val\" value=\"\"  <%if( item.is_right == 1){%>  checked=\"checked\"  <%}%> \/>");
    document.write("            <input type=\"checkbox\" class=\"check_answer\" name=\"check_answer_val[]\" value=\"\" <%if( item.is_right == 1){%>  checked=\"checked\"  <%}%>\/>");
    document.write("        <\/td>");
    document.write("        <td>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveUp(this)\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-up bigger-60\"> 上移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveDown(this)\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-down bigger-60\"> 下移<\/i>");
    document.write("            <\/a>");
    document.write("");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.del(this)\">");
    document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60\"> 移除<\/i>");
    document.write("            <\/a>");
    document.write("        <\/td>");
    document.write("    <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
