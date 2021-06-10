
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

});

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

    var pay_name = $('input[name=pay_name]').val();
    if(!judge_validate(4,'支付方式名称',pay_name,true,'length',1,50)){
        return false;
    }

    var pay_method = $('input[name=pay_method]').val();
    var judge_seled = judge_validate(1,'支付方式代码值',pay_method,true,'custom',/^([1248]|16|32|64|128|256|512|1024|2048|4096|8192|16284|32768)$/,"");
    if(judge_seled != ''){
        layer_alert("支付方式代码值有效值为：【1、2、4、8、16、32、64、128、256、512、1024、2048、4096、8192、16284、32768】",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    // 判断是否上传图片
    var uploader = $('#myUploaderLarge').data('zui.uploader');
    var files = uploader.getFiles();
    var filesCount = files.length;

    var imgObj = $('#myUploaderLarge').closest('.resourceBlock').find(".upload_img");

    // if( (!judge_list_checked(imgObj,3)) && filesCount <=0 ) {//没有选中的
    //     layer_alert('请选择要上传的课程图片！',3,0);
    //     return false;
    // }

    var pay_remarks = $('textarea[name=pay_remarks]').val();
    if(!judge_validate(4,'简要概述',pay_remarks,false,'length',2,500)){
        return false;
    }

    var status_online = $('input[name=status_online]:checked').val() || '';
    var judge_seled = judge_validate(1,'上架状态',status_online,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择上架状态",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var sort_num = $('input[name=sort_num]').val();
    if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
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
