

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
    {{--  本页单独使用 --}}
    <script src="{{asset('dist/lib/kindeditor/kindeditor.min.js')}}"></script>
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>课程名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="course_name" value="{{ $info['course_name'] ?? '' }}" placeholder="请输入课程名称"/>
                </td>
            </tr>
            <tr>
                <th>图片<span class="must">*</span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>一次最多上传1张图片。</p>
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
                            {{--
                            <input type="file" class="form-control" value="">
                            --}}
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <th>简要概述<span class="must"></span></th>
                <td>
                    <textarea name="explain_remarks" placeholder="请输入简要概述" class="layui-textarea">{{ replace_enter_char($info['explain_remarks'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>详细内容<span class="must">*</span></th>
                <td>
                    <textarea class="kindeditor" name="course_content" rows="15" id="doc-ta-1" style=" width:770px;height:400px;">{!!  htmlspecialchars($info['course_content'] ?? '' )   !!}</textarea>
                </td>
            </tr>
			<tr>
			    <th>收费标准(会员)<span class="must">*</span></th>
			    <td>
			        <input type="text" class="inp wnormal"  name="price_member" value="{{ $info['price_member'] ?? '' }}" placeholder="请输入收费标准(会员)"  onkeyup="numxs(this) " onafterpaste="numxs(this)" />
			        元/人
			    </td>
			</tr>
			
			<tr>
			    <th>收费标准(非会员)<span class="must">*</span></th>
			    <td>
			        <input type="text" class="inp wnormal"  name="price_general" value="{{ $info['price_general'] ?? '' }}" placeholder="请输入收费标准(非会员)"  onkeyup="numxs(this) " onafterpaste="numxs(this)" />
			        元/人
			    </td>
			</tr>
			<tr>
				<th><hr></th>
				<td>
					<hr>
				</td>
			</tr>
            <tr>
                <th>收款帐号<span class="must">*</span></th>
                <td>
                    @foreach ($pay_config_kv as $k=>$txt)
                        <label><input type="radio"  name="pay_config_id"  value="{{ $k }}"  @if(isset($defaultPayConfig) && $defaultPayConfig == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                    <p style="color: gray;">
                        课程必须设置收款账号信息，班级也可以再次设置，班级设置优先！
                    </p>
                </td>
            </tr>
            <tr>
                <th>收款开通类型<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($payMethod as $k=>$txt)
                        <label><input type="checkbox"  name="pay_method[]"  value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod > 0 && ($defaultPayMethod & $k) == $k) checked="checked"  @endif @if(isset($info['allow_pay_method']) && ($info['allow_pay_method'] & $k) <=0 ) disabled   @endif/>{{ $txt }} </label> <br />
                    @endforeach
                </td>
            </tr>
           
            <tr>
                <th>发票开票模板<span class="must">*</span></th>
                <td>
                    @foreach ($invoice_template_kv as $k=>$txt)
                        <label><input type="radio"  name="invoice_template_id"  value="{{ $k }}"  @if(isset($defaultInvoiceTemplate) && $defaultInvoiceTemplate == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>发票商品项目模板<span class="must">*</span></th>
                <td>
                    @foreach ($invoice_project_template_kv as $k=>$txt)
                        <label><input type="radio"  name="invoice_project_template_id"  value="{{ $k }}"  @if(isset($defaultInvoiceProjectTemplate) && $defaultInvoiceProjectTemplate == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>上架状态<span class="must">*</span></th>
                <td>
                    @foreach ($statusOnline as $k=>$txt)
                        <label><input type="radio"  name="status_online"  value="{{ $k }}"  @if(isset($defaultStatusOnline) && $defaultStatusOnline == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
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

    var SAVE_URL = "{{ url('api/admin/course/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/course')}}";//保存成功后跳转到的地址

    var PAY_CONFIG_INFO_URL = "{{ url('api/admin/order_pay_config/ajax_info') }}";// ajax获得支付方式详情记录地址

    // 文件上传相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/course/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/admin/course/up_file') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/course/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

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
    {{--var RESOURCE_LIST = @json($info['resource_list'] ?? []) ;--}}
    {{--var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}--}}
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/Course_edit.js') }}?1"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
