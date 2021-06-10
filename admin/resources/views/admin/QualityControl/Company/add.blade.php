

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
                <th>单位名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_name" value="{{ $info['company_name'] ?? '' }}" placeholder="请输入单位名称"/>
                </td>
            </tr>
            <tr>
                <th>统一社会信用代码<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_credit_code" value="{{ $info['company_credit_code'] ?? '' }}" placeholder="请输入统一社会信用代码"/>
                </td>
            </tr>
            <tr>
                <th>是否独立法人<span class="must"></span></th>
                <td>
                    <label class="company_is_legal_persion"><input type="checkbox"  name="company_is_legal_persion"  value="1"  @if(isset($info['company_is_legal_persion']) && $info['company_is_legal_persion'] == 1) checked="checked"  @endif />是否独立法人</label><span class="gray">企业类型为非独立法人时请填写主体单位信息</span>
                </td>
            </tr>
            <tr class="company_is_legal_persion_item">
                <th>主体机构统一社会信用代码<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_legal_credit_code" value="{{ $info['company_legal_credit_code'] ?? '' }}" placeholder="请输入主体机构统一社会信用代码"/>
                </td>
            </tr>
            <tr class="company_is_legal_persion_item">
                <th>主体机构<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_legal_name" value="{{ $info['company_legal_name'] ?? '' }}" placeholder="请输入主体机构"/>
                </td>
            </tr>
            <tr>
                <th>所在城市<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="city_id" style="width: 100px;">
                        <option value="">请选择城市</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>企业类型<span class="must">*</span></th>
                <td>
                    <label><input type="radio" name="company_type" value="1" @if (isset($info['company_type']) && $info['company_type'] == 1 ) checked @endif>检验检测机构</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="company_type" value="2" @if (isset($info['company_type']) && $info['company_type'] == 2 ) checked @endif>生产企业内部实验室</label>

                </td>
            </tr>
            <tr>
                <th>企业性质<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="company_prop" style="width: 100px;">
                        <option value="">请选择企业性质</option>
                        @foreach ($companyProp as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCompanyProp) && $defaultCompanyProp == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>通讯地址<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入通讯地址" style="width:600px"/>
                </td>
            </tr>
            <tr>
                <th>邮编<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="zip_code" value="{{ $info['zip_code'] ?? '' }}" placeholder="请输入邮编"/>
                </td>
            </tr>
            <!-- <tr>
                <th>传真<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="fax" value="{{ $info['fax'] ?? '' }}" placeholder="请输入传真"/>
                </td>
            </tr> -->
            <tr>
                <th>企业邮箱<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="email" value="{{ $info['email'] ?? '' }}" placeholder="请输入企业邮箱"/>
                    <p class="gray">用于接收通知等。</p>
                </td>
            </tr>
            <tr>
                <th>法人代表<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_legal" value="{{ $info['company_legal'] ?? '' }}" placeholder="请输入法人代表"/>
                </td>
            </tr>
            <tr>
                <th>营业执照<span class="must">*</span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>一次最多上传1张图片。</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    grid
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
                <th>单位人数<span class="must">*</span></th>
                <td>

                    <select class="wnormal" name="company_peoples_num" style="width: 100px;">
                        <option value="">请选择单位人数</option>
                        @foreach ($companyPeoples as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCompanyPeoples) && $defaultCompanyPeoples == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>所属行业<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="company_industry_id" style="width: 100px;">
                        <option value="">请选择行业</option>
                        @foreach ($industry_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultIndustry) && $defaultIndustry == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>资质认定证书编号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_certificate_no" value="{{ $info['company_certificate_no'] ?? '' }}" placeholder="请输入证书编号"/>
                </td>
            </tr>
            <tr>
                <th>资质认定证书有效起止时间<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wlong ratify_date" name="ratify_date" value="{{ $info['ratify_date'] ?? '' }}" placeholder="请选择批准日期" style="width: 150px;"  readonly="true"/>
                    -
                    <input type="text" class="inp wlong valid_date" name="valid_date" value="{{ $info['valid_date'] ?? '' }}" placeholder="请选择有效期至"  style="width: 150px;" readonly="true"/>
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>实验室地址<span class="must"></span></th>--}}
{{--                <td>--}}
{{--                    <input type="text" class="inp wnormal"  name="laboratory_addr" value="{{ $info['laboratory_addr'] ?? '' }}" placeholder="请输入实验室地址"/>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th>联系人<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_contact_name" value="{{ $info['company_contact_name'] ?? '' }}" placeholder="请输入联系人"/>
                </td>
            </tr>
            <tr>
                <th>联系人手机<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_contact_mobile" value="{{ $info['company_contact_mobile'] ?? '' }}" placeholder="请输入联系人手机"/>
                </td>
            </tr>
            <tr>
                <th>固定电话<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_contact_tel" value="{{ $info['company_contact_tel'] ?? '' }}" placeholder="请输入联系电话"/>
                </td>
            </tr>
            <tr>
                <th>用户名<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="admin_username" value="{{ $info['admin_username'] ?? '' }}" placeholder="请输入用户名"/>

                </td>
            </tr>
            <tr>
                <th>登录密码<span class="must">*</span></th>
                <td>
                    <input type="password"  class="inp wnormal"   name="admin_password" placeholder="登录密码" /><p>修改时，可为空：不修改密码。</p>
                </td>
            </tr>
            <tr>
                <th>确认密码<span class="must">*</span></th>
                <td>
                    <input type="password" class="inp wnormal"     name="sure_password"  placeholder="确认密码"/><p>修改时，可为空：不修改密码。</p>
                </td>
            </tr>
            <tr>
                <th>是否完善资料<span class="must">*</span></th>
                <td>
                    @foreach ($isPerfect as $k=>$txt)
                        <label><input type="radio"  name="is_perfect"  value="{{ $k }}"  @if(isset($defaultIsPerfect) && $defaultIsPerfect == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr style="display: none;">
                <th>会员等级<span class="must">*</span></th>
                <td>
                    @foreach ($companyGrade as $k=>$txt)
                        <label><input type="radio"  name="company_grade"  value="{{ $k }}"  @if(isset($defaultCompanyGrade) && $defaultCompanyGrade == $k) checked="checked"  @endif />{{ $txt }} </label>
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
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/company/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['ratify_date'] ?? '' }}" ;//批准日期
    var END_TIME = "{{ $info['valid_date'] ?? '' }}" ;//有效期至

    // 上传图片变量
    var FILE_UPLOAD_URL = "{{ url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    var PIC_DEL_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除图片url
    var MULTIPART_PARAMS = {pro_unit_id:'0'};// 附加参数	函数或对象，默认 {}
    var LIMIT_FILES_COUNT = 1;//   限制文件上传数目	false（默认）或数字
    var MULTI_SELECTION = false;//  是否可用一次选取多个文件	默认 true false
    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
    var SELF_UPLOAD = true;//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
    var FILE_UPLOAD_METHOD = 'initPic()';// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_UPLOAD_COMPLETE = '';  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_RESIZE = {quuality: 40};
    // resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：
    //     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；
    //     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；
    //     // crop: true,// 是否对图片进行裁剪；
    //     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；
    //     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。
    // },
    var RESOURCE_LIST = @json($info['resource_list'] ?? []) ;
    var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/Company_edit.js') }}?5"  type="text/javascript"></script>
@component('component.upfileincludejs')
@endcomponent
</body>
</html>
