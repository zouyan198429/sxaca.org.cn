
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


    $('#myUploader').uploader({
        url: UPLOAD_PDF_URL,
        lang: 'zh_cn',// 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
        file_data_name:'photo',//   文件域在表单中的名称  默认 'file'
        filters:{
            // 只允许上传图片或图标（.ico）
            mime_types: [
                {title: 'PDF', extensions: 'pdf'},
                // {title: '图标', extensions: 'ico'}
            ],
            // 最大上传文件为 2MB
            max_file_size: '100mb',
            // 不允许上传重复文件
            // prevent_duplicates: true
        },
        multipart_params:{pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
        resize:{quuality: 40},
        // limitSumCount:1,// 自定义的可以上传的总数，一直不变动
        limitFilesCount:1, // 限制文件上传数目  false（默认）或数字
        multi_selection:false,// 是否可用一次选取多个文件    默认 true
        flash_swf_url: FLASH_SWF_URL, // "http://work.kefu.cunwo.net/dist/lib/uploader/Moxie.swf",// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
        silverlight_xap_url:SILVERLIGHT_XAP_URL,// "http://work.kefu.cunwo.net/dist/lib/uploader/Moxie.xap",// silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
        onUploadFile: function(file) {
            console.log('上传成功', file);
        },
        onFileUploaded: function(file, responseObject) {// 当队列中的一个文件上传完成后触发
            console.log('onFileUploaded上传成功', responseObject);
            var responseObj = $.parseJSON( responseObject.response );
            console.log('onFileUploaded上传成功remoteData',responseObj);
            console.log('onFileUploaded-file', file);
            $('input[name=resource_id]').val(file.remoteId);

        },
    });

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

    // var type_name = $('input[name=type_name]').val();
    // if(!judge_validate(4,'类型名称',type_name,true,'length',1,20)){
    //     return false;
    // }
    //
    // var sort_num = $('input[name=sort_num]').val();
    // if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
    //     return false;
    // }

    // 对样品数据进行有效性验证
    let has_err = false; // 内部 是否有错 true:有错  false:没有错
    let hasVals = false;
    $('.sample_list').each(function(){
        let sampleObj = $(this);
        let sample_one = sampleObj.data('sample_one');
        console.log('sample_one=', sample_one);
        let sample_txt = '样品编号:' + sample_one;
        // 遍历数据--输入框
        let input_has_val = false;// 是否有值
        sampleObj.find('input').each(function () {
            let inputObj = $(this);
            let data_name  = inputObj.data('name');
            console.log('data_name=', data_name);
            let input_val = inputObj.val();
            console.log('input_val=', input_val);
            // 验证数据
            var judge_seled = judge_validate(1,sample_txt + '-' + data_name,input_val,true,'length',1,300);
            if( judge_seled != ''){
                $err_txt = sample_txt + '-' + data_name + '不能为空！';
                layer_alert($err_txt,3,0);
                //err_alert('<font color="#000000">' + judge_seled + '</font>');
                console.log('judge_seled=', judge_seled);
                has_err = true;
                return false;
            }
            if(trim(input_val) !== ''){
                input_has_val = true;
            }
        });
        if(has_err){
            return false;
        }
        // 没有值
        if(!input_has_val){
            layer_alert(sample_txt + '不能没有数据信息！',3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            has_err = true;
            return false;
        }
        hasVals = true;
    });
    if(has_err){
        return false;
    }
    // 没有输入值
    if(!hasVals){
        layer_alert('不能没有样品数据！',3,0);
        return false;
    }

    // 检测所用仪器
    let instrument_num = $('.instrument_list').length;
    console.log('instrument_num=', instrument_num);
    if(instrument_num <= 0){
        layer_alert('不能没有检测所用仪器！',3,0);
        return false;
    }
    has_err = false; // 内部 是否有错 true:有错  false:没有错
    let i = 1;
    $('.instrument_list').each(function(){
        let infoObj = $(this);
        var instrument_model = infoObj.find("input[name='instrument_model[]']").val();
        console.log('instrument_model=', instrument_model);
        if(!judge_validate(4,'检测所用仪器【名称/型号】',instrument_model,true,'length',1,50)){
            has_err = true;
            return false;
        }
        // 出厂编号
        var factory_number = infoObj.find('input[name="factory_number[]"]').val();
        console.log('factory_number=', factory_number);
        if(!judge_validate(4,'检测所用仪器【出厂编号】',factory_number,false,'length',1,50)){
            has_err = true;
            return false;
        }
        // 检定日期
        var check_date = infoObj.find('input[name="check_date[]"]').val();
        console.log('check_date=', check_date);
        if(!judge_validate(4,'检测所用仪器【检定日期】',check_date,false,'length',1,50)){
            has_err = true;
            return false;
        }

        // 有效期
        var valid_date = infoObj.find('input[name="valid_date[]"]').val();
        console.log('valid_date=', valid_date);
        if(!judge_validate(4,'检测所用仪器【有效期】',valid_date,false,'length',1,50)){
            has_err = true;
            return false;
        }

        i++;
    });
    if(has_err){
        return false;
    }


    // 标准物质
    let standard_num = $('.standard_list').length;
    console.log('standard_num=', standard_num);
    if(standard_num <= 0){
        layer_alert('不能没有标准物质！',3,0);
        return false;
    }
    has_err = false; // 内部 是否有错 true:有错  false:没有错
    let k = 1;
    $('.standard_list').each(function(){
        let infoObj = $(this);
        console.log('infoObj=', infoObj);
        // 名称
        var standard_name = infoObj.find('input[name="standard_name[]"]').val();
        console.log('standard_name=', standard_name);
        if(!judge_validate(4,'标准物质【名称】',standard_name,false,'length',1,50)){
            has_err = true;
            return false;
        }
        // 生产单位
        var produce_unit = infoObj.find('input[name="produce_unit[]"]').val();
        console.log('produce_unit=', produce_unit);
        if(!judge_validate(4,'标准物质【生产单位】',produce_unit,false,'length',1,50)){
            has_err = true;
            return false;
        }
        // 批号
        var batch_number = infoObj.find('input[name="batch_number[]"]').val();
        console.log('batch_number=', batch_number);
        if(!judge_validate(4,'标准物质【批号】',batch_number,false,'length',1,50)){
            has_err = true;
            return false;
        }
        // 有效期
        var standard_valid_date = infoObj.find('input[name="standard_valid_date[]"]').val();
        console.log('standard_valid_date=', standard_valid_date);
        if(!judge_validate(4,'标准物质【有效期】',standard_valid_date,false,'length',1,50)){
            has_err = true;
            return false;
        }

        k++;
    });
    if(has_err){
        return false;
    }

    // 方法依据
    let method_num = $('.method_list').length;
    if(method_num <= 0){
        layer_alert('不能没有方法依据！',3,0);
        return false;
    }
    has_err = false; // 内部 是否有错 true:有错  false:没有错
    $('.method_list').each(function(){
        let infoObj = $(this);
        // 方法依据内容
        var content = infoObj.find('textarea[name="content[]"]').val();
        if(!judge_validate(4,'方法依据内容',content,true,'length',2,5000)){
            has_err = true;
            return false;
        };
    });
    if(has_err){
        return false;
    }

    // 判断是否上传图片
    var uploader = $('#myUploader').data('zui.uploader');
    var files = uploader.getFiles();
    var filesCount = files.length;

    var imgObj = $('#myUploader').closest('.resourceBlock').find(".upload_img");

    if( (!judge_list_checked(imgObj,3)) && filesCount <=0 ) {//没有选中的
        layer_alert('请选择要上传的资料！',3,0);
        return false;
    }

    var index_query = layer.confirm('请仔细检查各项数据信息，谨防填选错误！<br/>提交后不能修改！', {
        btn: ['确认提交','返回检查'] //按钮
    }, function(){
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
        layer.close(index_query);
    }, function(){
    });


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
