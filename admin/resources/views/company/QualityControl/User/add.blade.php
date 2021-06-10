

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
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">

            <tr>
                <th>姓名<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="real_name" value="{{ $info['real_name'] ?? '' }}" placeholder="请输入姓名"/>
                </td>
            </tr>
            <tr>
                <th>性别<span class="must">*</span></th>
                <td  class="layui-input-block">
                    <label><input type="radio" name="sex" value="1" @if (isset($info['sex']) && $info['sex'] == 1 ) checked @endif>男</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="sex" value="2" @if (isset($info['sex']) && $info['sex'] == 2 ) checked @endif>女</label>
                </td>
            </tr>
            <tr>
                <th>证件照<span class="must"></span></th>
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
                <th>邮箱<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="email" value="{{ $info['email'] ?? '' }}" placeholder="请输入邮箱"  />
                </td>
            </tr>

            <tr>
                <th>手机<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="mobile" value="{{ $info['mobile'] ?? '' }}" placeholder="请输入手机"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>QQ\微信<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="qq_number" value="{{ $info['qq_number'] ?? '' }}" placeholder="请输入QQ\微信" />
                </td>
            </tr>
            <tr>
                <th>身份证号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="id_number" value="{{ $info['id_number'] ?? '' }}" placeholder="请输入身份证号"  />
                </td>
            </tr>
            <tr>
                <th>职位<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="position_name" value="{{ $info['position_name'] ?? '' }}" placeholder="请输入职位"  />
                </td>
            </tr>
            <tr>
                <th>所在城市<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="city_id">
                        <option value="">请选择城市</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>通讯地址<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入地址"  />
                </td>
            </tr>
            <tr>
                <th>角色<span class="must"></span></th>
                <td  class="seledRoleNumIds">
                    @foreach ($roleNum as $k=>$txt)
                        <label><input type="checkbox"  name="role_nums[]"  value="{{ $k }}"  @if(isset($defaultRoleNum) && (($defaultRoleNum & $k) == $k)) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr id="tr_sign">
                <th>签字范围<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sign_range" value="{{ $info['sign_range'] ?? '' }}" placeholder="请输入签字范围"  />
                    <label><input type="checkbox"  name="sign_is_food"  value="1"  @if(isset($info['sign_is_food']) && ($info['sign_is_food'] == 1)) checked="checked"  @endif /> 食品 </label>
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

    var SAVE_URL = "{{ url('api/company/user/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/user')}}";//保存成功后跳转到的地址
    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "companyQualityControlUseredit";

    // 文件上传相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/course/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/company/user/up_file') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/course/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('company/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/company/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/company/QualityControl/User_edit.js') }}?2"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
