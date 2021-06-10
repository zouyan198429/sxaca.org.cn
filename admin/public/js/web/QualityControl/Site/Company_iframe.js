// 调用页面需要定义列表地址 SEARCH_COMPANY_URL
$(function(){
    // 按单位全称查询
    $(document).on("click","#searchBtn",function(){
        $('.search_company_info').hide();
        $('.company_grade_block').hide();
        let obj = $(this);
        var company_name = $('input[name=company_name]').val();
        consoleLogs([company_name]);
        if(!judge_validate(4,'单位全称',company_name,true,'length',1,100)){
            return false;
        }
        // ajax请求数据
        // 验证通过
        SUBMIT_FORM = false;//标记为已经提交过
        var data = {'company_name' : company_name};// $("#addForm").serialize();
        console.log(COMPANY_NAME_SEARCH_URL);
        console.log(data);
        var layer_index = layer.load();
        $.ajax({
            'type' : 'POST',
            'url' : COMPANY_NAME_SEARCH_URL,
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
                    // layer.msg('操作成功！', {
                    //     icon: 1,
                    //     shade: 0.3,
                    //     time: 3000 //2秒关闭（如果不配置，默认是3秒）
                    // }, function(){
                    //     // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
                    //     var hidden_option = $('input[name=hidden_option]').val() || 0;
                    //     if( (hidden_option & 8192) != 8192){
                    //         var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    //         if(id > 0) reset_total = false;
                    //         parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                    //     }else{
                    //         eval( 'window.parent.' + PARENT_BUSINESS_FUN_NAME + '(paramsToObj(decodeURIComponent(data), 1), ret.result, 2)');
                    //         parent_reset_list();// 关闭弹窗
                    //     }
                    //     //do something
                    // });
                    // var supplier_id = ret.result['supplier_id'];
                    //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                    //    SUPPLIER_ID_VAL = supplier_id;
                    //    $('input[name="supplier_id"]').val(supplier_id);
                    //}
                    // save_success();
                    var info = ret.result;
                    $('.search_company_name').html(info.company_name);
                    $('.company_grade_text').html(info.company_grade_text);
                    $('.search_company_info').show();
                    if(info.company_grade > 1){
                        $('.company_grade_date').html(info.company_begin_time_format + ' 至 ' + info.company_end_time_format);
                        $('.company_grade_block').show();
                    }
                }
                SUBMIT_FORM = true;//标记为未提交过
                layer.close(layer_index);//手动关闭
            }
        });

        return false;
    });

});
