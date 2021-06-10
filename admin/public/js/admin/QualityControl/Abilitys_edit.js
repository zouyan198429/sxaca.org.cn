
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
// 标签配置
const TAGS_CONFIG = {
    'project_standards' :{// 能力验证项目标准
        'tag_name':'方法标准',// 标签文字名称
        'init_tags' : PROJECT_STANDARDS_TAGS,// [],// 标签初始化对象  初始化时，只要id 、tag_name 其它的，会根据配置自动完成     [{'id': 0, 'tag_name': '标签名称','id_input_name':'id[]','tag_input_name':'tag_name[]'},..]
        'default_id' : 0,// id默认值
        'id_input_name' : 'project_standard_ids[]',// id 输入框的名称
        'tag_input_name' : 'project_standard_names[]',// tag_name 输入框的名称
        'min_len': 1,// 标签的最小字符长度；> 0 ，才判断;其它值 空 ''：代表不判断
        'max_len': 80,// 标签的最大字符长度；> 0 ，才判断;其它值 空 ''：代表不判断
        'min_num': 1,// 标签的最小数量；> 0 ，才判断;其它值：代表不限
        'max_num': 30// 标签的最大数量；> 0 ，才判断;其它值：代表不限
    },
    'submit_items' :{// 能力验证--提交数据
        'tag_name':'验证数据项',// 标签文字名称
        'init_tags' : SUBMIT_ITEMS_TAGS,// [],// 标签初始化对象 初始化时，只要id 、tag_name 其它的，会根据配置自动完成 [{'id': 0, 'tag_name': '标签名称','id_input_name':'id[]','tag_input_name':'tag_name[]'},..]
        'default_id' : 0,// id默认值
        'id_input_name' : 'submit_item_ids[]',// id 输入框的名称
        'tag_input_name' : 'submit_item_names[]',// tag_name 输入框的名称
        'min_len': 1,// 标签的最小字符长度；> 0 ，才判断;其它值 空 ''：代表不判断
        'max_len': 30,// 标签的最大字符长度；> 0 ，才判断;其它值 空 ''：代表不判断
        'min_num': 1,// 标签的最小数量；> 0 ，才判断;其它值：代表不限
        'max_num': 20// 标签的最大数量；> 0 ，才判断;其它值：代表不限
    }
};

$(function(){
    //执行一个laydate实例
    // 开始日期
    var startConfig = {
        elem: '.join_begin_date' //指定元素
        ,type: 'datetime'
        ,value: BEGIN_TIME// '2018-08-18' //必须遵循format参数设定的格式
        ,min: get_now_format()//'2017-1-1'
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
        elem: '.join_end_date' //指定元素
        ,type: 'datetime'
        ,value: END_TIME// '2018-08-18' //必须遵循format参数设定的格式
        ,min: get_now_format()//'2017-1-1'
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

    // ~~~~~~~~~~~~~~~~~~~标签~~~~~~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 添加标签
    $(document).on("click",".tags_block .tag_add",function(){
        var obj = $(this);
        add_tag(obj);
        return false;
    });
    // 标签删除
    $(document).on("click",".tags_block .close",function(){
        var obj = $(this);
        del_tag(obj);
        return false;
    });
    init_tags();// 初始化标签
    // ~~~~~~~~~~~~~~~~~标签~~~~~~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

});

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

    var ability_name = $('input[name=ability_name]').val();
    if(!judge_validate(4,'检测项目',ability_name,true,'length',1,100)){
        return false;
    }

    var estimate_add_num = $('input[name=estimate_add_num]').val();
    if(!judge_validate(4,'预估参加实验数',estimate_add_num,true,'digit','','')){
        return false;
    }

    // 开始时间
    var begin_date = $('input[name=join_begin_date]').val();
    if(!judge_validate(4,'开始时间',begin_date,true,'date','','')){
        return false;
    }

    // 结束时间
    var end_date = $('input[name=join_end_date]').val();
    if(!judge_validate(4,'结束时间',end_date,true,'date','','')){
        return false;
    }

    if( end_date !== ''){
        if(begin_date == ''){
            layer_alert("请选择开始时间",3,0);
            return false;
        }
        if( !judge_validate(4,'结束时间必须',end_date,true,'data_size',begin_date,5)){
            return false;
        }
    }

    var duration_minute = $('input[name=duration_minute]').val();
    if(!judge_validate(4,'数据提交时限',duration_minute,true,'positive_int','','')){
        return false;
    }

    // 标签判断数量--所有标签
    if(!judge_tags_num()){
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
