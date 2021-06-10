{{--
九张图片上传的引用
需要变量
upload_id 上传图片控件id
submit_url 上传请求地址
$pro_unit_id  生产单元id
site_resources 相关资源图片二维数组 转换好的json
fileupload // 单个上传成功后执行方法 格式 aaa();  或  空白-没有
uploadComplete // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
--}}
@component('component.upfileone.piconejsinituse')
@slot('upload_id')
{{ $upload_id ?? 'myUploader' }}
@endslot
@slot('submit_url')
{{ $submit_url ?? url('api/upload') }}
@endslot
@slot('multipart_params')
{pro_unit_id:'{{ $pro_unit_id ?? 0 }}'}
@endslot
@slot('limitFilesCount')
1
@endslot
@slot('multi_selection')
false
@endslot
@slot('baidu_template_pic_list')
baidu_template_pic_show
@endslot
@slot('piclistJson')
{
"data_list":{{ $site_resources ?? [] }}
}
@endslot
@slot('fileupload')
{{ $fileupload ?? '' }}
@endslot
@slot('uploadComplete')
{{ $uploadComplete ?? '' }}
@endslot
@endcomponent