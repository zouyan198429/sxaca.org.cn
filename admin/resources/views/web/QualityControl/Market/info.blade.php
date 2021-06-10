<!DOCTYPE html>
<html>
	<head>
        <title>{{ $info['company_name'] ?? '' }}_{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力_{{ $info['city_name'] ?? '' }}检验检测能力</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="keywords" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        <meta name="description" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        @include('web.QualityControl.Market.layout_public.pagehead')
{{--        @include('admin.layout_public.pagehead')--}}
        <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
        <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

	</head>
	<body style="padding:20px; background-color: #fff;">


	<table   class="layui-table table2 tableWidthFixed" style="width: 96%;">
		<tr>
			<th width="120">机构名称:</th>
			<td>{{ $info['company_name'] ?? '' }}</td>
		</tr>
		<tr>
			<th>机构地址:</th>
			<td>{{ $info['addr'] ?? '' }}</td>
		</tr>
		<tr>
			<th>联系人:</th>
			<td>{{ $info['company_contact_name'] ?? '' }}</td>
		</tr>
		<tr>
			<th>联系电话:</th>
			<td>{{ $info['company_contact_mobile'] ?? '' }}</td>
		</tr>
		<tr>
			<th>资质认定编号:</th>
			<td>{{ $info['company_certificate_no'] ?? '' }}</td>
		</tr>
		<tr>
			<th>发证日期：</th>
			<td>{{ $info['ratify_date'] ?? '' }}</td>
		</tr>
		<tr>
			<th>有效日期：</th>
			<td>{{ $info['valid_date'] ?? '' }}</td>
		</tr>
	</table>

	</body>
</html>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
{{--<script src="{{asset('js/common/list.js')}}?2"></script>--}}
<script src="{{ asset('js/web/QualityControl/Market/Info.js') }}?3"  type="text/javascript"></script>
