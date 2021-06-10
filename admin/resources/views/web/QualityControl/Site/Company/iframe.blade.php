
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{{--    <title>副理事长单位|会员中心-陕西省质量认证认可协会-陕西省质量认证认可协会-陕西质量认证咨询中心</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />--}}
{{--    <meta name="author" content="陕西省质量认证认可协会-陕西质量认证咨询中心">--}}
{{--    <meta name="keywords" content="会员中心-陕西省质量认证认可协会"><meta name="description" content="陕西质量认证认可协会---官网----陕西省质量认证咨询中心-陕西省质量认证认可协会">--}}

    <title>陕西质量认证咨询中心_{{ $key_str ?? '' }}资质认定获证机构_陕西{{ $key_str ?? '' }}资质认定获证机构第{{ $page ?? '' }}页_检验检测能力</title>
    <meta name="keywords" content="陕西质量认证咨询中心,{{ $key_str ?? '' }}资质认定获证机构,陕西{{ $key_str ?? '' }}资质认定获证机构第{{ $page ?? '' }}页,检验检测能力" />
    <meta name="description" content="陕西质量认证咨询中心,{{ $key_str ?? '' }}资质认定获证机构,陕西{{ $key_str ?? '' }}资质认定获证机构第{{ $page ?? '' }}页,检验检测能力" />
    @include('web.QualityControl.Site.layout_public.pagehead')


    <style type="text/css">

        .tablist {
            width: 120px;
            float: left;
            margin-top:20px;
        }
        .tablist li {
            margin-bottom: 5px;
            width: 100%;
        }
        .tabbd {
            width: 1030px;
            float: right;
        }
        .tabbd li {
            width: 340px;
            overflow: hidden;
        }
        .Search_lebet {
            width: 490px;
            float: right;
            margin-top:-46px;
        }
        .el-input 		{
            width: 400px;
            border:1px solid #c6cede;
            height: 34px;
            border-image: none;
            background: #FFF;
            vertical-align: top;
            overflow: hidden;
            display: inline-block;
            font: 16px arial;
            margin: 0;
            zoom: 1;
            float: left;
        }
        .el-input_inner {
            width: 400px;
            height: 25px;
            margin: 4px 0px 0px 8px;
            padding: 0px;
            background: transparent none repeat scroll 0% 0%;
            border: 0px none;
            outline: 0px none;
        }
        .el-button  {
            width: 80px;
            background: #55a7e3;
            color: #fff;
            font-size: 14px;
            height: 34px;
            padding: 0;
            border: 0 none;
            cursor: pointer;
        }
        .pb20 {
            padding:20px;
        }

    </style>

</head>

<body  class="body_article" style="background-color: #fff;">
{{--@include('web.QualityControl.Site.layout_public.header')--}}
{{--@include('web.QualityControl.Site.layout_public.companyHeader')--}}
{{--@include('web.QualityControl.Site.layout_public.search')--}}

<div class="indtitle-1">
    <div class="wrap hd"><h2>会员服务</h2><span>Members</span></div>

</div>
<div class="wrap">
    <div  class="Search_lebet">
        <div  class="el-input"><input name="company_name" type="text" autocomplete="off" placeholder="会员状态查询：请输入单位全称查询" class="el-input_inner"></div>
        <button  type="button" class="el-button el-button--primary" id="searchBtn"><i class="el-icon-search"></i><span>搜索</span></button>
    </div>
</div>
<div class="wrap tc pb20 search_company_info" style="display: none;">
{{--    <p class="tc red">--}}
{{--        该单位非协会会员！或单位名称有误。--}}
{{--    </p>--}}
    <p>
        <span class="red search_company_name">	西安某某公司</span> 为协会<span class="red company_grade_text"> 理事单位</span>
        <span class="company_grade_block">会员起止时间：<span class="red company_grade_date">2018-12-18 至 2024-12-18</span></span>
    </p>
</div>


<div id="huiyuan">
    <div class="wrap">
        <div class="tablist">
            <ul>
                @foreach ($companyGrade as $k=>$txt)
                    <li @if(isset($defaultCompanyGrade) && $defaultCompanyGrade == $k)  class="on" @endif>{{ $txt }}单位</li>
                @endforeach
            </ul>
        </div>
        <div class="tabbd">
            @foreach ($companyGrade as $k => $txt)
            <ul @if(!isset($defaultCompanyGrade) || $defaultCompanyGrade != $k) style="display: none"  @endif>
                <?php
                $companyList = $companyGradeList[$k] ?? [];
                $i = 1;
                ?>
                @foreach ($companyList as $t_k => $info)
                <li class="n{{ $i ?? '' }}">
                    <a
                        href="{{url('web/company/info/' . $info['id'])}}"
                        target="_blank"
                        title="{{ $info['company_name'] ?? '' }}"
                    >{{ $info['company_name'] ?? '' }}</a
                    >
                </li>
                <?php
                    $i++;
                ?>
                @endforeach
            </ul>
            @endforeach
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function (e) {
            $("#huiyuan").slide({
                titCell: ".tablist li",
                mainCell: ".tabbd",
                trigger: "mouseover",
            });
        });
    </script>
</div>
<!--主体内容 结束-->


<div class="c"></div>

{{--@include('web.QualityControl.Site.layout_public.footer')--}}

</body>
</html>

<!--gotop start-->
@include('web.QualityControl.Site.layout_public.footerjs')
<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    // var SEARCH_COMPANY_URL = "{ {url('web/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var COMPANY_NAME_SEARCH_URL = "{{ url('api/web/company/ajax_search') }}";
</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/Site/Company_iframe.js') }}?4"  type="text/javascript"></script>

