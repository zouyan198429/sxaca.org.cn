

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
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
                <th>发票模板名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="template_name" value="{{ $info['template_name'] ?? '' }}" placeholder="请输入发票模板名称"/>
                </td>
            </tr>
            <tr>
                <th>是否合并开票<span class="must">*</span></th>
                <td>
                    @foreach ($mergeGoods as $k=>$txt)
                        <label><input type="radio"  name="merge_goods"  value="{{ $k }}"  @if(isset($defaultMergeGoods) && $defaultMergeGoods == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>开启状态<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>项目名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xmmc" value="{{ $info['xmmc'] ?? '' }}" placeholder="请输入项目名称"/>
                    <p>
                        (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
                        <br/>
                        如：*非学历教育服务*培训费
                    </p>
                </td>
            </tr>
            <tr>
                <th>是否拼接商品名称<span class="must">*</span></th>
                <td>
                    @foreach ($appendName as $k=>$txt)
                        <label><input type="radio"  name="append_name"  value="{{ $k }}"  @if(isset($defaultAppendName) && $defaultAppendName == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>商品编码<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="spbm" value="{{ $info['spbm'] ?? '' }}" placeholder="请输入商品编码"/>
                    <p>
                        (商品编码为税务总局颁发的19位税控编码)；如：3070201020000000000
                    </p>
                </td>
            </tr>
            <tr>
                <th>自行编码<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="zxbm" value="{{ $info['zxbm'] ?? '' }}" placeholder="请输入自行编码"/>
                    <p>
                        (一般不建议使用自行编码)
                    </p>
                </td>
            </tr>
            <tr>
                <th>规格型号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="ggxh" value="{{ $info['ggxh'] ?? '' }}" placeholder="请输入规格型号"/>
                </td>
            </tr>
            <tr>
                <th>计量单位<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="dw" value="{{ $info['dw'] ?? '' }}" placeholder="请输入计量单位"/>
                </td>
            </tr>
            <tr>
                <th>税率<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sl" value="{{ $info['sl'] ?? '' }}" placeholder="请输入税率"  onkeyup="numxs(this) " onafterpaste="numxs(this)" />
                    <p>例1%为0.01</p>
                </td>
            </tr>
            <tr>
                <th>优惠政策标识<span class="must">*</span></th>
                <td>
                    @foreach ($yhzcbs as $k=>$txt)
                        <label><input type="radio"  name="yhzcbs"  value="{{ $k }}"  @if(isset($defaultYhzcbs) && $defaultYhzcbs == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>零税率标识<span class="must">*</span></th>
                <td>
                    @foreach ($lslbs as $k=>$txt)
                        <label><input type="radio"  name="lslbs"  value="{{ $k }}"  @if(isset($defaultLslbs) && $defaultLslbs == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>增值税特殊管理<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="zzstsgl" value="{{ $info['zzstsgl'] ?? '' }}" placeholder="请输入增值税特殊管理"/>
                    <p>
                        如果【零税率标识】为【 1：免税】时，此项必填，具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
                    </p>
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

    var SAVE_URL = "{{ url('api/admin/invoice_project_template/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/invoice_project_template')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/InvoiceProjectTemplate_edit.js') }}?4"  type="text/javascript"></script>
</body>
</html>
