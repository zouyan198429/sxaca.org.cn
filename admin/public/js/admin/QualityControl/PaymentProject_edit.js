
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


window.onload = function() {
    var layer_index = layer.load();
    initPic();
    layer.close(layer_index);//手动关闭
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

// ~~~~~~~~~~~~~上传文件相关的~~~~~~~~~~~~~~~~~~~~~~~

// 文件删除操作时，组织需要加入的参数
function common_del() {
    return {'common_del': 1};
}

function large_del() {
    return {'large_del': 1};
}

function grid_del() {
    return {'grid_del': 1};
}

var FILE_UPLOAD_OBJ = {
    // 'myUploaderCommon':{
    //     'files_type': 0,// 0 图片文件 1 其它文件
    //     'operate_auth': 1 |  4,// 2 | 操作权限 1 查看 ；2 下载 ；4 删除
    //     'icon':'file-image',// 非图片时文件显示的图标--自定义的初始化用，重上传【不管】的会自动生成。 可以参考 common.js 中的变量 FILE_MIME_TYPES file-o  默认
    //     'file_upload_url': UPLOAD_PIC_URL,// "{ { url('api/admin/upload') }}",// 文件上传提交地址 'your/file/upload/url'
    //     'file_down_url': DOWN_FILE_URL,// 文件下载url--自定义格式化数据时可用
    //     'pic_del_url' : DEL_FILE_URL,// 删除图片url
    //     'del_fun_pre' : 'common_del',// 自定义的删除前的方法名，返回要加入删除时的参数对象,无参数 ，需要时自定义方法
    //     'multipart_params' : {pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
    //     'lang' : 'zh_cn', // 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
    //     'file_data_name' : 'photo', //	文件域在表单中的名称	默认 'file'
    //     'checkbox_name' : 'resource_id[]',// 上传后文件id的复选框名称
    //     'limit_files_count' : 1,//   限制文件上传数目	false（默认）或数字
    //     'mulit_selection' : false,//  是否可用一次选取多个文件	默认 true false
    //     'auto_upload' : true,//  当选择文件后立即自动进行上传操作 true / false
    //     'upload_file_filters' : {
    //         // 只允许上传图片或图标（.ico）
    //         mime_types: commonaction.getMineTypes(['pic']),// FILE_MIME_TYPES.pic.mime_types, 下标配置   如 ['pic','pdf',...]
    //         // 最大上传文件为 2MB
    //         max_file_size: '4mb'
    //         // 不允许上传重复文件
    //         // prevent_duplicates: true
    //     },
    //     'baidu_tem_pic_list' : 'baidu_template_upload_pic',// 上传列表格式数的百度数据格式化模型id   baidu_template_upload_pic
    //     'flash_swf_url' : FLASH_SWF_URL,// "{ {asset('dist/lib/uploader/Moxie.swf') }}",// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    //     'silverlight_xap_url' : SILVERLIGHT_XAP_URL, // "{ {asset('dist/lib/uploader/Moxie.xap') }}",// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
    //     'self_upload' : true,//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
    //     'file_upload_method' : 'initPic()',// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
    //     'file_upload_complete' : '',  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
    //     'file_resize' : {quuality: 40},
    //     // resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：
    //     //     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；
    //     //     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；
    //     //     // crop: true,// 是否对图片进行裁剪；
    //     //     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；
    //     //     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。
    //     // },
    //     'pic_list_json': {'data_list': RESOURCE_LIST_COMMON }// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
    //     // 不使用了，会自动格式化 pic_list_json 来 匹配  'static_files':@ json($info['static_files'] ?? [])// 初始静态文件对象数组 ； self_upload = false 使用； 格式 [{remoteData:{id:197}, name: 'zui.js', size: 216159, url: 'http://openzui.com'},...]
    // },
    'myUploaderLarge':{
        'files_type': 0,// 0 图片文件 1 其它文件
        'operate_auth': 1 |  2 | 4,// 1 | 操作权限 1 查看 ；2 下载 ；4 删除
        'icon':'file-o',// 非图片时文件显示的图标--自定义的初始化用，重上传【不管】的会自动生成。 可以参考 common.js 中的变量 FILE_MIME_TYPES file-o  默认
        'file_upload_url': UPLOAD_LARGE_URL,// "{ { url('api/admin/upload') }}",// 文件上传提交地址 'your/file/upload/url'
        'file_down_url': DOWN_FILE_URL,// 文件下载url--自定义格式化数据时可用
        'pic_del_url' : DEL_FILE_URL,// 删除图片url
        'del_fun_pre' : 'large_del',// 自定义的删除前的方法名，返回要加入删除时的参数对象,无参数 ，需要时自定义方法
        'multipart_params' : {pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
        'lang' : 'zh_cn', // 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
        'file_data_name' : 'photo', //	文件域在表单中的名称	默认 'file'
        'checkbox_name' : 'resource_id[]',// //'large_id[]',// 上传后文件id的复选框名称
        'limit_files_count' : 1,//   限制文件上传数目	false（默认）或数字
        'mulit_selection' : true,//  是否可用一次选取多个文件	默认 true false
        'auto_upload' : false,//  当选择文件后立即自动进行上传操作 true / false
        'upload_file_filters' : {
            // 只允许上传图片或图标（.ico）
            mime_types: commonaction.getMineTypes(['pic']),//  FILE_MIME_TYPES.excel.mime_types, 下标配置   如 ['pic','pdf',...] , 'excel', 'pdf', 'doc'
            // 最大上传文件为 2MB
            max_file_size: '5mb'
            // 不允许上传重复文件
            // prevent_duplicates: true
        },
        'baidu_tem_pic_list' : 'baidu_template_upload_pic',// 上传列表格式数的百度数据格式化模型id baidu_template_pic_show
        'flash_swf_url' : FLASH_SWF_URL,// "{ {asset('dist/lib/uploader/Moxie.swf') }}",// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
        'silverlight_xap_url' : SILVERLIGHT_XAP_URL, // "{ {asset('dist/lib/uploader/Moxie.xap') }}",// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
        'self_upload' : true,//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
        'file_upload_method' : 'initPic()',// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
        'file_upload_complete' : '',  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
        'file_resize' : {quuality: 40},
        // resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：
        //     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；
        //     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；
        //     // crop: true,// 是否对图片进行裁剪；
        //     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；
        //     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。
        // },
        'pic_list_json': {'data_list': RESOURCE_LIST_LARGE }// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
        // 不使用了，会自动格式化 pic_list_json 来 匹配  'static_files':@ json($info['static_files'] ?? [])// 初始静态文件对象数组 ； self_upload = false 使用； 格式 [{remoteData:{id:197}, name: 'zui.js', size: 216159, url: 'http://openzui.com'},...]
    },
    // 'myUploaderGrid':{
    //     'files_type': 1,// 0 图片文件 1 其它文件
    //     'operate_auth': 1 | 2,//  | 4,// 操作权限 1 查看 ；2 下载 ；4 删除
    //     'icon':'file-pdf',// 非图片时文件显示的图标--自定义的初始化用，重上传【不管】的会自动生成。 可以参考 common.js 中的变量 FILE_MIME_TYPES file-o  默认
    //     'file_upload_url': UPLOAD_GRID_URL,// "{ { url('api/admin/upload') }}",// 文件上传提交地址 'your/file/upload/url'
    //     'file_down_url': DOWN_FILE_URL,// 文件下载url--自定义格式化数据时可用
    //     'pic_del_url' : DEL_FILE_URL,// 删除图片url
    //     'del_fun_pre' : 'grid_del',// 自定义的删除前的方法名，返回要加入删除时的参数对象,无参数 ，需要时自定义方法
    //     'multipart_params' : {pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
    //     'lang' : 'zh_cn', // 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
    //     'file_data_name' : 'photo', //	文件域在表单中的名称	默认 'file'
    //     'checkbox_name' : 'grid_id[]',// 上传后文件id的复选框名称
    //     'limit_files_count' : 5,//   限制文件上传数目	false（默认）或数字
    //     'mulit_selection' : true,//  是否可用一次选取多个文件	默认 true false
    //     'auto_upload' : false,//  当选择文件后立即自动进行上传操作 true / false
    //     'upload_file_filters' : {
    //         // 只允许上传图片或图标（.ico）
    //         mime_types: commonaction.getMineTypes(['pdf']),// FILE_MIME_TYPES.pdf.mime_types,下标配置   如 ['pic','pdf',...]
    //         // 最大上传文件为 2MB
    //         max_file_size: '100mb'
    //         // 不允许上传重复文件
    //         // prevent_duplicates: true
    //     },
    //     'baidu_tem_pic_list' : 'baidu_template_upload_pic',// 上传列表格式数的百度数据格式化模型id baidu_template_pic_show
    //     'flash_swf_url' : FLASH_SWF_URL,// "{ {asset('dist/lib/uploader/Moxie.swf') }}",// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    //     'silverlight_xap_url' : SILVERLIGHT_XAP_URL, // "{ {asset('dist/lib/uploader/Moxie.xap') }}",// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
    //     'self_upload' : true,//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
    //     'file_upload_method' : 'initPic()',// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
    //     'file_upload_complete' : '',  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
    //     'file_resize' : {quuality: 40},
    //     // resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：
    //     //     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；
    //     //     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；
    //     //     // crop: true,// 是否对图片进行裁剪；
    //     //     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；
    //     //     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。
    //     // },
    //     'pic_list_json': {'data_list': RESOURCE_LIST_GRID }// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
    //     // 不使用了，会自动格式化 pic_list_json 来 匹配  'static_files':@ json($info['static_files'] ?? [])// 初始静态文件对象数组 ； self_upload = false 使用； 格式 [{remoteData:{id:197}, name: 'zui.js', size: 216159, url: 'http://openzui.com'},...]
    // }
};
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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

    //执行一个laydate实例
    // 开始日期
    var startConfig = {
        elem: '.pay_begin_time' //指定元素
        ,type: 'datetime'
        ,value: BEGIN_TIME// '2018-08-18' //必须遵循format参数设定的格式
        // ,min: get_now_format()//'2017-1-1'
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
        elem: '.pay_end_time' //指定元素
        ,type: 'datetime'
        ,value: END_TIME// '2018-08-18' //必须遵循format参数设定的格式
        //  ,min: get_now_format()//'2017-1-1'
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

    //切换收款账号
    $(document).on("change",'input[name=pay_config_id]',function(){
        var pay_config_id = $(this).val();
        console.log('==pay_config_id=', pay_config_id);
        initPayMethodShow(pay_config_id);
    });
    // 切换是否指定金额
    $(document).on("change",'input[name=specified_amount_status]',function(){
        var specified_amount_status = $(this).val();
        console.log('==specified_amount_status=', specified_amount_status);
        initSpecifiedAmountStatusShow(specified_amount_status);
    });
    initSpecifiedAmountStatusShow($('input[name=specified_amount_status]:checked').val() || '');
    // 切换收费生效时间
    $(document).on("change",'input[name=pay_valid_status]',function(){
        var pay_valid_status = $(this).val();
        console.log('==pay_valid_status=', pay_valid_status);
        initPayValidStatusShow(pay_valid_status);
    });
    initPayValidStatusShow($('input[name=pay_valid_status]:checked').val() || '');
    // 切换有效时长
    $(document).on("change",'input[name=valid_limit]',function(){
        var valid_limit = $(this).val();
        console.log('==valid_limit=', valid_limit);
        initValidLimitShow(valid_limit);
    });
    initValidLimitShow($('input[name=valid_limit]:checked').val() || '');

    // 字段-字段值类型选择
    $(document).on("change",'select[name="val_type[]"]',function(){
        var val_type = $(this).val();
        console.log('==val_type=', val_type);
        initValTypeShow($(this), val_type);
    });

    // 字段-是否必填点击
    $(document).on("change",'.field_required_status',function(){
        var required_status = $(this).val();
        console.log('==required_status=', required_status);
        $(this).closest('tr').find('input[name="required_status[]"]').val(required_status);
    });

    // 字段--填写终端点击
    $(document).on("change",'.field_input_status',function(){
        var input_status = $(this).val();
        console.log('==input_status=', input_status);
        var tdObj = $(this).closest('.td_input_status');
        // 获得复选框的值
        var checkboxIdStr = get_list_checked(tdObj, 3,1,'');
        var checkboxIdBit = getBitJoinVal(checkboxIdStr.split(','));
        tdObj.find('input[name="input_status[]"]').val(checkboxIdBit);
    });

    // 字段--显示终端点击
    $(document).on("change",'.field_show_status',function(){
        var show_status = $(this).val();
        console.log('==show_status=', show_status);
        var tdObj = $(this).closest('.td_show_status');
        // 获得复选框的值
        var checkboxIdStr = get_list_checked(tdObj, 3,1,'');
        var checkboxIdBit = getBitJoinVal(checkboxIdStr.split(','));
        tdObj.find('input[name="show_status[]"]').val(checkboxIdBit);
    });

    initAnswer(FIELDS_DATA_LIST, 1);// 初始化字段列表
});

// 切换字段-字段值类型选择
function initValTypeShow(obj, val_type){
  var tr_obj = obj.closest('tr');
  // var kvObj = getAttrVal(FIELD_CONFIG, 'val_type', true, {});
  if(valInArrOrObj([FIELD_VAL_TYPE_RADIO, FIELD_VAL_TYPE_CHECKBOX], val_type)){
      tr_obj.find('textarea[name="sel_items[]"]').show();
  }else{
      tr_obj.find('textarea[name="sel_items[]"]').hide();
  }
}

// 切换是否指定金额
function initSpecifiedAmountStatusShow(specified_amount_status){
    if(specified_amount_status == SPECIFIED_AMOUNT_STATUS_FIXED){
        $('.tr_pay_amount').show();
    }else{
        // $('input[name=pay_amount]').val(0);
        $('.tr_pay_amount').hide();
    }
}

// 切换收费生效时间
function initPayValidStatusShow(pay_valid_status){
    if(pay_valid_status == PAY_VALID_STATUS_FIXED){
        $('.tr_pay_time').show();
    }else{
        // $('input[name=pay_begin_time]').val('');
        // $('input[name=pay_end_time]').val('');
        $('.tr_pay_time').hide();
    }
}

// 切换有效时长
function initValidLimitShow(valid_limit){
    if(valid_limit == VALID_LIMIT_FIXED){
        $('.tr_valid_limit').show();
    }else{
        // $('input[name=limit_year]').val(0);
        // $('input[name=limit_day]').val(0);
        // $('input[name=limit_hour]').val(0);
        // $('input[name=limit_minute]').val(0);
        // $('input[name=limit_second]').val(0);
        $('.tr_valid_limit').hide();
    }
}

// 根据支付配置id,重新组织显示收款类型
function initPayMethodShow(pay_config_id) {
    var data = {id:pay_config_id};
    console.log(PAY_CONFIG_INFO_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : PAY_CONFIG_INFO_URL,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                // go(LIST_URL);

                // countdown_alert("操作成功!",1,5);
                // parent_only_reset_list(false);
                // wait_close_popus(2,PARENT_LAYER_INDEX);

                //操作成功
                var info = ret.result.info;
                console.log('==info==', info);
                var open_status = info.open_status;// 是否开启
                var pay_method = info.pay_method;// 可用的值
                console.log('==open_status==', open_status);
                console.log('==pay_method==', pay_method);
                $('.sel_pay_method').find('input[name="pay_method[]"]').each(function () {
                    var checkObj = $(this);
                    var checkVal = checkObj.val();
                    console.log('==checkVal==', checkVal);
                    checkObj.prop('checked', false);
                    if(open_status == 1 &&  ((pay_method & checkVal) == checkVal)){
                        checkObj.prop('disabled', false);
                    }else{
                        checkObj.prop('disabled', true);
                    }

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
        // var subject_type = $('input[name=subject_type]:checked').val() || '';
        // if([1,2].indexOf(parseInt(subject_type)) < 0) {// 不存在
        //     layer_alert("请选择单选或多选才能进行此操作！",3,0);
        //     return false;
        // }
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

    // 付款/收款分类
    if(!judge_list_checked('seledTypeNo',2)) {//没有选中的
        layer_alert('请选择付款/收款分类！',3,0);
        return false;
    }

    var title = $('input[name=title]').val();
    if(!judge_validate(4,'收费名称',title,true,'length',1,200)){
        return false;
    }

    var has_explain = false;// 是否有 【收款说明图片】或【收款文字说明】

    // 判断是否上传图片
    var uploader = $('#myUploaderLarge').data('zui.uploader');
    var files = uploader.getFiles();
    var filesCount = files.length;

    var imgObj = $('#myUploaderLarge').closest('.resourceBlock').find(".upload_img");

    if( (!judge_list_checked(imgObj,3)) && filesCount <=0 ) {//没有选中的
        // layer_alert('请选择要上传的课程图片！',3,0);
        // return false;
    }else{
        has_explain = true;
    }

    var pay_explain = $('textarea[name=pay_explain]').val();
    var judge_seled = judge_validate(1,'收款文字说明',pay_explain,true,'length',1,7000);
    if(judge_seled != ''){

    }else{
        has_explain = true;
    }
    // if(!judge_validate(4,'收款文字说明',pay_explain,false,'length',0,7000)){
    //     return false;
    // }
    if(!has_explain){
        layer_alert("【收款说明图片】或【收款文字说明】必须至少二选一",3,0);
        return false;
    }

    var specified_amount_status = $('input[name=specified_amount_status]:checked').val() || '';
    var judge_seled = judge_validate(1,'是否指定金额',specified_amount_status,true,'custom',getAttrVal(REG_CONFIG, 'specified_amount_status', true, 0),"");
    if(judge_seled != ''){
        layer_alert("请选择是否指定金额",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    if(specified_amount_status == SPECIFIED_AMOUNT_STATUS_FIXED){
        var pay_amount = $('input[name=pay_amount]').val();
        if(!judge_validate(4,'收费金额',pay_amount,true,'doublepositive','','')){
            return false;
        }
    }

    var pay_valid_status = $('input[name=pay_valid_status]:checked').val() || '';
    var judge_seled = judge_validate(1,'收费生效时间',pay_valid_status,true,'custom',getAttrVal(REG_CONFIG, 'pay_valid_status', true, 0),"");
    if(judge_seled != ''){
        layer_alert("请选择收费生效时间",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    if(pay_valid_status == PAY_VALID_STATUS_FIXED){

        // 开始时间
        var pay_begin_time = $('input[name=pay_begin_time]').val();
        if(!judge_validate(4,'开始时间',pay_begin_time,true,'date','','')){
            return false;
        }

        // 结束时间
        var pay_end_time = $('input[name=pay_end_time]').val();
        if(!judge_validate(4,'结束时间',pay_end_time,true,'date','','')){
            return false;
        }

        if( pay_end_time !== ''){
            if(pay_begin_time == ''){
                layer_alert("请选择开始时间",3,0);
                return false;
            }
            if( !judge_validate(4,'结束时间必须',pay_end_time,true,'data_size',pay_begin_time,5)){
                return false;
            }
        }


    }

    // 付款时限
    var has_pay_limit = false;// 是否有付款时限 true:有； false:没有
    // 年
    var pay_limit_year = $('input[name=pay_limit_year]').val();
    var judge_seled = judge_validate(1,'付款时限【年】',pay_limit_year,true,'positive_int','','');
    if(judge_seled != ''){
        // layer_alert("请输入-付款时限【年】",3,0);
        // //err_alert('<font color="#000000">' + judge_seled + '</font>');
        // return false;
    }else{
        has_pay_limit = true;
    }

    // 天
    var pay_limit_day = $('input[name=pay_limit_day]').val();
    var judge_seled = judge_validate(1,'付款时限【天】',pay_limit_day,true,'positive_int','','');
    if(judge_seled != ''){
        // layer_alert("请输入-付款时限【天】",3,0);
        // //err_alert('<font color="#000000">' + judge_seled + '</font>');
        // return false;
    }else{
        has_pay_limit = true;
    }

    // 时
    var pay_limit_hour = $('input[name=pay_limit_hour]').val();
    var judge_seled = judge_validate(1,'付款时限【时】',pay_limit_hour,true,'positive_int','','');
    if(judge_seled != ''){
        // layer_alert("请输入-付款时限【时】",3,0);
        // //err_alert('<font color="#000000">' + judge_seled + '</font>');
        // return false;
    }else{
        has_pay_limit = true;
    }

    // 分
    var pay_limit_minute = $('input[name=pay_limit_minute]').val();
    var judge_seled = judge_validate(1,'付款时限【分】',pay_limit_minute,true,'positive_int','','');
    if(judge_seled != ''){
        // layer_alert("请输入-付款时限【分】",3,0);
        // //err_alert('<font color="#000000">' + judge_seled + '</font>');
        // return false;
    }else{
        has_pay_limit = true;
    }

    // 秒
    var pay_limit_second = $('input[name=pay_limit_second]').val();
    var judge_seled = judge_validate(1,'付款时限【秒】',pay_limit_second,true,'positive_int','','');
    if(judge_seled != ''){
        // layer_alert("请输入-付款时限【秒】",3,0);
        // //err_alert('<font color="#000000">' + judge_seled + '</font>');
        // return false;
    }else{
        has_pay_limit = true;
    }
    if(!has_pay_limit){
        layer_alert("请输入有效的【付款时限】",3,0);
        return false;
    }

    var valid_limit = $('input[name=valid_limit]:checked').val() || '';
    var judge_seled = judge_validate(1,'有效时长',valid_limit,true,'custom',getAttrVal(REG_CONFIG, 'valid_limit', true, 0),"");
    if(judge_seled != ''){
        layer_alert("请选择【有效时长】",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }
    if(valid_limit == VALID_LIMIT_FIXED){

        // 有效时限
        var has_valid_limit = false;// 是否有有效时限 true:有； false:没有
        // 年
        var limit_year = $('input[name=limit_year]').val();
        var judge_seled = judge_validate(1,'有效时限【年】',limit_year,true,'positive_int','','');
        if(judge_seled != ''){
            // layer_alert("请输入-有效时限【年】",3,0);
            // //err_alert('<font color="#000000">' + judge_seled + '</font>');
            // return false;
        }else{
            has_valid_limit = true;
        }

        // 天
        var limit_day = $('input[name=limit_day]').val();
        var judge_seled = judge_validate(1,'有效时限【天】',limit_day,true,'positive_int','','');
        if(judge_seled != ''){
            // layer_alert("请输入-有效时限【天】",3,0);
            // //err_alert('<font color="#000000">' + judge_seled + '</font>');
            // return false;
        }else{
            has_valid_limit = true;
        }

        // 时
        var limit_hour = $('input[name=limit_hour]').val();
        var judge_seled = judge_validate(1,'有效时限【时】',limit_hour,true,'positive_int','','');
        if(judge_seled != ''){
            // layer_alert("请输入-有效时限【时】",3,0);
            // //err_alert('<font color="#000000">' + judge_seled + '</font>');
            // return false;
        }else{
            has_valid_limit = true;
        }

        // 分
        var limit_minute = $('input[name=limit_minute]').val();
        var judge_seled = judge_validate(1,'有效时限【分】',limit_minute,true,'positive_int','','');
        if(judge_seled != ''){
            // layer_alert("请输入-有效时限【分】",3,0);
            // //err_alert('<font color="#000000">' + judge_seled + '</font>');
            // return false;
        }else{
            has_valid_limit = true;
        }

        // 秒
        var limit_second = $('input[name=limit_second]').val();
        var judge_seled = judge_validate(1,'有效时限【秒】',limit_second,true,'positive_int','','');
        if(judge_seled != ''){
            // layer_alert("请输入-有效时限【秒】",3,0);
            // //err_alert('<font color="#000000">' + judge_seled + '</font>');
            // return false;
        }else{
            has_valid_limit = true;
        }
        if(!has_valid_limit){
            layer_alert("请输入有效的【有效时限】",3,0);
            return false;
        }
    }

    var unique_user_standard = $('input[name=unique_user_standard]:checked').val() || '';
    var judge_seled = judge_validate(1,'唯一用户付款标准',unique_user_standard,true,'custom',getAttrVal(REG_CONFIG, 'unique_user_standard', true, 0),"");
    if(judge_seled != ''){
        layer_alert("请选择【唯一用户付款标准】",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    // 对收集字段进行判断
    var trsObj = $('#data_list').find('tr');
    // if(trsObj.length <= 0){
    //     layer_alert("请先增加收集字段",3,0);
    //     return false;
    // }
    var tr_num = 1;// 记录序号
    var is_err = false;
    trsObj.each(function(){
        var firstStr = "第【" + tr_num + "】条记录";
        var trObj = $(this);
        var tr_field_name = trObj.find('input[name="field_name[]"]').val() || '';
        if(!judge_validate(4,firstStr + '【字段名称】',tr_field_name,true,'length',1,100)){
            is_err = true;
            return false;
        }
        firstStr += '字段名称【' + tr_field_name + '】';
        var val_type = trObj.find('select[name="val_type[]"]').val();
        var judge_seled = judge_validate(1, firstStr + '【字段值类型】',val_type,true,'custom',getPregByObj(getAttrVal(FIELD_CONFIG, 'val_type', true, {}), 1, [], false),'');
        if(judge_seled != ''){
            layer_alert("请选择" + firstStr + '【字段值类型】',3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            is_err = true;
            return false;
        }
        if(valInArrOrObj([FIELD_VAL_TYPE_RADIO, FIELD_VAL_TYPE_CHECKBOX], val_type)){
            // 对选项进行判断
            var sel_items = trObj.find('textarea[name="sel_items[]"]').val();
            if(!judge_validate(4,firstStr + '【选项】',sel_items,true,'length',1,1000)){
                is_err = true;
                return false;
            }

        }
        // 是否必填判断
        var required_status = trObj.find('input[name="required_status[]"]').val();
        var judge_seled = judge_validate(1, firstStr + '【是否必填】',required_status,true,'custom',getPregByObj(getAttrVal(FIELD_CONFIG, 'required_status', true, {}), 1, [], false),'');
        if(judge_seled != ''){
            layer_alert("请选择" + firstStr + '【是否必填】',3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            is_err = true;
            return false;
        }

        // 填写终端--判断
        var input_status_ids = get_list_checked(trObj.find('.td_input_status'), 3,1,'field_input_status');
        if(judge_empty(input_status_ids)) {//没有选中的
            layer_alert('请选择' + firstStr + '【填写终端】',3,0);
            is_err = true;
            return false;
        }
        // 对值进行判断
        if(!judgeValStrInPreg(input_status_ids, getPregByObj(getAttrVal(FIELD_CONFIG, 'input_status', true, {}), 1, [], false), ',')){
            layer_alert('请选择' + firstStr + '【填写终端】正确的值',3,0);
            is_err = true;
            return false;
        }

        // 显示终端--判断
        var show_status_ids = get_list_checked(trObj.find('.td_show_status'), 3,1,'field_show_status');
        if(judge_empty(show_status_ids)) {//没有选中的
            layer_alert('请选择' + firstStr + '【显示终端】',3,0);
            is_err = true;
            return false;
        }
        // 对值进行判断
        if(!judgeValStrInPreg(show_status_ids, getPregByObj(getAttrVal(FIELD_CONFIG, 'show_status', true, {}), 1, [], false), ',')){
            layer_alert('请选择' + firstStr + '【显示终端】正确的值',3,0);
            is_err = true;
            return false;
        }

        tr_num++;// 记录序号
    });
    if(is_err){
        return false;
    }

    var open_status = $('input[name=open_status]:checked').val() || '';
    var judge_seled = judge_validate(1,'开启状态',open_status,true,'custom',getAttrVal(REG_CONFIG, 'open_status', true, 0),"");
    if(judge_seled != ''){
        layer_alert("请选择【开启状态】",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    // var pay_status = $('input[name=pay_status]:checked').val() || '';
    // var judge_seled = judge_validate(1,'收费状态',valid_limit,true,'custom',getAttrVal(REG_CONFIG, 'pay_status', true, 0),"");
    // if(judge_seled != ''){
    //     layer_alert("请选择【收费状态】",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    var handle_method = $('input[name=handle_method]:checked').val() || '';
    var judge_seled = judge_validate(1,'记录处理方式',handle_method,true,'custom',getAttrVal(REG_CONFIG, 'handle_method', true, 0),"");
    if(judge_seled != ''){
        layer_alert("请选择【记录处理方式】",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var sort_num = $('input[name=sort_num]').val();
    if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
        return false;
    }

    var pay_config_id = $('input[name=pay_config_id]:checked').val() || '';
    var judge_seled = judge_validate(1,'收款帐号',pay_config_id,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择收款帐号",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    // 收款开通类型
    if(!judge_list_checked('sel_pay_method',2)) {//没有选中的
        layer_alert('请选择收款开通类型！',3,0);
        return false;
    }

    var invoice_template_id = $('input[name=invoice_template_id]:checked').val() || '';
    var judge_seled = judge_validate(1,'发票开票模板',invoice_template_id,true,'digit',"","");
    if(judge_seled != ''){
        layer_alert("请选择发票开票模板",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var invoice_project_template_id = $('input[name=invoice_project_template_id]:checked').val() || '';
    var judge_seled = judge_validate(1,'发票商品项目模板',invoice_project_template_id,true,'digit',"","");
    if(judge_seled != ''){
        layer_alert("请选择发票商品项目模板",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
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
    // var tbodyObj = $('#data_list');
    // // 1单选；2多选；4判断
    // var subject_type = $('input[name=subject_type]:checked').val() || '';
    // console.log('subject_type', subject_type);
    // $('.answer_judge').hide();
    // $('.answer_many').hide();
    // $('.hand_sure_answer').hide();
    // $('.hand_judge_answer').hide();
    // if(subject_type == '4'){// 判断
    //     $('.answer_judge').show();
    //     // $('.answer_many').hide();
    // }else if( [1,2].indexOf(parseInt(subject_type)) >= 0){// 单选或多选
    //     // $('.answer_judge').hide();
    //     $('.answer_many').show();
    // }else if(subject_type == '32'){// 填空题[确切答案]
    //     $('.hand_sure_answer').show();
    // }else if(subject_type == '16'){// 填空题[人工批阅]
    //     $('.hand_judge_answer').show();
    //     // }else{
    //     //     $('.answer_judge').hide();
    //     //     $('.answer_many').hide();
    // }
    // var key = 'A'.charCodeAt();
    // console.log('key');
    // var val = 1;
    // tbodyObj.find('tr').each(function () {
    //     var trObj = $(this);
    //     var colum = String.fromCharCode(key);
    //     console.log('colum',colum );
    //     trObj.find('.colum').html(colum);
    //     trObj.find('input[name=answer_val]').val(val);
    //     trObj.find('.check_answer').val(val);
    //     switch(subject_type)
    //     {
    //         case 1://1单选；
    //         case '1':
    //             console.log('1单选');
    //             trObj.find('input[name=answer_val]').show();
    //             trObj.find('.check_answer').hide();
    //             break;
    //         case 2://2多选
    //         case '2':
    //             console.log('2多选');
    //             trObj.find('input[name=answer_val]').hide();
    //             trObj.find('.check_answer').show();
    //             break;
    //         default:
    //             console.log('其它' + subject_type);
    //             break;
    //     }
    //     key++;
    //     val *= 2;
    // });
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
    document.write("    <%");
    document.write("    var val_type_option_html = reset_sel_option(getAttrVal(FIELD_CONFIG, 'val_type', true, {}));");
    document.write("    for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    can_modify = true;");
    document.write("    %>");
    document.write("    <tr>");
    document.write("        <td>");
    document.write("            <input type=\"hidden\" name=\"fields_id[]\" value=\"<%=item.id%>\"\/>");
    document.write("            <input type=\"text\" name=\"field_name[]\" class=\"inp wlong\" value=\"<%=item.field_name%>\" placeholder=\"请输入字段\" style=\"width: 120px;\" \/>");
    document.write("        <\/td>");
    document.write("        <td>");
    // document.write("            <select class=\"wmini\" name=\"val_type[]\" style=\"width: 80px;\">");
    // document.write("             <%=val_type_option_html%>");
    // document.write("            <\/select>");
    document.write("             <%=reset_select_item(getAttrVal(FIELD_CONFIG, 'val_type', true, {}), 'val_type[]', item.val_type, '', 'width: 80px;', '')%>");
    document.write("        <\/td>");
    document.write("        <td >");
    document.write("             <textarea name=\"sel_items[]\" placeholder=\"请输入选项\" class=\"layui-textarea\" <%if( !valInArrOrObj([FIELD_VAL_TYPE_RADIO, FIELD_VAL_TYPE_CHECKBOX], item.val_type)){%> style=\"display: none;\"  <%}%> ><%=textareaBRToEnterChar(item.sel_items)%></textarea>");
    document.write("        <\/td>");
    document.write("        <td>");
    document.write("            <input type=\"hidden\" name=\"required_status[]\" value=\"<%=item.required_status%>\"\/>");
    document.write("             <%=reset_radio_checkbox_item('radio', getAttrVal(FIELD_CONFIG, 'required_status', true, {}), 'required_status_' + FIELDS_NUMBER, item.required_status, 0, 'field_required_status', '', '')%>");
    document.write("        <\/td>");
    document.write("        <td class=\"td_input_status\">");
    document.write("            <input type=\"hidden\" name=\"input_status[]\" value=\"<%=item.input_status%>\"\/>");
    document.write("             <%=reset_radio_checkbox_item('checkbox', getAttrVal(FIELD_CONFIG, 'input_status', true, {}), 'input_status_' + FIELDS_NUMBER + '[]', item.input_status, 0, 'field_input_status', '', '')%>");
    document.write("        <\/td>");
    document.write("        <td class=\"td_show_status\">");
    document.write("            <input type=\"hidden\" name=\"show_status[]\" value=\"<%=item.show_status%>\"\/>");
    document.write("             <%=reset_radio_checkbox_item('checkbox', getAttrVal(FIELD_CONFIG, 'show_status', true, {}), 'show_status_' + FIELDS_NUMBER + '[]', item.show_status, 0, 'field_show_status', '', '')%>");
    document.write("        <\/td>");
    // document.write("        <td align=\"center\">");
    // document.write("            <input type=\"radio\" name=\"answer_val\" value=\"\"  <%if( item.is_right == 1){%>  checked=\"checked\"  <%}%> \/>");
    // document.write("            <input type=\"checkbox\" class=\"check_answer\" name=\"check_answer_val[]\" value=\"\" <%if( item.is_right == 1){%>  checked=\"checked\"  <%}%>\/>");
    // document.write("        <\/td>");
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
    document.write("     <%FIELDS_NUMBER++;%>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");

}).call();
