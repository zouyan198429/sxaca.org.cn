
$(function(){
    //提交
    // $(document).on("keypress",".searloc",function(event){
    //     if(event.keyCode == 13) {
    //         var obj = $(this);
    //         searchKey(obj);
    //         return false;
    //     }
    // })
    // initAttr();
    //切换收款账号
    $(document).on("change",'select[name=select_addr]',function(){
        var addr_name = $(this).val();
        console.log('==addr_name=', addr_name);
        addrShorOrHidden(addr_name);
    });
});

function addrShorOrHidden(addr_name) {
    $('.certificate_list').find('tr').each(function () {
        var trObj = $(this);
        var addr = trObj.find('.addr').html() || '';
        console.log('==addr=', addr);
        if(addr == addr_name || addr_name == ''){
            trObj.show();
        }else{
            trObj.hide();
        }
    });
}

function initAttr() {
    $('#data_list').find('tr').each(function () {
        var trObj = $(this);
        var category_name_obj = trObj.find('.category_name');
        if(category_name_obj.length > 0){
            category_name_obj.data('old', category_name_obj.html());
        }

        var project_name_obj = trObj.find('.project_name');
        if(project_name_obj.length > 0){
            project_name_obj.data('old', project_name_obj.html());
        }

        var param_name_obj = trObj.find('.param_name');
        if(param_name_obj.length > 0){
            param_name_obj.data('old', param_name_obj.html());
        }

        var method_name_obj = trObj.find('.method_name');
        if(method_name_obj.length > 0){
            method_name_obj.data('old', method_name_obj.html());
        }

        var limit_range_obj = trObj.find('.limit_range');
        if(limit_range_obj.length > 0){
            limit_range_obj.data('old', limit_range_obj.html());
        }

        var explain_text_obj = trObj.find('.explain_text');
        if(explain_text_obj.length > 0){
            explain_text_obj.data('old', explain_text_obj.html());
        }

        var addr_obj = trObj.find('.addr');
        if(addr_obj.length > 0){
            addr_obj.data('old', addr_obj.html());
        }

        var ratify_date_obj = trObj.find('.ratify_date');
        if(ratify_date_obj.length > 0){
            ratify_date_obj.data('old', ratify_date_obj.html());
        }
    });
}

function strInCount(str, findStr){
    console.log('==str==', str);
    console.log('==findStr==', findStr);
    return str.split(findStr).length - 1;
}

// str 原字符
// oldStr 要替换的字符
// newStr 新的字符
function replaceAllStr(str, oldStr , newStr) {
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
function getNewStr(str) {
    return '<span style="color: red;background-color: #1b6eab;font-weight: bold;">' + str + '</span>';
}
// obj 输入框对象
function searchKey(obj) {

    var key = obj.val();
    // alert('你输入的内容为：' + obj.val());
    if(judge_empty(key)){
        $('#data_list').find('tr').each(function () {
            var trObj = $(this);
            trObj.show();
            var category_name_obj = trObj.find('.category_name');
            if(category_name_obj.length > 0){
                category_name_obj.html(category_name_obj.data('old'));
            }

            var project_name_obj = trObj.find('.project_name');
            if(project_name_obj.length > 0){
                project_name_obj.html(project_name_obj.data('old'));
            }

            var param_name_obj = trObj.find('.param_name');
            if(param_name_obj.length > 0){
                param_name_obj.html(param_name_obj.data('old'));
            }

            var method_name_obj = trObj.find('.method_name');
            if(method_name_obj.length > 0){
                method_name_obj.html(method_name_obj.data('old'));
            }

            var limit_range_obj = trObj.find('.limit_range');
            if(limit_range_obj.length > 0){
                limit_range_obj.html(limit_range_obj.data('old'));
            }

            var explain_text_obj = trObj.find('.explain_text');
            if(explain_text_obj.length > 0){
                explain_text_obj.html(explain_text_obj.data('old'));
            }

            var addr_obj = trObj.find('.addr');
            if(addr_obj.length > 0){
                addr_obj.html(addr_obj.data('old'));
            }

            var ratify_date_obj = trObj.find('.ratify_date');
            if(ratify_date_obj.length > 0){
                ratify_date_obj.html(ratify_date_obj.data('old'));
            }
        });
        return false;
    }
    var is_show = false;
    $('#data_list').find('tr').each(function () {
        var trObj = $(this);
        is_show = false;
        var category_name_obj = trObj.find('.category_name');
        if(category_name_obj.length > 0){
            var category_name = category_name_obj.data('old');
            if(!judge_empty(category_name) && strInCount(category_name, key) > 0){
                is_show = true;
                category_name = replaceAllStr(category_name, key , getNewStr(key))
            }
            category_name_obj.html(category_name);
        }

        var project_name_obj = trObj.find('.project_name');
        if(project_name_obj.length > 0){
            var project_name = project_name_obj.data('old');
            if(!judge_empty(project_name) && strInCount(project_name, key) > 0){
                is_show = true;
                project_name = replaceAllStr(project_name, key , getNewStr(key))
            }
            project_name_obj.html(project_name);
        }

        var param_name_obj = trObj.find('.param_name');
        if(param_name_obj.length > 0){
            var param_name = param_name_obj.data('old');
            if(!judge_empty(param_name) && strInCount(param_name, key) > 0){
                is_show = true;
                param_name = replaceAllStr(param_name, key , getNewStr(key))
            }
            param_name_obj.html(param_name);
        }

        var method_name_obj = trObj.find('.method_name');
        if(method_name_obj.length > 0){
            var method_name = method_name_obj.data('old');
            if(!judge_empty(method_name) && strInCount(method_name, key) > 0){
                is_show = true;
                method_name = replaceAllStr(method_name, key , getNewStr(key))
            }
            method_name_obj.html(method_name);
        }

        var limit_range_obj = trObj.find('.limit_range');
        if(limit_range_obj.length > 0){
            var limit_range = limit_range_obj.data('old');
            if(!judge_empty(limit_range) && strInCount(limit_range, key) > 0){
                is_show = true;
                limit_range = replaceAllStr(limit_range, key , getNewStr(key))
            }
            limit_range_obj.html(limit_range);
        }

        var explain_text_obj = trObj.find('.explain_text');
        if(explain_text_obj.length > 0){
            var explain_text = explain_text_obj.data('old');
            if(!judge_empty(explain_text) && strInCount(explain_text, key) > 0){
                is_show = true;
                explain_text = replaceAllStr(explain_text, key , getNewStr(key))
            }
            explain_text_obj.html(explain_text);
        }

        var addr_obj = trObj.find('.addr');
        if(addr_obj.length > 0){
            var addr = addr_obj.data('old');
            if(!judge_empty(addr) && strInCount(addr, key) > 0){
                is_show = true;
                addr = replaceAllStr(addr, key , getNewStr(key))
            }
            addr_obj.html(addr);
        }

        var ratify_date_obj = trObj.find('.ratify_date');
        if(ratify_date_obj.length > 0){
            var ratify_date = ratify_date_obj.data('old');
            if(!judge_empty(ratify_date) && strInCount(ratify_date, key) > 0){
                is_show = true;
                ratify_date = replaceAllStr(ratify_date, key , getNewStr(key))
            }
            ratify_date_obj.html(ratify_date);
        }
        console.log('啊顶顶顶顶顶');
        if(is_show){
            trObj.show();
            console.log('显示行');
        }else{
            trObj.hide();
            console.log('隐藏行');
        }
    });
}
