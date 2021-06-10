

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <p class="red tip1" ><i class="ace-icon fa fa-info-circle  bigger-60"></i> 请上传资质认定的全部附表，每次上传一个文档的PDF版本。</p>
        <table class="table1">

            <tr  @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>文档类型<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="type_id" style="width: 100px;">
                        <option value="">请选择文档类型</option>
                        @foreach ($type_ids as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultTypeId) && $defaultTypeId == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <th>普通文件列表<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="resource_id_pdf" value=""/>

                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传一个图片格式的文件</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            <img src="http://qualitycontrol.admin.cunwo.net/resource/company/52/images/2020/09/02/202009021631158ee74791b682c949.png" id="img_id_show" />
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    common
                                @endslot
                                @slot('upload_id')
                                    myUploaderCommon
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                    {{--                <span>请上传pdf格式的文档</span>--}}
                </td>
            </tr>
            <tr>
                <th>使用大号文件列表<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="resource_id_pdf" value=""/>

                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传1-5个excel格式的文件</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    large
                                @endslot
                                @slot('upload_id')
                                    myUploaderLarge
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                    {{--                <span>请上传pdf格式的文档</span>--}}
                </td>
            </tr>
            <tr>
                <th>使用网格文件列表<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="resource_id_pdf" value=""/>

                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传1-5个pdf格式的文件</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    grid
                                @endslot
                                @slot('upload_id')
                                        myUploaderGrid
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                    {{--                <span>请上传pdf格式的文档</span>--}}
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>PDF文件上传<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <span class="file_name"></span>--}}
{{--                    <input type="hidden" name="resource_id_pdf" value="">--}}
{{--                    <button type="button" class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>--}}
{{--                    <div style="display:none;" ><input type="file" data-file_type="pdf" class="import_file img_input"></div>--}}{{--导入file对象--}}
{{--                    <span>请上传pdf格式的文档</span>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <th>word文件上传<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <span class="file_name"></span>--}}
{{--                    <input type="hidden" name="resource_id" value="">--}}
{{--                    <button type="button" class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>--}}
{{--                    <div style="display:none;" ><input type="file" data-file_type="doc" class="import_file img_input"></div>--}}{{--导入file对象--}}
{{--                    <span>请上传doc格式的文档</span>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/company_new_schedule/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company_new_schedule')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    var IMPORT_EXCEL_URL = "{{ url('api/admin/company_new_schedule/import') }}";//上传文件地址

    // 文件上传相关的
    var UPLOAD_COMMON_URL = "{{ url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{{ url('api/admin/company_new_schedule/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/admin/company_new_schedule/up_excel') }}";//上传excel地址
    var UPLOAD_GRID_URL = "{{ url('api/admin/company_new_schedule/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    var RESOURCE_LIST_COMMON = @json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_GRID = @json($info['resource_list'] ?? []) ;


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
        'myUploaderCommon':{
            'files_type': 0,// 0 图片文件 1 其它文件
            'operate_auth': 1 |  4,// 2 | 操作权限 1 查看 ；2 下载 ；4 删除
            'icon':'file-image',// 非图片时文件显示的图标--自定义的初始化用，重上传【不管】的会自动生成。 可以参考 common.js 中的变量 FILE_MIME_TYPES file-o  默认
            'file_upload_url': UPLOAD_COMMON_URL,// "{ { url('api/admin/upload') }}",// 文件上传提交地址 'your/file/upload/url'
            'file_down_url': DOWN_FILE_URL,// 文件下载url--自定义格式化数据时可用
            'pic_del_url' : DEL_FILE_URL,// 删除图片url
            'del_fun_pre' : 'common_del',// 自定义的删除前的方法名，返回要加入删除时的参数对象,无参数 ，需要时自定义方法
            'multipart_params' : {pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
            'lang' : 'zh_cn', // 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
            'file_data_name' : 'photo', //	文件域在表单中的名称	默认 'file'
            'checkbox_name' : 'resource_id[]',// 上传后文件id的复选框名称
            'limit_files_count' : 1,//   限制文件上传数目	false（默认）或数字
            'mulit_selection' : false,//  是否可用一次选取多个文件	默认 true false
            'auto_upload' : true,//  当选择文件后立即自动进行上传操作 true / false
            'upload_file_filters' : {
                // 只允许上传图片或图标（.ico）
                mime_types: commonaction.getMineTypes(['pic']),// FILE_MIME_TYPES.pic.mime_types, 下标配置   如 ['pic','pdf',...]
                // 最大上传文件为 2MB
                max_file_size: '4mb',
                // 不允许上传重复文件
                // prevent_duplicates: true
            },
            'baidu_tem_pic_list' : 'baidu_template_upload_pic',// 上传列表格式数的百度数据格式化模型id   baidu_template_upload_pic
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
            'pic_list_json': {'data_list': RESOURCE_LIST_COMMON },// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
           // 不使用了，会自动格式化 pic_list_json 来 匹配  'static_files':@ json($info['static_files'] ?? [])// 初始静态文件对象数组 ； self_upload = false 使用； 格式 [{remoteData:{id:197}, name: 'zui.js', size: 216159, url: 'http://openzui.com'},...]
        },
        'myUploaderLarge':{
            'files_type': 1,// 0 图片文件 1 其它文件
            'operate_auth':  2 | 4,// 1 | 操作权限 1 查看 ；2 下载 ；4 删除
            'icon':'file-excel',// 非图片时文件显示的图标--自定义的初始化用，重上传【不管】的会自动生成。 可以参考 common.js 中的变量 FILE_MIME_TYPES file-o  默认
            'file_upload_url': UPLOAD_LARGE_URL,// "{ { url('api/admin/upload') }}",// 文件上传提交地址 'your/file/upload/url'
            'file_down_url': DOWN_FILE_URL,// 文件下载url--自定义格式化数据时可用
            'pic_del_url' : DEL_FILE_URL,// 删除图片url
            'del_fun_pre' : 'large_del',// 自定义的删除前的方法名，返回要加入删除时的参数对象,无参数 ，需要时自定义方法
            'multipart_params' : {pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
            'lang' : 'zh_cn', // 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
            'file_data_name' : 'photo', //	文件域在表单中的名称	默认 'file'
            'checkbox_name' : 'large_id[]',// 上传后文件id的复选框名称
            'limit_files_count' : 5,//   限制文件上传数目	false（默认）或数字
            'mulit_selection' : true,//  是否可用一次选取多个文件	默认 true false
            'auto_upload' : false,//  当选择文件后立即自动进行上传操作 true / false
            'upload_file_filters' : {
                // 只允许上传图片或图标（.ico）
                mime_types: commonaction.getMineTypes(['excel']),// FILE_MIME_TYPES.excel.mime_types, 下标配置   如 ['pic','pdf',...]
                // 最大上传文件为 2MB
                max_file_size: '100mb',
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
            'pic_list_json': {'data_list': RESOURCE_LIST_LARGE },// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
            // 不使用了，会自动格式化 pic_list_json 来 匹配  'static_files':@ json($info['static_files'] ?? [])// 初始静态文件对象数组 ； self_upload = false 使用； 格式 [{remoteData:{id:197}, name: 'zui.js', size: 216159, url: 'http://openzui.com'},...]
        },
        'myUploaderGrid':{
            'files_type': 1,// 0 图片文件 1 其它文件
            'operate_auth': 1 | 2,//  | 4,// 操作权限 1 查看 ；2 下载 ；4 删除
            'icon':'file-pdf',// 非图片时文件显示的图标--自定义的初始化用，重上传【不管】的会自动生成。 可以参考 common.js 中的变量 FILE_MIME_TYPES file-o  默认
            'file_upload_url': UPLOAD_GRID_URL,// "{ { url('api/admin/upload') }}",// 文件上传提交地址 'your/file/upload/url'
            'file_down_url': DOWN_FILE_URL,// 文件下载url--自定义格式化数据时可用
            'pic_del_url' : DEL_FILE_URL,// 删除图片url
            'del_fun_pre' : 'grid_del',// 自定义的删除前的方法名，返回要加入删除时的参数对象,无参数 ，需要时自定义方法
            'multipart_params' : {pro_unit_id:'0'},// 附加参数	函数或对象，默认 {}
            'lang' : 'zh_cn', // 界面语言 默认情况下设置为空值，会从浏览器 <html lang=""> 属性上获取语言设置，但有也可以手动指定为以下选项：'zh_cn'：简体中文；'zh_tw'：繁体中文；
            'file_data_name' : 'photo', //	文件域在表单中的名称	默认 'file'
            'checkbox_name' : 'grid_id[]',// 上传后文件id的复选框名称
            'limit_files_count' : 5,//   限制文件上传数目	false（默认）或数字
            'mulit_selection' : true,//  是否可用一次选取多个文件	默认 true false
            'auto_upload' : false,//  当选择文件后立即自动进行上传操作 true / false
            'upload_file_filters' : {
                // 只允许上传图片或图标（.ico）
                mime_types: commonaction.getMineTypes(['pdf']),// FILE_MIME_TYPES.pdf.mime_types,下标配置   如 ['pic','pdf',...]
                // 最大上传文件为 2MB
                max_file_size: '100mb',
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
            'pic_list_json': {'data_list': RESOURCE_LIST_GRID },// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
            // 不使用了，会自动格式化 pic_list_json 来 匹配  'static_files':@ json($info['static_files'] ?? [])// 初始静态文件对象数组 ； self_upload = false 使用； 格式 [{remoteData:{id:197}, name: 'zui.js', size: 216159, url: 'http://openzui.com'},...]
        }
    };
    // 上传图片变量
    {{--var FILE_UPLOAD_URL = "{{ url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'--}}
    {{--var PIC_DEL_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除图片url--}}
    {{--var MULTIPART_PARAMS = {pro_unit_id:'0'};// 附加参数	函数或对象，默认 {}--}}
    {{--var LIMIT_FILES_COUNT = 1;//   限制文件上传数目	false（默认）或数字--}}
    {{--var MULTI_SELECTION = false;//  是否可用一次选取多个文件	默认 true false--}}
    {{--var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf--}}
    {{--var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。--}}
    {{--var SELF_UPLOAD = true;//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮--}}
    {{--var FILE_UPLOAD_METHOD = 'initPic()';// 单个上传成功后执行方法 格式 aaa();  或  空白-没有--}}
    {{--var FILE_UPLOAD_COMPLETE = '';  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有--}}
    {{--var FILE_RESIZE = {quuality: 40};--}}
    {{--// resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：--}}
    {{--//     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；--}}
    {{--//     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；--}}
    {{--//     // crop: true,// 是否对图片进行裁剪；--}}
    {{--//     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；--}}
    {{--//     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。--}}
    {{--// },--}}
    {{--var RESOURCE_LIST = @ json($info['resource_list'] ?? []) ;--}}
    {{--var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}--}}
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/CompanyNewSchedule_test.js') }}?7"  type="text/javascript"></script>

@component('component.upfileincludejsmany')
@endcomponent
{{--<link href="{{asset('dist/lib/uploader/zui.uploader.min.css') }}" rel="stylesheet">--}}
{{--<script src="{{asset('dist/lib/uploader/zui.uploader.min.js') }}"></script>--}}{{--此文件引用一次就可以了--}}
</body>
</html>


