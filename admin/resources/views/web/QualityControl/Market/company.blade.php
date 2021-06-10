<!DOCTYPE html>
<html lang="en">
	<head>
        <title>陕西省市场监督管理局_陕西省检验检测机构信息查询_{{ $key_str ?? '' }}市场监督管理局_陕西{{ $key_str ?? '' }}市场监督管理局第{{ $page ?? '' }}页_检验检测能力</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="keywords" content="陕西省市场监督管理局,陕西省检验检测机构信息查询,{{ $key_str ?? '' }}市场监督管理局,陕西{{ $key_str ?? '' }}市场监督管理局第{{ $page ?? '' }}页,检验检测能力" />
        <meta name="description" content="陕西省市场监督管理局,陕西省检验检测机构信息查询,{{ $key_str ?? '' }}市场监督管理局,陕西{{ $key_str ?? '' }}市场监督管理局第{{ $page ?? '' }}页,检验检测能力" />
        @include('web.QualityControl.Market.layout_public.pagehead')
        <link href="{{asset('static/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />
		<style>
			/* 在线客服 */
			html {overflow-x:hidden;}
			.scrollsidebar{position:absolute; z-index:999; top:350px;right:0}
			.side_content{width:124px; height:auto; overflow:hidden; float:left;  }
			.side_content .side_list {width:124px; overflow:hidden;}
			.show_btn{ width:0; height:112px; overflow:hidden; margin-top:50px; float:left; cursor:pointer;}
			.show_btn span { color: #fff;}
			a.close_btn {width:16px;height:24px;cursor:pointer;  margin:0; color: #fff; position: absolute; top:5px; right: 5px; display: block;  }				
			.side_title { position: relative;}
			.side_title,.side_bottom,.close_btn,.show_btn {background:#4695DD;}
			.side_title {height:32px; width: 100%; color: #fff;} 
			.side_title h2 { line-height: 22px; margin:0; font-size: 14px; width:5em; text-align: center;  position: absolute; left:5px; top:5px;}
			.side_center { border:2px solid #4695DD; padding:10px 0;}
			.side_center p { text-align: left; padding-left:12px; line-height: 16px; font-size: 14px; color: #666;}
			.close_btn { float:right; display:block; width:21px; height:16px; margin:16px 10px 0 0; _margin:16px 5px 0 0;}
			 .show_btn , .side_blue .show_btn { background-color: #4695DD; text-align: center; padding-top:12px} 
			 
		</style>
	</head>
	<body style="background-color: #F0F6FC;">
        @include('web.QualityControl.Market.layout_public.header')
        @include('web.QualityControl.Market.layout_public.search')
		<!-- <div class="keyword">
			<div class="wrap">
				<p>关键词：<strong>{{ $key_str ?? '' }}</strong></p>
				<div class="c"></div>
			</div>
		</div> -->
		<div class="list-wrap">
			<div class="wrap">
				<div class="list content">
					<table class=" comlist">
						<colgroup>
						    <col>
						    <col width="180">
							<col width="120">
							<col width="130">
							<col width="115">
						    <col width="115">
                            <col width="115">
                            <col width="115">
                            <col width="115">
						</colgroup>
						<thead>
							<tr>
								<th>机构名称</th>
								<th>资质认定证书编号</th>
								<th>机构信息</th>
								<th>资质认定证书附表</th>
								<th>自我声明	</th>
{{--							<th>监督检查信息	</th>--}}
								<th>行政处罚</th>
                                <th>能力验证</th>
                                <th>监督检查</th>
                                <th>其它</th>
							</tr>
						</thead>
                        @foreach ($company_list as $k => $v)
						<tr>
							<td class="com-name" >
								<p class="tl">
                                {{ $v['company_name'] ?? '' }}
								</p>
							</td>
							<td class="content-info">
								{{ $v['company_certificate_no'] ?? '' }}
							</td>
							<td>
 								 <!-- <p>公司地址：<span>{{ $v['addr'] ?? '' }}</span></p>
 								<p>联系人：<span>{{ $v['company_contact_name'] ?? '' }}</span></p>
 								<p>联系电话：<span>{{ $v['company_contact_mobile'] ?? '' }}/{{ $v['company_contact_tel'] ?? '' }}</span></p>  -->
                                <a href="javascript:void(0);" onclick="otheraction.browseInfo('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="机构信息" > <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
							</td>
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.schedule('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="资质认定证书附表">
									<img src="{{asset('quality/Market/images/details12.png')}}" alt="" />
								</a>
								<!-- ({{ $v['extend_info']['schedule_num'] ?? '0' }}) -->
							</td>
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.company_statement_num('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="自我声明公告"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
								({{ $v['extend_info']['statement_num'] ?? '0' }})<!--  -->
							</td>
{{--							<td>--}}
{{--                                <a href="javascript:void(0);" onclick="otheraction.company_supervise('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="监督检查信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>--}}
{{--								({{ $v['extend_info']['supervise_num'] ?? '0' }})<!--  -->--}}
{{--							</td>--}}
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.company_punish_num('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="行政处罚信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
								({{ $v['extend_info']['punish_num'] ?? '0' }})<!--  -->
							</td>
                            <td>
                                <a href="javascript:void(0);" onclick="otheraction.company_ability('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="能力验证信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
                                ({{ $v['extend_info']['ability_result_num'] ?? '0' }})<!--  -->
                            </td>
                            <td>
                                <a href="javascript:void(0);" onclick="otheraction.company_inspect('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="监督检查信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
                                ({{ $v['extend_info']['inspect_num'] ?? '0' }})<!--  -->
                            </td>
                            <td>
                                <a href="javascript:void(0);" onclick="otheraction.company_news('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="其它信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
                                ({{ $v['extend_info']['news_num'] ?? '0' }})<!--  -->
                            </td>
						</tr>
                        @endforeach

					</table>
                    <div class="mmfoot"><!--
                        <div class="mmfleft"></div> -->
                        <div class="pagination">
                            {!! $pageInfoLink ?? ''  !!}
                        </div>
                    </div>

				</div>
 

				<div class="c"></div>

			</div>
		</div>

		<div class="c"></div>
        @include('web.QualityControl.Market.layout_public.footer')
		
		<div class="scrollsidebar" id="scrollsidebar">
			<!-- 在线客服 -->
		  <div class="side_content">
		    <div class="side_list">
		      <div class="side_title"><h2>在线客服</h2><a title="隐藏" class="close_btn">X</a></div>
		      <div class="side_center">	 
		          <p>马治成</p>
		          <p>029-87290790</p>
				  <p>李伟</p>
				  <p>029-86138898</p> 		       
		      </div> 
		    </div>
		  </div>
		  <div class="show_btn"><span>在线客服</span></div>
		</div>
		
		
	</body>
</html>


<script>
	/*
 在线客服
	 */
	
	!(function() {
	    var serviceOnline = (function() {
	        var sideContent = document.querySelector(".side_content");
	        var show_btn = document.querySelector(".show_btn");
	        var close_btn = document.querySelector(".close_btn");
	        var timer = null;
	
	        //悬浮QQ匀速移动
	        var startMove = function(argument) {
	            var scrollsidebar = document.getElementById("scrollsidebar");
	            clearInterval(timer);
	            timer = setInterval(function() {
	                var speed = (argument - scrollsidebar.offsetTop) / 4;
	                speed = speed > 0 ? Math.ceil(speed) : Math.floor(speed);
	                if (argument == scrollsidebar.offsetTop) {
	                    clearInterval(timer);
	                } else {
	                    scrollsidebar.style.top = scrollsidebar.offsetTop + speed + "px";
	                }
	            }, 20);
	        };
	
	        //鼠标移动
	        var scrollMove = function() {
	            window.onscroll = window.onload = function() {
	                var scrollsidebar = document.getElementById("scrollsidebar");
	                var scrolltop =
	                    document.body.scrollTop || document.documentElement.scrollTop;
	                startMove(
	                    parseInt(
	                        (document.documentElement.clientHeight -
	                            scrollsidebar.offsetHeight) /2 +scrolltop
	                    )
	                );
	            };
	        };
	
	        //悬浮QQ显示
	        var slideShow = function() {
	            if (!show_btn) return false;
	            show_btn.addEventListener(
	                "click",
	                function() {
	                    show_btn.style.width = 0;
	                    sideContent.style.width = "154px";
	                },
	                false
	            );
	        };
	
	        //悬浮QQ隐藏
	        var slideClose = function() {
	            if (!close_btn) return false;
	            close_btn.addEventListener(
	                "click",
	                function() {
	                    console.log(this);
	                    sideContent.style.width = 0;
	                    show_btn.style.width = "25px";
	                },
	                false
	            );
	        };
	
	        //返回出来的方法
	        return {
	            init: function() {
	                scrollMove();
	                slideClose();
	                slideShow();
	            }
	        };
	    })();
	
	    //初始化
	    serviceOnline.init();
	})();
</script>

<script type="text/javascript">

    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SEARCH_COMPANY_URL = "{{url('web/market/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    // var SEARCH_COMPANY_URL = "{ {url('jigou/list/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址

    // 机构信息-查看
    var COMPANY_INFO_URL = "{{url('web/market/company/info')}}/";//显示页面地址前缀 + id

    var SCHEDULE_SHOW_URL = "{{url('jigou/info')}}/";// “{ { url('web/market/company_new_schedule')}}";// "{ { url('admin/company_new_schedule/show')}}/";//查看企业能力附表

    var COMPANY_SUPERVISE_EDIT_URL = "{{ url('web/market/company_supervise/info/0') }}"; // 监督检查信息修改/添加url


    var COMPANY_STATEMENT_URL = "{{ url('web/market/company_statement')}}/";// 查看机构自我声明
    var COMPANY_PUNISH_URL = "{{ url('web/market/company_punish')}}/";// 查看机构处罚
    var COMPANY_ABILITY_URL = "{{ url('web/market/company_ability')}}/";// 查看能力验证
    var COMPANY_INSPECT_URL = "{{ url('web/market/company_inspect')}}/";// 查看监督检查
    var COMPANY_NEWS_URL = "{{ url('web/market/company_news')}}/";// 查看企业其它【新闻】

</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/Market/search.js') }}?4"  type="text/javascript"></script>

<script src="{{ asset('/js/web/QualityControl/Market/company.js') }}?9"  type="text/javascript"></script>
