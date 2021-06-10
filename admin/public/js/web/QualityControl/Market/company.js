
$(function(){

});

//业务逻辑部分
var otheraction = {
    browseInfo:function(id, tishi){//下载网页打印机驱动
        var url = COMPANY_INFO_URL + id;
        commonaction.browse_file(url, tishi,850,500, 0);
        return false;
    },
    schedule : function(id, company_name){// 查看能力附表

        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(SCHEDULE_SHOW_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        // var weburl = SCHEDULE_SHOW_URL + id + '?' + url_params;
        // var weburl = SCHEDULE_SHOW_URL + '?company_id=' + id ;// + '&' + url_params;
		var weburl = SCHEDULE_SHOW_URL + '' + id ;
        console.log(weburl);
		goOpenUrl(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        // var tishi = company_name;//  + "-能力附表";
        // layeriframe(weburl,tishi,980,500,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    },
    company_statement_num : function(id, company_name){// 查看机构自我声明
        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(COMPANY_STATEMENT_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_STATEMENT_URL + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name;//  + "-机构自我声明管理";
        layeriframe(weburl,tishi,1050,500,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    },
    company_punish_num : function(id, company_name){// 查看机构处罚
        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(COMPANY_PUNISH_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_PUNISH_URL + '?company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name;// + "-机构处罚管理";
        layeriframe(weburl,tishi,1050,500,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    },
    company_supervise : function(id, company_name){// 查看或修改企业简介
        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(COMPANY_SUPERVISE_EDIT_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_SUPERVISE_EDIT_URL + '?company_id=' + id + "&company_hidden=1" ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name; + "-监督检查信息";
        layeriframe(weburl,tishi,1050,500,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    },
    company_ability : function(id, company_name){// 能力验证
        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(COMPANY_PUNISH_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_ABILITY_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-能力验证";
        layeriframe(weburl,tishi,1050,500,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    },
    company_inspect : function(id, company_name){// 监督检查
        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(COMPANY_PUNISH_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_INSPECT_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-监督检查";
        layeriframe(weburl,tishi,1050,500,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    },
    company_news : function(id, company_name){// 其它
        //获得表单各name的值
        // var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        // console.log(COMPANY_PUNISH_URL);
        // console.log(data);
        // var url_params = get_url_param(data);// parent.get_url_param(data);
        var weburl = COMPANY_NEWS_URL + '?hidden_option=1&company_id=' + id ;// + url_params;// + id + '?' + url_params;
        // var weburl = STAFF_SHOW_URL + '?company_id=' + id
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = company_name + "-其它";
        layeriframe(weburl,tishi,1050,600,SHOW_CLOSE_OPERATE, undefined, null, 2);
        return false;
    }
};

