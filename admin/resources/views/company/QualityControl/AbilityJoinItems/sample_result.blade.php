<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>数据上报---管理后台</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layuiadmin_quality/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layuiadmin_quality/style/admin.css')}}" media="all">
    <style>
    .layui-form-label {
    }
    .gray {
        color:#999;
        line-height: 160%;
    }
    </style>
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-row layui-card-body">

            <form  method="post"  id="addForm" >
                <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>测试结果</legend>
                </fieldset>
                <div  style="padding:25px 0;">
                @foreach ($info['join_item_reslut_info_updata']['items_samples_list'] as $k => $sample_info)
                <div class="layui-form-item" >
                    <!-- <label class="layui-form-label">样品编号： </label> -->
                    <div class="layui-input-block sample_list"  style="padding-left:30px; font-size: 16px; line-height: 180%;" data-sample_one="{{ $sample_info['sample_one'] ?? '' }}">
                        样品编号: <b> {{ $sample_info['sample_one'] ?? '' }} </b> <br >

                        <?php $sample_result_list = $sample_info['sample_result_list'] ?>

                        @foreach ($info['project_submit_items_list'] as $t_k => $submit_info)
                            <?php $key = ($sample_info['id'] ?? '') . '_' . ($submit_info['id'] ?? '') ?>
                            {{ $submit_info['name'] ?? '' }}：<input type="text" value="{{ $sample_result_list[$key]['sample_result'] ?? '' }}" data-name="{{ $submit_info['name'] ?? '' }}" name="sample_result_{{ $sample_info['id'] ?? '' }}_{{ $submit_info['id'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入{{ $submit_info['name'] ?? '' }}" style="width:200px; border:2px solid #888; height: 32px; font-size:16px;  display:inline;" class="layui-input">
                        @endforeach
                        <span style="color:red;">注：这里填写实验数据，不填写样品编号。不需要加单位，如:MPN。</span>
                    </div>
                </div>
                @endforeach
                </div>
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>检测所用仪器</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_instrument_list'] as $k => $instrument_info)
                    <div class="instrument_list">
                        <input type="hidden" name="instrument_id[]" value="{{ $instrument_info['id'] ?? '' }}">
                        <div class="layui-form-item ">
                            <label class="layui-form-label">名称/型号</label>
                            <div class="layui-input-block">
                                <input type="text" name="instrument_model[]" value="{{ $instrument_info['instrument_model'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入名称/型号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">出厂编号</label>
                            <div class="layui-input-block">
                                <input type="text" name="factory_number[]" value="{{ $instrument_info['factory_number'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入出厂编号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">检定日期</label>
                            <div class="layui-input-block">
                                <input type="text" name="check_date[]" value="{{ $instrument_info['check_date'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入检定日期" class="layui-input">
                                <p class="gray">格式如：2020-05-20</p>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">有效期</label>
                            <div class="layui-input-block">
                                <input type="text" name="valid_date[]" value="{{ $instrument_info['valid_date'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入有效期" class="layui-input">
                                <p class="gray">格式如：2020-05-20</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>标准物质</legend>
                </fieldset>
                <p style="text-indent: 2em; color:red; padding-bottom: 15px; ">注：水泥抗压强度（3d）项目，不需要填写此项，如要填写，全部填写数字0即可。</p>
                @foreach ($info['join_item_reslut_info_updata']['results_standard_list'] as $k => $standard_info)
                    <div class="standard_list">
                        <input type="hidden" name="standard_id[]" value="{{ $standard_info['id'] ?? '' }}">
                        <div class="layui-form-item">
                            <label class="layui-form-label">名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="standard_name[]" value="{{ $standard_info['name'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">生产单位</label>
                            <div class="layui-input-block">
                                <input type="text" name="produce_unit[]" value="{{ $standard_info['produce_unit'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入生产单位" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">批号</label>
                            <div class="layui-input-block">
                                <input type="text" name="batch_number[]" value="{{ $standard_info['batch_number'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入批号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">有效期</label>
                            <div class="layui-input-block">
                                <input type="text" name="standard_valid_date[]" value="{{ $standard_info['valid_date'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入有效期" class="layui-input">
                                <p class="gray">格式如：2020-05-20</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>方法依据</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_method_list'] as $k => $method_info)
                    <div class="method_list">
                        <input type="hidden" name="method_id[]" value="{{ $method_info['id'] ?? '' }}">
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <div class="layui-input-block">
                                <textarea name="content[]" placeholder="请输入内容" class="layui-textarea">{{ replace_enter_char($method_info['content'] ?? '',2) }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>资料上传</legend>
                </fieldset>

                <div class="layui-form-item">
                     <p style="text-indent: 2em; color:red; padding-bottom: 15px; ">注：将结果报告表（加盖公章）、检测报告、原始记录等资料扫描成一个PDF上传，上传完请机构再次确认，无误后提交。</p>
                    <label class="layui-form-label"> </label>
                    <div class="layui-input-block">

                        <input type="hidden" name="resource_id" value=""/>

                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <p>请上传文档的一个PDF文件。</p>
                        </div>
                        <div class="row ">
                            <div class="col-xs-6">
                                @component('component.upfileone.piconecode')
                                    @slot('fileList')
                                        large
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
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button  type="button"  class="layui-btn" lay-submit="" lay-filter="demo1"  id="submitBtn">立即提交</button>
                     </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/company/ability_join_item/ajax_save_result_sample') }}";// ajax保存提交数据地址
    var LIST_URL = "{{url('company/ability_join_item')}}";//保存成功后跳转到的地址

    var UPLOAD_PDF_URL = "{{ url('api/company/ability_join_item/up_pdf') }}";//上传pdf地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 上传图片变量
    {{--var FILE_UPLOAD_URL = "{{ url('api/company/upload') }}";// 文件上传提交地址 'your/file/upload/url'--}}
    {{--var PIC_DEL_URL = "{{ url('api/company/upload/ajax_del') }}";// 删除图片url--}}
    {{--var MULTIPART_PARAMS = {pro_unit_id:'0'};// 附加参数	函数或对象，默认 {}--}}
    {{--var LIMIT_FILES_COUNT = 30;//   限制文件上传数目	false（默认）或数字--}}
    {{--var MULTI_SELECTION = true//  是否可用一次选取多个文件	默认 true false--}}
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
    {{--var RESOURCE_LIST = @json($info['join_item_reslut_info_updata']['resource_list'] ?? []) ;--}}
    {{--var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}--}}

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/company/QualityControl/AbilityJoinItems_sample_result.js?134') }}"  type="text/javascript"></script>
@component('component.upfileincludejs')
@endcomponent
</body>
</html>
