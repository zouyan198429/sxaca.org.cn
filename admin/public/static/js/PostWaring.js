$(function(){
    var obj = {};
    obj.postProcessList = function(){
        $.ajax({
            'type':'POST',
            'url':'/delivery/delivery/waitPost',
            'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
            //'data':orderProcessData,
            'dataType':'json',
            'success':function(ret){
                if(ret.apistatus){
                    $('.wait_post').css({"display":"inline-block"});
                }
            },'error':function(res){

            }
        });
    };

    setInterval(obj.postProcessList,600000);
})
