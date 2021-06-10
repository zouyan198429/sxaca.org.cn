var ADMIN_AJAX_TYPE_NUM = 1;// admin 后台请求标识
var ADMIN_AJAX_HEADERS_ACCEPT = "application/vnd.myCUNwoApp.v1+json";
// window.name的值，这个值很奇怪，在页面加载时值是正确的，但是加载完成后值就变了--iframe中的页面。
// 当面有方法已经调用并赋值，其它地方就可以直接使用了；用这个方法 getWindowName() 或直接使用全局变量 WINDOW_NAME
var WINDOW_NAME = null;
// headers: {      //请求头
//     Accept: "application/json; charset=utf-8",
//         token: "" + token  //这是获取的token
// },
// var ADMIN_AJAX_HEARDERS = {      //请求头
//     // Accept: "application/json; charset=utf-8",
//     //token: "" + token  //这是获取的token
// };

var FILE_MIME_TYPES = {
    'pic': {
        'files_type': 0,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':1,// 后台对应的编号，标记参考作用
        'icon': 'file-image',// file-o  默认
        'mime_types' :[
            {title: '图片', extensions: 'jpg,jpeg,gif,bmp,png'},
            {title: '图标', extensions: 'ico'}
        ]
    },
    'excel': {
        'files_type': 1,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':2,// 后台对应的编号，标记参考作用
        'icon': 'file-excel',
        'mime_types' :[
            {title: 'excel文件', extensions: 'xls,xlsx'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'pdf':{
        'files_type': 2,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':8,// 后台对应的编号，标记参考作用
        'icon': 'file-pdf',
        'mime_types' :[
            {title: 'PDF', extensions: 'pdf'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'doc': {
        'files_type': 3,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':16,// 后台对应的编号，标记参考作用
        'icon': 'file-word',
        'mime_types' :[
            {title: 'word文件', extensions: 'doc,docx'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'ppt': {
        'files_type': 4,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':32,// 后台对应的编号，标记参考作用
        'icon': 'file-powerpoint',
        'mime_types' :[
            {title: 'ppt文件', extensions: 'ppt,pptx'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'htm': {
        'files_type': 5,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':64,// 后台对应的编号，标记参考作用
        'icon': 'globe',
        'mime_types' :[
            {title: 'excel文件', extensions: 'html,htm'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'android': {
        'files_type': 6,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':128,// 后台对应的编号，标记参考作用
        'icon': 'android',
        'mime_types' :[
            {title: 'android文件', extensions: 'apk'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'windows': {
        'files_type': 7,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':256,// 后台对应的编号，标记参考作用
        'icon': 'file-o',
        'mime_types' :[
            {title: 'windows文件', extensions: 'exe'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'book': {
        'files_type': 8,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':512,// 后台对应的编号，标记参考作用
        'icon': 'book',
        'mime_types' :[
            {title: 'book文件', extensions: 'epub'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'cube':{
        'files_type': 9,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':1024,// 后台对应的编号，标记参考作用
        'icon': 'cube',
        'mime_types' :[
            {title: 'cube文件', extensions: 'pkg,msi,dmg'}
            // {title: '图标', extensions: 'ico'}
        ]
    } ,
    'sketch': {
        'files_type': 10,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':2048,// 后台对应的编号，标记参考作用
        'icon': 'diamond',
        'mime_types' :[
            {title: 'diamond文件', extensions: 'sketch'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'zip':{
        'files_type': 11,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':4096,// 后台对应的编号，标记参考作用
        'icon': 'file-archive',
        'mime_types' :[
            {title: 'zip文件', extensions: 'zip,x-rar,x-7z-compressed'}
            // {title: '图标', extensions: 'ico'}
        ]
    } ,
    'video':{
        'files_type': 12,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':8192,// 后台对应的编号，标记参考作用
        'icon': 'file-movie',
        'mime_types' : [
            {title: 'video文件', extensions: 'mp4,avi,rmvb,rm,flv,mkv,mov,qt,asf,ogg,mod,wmv,mpg,mpeg,dat,asx,wvx,mpe,mpa,vob'}
            // {title: '图标', extensions: 'ico'}
        ]
    },
    'audio':{
        'files_type': 13,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':16384,// 后台对应的编号，标记参考作用
        'icon': 'file-audio',
        'mime_types' :[
            {title: 'audio文件', extensions: 'mp3,wma,acc,ac3,ogg,rm,wav,mid,midi,mka,voc'}
            // {title: '图标', extensions: 'ico'}
        ]
    } ,
    'text':{
        'files_type': 14,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':32768,// 后台对应的编号，标记参考作用
        'icon': 'file-text-o',
        'mime_types' :[
            {title: 'text文件', extensions: 'txt,text'}
            // {title: '图标', extensions: 'ico'}
        ]
    } ,
    'code': {
        'files_type': 15,// files_type 文件类型  0 图片文件 1 其它文件
        'resource_type':65536,// 后台对应的编号，标记参考作用
        'icon': 'file-code',
        'mime_types' : [
            {title: 'code文件', extensions: 'js,php,cs,jsx,css,less,json,java,lua,py,c,cpp,swift,h,sh,rb,yml,ini,sql,xml'}
            // {title: '图标', extensions: 'ico'}
        ]
    }
};


//通用 业务逻辑部分
var commonaction = {
    // 下载文件
    // down_file_url:下载地址 如 var DOWN_FILE_URL = "{{ url('admin/down_file') }}";
    // resource_url 下载的文件相对public 路径 /resource/company/47/pdf/2020/10/24/202010241053524a8f99d830f58729.pdf
    // save_file_name 下载后保存的文件名称 019 Disclosure Arrow Right.pdf  [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
    down_file:function(down_file_url, resource_url, save_file_name){//下载网页打印机驱动
        save_file_name = save_file_name || '';
        var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
        //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
        var url = down_file_url + '?resource_url=' + resource_url + '&save_file_name=' + save_file_name;
        console.log('下载文件：', url);
        // PrintOneURL(url);
        go(url);
        layer.close(layer_index); //手动关闭
    },
    // 浏览文件-- 弹窗中
    // $file_url  文件http路径  http://www.baidu.com/aa.txt
    // file_name  文件名称
    browse_file:function(file_url, file_name, widthnum, heightnum, operate_num){
        widthnum = widthnum || 850;
        heightnum = heightnum || 510;
        if(typeof(operate_num) != 'number'){
            operate_num =  0;
        }
        var tishi = '浏览-' + file_name;
        layeriframe(file_url,tishi,widthnum,heightnum,operate_num);
    },
    // 在浏览器打开页面--新窗口
    // target 不传，默认为 '_blank'
    browse_url:function(url, target){
        goOpenUrl(url, target);
    },
    // 根据上传文件的 下标配置，返回所有的 上传文件扩展名限 数组对象 FILE_MIME_TYPES 中的  mime_types 集合
    // fileTypeKeys 下标配置   如 ['pic','pdf',...]
    getMineTypes:function(fileTypeKeys){
        var mimeTypes = [];
        for (var i = 0; i < fileTypeKeys.length; i++) {
            var typeKey = fileTypeKeys[i];
            console.log('typeKey:', typeKey);
            if(isHasAttr(FILE_MIME_TYPES, typeKey)){
                let itemObj = getAttrVal(FILE_MIME_TYPES, typeKey, null, null);
                var temMimeTypes = getAttrVal(itemObj, 'mime_types', null, null);
                if(!isEmpeyVal(temMimeTypes)){
                    for (var j = 0; j < temMimeTypes.length; j++) {
                        mimeTypes.push(temMimeTypes[j]);
                    }
                }
            }
        }
        console.log('fileTypeKeys=>', mimeTypes);
        return mimeTypes;
    },
    // 根据 文件 的扩展名，获得对应的对象
    // {
    //         'files_type': 0,// files_type 文件类型  0 图片文件 1 其它文件
    //         'icon': 'file-image',// file-o  默认
    //         'mime_types' :[
    //             {title: '图片', extensions: 'jpg,jpeg,gif,png'},
    //             {title: '图标', extensions: 'ico'}
    //         ]
    //     }
    getFileMimeTypeObjByExt:function(ext){
        var reObj = {};
        var seledObj = false;
        for(var i in FILE_MIME_TYPES){
            var mimeTypeObj = FILE_MIME_TYPES[i];
            var temMimeTypes = getAttrVal(mimeTypeObj, 'mime_types', null, null);
            if(!isEmpeyVal(temMimeTypes)){
                for (var j = 0; j < temMimeTypes.length; j++) {
                    var itemMimeTypeObj = temMimeTypes[j];
                    var extensions = getAttrVal(itemMimeTypeObj, 'extensions', null, null);
                    var extObj = extensions.split(",");
                    if(extObj.indexOf(ext.toLowerCase()) >= 0) {//存在
                        reObj = mimeTypeObj;
                        seledObj = true;
                        break ;
                    }
                }
            }
            if(seledObj === true){
                break;
            }
        }
        console.log('==reObj=', reObj);
        return reObj;
    },
    // 判断上传对象中的所有文件是否都上传成功
    // uploader 上传对象
    // true:没有要上传的文件或都已经上传成功  ； false:还有没有上传成功的
    isUploadSuccess:function(uploader){
        var objfiles = uploader.getFiles();
        var filesCount = objfiles.length;
        console.log('上传中的文件', objfiles);
        console.log('上传中的文件的数量', filesCount);
        // for(var f in objfiles) {
        //     var tfObj = objfiles[f];
        if(filesCount <= 0){
            return true;
        }
        for(var f = 0; f < objfiles.length; f++) {
            var tfObj = objfiles[f];
            console.log('文件' + f + ' 的状态 ', tfObj.status);
            // 1 文件队列还没有开始上传，或者上传已暂停或已上传完成。
            // 2 文件队列正在上传中。
            // 4 文件上传失败。
            // 5 文件已上传到服务器。
            var status = tfObj.status;
            if(status != 5){
                return false;
            }

        }
        return true;
    },
    // 一个字符在字符串中出现的次数
    // split()方法将字符串按查找的字符拆分为数组，通过length属性获得数组元素的个数，进行减1操作
    strInCount:function(str, findStr){
        console.log('==str==', str);
        console.log('==findStr==', findStr);
        return str.split(findStr).length - 1;
    },
    // 替换字符内的所有字符为新的字符
    // str 原字符
    // oldStr 要替换的字符
    // newStr 新的字符
    replaceAllStr:function(str, oldStr , newStr){
        // 要替换全部匹配项，可以使用正则表达式：
        // var str = "a<br/>b<br/>c<br/>";
        // re = new RegExp("<br/>","g"); //定义正则表达式
        var re = new RegExp(oldStr,"g");
        //第一个参数是要替换掉的内容，第二个参数"g"表示替换全部（global）。
        // var Newstr = str.replace(re, ""); //第一个参数是正则表达式。
        var Newstr = str.replace(re, newStr);
        //本例会将全部匹配项替换为第二个参数。能将所有的</br>换为空的
        // alert(Newstr); //内容为：abc
        return Newstr;
    }
};


function drop_confirm(msg, url){
    if(confirm(msg)){
        window.location = url;
    }
}
function go(url){
    window.location = url;
}
function goTop(url){
    // 跳出框架在主窗口登录
    // if(top.location!=this.location)	top.location=this.location;
    top.location = url;
}

// 在浏览器打开页面
function goOpenUrl(url, target) {
    let tem_target = target || '_blank';
    window.open(url, tem_target).location;
}

// 调用 index 模块的相关方法-- 在 iframe 页面内部打开新标签
// href iframe打开的url
// text 标签栏标题
function layuiGoIframe(href, text){
    console.log(href, text);
    parent.layui.index.openTabsPage(href, text); //这里要注意的是 parent 的层级关系
}

// 获得window.name
function getWindowName(){
    if(isEmpeyVal(WINDOW_NAME)){
        WINDOW_NAME = window.name;// 是会有类似这样的值：layui-layer-iframe2
    }
    return WINDOW_NAME;
}
getWindowName();// 因为这个值很奇怪，js加载后，值就变了【iframe中】，所以先执行一下并赋值给全局变量

// 判断是不是Layui的iframe弹出层
// true: 是； false:不是
function isLayuiIframePage(){
    var windowName = getWindowName();// 是会有类似这样的值：layui-layer-iframe2
    if(windowName.indexOf("layui-layer") != -1){// 包含
        // consoleLogs(['&&&&&&&&&&包含&&&&&&&&', windowName]);
        return true;
    }else{// 不包含
        // consoleLogs(['&&&&&&&&&&不包含&&&&&&&&', windowName]);
        return false;
    }
}

// 判断是否是iframe弹出层
// true: 是； false:不是
function isIframePage() {
    // 1
    if (self.frameElement && self.frameElement.tagName == "IFRAME") {
        // alert('在iframe中');
        return true;
    }else{
        return false;
    }

    // 2
    // if (window.frames.length != parent.frames.length) {
    //     // alert('在iframe中');
    //     return true;
    // }else{
    //     return false;
    // }

    // 3
    // if (self != top) {
    //    // alert('在iframe中');
    //     return true;
    // }else{
    //     return false;
    // }
}

// 获取Laiui iframe 弹出层 当前窗口索引 PARENT_LAYER_INDEX; 如果不是 Laiui iframe 弹出层 返回null;
function getParentLayerIndex(){
    if(isLayuiIframePage()){
        // return parent.layer.getFrameIndex(window.name)  ;
        return parent.layer.getFrameIndex(getWindowName())  ;
    }else{
        return null;
    }
}

// 让Laiui iframe 弹出层最大化、最小化、关闭
// parentLayerIndex 父级弹出层的 index; 默认当前页面
// operateType 操作类型 [] 1、最大化[默认] ； 2、最小化、 4、关闭;8、还原大小
function operateLayuiIframeSize(parentLayerIndex, operateType) {
    if(getParentLayerIndex() === null){
        return ;// 不是Laiui iframe 弹出层 ，直接返回
    }
    parentLayerIndex = parentLayerIndex || getParentLayerIndex();

    operateType = operateType || 1;
    switch(operateType)
    {
        case 1:// 最大化[默认]
            //让层自适应iframe
            ////parent.layer.iframeAuto(parentLayerIndex);
            parent.layer.full(parentLayerIndex);// 用这个
            break;
        case 2:// 2、最小化
            parent.layer.min(parentLayerIndex);// 用这个
            break;
        case 4:// 4、关闭
            parent.layer.close(parentLayerIndex);// 用这个
            break;
        case 8:// 8、还原大小
            parent.layer.restore(parentLayerIndex);// 还原 后触发的回调
            break;
        default:
            break;
    }
}

// 批量连续执行 让Laiui iframe 弹出层最大化、最小化、关闭
// parentLayerIndex 父级弹出层的 index; 默认当前页面
// operateTypeArr 操作类型 数组 [1] 1、最大化 ； 2、最小化、 4、关闭、8、还原大小; 按数组指定的顺序执行
// waitmillisecond 从第二个开始等待的毫秒数 1000 为 1秒 ； 500[默认]
function operateBathLayuiIframeSize(parentLayerIndex, operateTypeArr, waitmillisecond) {
    // consoleLogs(['******operateTypeArr******222**', parentLayerIndex, operateTypeArr, waitmillisecond, getParentLayerIndex()]);
    if(getParentLayerIndex() === null){
        return ;// 不是Laiui iframe 弹出层 ，直接返回
    }
    // consoleLogs(['******operateTypeArr******333**', parentLayerIndex, operateTypeArr, waitmillisecond]);
    parentLayerIndex = parentLayerIndex || getParentLayerIndex();
    waitmillisecond = waitmillisecond || 500;
    operateTypeArr = operateTypeArr || [];
    if(typeof operateTypeArr !== 'object'){
        operateTypeArr = [];
    }
    // consoleLogs(['******operateTypeArr******444**', parentLayerIndex, operateTypeArr, waitmillisecond]);

    if(isEmpeyVal(operateTypeArr)){
        return ;
    }

    for (var i = 0 ; i< operateTypeArr.length ; i++) {
        var operateType = operateTypeArr[i];
        operateLayuiIframeSize(parentLayerIndex, operateType);
        operateTypeArr.splice(i, 1);
        break;
    }
    // consoleLogs(['******operateTypeArr********', operateTypeArr]);

    if(!isEmpeyVal(operateTypeArr)){
        setTimeout(function () {
            // consoleLogs(['******operateTypeArr***qqqqq*****', parentLayerIndex, operateTypeArr, waitmillisecond]);
            operateBathLayuiIframeSize(parentLayerIndex, operateTypeArr, waitmillisecond);
        }, waitmillisecond);
    }
}

// 获得ajax请求的headers
// in_headers 传入的headers
// type_num 类型 1 admin后台-- 后面扩展用
function get_ajax_headers(in_headers, type_num){
    in_headers = in_headers || {};
    type_num = type_num || 1;
    // 加入指定的 Accept
    let temVal = ADMIN_AJAX_HEADERS_ACCEPT;
    if(isHasAttr(in_headers, "Accept")){// 存在
        let accept = in_headers.Accept;

        if(isEmpeyVal(accept)){// 为空
            in_headers.Accept = temVal;
        }else{// 不为空
            in_headers.Accept = accept  + ';' + temVal;
        }
    }else{// 不存在
        in_headers.Accept = temVal;
    }

    return in_headers;
}

// 根据设置，自动刷新列表数据【每隔一定时间执行一次】
// 参数
// record_page_url  一般就是当前页面/为空时： window.location.href
// tag_key  获得模型表更新时间的关键标签，可为空：不获取
// timeout  获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟
// 使用页面需要这两个参数
// var IFRAME_TAG_KEY = "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
// var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟
// var SUBMIT_FORM = true;//防止多次点击提交 true:可进行提交操作；false:有其它操作正在进行，不可以操作
function autoRefeshList(record_page_url, tag_key, timeout) {
    // let tag_key = IFRAME_TAG_KEY || '';
    if( tag_key !== ''){
        // 自动更新数据
        var autoIframeTagObj = new Object();
        //var record_page_url = window.location.href;
        record_page_url = record_page_url || window.location.href;
        autoIframeTagObj.refeshList = function(){
            let submiting = SUBMIT_FORM;// true:可进行提交操作；false:有其它操作正在进行，不可以操作
            if(typeof(submiting) != 'boolean'){
                submiting =  true;
            }
            console.log('submiting=true-可进行提交操作==真实值为：', submiting);
            if(submiting && window.parent.autoRefeshList(record_page_url, tag_key)){
                console.log('刷新列表数据');
                //刷新当前列表页面-自己页面操作时[适合更新操作-不更新总数]
                list_fun_name = LIST_FUNCTION_NAME || 'reset_list';
                eval( '' + list_fun_name + '(' + true +', ' + true +', ' + false +', 2)');
            }
        };
        // let timeout = IFRAME_TAG_TIMEOUT || 60000;// 默认一分钟
        timeout = timeout || 60000;// 默认一分钟
        setInterval(autoIframeTagObj.refeshList,timeout);
    }
}

// 翻页跳转方法
// objThis 点击的当前按钮对象
// pageType 翻页的类型 1：ajax翻页 2 a 链接翻页--适合前端seo
function btn_go(objThis, pageType){
    let obj = $(objThis);
    var page = parseInt(obj.parent().find('.pagenum').val());
    var reg2 = /^\d+$/;// /^\d+(\.\d{0,})?$/
    if(!reg2.test(page) || page<=0){
        err_alert("请输入正确的页码");
        //var nr_html = "请输入正确的页码";
        //baidutemplate_init_modal(body_data_id+'alert_Modal',0+1+2+4+8,'提示信息',nr_html,'','','');
        return false;
    }
    var totalpage = parseInt(obj.attr("totalpage"));
    if (!page || isNaN(page) || page<=0) { page = 1; }
    if(page > totalpage) { page = totalpage; }
    switch(pageType){
        case 1:
            if($('#page').length>=1){
                $('#page').val(page);
            }
            // 根据页数据更新数据
            // reset_list(true, ajax_async, false, 2);
            console.log(LIST_FUNCTION_NAME);
            eval( LIST_FUNCTION_NAME + '(' + true +', false, false, 2)');
            // ajaxPageList(
            //     dynamic_id,baidu_template_page,ajax_url,true,frm_ids,
            //     false,baidu_template,body_data_id,baidu_template_loding,baidu_template_empty,
            //     page_id,pagesize,total_id,ajax_async
            // );
            break;
        case 2:
            var url_model = obj.parent().parent().find('input[name=url_model]').val();
            console.log('url_model=',url_model);
            var page_tag = obj.parent().parent().find('input[name=page_tag]').val();
            console.log('page_tag=',page_tag);
            // 地址替换为指定页
            go(url_model.replace(page_tag, page));
            break;
        default:
            break;
    }
}

//多少秒后关闭弹窗---间隔一定的时间循环执行一个方法或函数
//sec_num 秒[/次]数-总共执行多少秒[/次] ； 0 -没有限止 ; each_sec_num 值 为 1000时，就是秒次【1秒执行一次】
// each_sec_num 多少时间执行一次 毫秒--默认 1000[1秒]
// loopFun 每次循环要执行的方法 ；如果 有ajax,请用同步的
//        参数
//            intervalId setInterval执行的id
//            close_loop 对象 有 is_close 属性【可控制开关】 ：是否关闭循环 true：关闭 ;false不关闭
//            loopedSec  记录已执行的毫秒数 [计时开始]
//            loop_num  执行次数  1，2，3...
// secFun 每一秒钟循环要执行的方法
//        参数
//            intervalLoopId setInterval执行的id
//            close_loop 对象 有 is_close 属性【可控制开关】 ：是否关闭循环 true：关闭 ;false不关闭
//            do_sec_num  倒记的秒数 如果10秒倒计时： 9，8，7，...0
//            do_num 执行次数  1，2，3...
// secFun_num secFun 参数循环执行的间隔时间  默认 1000（1秒 ）
//样例 // 如：执行10秒，每一秒执行一次循环
// loopDoingFun(10, 1000, function (intervalId, close_loop, loopedSec, loop_num) {
//     console.log('===每次循环的方法开始==1=');
//     console.log('=1==intervalId===', intervalId);
//     console.log('=1==close_loop===', close_loop);
//     console.log('=1==loopedSec===', loopedSec);
//     console.log('=1==loop_num===', loop_num);
//     if(loop_num >= 20) {// 执行次数关闭
//         // clearInterval(intervalId);
//         // close_loop.is_close = true; // -- 一般用这个控制开关
//     }
// }, function (intervalLoopId, close_loop, do_sec_num, do_num) {
//     console.log('===每分钟循环的方法开始==2=');
//     console.log('=2==intervalLoopId===', intervalLoopId);
//     console.log('=2==close_loop===', close_loop);
//     console.log('=2==do_sec_num===', do_sec_num);
//     console.log('=2==do_num===', do_num);
//     if(do_num >= 10) {// 执行次数关闭
//         // clearInterval(intervalLoopId);
//         // close_loop.is_close = true; // -- 一般用这个控制开关
//     }
// }, 1000);
function loopDoingFun(sec_num, each_sec_num, loopFun, secFun, secFun_num){
    console.log('loopDoingFun begin');
    sec_num = sec_num || 0;
    var do_sec_num = sec_num;
    if(judge_judge_digit(do_sec_num) === false){
        do_sec_num = 0;
    }
    each_sec_num = each_sec_num || 1000;
    secFun_num = secFun_num || 1000;
    var close_loop = { is_close: false, is_doing: false};//is_close :是否关闭循环 true：关闭 ;false不关闭; is_doing :方法是否正在执行中 true:执行中，false;执行完毕
    var autoLoopDoFunObj = new Object();
    var sec_i = 0;// 执行次数
    autoLoopDoFunObj.eachSecFun = function(){// 每一分钟要执行的方法
        if(close_loop.is_close === true){// 初其它地方设置为关闭了
            clearInterval(intervalLoopId);
        }
        // 执行方法
        if(close_loop.is_doing !== true){// 如果 前一个还没有执行完，则自动跳过【不执行此次】
            close_loop.is_doing = true;// 标记执行中
            sec_i += 1;// 执行次数
            if(close_loop.is_close === false){
                secFun && secFun(intervalLoopId, close_loop, do_sec_num - 1, sec_i);// 如果 有ajax,请用同步的
            }

            if(sec_num > 0 && do_sec_num > 1){//是数字且大于0
                do_sec_num--;
            }else{//关闭弹窗
                if(sec_num > 0){
                    close_loop.is_close = true;
                    clearInterval(intervalLoopId);
                }
            }

            close_loop.is_doing = false;// 标记执行完毕一个
        }
    };

    // 记时
    // if(sec_num > 0){
    var intervalLoopId =setInterval(autoLoopDoFunObj.eachSecFun,secFun_num);// ,1000
    // }

    // 执行的代码-多少毫秒
    var loopedSec = 0;// 记录已执行的毫秒数
    var loop_doed_i = 0;// 已经执行的次数
    autoLoopDoFunObj.loopExeFun = function(){
        // 执行方法
        loopedSec += each_sec_num;
        loop_doed_i += 1;
        loopFun && loopFun(intervalId, close_loop, loopedSec, loop_doed_i);
        if(close_loop.is_close === true){// 到时间了
            clearInterval(intervalId);
        }
    };
    var intervalId =setInterval(autoLoopDoFunObj.loopExeFun,each_sec_num);
    console.log('loopDoingFun foot');
}

// ****************qrcode插件 显示二维码***************开始*******************
// 注意使用需要引用js jquery.qrcode.min.js

// qrcode插件 显示二维码 --  默认使用canvas方式
// 参数：
//    idName 显示二维码的id名称；如 ： qrcode
//    qrContent 二维码的内容 如： "http://www.helloweba.com"
function showCodeCanvas(idName, qrContent) {
    $('#' + idName).qrcode(toUtf8QRCode(qrContent)); //任意字符串
}

// qrcode插件 显示二维码 --  table方式
// 参数：
//    idName 显示二维码的id名称；如 ： qrcode
//    qrContent 二维码的内容 如： "http://www.helloweba.com"
//    width 二维码的宽度 不传默认250 ；小于此值可能扫码失败
//    height 二维码的高度 不传默认250；小于此值可能扫码失败
function showQRCodeTable(idName, qrContent, width, height){
    width = width || 250;
    height = height || 250;
    $("#" + idName).qrcode({
        render: "table", //table方式
        width: width, //宽度
        height:height, //高度
        text: toUtf8QRCode(qrContent) //任意内容
    });
}
// 识别中文
// 我们试验的时候发现不能识别中文内容的二维码，通过查找多方资料了解到，jquery-qrcode是采用charCodeAt()方式进行编码转换的。
// 而这个方法默认会获取它的Unicode编码，如果有中文内容，在生成二维码前就要把字符串转换成UTF-8，然后再生成二维码。
// 您可以通过以下函数来转换中文字符串：
//
function toUtf8QRCode(str) {
    var out, i, len, c;
    out = "";
    len = str.length;
    for(i = 0; i < len; i++) {
        c = str.charCodeAt(i);
        if ((c >= 0x0001) && (c <= 0x007F)) {
            out += str.charAt(i);
        } else if (c > 0x07FF) {
            out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
            out += String.fromCharCode(0x80 | ((c >>  6) & 0x3F));
            out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
        } else {
            out += String.fromCharCode(0xC0 | ((c >>  6) & 0x1F));
            out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
        }
    }
    return out;
}

// ****************qrcode插件 显示二维码***************结束*******************

// ***************ajax请求***封装****开始******************************************************


// ajax请求成功的默认执行函数
// ret 返回的结果对象
// paramObj 操作的参数对象
// {
//     apiSuccessObj: {},// 操作成功的对象-- 具体参数请看 ajaxAPIOperate 方法
//     apiFailObj: {},// 操作失败的对象-- 具体参数请看 ajaxAPIOperate 方法
// }
var AJAX_SUCESS_FUNCTION = function ajaxSuccessFun(ret, paramObj) {

    if(!ret.apistatus){//失败
        var ajaxAPIFailFun = getAttrVal(paramObj, 'apiFailObj', true, {});
        ajaxAPIFailFun.alert_icon_num = getAttrVal(ajaxAPIFailFun, 'alert_icon_num', true, 3);
        ajaxAPIFailFun.countDownFunIcon = getAttrVal(ajaxAPIFailFun, 'countDownFunIcon', true, 5);
        ajaxAPIOperate(ret, ajaxAPIFailFun);
    }else{//成功
        var ajaxAPISuccessFun = getAttrVal(paramObj, 'apiSuccessObj', true, {});
        ajaxAPISuccessFun.alert_icon_num = getAttrVal(ajaxAPISuccessFun, 'alert_icon_num', true, 1);
        ajaxAPISuccessFun.countDownFunIcon = getAttrVal(ajaxAPISuccessFun, 'countDownFunIcon', true, 1);
        ajaxAPIOperate(ret, ajaxAPISuccessFun);
    }
}

// ajax 接口请求成功或失败默认执行的方法
// ret 返回的结果对象
// operateObj 成功的参数对象
// {
//     operate_txt:'操作成功',// 操作名称--完整的句子 如 审核通过成功！ ；默认 '操作成功'
//     operate_num: 0, // 操作的编号; 默认 0 ； 1弹出显示成功的文字及确定按钮； 2 执行刷新列表操作;
//                     //      4 弹出倒计时的3秒的窗口，并可以指定一个执行函数; 8 其它指定的自定义函数
//                     //      16 : 在4的基础上，指定执行函数关闭弹窗并刷新列表【-适合弹层新加和修改页】
//     alert_icon_num : 3, // operate_num 有1 时 是成功还是失败； 0失败1成功2询问3警告 [默认]4对5错
//     reset_total : false,// operate_num 有2 和 16 时：是否重新从数据库获取总页数 true:重新获取,false不重新获取【默认】
//     countDownFun: '',// operate_num 有4 和 16时：倒计时后，同时要执行的函数 参数 ret ，参数二 paramObj {}；；默认 ''--不执行
//     countDownFunParams: {},// operate_num 有4 和 16时：自定义函数的第二个参数对象 默认 {}
//     countDownFunTime: 3000,// operate_num 有4 和 16时：倒计时；默认 3000-3秒
//     countDownFunIcon: 1,// operate_num 有4 和 16时：0-6 图标 0：紫红叹号--出错警示 ；1：绿色对勾--成功【默认】；2：无图标 ；3：淡黄问号；4：灰色小锁图标；
//             //  5：红色哭脸--         ； 6：绝色笑脸
//     customizeFun: '', // operate_num 有8时：自定义的要执行的函数 参数 ret ，参数二 paramObj {}；默认 ''--不执行
//     customizeFunParams: {},// operate_num 有8时：自定义函数的第二个参数对象 默认 {}
// }
function ajaxAPIOperate(ret, operateObj){
    var operate_txt = getAttrVal(operateObj, 'operate_txt', true, '');
    var operate_num = getAttrVal(operateObj, 'operate_num', true, 0);
    var reset_total = getAttrVal(operateObj, 'reset_total', true, false);
    var msg = ret.errorMsg;
    if(msg === ""){
        msg = operate_txt;// +"成功";
    }
    if(msg === ""){
        msg = "操作成功";
    }

    // 1弹出显示成功的文字及确定按钮--成功或失败
    if( (operate_num & 1) == 1){
        // 0失败1成功2询问3警告4对5错
        var alert_icon_num = getAttrVal(operateObj, 'alert_icon_num', true, 3);
        // countdown_alert(msg,1,5);
        layer_alert(msg,alert_icon_num,0);
    }

    // 4 弹出倒计时的3秒的窗口，并可以指定一个执行函数
    if( (operate_num & 4) == 4 ||  (operate_num & 16) == 16){
        var countDownFun = getAttrVal(operateObj, 'countDownFun', true, '');
        var countDownFunParams = getAttrVal(operateObj, 'countDownFunParams', true, {});
        var countDownFunTime = getAttrVal(operateObj, 'countDownFunTime', true, 3000);
        var countDownFunIcon = getAttrVal(operateObj, 'countDownFunIcon', true, 1);
        layerMsg(msg, countDownFunIcon, 0.3, countDownFunTime, function(ret){
            countDownFun && countDownFun(ret, countDownFunParams);
            if( (operate_num & 16) == 16){
                parent_reset_list_iframe_close(reset_total);// 刷新并关闭  ； reset_total：是否重新获取总数量： true:重新获取,false不重新获取【默认】
            }
        });
    }

    // 2 执行刷新列表操作
    if( (operate_num & 2) == 2){
        // reset_list(true, true);
        console.log(LIST_FUNCTION_NAME);
        eval( LIST_FUNCTION_NAME + '(' + true +', ' + true +', ' + reset_total + ', 2)');
    }

    // 8 其它指定的自定义函数
    if( (operate_num & 8) == 8){
        var customizeFun = getAttrVal(operateObj, 'customizeFun', true, '');
        var customizeFunParams = getAttrVal(operateObj, 'customizeFunParams', true, {});
        customizeFun && customizeFun(ret, customizeFunParams);
    }

}

// ajax请求的操作
// {
//     async: true, //false:同步;true:异步[默认]
//     ajax_url: '',//  请求的url 默认''
//     data: {},//  数据对象 {} 或表单序列化的字符串 $("#addForm").serialize();  格式： aa=1&b=2... ; 默认 ：｛｝
//     ajax_type: 'POST',//  请求的类型 默认POST ;  GET
//     headers:{},//  加入请求头的对象 默认 {}
//     dataType: 'json',//   返回数据类型 默认'json'
//     show_loading: true,//  请求时，是否显示遮罩 true:显示，false:不显示
//     successFun : AJAX_SUCESS_FUNCTION,//   ajax操作成功执行的函数,默认 AJAX_SUCESS_FUNCTION ，参数  ret, paramObj； 可重新写一个方法
//     paramObj: {},//      ajax操作成功执行的函数默认方法 ajaxSuccessFun的参数 格式如下： 或 自定义方法--则此参数自己按自己需要定义对象 {}
//          {
//            apiSuccessObj: {},// 操作成功的对象-- 具体参数请看 ajaxAPIOperate 方法
//            apiFailObj: {},// 操作失败的对象-- 具体参数请看 ajaxAPIOperate 方法
//         }
// }
//  示例
// ajaxQuery({
//     async: true, //false:同步;true:异步[默认]
//     ajax_url: '',//  请求的url 默认''
//     data: { order_no:order_no, pay_order_no:pay_order_no},// 数据对象 {} 或表单序列化的字符串 $("#addForm").serialize();  格式： aa=1&b=2... ; 默认 ：｛｝
//     ajax_type: 'POST',//  请求的类型 默认POST ;  GET
//     headers:{},//  加入请求头的对象 默认 {}
//     dataType: 'json',//   返回数据类型 默认'json'
//     show_loading: true,//  请求时，是否显示遮罩 true:显示，false:不显示
//     successFun : AJAX_SUCESS_FUNCTION,//   ajax操作成功执行的函数,默认 AJAX_SUCESS_FUNCTION ，参数  ret, paramObj； 可重新写一个方法
//     paramObj: {//      ajax操作成功执行的函数默认方法 ajaxSuccessFun的参数 格式如下： 或 自定义方法--则此参数自己按自己需要定义对象 {}
//         apiSuccessObj: {// 操作成功的对象-- 具体参数请看 ajaxAPIOperate 方法
//             operate_txt:'操作成功',// 操作名称--完整的句子 如 审核通过成功！ ；默认 '操作成功'
//             operate_num: 4, // 操作的编号; 默认 0 ； 1弹出显示成功的文字及确定按钮； 2 执行刷新列表操作;
//                             //      4 弹出倒计时的3秒的窗口，并可以指定一个执行函数; 8 其它指定的自定义函数
//                             //      16 : 在4的基础上，指定执行函数关闭弹窗并刷新列表【-适合弹层新加和修改页】
//             alert_icon_num : 1, // operate_num 有1 时 是成功还是失败； 0失败1成功2询问3警告 [默认]4对5错
//             reset_total : false,// operate_num 有2 和 16 时：是否重新从数据库获取总页数 true:重新获取,false不重新获取【默认】
//             countDownFun: '',// operate_num 有4 和 16时：倒计时后，同时要执行的函数 参数 ret ，参数二 paramObj {}；；默认 ''--不执行
//             countDownFunParams: {},// operate_num 有4 和 16时：自定义函数的第二个参数对象 默认 {}
//             countDownFunTime: 3000,// operate_num 有4 和 16时：倒计时；默认 3000-3秒
//             countDownFunIcon: 1,// operate_num 有4 和 16时：0-6 图标 0：紫红叹号--出错警示 ；1：绿色对勾--成功【默认】；2：无图标 ；3：淡黄问号；4：灰色小锁图标；
//             //  5：红色哭脸--         ； 6：绝色笑脸
//             customizeFun: '', // operate_num 有8时：自定义的要执行的函数 参数 ret ，参数二 paramObj {}；默认 ''--不执行
//             customizeFunParams: {}// operate_num 有8时：自定义函数的第二个参数对象 默认 {}
//         },
//         apiFailObj: {// 操作失败的对象-- 具体参数请看 ajaxAPIOperate 方法
//             operate_txt:'操作失败',// 操作名称--完整的句子 如 审核通过成功！ ；默认 '操作成功'
//             operate_num: 1, // 操作的编号; 默认 0 ； 1弹出显示成功的文字及确定按钮； 2 执行刷新列表操作;
//                             //      4 弹出倒计时的3秒的窗口，并可以指定一个执行函数; 8 其它指定的自定义函数
//                             //      16 : 在4的基础上，指定执行函数关闭弹窗并刷新列表【-适合弹层新加和修改页】
//             alert_icon_num : 3, // operate_num 有1 时 是成功还是失败； 0失败1成功2询问3警告 [默认]4对5错
//             reset_total : false,// operate_num 有2 和 16 时：是否重新从数据库获取总页数 true:重新获取,false不重新获取【默认】
//             countDownFun: '',// operate_num 有4 和 16时：倒计时后，同时要执行的函数 参数 ret ，参数二 paramObj {}；；默认 ''--不执行
//             countDownFunParams: {},// operate_num 有4 和 16时：自定义函数的第二个参数对象 默认 {}
//             countDownFunTime: 3000,// operate_num 有4 和 16时：倒计时；默认 3000-3秒
//             countDownFunIcon: 5,// operate_num 有4 和 16时：0-6 图标 0：紫红叹号--出错警示 ；1：绿色对勾--成功【默认】；2：无图标 ；3：淡黄问号；4：灰色小锁图标；
//             //  5：红色哭脸--         ； 6：绝色笑脸
//             customizeFun: '', // operate_num 有8时：自定义的要执行的函数 参数 ret ，参数二 paramObj {}；默认 ''--不执行
//             customizeFunParams: {}// operate_num 有8时：自定义函数的第二个参数对象 默认 {}
//         }
//     }
// });
function ajaxQuery(ajaxObj) {
    // if(typeof headers !== "object"){
    //     headers = {};
    // }
    // console.log('===ajaxObj==:',ajaxObj);
    consoleLogs(['===ajaxObj==:', ajaxObj]);
    var ajax_url = getAttrVal(ajaxObj, 'ajax_url', true, '');
    var data = getAttrVal(ajaxObj, 'data', true, {});
    var headers = getAttrVal(ajaxObj, 'headers', true, {});
    var show_loading = getAttrVal(ajaxObj, 'show_loading', true, true);
    var successFun =  getAttrVal(ajaxObj, 'successFun', true, AJAX_SUCESS_FUNCTION);
    var paramObj =  getAttrVal(ajaxObj, 'paramObj', true, {});
    // console.log('ajax_url:',ajax_url);
    consoleLogs(['===ajax_url==:', ajax_url]);
    // console.log('data:',data);
    consoleLogs(['===data==:', data]);
    // console.log('===show_loading==:',show_loading);
    consoleLogs(['===show_loading==:', show_loading]);
    if(show_loading === true){
        var layer_index = layer.load();
    }//layer.msg('加载中', {icon: 16});
    $.ajax({
        'async': getAttrVal(ajaxObj, 'async', true, true),// true,//false:同步;true:异步
        'type' : getAttrVal(ajaxObj, 'ajax_type', true, 'POST'),
        'url' : ajax_url,//'/pms/Supplier/ajax_del',
        'headers':get_ajax_headers(headers, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : getAttrVal(ajaxObj, 'dataType', true, 'json'),
        'success' : function(ret){
            // console.log('ret:');
            // console.log(ret);
            consoleLogs(['==ret===:', ret]);
            successFun && successFun(ret, paramObj);
            if(show_loading === true){
                layer.close(layer_index);//手动关闭
            }
        }
    });
}

// ***************ajax请求***封装****结束******************************************************
// 打印日志-- 写这个方法的原因是：console.log('aaa:', obj) 这种 方式 在控制台不能展示开对象，只能显示[object:object] 的方字，所以才有了此方法
// 格式：数组对象[] 或 非object类型 的 如： 'aaa' 或  111 或 true
function consoleLogs(logArr) {
    logArr = logArr || [];
    if(typeof logArr !== 'object'){
        logArr = [logArr];
    }

    for (var i=0 ; i< logArr.length ; i++) {
        var temAttr = logArr[i];
        console.log(temAttr);
    }
}

// 获得数据类型
// null:null值
// array:数组 ； object:对象
// string 字符串  ；
// numberString：数字字符串 ; number 数字
// undefined, function，boolean
function getDataTypeStr(o){
    if(o===null){
        return 'null';
    } else if(typeof o == 'object'){
        if( typeof o.length == 'number' ){
            return 'array';
        }else{
            return 'object';
        }

    } else if(typeof o == 'string'){
        if( o.length > 0 && (!isNaN(o)) ){
            return 'numberString';
        }else{
            return 'string';
        }
    }else{
        return typeof o;// 'param is no object type';
    }
}
// 获得数据类型;返回值同 getDataTypeStr 方法
function getDataTypeString(o){
    if(o===null){
        return 'null';
    }
    if(o instanceof Array){
        return 'array';
    }else if( o instanceof Object ){
        return 'object';
    } else if(typeof o == 'string'){
        if( o.length > 0 && (!isNaN(o)) ){
            return 'numberString';
        }else{
            return 'string';
        }
    }else{
        return typeof o;// 'param is no object type';
    }
}
// 获得对象的下标或值的集合数组
// obj 对象 {'aaa' : 1, '2' : '单选', '3':'aaa'}
// getType 获得的类型 1：获得下标数组【默认】；2：获得值数组
// 返回一维数组
function getObjKeysOrVals(obj, getType){
    getType = getType || 1;
    var reArr = [];
    if(getDataTypeStr(obj) == 'array') return obj;// 如果参数是数组，直接返回原数组
    if(getDataTypeStr(obj) != 'object') return reArr;// 如果参数不是对象，返回空数组
    for(var key in obj){
        var val = obj[key];
        if(getType == 1 || getType == '1'){// 下标
            if(reArr.indexOf(key) < 0){
                reArr.push(key);
            }
        }else{// 值
            if(reArr.indexOf(val) < 0){
                reArr.push(val);
            }
        }
    }
    return reArr;
}

// 判断一个值是否在数组中--特点：可以 数字或字符类的数字判断不相同
// arr 需要判断的数组
// val 需要判断的值
// forceEq 强制 全等 ===  ；true:===; false:== 【默认】
function valInArr(arr, val, forceEq) {
    forceEq = forceEq || false;
    if(getDataTypeStr(arr) != 'array') return false;
    for (var i=0 ; i< arr.length ; i++) {
        var temVal = arr[i];
        if(forceEq && temVal === val){
            return true;
        }else if(!forceEq && temVal == val){
            return true;
        }
    }
    return false;
}

// 判断一个值是否在对象中--特点：可以 数字或字符类的数字判断不相同
// obj 对象 {'aaa' : 1, '2' : '单选', '3':'aaa'}
// val 需要判断的值
// getType 获得的类型 1：获得下标数组；2：获得值数组【默认】
// forceEq 强制 全等 ===  ；true:===; false:== 【默认】
function valOrKeyInObj(obj, val, getType, forceEq) {
    getType = getType || 2;
    forceEq = forceEq || false;
    if(getDataTypeStr(obj) != 'object') return false;

    for(var key in obj){
        var temVal = obj[key];
        var judgeVal = temVal;
        if(getType == 1 || getType == '1'){// 下标
            judgeVal = key;
        }
        if(forceEq && judgeVal === val){
            return true;
        }else if(!forceEq && judgeVal == val){
            return true;
        }
    }
    return false;
}

// 判断一个值是否在数组或对象中--特点：可以 数字或字符类的数字判断不相同
// ArrOrObj 对象 {'aaa' : 1, '2' : '单选', '3':'aaa'}
// val 需要判断的值
// getType [对象专用]获得的类型 1：获得下标数组；2：获得值数组【默认】
// forceEq 强制 全等 ===  ；true:===; false:== 【默认】
function valInArrOrObj(ArrOrObj, val, getType, forceEq){
    if(getDataTypeStr(ArrOrObj) == 'array'){
        return valInArr(ArrOrObj, val, forceEq);
    }
    if(getDataTypeStr(ArrOrObj) == 'object'){
        return valOrKeyInObj(ArrOrObj, val, getType, forceEq);
    }
    return false;
}

// textarea <br>转为回车换行
function textareaBRToEnterChar(str){
    var reg = new RegExp("<br>","g");
    str = str.replace(reg,"\r\n");
    reg = new RegExp("<br\/>","g");
    str = str.replace(reg,"\r\n");
    return str;
}

// 根据key数组[一维]，生成对应的正则 /^([1248]|16|32)$/
// keyArr 位所有值数组 -- 一维数组 ；如 [ '1' , '2' , '4']；
// bigArr 已有的正则项数组--一维数组--这是对象，相当于引用传值[值会加入到结果中]，[128,512]
// appendSlash  是否需要加前后的 斜杠 true:需要  ; false:不需要【默认】 ；前端js new RegExp(reg2); 的字符时不需要 前后 加 斜杠
function getPregByKeyArr(keyArr, bigArr, appendSlash){
    bigArr = bigArr || [];
    appendSlash = appendSlash || false;
    var minIntArr = [];// 小于10的数字数组
    // var bigArr = [];// 大于 10的数字或其它
    for (var i = 0 ; i < keyArr.length ; i++) {
        var keySingle = keyArr[i];
        if(keySingle >= 0 && keySingle < 10){
            minIntArr.push(keySingle);
        }else{
            bigArr.push(keySingle);
        }
    }

    var bigArrEmpty = true;
    if(bigArr.length > 0){
        bigArrEmpty = false;
    }
    if(minIntArr.length > 0){
        bigArr.unshift('[' + minIntArr.join("") + ']');
    }

    if(bigArrEmpty){
        if(appendSlash){
            return "\/^" + bigArr.join("") + "$\/";// /^[12]$/
        }else{
            return "^" + bigArr.join("") + "$";// ^[12]$
        }
    }

    if(appendSlash) {
        return "\/^(" + bigArr.join("|") + ")$\/";// /^([1248]|16|32)$/ ；
    }else{
        return "^(" + bigArr.join("|") + ")$";// ^([1248]|16|32)$ ；
    }
}

// 根据key数组[一维]，生成对应的正则 /^([1248]|16|32)$/
// KVObj kv键值对对象 {'1':'单选', '2':'复选'}
// getType 获得的类型 1：获得下标数组【默认】；2：获得值数组
// bigArr 已有的正则项数组--一维数组--这是对象，相当于引用传值[值会加入到结果中]，[128,512]
// appendSlash  是否需要加前后的 斜杠 true:需要  ; false:不需要【默认】 ；前端js new RegExp(reg2); 的字符时不需要 前后 加 斜杠
function getPregByObj(KVObj, getType, bigArr, appendSlash){
    var keyArr = getObjKeysOrVals(KVObj, getType);
    return getPregByKeyArr(keyArr, bigArr, appendSlash);
}

// valArr 需要判断的值数组--必须有值，[1,2,4]
// pregStr 正则
// 返回值 true:都在范围内;false:有不在范围的或数组为空
function judgeValArrInPreg(valArr, pregStr){
    if(valArr.length <= 0) {
        return false;
    }
    for (var i=0 ; i< valArr.length ; i++) {
        var temVal = valArr[i];
        if(!judge_reg(temVal,pregStr)){// 只要有一个值不存正则中
            return false;
        }
    }
    return true;
}

// 判断id串的值，是否在正则表达式中
// varStr id串的值;多个有,逗号【splitStr 参数】分隔
// pregStr 正则
// splitStr 分隔符，默认 ，逗号
// 返回值 true:都在范围内;false:有不在范围的
function judgeValStrInPreg(varStr, pregStr, splitStr){
    splitStr = splitStr || ',';
    var valArr = varStr.split(splitStr);
    return judgeValArrInPreg(valArr, pregStr);
}

// input_type 类型 值 为 ： radio - 单选； checkbox - 复选
// item_json  初始化下拉框json串[注意:item_json];{"item_json":{"1": "北京","2": "天津","3": "上海"}} 中的 item_json下标对象 【kv值】
// input_name 输入框的名称 值  字符串 aaa 或 bbb[]
// checked_val 当前选中的值  可以是 单个值 【数字】 也可以是数组 ['2'] --注意：数组项 是字符串
// disabled_val 当前禁用的值 可以是 单个值 【数字】 也可以是数组 ['2'] --注意：数组项 是字符串
// input_class input 的 class 名称 ； 多个用 空格 隔开
// input_style input 的 style 值 ；
// input_padstr 可以 填入 id="111" data-aaa="8" ；多个值时，用 空格分隔
//返回select 的option html代码
function reset_radio_checkbox_item(input_type, item_json, input_name, checked_val, disabled_val, input_class, input_style, input_padstr){
    var radio_checkbox_json={"input_type": input_type, "input_name":input_name, "checked_val" : checked_val, "disabled_val":disabled_val, "item_json":item_json, "input_class":input_class, "input_style":input_style, "input_padstr":input_padstr};//{"item_json":{"1": "北京","2": "天津","3": "上海"}};
    var html_radio_checkbox = resolve_baidu_template('baidu_template_radio_checkbox_list',radio_checkbox_json,'');//解析
    //alert(html_radio_checkbox);
    return html_radio_checkbox;
}

// item_json  初始化下拉框json串[注意:item_json];{"item_json":{"1": "北京","2": "天津","3": "上海"}} 中的 item_json下标对象 【kv值】
// select_name 输入框的名称 值  字符串 aaa 或 bbb[]
// selected_val 当前选中的值  可以是 单个值 【数字】 也可以是数组 ['2'] --注意：数组项 是字符串
// select_class input 的 class 名称 ； 多个用 空格 隔开
// select_style input 的 style 值 ；
// select_padstr 可以 填入 disabled  multiple id="111" size="8" ；多个值时，用 空格分隔
//返回select 的option html代码
function reset_select_item( item_json, select_name, selected_val, select_class, select_style, select_padstr){
    var select_json={"select_name":select_name, "selected_val" : selected_val, "item_json":item_json, "select_class":select_class, "select_style":select_style, "select_padstr":select_padstr};//{"item_json":{"1": "北京","2": "天津","3": "上海"}};
    var html_select = resolve_baidu_template('baidu_template_select_option_list',select_json,'');//解析
    //alert(html_select);
    return html_select;
}

// -----------json对象--属性相关的操作---------------------
//是否有对象属性 ；有属性：true ;无属性:false  undefined/null:返回true -- 对 数组同样试用
// obj 要判断的对象
// attr 属性名称
function isHasAttr(obj, attr) {
    //判断是否有该键值
    if (obj && obj.hasOwnProperty(attr)) {
        //如果有返回true
        return true;
    }
    return false;
}

//是否含有对象属性对应的值 [具体值，不能是对象] true:有；false：无   undefined/null:返回true
// obj 要判断的对象
// attr 属性名称
// value 要判断的属性值  ''  或其它值    ；不能判断对象 如： {}
function isHasAttrVal(obj, attr, value) {
    //判断是否有该键值对应的值
    if (obj && obj.hasOwnProperty(attr) && obj[attr] == value) {
        //如果有返回true
        return true;
    }
    return false;
}

// 判断是否为空  true:为空, false:不为空  ； 说明： 不存在 或 '' 或 {} 或 [] 或 null 或 undefined 返回 true ;  数字0：不判断为空,返回false
function isEmpeyVal(val){
    console.log('------typeof--------', typeof val);
    //  JSON.stringify(val) == "{}"
    if (typeof val == "object") {// 对象
        for (let key in val) {
            return false;
        }
        // }else if(typeof val == "array") { // 没有此名称
        //     if(val.length > 0) return false;
    } else if (typeof val == "number") {
        return false;
    } else if (typeof val == "boolean") {
        return false;
    } else if (typeof val == "undefined") {
        return true;
    } else {
        if (val != '') return false;
    }
    return true;
}

// 判断是值是否是 不存在 或 '' 或 {} 或 [] 或 null 或 undefined true:为空, false:不为空  ；数字0：不判断为空,返回false
// obj 要判断的对象
// attr 属性名称
function isEmptyAttr(obj, attr) {
    //判断是否有该键值对应的值
    if (obj && obj.hasOwnProperty(attr)) {
        let val = obj[attr];
        return isEmpeyVal(val);
    }
    return true;
}

// 获得属性值
// obj 要判断的对象
// attr 属性名称
// emptyReDefautl 为空或{}时，是否用默认值  true:判断空 ; false:不用判断空
// defaultVal 不存在属性时，默认值
function getAttrVal(obj, attr, emptyReDefautl, defaultVal) {
    if (obj && obj.hasOwnProperty(attr)) {// 有属性
        let val = obj[attr];
        if (!emptyReDefautl) return val;
        if (isEmptyAttr(obj, attr)) return defaultVal;
        return val;
    }
    return defaultVal;
}

// 通过新对象{} 来 对源对象追加或覆盖属性
// obj 源对象  {} 是引用传参，对象值会同时追或覆盖--直接用，不用去取返回的对象
// appendObj 新对象  追加或覆盖属性
//     {
//         duration:0,
//         currentTime:0,
//         paused:0,
//         buffered:0,
//     }
// isCover 如果属性已存在，是否覆盖 true: 覆盖--[默认]， false: 不覆盖-原值不变
function objAppendProps(obj, appendObj, isCover) {
    if(isCover !== false){
        isCover = isCover || true;
    }
    if(typeof obj === "object"){
        for(var prop_key in appendObj) {
            // console.log('prop_key:' + prop_key, isHasAttr(obj, prop_key));
            if(isHasAttr(obj, prop_key) && !isCover){
                continue;
            }
            obj[prop_key] = appendObj[prop_key];
        }
    }
    return obj;
}

// 获得对象的多层属性 如 {a:{b:{c:1,d:2}}}
// 注意不适用于数组 [];
// keys 需要获得的对象的属性，多层用,号分隔 a,b,c 或 a,b,d
function getAttrByKeys(obj, keys){
    var reVal = obj;
    var key_array = keys.split(",");

    for (var i=0 ; i< key_array.length ; i++) {
        var temAttr = key_array[i];
        if(isEmpeyVal(temAttr)){
            reVal = null;
            break;
        }
        if(!isHasAttr(reVal, temAttr)){
            reVal = null;
            break;
        }
        reVal = getAttrVal(reVal, temAttr, null, null);
    }
    console.log('reVal=', reVal);
    return reVal;
}

// **************原生封装处理***************开始***************************************************************
/**
 ** 加法函数，用来得到精确的加法结果
 ** 说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
 ** 调用：accAdd(arg1,arg2)
 ** 返回值：arg1加上arg2的精确结果
 **/
function accAdd(arg1, arg2) {
    var r1, r2, m, c;
    try {
        r1 = arg1.toString().split(".")[1].length;
    } catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    } catch (e) {
        r2 = 0;
    }
    c = Math.abs(r1 - r2);
    m = Math.pow(10, Math.max(r1, r2));
    if (c > 0) {
        var cm = Math.pow(10, c);
        if (r1 > r2) {
            arg1 = Number(arg1.toString().replace(".", ""));
            arg2 = Number(arg2.toString().replace(".", "")) * cm;
        } else {
            arg1 = Number(arg1.toString().replace(".", "")) * cm;
            arg2 = Number(arg2.toString().replace(".", ""));
        }
    } else {
        arg1 = Number(arg1.toString().replace(".", ""));
        arg2 = Number(arg2.toString().replace(".", ""));
    }
    return (arg1 + arg2) / m;
}

//给Number类型增加一个add方法，调用起来更加方便。
// Number.prototype.add = function (arg) {
//     return accAdd(arg, this);
// };
/**
 ** 减法函数，用来得到精确的减法结果
 ** 说明：javascript的减法结果会有误差，在两个浮点数相减的时候会比较明显。这个函数返回较为精确的减法结果。
 ** 调用：accSub(arg1,arg2)
 ** 返回值：arg1加上arg2的精确结果
 **/
function accSub(arg1, arg2) {
    var r1, r2, m, n;
    try {
        r1 = arg1.toString().split(".")[1].length;
    } catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    } catch (e) {
        r2 = 0;
    }
    m = Math.pow(10, Math.max(r1, r2)); //last modify by deeka //动态控制精度长度
    n = r1 >= r2 ? r1 : r2;
    return ((arg1 * m - arg2 * m) / m).toFixed(n);
}

// 给Number类型增加一个mul方法，调用起来更加方便。
// Number.prototype.sub = function (arg) {
//     return accMul(arg, this);
// };
/**
 ** 乘法函数，用来得到精确的乘法结果
 ** 说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
 ** 调用：accMul(arg1,arg2)
 ** 返回值：arg1乘以 arg2的精确结果
 **/
function accMul(arg1, arg2) {
    var m = 0,
        s1 = arg1.toString(),
        s2 = arg2.toString();
    try {
        m += s1.split(".")[1].length;
    } catch (e) {}
    try {
        m += s2.split(".")[1].length;
    } catch (e) {}
    return (
        (Number(s1.replace(".", "")) * Number(s2.replace(".", ""))) /
        Math.pow(10, m)
    );
}

// 给Number类型增加一个mul方法，调用起来更加方便。
// Number.prototype.mul = function (arg) {
//     return accMul(arg, this);
// };
/**
 ** 除法函数，用来得到精确的除法结果
 ** 说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
 ** 调用：accDiv(arg1,arg2)
 ** 返回值：arg1除以arg2的精确结果
 **/
function accDiv(arg1, arg2) {
    var t1 = 0,
        t2 = 0,
        r1,
        r2;
    try {
        t1 = arg1.toString().split(".")[1].length;
    } catch (e) {}
    try {
        t2 = arg2.toString().split(".")[1].length;
    } catch (e) {}
    with (Math) {
        r1 = Number(arg1.toString().replace(".", ""));
        r2 = Number(arg2.toString().replace(".", ""));
        return (r1 / r2) * pow(10, t2 - t1);
    }
}

//给Number类型增加一个div方法，调用起来更加方便。
// Number.prototype.div = function (arg) {
//     return accDiv(this, arg);
// };
// **************原生封装处理***************结束***************************************************************

//*********math 方法************************开始**********价格相关的计算主要用math*********************************************
//  <script src="{{asset('static/js/math/8.1.0/math.min.js')}}"></script> {{--1，引入第三方的js库, math.js，--}}
$(function(){
    //统一配置math.js
    mathSetConfig();
});


// 注：大多数math.js函数，都需要valueof()或者done()函数来真正地获取操作的值
// 配置
function mathSetConfig() {
    //统一配置math.js
    if(typeof math != "undefined"){
        math.config({
            number: 'BigNumber',
            // 'number' (default),
            precision: 20
        });
        consoleLogs(['设置math对象']);
    }else{
        consoleLogs(['没有math对象']);
    }
}
// 加
function mathAdd(num1, num2){
    return math.format(math.chain(math.bignumber(num1)).add(math.bignumber(num2)).done());
}
// 减
function mathSubtract(num1, num2){
    return math.format(math.chain(math.bignumber(num1)).subtract(math.bignumber(num2)).done());
}
// 乘
function mathMultiply(num1, num2){
    return math.format(math.chain(math.bignumber(num1)).multiply(math.bignumber(num2)).done());
}
// 除
function mathDivide(num1, num2){

    return math.format(math.chain(math.bignumber(num1)).divide(math.bignumber(num2)).done());
}

// 比较大小
// num1 > num2 : 1; num1 == num2 : 0; num1 < num2 : -1;
// 可这样判断 if(mathCompare(0.2,1.2) == -1){ console.log('小于');}else{console.log('不小于');}
function mathCompare(num1, num2) {
    return math.format(math.compare(math.bignumber(num1), math.bignumber(num2)));
}
// 一个数【num1】的 【num2】次方 ；如：10 的2 次方 100
function mathPow(num1, num2) {
    return math.format(math.pow(math.bignumber(num1), math.bignumber(num2)));
}

// toFixed 的修复 , 四舍五入
// num : 需要格式化的数
// s: 保留小数的位数
// needPad ： 位数不够时，小数后面是否填充0； true:填充；false:不填充【默认】
function toMathFixed(num, s, needPad) {
    needPad = needPad || false;
    // var times = Math.pow(10, s);
    var times = mathPow(10, s);
    var des = mathAdd(mathMultiply(num, times), 0.5);
    // des = parseInt(des, 10) / times;
    des = mathDivide(number_format(des, 0), times);
    if(needPad){// 填充
        return number_format(des, s);
    }else{// 不填充
        return des + '';
    }
}

// 向上或向下取整
// num : 需要格式化的数
// s: 保留小数的位数
// operateType 1向下取整[默认] 、2向上取整； 四舍五入请用 上面的 toFixed 或 toMathFixed
// needPad ： 位数不够时，小数后面是否填充0； true:填充；false:不填充【默认】
function numMathUpDown(num, s, operateType, needPad) {
    operateType = operateType || 1;
    needPad = needPad || false;
    // var times = Math.pow(10, s);
    var times = mathPow(10, s);
    var des = mathMultiply(num, times);

    if(operateType == 2){// 向上取整
        var _str = des.toString();
        if(_str.indexOf('.') != -1){
            des = mathAdd(des, 1);
        }
    }
    // des = parseInt(des, 10) / times;
    des = mathDivide(number_format(des, 0), times);
    if(needPad){// 填充
        return number_format(des, s);
    }else{// 不填充
        return des + '';
    }
}

/* 格式化金额 */
// price 金额
// s: 保留小数的位数
// needPad ： 位数不够时，小数后面是否填充0； true:填充；false:不填充【默认】
// operateType 1向下取整、2向上取整； 3四舍五入[默认]
function numberMathFormat(num, s, needPad, operateType){
    operateType = operateType || 3;
    needPad = needPad || false;
    if(operateType == 3){
        return toMathFixed(num, s, needPad);
    }else{
        return numMathUpDown(num, s, operateType, needPad);
    }
}
//*********math 方法************************结束*******************************************************
// toFixed 的修复
// 在Firefox / Chrome中，toFixed并不会对于最后一位是5的如愿以偿的进行四舍五入。
// 1.35.toFixed(1) // 1.4 正确
// 1.335.toFixed(2) // 1.33  错误
// 1.3335.toFixed(3) // 1.333 错误
// 1.33335.toFixed(4) // 1.3334 正确
// 1.333335.toFixed(5)  // 1.33333 错误
// 1.3333335.toFixed(6) // 1.333333 错误
// Firefox 和 Chrome的实现没有问题，根本原因还是计算机里浮点数精度丢失问题。
//
// 修复方式：
// num : 需要格式化的数
// s: 保留小数的位数
// needPad ： 位数不够时，小数后面是否填充0； true:填充；false:不填充【默认】
function toFixed(num, s, needPad) {
    needPad = needPad || false;
    var times = Math.pow(10, s);
    var des = num * times + 0.5;
    des = parseInt(des, 10) / times;
    if(needPad){// 填充
        return number_format(des, s);
    }else{// 不填充
        return des + '';
    }
}

// 向上或向下取整
// num : 需要格式化的数
// s: 保留小数的位数
// operateType 1向下取整[默认] 、2向上取整； 四舍五入请用 上面的 toFixed 或 toMathFixed
// needPad ： 位数不够时，小数后面是否填充0； true:填充；false:不填充【默认】
function numUpDown(num, s, operateType, needPad) {
    operateType = operateType || 1;
    needPad = needPad || false;
    var times = Math.pow(10, s);
    var des = num * times;

    if(operateType == 2){// 向上取整
        var _str = des.toString();
        if(_str.indexOf('.') != -1){
            des += 1;
        }
    }

    des = parseInt(des, 10) / times;
    if(needPad){// 填充
        return number_format(des, s);
    }else{// 不填充
        return des + '';
    }
}

/* 格式化金额 */
// price 金额
// s: 保留小数的位数
// needPad ： 位数不够时，小数后面是否填充0； true:填充；false:不填充【默认】
// operateType 1向下取整、2向上取整； 3四舍五入[默认]
function numberFormat(num, s, needPad, operateType){
    operateType = operateType || 3;
    needPad = needPad || false;
    if(operateType == 3){
        return toFixed(num, s, needPad);
    }else{
        return numUpDown(num, s, operateType, needPad);
    }
}

/* 格式化金额-向下取整--价格四舍五入不能用此方法了，可用 numberMathFormat */
function price_format(price){
    if(typeof(PRICE_FORMAT) == 'undefined'){
        PRICE_FORMAT = '&yen;%s';
    }
    price = number_format(price, 2);

    return PRICE_FORMAT.replace('%s', price);
}

// 向下取整及不足小数后面补0
function number_format(num, ext){
    if(ext < 0){
        return num;
    }
    num = Number(num);
    if(isNaN(num)){
        num = 0;
    }
    var _str = num.toString();
    var _arr = _str.split('.');
    var _int = _arr[0];
    var _flt = _arr[1];
    if(_str.indexOf('.') == -1){
        /* 找不到小数点，则添加 */
        if(ext == 0){
            return _str;
        }
        var _tmp = '';
        for(var i = 0; i < ext; i++){
            _tmp += '0';
        }
        _str = _str + '.' + _tmp;
    }else{
        if(_flt.length == ext){
            return _str;
        }
        /* 找得到小数点，则截取 */
        if(_flt.length > ext){
            _str = _str.substr(0, _str.length - (_flt.length - ext));
            if(ext == 0){
                _str = _int;
            }
        }else{
            for(var i = 0; i < ext - _flt.length; i++){
                _str += '0';
            }
        }
    }

    return _str;
}

// 对数值参数进行初始化；如果为空或非数字，则改为0
function initNumberVal(numVal){
    if(isEmpeyVal(numVal)){
        numVal = 0;
    }
    if(isNaN(numVal)){
        numVal = 0;
    }
    return numVal;
}

/* 火狐下取本地全路径 */
function getFullPath(obj)
{
    if(obj)
    {
        //ie
        if (window.navigator.userAgent.indexOf("MSIE")>=1)
        {
            obj.select();
            if(window.navigator.userAgent.indexOf("MSIE") == 25){
                obj.blur();
            }
            return document.selection.createRange().text;
        }
        //firefox
        else if(window.navigator.userAgent.indexOf("Firefox")>=1)
        {
            if(obj.files)
            {
                //return obj.files.item(0).getAsDataURL();
                return window.URL.createObjectURL(obj.files.item(0));
            }
            return obj.value;
        }
        return obj.value;
    }
}
/* 转化JS跳转中的 ＆ */
function transform_char(str)
{
    if(str.indexOf('&'))
    {
        str = str.replace(/&/g, "%26");
    }
    return str;
}

function trim(str) {
    return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
}

// 那么让我解释为什么我更喜欢这个解决方案... 如果您使用相同名称的多重输入，则所有值都将存储在数组中，但如果不是，则该值将直接存储为JSON中索引的值...
// 这与Danilo Colasso的答案不同返回的JSON仅基于数组值... 因此，如果您有一个带有textarea命名内容和多个作者的表单，此函数将返回给您：
// {
//     content : 'This is The Content',
//         authors :
//     [
//         0: 'me',
//     1: 'you',
//     2: 'him',
// ]
// }
// //The way to use it :
// $('#myForm').submit(function(){
//     var datas = formToJSON(this);
//     return false;
// });
// 表单数据转为 ｛'字段名'：'字段值', '字段名'：['字段值1', '字段值2'], ... ｝
// 参数 f 表单对象 this
// 参数 fd 对象  $(f).serializeArray(); 的值  [{name: "LastName", value: "Gates"},{name: "open_status", value: "1"},{name: "role_nums[]", value: "1"},{name: "role_nums[]", value: "2"}]
// frmType 格式： 1 [默认]: 有多个值时，值转为数组；2：有多个值时，值转为分隔符分隔的值; 4: 2
// splitStr 分隔符，默认,逗号
// 返回 对象{'表单字段名'：'表单字段的值',...};注意：值可以是 '字符'或 数组 ['值1','值2'] 或 '字符：值1 分隔符 值2'
function formToJSON(f, frmType, splitStr) {
    var fd = $(f).serializeArray();// 格式为： [{name: "LastName", value: "Gates"},{name: "open_status", value: "1"},{name: "role_nums[]", value: "1"},{name: "role_nums[]", value: "2"}]
    return formObjToObj(fd, frmType, splitStr)
}

// 参数 fd 对象  $(f).serializeArray(); 的值  [{name: "LastName", value: "Gates"},{name: "open_status", value: "1"},{name: "role_nums[]", value: "1"},{name: "role_nums[]", value: "2"}]
// frmType 格式： 1 [默认]: 有多个值时，值转为数组；2：有多个值时，值转为分隔符分隔的值
// splitStr 分隔符，默认,逗号
// 返回 对象{'表单字段名'：'表单字段的值',...};注意：值可以是 '字符'或 数组 ['值1','值2'] 或 '字符：值1 分隔符 值2'
function formObjToObj(fd, frmType, splitStr) {
    frmType = frmType || 1;
    splitStr = splitStr || ',';
    var d = {};
    $(fd).each(function() {
        if (d[this.name] !== undefined){
            objAppExistNameVal(d, this.name, this.value, frmType, splitStr);
            // var val = this.value;
            // var oldVal = d[this.name];
            // switch(frmType)
            // {
            //     case 2://有多个值时，值转为分隔符分隔的值
            //         d[this.name] = oldVal + splitStr + val;
            //         break;
            //     case 1:// 1 [默认]: 有多个值时，值转为数组
            //     default:
            //         if (!Array.isArray(d[this.name])) {
            //             d[this.name] = [d[this.name]];
            //         }
            //         d[this.name].push(this.value);
            //         break;
            // }

        }else{
            d[this.name] = this.value;
        }
    });
    return d;
}
// 对加对已经存在的下标的新值进行追加处理
// obj 当前对象
// fieldName 当前对象下标名称， 注意：下标在对象中已存在
// val 新的值
// frmType 格式： 1 [默认]: 有多个值时，值转为数组；2：有多个值时，值转为分隔符分隔的值
// splitStr 分隔符，默认,逗号
// 返回 对象新加一个值 ； 格式： 数组 ['值1','值2'] 或 '字符：值1 分隔符 值2'
function objAppExistNameVal(obj, fieldName, val, frmType, splitStr) {
    frmType = frmType || 1;
    splitStr = splitStr || ',';
    var oldVal = obj[fieldName] || '';
    switch(frmType)
    {
        case 2://有多个值时，值转为分隔符分隔的值
            obj[fieldName] = oldVal + splitStr + val;
            break;
        case 1:// 1 [默认]: 有多个值时，值转为数组
        default:
            if (!Array.isArray(obj[fieldName])) {
                obj[fieldName] = [obj[fieldName]];
            }
            obj[fieldName].push(val);
            break;
    }
}


// 表单 $("#addForm").serialize(); 值如下
// hidden_option=0&id=123&real_name=会员企业&sex=1&email=305463219%40qq.com&mobile=15855686962&qq_number=&id_number=&position_name=&city_id=1
// &addr=&role_nums[]=1&role_nums[]=2&role_nums[]=4&sign_range=
// str 原字符串 如上面的 ..=aaa&..=
// frmType 格式： 1 [默认]: 有多个值时，值转为数组；2：有多个值时，值转为分隔符分隔的值
// bigStr 大分隔符，默认 &
// smallStr 小隔符，默认 =
// splitStr 分隔符，默认,逗号
// 返回 对象{'表单字段名'：'表单字段的值',...};注意：值可以是 '字符'或 数组 ['值1','值2'] 或 '字符：值1 分隔符 值2'
function paramsToObj(str, frmType, bigStr, smallStr, splitStr) {
    bigStr = bigStr || '&';
    smallStr = smallStr || '=';
    frmType = frmType || 1;
    splitStr = splitStr || ',';

    var obj = [];
    var bigArr = str.split(bigStr);
    for (var i = 0 ; i < bigArr.length ; i++) {
        var temStr = bigArr[i];
        var smallArr = temStr.split(smallStr);
        var fieldName = smallArr[0];
        var fieldVal = smallArr[1];
        var temObj = {"name": fieldName, "value": fieldVal};
        obj.push(temObj);
    }
    console.log(obj);
    return formObjToObj(obj, frmType, splitStr)
}

//获得表单各name的值[只能是input]
//frm_ids 需要读取的表单的id，多个用,号分隔
//返回值不为空的表单的json对象
//function get_frm_input_values(frm_ids){
//    var data = {};
//    //获得表单的值
//    var frm_array = frm_ids.split(",");
//    var used_frm = [];
//    for (var i=0 ; i< frm_array.length ; i++)
//    {
//        var frm_id = frm_array[i];//表单id
//        if($('#'+frm_id).length<=0){
//            continue;
//        }
//        if(used_frm.indexOf(frm_id)<0){//不存在
//            used_frm.push(frm_id);
//            var frm_obj = $("#"+frm_id)[0];
//            for(var j=0;j<frm_obj.length;j++)
//            {
//                var jq_obj= $(frm_obj[j]);
//                frmvar_name=jq_obj.attr('name');//frm_obj[i].name;
//                if(frmvar_name===undefined || frmvar_name===''){
//                      continue;
//                }
//                frmvar_value=jq_obj.val();
//                if(frmvar_value == '') continue;
//                //一定不要用转义
//                //data[frmvar_name] = encodeURIComponent(frmvar_value);
//                data[frmvar_name] = frmvar_value;
//            }
//        }
//    }
//    return data;
//}
//获得表单各name的值
//frm_ids 需要读取的表单的id，多个用,号分隔
//返回值不为空的表单的json对象
function get_frm_values(frm_ids){
    var data = {};
    //获得表单的值
    var frm_array = frm_ids.split(",");
    var used_frm = [];
    for (var i=0 ; i< frm_array.length ; i++)
    {
        var frm_id = frm_array[i];//表单id
        if($('#'+frm_id).length<=0){
            continue;
        }
        if(used_frm.indexOf(frm_id)<0){//不存在
            used_frm.push(frm_id);
            var frm_obj = $("#"+frm_id)[0];
//            var frm_data_ser = $("#"+frm_id).serialize();
//
//            var params_array = frm_data_ser.split("&");
//            for (var j=0 ; j< params_array.length ; j++)
//            {
//                var param_vals = params_array[j];
//                if(param_vals===undefined || param_vals===''){
//                    continue;
//                }
//                var param_arr = param_vals.split("=");
//                if(param_arr.length<=1){
//                    continue;
//                }
//                var frmvar_name= param_arr[0];
//                if(frmvar_name===undefined || frmvar_name===''){
//                      continue;
//                }
//                var frmvar_value = param_arr[1];
//                if(frmvar_value == '') continue;
//                //一定不要用转义
//                //data[frmvar_name] = encodeURIComponent(frmvar_value);
//                var old_value = data[frmvar_name];
//                if(old_value!==undefined && old_value!==''){
//                      frmvar_value = old_value + "," + frmvar_value;
//                }
//                data[frmvar_name] = frmvar_value;
//            }
//
            for(var j=0;j<frm_obj.length;j++)
            {
                var jq_obj= $(frm_obj[j]);
                var frmvar_name=jq_obj.attr('name');//frm_obj[i].name;
                if(frmvar_name===undefined || frmvar_name===''){
                      continue;
                }
                var frmvar_value=jq_obj.val();
                if(frmvar_value == '') continue;
                var input_type = jq_obj.prop('type');
                if(input_type == "radio" || input_type == "checkbox"){
                    if(jq_obj.prop('checked') === false){
                        continue;
                    }
                }
                var old_value = data[frmvar_name];
                if(old_value!==undefined && old_value!==''){
                      frmvar_value = old_value + "," + frmvar_value;
                }
                //一定不要用转义
                //data[frmvar_name] = encodeURIComponent(frmvar_value);
                data[frmvar_name] = frmvar_value;
            }
        }
    }
    return data;
}
//返回{'input_vlist':[{'name':'user_id','value':'10'}]}
function get_frm_kv(frm_ids){
    var data = get_frm_values(frm_ids);//{};
    var data_json = {'input_vlist':[]};//{'input_vlist':[{'name':'user_id','value':'10'}]};
    for(var p in data){
        var tem_json = {'name':p,'value':data[p]};
        data_json.input_vlist.push(tem_json);
    }
    return data_json;
}
//返回参数字串:name=user_id&value=10
function get_frm_param(frm_ids){
    var data = get_frm_values(frm_ids);//{};
    return get_url_param(data);
    // var newurl="";
    // var tem_name,tem_value;
    // for(var p in data){
    //     tem_name = p;
    //     tem_value = data[p];
    // if(tem_value == '') continue;
    // if(newurl=="")
    // {
		// newurl=tem_name+"="+encodeURIComponent(tem_value);
    // }else{
		// newurl=newurl+"&"+tem_name+"="+encodeURIComponent(tem_value);
    // }
    // }
    // return newurl;
}
// 根据dat拼接参数,自动过滤''值参数
// 数据对象 {'键'=>'值'}
//返回参数字串:name=user_id&value=10
function get_url_param(data){
    var newurl="";
    var tem_name,tem_value;
    for(var p in data){
        tem_name = p;
        tem_value = data[p];
        if(tem_value == '') continue;
        if(newurl=="")
        {
            newurl=tem_name+"="+encodeURIComponent(tem_value);
        }else{
            newurl=newurl+"&"+tem_name+"="+encodeURIComponent(tem_value);
        }
    }
    return newurl;
}

//reFromSearchAction将搜索框转换为地址形式以便搜索引警用
//filename 搜索结果文件名称
//obj 搜索框form
function reFromSearchAction(filename,obj)
{
  var newurl,frmvar_name,frmvar_value;
  newurl="";

  for(var i=0;i<obj.length;i++)
  {
	var jq_obj= $(obj[i]);
	frmvar_name=jq_obj.attr('name');//obj[i].name;
	if(frmvar_name===undefined || frmvar_name===''){
		continue;
	}
	frmvar_value=jq_obj.val();//eval(obj.name+"."+obj[i].name+".value");
	if(frmvar_value == '') continue;
	if(newurl=="")
	{
		newurl=frmvar_name+"="+encodeURIComponent(frmvar_value);
	}else{
		newurl=newurl+"&"+frmvar_name+"="+encodeURIComponent(frmvar_value);
	}

  }
  obj.action=filename+"?"+newurl;
 return true;
}
/*
去掉非数字函数
param string str 需要操作的字符
return string 去掉字符后的内容
姓名：邹燕
时间：2014.8.13
*/
function del_char(str){
	return str.replace(/\D/g,'');
}

//验证只能输入数字
function isnum(obj){
	var tem_obj = $(obj);
  //obj.value=obj.value.replace(/[^\d]/g,'')
  	var tem_value = tem_obj.val();
	tem_obj.val(tem_value.replace(/[^\d]/g,''));
}
//验证只能输入[正]数字及小数点[最多2位小数]
function numxs(obj){
	var tem_obj =$(obj);
   var value = tem_obj.val();//obj.value;
   var reg2 = /^\d+(\.\d{0,})?$/;// /^\d+(\.\d{0,})?$/
   if(!reg2.test(value)){
	  //obj.value= "";
	  //obj.value=obj.value.replace(/[^\d\.]/g,'');
	  value = value.replace(/[^\d\.]/g,'');
	  //obj.value=obj.value.replace(/[\.]{2,}/g,'.');//多个.，只保留一个
	  value = value.replace(/[\.]{2,}/g,'.');//多个.，只保留一个
	  //obj.value=obj.value.replace(/^[\.]{1,}/g,'');//开头是.，去掉
	  value = value.replace(/^[\.]{1,}/g,'');//开头是.，去掉
	  tem_obj.val(value);
   }

}
//验证只能输入数字[正负]及小数点[最多2位小数]
function decimal_numxs(obj){
	var tem_obj = $(obj);
   var value = tem_obj.val();//obj.value;
   var reg2 = /^[\-]{0,1}\d+(\.\d{0,})?$/;// /^\d+(\.\d{0,})?$/
   if(!reg2.test(value)){
	  //obj.value= "";
	  //obj.value=obj.value.replace(/[^\d\-\.]/g,'');
	  value=value.replace(/[^\d\-\.]/g,'');
	  //obj.value=obj.value.replace(/[\.]{2,}/g,'.');//多个.，只保留一个
	  value=value.replace(/[\.]{2,}/g,'.');//多个.，只保留一个
	  //obj.value=obj.value.replace(/^[\.]{1,}/g,'');//开头是.，去掉
	  value=value.replace(/^[\.]{1,}/g,'');//开头是.，去掉
	  //obj.value=obj.value.replace(/[\-]{2,}/g,'-');//多个-，只保留一个
	  value=value.replace(/[\-]{2,}/g,'-');//多个-，只保留一个
	  tem_obj.val(value);
   }

}

//综合判断
//err_type 错误误返回类型
//  1返回错误字符串,空:没有错误;
//  2返回值 true：正确-通过;false:失败-有误;
//  4返回2的同时，弹出错误提示窗
//tishi_name 提示名称[关键字名]
//value 需要判断的字符串
//is_must 是否必填 true:必填;false:非必填
//reg_msg [多个用,号分隔-后面的单参数的可以无限个,但多参数的只能有一个;前面的优先判断]正则或指定判断关键字[不在下面的，请直接写正则表达式来判断,空：则不进行判断]

        //custom 正则验证 min_length 为正则表达式[regexp]
        //length 判断字符长度 min_length 最小长度[为空:不参与判断];max_length 最大长度[为空:不参与判断]
        //range 判断数字范围 min_length 最小值>=[为空:不参与判断];max_length 最大值<=[为空:不参与判断]
        //compare 比较 min_length 比较符[必填];max_length 被比较值[必填]
        //data_size 判断日期大小 value>max_length  min_length 日期2[必填];max_length 日期操作类型[位操作] 1 > ;2< ; 4 =[必填]
        //     日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59
        //email: 邮箱 judge_email(value)
        //phone: 电话号码 judge_phone(value)
        //mobile: 手机 judge_mobile(value)
        //url:url judge_url(value)
        //currency: 货币 judge_currency(value)
        //number: 任何数字[纯数字]验证 judge_number(value)
        //zip:邮编 judge_zip(value)
        //qq:qq号码 judge_qq(value)
        //integer: [-+]正负整数 judge_integer(value)
        //integerpositive: [+]正整 judge_integerpositive(value)
        //double: [-+] 数字.数字 正负双精度数 judge_double(value)
        //doublepositive [+]数字.数字 正双精度数 judge_doublepositive(value)
        //english 大小写字母 judge_english(value)
        //englishsentence 大小写字母空格 judge_englishsentence(value)
        //englishnumber 大小写字母数字 judge_englishnumber(value)
        //chinese 中文 judge_chinese(value)
        //username 至少3位 用户名 judge_username(value)
        //nochinese 非中文 judge_nochinese(value)
        //datatime 日期时间 judge_datatime(value)
        //int [\-]负整数或正整数,正的没有+号 judge_int(value)
        //positive_int >0正整数[全是数字且>0] judge_positive_int(value)
        //digit:0+正整数 judge_judge_digit(value)
        //date [见意用这个]判断日期格式是否正确 judge_date(dateTime) 日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59
        //time 判断时间格式是否正确 true正确 false 有误  时间格式 23:59:59
        //bitint 判断值是否是位数字[1、2、4..] min_length 最大执行的次数 如 int: 31  bigint: 63[默认]  judge_validate(1,'参数类型',8,true,'bitint',63,"");
//min_length 最小长度;为空则不判断
//max_length 最大长度;为空则不判断
//返回值 true：正确-通过;false:失败-有误
function judge_validate(err_type,tishi_name,value,is_must,reg_msg,min_length,max_length){
    var tem_value = trim(value);
    var err_str = "";
    if(is_must == true){
        if(judge_empty(tem_value)){
            err_str = tishi_name + '不能为空!';
            if(err_type == 4){err_alert(err_str);}
            if(err_type == 1){return err_str;}
            return false;
        }
    }
    //为空，则判断是否是空格
    if(judge_empty(tem_value)){
        if(value.length>0){//判断是否全是空格
            err_str = tishi_name + '不能全为空格!';
            if(err_type == 4){err_alert(err_str);}
            if(err_type == 1){return err_str;}
            return false;
        }else{
            if(err_type == 1){return err_str;}
            return true;
        }
    }
    //空,则不进行后面的正则判断
    if(judge_empty(reg_msg)){
        if(err_type == 1){return err_str;}
        return true;
    }
    var back_err = "";
    var tem_lower_msg = reg_msg.toLowerCase();
    var msg_arr= new Array(); //定义一数组
    msg_arr = tem_lower_msg.split(","); //字符分割
    for (i=0;i<msg_arr.length ;i++ )
    {
        back_err = "";
        var tem_reg = msg_arr[i];
        if(judge_empty(tem_reg)){
            continue;
        }
        switch(tem_reg){
            case "custom":// 正则验证 min_length 为正则表达式[regexp]
                if(!judge_reg(tem_value,min_length)){
                   back_err = "格式有误!";
                }
                break;
            case "length":// 判断字符长度 min_length 最小长度[为空:不参与判断];max_length 最大长度[为空:不参与判断]
                if(!judge_length(tem_value,min_length,max_length)){
                    back_err = '长度为'+min_length+'~'+max_length+'个字符!';
                }
                break;
            case "range":// 判断数字范围 min_length 最小值[为空:不参与判断];max_length 最大值[为空:不参与判断]
                if(!judge_range(tem_value,min_length,max_length)){
                    back_err = '范围为'+min_length+'~'+max_length+'!';
                }
                break;
            case "compare":// 比较 min_length 比较符[必填];max_length 被比较值[必填]
                if(!judge_compare(tem_value,min_length,max_length)){
                    back_err = '必须为[' + ' ' + min_length+']!';
                }
                break;
            case "data_size":// data_size 判断日期大小 min_length>max_length  min_length 日期2[必填];max_length 日期操作类型[位操作] 1 > ;2< ; 4 =[必填]
                if(!judge_data_size(tem_value,min_length,max_length)){
                    var operate_str = "";
                    if( (max_length & 1) == 1 ){//>
                        operate_str +=">";
                    }
                    if( (max_length & 2) == 2 ){//<
                        operate_str +="<";
                    }
                    if( (max_length & 4) == 4 ){//=
                        operate_str +="=";
                    }
                    back_err = '必须[' + operate_str + ' ' + min_length+']!';
                }
                break;
            case "email"://邮箱
                if(!judge_email(tem_value)){
                   back_err = "格式不是有效的邮箱格式!";
                }
                break;
            case "phone":// 电话号码 judge_phone(value)
                if(!judge_phone(tem_value)){
                   back_err = "格式不是有效的电话号码格式!";
                }
                break;
            case "mobile":// 手机 judge_mobile(value)
                if(!judge_mobile(tem_value)){
                   back_err = "格式不是有效的手机格式!";
                }
                break;
            case "url"://url judge_url(value)
                if(!judge_url(tem_value)){
                   back_err = "格式不是有效的网址格式!";
                }
                break;
            case "currency":// 货币 judge_currency(value)
                if(!judge_currency(tem_value)){
                   back_err = "格式不是有效的货币格式!";
                }
                break;
            case "number":// 任何数字验证 judge_number(value)
                if(!judge_number(tem_value)){
                   back_err = "只能是数字!";
                }
                break;
            case "zip"://邮编 judge_zip(value)
                if(!judge_zip(tem_value)){
                   back_err = "格式不是有效的邮编格式!";
                }
                break;
            case "qq"://qq号码 judge_qq(value)
                if(!judge_qq(tem_value)){
                   back_err = "不是有效的qq号码!";
                }
                break;
            case "integer":// [-+]正负整数 judge_integer(value)
                if(!judge_integer(tem_value)){
                   back_err = "不是[-+]正负整数!";
                }
                break;
            case "integerpositive":// [+]正整 judge_integerpositive(value)
                if(!judge_integerpositive(tem_value)){
                   back_err = "不是[+]正整数!";
                }
                break;
            case "double":// [-+] 数字.数字 正负双精度数 judge_double(value)
                if(!judge_double(tem_value)){
                   back_err = "不是[-+]正负双精度数!";
                }
                break;
            case "doublepositive":// [+]数字.数字 正双精度数 judge_doublepositive(value)
                if(!judge_doublepositive(tem_value)){
                   back_err = "不是[+]数字.数字 正双精度数!";
                }
                break;
            case "english":// 大小写字母 judge_english(value)
                if(!judge_english(tem_value)){
                   back_err = "只能是大小写字母!";
                }
                break;
            case "englishsentence":// 大小写字母空格 judge_englishsentence(value)
                if(!judge_englishsentence(tem_value)){
                   back_err = "只能是大小写字母空格!";
                }
                break;
            case "englishnumber":// 大小写字母数字 judge_englishnumber(value)
                if(!judge_englishnumber(tem_value)){
                   back_err = "只能是大小写字母数字!";
                }
                break;
            case "chinese"://  judge_chinese(value)
                if(!judge_chinese(tem_value)){
                   back_err = "不是中文!";
                }
                break;
            case "username":// 至少3位 用户名 judge_username(value)
                if(!judge_username(tem_value)){
                   back_err = "至少3位!";
                }
                break;
            case "nochinese":// 非中文 judge_nochinese(value)
                if(!judge_nochinese(tem_value)){
                   back_err = "不是非中文!";
                }
                break;
            case "datatime":// 日期时间 judge_datatime(value)
                if(!judge_datatime(tem_value)){
                   back_err = "格式不是有效的日期时间格式!";
                }
                break;
            case "int"://int [\-]负整数或正整数,正的没有+号 judge_int(value)
                if(!judge_int(tem_value)){
                   back_err = "格式不是有效的[\-]负整数或正整数,正的没有+号格式!";
                }
                break;
            case "positive_int":// >0正整数[全是数字且>0] judge_positive_int(value)
                if(!judge_positive_int(tem_value)){
                   // back_err = "格式不是有效的>0正整数[全是数字且>0]格式!";
                   back_err = "必须是正整数[>0]!";
                }
                break;
            case "digit"://:0+正整数 judge_judge_digit(value)
                if(!judge_judge_digit(tem_value)){
                   back_err = "不是0或正整数!";
                }
                break;
            case "date"://date 判断日期格式是否正确 judge_date(dateTime) 日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59
                if(!judge_date(tem_value)){
                   back_err = "格式不是有效的日期格式!";
                }
                break;
            case "time"://time 判断时间格式是否正确 true正确 false 有误  时间格式 23:59:59
                if(!judge_time(tem_value)){
                    back_err = "格式不是有效的时间格式!";
                }
                break;
            case "bitint":// bitint 判断值是否是位数字[1、2、4..] min_length 最大执行的次数 如 int: 31  bigint: 63[默认]
                min_length = min_length || 63;
                if(!isBitNum(tem_value,min_length)){
                    back_err = '必须为[' + '1、2、4、8、16...等位数值；且在' + min_length+'个占位以内]!';
                }
                break;
            default://其它正则表达式
                if(!judge_reg(tem_value,reg_msg)){
                   back_err = "格式有误!";
                }
                break;
        }
        if(back_err != ''){
            err_str = tishi_name + back_err;
            if(err_type == 4){
                err_alert(err_str);
            }
            if(err_type == 1){return err_str;}
            return false;
        }
    }
    if(err_type == 1){return err_str;}
    return true;
}
//判断是否为空 true:空;false:非空
function judge_empty(value){
    if(value === undefined){
        return true;
    }
   var tem_value = trim(value);
   return judge_length(tem_value,0,0);
}
//判断正则表达式
//value需要判断的值
//reg正则表达式 可以是正则表达式对象 或 字符串
function judge_reg(value,reg2){
    if(typeof reg2 == 'string' ){// 是文字,则转为对象
        reg2 = new RegExp(reg2);
    }
   if(reg2.test(value)){
       return true;
   }else{
       return false;
   }
}
//判断email
function judge_email(value){
   var reg2 = /^([.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)$/;
   return judge_reg(value,reg2);
}
//判断phone 电话号码
function judge_phone(value){
   var reg2 = /^(([0-9]{2,3})|([0-9]{3}-))?((0[0-9]{2,3})|0[0-9]{2,3}-)?[1-9][0-9]{6,7}(-[0-9]{1,4})?$/;
   return judge_reg(value,reg2);
}
//判断mobile 手机
function judge_mobile(value){
   var reg2 = /^1[0-9]{10}$/;
   return judge_reg(value,reg2);
}
//判断url
function judge_url(value){
   var reg2 = /^http:(\/){2}[A-Za-z0-9]+.[A-Za-z0-9]+[\/=?%-&_~`@\[\]\':+!]*([^<>\"\"])*$/;
   return judge_reg(value,reg2);
}
//判断currency 货币
function judge_currency(value){
   var reg2 = /^[0-9]+(\.[0-9]+)?$/;
   return judge_reg(value,reg2);
}
//判断number 数字验证
function judge_number(value){
   var reg2 = /^[0-9]+$/;
   return judge_reg(value,reg2);
}
//判断zip 邮编
function judge_zip(value){
   var reg2 = /^[0-9][0-9]{5}$/;
   return judge_reg(value,reg2);
}
//判断qq
function judge_qq(value){
   var reg2 = /^[1-9][0-9]{4,8}$/;
   return judge_reg(value,reg2);
}
//判断integer [-+]正负整数
function judge_integer(value){
   var reg2 = /^[-+]?[0-9]+$/;
   return judge_reg(value,reg2);
}
//判断integerpositive [+]正整
function judge_integerpositive(value){
   var reg2 = /^[+]?[0-9]+$/;
   return judge_reg(value,reg2);
}
//判断double [-+] 数字.数字 正负双精度数
function judge_double(value){
   var reg2 = /^[-+]?[0-9]+(\.[0-9]+)?$/;
   return judge_reg(value,reg2);
}
//判断doublepositive [+]数字.数字 正双精度数
function judge_doublepositive(value){
   var reg2 = /^[+]?[0-9]+(\.[0-9]+)?$/;
   return judge_reg(value,reg2);
}
//判断english 大小写字母
function judge_english(value){
   var reg2 = /^[A-Za-z]+$/;
   return judge_reg(value,reg2);
}
//判断englishsentence 大小写字母空格
function judge_englishsentence(value){
   var reg2 = /^[A-Za-z ]+$/;
   return judge_reg(value,reg2);
}
//判断englishnumber 大小写字母数字
function judge_englishnumber(value){
   var reg2 = /^[A-Za-z0-9]+$/;
   return judge_reg(value,reg2);
}
//判断chinese 中文
function judge_chinese(value){
   var reg2 = /^[\x80-\xff]+$/;
   return judge_reg(value,reg2);
}
//判断username 至少3位 用户名
function judge_username(value){
   var reg2 = /^[\w]{3,}$/;
   return judge_reg(value,reg2);
}
//判断nochinese 非中文
function judge_nochinese(value){
   var reg2 = /^[A-Za-z0-9_-]+$/;
   return judge_reg(value,reg2);
}
//判断datatime 日期时间
function judge_datatime(value){
   var reg2 = /^\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/;//匹配时间格式为2012-02-16或2012-02-16 23:59:59前面为0的时候可以不写
   return judge_reg(value,reg2);
}
//整数int [\-]负整数或正整数,正的没有+号
function judge_int(value){
   var reg2 = /^[\-]{0,1}$/;// /^\d+(\.\d{0,})?$/
   if(reg2.test(value)){
       return true;
   }else{
       return false;
   }
}
//正整数 positive_int >0正整数[全是数字且>0]
function judge_positive_int(value){
    console.log('value',value);
   var reg2 = /^\d+$/;// /^\d+(\.\d{0,})?$/
   if(reg2.test(value) && value>0){
       return true;
   }else{
       return false;
   }
}
//digit:0+正整数
function judge_judge_digit(value){
   var reg2 = /^\d+$/;// /^\d+(\.\d{0,})?$/
   if(reg2.test(value)){
       return true;
   }else{
       return false;
   }
}
//date 判断日期格式是否正确 true正确 false 有误
//$dateTime 日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59
function judge_date(dateTime){
   var reg2 = /^\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/;
   if(reg2.test(dateTime)){
	   return true;
   }else{
	   return false;
   }
}

// 判断时间格式是否正确 true正确 false 有误
//time 时间格式 23:59:59
function judge_time(timeVal){
    var reg2 = /^(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9])$/;
    if(reg2.test(timeVal)){
        return true;
    }else{
        return false;
    }
}

// 时间转换为当天的秒数
//err_type 错误误返回类型
//  1返回错误字符串,>=0:没有错误;
//  2返回值 >=0：秒值 正确-通过; <0 :失败-有误;
//  4返回2的同时，弹出错误提示窗
// time 时间格式 23:59:59
// timeName 时间名称 如 开始时间
function timeToDaySecond(err_type, timeVal, timeName){
    let intDaySecnd = -1;
    let errText = '';
    if(typeof timeVal == 'string' && timeVal.constructor == String && judge_time(timeVal)){
        let  timeArr = timeVal.split(":");
        if(timeArr.length == 3){
            intDaySecnd = parseInt(timeArr[0]) * 3600 + parseInt(timeArr[1]) * 60 + parseInt(timeArr[2]);
        }else{
            errText = timeName + "格式错误";
        }
    }else{
        errText = timeName + "格式错误";
    }
    if(errText != ''){
        if(err_type == 4){
            err_alert(errText);
        }
        if(err_type == 1){
            return errText;
        }
    }
    return intDaySecnd;
}

// 判断一个数是不是 1，2，4，8...,通过先获得所有的位一维数组
// val  需要判断的数
// maxNum 最大执行的次数 如 int: 31  bigint: 63[默认]
function isBitNum(val, maxNum){
    var bitNumArr = getBitArr(maxNum);
    // console.log('val=', val);
    // console.log('bitNumArr=', bitNumArr);
    if(bitNumArr.indexOf(parseInt(val))>= 0) {//存在
        return true;
    }else{
        return false;
    }
}

// 判断一个数是不是 1，2，4，8...,通过先获得所有的位一维数组
// val  需要判断的数
// bitNumArr  所有的位一维数组; 默认为空数组--会自动去获取数组值； 可以获取到位数组后继续使用
// maxNum 最大执行的次数 如 int: 31  bigint: 63[默认]
function isBitNumByArr(val, bitNumArr, maxNum){
    bitNumArr = bitNumArr || getBitArr(maxNum);
    if(bitNumArr.indexOf(parseInt(val))>= 0) {//存在
        return true;
    }else{
        return false;
    }
}

// 获得位数组 --一维 [1，2，4，8...]
function getBitArr(maxNum){
    maxNum = maxNum || 63;
    var reArr = [];
    for(var i = 0; i < maxNum; i++){
        reArr.push(Math.pow(2,i));
    }
    return reArr;
}

// 将位数组，合并为一个数值
// bitArr 位值数组 【1、2、、4、8...】
// 合并后的数值
function getBitJoinVal(bitArr){
    var bitJoinVal = 0;
    for (var i=0 ; i< bitArr.length ; i++) {
        var temVal = bitArr[i];
        bitJoinVal = bitJoinVal | temVal;
    }
    return bitJoinVal;
}

// 比较两个时间,返回  end_time 结束时间 - begin_time 开始时间
//err_type 错误误返回类型
//  1返回错误字符串,数字:没有错误;
//  2返回值 数字：秒值 正确-通过; 字符 :失败-有误;
//  4返回2的同时，弹出错误提示窗  --  不推荐
// begin_time 开始时间
// end_time 结束时间
// begin_time_name 时间名称 如 开始时间
// end_time_name 时间名称 如 结束时间
function compare_time(err_type, begin_time, end_time, begin_time_name, end_time_name){
    let beginDaySecond = timeToDaySecond(err_type, begin_time, begin_time_name);
    if(typeof beginDaySecond == 'string' ){// 有错
        return beginDaySecond;
    }
    if(beginDaySecond < 0){
        return begin_time_name + '有误';
    }

    let endDaySecond = timeToDaySecond(err_type, end_time, end_time_name);
    if(typeof endDaySecond == 'string' ){// 有错
        return endDaySecond;
    }
    if(endDaySecond < 0){
        return end_time_name + '有误';
    }
    return endDaySecond - beginDaySecond;
}


//判断字符长度
//str 需要验证的字符串
//min_length 最小长度;为空则不判断
//max_length 最大长度;为空则不判断
//返回值 true：正确;false:失败
function judge_length(str,min_length,max_length){
	var re_boolean = true;
	var tem_str = trim(str);
	var str_len = tem_str.length;
	if(judge_judge_digit(min_length) && str_len < min_length){
		re_boolean = false;
	}
	if(judge_judge_digit(max_length) && str_len > max_length){
		re_boolean = false;
	}
	return re_boolean;
}

//判断数字范围
//judge_num 需要验证的数字
//min_num 最小;为空则不判断
//max_num 最大;为空则不判断
//返回值 true：正确;false:失败
function judge_range(judge_num,min_num,max_num){
    if(!judge_double(judge_num)){
        return false;
    }
    var re_boolean = true;
    if(judge_double(min_num) && judge_num < min_num){
        re_boolean = false;
    }
    if(judge_double(max_num) && judge_num > max_num){
        re_boolean = false;
    }
    return re_boolean;
}

//比较
//compare_val 需要比较的值[必填]
//operate 操作符[必填]
//operate_val 被比较的值[必填]
//返回值 true：正确;false:失败
function judge_compare(judge_num,operate,operate_val){
    //都为空，则返回false
    if(judge_empty(judge_num) && judge_empty(operate) && judge_empty(operate_val) ){
        return false;
    }
    var operate_str = judge_num + ' ' + operate + ' ' + operate_val;
    return eval(operate_str);
}

//判断日期大小
//日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59
//data1 需要比较的值[必填]
//data2 操作符[必填]
//operate 判断类型 1 > ;2< ; 4 =
//返回值 true：正确;false:失败
function judge_data_size(data1,data2,operate){
    //只要一个参数不是有效日期，则返回false
    if( (!judge_date(data1)) || (!judge_date(data2)) || (!judge_number(operate)) ){
        return false;
    }
    //var has_operate = false;//是否有成功判断的操作 false:没有;true:有-目的:防止没有任何判断,返回成功
    //var need_wait_eq = false;//在判断>或<时已经有错的情况下;是否可能需要判断等于 true:需要；false:不需要
    //转换为时间戳
    var data1_unix = get_unix_time(data1,true);
    var data2_unix = get_unix_time(data2,true);
    //判断大于
    if( (operate & 1) == 1  ){//>
       if( data1_unix > data2_unix){
           //has_operate = true;
           return true;
       }else{//可能还需要判断==
          //need_wait_eq = true;
       }
    }
    //判断小于
    if( (operate & 2) == 2  ){//<
       if( data1_unix < data2_unix){
           //has_operate = true;
           return true;
       }else{//可能还需要判断==
          //need_wait_eq = true;
       }
    }

    //判断等于
    if(   (operate & 4) == 4  ){//=
       if( data1_unix == data2_unix){
           //has_operate = true;
           return true;
       }//else{

          //return false;
       //}
    }//else{
        //if(need_wait_eq){
            //return false;
        //}
    //}
    return false;
    //return has_operate;
}
//生成随机数
function get_random(mix_num,max_num){
	return parseInt(Math.random()*(max_num-mix_num+1)+mix_num,10);
}
//获得当前的时间戳[无毫秒]
function get_now_timestamp(){
    return get_unix_time('',false);
}
//获得当前的时间
//format 'Y-m-d H:i:s'
function get_now_format(format){
    var tem_format = format || 'Y-m-d H:i:s';
    return format_date(tem_format,get_now_timestamp());
}
//格式化时间戳为时间格式
//unix_time 日期时间戳
//format 'Y-m-d H:i:s'
function format_timestamp(unix_time,format){
    var format_data = format_date ( unix_time, format );
    return format_data;
}

function format_date ( format, timestamp ) {
    var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
    var pad = function(n, c){
        if( (n = n + "").length < c ) {
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var txt_weekdays = ["Sunday","Monday","Tuesday","Wednesday",
        "Thursday","Friday","Saturday"];
    var txt_ordin = {1:"st",2:"nd",3:"rd",21:"st",22:"nd",23:"rd",31:"st"};
    var txt_months = ["", "January", "February", "March", "April",
        "May", "June", "July", "August", "September", "October", "November",
        "December"];
    var f = {
        // Day
            d: function(){
                return pad(f.j(), 2);
            },
            D: function(){
                t = f.l(); return t.substr(0,3);
            },
            j: function(){
                return jsdate.getDate();
            },
            l: function(){
                return txt_weekdays[f.w()];
            },
            N: function(){
                return f.w() + 1;
            },
            S: function(){
                return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th';
            },
            w: function(){
                return jsdate.getDay();
            },
            z: function(){
                return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0;
            },


        // Week
            W: function(){
                var a = f.z(), b = 364 + f.L() - a;
                var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;


                if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
                    return 1;
                } else{


                    if(a <= 2 && nd >= 4 && a >= (6 - nd)){
                        nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                        return date("W", Math.round(nd2.getTime()/1000));
                    } else{
                        return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                    }
                }
            },


        // Month
            F: function(){
                return txt_months[f.n()];
            },
            m: function(){
                return pad(f.n(), 2);
            },
            M: function(){
                t = f.F(); return t.substr(0,3);
            },
            n: function(){
                return jsdate.getMonth() + 1;
            },
            t: function(){
                var n;
                if( (n = jsdate.getMonth() + 1) == 2 ){
                    return 28 + f.L();
                } else{
                    if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
                        return 31;
                    } else{
                        return 30;
                    }
                }
            },


        // Year
            L: function(){
                var y = f.Y();
                return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0;
            },
            //o not supported yet
            Y: function(){
                return jsdate.getFullYear();
            },
            y: function(){
                return (jsdate.getFullYear() + "").slice(2);
            },


        // Time
            a: function(){
                return jsdate.getHours() > 11 ? "pm" : "am";
            },
            A: function(){
                return f.a().toUpperCase();
            },
            B: function(){
                // peter paul koch:
                var off = (jsdate.getTimezoneOffset() + 60)*60;
                var theSeconds = (jsdate.getHours() * 3600) +
                                 (jsdate.getMinutes() * 60) +
                                  jsdate.getSeconds() + off;
                var beat = Math.floor(theSeconds/86.4);
                if (beat > 1000) beat -= 1000;
                if (beat < 0) beat += 1000;
                if ((String(beat)).length == 1) beat = "00"+beat;
                if ((String(beat)).length == 2) beat = "0"+beat;
                return beat;
            },
            g: function(){
                return jsdate.getHours() % 12 || 12;
            },
            G: function(){
                return jsdate.getHours();
            },
            h: function(){
                return pad(f.g(), 2);
            },
            H: function(){
                return pad(jsdate.getHours(), 2);
            },
            i: function(){
                return pad(jsdate.getMinutes(), 2);
            },
            s: function(){
                return pad(jsdate.getSeconds(), 2);
            },
            //u not supported yet


        // Timezone
            //e not supported yet
            //I not supported yet
            O: function(){
               var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
               if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
               return t;
            },
            P: function(){
                var O = f.O();
                return (O.substr(0, 3) + ":" + O.substr(3, 2));
            },
            //T not supported yet
            //Z not supported yet


        // Full Date/Time
            c: function(){
                return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P();
            },
            //r not supported yet
            U: function(){
                return Math.round(jsdate.getTime()/1000);
            }
    };


    return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
        if( t!=s ){
            // escaped
            ret = s;
        } else if( f[s] ){
            // a date function exists
            ret = f[s]();
        } else{
            // nothing special
            ret = s;
        }


        return ret;
    });
}

//获得当前/指定的时间戳
//dateTime为空，则获得当前的 日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59
//need_msec 是否保留毫秒 true 保留 false不保留

//new date("month dd,yyyy hh:mm:ss");
//new date("month dd,yyyy");
//new date(yyyy,mth,dd,hh,mm,ss);
//new date(yyyy,mth,dd);
//new date(ms);
//javascript中日期的构造还可以支持 new date("yyyy/mm/dd"); 其中：mm是整数表示月份从0（1月）到11（12月），这样再利用正则表达式就很方便地能够转换字符串日期了。
function get_unix_time(dateTime,need_msec){
	var timestamp = 0;
	if(judge_date(dateTime)){
		timestamp=new Date(dateTime.replace(/-/g,"/")).getTime();
	}else{
		timestamp=new Date().getTime();
	}
	if(need_msec!=true){
		timestamp=Math.floor(timestamp/1000);
	}
	return timestamp;
}
//need_msec 是否保留毫秒 true 保留 false不保留
//当前时间戳[]+随机数
function get_unix_time_random(need_msec,mix_num,max_num){
	return get_unix_time('',need_msec)+ '' + get_random(mix_num,max_num);
}

//解析BaiduTemplate
//template_id 模板id
//json_data 需要解析的json数据对象{....}
//html_id 显示内容的id，如果为空，则只返回解析好的html代码
//返回解析好的html代码
function resolve_baidu_template(template_id,json_data,html_id){
    //可以付值给一个短名变量使用
    //var bt = baidu.template;
    //设置左分隔符为 <!
    //baidu.template.LEFT_DELIMITER='<!';
    //设置右分隔符为 <!
    //baidu.template.RIGHT_DELIMITER='!>';
    //设置默认输出变量是否自动HTML转义，true自动转义，false不转义
    baidu.template.ESCAPE = false;
    var trtemlater = baidu.template(template_id);
    var template_html = trtemlater(json_data);
    if(html_id != ''){
        $("#"+html_id).html(template_html);
    }
    return template_html;
}

//iframe弹出
//iframe的url
//iframe的宽[数字]
//iframe的高[数字] // 建议最商设置为580-小屏笔记本才能显示下
//tishi 标题
//operate_num关闭时的操作0不做任何操作1刷新当前页面
//                          2刷新当前列表页面--[适合更新操作-不更新总数]
//                          22刷新当前列表页面--[适合新加操作-更新总数]
//                          3 执行回调函数 -- 无参数
//                         4刷新当前页面--当前页操作5刷新当前列表页面--当前页操作[适合更新操作-不更新总数]
//                                                  6刷新当前列表页面--自己页面操作时[适合新加操作-更新总数]
//sure_close_tishi 关闭窗口提示文字
// operate_type 操作类型 1：询问再执行[默认]； 2：不询问直接执行
function layeriframe(weburl, tishi, widthnum, heightnum, operate_num, sure_close_tishi, doFun, operate_type){
    operate_type = operate_type || 1;
	 layer.open({
		type: 2,
		//shade: [0.5, '#000'],
		//closeBtn: false,
		fix: false,
		title: tishi,
		maxmin: true,
		//iframe: {src : weburl},
                content: weburl,
		area: [widthnum+'px' , heightnum+'px'],// 宽，高
                //offset: ['0px', '0px'],
		//close: function(index){
                cancel: function(index){
                        var close_tishi = sure_close_tishi || '确定关闭吗？';
			//layer.msg('您获得了子窗口标记：' + layer.getChildFrame('#name', index).val(),3,1);
//			var index1 = parent.layer.confirm(close_tishi, function(){
//				//关闭成功
//				parent.layer.close(index1);
//				switch (operate_num){
//					case 0:
//					  break;
//					case 1:
//					  //刷新当前页面
//					  parent.location.reload()
//					  break;
//					default:
//				}
//				layer.close(index);
//			});
                    switch (operate_type){
                        case 2:// 不询问直接执行
                            layeriframeDo(index, operate_num, doFun);// 执行具体代码
                            break;
                        case 1:// 1：询问再执行
                        //break;
                        default:
                            var index_query = layer.confirm(close_tishi, {
                                btn: ['确定','取消'] //按钮
                            }, function(){
                                layer.close(index_query);
                                layeriframeDo(index, operate_num, doFun);// 执行具体代码
                                // let list_fun_name = '';
                                // switch (operate_num){
                                //         case 0:
                                //             break;
                                //         case 1:
                                //               //刷新当前页面-父页操作时
                                //               parent.location.reload();
                                //               break;
                                //         case 2:
                                //             //刷新当前列表页面-父页操作时--[适合更新操作-不更新总数]
                                //             list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
                                //             eval( 'parent.' + list_fun_name + '(' + true +', ' + true +', ' + false +', 2)');
                                //             // parent.reset_list(true, true, false, 2);
                                //             break;
                                //         case 22:
                                //             //刷新当前列表页面-父页操作时--[适合新加操作-更新总数]
                                //             list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
                                //             eval( 'parent.' + list_fun_name + '(' + true +', ' + true +', ' + true +', 2)');
                                //             // parent.reset_list(true, true, false, 2);
                                //             break;
                                //         case 3:// 执行回调函数
                                //             doFun && doFun();
                                //             break;
                                //         case 4:
                                //             //刷新当前页面-自己页面
                                //             location.reload();
                                //             break;
                                //         case 5:
                                //             //刷新当前列表页面-自己页面操作时[适合更新操作-不更新总数]
                                //             list_fun_name = LIST_FUNCTION_NAME || 'reset_list';
                                //             eval( '' + list_fun_name + '(' + true +', ' + true +', ' + false +', 2)');
                                //             // parent.reset_list(true, true, false, 2);
                                //             break;
                                //         case 6:
                                //             //刷新当前列表页面-自己页面操作时[适合新加操作-更新总数]
                                //             list_fun_name = LIST_FUNCTION_NAME || 'reset_list';
                                //             eval( '' + list_fun_name + '(' + true +', ' + true +', ' + true +', 2)');
                                //             // parent.reset_list(true, true, false, 2);
                                //             break;
                                //         default:
                                // }
                                // layer.close(index);
                            }, function(){
                            });
                    }
                    return false;
		}
	});
}
// 上面的 layeriframe方法的执行体
// 参数也查看上面的方法
function layeriframeDo(index, operate_num, doFun) {

    let list_fun_name = '';
    switch (operate_num){
        case 0:
            break;
        case 1:
            //刷新当前页面-父页操作时
            parent.location.reload();
            break;
        case 2:
            //刷新当前列表页面-父页操作时--[适合更新操作-不更新总数]
            list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
            eval( 'parent.' + list_fun_name + '(' + true +', ' + true +', ' + false +', 2)');
            // parent.reset_list(true, true, false, 2);
            break;
        case 22:
            //刷新当前列表页面-父页操作时--[适合新加操作-更新总数]
            list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
            eval( 'parent.' + list_fun_name + '(' + true +', ' + true +', ' + true +', 2)');
            // parent.reset_list(true, true, false, 2);
            break;
        case 3:// 执行回调函数
            doFun && doFun();
            break;
        case 4:
            //刷新当前页面-自己页面
            location.reload();
            break;
        case 5:
            //刷新当前列表页面-自己页面操作时[适合更新操作-不更新总数]
            list_fun_name = LIST_FUNCTION_NAME || 'reset_list';
            eval( '' + list_fun_name + '(' + true +', ' + true +', ' + false +', 2)');
            // parent.reset_list(true, true, false, 2);
            break;
        case 6:
            //刷新当前列表页面-自己页面操作时[适合新加操作-更新总数]
            list_fun_name = LIST_FUNCTION_NAME || 'reset_list';
            eval( '' + list_fun_name + '(' + true +', ' + true +', ' + true +', 2)');
            // parent.reset_list(true, true, false, 2);
            break;
        default:
    }
    layer.close(index);
}

//iframe中的关闭按钮
//index 父窗口layer对象
//operate_num关闭时的操作0不做任何操作1刷新当前页面
//sure_close_tishi 关闭窗口提示文字
// operate_type 操作类型 1：询问再执行[默认]； 2：不询问直接执行
function iframeclose(index, operate_num,sure_close_tishi, operate_type){
    var close_tishi = sure_close_tishi || '确定关闭吗？';
    operate_type = operate_type || 1;
    //parent.layer.msg('您将标记"' + $('#name').val() + '"成功传送给了父窗口' , 1);
    switch (operate_type){
        case 2:// 不询问直接执行
            iframecloseDo(index, operate_num);// 执行具体代码
            break;
        case 1:// 1：询问再执行
            //break;
        default:
            var index1 = parent.layer.confirm(close_tishi, function(){
                //关闭成功
                parent.layer.close(index1);
                iframecloseDo(index, operate_num);// 执行具体代码
                // switch (operate_num){
                //     case 0:
                //         break;
                //     case 1:
                //         //刷新当前页面
                //         parent.location.reload();
                //         break;
                //     default:
                // }
                // parent.layer.close(index);
            });

    }
}

// iframeclose 上面方法的执行体代码
//index 父窗口layer对象
//operate_num关闭时的操作0不做任何操作1刷新当前页面
function iframecloseDo(index, operate_num){

    switch (operate_num){
        case 0:
            break;
        case 1:
            //刷新当前页面
            parent.location.reload();
            break;
        default:
    }
    parent.layer.close(index);
}

//多少秒后关闭弹窗
//sec_num 秒数
//layer_index 弹窗 标识
function wait_close_popus(sec_num,layer_index){
    var intervalId =setInterval(function(){
        var close_loop = false;//是否关闭循环 true：关闭 ;false不关闭
        if(judge_judge_digit(sec_num) === false){
            sec_num = 0;
        }
        if(sec_num>1){//是数字且大于0
            sec_num--;
        }else{//关闭弹窗
            close_loop = true;
        }
        if(close_loop === true){
            clearInterval(intervalId);
            parent.layer.close(layer_index);
        }
    },1000);
}
//
//
////layer提交表单,完成后刷新父iframe
////url,保存控制器'__URL__/insert'
////fdata 表单序列化后的内容
////return 成功true 失败 false
//function layerfrom(url,fdata){
//	var re_boolean = false;
//	$.post(url, fdata,function(data) {
//	    var state = data.state;
//		var msg = data.msg;
//		var url = data.url;
//		if(state == -1){
//		//失败
//			//layer.alert(msg,8,'提示');
//			var layer1 =  parent.$.layer({
//				title: '操作提示',
//				area: ['auto','auto'],
//				dialog: {
//					msg: msg,
//					btns: 1,
//					type: 10,
//					btn: ['确定'],
//					yes: function(){
//						//window.location.href="__APP__/Attrs/index";
//						re_boolean = false;
//						parent.layer.close(layer1);
//					}
//				}
//			});
//		}else{
//		//成功
//			//layer.alert(msg,8,'提示');
//			var layer1 =  parent.$.layer({
//				title: '操作提示',
//				area: ['auto','auto'],
//				dialog: {
//					msg: msg,
//					btns: 1,
//					type: 10,
//					btn: ['确定'],
//					yes: function(){
//						//window.location.href="__APP__/Attrs/index";
//						re_boolean = true;
//						parent.layer.close(layer1);
//					}
//				}
//			});
//		}
//
//		//关闭iframe
//		//parent.layer.close(index);
//		//if (data>0) {
//
//		//}else{
//		//	layer.alert("添加失败，请重新添加",8,'提示');
//		//	return;
//		//}
//	});
//	return re_boolean;
//}
//获得中间字符串
//oldstr 原字符
//presplit 前分隔符
//backsplit 后分隔符
function get_mid_str(oldstr,presplit,backsplit){
	if(presplit != ""){
		splitstrs=oldstr.split(presplit); //字符分割
		if(splitstrs.length>=2){
			oldstr=splitstrs[1]
		}
	}
	if(backsplit != ""){
		splitstrs=oldstr.split(backsplit); //字符分割
		if(splitstrs.length>=2){
			oldstr=splitstrs[0]
		}
	}
	return oldstr;
}
////根据url地址，用js输出获得的内容
////get_url 要获取内容的url
//function url_writeln(get_url){
//   var layer_index = layer.load('正在努力加载...');
//	$.ajax({
//	   type: "get",
//	   async: false,
//	   url: get_url,
//	   data: '',
//	   beforeSend:function(){
//		 //obj.text("正在加载,请稍等!");
//	  },
//	   success: function(data){
//			layer.close(layer_index);
//			document.write(data);
//	   }
//	});
//}

//城市下拉框功能方法开始

//初始化下拉框选项
//area_id 城市编号 0 获得省
//level 城市等级 1:省;2:市;3:区/县
//click_obj 点击省/市的当前点击对象
//[去掉返回值,改用异步]返回select 的option html代码
function reset_area_sel(area_id,level,click_obj){
	var option_html = "";
	if(area_id>=0 && level>0){
         var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
		//ajax请求银行信息
		var data = {};
		data['area_id'] = area_id;
		data['level'] = level;
		$.ajax({
			'async': false,//同步
			'type' : 'POST',
			'url' : '/api/area',
            'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
			'data' : data,
			'dataType' : 'json',
			'success' : function(ret){
				if(!ret.apistatus){//失败
					//alert('失败');
					err_alert(ret.errorMsg);
				}else{//成功
					//alert('成功');
					option_html = reset_sel_option(ret.result);
					switch(level){
						case 1://1:省[初始化省]
							reset_province(option_html);
							break;
						case 2://;2:市;
							reset_city(option_html,click_obj);
							break;
						case 3://3:区/县
							reset_area(option_html,click_obj);
							break;
						default:
					}
                    console.log('省市加载成功');
				}
                layer.close(layer_index);//手动关闭
			}
		});
	}
	//return option_html;
}
//初始化[页面所有的]省下拉框
//select 的option html代码
function reset_province(option_html){
	var province_obj = $(".province_id");
	//初始省下拉项及给改变值事件
	$(".province_id").each(function () {
		empty_province_option($(this));
		$(this).append(option_html);
		$(this).change(function () {
			//var province_id = $(this).val();
			change_province_sel($(this));
		});
	});
}
//点击省重置市下拉框[清空不在此，请在之前处理]
//select 的option html代码
//click_obj 点击省/市的当前点击对象
function reset_city(option_html,click_obj){
	//清空市、县/区
	var area_sel_obj = click_obj.closest('.area_select');//当前的父对象
	var city_obj = area_sel_obj.find(".city_id");
	if(city_obj.length<=0){
		return;
	}
	empty_city_option(city_obj);
	city_obj.append(option_html);
	city_obj.change(function () {
		change_city_sel($(this));
	});
}

//点击市重置县/区下拉框[清空不在此，请在之前处理]
//select 的option html代码
//click_obj 点击省/市的当前点击对象
function reset_area(option_html,click_obj){
	//清空市、县/区
	var area_sel_obj = click_obj.closest('.area_select');//当前的父对象
	var area_obj = area_sel_obj.find(".area_id");
	if(area_obj.length<=0){
		return;
	}
	empty_area_option(area_obj);
	area_obj.append(option_html);
}
//根据选择的省id,重置市下拉框
//province_obj 当前点击的省对象
function change_province_sel(province_obj){
	var province_id = province_obj.val();
	//清空市、县/区
	var area_sel_obj = province_obj.closest('.area_select');//当前的父对象
	var city_obj = area_sel_obj.find(".city_id");
	var area_obj = area_sel_obj.find(".area_id");
	if(city_obj.length>0){
		empty_city_option(city_obj);
		if(province_id>0){
			reset_area_sel(province_id,2,province_obj);
		}
	}
	if(area_obj.length>0){
		empty_area_option(area_obj);
	}
}

//根据选择的市id,重置区/县下拉框
//province_obj 当前点击的市对象
function change_city_sel(city_obj){
	var city_id = city_obj.val();
	//清空市、县/区
	var area_sel_obj = city_obj.closest('.area_select');//当前的父对象
	var area_obj = area_sel_obj.find(".area_id");
	if(area_obj.length>0){
		empty_area_option(area_obj);
		if(city_id>0){
			reset_area_sel(city_id,3,city_obj);
		}
	}
}
//清空省对象
//record_obj 当前操作对象
function empty_province_option(record_obj){
	var empty_option_json = {"": "请选择省"};
	var empty_option_html = reset_sel_option(empty_option_json);//请选择省
	record_obj.empty();//清空下拉
	record_obj.append(empty_option_html);
}
//清空城市对象
//record_obj 当前操作对象
function empty_city_option(record_obj){
	var empty_option_json = {"": "请选择市"};
	var empty_option_html = reset_sel_option(empty_option_json);//请选择省
	record_obj.empty();//清空下拉
	record_obj.append(empty_option_html);
}
//清空省对象
//record_obj 当前操作对象
function empty_area_option(record_obj){
	var empty_option_json = {"": "请选择区/县"};
	var empty_option_html = reset_sel_option(empty_option_json);//请选择省
	record_obj.empty();//清空下拉
	record_obj.append(empty_option_html);
}

//初始化标签json串;[{'id': 0,'tag_name': '标签名称','id_input_name':'id[]','tag_input_name':'tag_name[]'},...]
//返回select 的option html代码
function reset_tags_option(tags_json){
    var tags_data_list_json = {"data_list":tags_json};
    var html_tags = resolve_baidu_template('baidu_template_tag_item_list',tags_data_list_json,'');//解析
    //alert(html_sel_option);
    return html_tags;
}

//初始化下拉框json串[注意:option_json下标名不能变];{"option_json":{"1": "北京","2": "天津","3": "上海"}}
//返回select 的option html代码
function reset_sel_option(option_json){
	var sel_option_json={"option_json":option_json};//{"option_json":{"1": "北京","2": "天津","3": "上海"}};
	var html_sel_option = resolve_baidu_template('baidu_template_option_list',sel_option_json,'');//解析
	//alert(html_sel_option);
	return html_sel_option;
}

//初始化省市区
//area_json = {"province":{"id":"province_id","value":"1"},"city":{"id":"city_id","value":"1"},"area":{"id":"area_id","value":"1"}}
//level 城市等级 1:省;2:市;3:区/县
function init_area_sel(area_json,level){
	if( trim(level) == '' || (!judge_positive_int(level)) || level<1 || level>3 ){
		return false;
	}
	var sel_json = {};
	switch(level){
		case 1://1:省[初始化省]
			sel_json = area_json.province;
			break;
		case 2://;2:市;
			sel_json = area_json.city;
			break;
		case 3://3:区/县
			sel_json = area_json.area;
			break;
		default:
	}
    console.log(sel_json);

	//下拉框名称
	var select_name_id = sel_json.id || '';
	if( trim(select_name_id) == ''  ){
		return false;
	}
	var select_obj = $("#"+select_name_id);
	if(select_obj.length<=0){
		return false;
	}
    console.log(select_obj);
	var select_val_id = sel_json.value || '';
    console.log(select_val_id);
	if( trim(select_val_id) == '' || (!judge_positive_int(select_val_id)) ){
		return false;
	}
	//三次去指定省下拉框
	var sec_num = 3;
	var intervalId =setInterval(function(){
		var close_loop = false;//是否关闭循环 true：关闭 ;false不
		if(judge_judge_digit(sec_num) === false){
				sec_num = 0;
		}
		if(sec_num>1){//是数字且大于0
			sec_num--;

			var option_num = $("#"+ select_name_id +" option").length;
			if(option_num > 1){
				close_loop = true;
				select_obj.val(select_val_id).change();// 如果#select有定义change()事件就会调用

			}
		}else{//关闭弹窗
			close_loop = true;
		}
		if(close_loop === true){
			clearInterval(intervalId);
			//下一级展开
			var tem_level = level+1;
			init_area_sel(area_json,tem_level);
		}
	},1000);
}
//城市下拉框功能方法结束

//判断是否有权限
//page_operate_json 当前页面的权限json串
//page_power_arr 当前页面的权限数组 [1,2,3]
//operate_num 当前的操作[动作]编号
//operate_arr 可以有的操作类目[1,2,3];注意不要加引号
//item 当前记录的json
//is_alert_err 是否弹出错误提示[没有权限的提示]true:弹出提示,false:不弹出提示
//有权限返回true[显示],没有权限false[不显示]
function judge_power(page_operate_json,page_power_arr,operate_num,operate_arr,item,is_alert_err){
    if(operate_arr.length<=0){
        return true;
    }
    //判断动作对象是否存在
    var record_action = page_operate_json[operate_num];
    if(record_action == undefined){
        if(is_alert_err){
            err_alert('当前操作非法!');
        }
        return false;
    }
    var operate_list = record_action.operate_list;
    if(operate_list == undefined){
        if(is_alert_err){
            err_alert('没有操作对象!');
        }
        return false;
    }
    //遍历每一个权限
    for(var obj_i in operate_list){//遍历json对象的每个key/value对,p为key
       //判断当前对象是否在当前操作范围
       if(operate_arr.indexOf(parseInt(obj_i))<0){//不存在
            alert(obj_i + "不存在");
            continue;
       }
       var record_power = operate_list[obj_i];
       //判断是否有此权限
       var is_super = record_power.is_super;//是否超级权限1:是[直接操作],0:看power_num权限
       var power_num = record_power.power_num;//权限编号
       var power_name = record_power.power_name+"权限";//权限名称
       var judge_fields_json = record_power.judge_fields;//权限字段
       if(is_super == 1 || is_super == "1"){//超级权限
           return true;
       }
       //判断是否有当前权限
       if(page_power_arr.indexOf(parseInt(power_num))<0){
           continue;
       }
       //遍历字段
        for(var field in judge_fields_json){//遍历json对象的每个key/value对,p为key
            var field_value = item[field];
            if(field_value == undefined){
                if(is_alert_err){
                    err_alert('字段或值不存在!');
                }
                return false;
            }
            var field_json = judge_fields_json[field];
            var judge_value_json = field_json.field_val;
            if(judge_value_json == undefined){
                if(is_alert_err){
                    err_alert('字段或值不存在。');
                }
                return false;
            }
            var old_val = judge_value_json.old_val;
            if(field_value == undefined || old_val.length<=0){
                if(is_alert_err){
                    err_alert('对比值不存在。');
                }
                return false;
            }
            var operate = field_json.operate;
            if(operate == undefined){
                if(is_alert_err){
                    err_alert('操作符不存在。');
                }
                return false;
            }
            //遍历判断的值
            var judge_power_result = false;//true:有权限,false:没有权限
            for(var k = 0; k < old_val.length; k++) {
                var contrast_val = old_val[k];
                var err_msg = judge_validate(1,power_name,field_value,true,"compare",operate,contrast_val);
                if(judge_empty(err_msg)){//值正确,有权限
                    judge_power_result = true;
                    break;
                }
            }
            if(!judge_power_result){//没有权限
                if(is_alert_err){
                    err_alert('您没有['+power_name+']操作权限!');
                }
                return false;
            }else{
                return true;
            }
        }

    }
    return false;
}

//json对象引用传递改为值传递
//province_obj 当前点击的省对象
function json_quote_val(json_obj){
    var json_str = JSON.stringify(json_obj);
    var re_json = $.parseJSON(json_str); //$为jQuery对象需要引入jQuery包
    return re_json;
}
//判断是否有复选框被选中
//body_data_id 动太表格 内容列表id
//ele_type 元素类型 1:id,2class,3 body_data_id就是外面对象
//返回 true:有选中;false:没有选中
function judge_list_checked(body_data_id,ele_type){
    var body_obj = null;
    if(ele_type == "1" || ele_type == 1){
        body_obj = $('#'+body_data_id);
    }else if(ele_type == "2" || ele_type == 2){
        body_obj = $('.'+body_data_id);
    }else{
        body_obj = body_data_id;
    }
    var re_result = false;
    body_obj.find('input:checkbox').each(function(){
        var tem_val = $(this).val();
        console.log('disabled', $(this).prop('disabled'));
        if ($(this).is(':checked') && (!$(this).prop('disabled')) ) {
            //alert('选中'+tem_val);
            re_result = true;
            return true;
        } else {
            if(re_result){//退出each
                return false;
            }
            //alert('未选中'+tem_val);
        }
    });
    return re_result;
}

//获得选中的值 , 需要特别注意,没有选中时，返回的是""字符
//body_data_id 动太表格 内容列表id
//ele_type 元素类型 1:id,2class,3 body_data_id就是外面对象
//check_type 选择类型[位操作] 1:选中,2未选中的
// has_class_name 默认为空，不为空，则需要判断有此class，才是我要的对象
// val_split_str 值分隔符，默认逗号 ,
//返回 选中的值,多个用,号分隔
function get_list_checked(body_data_id,ele_type,check_type, has_class_name, val_split_str){
    // console.log('数组', ele_type);
    var body_obj = null;
    if(ele_type == "1" || ele_type == 1){
        body_obj = $('#'+body_data_id);
    }else if(ele_type == "2" || ele_type == 2){
        body_obj = $('.'+body_data_id);
    }else{
        body_obj = body_data_id;
    }
    val_split_str = val_split_str || ',';
    var seled_ids = '';
    body_obj.find('input:checkbox').each(function(){
        var checkObj = $(this);
        var tem_val = $(this).val();
        var is_need = false;
        console.log('disabled', $(this).prop('disabled'));
        if ( $(this).is(':checked') && (!$(this).prop('disabled'))  && ( (check_type & 1) == 1) ) {
            is_need = true;
        }else{
            if(  !$(this).is(':checked')  && (!$(this).prop('disabled')) &&  (check_type & 2) == 2){
                is_need = true;
            }
        }
        if(is_need){
            if(!isEmpeyVal(has_class_name)){
                if(!checkObj.hasClass(has_class_name)){
                    is_need = false;
                }
            }
            if(is_need){
                if(seled_ids != ''){
                    // seled_ids+=',';
                    seled_ids+=val_split_str;
                }
                seled_ids+=tem_val;
            }
        }
    });
    return seled_ids;
}

// 初始化下拉框
// select_name 下接框 name 名称
// empty_option_json  初始对象 {"": "请选择" + config.child_sel_txt};
function initSelect(select_name ,empty_option_json) {
    var obj =$('select[name=' + select_name + ']');
    // var empty_option_json = config.child_sel_txt;// {"": "请选择" + config.child_sel_txt};
    var empty_option_html = reset_sel_option(empty_option_json);//请选择省
    obj.empty();//清空下拉
    obj.append(empty_option_html);
}

// 下拉框选择事件[二级分类的，第一级点击，ajax更新第二级下拉框]
// config 配置对象
/*
{
        'child_sel_name': 'group_id',// 第二级下拉框的name
        'child_sel_txt': {'': "请选择小组" },// 第二级下拉框的{值:请选择文字名称}
        'change_ajax_url': "{{ url('api/manage/staff/ajax_get_child') }}",// 获取下级的ajax地址
        'parent_param_name': 'parent_id',// ajax调用时传递的参数名
        'other_params':{'aaa':123,'ccd':'dfasfs'},//其它参数
    }
 */
// first_seled_val 第一级下拉框选中的值
// group_id 第二级下拉框选中的值 [修改页面初始化时使用]
// ajax_async ajax 同步/导步执行 //false:同步;true:异步
function changeFirstSel(config, first_seled_val, second_seled_val, ajax_async){
    var obj =$('select[name=' + config.child_sel_name + ']');
    var empty_option_json = config.child_sel_txt;// {"": "请选择" + config.child_sel_txt};
    var empty_option_html = reset_sel_option(empty_option_json);//请选择省
    obj.empty();//清空下拉
    obj.append(empty_option_html);

    var option_html = "";
    if(first_seled_val != "" ){ //first_seled_val >0
        var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
        //ajax请求银行信息
        var data = config.other_params;//{};
        data[config.parent_param_name] = first_seled_val;
        console.log(config.change_ajax_url);
        console.log(data);
        $.ajax({
            'async': ajax_async,// true,//false:同步;true:异步
            'type' : 'POST',
            'url' : config.change_ajax_url,
            'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
            'data' : data,
            'dataType' : 'json',
            'success' : function(ret){
                if(!ret.apistatus){//失败
                    //alert('失败');
                    err_alert(ret.errorMsg);
                }else{//成功
                    //alert('成功');
                    option_html = reset_sel_option(ret.result);
                    obj.append(option_html);
                    console.log('加载成功');
                    if( obj.find("option[value = '" + second_seled_val + "']").length > 0){
                        obj.val(second_seled_val);
                    }
                    //if(second_seled_val != ""){ // second_seled_val > 0
                    //    obj.val(second_seled_val);
                    //}
                }
                layer.close(layer_index);//手动关闭
            }
        });
    }
}

//获取两日期之间日期列表函数
// var stime = '2018-07-25'; //开始日期
// var etime = '2018-08-02'; //结束日期
// getdiffdate(stime,etime);
function getdiffdate(stime,etime){
    //初始化日期列表，数组
    var diffdate = new Array();
    var i=0;
    //开始日期小于等于结束日期,并循环
    while(stime<=etime){
        diffdate[i] = stime;

        //获取开始日期时间戳
        var stime_ts = new Date(stime).getTime();
        console.log('当前日期：'+stime   +'当前时间戳：'+stime_ts);

        //增加一天时间戳后的日期
        var next_date = stime_ts + (24*60*60*1000);

        //拼接年月日，这里的月份会返回（0-11），所以要+1
        var next_dates_y = new Date(next_date).getFullYear()+'-';
        var next_dates_m = (new Date(next_date).getMonth()+1 < 10)?'0'+(new Date(next_date).getMonth()+1)+'-':(new Date(next_date).getMonth()+1)+'-';
        var next_dates_d = (new Date(next_date).getDate() < 10)?'0'+new Date(next_date).getDate():new Date(next_date).getDate();

        stime = next_dates_y+next_dates_m+next_dates_d;

        //增加数组key
        i++;
    }
    console.log(diffdate);
}

// 计算日期差
// console.log(getDiffDate('2019-03-21')) ;
// end_time 结束日期 格式 :当前日期/指定日期 -->到这个日期的信息
// start_time 开始日期 --不传为当前日期时间
function getDiffDate(end_time, start_time){
    console.log('----end_time----', end_time);
    console.log('----start_time----', start_time);
    var date = new Date();
    console.log('----date----', date);
    if(start_time){
        date = new Date(start_time);//设置截止时间
        console.log('----date----', date);
    }
    var now = date.getTime();
    console.log('----now----', now);

    // var exam_end_time = EXAM_END_TIME;// 结束时间
    var endDate = new Date(end_time);//设置截止时间
    console.log('----endDate----', endDate);
    var end = endDate.getTime();
    console.log('----end----', end);
    var leftTime = end - now; //时间差
    console.log('----leftTime----', leftTime);
    // var y, d, h, m, s, ms;
    var diffTimeObj = getDiffTime(leftTime);
    if(leftTime < 0 ){
        for(let p in diffTimeObj){
            diffTimeObj[p] = - diffTimeObj[p];
        }
    }
    return diffTimeObj;
}

// 格式化值
// num 数字
// n 保留长度
function padZoreLeft(num, n) {
    var len = num.toString().length;
    while(len < n) {
        num = "0" + num;
        len++;
    }
    return num;
}

// 根据毫秒，返回时间对象
// leftTime 时间相差的毫秒数
function getDiffTime(leftTime){
    leftTime = Math.abs(leftTime);
    var returnObj = {};
    // ceil向上取整  floor向下取整
    // max 向上取整
    // min 下下取整

    // 年
    returnObj.a_min_y = Math.floor(leftTime / 1000 / 60 / 60 / 24 / 365);// 多少年 -- 向下取整
    returnObj.a_max_y = Math.ceil(leftTime / 1000 / 60 / 60 / 24 / 365);// 多少年-- 向上取整

    // 天
    // 共多少天
    returnObj.a_min_d = Math.floor(leftTime / 1000 / 60 / 60 / 24);// 共多少天 -- 向下取整
    returnObj.a_max_d = Math.ceil(leftTime / 1000 / 60 / 60 / 24);// 共多少天 -- 向上取整

    // 共多少天-- 除整年
    returnObj.y_mix_d = Math.floor(leftTime / 1000 / 60 / 60 / 24 % 365);// -- 向下取整
    returnObj.y_max_d = Math.ceil(leftTime / 1000 / 60 / 60 / 24 % 365);// -- 向上取整


    // 小时
    returnObj.a_min_h = Math.floor(leftTime / 1000 / 60 / 60);// 共多少小时--- 向下取整
    returnObj.a_max_h = Math.ceil(leftTime / 1000 / 60 / 60);// 共多少小时--- 向上取整

    // 共多少小时-- 除整年
    returnObj.y_mix_h = Math.floor( leftTime / 1000 / 60 / 60  % (365 * 24 ) );// -- 向下取整
    returnObj.y_max_h = Math.ceil( leftTime / 1000 / 60 / 60  % (365 * 24 ) );// -- 向上取整

    // 共多少小时-- 除整年天
    returnObj.d_mix_h = Math.floor(leftTime / 1000 / 60 / 60 % 24);// 最后一天的多少小时 -- 向下取整
    returnObj.d_max_h = Math.ceil(leftTime / 1000 / 60 / 60 % 24);// 最后一天的多少小时 -- 向上取整

    // 分钟
    returnObj.a_min_m = Math.floor(leftTime / 1000 / 60);// 共多少分钟 -- 向下取整
    returnObj.a_max_m = Math.ceil(leftTime / 1000 / 60);// 共多少分钟 -- 向上取整

    returnObj.y_mix_m = Math.floor( leftTime / 1000 / 60  % (365 * 24 * 60 ) );// -- 向下取整
    returnObj.y_max_m = Math.ceil( leftTime / 1000 / 60  % (365 * 24  * 60 ) );// -- 向上取整

    returnObj.h_min_m = Math.floor(leftTime / 1000 / 60 % 60);// 最后一小时的多少分钟 -- 向下取整
    returnObj.h_max_m = Math.ceil(leftTime / 1000 / 60 % 60);// 最后一小时的多少分钟 -- 向上取整

    // 秒
    returnObj.a_min_s = Math.floor(leftTime / 1000);// 共多少分钟 -- 向下取整
    returnObj.a_max_s = Math.ceil(leftTime / 1000);// 共多少分钟 -- 向上取整

    returnObj.y_mix_s = Math.floor( leftTime / 1000 % (365 * 24 * 60 * 60 ) );// -- 向下取整
    returnObj.y_max_s = Math.ceil( leftTime / 1000 % (365 * 24  * 60 * 60 ) );// -- 向上取整

    returnObj.m_min_s = Math.floor(leftTime / 1000 % 60);// 最后一分钟的多少秒 -- 向下取整
    returnObj.m_max_s = Math.ceil(leftTime / 1000 % 60);// 最后一分钟的多少秒 -- 向上取整

    // 毫秒
    returnObj.a_min_ms = Math.floor(leftTime);// 共多少毫秒 -- 向下取整
    returnObj.a_max_ms = Math.ceil(leftTime);// 共多少毫秒 -- 向上取整

    returnObj.y_mix_ms = Math.floor( leftTime % (365 * 24 * 60 * 60 * 1000) );// -- 向下取整
    returnObj.y_max_ms = Math.ceil( leftTime % (365 * 24  * 60 * 60  * 1000) );// -- 向上取整

    returnObj.s_min_ms = Math.floor(leftTime  % 1000);// 最后一分钟的多少毫秒 -- 向下取整
    returnObj.s_max_ms = Math.ceil(leftTime  % 1000);// 最后一分钟的多少毫秒 -- 向上取整

    return returnObj;
}

// 获得文件名称
// filePath  为 $("#file").val();
function getUpFileName(filePath){
    console.log('filePath=', filePath);
    var fileName = '';// getFileName(filePath);
    // filePath.indexOf("jpg")!=-1 || filePath.indexOf("png")!=-1
    if(filePath.length > 0){
        // $(".fileerrorTip").html("").hide();
        var arr = filePath.split('\\');
        fileName = arr[arr.length-1];
        // $(".showFileName").html(fileName);
    }else{
        // $(".showFileName").html("");
        // $(".fileerrorTip").html("您未上传文件，或者您上传文件类型有误！").show();
        // return false;
    }
    console.log('fileName=', fileName);
    return fileName;
}

// 获得文件名称
// o  为 $("#file").val();
function getFileName(o){
    var pos=o.lastIndexOf("\\");
    return o.substring(pos+1);
}

// 单个文件上传
// fileObj 文件上传对象
// ajaxUrl 上传文件处理url
// operate_num 关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面--当前页 ; 4 刷新当前列表页面-第一页
//                          5 执行回调函数 -- 有一个参数 ret  调用上传后接口返回值
// otherParams 其它参数 {'键':值,...}
function upLoadFileSingle(fileObj, ajaxUrl, operate_num, otherParams, doFun) {
    if (fileObj.files.length == 0) {
        return false;
    }
    var data = new FormData();

    data.append('photo', fileObj.files[0]);
    //            data.append('allowTypes', 'jpg|png');
    //            data.append('size', 1024*2);
    //data.append('maxWidth', 800);
    //data.append('maxHeight', 800);
    //            data.append('upload_type', upload_type);
    // 其它参数
    for(var p in otherParams){
        tem_name = p;
        tem_value = otherParams[p];
        if(tem_value == '') continue;
        data.append(tem_name, tem_value);
    }
    var layer_index = layer.load();
    console.log(ajaxUrl);
    console.log(data);
    $.ajax({
        url: ajaxUrl,// '/public/AjaxData/uploadImg2',
        headers:get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        type: 'POST',
        data: data,
        cache: false,
        contentType: false, //不可缺
        processData: false, //不可缺
        dataType: 'json',
        success: function (ret) {
            console.log(ret);
            if (!ret.apistatus) {
                err_alert(ret.errorMsg);
                fileObj.value = ''; //虽然file的value不能设为有字符的值，但是可以设置为空值
            } else {
                layer.msg('处理成功！', {
                    icon: 1,
                    shade: 0.3,
                    time: 4000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    switch (operate_num){
                        case 0:
                            break;
                        case 1:
                            //刷新当前页面
                            location.reload();
                            break;
                        case 2:
                            //刷新当前列表页面--当前页
                            reset_list(true, true, false, 2);
                            break;
                        case 4:
                            //刷新当前列表页面-第一页
                            reset_list(false, true, true, 2);
                            break;
                        case 5:// 执行回调函数 -- 有一个参数 ret  调用上传后接口返回值
                            doFun && doFun(ret);
                            break;
                        default:
                    }
                    fileObj.value = ''; //虽然file的value不能设为有字符的值，但是可以设置为空值
                });
            }
            layer.close(layer_index);//手动关闭
        }
    });
}
// js求距离的方法
/**
 * 转换弧度
 * @param d
 * @returns {number}
 */
function getRad(d){
    var PI = Math.PI;
    return d*PI/180.0;
}

/**
 * 根据经纬度计算两点间距离
 * @param lng1
 * @param lat1
 * @param lng2
 * @param lat2
 * @returns {number|*}
 * @constructor
 */
function CoolWPDistance(lng1,lat1,lng2,lat2){
    var f = getRad((lat1 + lat2)/2);
    var g = getRad((lat1 - lat2)/2);
    var l = getRad((lng1 - lng2)/2);
    var sg = Math.sin(g);
    var sl = Math.sin(l);
    var sf = Math.sin(f);
    var s,c,w,r,d,h1,h2;
    var a = 6378137.0;//The Radius of eath in meter.
    var fl = 1/298.257;
    sg = sg*sg;
    sl = sl*sl;
    sf = sf*sf;
    s = sg*(1-sl) + (1-sf)*sl;
    c = (1-sg)*(1-sl) + sf*sl;
    w = Math.atan(Math.sqrt(s/c));
    r = Math.sqrt(s*c)/w;
    d = 2*w*a;
    h1 = (3*r -1)/2/c;
    h2 = (3*r +1)/2/s;
    s = d*(1 + fl*(h1*sf*(1-sg) - h2*(1-sf)*sg));
    if(s >= 1000 && s <= 99000){
        var kilometer = s/1000;
        s = kilometer.toFixed(1) + 'km';
    }else if(s > 99000){
        s = '>99km';
    }else{
        s = Math.round(s) + 'm';
    }
    // s = s/1000;
    // s = s.toFixed(2);//指定小数点后的位数。
    return s;
}

function GetDistance( lat1,  lng1,  lat2,  lng2){
    var radLat1 = getRad(lat1);
    var radLat2 = getRad(lat2);
    var a = radLat1 - radLat2;
    var  b = getRad(lng1) - getRad(lng2);
    var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a/2),2) +
        Math.cos(radLat1)*Math.cos(radLat2)*Math.pow(Math.sin(b/2),2)));
    s = s *6378.137 ;
    s = Math.round(s * 10000) / 10000;
    return s;
}
// ~~~~~~~~~~~~~~~~~~~~标签~~相关~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// 初始化时，只要id 、tag_name 其它的，会根据配置自动完成
// var aaa = [
//     {'id': 0, 'tag_name': '标签名称','id_input_name':'id[]','tag_input_name':'tag_name[]'},
//     {'id': 0, 'tag_name': '标签名称sss','id_input_name':'id[]','tag_input_name':'tag_name[]'}
// ];
// 初始化标签
function  init_tags(){
    for(var p in TAGS_CONFIG){
        var tag_obj = $('#' + p);
        if(tag_obj.length <= 0) continue;
        var record_config = TAGS_CONFIG[p];
        var init_tags = getAttrVal(record_config, 'init_tags', true, []);

        // id  name 进行覆盖
        var id_input_name = getAttrVal(record_config, 'id_input_name', true, 'id_input_name[]');
        var tag_input_name = getAttrVal(record_config, 'tag_input_name', true, 'tag_input_name[]');
        for(var p in init_tags){
            init_tags[p].id_input_name = id_input_name;
            init_tags[p].tag_input_name = tag_input_name;
        }

        var tags_html = reset_tags_option(init_tags);
        console.log(tags_html);
        tag_obj.find('.tags_list').html(tags_html);
    }
}
// 判断标签数量--所有标签
// true：正确；false:有错
function judge_tags_num() {
    for(var p in TAGS_CONFIG){
        var tag_obj = $('#' + p);
        if(tag_obj.length <= 0) continue;
        if(!judge_tags_item_num(p)) return false;// 有错
    }
    return true;
}
// 判断标签数量--指定标签
// true：正确；false:有错
function judge_tags_item_num(tag_config_key) {
    var record_config = TAGS_CONFIG[tag_config_key];
    var tag_txt_name = getAttrVal(record_config, 'tag_name', true, 0);
    var min_num = getAttrVal(record_config, 'min_num', true, 0);
    var max_num = getAttrVal(record_config, 'max_num', true, 0);
    var tag_obj = $('#' + tag_config_key);
    var record_num = tag_obj.find('.tag').length;
    if(min_num > 0 && min_num > record_num){
        err_alert( tag_txt_name + '至少添加' +  min_num + '条记录！');
        return false;
    }
    if(max_num > 0 && max_num <= record_num){
        err_alert(tag_txt_name + '最多只能添加' +  max_num + '条记录！');
        return false;
    }
    return true;
}
// 添加标签
// obj 添加按钮对象
function add_tag(obj) {
    var tags_block = obj.closest('.tags_block');
    // 判断值是否为空
    var tag_name = tags_block.find('input[name="tag_name"]').val();
    console.log('tag_name=', tag_name);
    if(tag_name == ''){
        err_alert('名称不能为空！');
        return false;
    }
    var tag_config_key = tags_block.attr('id');// obj.data("tag_config_key");
    console.log('id=' , tag_config_key);
    if(isEmptyAttr(TAGS_CONFIG, tag_config_key)){
        err_alert('标签配置信息不存在！');
        return false;
    }
    var record_config = getAttrVal(TAGS_CONFIG, tag_config_key, true, {});
    var tag_txt_name = getAttrVal(record_config, 'tag_name', true, 0);
    var default_id = getAttrVal(record_config, 'default_id', true, 0);
    var id_input_name = getAttrVal(record_config, 'id_input_name', true, 0);
    var tag_input_name = getAttrVal(record_config, 'tag_input_name', true, 0);
    var min_len = getAttrVal(record_config, 'min_len', true, '');
    var max_len = getAttrVal(record_config, 'max_len', true, '');
    var min_num = getAttrVal(record_config, 'min_num', true, 0);
    var max_num = getAttrVal(record_config, 'max_num', true, 0);

    // 判断值的长度
    if(!judge_validate(4,tag_txt_name,tag_name,true,'length',min_len,max_len)){
        return false;
    }

    var record_num = tags_block.find('.tag').length;
    if(max_num > 0 && max_num <= record_num){
        err_alert(tag_txt_name + '最多只能添加' +  max_num + '条记录！');
        return false;
    }

    var tag_exist = false;// 记录是否已经存在 true:存在；false:不存在
    // 判断是否已经存在
    tags_block.find('.tag').each(function () {
        var tagObj = $(this);
        var tag_txt = tagObj.find('.tag_txt').html();
        console.log(tag_txt);
        if(tag_name == tag_txt){
            console.log('记录已经存在！');
            tag_exist = true;
            return false;
        }
        console.log('===',tag_txt);
    });
    if(tag_exist){
        console.log('=记录已经存在！');
        err_alert('记录已经存在！');
        return false;
    }else{
        console.log('=记录不存在！');
        var tag_obj = {'id': default_id, 'tag_name': tag_name,'id_input_name':id_input_name,'tag_input_name':tag_input_name};
        tags_json = [
            // {'id': 0, 'tag_name': '标签名称','id_input_name':'id[]','tag_input_name':'tag_name[]'},
            // {'id': 0, 'tag_name': '标签名称sss','id_input_name':'id[]','tag_input_name':'tag_name[]'}
        ];
        tags_json.push(tag_obj);
        var tags_html = reset_tags_option(tags_json);
        tags_block.find('.tags_list').append(tags_html);
        tags_block.find('input[name="tag_name"]').val('');

    }
}
// 标签删除
// obj 点击的删除对象--叉 *
function del_tag(obj) {
    var index_query = layer.confirm('您确定移除吗？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        obj.closest('.tag').remove();
        layer.close(index_query);
    }, function(){
    });
}
// 清空标签
function empty_tag(tag_config_key){
    var tag_obj = $('#' + tag_config_key);
    if(tag_obj.length <= 0) return true;
    tags_block.find('.tags_list').html('');
}

//~~~~~~~~~~~~~~~~~~~~标签~~相关~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//~~~~~~~~~~~~~~~~~~~~JavaScript中unicode编码与String互转（三种方法）~~相关~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//unicode转String
// 1. eval("'" + str + "'");//当str中有带分号'或者"时，会报错，此时改成eval('"' + str + '"')即可
// 2. (new Function("return '" + str + "'"))();//同上
// 3. unescape(str.replace(/\u/g, "%u"));
// //string转unicode（str字符的第i个）
// 1."\\u" + str.charCodeAt(i).toString(16);

//unicode2string
// var str = "我是中国人";
// var str_u = string2unicode(str);//\u6211\u662f\u4e2d\u56fd\u4eba
// var str_s = unicode2string(str_u);// 我是中国人
// function string2unicode(str){
//     var ret ="";
//     for(var i=0; i<str.length; i++){
//         //var code = str.charCodeAt(i);
//         //var code16 = code.toString(16);   　　　　
//         //var ustr = "\\u"+code16;
//         //ret +=ustr;
//         ret += "\\u" + str.charCodeAt(i).toString(16);
//     }
//     return ret;
// }

// 样例(包含英文的String)
// 如果String包含有英文时，转unicode编码时会产生\\u34这样子的，而JS自身的unicode转字符串不能识别这种类型不足4位的unicode嘛。
// 此时string2unicode需要修改一下即可。
//string转unicode
function string2unicode(str){
    var ret ="";
    var ustr = "";

    for(var i=0; i<str.length; i++){

        var code = str.charCodeAt(i);
        var code16 = code.toString(16);

        if(code < 0xf){
            ustr = "\\u"+"000"+code16;
        }else if(code < 0xff){
            ustr = "\\u"+"00"+code16;
        }else if(code < 0xfff){
            ustr = "\\u"+"0"+code16;
        }else{
            ustr = "\\u"+code16;
        }
        ret +=ustr;
        //ret += "\\u" + str.charCodeAt(i).toString(16);
    }
    return ret;
}
    // var str_u = string2unicode("中国人CN");//"\u4e2d\u56fd\u4eba\u0043\u004e"
    // var str_s = unicode2string(str_u);//中国人CN　
//unicode转String
function unicode2string(unicode){
    return eval("'" + unicode + "'");
}

//~~~~~~~~~~~~~~~~~~~~JavaScript中unicode编码与String互转（三种方法）~~相关~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//~~~~~~~~~~~~~~~~~~~~复制内容到剪贴板（无插件，兼容所有浏览器）~~相关~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// https://blog.csdn.net/sunnyzyq/article/details/85065022
// HTML部分:
// <button onclick="copyToClip(this,'内容')"> Copy </button>
// JS部分:---主要用这个 copyToClip(this,'内容')
/**
 * 复制内容到粘贴板
 * clickObj : 当前点击的对象或其它指定的对象[对象内用来放inpuut或textarea]--javascript原生对象  this 或 document.createElement("input");
 * content : 需要复制的内容
 * message : 复制完后的提示，不传则默认提示"复制成功"
 */
function copyToClip(clickObj, content, message) {
    var hasHitObj = true; // 是否指定点击对象 true:有传入点击对象 ； false:没有传入点击对象
    if(clickObj === null || typeof clickObj !== "object") {
        hasHitObj = false;
    }
    var aux = document.createElement("input");
    // var aux = document.createElement("textarea");
    aux.setAttribute("value", content);
    // ios 点击复制时屏幕下方会出现白屏抖动，仔细看是拉起键盘又瞬间收起
    //  是只读的，就不会拉起键盘了。
    aux.setAttribute('readonly', 'readonly');

    if(!hasHitObj) {
        document.body.appendChild(aux);
    }else{
        clickObj.parentElement.appendChild(aux);
    }

    // 前面加上 input.focus()就行了
    aux.focus();

    //如果是ios端
    // 那如果是移动端 的话，就要兼容IOS，但是依然在iPhone5的10.2的系统中，依然显示复制失败，
    // 由于用户使用率较低，兼容就做到这里，那些用户你们就自己手动复制吧。
    // 下面的两种方法都可以进行复制，因为核心代码就那么几行，先来简单的
    // if(isiOSDevice){
    if(getOS() == 'ios'){
        var obj = aux;
        // 获取元素内容是否可编辑和是否只读
        var editable = obj.contentEditable;
        var readOnly = obj.readOnly;

        // 将对象变成可编辑的
        obj.contentEditable = true;
        obj.readOnly = false;

        // 创建一个Range对象，Range 对象表示文档的连续范围区域，如用户在浏览器窗口中用鼠标拖动选中的区域
        var range = document.createRange();
        //获取obj的内容作为选中的范围
        range.selectNodeContents(obj);

        var selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);

        // obj.setSelectionRange(0, 999999);  //选择范围，确保全选
        obj.setSelectionRange(0, obj.value.length);// 在ios下并没有选中全部内容，我们需要使用另一个方法来选中内容
        //恢复原来的状态
        obj.contentEditable = editable;
        obj.readOnly = readOnly;
    //如果是安卓端
    }else{
        aux.select();
    }

    try{
        // document.execCommand('copy') 之前 先执行一个 document.execCommand('selectAll')就补偿全选的坑了
        if(document.execCommand("copy","false",null)){
            if (message == null) {
                // alert("复制成功");
                message = "复制成功";
            } else{
                // alert(message);
            }

            layerMsg(message, 1, 0.3, 2000, function () {
            });
        }else{
            // alert("复制失败！请手动复制！");
            // err_alert("复制失败！请手动复制！");
            layerMsg("复制失败！请手动复制！", 5, 0.3, 2000, function () {
            });
        }
    }catch(err){
        // alert("复制错误！请手动复制！")
        // err_alert("复制失败！请手动复制！");
        layerMsg("复制失败！请手动复制！", 5, 0.3, 2000, function () {
        });
    }
    if(!hasHitObj) {
        document.body.removeChild(aux);
    }else{
        clickObj.parentElement.removeChild(aux);
    }
}
// 下面是一个比较完整的升级版方法，和插件Clipboard.js一样，不过代码不多，就直接拿来用好了。 这个获取的不是input对象，而是需要复制的内容。
//使用函数
// $("#copy").on("tap",function(){
//     var  val = $("#textAreas").val();
//     Clipboard.copy(this,val);
// });
//定义函数
window.Clipboard = (function(window, document, navigator) {
    var textArea,
        copy;

    // 判断是不是ios端
    function isOS() {
        return navigator.userAgent.match(/ipad|iphone/i);
    }
    //创建文本元素
    function createTextArea(clickObj, text) {
        var hasHitObj = true; // 是否指定点击对象 true:有传入点击对象 ； false:没有传入点击对象
        if(clickObj === null || typeof clickObj !== "object") {
            hasHitObj = false;
        }
        textArea = document.createElement('textArea');
        textArea.value = text;
        // ios 点击复制时屏幕下方会出现白屏抖动，仔细看是拉起键盘又瞬间收起
        //  是只读的，就不会拉起键盘了。
        textArea.setAttribute('readonly', 'readonly');
        if(!hasHitObj) {
            document.body.appendChild(textArea);
        }else{
            clickObj.parentElement.appendChild(textArea);
        }

    }
    //选择内容
    function selectText() {
        var range,
            selection;

        // 前面加上 input.focus()就行了
        textArea.focus();
        if (isOS()) {
            range = document.createRange();
            range.selectNodeContents(textArea);
            selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            // textArea.setSelectionRange(0, 999999);
            textArea.setSelectionRange(0, textArea.value.length);
        } else {
            textArea.select();
        }
    }

//复制到剪贴板
    function copyToClipboard(clickObj, message) {
        var hasHitObj = true; // 是否指定点击对象 true:有传入点击对象 ； false:没有传入点击对象
        if(clickObj === null || typeof clickObj !== "object") {
            hasHitObj = false;
        }
        try{
            if(document.execCommand("copy","false",null)){

                if (message == null) {
                    // alert("复制成功");
                    message = "复制成功";
                }
                // alert("复制成功！");
                layerMsg(message, 1, 0.3, 2000, function () {
                });
            }else{
                // alert("复制失败！请手动复制！");
                layerMsg("复制失败！请手动复制！", 5, 0.3, 2000, function () {
                });
            }
        }catch(err){
            // alert("复制错误！请手动复制！")
            layerMsg("复制失败！请手动复制！", 5, 0.3, 2000, function () {
            });
        }
        if(!hasHitObj) {
            document.body.removeChild(textArea);
        }else{
            clickObj.parentElement.removeChild(textArea);
        }
    }

    copy = function(clickObj,text, message) {
        createTextArea(clickObj,text);
        selectText();
        copyToClipboard(clickObj, message);
    };

    return {
        copy: copy
    };
})(window, document, navigator);
//~~~~~~~~~~~~~~~~~~~~复制内容到剪贴板（无插件，兼容所有浏览器）~~相关~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~获取浏览器信息(类型及系统)~~相关~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// 各主流浏览器
function getBrowser() {
    var u = navigator.userAgent;

    var bws = [{
        name: 'sgssapp',
        it: /sogousearch/i.test(u)
    }, {
        name: 'wechat',
        it: /MicroMessenger/i.test(u)
    }, {
        name: 'weibo',
        it: !!u.match(/Weibo/i)
    }, {
        name: 'uc',
        it: !!u.match(/UCBrowser/i) || u.indexOf(' UBrowser') > -1
    }, {
        name: 'sogou',
        it: u.indexOf('MetaSr') > -1 || u.indexOf('Sogou') > -1
    }, {
        name: 'xiaomi',
        it: u.indexOf('MiuiBrowser') > -1
    }, {
        name: 'baidu',
        it: u.indexOf('Baidu') > -1 || u.indexOf('BIDUBrowser') > -1
    }, {
        name: '360',
        it: u.indexOf('360EE') > -1 || u.indexOf('360SE') > -1
    }, {
        name: '2345',
        it: u.indexOf('2345Explorer') > -1
    }, {
        name: 'edge',
        it: u.indexOf('Edge') > -1
    }, {
        name: 'ie11',
        it: u.indexOf('Trident') > -1 && u.indexOf('rv:11.0') > -1
    }, {
        name: 'ie',
        it: u.indexOf('compatible') > -1 && u.indexOf('MSIE') > -1
    }, {
        name: 'firefox',
        it: u.indexOf('Firefox') > -1
    }, {
        name: 'safari',
        it: u.indexOf('Safari') > -1 && u.indexOf('Chrome') === -1
    }, {
        name: 'qqbrowser',
        it: u.indexOf('MQQBrowser') > -1 && u.indexOf(' QQ') === -1
    }, {
        name: 'qq',
        it: u.indexOf('QQ') > -1
    }, {
        name: 'chrome',
        it: u.indexOf('Chrome') > -1 || u.indexOf('CriOS') > -1
    }, {
        name: 'opera',
        it: u.indexOf('Opera') > -1 || u.indexOf('OPR') > -1
    }];

    for (var i = 0; i < bws.length; i++) {
        if (bws[i].it) {
            return bws[i].name;
        }
    }

    return 'other';
}

// 系统区分
function getOS() {
    var u = navigator.userAgent;
    if (!!u.match(/compatible/i) || u.match(/Windows/i)) {
        return 'windows';
    } else if (!!u.match(/Macintosh/i) || u.match(/MacIntel/i)) {
        return 'macOS';
    } else if (!!u.match(/iphone/i) || u.match(/Ipad/i)) {
        return 'ios';
    } else if (!!u.match(/android/i)) {
        return 'android';
    } else {
        return 'other';
    }
}
//~~~~~~~~~~~~~~~~~~~~获取浏览器信息(类型及系统)~~相关~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// 自己实现一个copy，可以传入deep参数表示是否执行深复制：https://www.cnblogs.com/tracylin/p/5346314.html 也来谈一谈js的浅复制和深复制
//util作为判断变量具体类型的辅助模块
var util = (function(){
    var class2type = {};
    ["Null","Undefined","Number","Boolean","String","Object","Function","Array","RegExp","Date"].forEach(function(item){
        class2type["[object "+ item + "]"] = item.toLowerCase();
    })

    function isType(obj, type){
        return getType(obj) === type;
    }
    function getType(obj){
        return class2type[Object.prototype.toString.call(obj)] || "object";
    }
    return {
        isType:isType,
        getType:getType
    }
})();
// 深度复制对象
// deep参数表示是否执行深复制
function copy(obj,deep){
    //如果obj不是对象，那么直接返回值就可以了
    if(obj === null || typeof obj !== "object"){
        return obj;
    }
    //定义需要的局部变脸，根据obj的类型来调整target的类型
    var i, target = util.isType(obj,"array") ? [] : {},value,valueType;
    for(i in obj){
        value = obj[i];
        valueType = util.getType(value);
        //只有在明确执行深复制，并且当前的value是数组或对象的情况下才执行递归复制
        if(deep && (valueType === "array" || valueType === "object")){
            target[i] = copy(value);
        }else{
            target[i] = value;
        }
    }
    return target;
}

(function() {
    document.write("<!-- 前端模板开始 -->");
    document.write("    <!-- 加载中模板部分 开始-->");
    document.write("<!-- tr 版 -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_loding\">");
    document.write("        <tr><td colspan=\"14\" align=\"center\">信息努力加载中.......<\/td><\/tr>");
    document.write("    <\/script>");
    document.write("<!-- div 版 -->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_data_loding_div\">");
    document.write("    <div class=\"loding\">信息努力加载中.......<\/div>");
    document.write("<\/script>");
    document.write("    <!-- 没有 版 -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_loding_null\">");
    document.write("    <\/script>");
    document.write("        <!-- li 版 -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_loding_li\">");
    document.write("        <li class=\"loding\">信息努力加载中.......<\/li>");
    document.write("    <\/script>");
    document.write("    <!-- 加载中模板部分 结束-->");
    document.write("    <!-- 没有数据记录模板部分 开始-->");
    document.write("<!-- tr 版 -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_empty\">");
    document.write("        <tr><td colspan=\"<%=head_num%>\" align=\"center\">当前没有您要查询的记录！<\/td><\/tr>");
    document.write("    <\/script>");
    document.write("<!-- div 版 -->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_data_empty_div\">");
    document.write("    <div class=\"loding list_empty\">当前没有您要查询的记录！<\/div>");
    document.write("<\/script>");
    document.write("    <!-- 没有内容 版 -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_empty_null\">");
    document.write("    <\/script>");
    document.write("        <!-- li 版 -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_empty_li\">");
    document.write("        <li class=\"loding list_empty\">当前没有您要查询的记录！<\/li>");
    document.write("    <\/script>");
    document.write("    <!-- 没有数据记录模板部分 结束-->");
    document.write("    <!-- 列表分页模板部分 开始-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_page\">");
    document.write("        <div class=\"row\">");
    document.write("                <div class=\"col-xs-12\">");
    document.write("                    <div id=\"dynamic-table_paginate\" class=\"dataTables_paginate paging_simple_numbers\">");
    document.write("                        <ul class=\"pagination\">");
    document.write("                        <\/ul>");
    document.write("                    <\/div>");
    document.write("                <\/div>");
    document.write("        <\/div> ");
    document.write("    <\/script>");
    document.write("    <!-- 列表分页模板部分 结束-->");
    document.write("");
    document.write("");
    document.write("    <!-- 确定+取消弹窗模板部分 开始");
    document.write("    $sure_cancel_data = {");
    document.write("        \'content\':\'确定导出Excel？ \',\/\/提示文字");
    document.write("        \'sure_event\':\'excel_sure();\',\/\/确定");
    document.write("        \'cancel_event\':\'excel_cancel();\',\/\/取消");
    document.write("    };");
    document.write("    -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_sure_cancel\">");
    document.write("        <table>");
    document.write("          <tr>");
    document.write("            <td style=\"width:5px\"  rowspan=\"3\"><\/td>");
    document.write("            <td><img src=\"\/static\/images\/question.jpg\" style=\"height:25px;width:25px;\"><\/td>");
    document.write("            <td>&nbsp;&nbsp;<\/td>");
    document.write("            <td style=\"text-align:left;\"><%=content%><\/td>");
    document.write("          <\/tr>");
    document.write("          <tr>");
    document.write("            <td><\/td>");
    document.write("            <td><\/td>");
    document.write("            <td><br\/>");
    document.write("              <button class=\"btn btn-info butdata m2 sure_submit_btn\" type=\"button\" onclick=\"<%=sure_event%>\">确 定<\/button>&nbsp;&nbsp;&nbsp;&nbsp;");
    document.write("              <button class=\"btn btn-default butdata m2 sure_cancel_btn\" style=\"margin-left:20px;\"  type=\"button\" onclick=\"<%=cancel_event%>\" >取 消<\/button>");
    document.write("            <\/td>");
    document.write("          <\/tr>");
    document.write("        <\/table>");
    document.write("    <\/script>");
    document.write("    <!-- 确定+取消弹窗模板部分 结束-->");
    document.write("");
    document.write("    <!-- error错误弹窗模板部分 开始");
    document.write("    $sure_cancel_data = {");
    document.write("        \'content\':\'***\',\/\/提示文字");
    document.write("    };");
    document.write("    -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_error\">");
    document.write("        <table>");
    document.write("          <tr>");
    document.write("            <td style=\"width:5px\"  rowspan=\"3\"><\/td>");
    document.write("            <td><img src=\"\/static\/images\/that.png\" style=\"height:25px;width:25px;\"><\/td>");
    document.write("            <td>&nbsp;&nbsp;<\/td>");
    document.write("            <td style=\"text-align:left;\"><%=content%><\/td>");
    document.write("          <\/tr>");
    document.write("        <\/table>");
    document.write("    <\/script>");
    document.write("    <!-- error错误弹窗模板部分 结束-->");
    document.write("    <!-- 倒记时关闭弹窗模板部分 开始");
    document.write("    $sure_cancel_data = {");
    document.write("        \'content\':\'***\',\/\/提示文字");
    document.write("        \'sec_num\':10,\/\/默认秒数");
    document.write("        \'icon_name\',\/\/图片名称");
    document.write("    };");
    document.write("    -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_countdown\">");
    document.write("        <table>");
    document.write("          <tr>");
    document.write("            <td style=\"width:5px\"  rowspan=\"3\"><\/td>");
    document.write("            <td  rowspan=\"3\"><img src=\"\/static\/images\/<%=icon_name%>\" style=\"height:25px;width:25px;\"><\/td>");
    document.write("            <td>&nbsp;&nbsp;<\/td>");
    document.write("            <td style=\"text-align:left;\"><%=content%><\/td>");
    document.write("          <\/tr>");
    document.write("          <tr>");
    document.write("            <td>&nbsp;&nbsp;<\/td>");
    document.write("            <td style=\"text-align:left;\">窗口将在<b><span  style=\"color: #F00;\" class=\"show_second\"><%=sec_num%><\/span><\/b>秒后窗口关闭<\/td>");
    document.write("          <\/tr>");
    document.write("        <\/table>");
    document.write("    <\/script>");
    document.write("    <!-- 倒记时关闭弹窗模板部分 结束-->");
    document.write("    <!-- 确认搜索条件值表单模板部分 开始-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_search_sure_form\">");
    document.write("        <form  id=\"<%=search_sure_form%>\" method=\"post\" action=\"#\">");
    document.write("        <%for(var i = 0; i<input_vlist.length;i++){");
    document.write("        var item = input_vlist[i];");
    document.write("        %>");
    document.write("        <input type=\"hidden\" name=\"<%=item.name%>\" value=\"<%=item.value%>\"\/>");
    document.write("        <%}%>");
    document.write("        <\/form>");
    document.write("    <\/script>");
    document.write("    <!-- 确认搜索条件值表单模板部分 结束-->");
    document.write("");
    document.write("    <!-- [省市区\/县]下拉框模板部分 开始-->");
    document.write("    <!-- \/\/遍历json对象的每个key\/value对,p为key{key:val,..}-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_option_list\">");
    document.write("        <%for(var key in option_json){");
    document.write("            %>");
    document.write("            <option value=\"<%=key%>\"><%=option_json[key]%><\/option>");
    document.write("            <%");
    document.write("        }%>");
    document.write("    <\/script>");
    document.write("    <!-- [省市区\/县]下拉框模板部分 结束-->");
    document.write("    <!-- 前端模板结束 -->");
    document.write("");
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_tag_item_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    %>");
    document.write("    <span class=\"tag\">");
    document.write("        <span class=\"tag_txt\" data-id=\"<%=item.id%>\"><%=item.tag_name%><\/span>");
    document.write("        <input type=\"hidden\" name=\"<%=item.id_input_name%>\" value=\"<%=item.id%>\">");
    document.write("        <input type=\"hidden\" name=\"<%=item.tag_input_name%>\" value=\"<%=item.tag_name%>\">");
    document.write("        <i class=\"close\">×<\/i>");
    document.write("    <\/span>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
    document.write("");
    document.write("    <!-- 单选 或 复选  模板部分 开始-->");
    document.write("    <!-- input_class input 的 class 名称 ； 多个用 空格 隔开 -->");
    document.write("    <!-- input_style input 的 style 值 ；  -->");
    document.write("    <!-- input_padstr 可以 填入 id=\"111\" data-aaa=\"8\" ；多个值时，用 空格分隔  -->");
    document.write("    <!-- input_type 类型 值 为 ： radio - 单选； checkbox - 复选 -->");
    document.write("    <!-- input_name 输入框的名称 值  字符串 aaa 或 bbb[]  -->");
    document.write("    <!-- checked_val 当前选中的值 可以是 单个值 【数字】 也可以是数组 ['2'] --注意：数组项 是字符串  -->");
    document.write("    <!-- disabled_val 当前禁用的值  可以是 单个值 【数字】 也可以是数组 ['2'] --注意：数组项 是字符串 -->");
    document.write("    <!-- item_json 选项值对象 遍历json对象的每个key\/value对,p为key{key:val,..}  -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_radio_checkbox_list\">");
    document.write("        <%");
    document.write("         var input_class = input_class || ''; ");
    document.write("         var input_style = input_style || ''; ");
    document.write("         var input_padstr = input_padstr || ''; ");
    document.write("         var input_type = input_type || 'radio'; ");
    document.write("         var input_name = input_name || input_type;");
    document.write("         var checked_val = checked_val || 0;");
    document.write("         var disabled_val = disabled_val || 0;");
    document.write("         var is_bit = true;");
    document.write("         var maxNum = 63;");
    document.write("         var bitNumArr = getBitArr(maxNum);");
    document.write("         for(var tem_key in item_json){");
    document.write("            if(isNaN(tem_key)){");
    document.write("                 is_bit = false;");
    document.write("                 break;");
    document.write("            }");
    document.write("            if(!isBitNumByArr(tem_key, bitNumArr, maxNum)){");
    document.write("                 is_bit = false;");
    document.write("                 break;");
    document.write("            }");
    document.write("         }");
    document.write("         ");
    document.write("        for(var key in item_json){");
    document.write("            %>");
    document.write("            <label>");
    document.write("            <input type=\"<%=input_type%>\"  name=\"<%=input_name%>\"  value=\"<%=key%>\" <%=input_padstr%> ");
    document.write("            <%if( input_class != ''){%>");
    document.write("              class=\"<%=input_class%>\"  ");
    document.write("            <%}%>");
    document.write("            <%if( input_style != ''){%>");
    document.write("              style=\"<%=input_style%>\"  ");
    document.write("            <%}%>");
    document.write("            <%if( ( getDataTypeStr(checked_val) == 'array' && checked_val.indexOf(key) >= 0 ) || ( getDataTypeStr(checked_val) != 'array' && (key == checked_val || (is_bit && (checked_val & key) == key)) ) ){%>");
    document.write("                checked=\"checked\" ");
    document.write("            <%}%>");
    document.write("            <%if( ( getDataTypeStr(disabled_val) == 'array' && disabled_val.indexOf(key) >= 0 ) || ( getDataTypeStr(disabled_val) != 'array' && (key == disabled_val || (is_bit && (disabled_val & key) == key)) ) ){%>");
    document.write("              disabled ");
    document.write("            <%}%>");
    document.write("            \/>");
    document.write("            <%=item_json[key]%>");
    document.write("            <\/label>");
    document.write("            <%");
    document.write("        }%>");
    document.write("    <\/script>");
    document.write("    <!-- 单选 或 复选 模板部分 结束-->");
    document.write("    <!-- 前端模板结束 -->");
    document.write("    <!-- 下拉框  模板部分 开始-->");
    document.write("    <!-- select_class input 的 class 名称 ； 多个用 空格 隔开 -->");
    document.write("    <!-- select_style input 的 style 值 ；  -->");
    document.write("    <!-- select_name 下拉框的名称 值  字符串 aaa 或 bbb[]  -->");
    document.write("    <!-- select_padstr 可以 填入 disabled  multiple id=\"111\" size=\"8\" ；多个值时，用 空格分隔 -->");
    document.write("    <!-- selected_val 当前选中的值 可以是 单个值 【数字】 也可以是数组 ['2'] --注意：数组项 是字符串  -->");
    document.write("    <!-- item_json 选项值对象 遍历json对象的每个key\/value对,p为key{key:val,..}  -->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_select_option_list\">");
    document.write("        <%");
    document.write("         var select_class = select_class || ''; ");
    document.write("         var select_style = select_style || ''; ");
    document.write("         var select_name = select_name || 'select';");
    document.write("         var select_padstr = select_padstr || ''; ");
    document.write("         var selected_val = selected_val || 0;");
    document.write("         var is_bit = true;");
    document.write("         var maxNum = 63;");
    document.write("         var bitNumArr = getBitArr(maxNum);");
    document.write("         for(var tem_key in item_json){");
    document.write("            if(isNaN(tem_key)){");
    document.write("                 is_bit = false;");
    document.write("                 break;");
    document.write("            }");
    document.write("            if(!isBitNumByArr(tem_key, bitNumArr, maxNum)){");
    document.write("                 is_bit = false;");
    document.write("                 break;");
    document.write("            }");
    document.write("         }");
    document.write("         ");
    document.write("            %>");
    document.write("         <select  name=\"<%=select_name%>\" <%=select_padstr%> ");
    document.write("            <%if( select_class != ''){%>");
    document.write("              class=\"<%=select_class%>\"  ");
    document.write("            <%}%>");
    document.write("            <%if( select_style != ''){%>");
    document.write("              style=\"<%=select_style%>\"  ");
    document.write("            <%}%>");
    document.write("            ");
    document.write("         >");
    document.write("        <%");
    document.write("        for(var key in item_json){");
    document.write("            %>");
    document.write("            <option value=\"<%=key%>\" ");
    document.write("            <%if( ( getDataTypeStr(selected_val) == 'array' && selected_val.indexOf(key) >= 0 ) || ( getDataTypeStr(selected_val) != 'array' && (key == selected_val || (is_bit && (selected_val & key) == key)) ) ){%>");
    document.write("                selected  ");
    document.write("            <%}%>");
    document.write("             ><%=item_json[key]%><\/option>");
    // document.write("            <%=item_json[key]%>");
    // document.write("            <\/option>");
    document.write("            <%");
    document.write("        }%>");
    document.write("        <\/select>");
    document.write("    <\/script>");
    document.write("    <!-- 下拉框 模板部分 结束-->");
    document.write("    <!-- 前端模板结束 -->");
}).call();
