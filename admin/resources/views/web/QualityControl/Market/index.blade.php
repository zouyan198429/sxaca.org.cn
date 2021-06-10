<!DOCTYPE html>
<html lang="en">
	<head>
        <title>陕西省市场监督管理局_陕西省检验检测机构信息查询_陕西省检验检测机构信息管理平台_检验检测能力</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="keywords" content="陕西省市场监督管理局,陕西省检验检测机构信息查询,陕西省检验检测机构信息管理平台,检验检测能力" />
        <meta name="description" content="陕西省市场监督管理局,陕西省检验检测机构信息查询,陕西省检验检测机构信息管理平台,检验检测能力" />
        @include('web.QualityControl.Market.layout_public.pagehead')
	</head>
	<body id="indexbody">
        @include('web.QualityControl.Market.layout_public.header-i')

{{--    @include('web.QualityControl.Market.layout_public.search')    这里写新的内容--}}
		<div class="btngroup">
			<a href="{{ url('web/market/company') }}" class="butStyle" > <img src="{{asset('quality/Market/images/xyhzc.png')}}" alt=""> 机构信息查询</a>
			<a href="{{ url('web/market/platform_notices') }}" class="butStyle" > <img src="{{asset('quality/Market/images/xyhzc.png')}}" alt=""> 通知公告</a>
			<a href="{{ url('web/market/platform_down_files') }}" class="butStyle" > <img src="{{asset('quality/Market/images/xyhzc.png')}}" alt=""> 表格下载</a>
			<a href="{{ url('web/market/link') }}" target="_blank" class="butStyle" > <img src="{{asset('quality/Market/images/qydl.png')}}" alt=""> 相关链接</a> 
		</div>
        <div class="foot-i" style="color: #fff;">
			 <script type="text/javascript" src="https://v1.cnzz.com/z_stat.php?id=1279411543&web_id=1279411543"></script>
		</div>
	</body>
</html>

<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var SEARCH_COMPANY_URL = "{{url('jigou/list/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/Market/search.js') }}?1"  type="text/javascript"></script>

