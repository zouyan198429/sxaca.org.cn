<!DOCTYPE html>
<html>
	<head>
        <title>陕西省市场监督管理局_陕西省检验检测机构信息查询_陕西省检验检测机构信息管理平台_检验检测能力</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="keywords" content="陕西省市场监督管理局,陕西省检验检测机构信息查询,陕西省检验检测机构信息管理平台,检验检测能力" />
        <meta name="description" content="陕西省市场监督管理局,陕西省检验检测机构信息查询,陕西省检验检测机构信息管理平台,检验检测能力" />

        @include('web.QualityControl.Market.layout_public.pagehead')
		<style type="text/css">
		   a:link{text-decoration: none; color:#444; font-size: 18px; font-family: 微软雅黑;}
		   a:visited{ color:#333;}
		   a:hover{text-decoration: underline; color:#FF0000; font-size: 19px;}
		   a:active{text-decoration: blink; color: #FF6600;} 
		.content {

			background: none;
		}
		.div12
		{
			position: relative;
		    width: 300px;
		    height: 130px; 
			text-align: center;
			line-height: 130px;
		    background-color: 	#FFFFFF;
		    box-shadow: 5px 5px 5px grey; 
			float: left;
			margin-right: 30px;
		}
		 </style>
	</head>
	<body style="background-color: #F0F6FC;">
        @include('web.QualityControl.Market.layout_public.header')
	<div class="content">
		<div class="wrap">
			<div  style="padding-top:100px;">
				<div class="div12">
					<a href="http://qts.cnca.cn/qts/" target="_blank">检验检测统计直报系统</a>
				</div>
				<div class="div12">
					<a href="http://113.140.67.203:1291" target="_blank">行政审批企业上报系统</a>
				</div>
			</div>

		</div>
	</div>



        @include('web.QualityControl.Market.layout_public.footer')
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

