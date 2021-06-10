

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

<div class="layui-fluid">
  <div class="layui-row">
      <div class="layui-col-md12">
			<div class="layui-card">


				<div class="mm">
					<form class="am-form am-form-horizontal" method="post"  id="addForm" onsubmit="return false;">
						<input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
						<input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
						<input type="hidden" name="company_id" value="{{ $company_id ?? 0 }}"/>
						<table class="table1">
							<tr>
								<th>企业名称<span class="must">*</span></th>
								<td>
									{{ $company_name ?? '' }}
								</td>
							</tr>
							<tr>
								<td colspan="2"  class="staff_td">
									<table class="table2">
										<thead>
										<tr>
											<th>订单号</th>
											<th>订单类型</th>
											<th>商品数量</th>
											<th>实收金额</th>
										</tr>
										</thead>
										<tbody class="data_list   baguetteBoxOne gallery" >
										@foreach ($data_list as $k => $order_info)
										   <tr>
											   <td>{{ $order_info['order_no'] ?? '' }}</td>
											   <td>{{ $order_info['order_type_text'] ?? '' }}</td>
											   <td>{{ $order_info['total_amount'] ?? '' }}</td>
											   <td>￥{{ $order_info['check_price'] ?? '' }}</td>
										   </tr>
										@endforeach
										</tbody>

									</table>

								</td>
							</tr>
							<tr >
								<th>发票抬头<span class="must"></span></th>
								<td>
									<span>
										<button class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.addBuyer(this)">新加抬头</button>
									</span>
									<span id="invoice_buyer_list">
									@foreach ($invoice_buyer_kv as $k=>$txt)
											<label id="invoice_buyer_{{ $k }}">
												<input type="radio"  name="invoice_buyer_id"  value="{{ $k }}"  @if(isset($defaultInvoiceBuyer) && $defaultInvoiceBuyer == $k) checked="checked"  @endif />{{ $txt }}
												<a href="javascript:void(0);" onclick="otheraction.showInvoice({{ $k }})">查看</a>
											</label>
									@endforeach
									</span>
								</td>
							</tr>
							<tr>
								<th> </th>
								<td><button class="btn btn-l wnormal"  id="submitBtn" >开电子发票</button></td>
							</tr>

						</table>
					</form>
				</div>
</div>
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

    var SAVE_URL = "{{ url('api/company/orders/ajax_invoices_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/orders')}}";//保存成功后跳转到的地址

    var ADD_INVOICE_BUYER_URL = "{{ url('company/invoice_buyer/add/0') }}"; //添加企业抬头url
    var INFO_INVOICE_BUYER_URL = "{{ url('company/invoice_buyer/info/') }}/"; //详情企业抬头url
</script>
<script src="{{ asset('/js/company/QualityControl/Orders_invoices.js') }}?12"  type="text/javascript"></script>
</body>
</html>
