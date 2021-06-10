// 调用页面需要定义列表地址 SEARCH_COMPANY_URL
$(function(){
    // 按证书号查询
    $(document).on("click",".searchbtn_no",function(){
        let obj = $(this);
        let keyword = obj.parent().find('input[name=keyword]').val();
        console.log('keyword=', keyword);
        if(!judge_validate(4, '证书号', keyword, false, 'length', 1, 30)){
            return false;
        }
        webURL = SEARCH_COMPANY_URL + '?qkey=4&field=company_certificate_no&keyword=' + keyword;
        console.log('webURL=', webURL);
        go(webURL);
        return false;
    });
    // 企业名称等搜索
    $(document).on("click",".searchbtn_company",function(){
        let obj = $(this);
        // 查询字段类型

        var company_field = obj.parent().find('input[name=company_field]:checked').val() || '';
        var judge_seled = judge_validate(1,'企业信息',company_field,true,'custom',/^(company_name|company_credit_code)$/,"");
        if(judge_seled != ''){
            layer_alert("请选择企业信息",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            return false;
        }
        let keyword = obj.parent().find('input[name=keyword]').val();
        console.log('keyword=', keyword);
        if(!judge_validate(4, '关键字', keyword, false, 'length', 1, 30)){
            return false;
        }
        webURL = SEARCH_COMPANY_URL + '?qkey=1&field=' + company_field +'&keyword=' + keyword;
        console.log('webURL=', webURL);
        go(webURL);
        return false;
    });
    // 按检测项目查询
    $(document).on("click",".searchbtn_range",function(){
        let obj = $(this);
        // 查询字段类型
        var rang_f_type = obj.parent().find('input[name=rang_f_type]:checked').val() || '';
        var judge_seled = judge_validate(1,'标准名称或标准编号',rang_f_type,true,'custom',/^[12]$/,"");
        if(judge_seled != ''){
            layer_alert("请选择标准名称或标准编号",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            return false;
        }
        let keyword = obj.parent().find('input[name=keyword]').val();
        console.log('keyword=', keyword);
        if(!judge_validate(4, '关键字', keyword, true, 'length', 1, 30)){
            return false;
        }
        webURL = SEARCH_COMPANY_URL + '?qkey=2&rang_f_type=' + rang_f_type + '&field=method_name&keyword=' + keyword;
        console.log('webURL=', webURL);
        go(webURL);
        return false;
    });

});
