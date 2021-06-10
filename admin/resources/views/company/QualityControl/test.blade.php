

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
{{--    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css?88')}}" media="all">--}}
{{--    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css?8')}}" media="all">--}}
    <style>
    .gray {
    color: #999;
    margin-top:10px;
     }
     .btn {
        height: 32px;
     }
     .tip1 {
        text-indent: 2em;

     }
     .k20 {
        height: 20px;
        clear: both;
        width: 100%;
     }
    </style>
</head>
<body>

<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
    <p class="red tip1" ><i class="ace-icon fa fa-info-circle  bigger-60"></i> 请上传资质认定的全部附表，每次上传一个文档的PDF版本。</p>
    <table class="table1">

        <tr>
            <th>PDF文件上传<span class="must">*</span></th>
            <td>

                <div  class="uploader" data-ride="uploader" data-url="your/file/upload/url" id="myUploader">
                    <div class="uploader-message text-center">
                        <div class="content"></div>
                        <button type="button" class="close">×</button>
                    </div>
                    <div class="uploader-files file-list file-list-grid"></div>
                    <div>
                        <hr class="divider">
                        <div class="uploader-status pull-right text-muted"></div>
                        <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
                        <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
                    </div>
                </div>

                <span>请上传pdf格式的文档</span>
            </td>
        </tr>
{{--        <tr>--}}
{{--            <th>word文件上传<span class="must">*</span></th>--}}
{{--            <td>--}}
{{--                <span class="file_name"></span>--}}
{{--                <input type="hidden" name="resource_id" value="">--}}
{{--                <button type="button" class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>--}}
{{--                <div style="display:none;" ><input type="file" data-file_type="doc" class="import_file img_input"></div>--}}{{--导入file对象--}}
{{--                <span>请上传doc格式的文档</span>--}}
{{--                <p class="gray">word文件内容要和pdf文件的内容保持一致!</p>--}}
{{--            </td>--}}
{{--        </tr>--}}
        <tr>
            <th> </th>
            <td>
            <div class="k20"></div>
            <button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
        </tr>

    </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/company/company_new_schedule/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/company_new_schedule')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('company/company/select')}}";// 选择所属企业

    var IMPORT_EXCEL_URL = "{{ url('api/company/company_new_schedule/import') }}";//上传文件地址

    var UPLOAD_WORD_URL = "{{ url('api/company/company_new_schedule/up_word') }}";//上传word地址
    var UPLOAD_PDF_URL = "{{ url('api/company/company_new_schedule/up_pdf') }}";//上传pdf地址
    var UPLOAD_IMG_URL = "{{ url('api/company/company_new_schedule/up_img') }}";//上传img地址

    $(function(){
        $('#myUploader').uploader({
            url: UPLOAD_PDF_URL,
            lang: 'zh_cn',// 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
            file_data_name:'photo',//   文件域在表单中的名称  默认 'file'
            onUploadFile: function(file) {
                console.log('上传成功', file);
            }
        });
        //提交
        $(document).on("click","#submitBtn",function(){

            // 判断是否上传图片
            var uploader = $('#myUploader').data('zui.uploader');
            var files = uploader.getFiles();
            console.log('files', files);
            var filesCount = files.length;
            console.log('filesCount', filesCount);
            return false;
        });
    });
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
{{--<script src="{{ asset('/js/company/QualityControl/CompanyNewSchedule_edit.js') }}?4"  type="text/javascript"></script>--}}

<link href="{{asset('dist/lib/uploader/zui.uploader.min.css') }}" rel="stylesheet">
<script src="{{asset('dist/lib/uploader/zui.uploader.min.js') }}"></script>{{--此文件引用一次就可以了--}}

</body>
</html>
