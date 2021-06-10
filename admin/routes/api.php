<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

*/
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->post ('user/register', 'App\Api\Controllers\UserController@register');// 测试

    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        // $api->post('decode', 'AccountController@decode');
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之前在这里写api
        // $api->post('login', 'AccountController@login');
        $api->get('users/{id}', 'UserController@show');
    });
    $api->group(["namespace" => "App\Http\Controllers"], function ($api) {
        // 接口文档swaggler测试
        $api->get('show','OASController@show');
        $api->get('hello','OASController@hello');

        $api->get('/test', 'IndexController@test');// 测试
        // jwt测试
        $api->post('login', 'ApiJWTController@login');
        $api->post('register', 'ApiJWTController@register');
        $api->post('testaa', 'ApiJWTController@testaa');
        $api->post('testbb', 'ApiJWTController@testbb');

        //$api->group(['middleware' => 'auth.jwt'], function () {
        //    $api->get('logout', 'ApiJWTController@logout');
        //    $api->get('usera', 'ApiJWTController@getAuthUser');
        //
        //    $api->get('products', 'ProductController@index');
        //    $api->get('products/{id}', 'ProductController@show');
        //    $api->post('products', 'ProductController@store');
        //    $api->put('products/{id}', 'ProductController@update');
        //    $api->delete('products/{id}', 'ProductController@destroy');
        //});
        // 原文链接：https://blog.csdn.net/qq_37788558/article/details/91886363
        // 然后在标头请求中添加“Authorization：Bearer {token}”
        //$api->group(['prefix' => 'auth'], function () {
        //    $api->post('login', 'Auth\JwtAuthController@login');
        //    $api->post('logout', 'Auth\JwtAuthController@logout');
        //    $api->post('refresh', 'Auth\JwtAuthController@refresh');
        //    $api->post('me', 'Auth\JwtAuthController@me');
        //});

        // 文件上传 any(
        // $api->post('file/upload', 'IndexController@upload');
        $api->post('upload', 'UploadController@index');
        // $api->post('upload/test', 'UploadController@test');
        // excel
        $api->get('excel/test','ExcelController@test');
        $api->get('excel/export','ExcelController@export'); // 导出
        $api->get('excel/import','ExcelController@import'); // 导入
        $api->get('excel/import_test','ExcelController@import_test'); // 导入 - 测试

        // ------数据查看人员后台

        // 验证码 -- ok
//        $api->get('expert/ajax_captcha', 'Expert\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('expert/ajax_captcha_verify', 'Expert\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('expert/ajax_captcha', 'Expert\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('expert/ajax_captcha_verify', 'Expert\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        //// 登陆
        $api->any('expert/ajax_login', 'Expert\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('expert/ajax_login_sms', 'Expert\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('expert/ajax_password_save', 'Expert\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('expert/ajax_info_save', 'Expert\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        $api->any('expert/ajax_getTableUpdateTime', 'Expert\QualityControl\IndexController@ajax_getTableUpdateTime');// 获得模块表的最新更新时间

        // 企业帐号管理
        $api->any('expert/company/ajax_alist', 'Expert\QualityControl\CompanyController@ajax_alist');//ajax获得列表数据
//        $api->any('expert/company/ajax_del', 'Expert\QualityControl\CompanyController@ajax_del');// 删除
//        $api->post('expert/company/ajax_save', 'Expert\QualityControl\CompanyController@ajax_save');// 新加/修改
        $api->post('expert/company/ajax_get_child', 'Expert\QualityControl\CompanyController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('expert/company/ajax_get_areachild', 'Expert\QualityControl\CompanyController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('expert/company/ajax_import_staff','Expert\QualityControl\CompanyController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('expert/company/import', 'Expert\QualityControl\CompanyController@import');// 导入excel
        $api->post('expert/company/ajax_get_ids', 'Expert\QualityControl\CompanyController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//        $api->any('expert/company/ajax_open', 'Expert\QualityControl\CompanyController@ajax_open');// 审核操作(通过/不通过)
//        $api->post('expert/company/ajax_frozen', 'Expert\QualityControl\CompanyController@ajax_frozen');// 操作(冻结/解冻)

        // 能力验证
        $api->any('expert/abilitys/ajax_alist', 'Expert\QualityControl\AbilitysController@ajax_alist');//ajax获得列表数据
//        $api->post('expert/abilitys/ajax_del', 'Expert\QualityControl\AbilitysController@ajax_del');// 删除
//        $api->post('expert/abilitys/ajax_save', 'Expert\QualityControl\AbilitysController@ajax_save');// 新加/修改
        $api->post('expert/abilitys/ajax_get_child', 'Expert\QualityControl\AbilitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('expert/abilitys/ajax_get_areachild', 'Expert\QualityControl\AbilitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('expert/abilitys/ajax_import_staff','Expert\QualityControl\AbilitysController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('expert/abilitys/import', 'Expert\QualityControl\AbilitysController@import');// 导入excel
        $api->post('expert/abilitys/ajax_get_ids', 'Expert\QualityControl\AbilitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//        $api->post('expert/abilitys/ajax_save_publish', 'Expert\QualityControl\AbilitysController@ajax_save_publish');// 修改公布时间类型
        //****************************************************************************
        // 能力验证管理

        // 能力验证--报名管理--参加单位
        $api->any('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_alist', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_alist');//ajax获得列表数据
//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_del', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_del');// 删除
//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_save', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_save');// 新加/修改
//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_get_child', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_get_areachild', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_import_staff','Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/import', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@import');// 导入excel
//        $api->post('expert/abilitys_admin/{ability_id}/ability_join_items/ajax_get_ids', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证结果--报名管理--参加单位
        $api->any('expert/abilitys_admin/{ability_id}/ability_join_items_results/ajax_alist', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_alist');//ajax获得列表数据
        $api->any('expert/abilitys_admin/{ability_id}/ability_join_items_results/ajax_save', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_save');// 新加/修改
        $api->any('expert/abilitys_admin/{ability_id}/ability_join_items_results/ajax_save_sample', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_save_sample');// 新加/修改--取样

        $api->any('expert/abilitys_admin/{ability_id}/ability_join_items_results/ajax_save_dissatisfied', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_save_dissatisfied');// 已领样，未上传数据的--可以手动直接判断为不满意
        //****************************************************************************

        // 订单支付相关的
//        $api->any('orderPay/pay', 'WX\OrderPayController@pay');// 订单付款
//        $api->any('orderPay/refund', 'WX\OrderPayController@refund');// 订单退款
//        $api->any('orderPay/bond', 'WX\OrderPayController@bond');// 支付保证金
//        $api->any('orderPay/recharge', 'WX\OrderPayController@recharge');// 充值

        // 微信支付相关的
        $api->any('pay/wechat/unifiedorderByNo', 'Pay\WeChatController@unifiedorderByNo');// 统一下单--支付

        $api->any('pay/wechat/unifiedorder', 'Pay\WeChatController@unifiedorder');// 统一下单
        $api->any('pay/wechat/wechatNotify/{pay_key}', 'Pay\WeChatController@wechatNotify');// 支付结果通知--回调
        $api->any('pay/wechat/refundOrder', 'Pay\WeChatController@refundOrder');// 退单
        $api->any('pay/wechat/refundNotify', 'Pay\WeChatController@refundNotify');// 退款结果通知--回调
        $api->any('pay/wechat/sweepCodePayNotify', 'Pay\WeChatController@sweepCodePayNotify');// 扫码支付通知

        $api->any('pay/wechat/operateRefundByNo', 'Pay\WeChatController@operateRefundByNo');// 退款--手动查询退单结果并操作记录

        $api->any('pay/wechat/test', 'Pay\WeChatController@test');// 统一下单



        // 支付宝相关的
        $api->any('pay/alipay/authRedirect', 'Pay\AlipayController@authRedirect');// 授权回调地址
        $api->any('pay/alipay/alipayNotify', 'Pay\AlipayController@alipayNotify');// 支付结果通知--回调

        // ------后台

        // 验证码 -- ok
//        $api->get('admin/ajax_captcha', 'Admin\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('admin/ajax_captcha_verify', 'Admin\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('admin/ajax_captcha', 'Admin\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('admin/ajax_captcha_verify', 'Admin\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码 -- ok
        $api->any('admin/ajax_send_mobile_vercode', 'Admin\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('admin/ajax_mobile_code_verify', 'Admin\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
        $api->any('admin/testAPI', 'Admin\QualityControl\IndexController@testAPI');// 签名接口测试
        $api->any('admin/ajax_login', 'Admin\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('admin/ajax_login_sms', 'Admin\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('admin/ajax_password_save', 'Admin\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('admin/ajax_info_save', 'Admin\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        $api->any('admin/ajax_getTableUpdateTime', 'Admin\QualityControl\IndexController@ajax_getTableUpdateTime');// 获得模块表的最新更新时间

        // 上传图片
        $api->post('admin/upload', 'Admin\QualityControl\UploadController@index');
        $api->any('admin/upload/ajax_del', 'Admin\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 系统管理员
        $api->any('admin/staff/ajax_alist', 'Admin\QualityControl\StaffController@ajax_alist');//ajax获得列表数据
        $api->any('admin/staff/ajax_del', 'Admin\QualityControl\StaffController@ajax_del');// 删除
        $api->post('admin/staff/ajax_save', 'Admin\QualityControl\StaffController@ajax_save');// 新加/修改
        $api->post('admin/staff/ajax_get_child', 'Admin\QualityControl\StaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/staff/ajax_get_areachild', 'Admin\QualityControl\StaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/staff/ajax_import_staff','Admin\QualityControl\StaffController@ajax_import'); // 导入员工
        $api->post('admin/staff/ajax_sms_send', 'Admin\QualityControl\StaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/staff/import', 'Admin\QualityControl\StaffController@import');// 导入excel
        $api->post('admin/staff/ajax_get_ids', 'Admin\QualityControl\StaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/staff/ajax_open', 'Admin\QualityControl\StaffController@ajax_open');// 审核操作(通过/不通过)
        $api->any('admin/staff/ajax_frozen', 'Admin\QualityControl\StaffController@ajax_frozen');// 操作(冻结/解冻)

        // 专家
        $api->any('admin/expert/ajax_alist', 'Admin\QualityControl\ExpertController@ajax_alist');//ajax获得列表数据
        $api->any('admin/expert/ajax_del', 'Admin\QualityControl\ExpertController@ajax_del');// 删除
        $api->post('admin/expert/ajax_save', 'Admin\QualityControl\ExpertController@ajax_save');// 新加/修改
        $api->post('admin/expert/ajax_get_child', 'Admin\QualityControl\ExpertController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/expert/ajax_get_areachild', 'Admin\QualityControl\ExpertController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/expert/ajax_import_staff','Admin\QualityControl\ExpertController@ajax_import'); // 导入员工
        $api->post('admin/expert/ajax_sms_send', 'Admin\QualityControl\ExpertController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/expert/import', 'Admin\QualityControl\ExpertController@import');// 导入excel
        $api->post('admin/expert/ajax_get_ids', 'Admin\QualityControl\ExpertController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/expert/ajax_open', 'Admin\QualityControl\ExpertController@ajax_open');// 审核操作(通过/不通过)
        $api->any('admin/expert/ajax_frozen', 'Admin\QualityControl\ExpertController@ajax_frozen');// 操作(冻结/解冻)

        // 第三方服务商
        $api->any('admin/third_service/ajax_alist', 'Admin\QualityControl\ThirdServiceController@ajax_alist');//ajax获得列表数据
        $api->any('admin/third_service/ajax_del', 'Admin\QualityControl\ThirdServiceController@ajax_del');// 删除
        $api->post('admin/third_service/ajax_save', 'Admin\QualityControl\ThirdServiceController@ajax_save');// 新加/修改
        $api->post('admin/third_service/ajax_get_child', 'Admin\QualityControl\ThirdServiceController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/third_service/ajax_get_areachild', 'Admin\QualityControl\ThirdServiceController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/third_service/ajax_import_staff','Admin\QualityControl\ThirdServiceController@ajax_import'); // 导入员工
        $api->post('admin/third_service/ajax_sms_send', 'Admin\QualityControl\ThirdServiceController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/third_service/import', 'Admin\QualityControl\ThirdServiceController@import');// 导入excel
        $api->post('admin/third_service/ajax_get_ids', 'Admin\QualityControl\ThirdServiceController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/third_service/ajax_open', 'Admin\QualityControl\ThirdServiceController@ajax_open');// 审核操作(通过/不通过)
        $api->any('admin/third_service/ajax_frozen', 'Admin\QualityControl\ThirdServiceController@ajax_frozen');// 操作(冻结/解冻)

        // 企业帐号管理
        $api->any('admin/company/ajax_alist', 'Admin\QualityControl\CompanyController@ajax_alist');//ajax获得列表数据
        $api->any('admin/company/ajax_del', 'Admin\QualityControl\CompanyController@ajax_del');// 删除
        $api->post('admin/company/ajax_save', 'Admin\QualityControl\CompanyController@ajax_save');// 新加/修改
        $api->post('admin/company/ajax_get_child', 'Admin\QualityControl\CompanyController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company/ajax_get_areachild', 'Admin\QualityControl\CompanyController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company/ajax_import_staff','Admin\QualityControl\CompanyController@ajax_import'); // 导入员工
        $api->post('admin/company/ajax_sms_send', 'Admin\QualityControl\CompanyController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company/import', 'Admin\QualityControl\CompanyController@import');// 导入excel
        $api->post('admin/company/ajax_get_ids', 'Admin\QualityControl\CompanyController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/company/ajax_open', 'Admin\QualityControl\CompanyController@ajax_open');// 审核操作(通过/不通过)
        $api->post('admin/company/ajax_frozen', 'Admin\QualityControl\CompanyController@ajax_frozen');// 操作(冻结/解冻)

        // 个人帐号管理
        $api->any('admin/user/ajax_alist', 'Admin\QualityControl\UserController@ajax_alist');//ajax获得列表数据
        $api->any('admin/user/ajax_del', 'Admin\QualityControl\UserController@ajax_del');// 删除
        $api->post('admin/user/ajax_save', 'Admin\QualityControl\UserController@ajax_save');// 新加/修改
        $api->post('admin/user/ajax_get_child', 'Admin\QualityControl\UserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/user/ajax_get_areachild', 'Admin\QualityControl\UserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/user/ajax_import_staff','Admin\QualityControl\UserController@ajax_import'); // 导入员工
        $api->post('admin/user/ajax_sms_send', 'Admin\QualityControl\UserController@ajax_sms_send');// 短信模板发送短信

        $api->any('admin/user/import', 'Admin\QualityControl\UserController@import');// 导入excel
        $api->post('admin/user/ajax_get_ids', 'Admin\QualityControl\UserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/user/ajax_role', 'Admin\QualityControl\UserController@ajax_role');// 角色审核操作(通过/不通过)
        $api->any('admin/user/ajax_sign', 'Admin\QualityControl\UserController@ajax_sign');// 授权人审核操作(通过/不通过)
        $api->any('admin/user/ajax_open', 'Admin\QualityControl\UserController@ajax_open');// 审核操作(通过/不通过)
        $api->post('admin/user/ajax_frozen', 'Admin\QualityControl\UserController@ajax_frozen');// 操作(冻结/解冻)

        $api->post('admin/user/up_file', 'Admin\QualityControl\UserController@up_file');// 上传文件地址

        // 选民组表
        $api->any('admin/voter_group/ajax_alist', 'Admin\QualityControl\VoterGroupController@ajax_alist');//ajax获得列表数据
        $api->post('admin/voter_group/ajax_del', 'Admin\QualityControl\VoterGroupController@ajax_del');// 删除
        $api->post('admin/voter_group/ajax_save', 'Admin\QualityControl\VoterGroupController@ajax_save');// 新加/修改
        $api->post('admin/voter_group/ajax_get_child', 'Admin\QualityControl\VoterGroupController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/voter_group/ajax_get_areachild', 'Admin\QualityControl\VoterGroupController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/voter_group/ajax_import_staff','Admin\QualityControl\VoterGroupController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/voter_group/import', 'Admin\QualityControl\VoterGroupController@import');// 导入excel
        $api->post('admin/voter_group/ajax_get_ids', 'Admin\QualityControl\VoterGroupController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


        // 选民表
        $api->any('admin/voters/ajax_alist', 'Admin\QualityControl\VotersController@ajax_alist');//ajax获得列表数据
        $api->post('admin/voters/ajax_del', 'Admin\QualityControl\VotersController@ajax_del');// 删除
        $api->post('admin/voters/ajax_save', 'Admin\QualityControl\VotersController@ajax_save');// 新加/修改
        $api->post('admin/voters/ajax_get_child', 'Admin\QualityControl\VotersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/voters/ajax_get_areachild', 'Admin\QualityControl\VotersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/voters/ajax_import_staff','Admin\QualityControl\VotersController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/voters/import', 'Admin\QualityControl\VotersController@import');// 导入excel
        $api->post('admin/voters/ajax_get_ids', 'Admin\QualityControl\VotersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 行业[一级分类]
        $api->any('admin/industry/ajax_alist', 'Admin\QualityControl\IndustryController@ajax_alist');//ajax获得列表数据
        $api->post('admin/industry/ajax_del', 'Admin\QualityControl\IndustryController@ajax_del');// 删除
        $api->post('admin/industry/ajax_save', 'Admin\QualityControl\IndustryController@ajax_save');// 新加/修改
        $api->post('admin/industry/ajax_get_child', 'Admin\QualityControl\IndustryController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/industry/ajax_get_areachild', 'Admin\QualityControl\IndustryController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/industry/ajax_import_staff','Admin\QualityControl\IndustryController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/industry/import', 'Admin\QualityControl\IndustryController@import');// 导入excel
        $api->post('admin/industry/ajax_get_ids', 'Admin\QualityControl\IndustryController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 课程管理
        $api->any('admin/course/ajax_alist', 'Admin\QualityControl\CourseController@ajax_alist');//ajax获得列表数据
        $api->post('admin/course/ajax_del', 'Admin\QualityControl\CourseController@ajax_del');// 删除
        $api->any('admin/course/ajax_save', 'Admin\QualityControl\CourseController@ajax_save');// 新加/修改
        $api->post('admin/course/ajax_get_child', 'Admin\QualityControl\CourseController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/course/ajax_get_areachild', 'Admin\QualityControl\CourseController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/course/ajax_import_staff','Admin\QualityControl\CourseController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/course/import', 'Admin\QualityControl\CourseController@import');// 导入excel
        $api->post('admin/course/ajax_get_ids', 'Admin\QualityControl\CourseController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/course/up_file', 'Admin\QualityControl\CourseController@up_file');// 上传文件地址

        // 培训班管理
        $api->any('admin/course_class/ajax_alist', 'Admin\QualityControl\CourseClassController@ajax_alist');//ajax获得列表数据
        $api->post('admin/course_class/ajax_del', 'Admin\QualityControl\CourseClassController@ajax_del');// 删除
        $api->post('admin/course_class/ajax_save', 'Admin\QualityControl\CourseClassController@ajax_save');// 新加/修改
        $api->post('admin/course_class/ajax_get_child', 'Admin\QualityControl\CourseClassController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/course_class/ajax_get_areachild', 'Admin\QualityControl\CourseClassController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/course_class/ajax_import_staff','Admin\QualityControl\CourseClassController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/course_class/import', 'Admin\QualityControl\CourseClassController@import');// 导入excel
        $api->post('admin/course_class/ajax_get_ids', 'Admin\QualityControl\CourseClassController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/course_class/ajax_open_class', 'Admin\QualityControl\CourseClassController@ajax_open_class');// 操作(开班)
        $api->post('admin/course_class/ajax_cancel_class', 'Admin\QualityControl\CourseClassController@ajax_cancel_class');// 操作(作废)
        $api->post('admin/course_class/ajax_finish_class', 'Admin\QualityControl\CourseClassController@ajax_finish_class');// 操作(结业)

        // 报名企业(主表)
        $api->any('admin/course_order/ajax_alist', 'Admin\QualityControl\CourseOrderController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/course_order/ajax_del', 'Admin\QualityControl\CourseOrderController@ajax_del');// 删除
//        $api->post('admin/course_order/ajax_save', 'Admin\QualityControl\CourseOrderController@ajax_save');// 新加/修改
//        $api->post('admin/course_order/ajax_get_child', 'Admin\QualityControl\CourseOrderController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/course_order/ajax_get_areachild', 'Admin\QualityControl\CourseOrderController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/course_order/ajax_import_staff','Admin\QualityControl\CourseOrderController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/course_order/import', 'Admin\QualityControl\CourseOrderController@import');// 导入excel
//        $api->post('admin/course_order/ajax_get_ids', 'Admin\QualityControl\CourseOrderController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 培训班企业管理
        $api->any('admin/course_class_company/ajax_alist', 'Admin\QualityControl\CourseClassCompanyController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/course_class_company/ajax_del', 'Admin\QualityControl\CourseClassCompanyController@ajax_del');// 删除
//        $api->post('admin/course_class_company/ajax_save', 'Admin\QualityControl\CourseClassCompanyController@ajax_save');// 新加/修改
//        $api->post('admin/course_class_company/ajax_get_child', 'Admin\QualityControl\CourseClassCompanyController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/course_class_company/ajax_get_areachild', 'Admin\QualityControl\CourseClassCompanyController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/course_class_company/ajax_import_staff','Admin\QualityControl\CourseClassCompanyController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/course_class_company/import', 'Admin\QualityControl\CourseClassCompanyController@import');// 导入excel
//        $api->post('admin/course_class_company/ajax_get_ids', 'Admin\QualityControl\CourseClassCompanyController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 报名学员
        $api->any('admin/course_order_staff/ajax_alist', 'Admin\QualityControl\CourseOrderStaffController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/course_order_staff/ajax_del', 'Admin\QualityControl\CourseOrderStaffController@ajax_del');// 删除
        $api->post('admin/course_order_staff/ajax_save', 'Admin\QualityControl\CourseOrderStaffController@ajax_save');// 新加/修改
//        $api->post('admin/course_order_staff/ajax_get_child', 'Admin\QualityControl\CourseOrderStaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/course_order_staff/ajax_get_areachild', 'Admin\QualityControl\CourseOrderStaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/course_order_staff/ajax_import_staff','Admin\QualityControl\CourseOrderStaffController@ajax_import'); // 导入员工
        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信
//
//        $api->post('admin/course_order_staff/import', 'Admin\QualityControl\CourseOrderStaffController@import');// 导入excel
//        $api->post('admin/course_order_staff/ajax_get_ids', 'Admin\QualityControl\CourseOrderStaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/course_order_staff/ajax_frozen', 'Admin\QualityControl\CourseOrderStaffController@ajax_frozen');// 操作(作废/取消作废)
        $api->post('admin/course_order_staff/ajax_join_class_save', 'Admin\QualityControl\CourseOrderStaffController@ajax_join_class_save');// 分班
        $api->post('admin/course_order_staff/ajax_cancel_class', 'Admin\QualityControl\CourseOrderStaffController@ajax_cancel_class');// 取消分班
        $api->any('admin/course_order_staff/ajax_create_order', 'Admin\QualityControl\CourseOrderStaffController@ajax_create_order');// 缴费生成订单
        // 面授操作日志
        $api->any('admin/course_log/ajax_alist', 'Admin\QualityControl\CourseLogController@ajax_alist');//ajax获得列表数据
        $api->post('admin/course_log/ajax_del', 'Admin\QualityControl\CourseLogController@ajax_del');// 删除
        $api->post('admin/course_log/ajax_save', 'Admin\QualityControl\CourseLogController@ajax_save');// 新加/修改
        $api->post('admin/course_log/ajax_get_child', 'Admin\QualityControl\CourseLogController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/course_log/ajax_get_areachild', 'Admin\QualityControl\CourseLogController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/course_log/ajax_import_staff','Admin\QualityControl\CourseLogController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/course_log/import', 'Admin\QualityControl\CourseLogController@import');// 导入excel
        $api->post('admin/course_log/ajax_get_ids', 'Admin\QualityControl\CourseLogController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        //  收款方式配置
        $api->any('admin/order_pay_method/ajax_alist', 'Admin\QualityControl\OrderPayMethodController@ajax_alist');//ajax获得列表数据
        $api->post('admin/order_pay_method/ajax_del', 'Admin\QualityControl\OrderPayMethodController@ajax_del');// 删除
        $api->any('admin/order_pay_method/ajax_save', 'Admin\QualityControl\OrderPayMethodController@ajax_save');// 新加/修改
        $api->post('admin/order_pay_method/ajax_get_child', 'Admin\QualityControl\OrderPayMethodController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/order_pay_method/ajax_get_areachild', 'Admin\QualityControl\OrderPayMethodController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/order_pay_method/ajax_import_staff','Admin\QualityControl\OrderPayMethodController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/order_pay_method/import', 'Admin\QualityControl\OrderPayMethodController@import');// 导入excel
        $api->post('admin/order_pay_method/ajax_get_ids', 'Admin\QualityControl\OrderPayMethodController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/order_pay_method/up_file', 'Admin\QualityControl\OrderPayMethodController@up_file');// 上传文件地址

        // 收款帐号配置
        $api->any('admin/order_pay_config/ajax_alist', 'Admin\QualityControl\OrderPayConfigController@ajax_alist');//ajax获得列表数据
        $api->post('admin/order_pay_config/ajax_del', 'Admin\QualityControl\OrderPayConfigController@ajax_del');// 删除
        $api->post('admin/order_pay_config/ajax_save', 'Admin\QualityControl\OrderPayConfigController@ajax_save');// 新加/修改
        $api->post('admin/order_pay_config/ajax_get_child', 'Admin\QualityControl\OrderPayConfigController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/order_pay_config/ajax_get_areachild', 'Admin\QualityControl\OrderPayConfigController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/order_pay_config/ajax_import_staff','Admin\QualityControl\OrderPayConfigController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/order_pay_config/import', 'Admin\QualityControl\OrderPayConfigController@import');// 导入excel
        $api->post('admin/order_pay_config/ajax_get_ids', 'Admin\QualityControl\OrderPayConfigController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/order_pay_config/ajax_info', 'Admin\QualityControl\OrderPayConfigController@ajax_info');//ajax获得详情数据
        $api->any('admin/order_pay_config/ajax_refreshAlipayToken', 'Admin\QualityControl\OrderPayConfigController@ajax_refreshAlipayToken');//ajax刷新授权令牌 access_token

        // 收款订单
        $api->any('admin/orders/ajax_alist', 'Admin\QualityControl\OrdersController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/orders/ajax_del', 'Admin\QualityControl\OrdersController@ajax_del');// 删除
//        $api->post('admin/orders/ajax_save', 'Admin\QualityControl\OrdersController@ajax_save');// 新加/修改
//        $api->post('admin/orders/ajax_get_child', 'Admin\QualityControl\OrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/orders/ajax_get_areachild', 'Admin\QualityControl\OrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/orders/ajax_import_staff','Admin\QualityControl\OrdersController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/orders/import', 'Admin\QualityControl\OrdersController@import');// 导入excel
//        $api->post('admin/orders/ajax_get_ids', 'Admin\QualityControl\OrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/orders/ajax_sure_order', 'Admin\QualityControl\OrdersController@ajax_sure_order');// 操作(确认)
        $api->post('admin/orders/ajax_finish_order', 'Admin\QualityControl\OrdersController@ajax_finish_order');// 操作(服务完成)
        $api->any('admin/orders/ajax_invoices_save', 'Admin\QualityControl\OrdersController@ajax_invoices_save');// 操作(开电子发票--蓝票)
        $api->post('admin/orders/ajax_invoices_cancel_save', 'Admin\QualityControl\OrdersController@ajax_invoices_cancel_save');// 操作(开电子全额冲红发票--红票)

        // 收款订单财务流水
        $api->any('admin/order_flow/ajax_alist', 'Admin\QualityControl\OrderFlowController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/order_flow/ajax_del', 'Admin\QualityControl\OrderFlowController@ajax_del');// 删除
//        $api->post('admin/order_flow/ajax_save', 'Admin\QualityControl\OrderFlowController@ajax_save');// 新加/修改
//        $api->post('admin/order_flow/ajax_get_child', 'Admin\QualityControl\OrderFlowController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/order_flow/ajax_get_areachild', 'Admin\QualityControl\OrderFlowController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/order_flow/ajax_import_staff','Admin\QualityControl\OrderFlowController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/order_flow/import', 'Admin\QualityControl\OrderFlowController@import');// 导入excel
//        $api->post('admin/order_flow/ajax_get_ids', 'Admin\QualityControl\OrderFlowController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 第三方对帐单
        $api->any('admin/order_pay/ajax_alist', 'Admin\QualityControl\OrderPayController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/order_pay/ajax_del', 'Admin\QualityControl\OrderPayController@ajax_del');// 删除
//        $api->post('admin/order_pay/ajax_save', 'Admin\QualityControl\OrderPayController@ajax_save');// 新加/修改
//        $api->post('admin/order_pay/ajax_get_child', 'Admin\QualityControl\OrderPayController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/order_pay/ajax_get_areachild', 'Admin\QualityControl\OrderPayController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/order_pay/ajax_import_staff','Admin\QualityControl\OrderPayController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/order_pay/import', 'Admin\QualityControl\OrderPayController@import');// 导入excel
//        $api->post('admin/order_pay/ajax_get_ids', 'Admin\QualityControl\OrderPayController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/order_pay/ajax_wx_query_order', 'Admin\QualityControl\OrderPayController@ajax_wx_query_order');// ajax查询微信扫码支付是否成功

        // 发票配置沪友
        $api->any('admin/invoice_config_hydzfp/ajax_alist', 'Admin\QualityControl\InvoiceConfigHydzfpController@ajax_alist');//ajax获得列表数据
        $api->post('admin/invoice_config_hydzfp/ajax_del', 'Admin\QualityControl\InvoiceConfigHydzfpController@ajax_del');// 删除
        $api->post('admin/invoice_config_hydzfp/ajax_save', 'Admin\QualityControl\InvoiceConfigHydzfpController@ajax_save');// 新加/修改
        $api->post('admin/invoice_config_hydzfp/ajax_get_child', 'Admin\QualityControl\InvoiceConfigHydzfpController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_config_hydzfp/ajax_get_areachild', 'Admin\QualityControl\InvoiceConfigHydzfpController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_config_hydzfp/ajax_import_staff','Admin\QualityControl\InvoiceConfigHydzfpController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/invoice_config_hydzfp/import', 'Admin\QualityControl\InvoiceConfigHydzfpController@import');// 导入excel
        $api->post('admin/invoice_config_hydzfp/ajax_get_ids', 'Admin\QualityControl\InvoiceConfigHydzfpController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/invoice_config_hydzfp/notify', 'Admin\QualityControl\InvoiceConfigHydzfpController@notify');// E0018-开票结果通知

        // 发票配置销售方
        $api->any('admin/invoice_seller/ajax_alist', 'Admin\QualityControl\InvoiceSellerController@ajax_alist');//ajax获得列表数据
        $api->post('admin/invoice_seller/ajax_del', 'Admin\QualityControl\InvoiceSellerController@ajax_del');// 删除
        $api->post('admin/invoice_seller/ajax_save', 'Admin\QualityControl\InvoiceSellerController@ajax_save');// 新加/修改
        $api->post('admin/invoice_seller/ajax_get_child', 'Admin\QualityControl\InvoiceSellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_seller/ajax_get_areachild', 'Admin\QualityControl\InvoiceSellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_seller/ajax_import_staff','Admin\QualityControl\InvoiceSellerController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/invoice_seller/import', 'Admin\QualityControl\InvoiceSellerController@import');// 导入excel
        $api->post('admin/invoice_seller/ajax_get_ids', 'Admin\QualityControl\InvoiceSellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 发票配置购买方
        $api->any('admin/invoice_buyer/ajax_alist', 'Admin\QualityControl\InvoiceBuyerController@ajax_alist');//ajax获得列表数据
        $api->post('admin/invoice_buyer/ajax_del', 'Admin\QualityControl\InvoiceBuyerController@ajax_del');// 删除
        $api->post('admin/invoice_buyer/ajax_save', 'Admin\QualityControl\InvoiceBuyerController@ajax_save');// 新加/修改
        $api->post('admin/invoice_buyer/ajax_get_child', 'Admin\QualityControl\InvoiceBuyerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_buyer/ajax_get_areachild', 'Admin\QualityControl\InvoiceBuyerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_buyer/ajax_import_staff','Admin\QualityControl\InvoiceBuyerController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/invoice_buyer/import', 'Admin\QualityControl\InvoiceBuyerController@import');// 导入excel
        $api->post('admin/invoice_buyer/ajax_get_ids', 'Admin\QualityControl\InvoiceBuyerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 发票开票模板
        $api->any('admin/invoice_template/ajax_alist', 'Admin\QualityControl\InvoiceTemplateController@ajax_alist');//ajax获得列表数据
        $api->post('admin/invoice_template/ajax_del', 'Admin\QualityControl\InvoiceTemplateController@ajax_del');// 删除
        $api->post('admin/invoice_template/ajax_save', 'Admin\QualityControl\InvoiceTemplateController@ajax_save');// 新加/修改
        $api->post('admin/invoice_template/ajax_get_child', 'Admin\QualityControl\InvoiceTemplateController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_template/ajax_get_areachild', 'Admin\QualityControl\InvoiceTemplateController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_template/ajax_import_staff','Admin\QualityControl\InvoiceTemplateController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/invoice_template/import', 'Admin\QualityControl\InvoiceTemplateController@import');// 导入excel
        $api->post('admin/invoice_template/ajax_get_ids', 'Admin\QualityControl\InvoiceTemplateController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 发票商品项目模板
        $api->any('admin/invoice_project_template/ajax_alist', 'Admin\QualityControl\InvoiceProjectTemplateController@ajax_alist');//ajax获得列表数据
        $api->post('admin/invoice_project_template/ajax_del', 'Admin\QualityControl\InvoiceProjectTemplateController@ajax_del');// 删除
        $api->post('admin/invoice_project_template/ajax_save', 'Admin\QualityControl\InvoiceProjectTemplateController@ajax_save');// 新加/修改
        $api->post('admin/invoice_project_template/ajax_get_child', 'Admin\QualityControl\InvoiceProjectTemplateController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_project_template/ajax_get_areachild', 'Admin\QualityControl\InvoiceProjectTemplateController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/invoice_project_template/ajax_import_staff','Admin\QualityControl\InvoiceProjectTemplateController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/invoice_project_template/import', 'Admin\QualityControl\InvoiceProjectTemplateController@import');// 导入excel
        $api->post('admin/invoice_project_template/ajax_get_ids', 'Admin\QualityControl\InvoiceProjectTemplateController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 发票主表
        $api->any('admin/invoices/ajax_alist', 'Admin\QualityControl\InvoicesController@ajax_alist');//ajax获得列表数据
        $api->post('admin/invoices/ajax_del', 'Admin\QualityControl\InvoicesController@ajax_del');// 删除
        $api->post('admin/invoices/ajax_save', 'Admin\QualityControl\InvoicesController@ajax_save');// 新加/修改
        $api->post('admin/invoices/ajax_get_child', 'Admin\QualityControl\InvoicesController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/invoices/ajax_get_areachild', 'Admin\QualityControl\InvoicesController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/invoices/ajax_import_staff','Admin\QualityControl\InvoicesController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/invoices/import', 'Admin\QualityControl\InvoicesController@import');// 导入excel
        $api->post('admin/invoices/ajax_get_ids', 'Admin\QualityControl\InvoicesController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业到期配置
        $api->any('admin/company_expire/ajax_alist', 'Admin\QualityControl\CompanyExpireController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_expire/ajax_del', 'Admin\QualityControl\CompanyExpireController@ajax_del');// 删除
        $api->post('admin/company_expire/ajax_save', 'Admin\QualityControl\CompanyExpireController@ajax_save');// 新加/修改
        $api->post('admin/company_expire/ajax_get_child', 'Admin\QualityControl\CompanyExpireController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_expire/ajax_get_areachild', 'Admin\QualityControl\CompanyExpireController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_expire/ajax_import_staff','Admin\QualityControl\CompanyExpireController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_expire/import', 'Admin\QualityControl\CompanyExpireController@import');// 导入excel
        $api->post('admin/company_expire/ajax_get_ids', 'Admin\QualityControl\CompanyExpireController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业会员等级配置
        $api->any('admin/company_grade_config/ajax_alist', 'Admin\QualityControl\CompanyGradeConfigController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_grade_config/ajax_del', 'Admin\QualityControl\CompanyGradeConfigController@ajax_del');// 删除
        $api->post('admin/company_grade_config/ajax_save', 'Admin\QualityControl\CompanyGradeConfigController@ajax_save');// 新加/修改
        $api->post('admin/company_grade_config/ajax_get_child', 'Admin\QualityControl\CompanyGradeConfigController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_grade_config/ajax_get_areachild', 'Admin\QualityControl\CompanyGradeConfigController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_grade_config/ajax_import_staff','Admin\QualityControl\CompanyGradeConfigController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_grade_config/import', 'Admin\QualityControl\CompanyGradeConfigController@import');// 导入excel
        $api->post('admin/company_grade_config/ajax_get_ids', 'Admin\QualityControl\CompanyGradeConfigController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/company_grade_config/ajax_open', 'Admin\QualityControl\CompanyGradeConfigController@ajax_open');// 审核操作(通过/不通过)
        // 城市[一级分类]
        $api->any('admin/citys/ajax_alist', 'Admin\QualityControl\CitysController@ajax_alist');//ajax获得列表数据
        $api->post('admin/citys/ajax_del', 'Admin\QualityControl\CitysController@ajax_del');// 删除
        $api->post('admin/citys/ajax_save', 'Admin\QualityControl\CitysController@ajax_save');// 新加/修改
        $api->any('admin/citys/ajax_info', 'Admin\QualityControl\CitysController@ajax_info');// 详情
        $api->post('admin/citys/ajax_get_child', 'Admin\QualityControl\CitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/citys/ajax_get_areachild', 'Admin\QualityControl\CitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/citys/ajax_import_staff','Admin\QualityControl\CitysController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/citys/import', 'Admin\QualityControl\CitysController@import');// 导入excel
        $api->post('admin/citys/ajax_get_ids', 'Admin\QualityControl\CitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 证书设置[一级分类]
        $api->any('admin/ability_code/ajax_alist', 'Admin\QualityControl\AbilityCodeController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/ability_code/ajax_del', 'Admin\QualityControl\AbilityCodeController@ajax_del');// 删除
        $api->any('admin/ability_code/ajax_save', 'Admin\QualityControl\AbilityCodeController@ajax_save');// 新加/修改
//        $api->any('admin/ability_code/ajax_info', 'Admin\QualityControl\AbilityCodeController@ajax_info');// 详情
//        $api->post('admin/ability_code/ajax_get_child', 'Admin\QualityControl\AbilityCodeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/ability_code/ajax_get_areachild', 'Admin\QualityControl\AbilityCodeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/ability_code/ajax_import_staff','Admin\QualityControl\AbilityCodeController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信
//
//        $api->post('admin/ability_code/import', 'Admin\QualityControl\AbilityCodeController@import');// 导入excel
//        $api->post('admin/ability_code/ajax_get_ids', 'Admin\QualityControl\AbilityCodeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 老师登录验证码 验证码
        $api->any('admin/sms_code/ajax_alist', 'Admin\QualityControl\SmsCodeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_code/ajax_del', 'Admin\QualityControl\SmsCodeController@ajax_del');// 删除
        $api->post('admin/sms_code/ajax_save', 'Admin\QualityControl\SmsCodeController@ajax_save');// 新加/修改
        $api->post('admin/sms_code/ajax_get_child', 'Admin\QualityControl\SmsCodeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_code/ajax_get_areachild', 'Admin\QualityControl\SmsCodeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_code/ajax_import_staff','Admin\QualityControl\SmsCodeController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/sms_code/import', 'Admin\QualityControl\SmsCodeController@import');// 导入excel
        $api->post('admin/sms_code/ajax_get_ids', 'Admin\QualityControl\SmsCodeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 证书
        $api->any('admin/certificate/ajax_alist', 'Admin\QualityControl\CertificateController@ajax_alist');//ajax获得列表数据
        $api->post('admin/certificate/ajax_del', 'Admin\QualityControl\CertificateController@ajax_del');// 删除
        $api->post('admin/certificate/ajax_save', 'Admin\QualityControl\CertificateController@ajax_save');// 新加/修改
        $api->any('admin/certificate/ajax_info', 'Admin\QualityControl\CertificateController@ajax_info');// 详情
        $api->post('admin/certificate/ajax_get_child', 'Admin\QualityControl\CertificateController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/certificate/ajax_get_areachild', 'Admin\QualityControl\CertificateController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/certificate/ajax_import_staff','Admin\QualityControl\CertificateController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/certificate/import', 'Admin\QualityControl\CertificateController@import');// 导入excel
        $api->post('admin/certificate/ajax_get_ids', 'Admin\QualityControl\CertificateController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 实验室地址
        $api->any('admin/laboratory_addr/ajax_alist', 'Admin\QualityControl\LaboratoryAddrController@ajax_alist');//ajax获得列表数据
        $api->post('admin/laboratory_addr/ajax_del', 'Admin\QualityControl\LaboratoryAddrController@ajax_del');// 删除
        $api->post('admin/laboratory_addr/ajax_save', 'Admin\QualityControl\LaboratoryAddrController@ajax_save');// 新加/修改
        $api->post('admin/laboratory_addr/ajax_get_child', 'Admin\QualityControl\LaboratoryAddrController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/laboratory_addr/ajax_get_areachild', 'Admin\QualityControl\LaboratoryAddrController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/laboratory_addr/ajax_import_staff','Admin\QualityControl\LaboratoryAddrController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/laboratory_addr/import', 'Admin\QualityControl\LaboratoryAddrController@import');// 导入excel
        $api->post('admin/laboratory_addr/ajax_get_ids', 'Admin\QualityControl\LaboratoryAddrController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 证书-能力范围
        $api->any('admin/certificate_schedule/ajax_alist', 'Admin\QualityControl\CertificateScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('admin/certificate_schedule/ajax_del', 'Admin\QualityControl\CertificateScheduleController@ajax_del');// 删除
        $api->any('admin/certificate_schedule/ajax_save', 'Admin\QualityControl\CertificateScheduleController@ajax_save');// 新加/修改
        $api->any('admin/certificate_schedule/ajax_info', 'Admin\QualityControl\CertificateScheduleController@ajax_info');// 详情
        $api->post('admin/certificate_schedule/ajax_get_child', 'Admin\QualityControl\CertificateScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/certificate_schedule/ajax_get_areachild', 'Admin\QualityControl\CertificateScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/certificate_schedule/ajax_import_staff','Admin\QualityControl\CertificateScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/certificate_schedule/import', 'Admin\QualityControl\CertificateScheduleController@import');// 导入excel
        $api->post('admin/certificate_schedule/ajax_get_ids', 'Admin\QualityControl\CertificateScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/certificate_schedule/up_excel', 'Admin\QualityControl\CertificateScheduleController@up_excel');// 上传excel地址
        $api->post('admin/certificate_schedule/ajax_excel_save', 'Admin\QualityControl\CertificateScheduleController@ajax_excel_save');// 上传excel--导入保存

        // 证书-证书导入批次
        $api->any('admin/certificate_import_log/ajax_alist', 'Admin\QualityControl\CertificateImportLogController@ajax_alist');//ajax获得列表数据
        $api->post('admin/certificate_import_log/ajax_del', 'Admin\QualityControl\CertificateImportLogController@ajax_del');// 删除
        $api->any('admin/certificate_import_log/ajax_save', 'Admin\QualityControl\CertificateImportLogController@ajax_save');// 新加/修改
        $api->any('admin/certificate_import_log/ajax_info', 'Admin\QualityControl\CertificateImportLogController@ajax_info');// 详情
        $api->post('admin/certificate_import_log/ajax_get_child', 'Admin\QualityControl\CertificateImportLogController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/certificate_import_log/ajax_get_areachild', 'Admin\QualityControl\CertificateImportLogController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/certificate_import_log/ajax_import_staff','Admin\QualityControl\CertificateImportLogController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/certificate_import_log/import', 'Admin\QualityControl\CertificateImportLogController@import');// 导入excel
        $api->post('admin/certificate_import_log/ajax_get_ids', 'Admin\QualityControl\CertificateImportLogController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 资质证书类型[一级分类]
        $api->any('admin/company_certificate_type/ajax_alist', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_alist');//ajax获得列表数据
        $api->any('admin/company_certificate_type/ajax_del', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_del');// 删除
        $api->post('admin/company_certificate_type/ajax_save', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_save');// 新加/修改
        $api->post('admin/company_certificate_type/ajax_get_child', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_certificate_type/ajax_get_areachild', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_certificate_type/ajax_import_staff','Admin\QualityControl\CompanyCertificateTypeController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_certificate_type/import', 'Admin\QualityControl\CompanyCertificateTypeController@import');// 导入excel
        $api->post('admin/company_certificate_type/ajax_get_ids', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证行业分类[一级分类]
        $api->any('admin/ability_type/ajax_alist', 'Admin\QualityControl\AbilityTypeController@ajax_alist');//ajax获得列表数据
        $api->any('admin/ability_type/ajax_del', 'Admin\QualityControl\AbilityTypeController@ajax_del');// 删除
        $api->post('admin/ability_type/ajax_save', 'Admin\QualityControl\AbilityTypeController@ajax_save');// 新加/修改
        $api->post('admin/ability_type/ajax_get_child', 'Admin\QualityControl\AbilityTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/ability_type/ajax_get_areachild', 'Admin\QualityControl\AbilityTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/ability_type/ajax_import_staff','Admin\QualityControl\AbilityTypeController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/ability_type/import', 'Admin\QualityControl\AbilityTypeController@import');// 导入excel
        $api->post('admin/ability_type/ajax_get_ids', 'Admin\QualityControl\AbilityTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业内容管理
        $api->any('admin/company_content/ajax_alist', 'Admin\QualityControl\CompanyContentController@ajax_alist');//ajax获得列表数据
        $api->any('admin/company_content/ajax_del', 'Admin\QualityControl\CompanyContentController@ajax_del');// 删除
        $api->any('admin/company_content/ajax_save', 'Admin\QualityControl\CompanyContentController@ajax_save');// 新加/修改
        $api->post('admin/company_content/ajax_get_child', 'Admin\QualityControl\CompanyContentController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_content/ajax_get_areachild', 'Admin\QualityControl\CompanyContentController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_content/ajax_import_staff','Admin\QualityControl\CompanyContentController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_content/import', 'Admin\QualityControl\CompanyContentController@import');// 导入excel
        $api->post('admin/company_content/ajax_get_ids', 'Admin\QualityControl\CompanyContentController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证
        $api->any('admin/abilitys/ajax_alist', 'Admin\QualityControl\AbilitysController@ajax_alist');//ajax获得列表数据
        $api->post('admin/abilitys/ajax_del', 'Admin\QualityControl\AbilitysController@ajax_del');// 删除
        $api->post('admin/abilitys/ajax_save', 'Admin\QualityControl\AbilitysController@ajax_save');// 新加/修改
        $api->post('admin/abilitys/ajax_get_child', 'Admin\QualityControl\AbilitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/abilitys/ajax_get_areachild', 'Admin\QualityControl\AbilitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/abilitys/ajax_import_staff','Admin\QualityControl\AbilitysController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/abilitys/import', 'Admin\QualityControl\AbilitysController@import');// 导入excel
        $api->post('admin/abilitys/ajax_get_ids', 'Admin\QualityControl\AbilitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/abilitys/ajax_save_publish', 'Admin\QualityControl\AbilitysController@ajax_save_publish');// 修改公布时间类型

        $api->post('admin/abilitys/up_excel', 'Admin\QualityControl\AbilitysController@up_excel');// 上传excel地址
        $api->post('admin/abilitys/ajax_excel_save', 'Admin\QualityControl\AbilitysController@ajax_excel_save');// 上传excel--导入保存

        //****************************************************************************
        // 能力验证管理

        // 能力验证--报名管理--参加单位
        $api->any('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_alist', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_del', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_del');// 删除
//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_save', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_save');// 新加/修改
//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_get_child', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_get_areachild', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_import_staff','Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/import', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@import');// 导入excel
//        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items/ajax_get_ids', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证结果--报名管理--参加单位
        $api->any('admin/abilitys_admin/{ability_id}/ability_join_items_results/ajax_alist', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_alist');//ajax获得列表数据
        $api->any('admin/abilitys_admin/{ability_id}/ability_join_items_results/ajax_save', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_save');// 新加/修改
        $api->any('admin/abilitys_admin/{ability_id}/ability_join_items_results/ajax_save_sample', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_save_sample');// 新加/修改--取样

        $api->any('admin/abilitys_admin/{ability_id}/ability_join_items_results/ajax_save_dissatisfied', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_save_dissatisfied');// 已领样，未上传数据的--可以手动直接判断为不满意

        $api->post('admin/abilitys_admin/{ability_id}/ability_join_items_results/ajax_sms_send', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@ajax_sms_send');// 短信模板发送短信
        //****************************************************************************

        // 能力验证--报名管理
        $api->any('admin/ability_join/ajax_alist', 'Admin\QualityControl\AbilityJoinController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/ability_join/ajax_del', 'Admin\QualityControl\AbilityJoinController@ajax_del');// 删除
//        $api->post('admin/ability_join/ajax_save', 'Admin\QualityControl\AbilityJoinController@ajax_save');// 新加/修改
//        $api->post('admin/ability_join/ajax_get_child', 'Admin\QualityControl\AbilityJoinController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/ability_join/ajax_get_areachild', 'Admin\QualityControl\AbilityJoinController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/ability_join/ajax_import_staff','Admin\QualityControl\AbilityJoinController@ajax_import'); // 导入员工
        $api->post('admin/ability_join/ajax_sms_send', 'Admin\QualityControl\AbilityJoinController@ajax_sms_send');// 短信模板发送短信

//        $api->post('admin/ability_join/import', 'Admin\QualityControl\AbilityJoinController@import');// 导入excel
//        $api->post('admin/ability_join/ajax_get_ids', 'Admin\QualityControl\AbilityJoinController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/ability_join/ajax_save_sample', 'Admin\QualityControl\AbilityJoinController@ajax_save_sample');// 保存取样
        $api->any('admin/ability_join/ajax_print', 'Admin\QualityControl\AbilityJoinController@ajax_print');// 操作(标记打印操作)
        $api->any('admin/ability_join/ajax_grant', 'Admin\QualityControl\AbilityJoinController@ajax_grant');// 操作(标记证书领取操作)
        $api->any('admin/ability_join/ajax_search_print', 'Admin\QualityControl\AbilityJoinController@ajax_search_print');// 按查询条件操作(标记打印操作)
        // 企业能力附表
        $api->any('admin/company_schedule/ajax_alist', 'Admin\QualityControl\CompanyScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_schedule/ajax_del', 'Admin\QualityControl\CompanyScheduleController@ajax_del');// 删除
        $api->post('admin/company_schedule/ajax_save', 'Admin\QualityControl\CompanyScheduleController@ajax_save');// 新加/修改
        $api->post('admin/company_schedule/ajax_get_child', 'Admin\QualityControl\CompanyScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_schedule/ajax_get_areachild', 'Admin\QualityControl\CompanyScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_schedule/ajax_import_staff','Admin\QualityControl\CompanyScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_schedule/import', 'Admin\QualityControl\CompanyScheduleController@import');// 导入excel
        $api->post('admin/company_schedule/ajax_get_ids', 'Admin\QualityControl\CompanyScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业能力附表
        $api->any('admin/company_new_schedule/ajax_alist', 'Admin\QualityControl\CompanyNewScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_new_schedule/ajax_del', 'Admin\QualityControl\CompanyNewScheduleController@ajax_del');// 删除
        $api->any('admin/company_new_schedule/ajax_save', 'Admin\QualityControl\CompanyNewScheduleController@ajax_save');// 新加/修改
        $api->any('admin/company_new_schedule/ajax_excel_save', 'Admin\QualityControl\CompanyNewScheduleController@ajax_excel_save');// 上传excel
        $api->post('admin/company_new_schedule/ajax_get_child', 'Admin\QualityControl\CompanyNewScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_new_schedule/ajax_get_areachild', 'Admin\QualityControl\CompanyNewScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_new_schedule/ajax_import_staff','Admin\QualityControl\CompanyNewScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_new_schedule/import', 'Admin\QualityControl\CompanyNewScheduleController@import');// 导入excel
        $api->post('admin/company_new_schedule/ajax_get_ids', 'Admin\QualityControl\CompanyNewScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/company_new_schedule/up_word', 'Admin\QualityControl\CompanyNewScheduleController@up_word');// 上传word地址
        $api->post('admin/company_new_schedule/up_pdf', 'Admin\QualityControl\CompanyNewScheduleController@up_pdf');// 上传pdf地址
        $api->post('admin/company_new_schedule/up_excel', 'Admin\QualityControl\CompanyNewScheduleController@up_excel');// 上传excel地址
        $api->any('admin/company_new_schedule/ajax_alist_company', 'Admin\QualityControl\CompanyNewScheduleController@ajax_alist_company');//ajax获得列表数据--按企业id降序

        // 应用管理
        $api->any('admin/apply/ajax_alist', 'Admin\QualityControl\ApplyController@ajax_alist');//ajax获得列表数据
        $api->post('admin/apply/ajax_del', 'Admin\QualityControl\ApplyController@ajax_del');// 删除
        $api->any('admin/apply/ajax_save', 'Admin\QualityControl\ApplyController@ajax_save');// 新加/修改
        $api->any('admin/apply/ajax_info', 'Admin\QualityControl\ApplyController@ajax_info');// 详情
        $api->post('admin/apply/ajax_get_child', 'Admin\QualityControl\ApplyController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/apply/ajax_get_areachild', 'Admin\QualityControl\ApplyController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/apply/ajax_import_staff','Admin\QualityControl\ApplyController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/apply/import', 'Admin\QualityControl\ApplyController@import');// 导入excel
        $api->post('admin/apply/ajax_get_ids', 'Admin\QualityControl\ApplyController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/apply/up_file', 'Admin\QualityControl\ApplyController@up_file');// 上传文件地址

        // 接口日志
        $api->any('admin/api_log/ajax_alist', 'Admin\QualityControl\ApiLogController@ajax_alist');//ajax获得列表数据
        $api->post('admin/api_log/ajax_del', 'Admin\QualityControl\ApiLogController@ajax_del');// 删除
        $api->post('admin/api_log/ajax_save', 'Admin\QualityControl\ApiLogController@ajax_save');// 新加/修改
        $api->post('admin/api_log/ajax_get_child', 'Admin\QualityControl\ApiLogController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/api_log/ajax_get_areachild', 'Admin\QualityControl\ApiLogController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/api_log/ajax_import_staff','Admin\QualityControl\ApiLogController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/api_log/import', 'Admin\QualityControl\ApiLogController@import');// 导入excel
        $api->post('admin/api_log/ajax_get_ids', 'Admin\QualityControl\ApiLogController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 短信相关的
        // 短信模板所属模块
        $api->any('admin/sms_module/ajax_alist', 'Admin\QualityControl\SmsModuleController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_module/ajax_del', 'Admin\QualityControl\SmsModuleController@ajax_del');// 删除
        $api->any('admin/sms_module/ajax_save', 'Admin\QualityControl\SmsModuleController@ajax_save');// 新加/修改
        $api->post('admin/sms_module/ajax_get_child', 'Admin\QualityControl\SmsModuleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_module/ajax_get_areachild', 'Admin\QualityControl\SmsModuleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_module/ajax_import_staff','Admin\QualityControl\SmsModuleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/sms_module/import', 'Admin\QualityControl\SmsModuleController@import');// 导入excel
        $api->post('admin/sms_module/ajax_get_ids', 'Admin\QualityControl\SmsModuleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 快捷常用参数
        $api->any('admin/sms_module_params_common/ajax_alist', 'Admin\QualityControl\SmsModuleParamsCommonController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_module_params_common/ajax_del', 'Admin\QualityControl\SmsModuleParamsCommonController@ajax_del');// 删除
        $api->post('admin/sms_module_params_common/ajax_save', 'Admin\QualityControl\SmsModuleParamsCommonController@ajax_save');// 新加/修改
        $api->post('admin/sms_module_params_common/ajax_get_child', 'Admin\QualityControl\SmsModuleParamsCommonController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_module_params_common/ajax_get_areachild', 'Admin\QualityControl\SmsModuleParamsCommonController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_module_params_common/ajax_import_staff','Admin\QualityControl\SmsModuleParamsCommonController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/sms_module_params_common/import', 'Admin\QualityControl\SmsModuleParamsCommonController@import');// 导入excel
        $api->post('admin/sms_module_params_common/ajax_get_ids', 'Admin\QualityControl\SmsModuleParamsCommonController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 短信模板所属模块参数
        $api->any('admin/sms_module_params/ajax_alist', 'Admin\QualityControl\SmsModuleParamsController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_module_params/ajax_del', 'Admin\QualityControl\SmsModuleParamsController@ajax_del');// 删除
        $api->post('admin/sms_module_params/ajax_save', 'Admin\QualityControl\SmsModuleParamsController@ajax_save');// 新加/修改
        $api->post('admin/sms_module_params/ajax_get_child', 'Admin\QualityControl\SmsModuleParamsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_module_params/ajax_get_areachild', 'Admin\QualityControl\SmsModuleParamsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_module_params/ajax_import_staff','Admin\QualityControl\SmsModuleParamsController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/sms_module_params/import', 'Admin\QualityControl\SmsModuleParamsController@import');// 导入excel
        $api->post('admin/sms_module_params/ajax_get_ids', 'Admin\QualityControl\SmsModuleParamsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/sms_module_params/ajax_alist_all', 'Admin\QualityControl\SmsModuleParamsController@ajax_alist_all');//ajax获得列表数据--所有的
        // 限次配置
        $api->any('admin/sms_limit/ajax_alist', 'Admin\QualityControl\SmsLimitController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_limit/ajax_del', 'Admin\QualityControl\SmsLimitController@ajax_del');// 删除
        $api->post('admin/sms_limit/ajax_save', 'Admin\QualityControl\SmsLimitController@ajax_save');// 新加/修改
        $api->post('admin/sms_limit/ajax_get_child', 'Admin\QualityControl\SmsLimitController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_limit/ajax_get_areachild', 'Admin\QualityControl\SmsLimitController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_limit/ajax_import_staff','Admin\QualityControl\SmsLimitController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/sms_limit/import', 'Admin\QualityControl\SmsLimitController@import');// 导入excel
        $api->post('admin/sms_limit/ajax_get_ids', 'Admin\QualityControl\SmsLimitController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 短信模板
        $api->any('admin/sms_template/ajax_alist', 'Admin\QualityControl\SmsTemplateController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_template/ajax_del', 'Admin\QualityControl\SmsTemplateController@ajax_del');// 删除
        $api->post('admin/sms_template/ajax_save', 'Admin\QualityControl\SmsTemplateController@ajax_save');// 新加/修改
        $api->post('admin/sms_template/ajax_get_child', 'Admin\QualityControl\SmsTemplateController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_template/ajax_get_areachild', 'Admin\QualityControl\SmsTemplateController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_template/ajax_import_staff','Admin\QualityControl\SmsTemplateController@ajax_import'); // 导入员工
        $api->post('admin/sms_template/ajax_sms_send', 'Admin\QualityControl\SmsTemplateController@ajax_sms_send');// 短信模板发送短信测试

        $api->post('admin/sms_template/import', 'Admin\QualityControl\SmsTemplateController@import');// 导入excel
        $api->post('admin/sms_template/ajax_get_ids', 'Admin\QualityControl\SmsTemplateController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 短信日志
        $api->any('admin/sms_log/ajax_alist', 'Admin\QualityControl\SmsLogController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_log/ajax_del', 'Admin\QualityControl\SmsLogController@ajax_del');// 删除
        $api->post('admin/sms_log/ajax_save', 'Admin\QualityControl\SmsLogController@ajax_save');// 新加/修改
        $api->post('admin/sms_log/ajax_get_child', 'Admin\QualityControl\SmsLogController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_log/ajax_get_areachild', 'Admin\QualityControl\SmsLogController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_log/ajax_import_staff','Admin\QualityControl\SmsLogController@ajax_import'); // 导入员工
        // $api->post('admin/sms_log/ajax_sms_send', 'Admin\QualityControl\SmsLogController@ajax_sms_send');// 短信模板发送短信测试

        $api->post('admin/sms_log/import', 'Admin\QualityControl\SmsLogController@import');// 导入excel
        $api->post('admin/sms_log/ajax_get_ids', 'Admin\QualityControl\SmsLogController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 点播相关的
        // 点播课程分类
        $api->any('admin/vod_type/ajax_alist', 'Admin\QualityControl\VodTypeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vod_type/ajax_del', 'Admin\QualityControl\VodTypeController@ajax_del');// 删除
        $api->post('admin/vod_type/ajax_save', 'Admin\QualityControl\VodTypeController@ajax_save');// 新加/修改
        $api->post('admin/vod_type/ajax_get_child', 'Admin\QualityControl\VodTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_type/ajax_get_areachild', 'Admin\QualityControl\VodTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_type/ajax_import_staff','Admin\QualityControl\VodTypeController@ajax_import'); // 导入员工
//        $api->post('admin/vod_type/ajax_sms_send', 'Admin\QualityControl\VodTypeController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vod_type/import', 'Admin\QualityControl\VodTypeController@import');// 导入excel
        $api->post('admin/vod_type/ajax_get_ids', 'Admin\QualityControl\VodTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->post('admin/vod_type/up_file', 'Admin\QualityControl\VodTypeController@up_file');// 上传文件地址

        // 点播课程
        $api->any('admin/vods/ajax_alist', 'Admin\QualityControl\VodsController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vods/ajax_del', 'Admin\QualityControl\VodsController@ajax_del');// 删除
        $api->post('admin/vods/ajax_save', 'Admin\QualityControl\VodsController@ajax_save');// 新加/修改
        $api->any('admin/vods/ajax_join_save', 'Admin\QualityControl\VodsController@ajax_join_save');// 报名
        $api->post('admin/vods/ajax_get_child', 'Admin\QualityControl\VodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vods/ajax_get_areachild', 'Admin\QualityControl\VodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vods/ajax_import_staff','Admin\QualityControl\VodsController@ajax_import'); // 导入员工
//        $api->post('admin/vods/ajax_sms_send', 'Admin\QualityControl\VodsController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vods/import', 'Admin\QualityControl\VodsController@import');// 导入excel
        $api->post('admin/vods/ajax_get_ids', 'Admin\QualityControl\VodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->post('admin/vods/up_file', 'Admin\QualityControl\VodsController@up_file');// 上传文件地址

        // 点播课程内容
        $api->any('admin/vods_content/ajax_alist', 'Admin\QualityControl\VodsContentController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vods_content/ajax_del', 'Admin\QualityControl\VodsContentController@ajax_del');// 删除
        $api->post('admin/vods_content/ajax_save', 'Admin\QualityControl\VodsContentController@ajax_save');// 新加/修改
        $api->post('admin/vods_content/ajax_get_child', 'Admin\QualityControl\VodsContentController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vods_content/ajax_get_areachild', 'Admin\QualityControl\VodsContentController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vods_content/ajax_import_staff','Admin\QualityControl\VodsContentController@ajax_import'); // 导入员工
//        $api->post('admin/vods_content/ajax_sms_send', 'Admin\QualityControl\VodsContentController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vods_content/import', 'Admin\QualityControl\VodsContentController@import');// 导入excel
        $api->post('admin/vods_content/ajax_get_ids', 'Admin\QualityControl\VodsContentController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 点播课程视频目录
        $api->any('admin/vod_video/ajax_alist', 'Admin\QualityControl\VodVideoController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vod_video/ajax_del', 'Admin\QualityControl\VodVideoController@ajax_del');// 删除
        $api->post('admin/vod_video/ajax_save', 'Admin\QualityControl\VodVideoController@ajax_save');// 新加/修改
        $api->post('admin/vod_video/ajax_get_child', 'Admin\QualityControl\VodVideoController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_video/ajax_get_areachild', 'Admin\QualityControl\VodVideoController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_video/ajax_import_staff','Admin\QualityControl\VodVideoController@ajax_import'); // 导入员工
//        $api->post('admin/vod_video/ajax_sms_send', 'Admin\QualityControl\VodVideoController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vod_video/import', 'Admin\QualityControl\VodVideoController@import');// 导入excel
        $api->post('admin/vod_video/ajax_get_ids', 'Admin\QualityControl\VodVideoController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/vod_video/up_file', 'Admin\QualityControl\VodVideoController@up_file');// 上传文件地址--封面
        $api->post('admin/vod_video/up_file_video', 'Admin\QualityControl\VodVideoController@up_file_video');// 上传文件地址--音频、视频
        $api->post('admin/vod_video/up_file_courseware', 'Admin\QualityControl\VodVideoController@up_file_courseware');// 上传文件地址--附件课件资料
        $api->post('admin/vod_video/ajax_dir_save', 'Admin\QualityControl\VodVideoController@ajax_dir_save');// 新加/修改--目录
        $api->any('admin/vod_video/ajax_get_vod_dir', 'Admin\QualityControl\VodVideoController@ajax_get_vod_dir');// 根据课程id 及当前记录id，获得 课程的目录
        // 点播课程订单
        $api->any('admin/vod_orders/ajax_alist', 'Admin\QualityControl\VodOrdersController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/vod_orders/ajax_del', 'Admin\QualityControl\VodOrdersController@ajax_del');// 删除
//        $api->post('admin/vod_orders/ajax_save', 'Admin\QualityControl\VodOrdersController@ajax_save');// 新加/修改
        $api->post('admin/vod_orders/ajax_get_child', 'Admin\QualityControl\VodOrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_orders/ajax_get_areachild', 'Admin\QualityControl\VodOrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_orders/ajax_import_staff','Admin\QualityControl\VodOrdersController@ajax_import'); // 导入员工
//        $api->post('admin/vod_orders/ajax_sms_send', 'Admin\QualityControl\VodOrdersController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vod_orders/import', 'Admin\QualityControl\VodOrdersController@import');// 导入excel
        $api->post('admin/vod_orders/ajax_get_ids', 'Admin\QualityControl\VodOrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->any('admin/vod_orders/ajax_create_order', 'Admin\QualityControl\VodOrdersController@ajax_create_order');// 缴费生成订单

        // 点播课程销量统计【流水】
        $api->any('admin/vod_sales/ajax_alist', 'Admin\QualityControl\VodSalesController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vod_sales/ajax_del', 'Admin\QualityControl\VodSalesController@ajax_del');// 删除
        $api->post('admin/vod_sales/ajax_save', 'Admin\QualityControl\VodSalesController@ajax_save');// 新加/修改
        $api->post('admin/vod_sales/ajax_get_child', 'Admin\QualityControl\VodSalesController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_sales/ajax_get_areachild', 'Admin\QualityControl\VodSalesController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_sales/ajax_import_staff','Admin\QualityControl\VodSalesController@ajax_import'); // 导入员工
//        $api->post('admin/vod_sales/ajax_sms_send', 'Admin\QualityControl\VodSalesController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vod_sales/import', 'Admin\QualityControl\VodSalesController@import');// 导入excel
        $api->post('admin/vod_sales/ajax_get_ids', 'Admin\QualityControl\VodSalesController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 点播课程评论
        $api->any('admin/vod_comment/ajax_alist', 'Admin\QualityControl\VodCommentController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vod_comment/ajax_del', 'Admin\QualityControl\VodCommentController@ajax_del');// 删除
        $api->post('admin/vod_comment/ajax_save', 'Admin\QualityControl\VodCommentController@ajax_save');// 新加/修改
        $api->post('admin/vod_comment/ajax_get_child', 'Admin\QualityControl\VodCommentController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_comment/ajax_get_areachild', 'Admin\QualityControl\VodCommentController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_comment/ajax_import_staff','Admin\QualityControl\VodCommentController@ajax_import'); // 导入员工
//        $api->post('admin/vod_comment/ajax_sms_send', 'Admin\QualityControl\VodCommentController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vod_comment/import', 'Admin\QualityControl\VodCommentController@import');// 导入excel
        $api->post('admin/vod_comment/ajax_get_ids', 'Admin\QualityControl\VodCommentController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 点播课程学员学习进度
        $api->any('admin/vod_rate/ajax_alist', 'Admin\QualityControl\VodRateController@ajax_alist');//ajax获得列表数据
        $api->post('admin/vod_rate/ajax_del', 'Admin\QualityControl\VodRateController@ajax_del');// 删除
        $api->post('admin/vod_rate/ajax_save', 'Admin\QualityControl\VodRateController@ajax_save');// 新加/修改
        $api->post('admin/vod_rate/ajax_get_child', 'Admin\QualityControl\VodRateController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_rate/ajax_get_areachild', 'Admin\QualityControl\VodRateController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/vod_rate/ajax_import_staff','Admin\QualityControl\VodRateController@ajax_import'); // 导入员工
//        $api->post('admin/vod_rate/ajax_sms_send', 'Admin\QualityControl\VodRateController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/vod_rate/import', 'Admin\QualityControl\VodRateController@import');// 导入excel
        $api->post('admin/vod_rate/ajax_get_ids', 'Admin\QualityControl\VodRateController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 直播

        // 直播公告
        $api->any('admin/live_notice/ajax_alist', 'Admin\QualityControl\LiveNoticeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/live_notice/ajax_del', 'Admin\QualityControl\LiveNoticeController@ajax_del');// 删除
        $api->post('admin/live_notice/ajax_save', 'Admin\QualityControl\LiveNoticeController@ajax_save');// 新加/修改
        $api->post('admin/live_notice/ajax_get_child', 'Admin\QualityControl\LiveNoticeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/live_notice/ajax_get_areachild', 'Admin\QualityControl\LiveNoticeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/live_notice/ajax_import_staff','Admin\QualityControl\LiveNoticeController@ajax_import'); // 导入员工
//        $api->post('admin/live_notice/ajax_sms_send', 'Admin\QualityControl\LiveNoticeController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/live_notice/import', 'Admin\QualityControl\LiveNoticeController@import');// 导入excel
        $api->post('admin/live_notice/ajax_get_ids', 'Admin\QualityControl\LiveNoticeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 省局企业相关的

        // 监督检查信息管理
        $api->any('admin/company_supervise/ajax_alist', 'Admin\QualityControl\CompanySuperviseController@ajax_alist');//ajax获得列表数据
        $api->any('admin/company_supervise/ajax_del', 'Admin\QualityControl\CompanySuperviseController@ajax_del');// 删除
        $api->any('admin/company_supervise/ajax_save', 'Admin\QualityControl\CompanySuperviseController@ajax_save');// 新加/修改
        $api->post('admin/company_supervise/ajax_get_child', 'Admin\QualityControl\CompanySuperviseController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_supervise/ajax_get_areachild', 'Admin\QualityControl\CompanySuperviseController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_supervise/ajax_import_staff','Admin\QualityControl\CompanySuperviseController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_supervise/import', 'Admin\QualityControl\CompanySuperviseController@import');// 导入excel
        $api->post('admin/company_supervise/ajax_get_ids', 'Admin\QualityControl\CompanySuperviseController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 机构自我声明
        $api->any('admin/company_statement/ajax_alist', 'Admin\QualityControl\CompanyStatementController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_statement/ajax_del', 'Admin\QualityControl\CompanyStatementController@ajax_del');// 删除
        $api->any('admin/company_statement/ajax_save', 'Admin\QualityControl\CompanyStatementController@ajax_save');// 新加/修改
        $api->any('admin/company_statement/ajax_info', 'Admin\QualityControl\CompanyStatementController@ajax_info');// 详情
        $api->post('admin/company_statement/ajax_get_child', 'Admin\QualityControl\CompanyStatementController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_statement/ajax_get_areachild', 'Admin\QualityControl\CompanyStatementController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_statement/ajax_import_staff','Admin\QualityControl\CompanyStatementController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_statement/import', 'Admin\QualityControl\CompanyStatementController@import');// 导入excel
        $api->post('admin/company_statement/ajax_get_ids', 'Admin\QualityControl\CompanyStatementController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/company_statement/up_file', 'Admin\QualityControl\CompanyStatementController@up_file');// 上传文件地址

        // 能力验证
        $api->any('admin/company_ability/ajax_alist', 'Admin\QualityControl\CompanyAbilityController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_ability/ajax_del', 'Admin\QualityControl\CompanyAbilityController@ajax_del');// 删除
        $api->post('admin/company_ability/ajax_save', 'Admin\QualityControl\CompanyAbilityController@ajax_save');// 新加/修改
        $api->post('admin/company_ability/ajax_get_child', 'Admin\QualityControl\CompanyAbilityController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_ability/ajax_get_areachild', 'Admin\QualityControl\CompanyAbilityController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_ability/ajax_import_staff','Admin\QualityControl\CompanyAbilityController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_ability/import', 'Admin\QualityControl\CompanyAbilityController@import');// 导入excel
        $api->post('admin/company_ability/ajax_get_ids', 'Admin\QualityControl\CompanyAbilityController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/company_ability/up_excel', 'Admin\QualityControl\CompanyAbilityController@up_excel');// 上传excel地址
        $api->post('admin/company_ability/ajax_excel_save', 'Admin\QualityControl\CompanyAbilityController@ajax_excel_save');// 上传excel--导入保存

        // 监督检查
        $api->any('admin/company_inspect/ajax_alist', 'Admin\QualityControl\CompanyInspectController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_inspect/ajax_del', 'Admin\QualityControl\CompanyInspectController@ajax_del');// 删除
        $api->post('admin/company_inspect/ajax_save', 'Admin\QualityControl\CompanyInspectController@ajax_save');// 新加/修改
        $api->post('admin/company_inspect/ajax_get_child', 'Admin\QualityControl\CompanyInspectController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_inspect/ajax_get_areachild', 'Admin\QualityControl\CompanyInspectController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_inspect/ajax_import_staff','Admin\QualityControl\CompanyInspectController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_inspect/import', 'Admin\QualityControl\CompanyInspectController@import');// 导入excel
        $api->post('admin/company_inspect/ajax_get_ids', 'Admin\QualityControl\CompanyInspectController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业其它【新闻】
        $api->any('admin/company_news/ajax_alist', 'Admin\QualityControl\CompanyNewsController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_news/ajax_del', 'Admin\QualityControl\CompanyNewsController@ajax_del');// 删除
        $api->post('admin/company_news/ajax_save', 'Admin\QualityControl\CompanyNewsController@ajax_save');// 新加/修改
        $api->post('admin/company_news/ajax_get_child', 'Admin\QualityControl\CompanyNewsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_news/ajax_get_areachild', 'Admin\QualityControl\CompanyNewsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_news/ajax_import_staff','Admin\QualityControl\CompanyNewsController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_news/import', 'Admin\QualityControl\CompanyNewsController@import');// 导入excel
        $api->post('admin/company_news/ajax_get_ids', 'Admin\QualityControl\CompanyNewsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 机构处罚
        $api->any('admin/company_punish/ajax_alist', 'Admin\QualityControl\CompanyPunishController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_punish/ajax_del', 'Admin\QualityControl\CompanyPunishController@ajax_del');// 删除
        $api->any('admin/company_punish/ajax_save', 'Admin\QualityControl\CompanyPunishController@ajax_save');// 新加/修改
        $api->any('admin/company_punish/ajax_info', 'Admin\QualityControl\CompanyPunishController@ajax_info');// 详情
        $api->post('admin/company_punish/ajax_get_child', 'Admin\QualityControl\CompanyPunishController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_punish/ajax_get_areachild', 'Admin\QualityControl\CompanyPunishController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_punish/ajax_import_staff','Admin\QualityControl\CompanyPunishController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_punish/import', 'Admin\QualityControl\CompanyPunishController@import');// 导入excel
        $api->post('admin/company_punish/ajax_get_ids', 'Admin\QualityControl\CompanyPunishController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/company_punish/up_file', 'Admin\QualityControl\CompanyPunishController@up_file');// 上传文件地址

        // 通知公告
        $api->any('admin/platform_notices/ajax_alist', 'Admin\QualityControl\PlatformNoticesController@ajax_alist');//ajax获得列表数据
        $api->post('admin/platform_notices/ajax_del', 'Admin\QualityControl\PlatformNoticesController@ajax_del');// 删除
        $api->any('admin/platform_notices/ajax_save', 'Admin\QualityControl\PlatformNoticesController@ajax_save');// 新加/修改
        $api->any('admin/platform_notices/ajax_info', 'Admin\QualityControl\PlatformNoticesController@ajax_info');// 详情
        $api->post('admin/platform_notices/ajax_get_child', 'Admin\QualityControl\PlatformNoticesController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/platform_notices/ajax_get_areachild', 'Admin\QualityControl\PlatformNoticesController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/platform_notices/ajax_import_staff','Admin\QualityControl\PlatformNoticesController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/platform_notices/import', 'Admin\QualityControl\PlatformNoticesController@import');// 导入excel
        $api->post('admin/platform_notices/ajax_get_ids', 'Admin\QualityControl\PlatformNoticesController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/platform_notices/up_file', 'Admin\QualityControl\PlatformNoticesController@up_file');// 上传文件地址

        // 表格下载
        $api->any('admin/platform_down_files/ajax_alist', 'Admin\QualityControl\PlatformDownFilesController@ajax_alist');//ajax获得列表数据
        $api->post('admin/platform_down_files/ajax_del', 'Admin\QualityControl\PlatformDownFilesController@ajax_del');// 删除
        $api->any('admin/platform_down_files/ajax_save', 'Admin\QualityControl\PlatformDownFilesController@ajax_save');// 新加/修改
        $api->any('admin/platform_down_files/ajax_info', 'Admin\QualityControl\PlatformDownFilesController@ajax_info');// 详情
        $api->post('admin/platform_down_files/ajax_get_child', 'Admin\QualityControl\PlatformDownFilesController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/platform_down_files/ajax_get_areachild', 'Admin\QualityControl\PlatformDownFilesController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/platform_down_files/ajax_import_staff','Admin\QualityControl\PlatformDownFilesController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/platform_down_files/import', 'Admin\QualityControl\PlatformDownFilesController@import');// 导入excel
        $api->post('admin/platform_down_files/ajax_get_ids', 'Admin\QualityControl\PlatformDownFilesController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/platform_down_files/up_file', 'Admin\QualityControl\PlatformDownFilesController@up_file');// 上传文件地址

        // 在线考试
        // 试题分类[一级分类]
        $api->any('admin/company_subject_type/ajax_alist', 'Admin\QualityControl\CompanySubjectTypeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_subject_type/ajax_del', 'Admin\QualityControl\CompanySubjectTypeController@ajax_del');// 删除
        $api->any('admin/company_subject_type/ajax_save', 'Admin\QualityControl\CompanySubjectTypeController@ajax_save');// 新加/修改
        $api->post('admin/company_subject_type/ajax_get_child', 'Admin\QualityControl\CompanySubjectTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_subject_type/ajax_get_areachild', 'Admin\QualityControl\CompanySubjectTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_subject_type/ajax_import_staff','Admin\QualityControl\CompanySubjectTypeController@ajax_import'); // 导入员工
//        $api->post('admin/company_subject_type/ajax_sms_send', 'Admin\QualityControl\CompanySubjectTypeController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_subject_type/import', 'Admin\QualityControl\CompanySubjectTypeController@import');// 导入excel
        $api->post('admin/company_subject_type/ajax_get_ids', 'Admin\QualityControl\CompanySubjectTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 试题
        $api->any('admin/company_subject/ajax_alist', 'Admin\QualityControl\CompanySubjectController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_subject/ajax_del', 'Admin\QualityControl\CompanySubjectController@ajax_del');// 删除
        $api->post('admin/company_subject/ajax_save', 'Admin\QualityControl\CompanySubjectController@ajax_save');// 新加/修改
        $api->post('admin/company_subject/ajax_get_child', 'Admin\QualityControl\CompanySubjectController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_subject/ajax_get_areachild', 'Admin\QualityControl\CompanySubjectController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_subject/ajax_import_staff','Admin\QualityControl\CompanySubjectController@ajax_import'); // 导入员工
//        $api->post('admin/company_subject/ajax_sms_send', 'Admin\QualityControl\CompanySubjectController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_subject/import', 'Admin\QualityControl\CompanySubjectController@import');// 导入excel
        $api->post('admin/company_subject/ajax_get_ids', 'Admin\QualityControl\CompanySubjectController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 试卷
        $api->any('admin/company_paper/ajax_alist', 'Admin\QualityControl\CompanyPaperController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_paper/ajax_del', 'Admin\QualityControl\CompanyPaperController@ajax_del');// 删除
        $api->post('admin/company_paper/ajax_save', 'Admin\QualityControl\CompanyPaperController@ajax_save');// 新加/修改
        $api->post('admin/company_paper/ajax_get_child', 'Admin\QualityControl\CompanyPaperController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_paper/ajax_get_areachild', 'Admin\QualityControl\CompanyPaperController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_paper/ajax_import_staff','Admin\QualityControl\CompanyPaperController@ajax_import'); // 导入员工
//        $api->post('admin/company_paper/ajax_sms_send', 'Admin\QualityControl\CompanyPaperController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_paper/import', 'Admin\QualityControl\CompanyPaperController@import');// 导入excel
        $api->post('admin/company_paper/ajax_get_ids', 'Admin\QualityControl\CompanyPaperController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 分数等级
        $api->any('admin/company_core_grade/ajax_alist', 'Admin\QualityControl\CompanyCoreGradeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_core_grade/ajax_del', 'Admin\QualityControl\CompanyCoreGradeController@ajax_del');// 删除
        $api->post('admin/company_core_grade/ajax_save', 'Admin\QualityControl\CompanyCoreGradeController@ajax_save');// 新加/修改
        $api->post('admin/company_core_grade/ajax_get_child', 'Admin\QualityControl\CompanyCoreGradeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_core_grade/ajax_get_areachild', 'Admin\QualityControl\CompanyCoreGradeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_core_grade/ajax_import_staff','Admin\QualityControl\CompanyCoreGradeController@ajax_import'); // 导入员工
//        $api->post('admin/company_core_grade/ajax_sms_send', 'Admin\QualityControl\CompanyCoreGradeController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_core_grade/import', 'Admin\QualityControl\CompanyCoreGradeController@import');// 导入excel
        $api->post('admin/company_core_grade/ajax_get_ids', 'Admin\QualityControl\CompanyCoreGradeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 考次
        $api->any('admin/company_exam/ajax_alist', 'Admin\QualityControl\CompanyExamController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_exam/ajax_del', 'Admin\QualityControl\CompanyExamController@ajax_del');// 删除
        $api->post('admin/company_exam/ajax_save', 'Admin\QualityControl\CompanyExamController@ajax_save');// 新加/修改
        $api->post('admin/company_exam/ajax_get_child', 'Admin\QualityControl\CompanyExamController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_exam/ajax_get_areachild', 'Admin\QualityControl\CompanyExamController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_exam/ajax_import_staff','Admin\QualityControl\CompanyExamController@ajax_import'); // 导入员工
//        $api->post('admin/company_exam/ajax_sms_send', 'Admin\QualityControl\CompanyExamController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_exam/import', 'Admin\QualityControl\CompanyExamController@import');// 导入excel
        $api->post('admin/company_exam/ajax_get_ids', 'Admin\QualityControl\CompanyExamController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 考次的人员
        $api->any('admin/company_exam_staff/ajax_alist', 'Admin\QualityControl\CompanyExamStaffController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_exam_staff/ajax_del', 'Admin\QualityControl\CompanyExamStaffController@ajax_del');// 删除
        $api->post('admin/company_exam_staff/ajax_save', 'Admin\QualityControl\CompanyExamStaffController@ajax_save');// 新加/修改
        $api->post('admin/company_exam_staff/ajax_get_child', 'Admin\QualityControl\CompanyExamStaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_exam_staff/ajax_get_areachild', 'Admin\QualityControl\CompanyExamStaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_exam_staff/ajax_import_staff','Admin\QualityControl\CompanyExamStaffController@ajax_import'); // 导入员工
//        $api->post('admin/company_exam_staff/ajax_sms_send', 'Admin\QualityControl\CompanyExamStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_exam_staff/import', 'Admin\QualityControl\CompanyExamStaffController@import');// 导入excel
        $api->post('admin/company_exam_staff/ajax_get_ids', 'Admin\QualityControl\CompanyExamStaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 考次的人员试题答案
        $api->any('admin/company_exam_staff_subject/ajax_alist', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_exam_staff_subject/ajax_del', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_del');// 删除
        $api->post('admin/company_exam_staff_subject/ajax_save', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_save');// 新加/修改
        $api->post('admin/company_exam_staff_subject/ajax_get_child', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_exam_staff_subject/ajax_get_areachild', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_exam_staff_subject/ajax_import_staff','Admin\QualityControl\CompanyExamStaffSubjectController@ajax_import'); // 导入员工
//        $api->post('admin/company_exam_staff_subject/ajax_sms_send', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/company_exam_staff_subject/import', 'Admin\QualityControl\CompanyExamStaffSubjectController@import');// 导入excel
        $api->post('admin/company_exam_staff_subject/ajax_get_ids', 'Admin\QualityControl\CompanyExamStaffSubjectController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 付款/收款相关的
        // 付款/收款类型
        $api->any('admin/payment_type/ajax_alist', 'Admin\QualityControl\PaymentTypeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/payment_type/ajax_del', 'Admin\QualityControl\PaymentTypeController@ajax_del');// 删除
        $api->any('admin/payment_type/ajax_save', 'Admin\QualityControl\PaymentTypeController@ajax_save');// 新加/修改
        $api->post('admin/payment_type/ajax_get_child', 'Admin\QualityControl\PaymentTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_type/ajax_get_areachild', 'Admin\QualityControl\PaymentTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_type/ajax_import_staff','Admin\QualityControl\PaymentTypeController@ajax_import'); // 导入员工
//        $api->post('admin/payment_type/ajax_sms_send', 'Admin\QualityControl\PaymentTypeController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/payment_type/import', 'Admin\QualityControl\PaymentTypeController@import');// 导入excel
        $api->post('admin/payment_type/ajax_get_ids', 'Admin\QualityControl\PaymentTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 付款/收款项目
        $api->any('admin/payment_project/ajax_alist', 'Admin\QualityControl\PaymentProjectController@ajax_alist');//ajax获得列表数据
        $api->post('admin/payment_project/ajax_del', 'Admin\QualityControl\PaymentProjectController@ajax_del');// 删除
        $api->any('admin/payment_project/ajax_save', 'Admin\QualityControl\PaymentProjectController@ajax_save');// 新加/修改
        $api->post('admin/payment_project/ajax_get_child', 'Admin\QualityControl\PaymentProjectController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_project/ajax_get_areachild', 'Admin\QualityControl\PaymentProjectController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_project/ajax_import_staff','Admin\QualityControl\PaymentProjectController@ajax_import'); // 导入员工
//        $api->post('admin/payment_project/ajax_sms_send', 'Admin\QualityControl\PaymentProjectController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/payment_project/import', 'Admin\QualityControl\PaymentProjectController@import');// 导入excel
        $api->post('admin/payment_project/ajax_get_ids', 'Admin\QualityControl\PaymentProjectController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->post('admin/payment_project/up_file', 'Admin\QualityControl\PaymentProjectController@up_file');// 上传文件地址
        $api->any('admin/payment_project/ajax_pay_save', 'Admin\QualityControl\PaymentProjectController@ajax_pay_save');// 付款

        // 付款/收款记录
        $api->any('admin/payment_record/ajax_alist', 'Admin\QualityControl\PaymentRecordController@ajax_alist');//ajax获得列表数据
        $api->post('admin/payment_record/ajax_del', 'Admin\QualityControl\PaymentRecordController@ajax_del');// 删除
        $api->any('admin/payment_record/ajax_save', 'Admin\QualityControl\PaymentRecordController@ajax_save');// 新加/修改
        $api->post('admin/payment_record/ajax_get_child', 'Admin\QualityControl\PaymentRecordController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_record/ajax_get_areachild', 'Admin\QualityControl\PaymentRecordController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_record/ajax_import_staff','Admin\QualityControl\PaymentRecordController@ajax_import'); // 导入员工
//        $api->post('admin/payment_record/ajax_sms_send', 'Admin\QualityControl\PaymentRecordController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/payment_record/import', 'Admin\QualityControl\PaymentRecordController@import');// 导入excel
        $api->post('admin/payment_record/ajax_get_ids', 'Admin\QualityControl\PaymentRecordController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->any('admin/payment_record/ajax_create_order', 'Admin\QualityControl\PaymentRecordController@ajax_create_order');// 缴费生成订单

        // 付款/收款记录流水
        $api->any('admin/payment_record_flow/ajax_alist', 'Admin\QualityControl\PaymentRecordFlowController@ajax_alist');//ajax获得列表数据
        $api->post('admin/payment_record_flow/ajax_del', 'Admin\QualityControl\PaymentRecordFlowController@ajax_del');// 删除
        $api->any('admin/payment_record_flow/ajax_save', 'Admin\QualityControl\PaymentRecordFlowController@ajax_save');// 新加/修改
        $api->post('admin/payment_record_flow/ajax_get_child', 'Admin\QualityControl\PaymentRecordFlowController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_record_flow/ajax_get_areachild', 'Admin\QualityControl\PaymentRecordFlowController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_record_flow/ajax_import_staff','Admin\QualityControl\PaymentRecordFlowController@ajax_import'); // 导入员工
//        $api->post('admin/payment_record_flow/ajax_sms_send', 'Admin\QualityControl\PaymentRecordFlowController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/payment_record_flow/import', 'Admin\QualityControl\PaymentRecordFlowController@import');// 导入excel
        $api->post('admin/payment_record_flow/ajax_get_ids', 'Admin\QualityControl\PaymentRecordFlowController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 付款/收款记录操作日志
        $api->any('admin/payment_record_log/ajax_alist', 'Admin\QualityControl\PaymentRecordLogController@ajax_alist');//ajax获得列表数据
        $api->post('admin/payment_record_log/ajax_del', 'Admin\QualityControl\PaymentRecordLogController@ajax_del');// 删除
        $api->any('admin/payment_record_log/ajax_save', 'Admin\QualityControl\PaymentRecordLogController@ajax_save');// 新加/修改
        $api->post('admin/payment_record_log/ajax_get_child', 'Admin\QualityControl\PaymentRecordLogController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_record_log/ajax_get_areachild', 'Admin\QualityControl\PaymentRecordLogController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/payment_record_log/ajax_import_staff','Admin\QualityControl\PaymentRecordLogController@ajax_import'); // 导入员工
//        $api->post('admin/payment_record_log/ajax_sms_send', 'Admin\QualityControl\PaymentRecordLogController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/payment_record_log/import', 'Admin\QualityControl\PaymentRecordLogController@import');// 导入excel
        $api->post('admin/payment_record_log/ajax_get_ids', 'Admin\QualityControl\PaymentRecordLogController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


        // 对外提供接口

        // 证书-能力范围
        $api->any('market/certificate_schedule/list', 'Admin\QualityControl\API\CertificateScheduleController@ajax_alist');//ajax获得列表数据
        $api->any('market/certificate_schedule/ajax_alist', 'Admin\QualityControl\API\CertificateScheduleController@ajax_alist_api');//ajax获得列表数据--接口
//        $api->post('market/certificate_schedule/del', 'Admin\QualityControl\API\CertificateScheduleController@ajax_del');// 删除
//        $api->any('market/certificate_schedule/save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_save');// 新加/修改
        $api->any('market/certificate_schedule/info', 'Admin\QualityControl\API\CertificateScheduleController@ajax_info');// 详情
        $api->post('market/certificate_schedule/ajax_get_child', 'Admin\QualityControl\API\CertificateScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('market/certificate_schedule/ajax_get_areachild', 'Admin\QualityControl\API\CertificateScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('market/certificate_schedule/ajax_import_staff','Admin\QualityControl\API\CertificateScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('market/certificate_schedule/import', 'Admin\QualityControl\API\CertificateScheduleController@import');// 导入excel
        $api->post('market/certificate_schedule/ajax_get_ids', 'Admin\QualityControl\API\CertificateScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('market/certificate_schedule/up_excel', 'Admin\QualityControl\API\CertificateScheduleController@up_excel');// 上传excel地址
        $api->post('market/certificate_schedule/ajax_excel_save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_excel_save');// 上传excel--导入保存

        $api->post('market/certificate_schedule/bath_save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_bath_save');// 批量保存
        $api->post('market/certificate_schedule/files_save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_files_save');// 文件接口保存
        $api->post('market/certificate_schedule/company_modify', 'Admin\QualityControl\API\CertificateScheduleController@ajax_company_save');// 注册/修改企业信息接口保存
        $api->post('market/certificate_schedule/bath_modify', 'Admin\QualityControl\API\CertificateScheduleController@ajax_bath_modify');// 能力范围删除或新加-修改接口保存
        $api->post('market/certificate_schedule/update', 'Admin\QualityControl\API\CertificateScheduleController@ajax_update');// 根据条件修改能力范围

        // 证书-能力范围-页面调用
        $api->any('admin/API/certificate_schedule/ajax_alist', 'Admin\QualityControl\API\CertificateScheduleController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/API/certificate_schedule/ajax_del', 'Admin\QualityControl\API\CertificateScheduleController@ajax_del');// 删除
//        $api->any('admin/API/certificate_schedule/ajax_save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_save');// 新加/修改
        $api->any('admin/API/certificate_schedule/ajax_info', 'Admin\QualityControl\API\CertificateScheduleController@ajax_info');// 详情
        $api->post('admin/API/certificate_schedule/ajax_get_child', 'Admin\QualityControl\API\CertificateScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/API/certificate_schedule/ajax_get_areachild', 'Admin\QualityControl\API\CertificateScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/API/certificate_schedule/ajax_import_staff','Admin\QualityControl\API\CertificateScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('admin/API/certificate_schedule/import', 'Admin\QualityControl\API\CertificateScheduleController@import');// 导入excel
        $api->post('admin/API/certificate_schedule/ajax_get_ids', 'Admin\QualityControl\API\CertificateScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/API/certificate_schedule/up_excel', 'Admin\QualityControl\API\CertificateScheduleController@up_excel');// 上传excel地址
        $api->post('admin/API/certificate_schedule/ajax_excel_save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_excel_save');// 上传excel--导入保存
        $api->post('admin/API/certificate_schedule/ajax_bath_save', 'Admin\QualityControl\API\CertificateScheduleController@ajax_bath_save');// 批量保存

        // 企业后台 company
        // 验证码 -- ok
//        $api->get('company/ajax_captcha', 'WebFront\Company\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('company/ajax_captcha_verify', 'WebFront\Company\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('company/ajax_captcha', 'WebFront\Company\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('company/ajax_captcha_verify', 'WebFront\Company\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码 -- ok
        $api->any('company/ajax_send_mobile_vercode', 'WebFront\Company\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('company/ajax_mobile_code_verify', 'WebFront\Company\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
        $api->any('company/ajax_login', 'WebFront\Company\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('company/ajax_login_sms', 'WebFront\Company\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('company/ajax_password_save', 'WebFront\Company\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('company/ajax_info_save', 'WebFront\Company\QualityControl\IndexController@ajax_info_save');// 修改设置--ok
        $api->any('company/ajax_basic_save', 'WebFront\Company\QualityControl\IndexController@ajax_basic_save');// 修改基本信息设置--ok

        // 上传图片
        $api->post('company/upload', 'WebFront\Company\QualityControl\UploadController@index');
        $api->post('company/upload/ajax_del', 'WebFront\Company\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 个人帐号管理
        $api->any('company/user/ajax_alist', 'WebFront\Company\QualityControl\UserController@ajax_alist');//ajax获得列表数据
        $api->any('company/user/ajax_del', 'WebFront\Company\QualityControl\UserController@ajax_del');// 删除
        $api->post('company/user/ajax_save', 'WebFront\Company\QualityControl\UserController@ajax_save');// 新加/修改
        $api->post('company/user/ajax_get_child', 'WebFront\Company\QualityControl\UserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('company/user/ajax_get_areachild', 'WebFront\Company\QualityControl\UserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('company/user/ajax_import_staff','WebFront\Company\QualityControl\UserController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->any('company/user/import', 'WebFront\Company\QualityControl\UserController@import');// 导入excel
        $api->post('company/user/ajax_get_ids', 'WebFront\Company\QualityControl\UserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('company/user/ajax_open', 'WebFront\Company\QualityControl\UserController@ajax_open');// 审核操作(通过/不通过)
        $api->post('company/user/ajax_frozen', 'WebFront\Company\QualityControl\UserController@ajax_frozen');// 操作(冻结/解冻)

        $api->post('company/user/up_file', 'WebFront\Company\QualityControl\UserController@up_file');// 上传文件地址

        // 企业内容管理
        $api->any('company/company_content/ajax_alist', 'WebFront\Company\QualityControl\CompanyContentController@ajax_alist');//ajax获得列表数据
        $api->any('company/company_content/ajax_del', 'WebFront\Company\QualityControl\CompanyContentController@ajax_del');// 删除
        $api->any('company/company_content/ajax_save', 'WebFront\Company\QualityControl\CompanyContentController@ajax_save');// 新加/修改
        $api->post('company/company_content/ajax_get_child', 'WebFront\Company\QualityControl\CompanyContentController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('company/company_content/ajax_get_areachild', 'WebFront\Company\QualityControl\CompanyContentController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('company/company_content/ajax_import_staff','WebFront\Company\QualityControl\CompanyContentController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('company/company_content/import', 'WebFront\Company\QualityControl\CompanyContentController@import');// 导入excel
        $api->post('company/company_content/ajax_get_ids', 'WebFront\Company\QualityControl\CompanyContentController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证
        $api->any('company/abilitys/ajax_alist', 'WebFront\Company\QualityControl\AbilitysController@ajax_alist');//ajax获得列表数据
//        $api->post('company/abilitys/ajax_join_save', 'WebFront\Company\QualityControl\AbilitysController@ajax_join_save');// 报名
        $api->any('company/abilitys/ajax_new_join_save', 'WebFront\Company\QualityControl\AbilitysController@ajax_new_join_save');// 报名--新版
        $api->any('company/abilitys/ajax_company_extend', 'WebFront\Company\QualityControl\AbilitysController@ajax_company_extend');// 获得企业扩展信息
        $api->any('company/abilitys/ajax_schedule_num', 'WebFront\Company\QualityControl\AbilitysController@ajax_schedule_num');// 获得企业上传的能力附表pdf数量
//        $api->post('company/abilitys/ajax_del', 'WebFront\Company\QualityControl\AbilitysController@ajax_del');// 删除
//        $api->post('company/abilitys/ajax_save', 'WebFront\Company\QualityControl\AbilitysController@ajax_save');// 新加/修改
//        $api->post('company/abilitys/ajax_get_child', 'WebFront\Company\QualityControl\AbilitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/abilitys/ajax_get_areachild', 'WebFront\Company\QualityControl\AbilitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/abilitys/ajax_import_staff','WebFront\Company\QualityControl\AbilitysController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信
//
//        $api->post('company/abilitys/import', 'WebFront\Company\QualityControl\AbilitysController@import');// 导入excel
//        $api->post('company/abilitys/ajax_get_ids', 'WebFront\Company\QualityControl\AbilitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证--报名管理
        $api->any('company/ability_join/ajax_alist', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_alist');//ajax获得列表数据
//        $api->post('company/ability_join/ajax_del', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_del');// 删除
//        $api->post('company/ability_join/ajax_save', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_save');// 新加/修改
//        $api->post('company/ability_join/ajax_get_child', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join/ajax_get_areachild', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join/ajax_import_staff','WebFront\Company\QualityControl\AbilityJoinController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/ability_join/import', 'WebFront\Company\QualityControl\AbilityJoinController@import');// 导入excel
//        $api->post('company/ability_join/ajax_get_ids', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证--项目管理
        $api->any('company/ability_join_item/ajax_alist', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_alist');//ajax获得列表数据
//        $api->post('company/ability_join_item/ajax_del', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_del');// 删除
//        $api->post('company/ability_join_item/ajax_save', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_save');// 新加/修改
//        $api->post('company/ability_join_item/ajax_get_child', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join_item/ajax_get_areachild', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join_item/ajax_import_staff','WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/ability_join_item/import', 'WebFront\Company\QualityControl\AbilityJoinItemsController@import');// 导入excel
//        $api->post('company/ability_join_item/ajax_get_ids', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('company/ability_join_item/ajax_save_result_sample', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_save_result_sample');// 新加/修改 -- 数据提交
        $api->post('company/ability_join_item/up_pdf', 'WebFront\Company\QualityControl\AbilityJoinItemsController@up_pdf');// 上传pdf地址
        // 企业能力附表
        $api->any('company/company_schedule/ajax_alist', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('company/company_schedule/ajax_del', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_del');// 删除
//        $api->post('company/company_schedule/ajax_save', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_save');// 新加/修改
//        $api->post('company/company_schedule/ajax_get_child', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/company_schedule/ajax_get_areachild', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/company_schedule/ajax_import_staff','WebFront\Company\QualityControl\CompanyScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('company/company_schedule/import', 'WebFront\Company\QualityControl\CompanyScheduleController@import');// 导入excel
//        $api->post('company/company_schedule/ajax_get_ids', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业能力附表
        $api->any('company/company_new_schedule/ajax_alist', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('company/company_new_schedule/ajax_del', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_del');// 删除
        $api->post('company/company_new_schedule/ajax_save', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_save');// 新加/修改
        $api->any('company/company_new_schedule/ajax_excel_save', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_excel_save');// 上传excel
//        $api->post('company/company_new_schedule/ajax_get_child', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/company_new_schedule/ajax_get_areachild', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/company_new_schedule/ajax_import_staff','WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('company/company_new_schedule/import', 'WebFront\Company\QualityControl\CompanyNewScheduleController@import');// 导入excel
//        $api->post('company/company_new_schedule/ajax_get_ids', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('company/company_new_schedule/up_word', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_word');// 上传word地址
        $api->post('company/company_new_schedule/up_pdf', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_pdf');// 上传pdf地址
        $api->post('company/company_new_schedule/up_img', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_img');// 上传图片地址
        $api->post('company/company_new_schedule/up_excel', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_excel');// 上传excel地址

        // 课程管理
        $api->any('company/course/ajax_alist', 'WebFront\Company\QualityControl\CourseController@ajax_alist');//ajax获得列表数据
//        $api->post('company/course/ajax_del', 'WebFront\Company\QualityControl\CourseController@ajax_del');// 删除
//        $api->any('company/course/ajax_save', 'WebFront\Company\QualityControl\CourseController@ajax_save');// 新加/修改
//        $api->post('company/course/ajax_get_child', 'WebFront\Company\QualityControl\CourseController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/course/ajax_get_areachild', 'WebFront\Company\QualityControl\CourseController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/course/ajax_import_staff','WebFront\Company\QualityControl\CourseController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/course/import', 'WebFront\Company\QualityControl\CourseController@import');// 导入excel
//        $api->post('company/course/ajax_get_ids', 'WebFront\Company\QualityControl\CourseController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//        $api->post('company/course/up_file', 'WebFront\Company\QualityControl\CourseController@up_file');// 上传文件地址
        $api->any('company/course/ajax_join_save', 'WebFront\Company\QualityControl\CourseController@ajax_join_save');// 报名
        $api->any('company/course/ajax_add_user', 'WebFront\Company\QualityControl\CourseController@ajax_add_user');// ajax添加人员地址
        // 报名企业(主表)
        $api->any('company/course_order/ajax_alist', 'WebFront\Company\QualityControl\CourseOrderController@ajax_alist');//ajax获得列表数据
//        $api->post('company/course_order/ajax_del', 'WebFront\Company\QualityControl\CourseOrderController@ajax_del');// 删除
//        $api->post('company/course_order/ajax_save', 'WebFront\Company\QualityControl\CourseOrderController@ajax_save');// 新加/修改
//        $api->post('company/course_order/ajax_get_child', 'WebFront\Company\QualityControl\CourseOrderController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/course_order/ajax_get_areachild', 'WebFront\Company\QualityControl\CourseOrderController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/course_order/ajax_import_staff','WebFront\Company\QualityControl\CourseOrderController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/course_order/import', 'WebFront\Company\QualityControl\CourseOrderController@import');// 导入excel
//        $api->post('company/course_order/ajax_get_ids', 'WebFront\Company\QualityControl\CourseOrderController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 报名学员
        $api->any('company/course_order_staff/ajax_alist', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_alist');//ajax获得列表数据
//        $api->post('company/course_order_staff/ajax_del', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_del');// 删除
//        $api->post('company/course_order_staff/ajax_save', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_save');// 新加/修改
//        $api->post('company/course_order_staff/ajax_get_child', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/course_order_staff/ajax_get_areachild', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/course_order_staff/ajax_import_staff','WebFront\Company\QualityControl\CourseOrderStaffController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信
//
//        $api->post('company/course_order_staff/import', 'WebFront\Company\QualityControl\CourseOrderStaffController@import');// 导入excel
//        $api->post('company/course_order_staff/ajax_get_ids', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//        $api->post('company/course_order_staff/ajax_frozen', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_frozen');// 操作(作废/取消作废)
//        $api->post('company/course_order_staff/ajax_join_class_save', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_join_class_save');// 分班
//        $api->post('company/course_order_staff/ajax_cancel_class', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_cancel_class');// 取消分班
        $api->any('company/course_order_staff/ajax_create_order', 'WebFront\Company\QualityControl\CourseOrderStaffController@ajax_create_order');// 缴费生成订单

        // 收款订单
        $api->any('company/orders/ajax_alist', 'WebFront\Company\QualityControl\OrdersController@ajax_alist');//ajax获得列表数据
//        $api->post('company/orders/ajax_del', 'WebFront\Company\QualityControl\OrdersController@ajax_del');// 删除
//        $api->post('company/orders/ajax_save', 'WebFront\Company\QualityControl\OrdersController@ajax_save');// 新加/修改
//        $api->post('company/orders/ajax_get_child', 'WebFront\Company\QualityControl\OrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/orders/ajax_get_areachild', 'WebFront\Company\QualityControl\OrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/orders/ajax_import_staff','WebFront\Company\QualityControl\OrdersController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/orders/import', 'WebFront\Company\QualityControl\OrdersController@import');// 导入excel
//        $api->post('company/orders/ajax_get_ids', 'WebFront\Company\QualityControl\OrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->post('company/orders/ajax_invoices_save', 'WebFront\Company\QualityControl\OrdersController@ajax_invoices_save');// 操作(开电子发票--蓝票)

        // 收款订单财务流水
        $api->any('company/order_flow/ajax_alist', 'WebFront\Company\QualityControl\OrderFlowController@ajax_alist');//ajax获得列表数据
//        $api->post('company/order_flow/ajax_del', 'WebFront\Company\QualityControl\OrderFlowController@ajax_del');// 删除
//        $api->post('company/order_flow/ajax_save', 'WebFront\Company\QualityControl\OrderFlowController@ajax_save');// 新加/修改
//        $api->post('company/order_flow/ajax_get_child', 'WebFront\Company\QualityControl\OrderFlowController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/order_flow/ajax_get_areachild', 'WebFront\Company\QualityControl\OrderFlowController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/order_flow/ajax_import_staff','WebFront\Company\QualityControl\OrderFlowController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/order_flow/import', 'WebFront\Company\QualityControl\OrderFlowController@import');// 导入excel
//        $api->post('company/order_flow/ajax_get_ids', 'WebFront\Company\QualityControl\OrderFlowController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 第三方对帐单
        $api->any('company/order_pay/ajax_alist', 'WebFront\Company\QualityControl\OrderPayController@ajax_alist');//ajax获得列表数据
//        $api->post('company/order_pay/ajax_del', 'WebFront\Company\QualityControl\OrderPayController@ajax_del');// 删除
//        $api->post('company/order_pay/ajax_save', 'WebFront\Company\QualityControl\OrderPayController@ajax_save');// 新加/修改
//        $api->post('company/order_pay/ajax_get_child', 'WebFront\Company\QualityControl\OrderPayController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/order_pay/ajax_get_areachild', 'WebFront\Company\QualityControl\OrderPayController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/order_pay/ajax_import_staff','WebFront\Company\QualityControl\OrderPayController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/order_pay/import', 'WebFront\Company\QualityControl\OrderPayController@import');// 导入excel
//        $api->post('company/order_pay/ajax_get_ids', 'WebFront\Company\QualityControl\OrderPayController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('company/order_pay/ajax_wx_query_order', 'WebFront\Company\QualityControl\OrderPayController@ajax_wx_query_order');// ajax查询微信扫码支付是否成功

        // 发票配置购买方
        $api->any('company/invoice_buyer/ajax_alist', 'WebFront\Company\QualityControl\InvoiceBuyerController@ajax_alist');//ajax获得列表数据
        $api->post('company/invoice_buyer/ajax_del', 'WebFront\Company\QualityControl\InvoiceBuyerController@ajax_del');// 删除
        $api->post('company/invoice_buyer/ajax_save', 'WebFront\Company\QualityControl\InvoiceBuyerController@ajax_save');// 新加/修改
        $api->post('company/invoice_buyer/ajax_get_child', 'WebFront\Company\QualityControl\InvoiceBuyerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('company/invoice_buyer/ajax_get_areachild', 'WebFront\Company\QualityControl\InvoiceBuyerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('company/invoice_buyer/ajax_import_staff','WebFront\Company\QualityControl\InvoiceBuyerController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('company/invoice_buyer/import', 'WebFront\Company\QualityControl\InvoiceBuyerController@import');// 导入excel
        $api->post('company/invoice_buyer/ajax_get_ids', 'WebFront\Company\QualityControl\InvoiceBuyerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 发票主表
        $api->any('company/invoices/ajax_alist', 'WebFront\Company\QualityControl\InvoicesController@ajax_alist');//ajax获得列表数据
        $api->post('company/invoices/ajax_del', 'WebFront\Company\QualityControl\InvoicesController@ajax_del');// 删除
        $api->post('company/invoices/ajax_save', 'WebFront\Company\QualityControl\InvoicesController@ajax_save');// 新加/修改
        $api->post('company/invoices/ajax_get_child', 'WebFront\Company\QualityControl\InvoicesController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('company/invoices/ajax_get_areachild', 'WebFront\Company\QualityControl\InvoicesController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('company/invoices/ajax_import_staff','WebFront\Company\QualityControl\InvoicesController@ajax_import'); // 导入员工
//        $api->post('admin/course_order_staff/ajax_sms_send', 'Admin\QualityControl\CourseOrderStaffController@ajax_sms_send');// 短信模板发送短信

        $api->post('company/invoices/import', 'WebFront\Company\QualityControl\InvoicesController@import');// 导入excel
        $api->post('company/invoices/ajax_get_ids', 'WebFront\Company\QualityControl\InvoicesController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 点播课程
        $api->any('company/vods/ajax_alist', 'WebFront\Company\QualityControl\VodsController@ajax_alist');//ajax获得列表数据
//        $api->post('company/vods/ajax_del', 'WebFront\Company\QualityControl\VodsController@ajax_del');// 删除
//        $api->post('company/vods/ajax_save', 'WebFront\Company\QualityControl\VodsController@ajax_save');// 新加/修改
        $api->any('company/vods/ajax_join_save', 'WebFront\Company\QualityControl\VodsController@ajax_join_save');// 报名
//        $api->post('company/vods/ajax_get_child', 'WebFront\Company\QualityControl\VodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/vods/ajax_get_areachild', 'WebFront\Company\QualityControl\VodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/vods/ajax_import_staff','WebFront\Company\QualityControl\VodsController@ajax_import'); // 导入员工
//        $api->post('company/vods/ajax_sms_send', 'WebFront\Company\QualityControl\VodsController@ajax_sms_send');// 短信模板发送短信

//        $api->post('company/vods/import', 'WebFront\Company\QualityControl\VodsController@import');// 导入excel
//        $api->post('company/vods/ajax_get_ids', 'WebFront\Company\QualityControl\VodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//        $api->post('company/vods/up_file', 'WebFront\Company\QualityControl\VodsController@up_file');// 上传文件地址
        // 点播课程订单
        $api->any('company/vod_orders/ajax_alist', 'WebFront\Company\QualityControl\VodOrdersController@ajax_alist');//ajax获得列表数据
//        $api->post('company/vod_orders/ajax_del', 'WebFront\Company\QualityControl\VodOrdersController@ajax_del');// 删除
//        $api->post('company/vod_orders/ajax_save', 'WebFront\Company\QualityControl\VodOrdersController@ajax_save');// 新加/修改
        $api->post('company/vod_orders/ajax_get_child', 'WebFront\Company\QualityControl\VodOrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('company/vod_orders/ajax_get_areachild', 'WebFront\Company\QualityControl\VodOrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('company/vod_orders/ajax_import_staff','WebFront\Company\QualityControl\VodOrdersController@ajax_import'); // 导入员工
//        $api->post('company/vod_orders/ajax_sms_send', 'WebFront\Company\QualityControl\VodOrdersController@ajax_sms_send');// 短信模板发送短信

        $api->post('company/vod_orders/import', 'WebFront\Company\QualityControl\VodOrdersController@import');// 导入excel
        $api->post('company/vod_orders/ajax_get_ids', 'WebFront\Company\QualityControl\VodOrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->any('company/vod_orders/ajax_create_order', 'WebFront\Company\QualityControl\VodOrdersController@ajax_create_order');// 缴费生成订单

        // 用户中心 user
        // 验证码 -- ok
//        $api->get('user/ajax_captcha', 'WebFront\User\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('user/ajax_captcha_verify', 'WebFront\User\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('user/ajax_captcha', 'WebFront\User\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('user/ajax_captcha_verify', 'WebFront\User\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码--注册 -- ok
        $api->any('user/reg/ajax_send_mobile_vercode', 'WebFront\User\QualityControl\SMSRegController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('user/reg/ajax_mobile_code_verify', 'WebFront\User\QualityControl\SMSRegController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        // 手机验证码--登录 -- ok
        $api->any('user/ajax_send_mobile_vercode', 'WebFront\User\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('user/ajax_mobile_code_verify', 'WebFront\User\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
        $api->any('user/ajax_login', 'WebFront\User\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('user/ajax_login_sms', 'WebFront\User\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('user/ajax_password_save', 'WebFront\User\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('user/ajax_info_save', 'WebFront\User\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        // 上传图片
        $api->post('user/upload', 'WebFront\User\QualityControl\UploadController@index');
        $api->post('user/upload/ajax_del', 'WebFront\User\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 点播课程
        $api->any('user/vods/ajax_alist', 'WebFront\User\QualityControl\VodsController@ajax_alist');//ajax获得列表数据
//        $api->post('user/vods/ajax_del', 'WebFront\User\QualityControl\VodsController@ajax_del');// 删除
//        $api->post('user/vods/ajax_save', 'WebFront\User\QualityControl\VodsController@ajax_save');// 新加/修改
        $api->any('user/vods/ajax_join_save', 'WebFront\User\QualityControl\VodsController@ajax_join_save');// 报名
//        $api->post('user/vods/ajax_get_child', 'WebFront\User\QualityControl\VodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('user/vods/ajax_get_areachild', 'WebFront\User\QualityControl\VodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('user/vods/ajax_import_staff','WebFront\User\QualityControl\VodsController@ajax_import'); // 导入员工
//        $api->post('user/vods/ajax_sms_send', 'WebFront\User\QualityControl\VodsController@ajax_sms_send');// 短信模板发送短信

//        $api->post('user/vods/import', 'WebFront\User\QualityControl\VodsController@import');// 导入excel
//        $api->post('user/vods/ajax_get_ids', 'WebFront\User\QualityControl\VodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//        $api->post('user/vods/up_file', 'WebFront\User\QualityControl\VodsController@up_file');// 上传文件地址
        // 点播课程订单
        $api->any('user/vod_orders/ajax_alist', 'WebFront\User\QualityControl\VodOrdersController@ajax_alist');//ajax获得列表数据
//        $api->post('user/vod_orders/ajax_del', 'WebFront\User\QualityControl\VodOrdersController@ajax_del');// 删除
//        $api->post('user/vod_orders/ajax_save', 'WebFront\User\QualityControl\VodOrdersController@ajax_save');// 新加/修改
        $api->post('user/vod_orders/ajax_get_child', 'WebFront\User\QualityControl\VodOrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('user/vod_orders/ajax_get_areachild', 'WebFront\User\QualityControl\VodOrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('user/vod_orders/ajax_import_staff','WebFront\User\QualityControl\VodOrdersController@ajax_import'); // 导入员工
//        $api->post('user/vod_orders/ajax_sms_send', 'WebFront\User\QualityControl\VodOrdersController@ajax_sms_send');// 短信模板发送短信

        $api->post('user/vod_orders/import', 'WebFront\User\QualityControl\VodOrdersController@import');// 导入excel
        $api->post('user/vod_orders/ajax_get_ids', 'WebFront\User\QualityControl\VodOrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->any('user/vod_orders/ajax_create_order', 'WebFront\User\QualityControl\VodOrdersController@ajax_create_order');// 缴费生成订单
        $api->post('user/vod_orders/ajax_wx_query_order', 'WebFront\User\QualityControl\VodOrdersController@ajax_wx_query_order');// ajax查询微信扫码支付是否成功

        // 付款/收款相关的
        // 付款/收款项目
        $api->any('user/payment_project/ajax_alist', 'WebFront\User\QualityControl\PaymentProjectController@ajax_alist');//ajax获得列表数据
        $api->post('user/payment_project/ajax_del', 'WebFront\User\QualityControl\PaymentProjectController@ajax_del');// 删除
        $api->any('user/payment_project/ajax_save', 'WebFront\User\QualityControl\PaymentProjectController@ajax_save');// 新加/修改
        $api->post('user/payment_project/ajax_get_child', 'WebFront\User\QualityControl\PaymentProjectController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('user/payment_project/ajax_get_areachild', 'WebFront\User\QualityControl\PaymentProjectController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('user/payment_project/ajax_import_staff','WebFront\User\QualityControl\PaymentProjectController@ajax_import'); // 导入员工
//        $api->post('user/payment_project/ajax_sms_send', 'WebFront\User\QualityControl\PaymentProjectController@ajax_sms_send');// 短信模板发送短信

        $api->post('user/payment_project/import', 'WebFront\User\QualityControl\PaymentProjectController@import');// 导入excel
        $api->post('user/payment_project/ajax_get_ids', 'WebFront\User\QualityControl\PaymentProjectController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->post('user/payment_project/up_file', 'WebFront\User\QualityControl\PaymentProjectController@up_file');// 上传文件地址
        $api->any('user/payment_project/ajax_pay_save', 'WebFront\User\QualityControl\PaymentProjectController@ajax_pay_save');// 付款

        // 付款/收款记录
        $api->any('user/payment_record/ajax_alist', 'WebFront\User\QualityControl\PaymentRecordController@ajax_alist');//ajax获得列表数据
        $api->post('user/payment_record/ajax_del', 'WebFront\User\QualityControl\PaymentRecordController@ajax_del');// 删除
        $api->any('user/payment_record/ajax_save', 'WebFront\User\QualityControl\PaymentRecordController@ajax_save');// 新加/修改
        $api->post('user/payment_record/ajax_get_child', 'WebFront\User\QualityControl\PaymentRecordController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('user/payment_record/ajax_get_areachild', 'WebFront\User\QualityControl\PaymentRecordController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('user/payment_record/ajax_import_staff','WebFront\User\QualityControl\PaymentRecordController@ajax_import'); // 导入员工
//        $api->post('user/payment_record/ajax_sms_send', 'WebFront\User\QualityControl\PaymentRecordController@ajax_sms_send');// 短信模板发送短信

        $api->post('user/payment_record/import', 'WebFront\User\QualityControl\PaymentRecordController@import');// 导入excel
        $api->post('user/payment_record/ajax_get_ids', 'WebFront\User\QualityControl\PaymentRecordController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->any('user/payment_record/ajax_create_order', 'WebFront\User\QualityControl\PaymentRecordController@ajax_create_order');// 缴费生成订单

        // 付款/收款记录流水
        $api->any('user/payment_record_flow/ajax_alist', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_alist');//ajax获得列表数据
        $api->post('user/payment_record_flow/ajax_del', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_del');// 删除
        $api->any('user/payment_record_flow/ajax_save', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_save');// 新加/修改
        $api->post('user/payment_record_flow/ajax_get_child', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('user/payment_record_flow/ajax_get_areachild', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('user/payment_record_flow/ajax_import_staff','WebFront\User\QualityControl\PaymentRecordFlowController@ajax_import'); // 导入员工
//        $api->post('user/payment_record_flow/ajax_sms_send', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_sms_send');// 短信模板发送短信

        $api->post('user/payment_record_flow/import', 'WebFront\User\QualityControl\PaymentRecordFlowController@import');// 导入excel
        $api->post('user/payment_record_flow/ajax_get_ids', 'WebFront\User\QualityControl\PaymentRecordFlowController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 付款/收款记录操作日志
        $api->any('user/payment_record_log/ajax_alist', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_alist');//ajax获得列表数据
        $api->post('user/payment_record_log/ajax_del', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_del');// 删除
        $api->any('user/payment_record_log/ajax_save', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_save');// 新加/修改
        $api->post('user/payment_record_log/ajax_get_child', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('user/payment_record_log/ajax_get_areachild', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('user/payment_record_log/ajax_import_staff','WebFront\User\QualityControl\PaymentRecordLogController@ajax_import'); // 导入员工
//        $api->post('user/payment_record_log/ajax_sms_send', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_sms_send');// 短信模板发送短信

        $api->post('user/payment_record_log/import', 'WebFront\User\QualityControl\PaymentRecordLogController@import');// 导入excel
        $api->post('user/payment_record_log/ajax_get_ids', 'WebFront\User\QualityControl\PaymentRecordLogController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 前台 web
        // 验证码 -- ok
//        $api->get('web/ajax_captcha', 'WebFront\Web\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('web/ajax_captcha_verify', 'WebFront\Web\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('web/ajax_captcha', 'WebFront\Web\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('web/ajax_captcha_verify', 'WebFront\Web\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码 -- ok
        $api->any('web/ajax_send_mobile_vercode', 'WebFront\Web\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('web/ajax_mobile_code_verify', 'WebFront\Web\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
//        $api->any('web/ajax_login', 'WebFront\Web\QualityControl\IndexController@ajax_login');// 登陆--ok
//        $api->post('web/ajax_password_save', 'WebFront\Web\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
//        $api->any('web/ajax_info_save', 'WebFront\Web\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        // 登录 注册
        $api->any('web/ajax_reg', 'WebFront\Web\QualityControl\HomeController@ajax_reg');// 注册
        $api->any('web/ajax_perfect_company', 'WebFront\Web\QualityControl\HomeController@ajax_perfect_company');// 注册-补充企业资料
        $api->any('web/ajax_perfect_user', 'WebFront\Web\QualityControl\HomeController@ajax_perfect_user');// 注册-补充用户资料

        $api->any('web/ajax_login_company', 'WebFront\Web\QualityControl\HomeController@ajax_login_company');// 登陆----为登录测试  补充资料用
        $api->any('web/ajax_login_user', 'WebFront\Web\QualityControl\HomeController@ajax_login_user');// 登陆----为登录测试  补充资料用
        $api->any('web/company_ajax_alist', 'WebFront\Web\QualityControl\HomeController@company_ajax_alist');// 登陆----为登录测试  补充资料用--获得企业列表信息

        // 上传图片
        $api->post('web/upload', 'WebFront\Web\QualityControl\UploadController@index');
        $api->post('web/upload/ajax_del', 'WebFront\Web\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 陕西省市场监督管理局 market
        // 陕西省检验机构信息管理平台

        // 企业能力附表-最新
        $api->any('web/market/company_new_schedule/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanyNewScheduleController@ajax_alist');//ajax获得列表数据

        // 监督检查信息管理
        $api->any('web/market/company_supervise/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanySuperviseController@ajax_alist');//ajax获得列表数据

        // 机构自我声明
        $api->any('web/market/company_statement/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanyStatementController@ajax_alist');//ajax获得列表数据

        // 机构处罚
        $api->any('web/market/company_punish/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanyPunishController@ajax_alist');//ajax获得列表数据

        // 通知公告
        $api->any('web/market/platform_notices/ajax_alist', 'WebFront\Web\QualityControl\Market\PlatformNoticesController@ajax_alist');//ajax获得列表数据

        // 表格下载
        $api->any('web/market/platform_down_files/ajax_alist', 'WebFront\Web\QualityControl\Market\PlatformDownFilesController@ajax_alist');//ajax获得列表数据

        // 能力验证导入结果
        $api->any('web/market/company_ability/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanyAbilityController@ajax_alist');//ajax获得列表数据

        // 监督检查
        $api->any('web/market/company_inspect/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanyInspectController@ajax_alist');//ajax获得列表数据

        // 企业其它【新闻】
        $api->any('web/market/company_news/ajax_alist', 'WebFront\Web\QualityControl\Market\CompanyNewsController@ajax_alist');//ajax获得列表数据


        // 企业帐号管理
        $api->any('web/company/ajax_alist', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_alist');//ajax获得列表数据
//        $api->any('web/company/ajax_del', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_del');// 删除
//        $api->post('web/company/ajax_save', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_save');// 新加/修改
//        $api->post('web/company/ajax_get_child', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('web/company/ajax_get_areachild', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('web/company/ajax_import_staff','WebFront\Web\QualityControl\Site\CompanyController@ajax_import'); // 导入员工
//        $api->post('web/company/ajax_sms_send', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_sms_send');// 短信模板发送短信
//
//        $api->post('web/company/import', 'WebFront\Web\QualityControl\Site\CompanyController@import');// 导入excel
//        $api->post('web/company/ajax_get_ids', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('web/company/ajax_search', 'WebFront\Web\QualityControl\Site\CompanyController@ajax_search');//ajax根据企业名称获得详情数据

        // 直播公告管理
        $api->any('web/live_notice/ajax_alist', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_alist');//ajax获得列表数据
//        $api->any('web/live_notice/ajax_del', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_del');// 删除
//        $api->post('web/live_notice/ajax_save', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_save');// 新加/修改
//        $api->post('web/live_notice/ajax_get_child', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('web/live_notice/ajax_get_areachild', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('web/live_notice/ajax_import_staff','WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_import'); // 导入员工
//        $api->post('web/live_notice/ajax_sms_send', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_sms_send');// 短信模板发送短信
//
//        $api->post('web/live_notice/import', 'WebFront\Web\QualityControl\Site\LiveNoticeController@import');// 导入excel
//        $api->post('web/live_notice/ajax_get_ids', 'WebFront\Web\QualityControl\Site\LiveNoticeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 点播课程
        $api->any('web/vods/ajax_alist', 'WebFront\Web\QualityControl\Site\VodsController@ajax_alist');//ajax获得列表数据
//        $api->post('web/vods/ajax_del', 'WebFront\Web\QualityControl\Site\VodsController@ajax_del');// 删除
//        $api->post('web/vods/ajax_save', 'WebFront\Web\QualityControl\Site\VodsController@ajax_save');// 新加/修改
//        $api->post('web/vods/ajax_get_child', 'WebFront\Web\QualityControl\Site\VodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('web/vods/ajax_get_areachild', 'WebFront\Web\QualityControl\Site\VodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('web/vods/ajax_import_staff','WebFront\Web\QualityControl\Site\VodsController@ajax_import'); // 导入员工
//        $api->post('web/vods/ajax_sms_send', 'WebFront\Web\QualityControl\Site\VodsController@ajax_sms_send');// 短信模板发送短信

//        $api->post('web/vods/import', 'WebFront\Web\QualityControl\Site\VodsController@import');// 导入excel
//        $api->post('web/vods/ajax_get_ids', 'WebFront\Web\QualityControl\Site\VodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('web/vods/ajax_join_save', 'WebFront\Web\QualityControl\Site\VodsController@ajax_join_save');// 报名
        // 点播课程视频目录
        $api->any('web/vod_video/ajax_alist', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_alist');//ajax获得列表数据
//        $api->post('web/vod_video/ajax_del', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_del');// 删除
//        $api->post('web/vod_video/ajax_save', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_save');// 新加/修改
//        $api->post('web/vod_video/ajax_get_child', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('web/vod_video/ajax_get_areachild', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('web/vod_video/ajax_import_staff','WebFront\Web\QualityControl\Site\VodVideoController@ajax_import'); // 导入员工
//        $api->post('web/vod_video/ajax_sms_send', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_sms_send');// 短信模板发送短信

//        $api->post('web/vod_video/import', 'WebFront\Web\QualityControl\Site\VodVideoController@import');// 导入excel
//        $api->post('web/vod_video/ajax_get_ids', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//        $api->post('web/vod_video/up_file', 'WebFront\Web\QualityControl\Site\VodVideoController@up_file');// 上传文件地址--封面
//        $api->post('web/vod_video/up_file_video', 'WebFront\Web\QualityControl\Site\VodVideoController@up_file_video');// 上传文件地址--音频、视频
//        $api->post('web/vod_video/up_file_courseware', 'WebFront\Web\QualityControl\Site\VodVideoController@up_file_courseware');// 上传文件地址--附件课件资料
//        $api->post('web/vod_video/ajax_dir_save', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_dir_save');// 新加/修改--目录
//        $api->post('web/vod_video/ajax_get_vod_dir', 'WebFront\Web\QualityControl\Site\VodVideoController@ajax_get_vod_dir');// 根据课程id 及当前记录id，获得 课程的目录

        // 点播课程订单
        $api->any('web/vod_orders/ajax_alist', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_alist');//ajax获得列表数据
//        $api->post('web/vod_orders/ajax_del', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_del');// 删除
//        $api->post('web/vod_orders/ajax_save', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_save');// 新加/修改
        $api->post('web/vod_orders/ajax_get_child', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('web/vod_orders/ajax_get_areachild', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('web/vod_orders/ajax_import_staff','WebFront\Web\QualityControl\Site\VodOrdersController@ajax_import'); // 导入员工
//        $api->post('web/vod_orders/ajax_sms_send', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_sms_send');// 短信模板发送短信

        $api->post('web/vod_orders/import', 'WebFront\Web\QualityControl\Site\VodOrdersController@import');// 导入excel
        $api->post('web/vod_orders/ajax_get_ids', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
        $api->any('web/vod_orders/ajax_create_order', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_create_order');// 缴费生成订单
        $api->post('web/vod_orders/ajax_wx_query_order', 'WebFront\Web\QualityControl\Site\VodOrdersController@ajax_wx_query_order');// ajax查询微信扫码支付是否成功

//        Route::middleware('auth:api')->get('/user', function (Request $request) {
//            return $request->user();
//        });
        $api->group(['middleware' => 'auth:api'], function ($api) {
            $api->get('/user', function (Request $request) {
                return $request->user();
            });
        });
    });
});

//// 接口文档swaggler测试
//Route::get('show','BaseController@show');
//Route::get('hello','BaseController@hello');

/**
 *
// jwt测试
Route::post('login', 'ApiJWTController@login');
Route::post('register', 'ApiJWTController@register');
Route::post('testaa', 'ApiJWTController@testaa');
Route::post('testbb', 'ApiJWTController@testbb');

//Route::group(['middleware' => 'auth.jwt'], function () {
//    Route::get('logout', 'ApiJWTController@logout');
//    Route::get('usera', 'ApiJWTController@getAuthUser');
//
//    Route::get('products', 'ProductController@index');
//    Route::get('products/{id}', 'ProductController@show');
//    Route::post('products', 'ProductController@store');
//    Route::put('products/{id}', 'ProductController@update');
//    Route::delete('products/{id}', 'ProductController@destroy');
//});
// 原文链接：https://blog.csdn.net/qq_37788558/article/details/91886363
// 然后在标头请求中添加“Authorization：Bearer {token}”
//Route::group(['prefix' => 'auth'], function () {
//    Route::post('login', 'Auth\JwtAuthController@login');
//    Route::post('logout', 'Auth\JwtAuthController@logout');
//    Route::post('refresh', 'Auth\JwtAuthController@refresh');
//    Route::post('me', 'Auth\JwtAuthController@me');
//});

// 文件上传 any(
// Route::post('file/upload', 'IndexController@upload');
Route::post('upload', 'UploadController@index');
// Route::post('upload/test', 'UploadController@test');
// excel
Route::get('excel/test','ExcelController@test');
Route::get('excel/export','ExcelController@export'); // 导出
Route::get('excel/import','ExcelController@import'); // 导入
Route::get('excel/import_test','ExcelController@import_test'); // 导入 - 测试


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
 *
 */


/*
Route::post('file/upload', function(\Illuminate\Http\Request $request) {
    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
        $photo = $request->file('photo');
        $extension = $photo->extension();
        //$store_result = $photo->store('photo');
        $store_result = $photo->storeAs('photo', 'test.jpg');
        $output = [
            'extension' => $extension,
            'store_result' => $store_result
        ];
        print_r($output);exit();
    }
    exit('未获取到上传文件或上传过程出错');
});
*/

//  将Passport集成到您的Laravel API https://justlaravel.com/integrate-passport-laravel-api/
//Route::get('/users', 'DetailController@index')->middleware('auth:api');


// laravelpassport实现API认证（Laravel5.6）---authuser+jwt格式token的登陆状态
// https://segmentfault.com/a/1190000017560443?utm_source=tag-newest
//
//Route::group(['namespace' => 'Api'], function () {
//    // 登录
//    Route::post('login', 'LoginController@login');
//    // 注册
//    Route::post('register', 'LoginController@register');
//    Route::group(['middleware' => 'auth:api'], function () {
//        // 用户信息
//        Route::get('user', 'LoginController@read');
//    });
//});

// https://learnku.com/docs/laravel/5.6/passport/1380
// 将授权码转换为访问令牌
// 路由 /oauth/token 返回的 JSON 响应中会包含 access_token 、refresh_token 和 expires_in 属性。
// expires_in 属性包含访问令牌的有效期（单位：秒）。
/**
 *
 *  {
 *      "token_type": "Bearer",
 *      "expires_in": 1296000,
 *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjgwOTAzYmEwNGI4MjlhMDliY2MwY2E5ZTZhY2MxZGU0MTljNzk1YTU1NTk3YjZjZDcxOWMxMThiMzRjMGNlMmFjZGZjNWVkOWM2ZWQ0NzlkIn0.eyJhdWQiOiI1IiwianRpIjoiODA5MDNiYTA0YjgyOWEwOWJjYzBjYTllNmFjYzFkZTQxOWM3OTVhNTU1OTdiNmNkNzE5YzExOGIzNGMwY2UyYWNkZmM1ZWQ5YzZlZDQ3OWQiLCJpYXQiOjE1NzE2Njg3NTEsIm5iZiI6MTU3MTY2ODc1MSwiZXhwIjoxNTcyOTY0NzUxLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.i7h1RTkrstkKOUMwaGTjzDsvo5sptYNZV_z2e8Pt3feTvU1pwBKHy-c7c48TST99PZ5MrG9ZQsrn5TnkuUkmuIxRC55Of-FkJUtADWFyxiO0LB-YJFp5pOp7-qBoTmWDWObo1nv58yX2eiy-0aTEN8i8VWcoYI-y_oJbMaYpsBA6eWZ9qGRL06L3K5ZEyN-I_5wtb03fy9QMsZiOohqPv0qcTs7Hr9BQ26PH0vQBxI07UxQq-fV57oNpyiGNT9_GFiWjHlwE7As-n_Y3UzDEUUk5YfSABQaeu5qhCYye-KOVuAFxHTnlnMh9yX36XcAy85WYigMhe2QIgCgu-WspHRafDgQvd8SYn6hObJX4fF_lZE8NSxKTYp0jrpSVWJIVXw31umiz2HBGJ6awAcb07B2o4Asa4LJIvx2bMc3okyAdIxUEsZaGM6zLOW8KD22o8tsDqLlv-D5SbEpycIUqyaCg56HzjuxlMzcYxA27S7qeTrmn403Y6cEWZ-rqod2UzuPbMJeLx7XqUReNAo27--6c2u3zsY9Gglo3dc42cKD7REeQeu2QbXSyiDi0YRWXFHtw-Rglm9Yzu37Chiy4ipSHBTYiI5oMOJ8vQdo_JEe3K0fVNO3aKbMJvnP33dZ0t8tufvGbVumnV4TT9RTunvsEUtUN8Dt04kDMyoXppuI",
 *      "refresh_token": "def502003d4792e10b8a83421e37f1044aacd9c4d34fe3df889236b89547385b891926c78664c87ea7aab5682c8e20f90cec41d22e79d421a4fcd238ca23010b7e007361c4e4121d2817ab86ab6289e6fae97049358c10c5baec98a78d466fa05c93f921bf413530d4487409b66ec980c3e41a9521a35df6512eab113b5c52ac913f17d3e02a4f10ddb8352efd544ea9c445eb7767ddc9d3ae3b70e8cd447eebe6fac7827b1a78f53e270b403ddb5da1fb74a386e7b678a65592ec0c4f082e0013d5ee7944f91bef2c74c3b1af5f52439506dbdf5ab7b25de20994d9ec9498f752ba22cb1a01aebcac8f4e6b74e2eb2112bf11795018ff0f4af0d6ef88c5b7a913fe0bc4955c422142ba824eedf2087326d99beaf75fe821e131e360840eacf49d886eb4c325f6900e073cf473462d4df8379b5707040a051052681d797b0c5724cd1d41a32d27c4170433dddc3f239cd75f0640be6783b9acb8aa0c620b2c9577"
 *  }
 *
 */
//Route::get('/auth/callback', function (Request $request) {
//    $http = new GuzzleHttp\Client;
//
//    $response = $http->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'authorization_code',
//            'client_id' => 5,//'client-id',
//            'client_secret' => 'cJN5b2CXvSovrUdQsqJLLOYNM3vKjkaNsSYFqrBm',// 'client-secret',
//            'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback',// 'http://example.com/callback',
//            'code' => $request->code,
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//});

// 隐式授权令牌回调
/**
 * 有个问题：为什么返回的地址参数是#而不是?号
 *  http://runbuy.admin.cunwo.net/api/auth/callback/implicit#access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjIyMmZiY2E4ZGNmOGQ3NzAwNTMxOGI2YjlkYjM3YWIzZmZlODg5YjFiZjQwZTYzYTU3ZTQ3ZjFkZjlmZGMyNWQwNzU2MjdlYzhmMDM5NWE3In0.eyJhdWQiOiI1IiwianRpIjoiMjIyZmJjYThkY2Y4ZDc3MDA1MzE4YjZiOWRiMzdhYjNmZmU4ODliMWJmNDBlNjNhNTdlNDdmMWRmOWZkYzI1ZDA3NTYyN2VjOGYwMzk1YTciLCJpYXQiOjE1NzE3MDUxMjcsIm5iZiI6MTU3MTcwNTEyNywiZXhwIjoxNTczMDAxMTI3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.dNXslW8QHo7iQTOWTIQ3h0RXpL7PqUHdyE1QAzr-0osDCuoNQbHhWq2pcXJxCwJnO1YeNlsiBswkBkBb5QcR9UYJNL6ZmnbVBsJxxRBOY2TdPVd26bxDdHN0g3tBLEt4OB5uNT20fDBdBQPU9nAF3hEqBhEpN3kJiKmR4E0QsNKn65nobXKhhjTg4cuuopT2ZK7J1VNQQryIb4IOgDUNIGR-qb_gYqoi6J5son9wtAHmI72nz1zG7gitdt_yV1VYdkGx3fSsfL3qt0HDaflhBdi4BEL-KSZgmy3rgHO5TNx5idszDoHzpwzxuFEIhzUZoMpg5Nj-vjiqFYmZ6XUOPEhBq5V77n1h4Hvpj9xXNH3ckO7VsAy6wsHol0hjDWER-WeOmyakT2mADYgtixcinmW7ZYJEcHhRAwyBTA-rY8iVz013NHsVIJbocntdNdpvvuQc3Crqu1CnKorBYPZjsfI15vISE8UDRYC6z7MNYPV5XrJ9QvI_PFnOWL6jDnIieAjT_wB-BbBNpFQytOwjRQL5wIJXMHcon-SPqkPm41Dlt2nHjepPtsViHZXwyJpHL3ZvxwICYmpFE4Vtr5wRapQeeirnwYPOoBcbMTurSS9J3WNqhuEkkxCX90q29tI6R_u38eFz8LFmedZoI4LrZTo7mZCysZroonuy6LAcLic&token_type=Bearer&expires_in=1296000
 *
 */
//Route::get('/auth/callback/implicit', function (Request $request) {
//    pr($request->all());
//});
// 刷新令牌
/**
 *
 *  {
 *      "token_type": "Bearer",
 *      "expires_in": 1296000,
 *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI0NjQyMzQyNzRjN2FlYWFiYmZlMjRiZGVkZjEyZjQzZmQ4YWUwOThkZjA5ODM3ZDE0MWIzYzgxYjhjZWFjZjViNjgzYTBkOTUyNTIyZTgwIn0.eyJhdWQiOiI1IiwianRpIjoiYjQ2NDIzNDI3NGM3YWVhYWJiZmUyNGJkZWRmMTJmNDNmZDhhZTA5OGRmMDk4MzdkMTQxYjNjODFiOGNlYWNmNWI2ODNhMGQ5NTI1MjJlODAiLCJpYXQiOjE1NzE2Njk3MTYsIm5iZiI6MTU3MTY2OTcxNiwiZXhwIjoxNTcyOTY1NzE2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.dhPgP6vEGU6bwrjiPpFJsTjyKH0sXUdwslAZgF-2NGbFFWf6FX5v-wPGZIXQE_qmVFgqP6Nj0To0uUZ4f4ngdUmlCbPVjiWG6UfaDCRKqKTYO41fhTBKtsJb3pE5tnes0hWp8tPQD_eThjLD48NpTDKRdo1OO_GYqVt9XPzz4jZIu2Piqx0t-BYHDiL4RwS0itrUAxo14cb2vhiZSrqW9XOH9QgeXCY5rcMfHqtOjqNU10-VwK-5C3qruSY843Uq9dtjv6iEmpHoyfi2fPjYDeaNZrPOErsFw8WwB6B_VQbyOX3jSBJ91ZcBrLf-Bi8nll9DltFNNiDcRiuVlRLK-S9aVt672vA0zV0AjFjzak5YuYOIRsplKoRpW6G830rG-1x1YTFpbfV91IH0CtE21VhNhzz0X0bWvGIuYkbULJS3Mwr7hzpCHDDcovVgQb08Q-M9i3FvpFQGWvQkTnJ8IA4EEDjbC2L24n48y5yzdnJ4T3d_Xbos0dI1gI4XVoP4qL8BuPiSUBKgFC5vHM3CgOt672sXMM7c59PzIRyE5ws6sBKEAGa5CDDDw8n11IzvsfsFwZ2a8KMjBNaxqQDWv64CB5h4qwwoQ2X2kPCVa6K-ZSweDqXVjiKZAv-Ank-3IF9kPer20_od1nkTlfNxcirzx_3IbEv7ClQEpMO7xXM",
 *       "refresh_token": "def50200984e3f8282410e29122a90f934ab9b1e54e389749ab2b1ff41d1df8b119562cf372bc564cef8c5e3e45169b979062d2a8c6e2dda9e8ddb6699d850453558e1b7f985ffef93e15bcd80b9ef5d7d3fb6b07df2bf15c9aeb0d9126db592d59da98ead84b4c538a5ac690de609aaa7f82a3d76d7d2c2b97929ab68c8ec5571e57e372f6336806501d4cc8687fad1d1dd692bea30fc3040b2d1ed488e8c655cc8ed6e5d5747b70666b7e34f37751cfb207d2c48aa3c054be89241756352ecf8c1a3999c38c1262410e458606190119d4455ca68fd13a3e11cee8b095b3ac8fa739f62cb06705104ea39409353ce7b9e1230b573ead483733c6167987b0d6e52e28191615ee3ddcbe3c291d7090d66ed7f6ec8f4cf74b96ea99cf756c7e87a73d11d1fa4be1aca2c2cf0e48f756ef74d421016b3f5dd336410dff4dc1d2693c501aca32bf185defaf9fdaf8d6b1b11a983479918074bfd8438c17ae662769c88"
 *   }
 */
//Route::get('/auth/refreshToken', function (Request $request) {
//    $http = new GuzzleHttp\Client;
//
//    $response = $http->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'refresh_token',
//            'refresh_token' => 'def502003d4792e10b8a83421e37f1044aacd9c4d34fe3df889236b89547385b891926c78664c87ea7aab5682c8e20f90cec41d22e79d421a4fcd238ca23010b7e007361c4e4121d2817ab86ab6289e6fae97049358c10c5baec98a78d466fa05c93f921bf413530d4487409b66ec980c3e41a9521a35df6512eab113b5c52ac913f17d3e02a4f10ddb8352efd544ea9c445eb7767ddc9d3ae3b70e8cd447eebe6fac7827b1a78f53e270b403ddb5da1fb74a386e7b678a65592ec0c4f082e0013d5ee7944f91bef2c74c3b1af5f52439506dbdf5ab7b25de20994d9ec9498f752ba22cb1a01aebcac8f4e6b74e2eb2112bf11795018ff0f4af0d6ef88c5b7a913fe0bc4955c422142ba824eedf2087326d99beaf75fe821e131e360840eacf49d886eb4c325f6900e073cf473462d4df8379b5707040a051052681d797b0c5724cd1d41a32d27c4170433dddc3f239cd75f0640be6783b9acb8aa0c620b2c9577',
//            'client_id' => 5,//'client-id',
//            'client_secret' => 'cJN5b2CXvSovrUdQsqJLLOYNM3vKjkaNsSYFqrBm',// 'client-secret',
//            'scope' => '',
//            // 'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback',// 'http://example.com/callback',
//            // 'code' => $request->code,
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//});


// 客户端凭据授权令牌
//客户端凭据授权适用于机器到机器的认证。例如，你可以在通过 API 执行维护任务中使用此授权。
//要使用这种授权，你首先需要在 app/Http/Kernel.php 的 $routeMiddleware 变量中添加新的中间件：
/*
 *
 *   {
 *        "token_type": "Bearer",
 *        "expires_in": 1296000,
 *        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZlMGMxMTkzMTNhZDk0NDc0NGM0NjQ4MjBlMzgyMjM3M2NkMGFjMTAxYjUzZjg3YjU0NDczMGM2Y2QyMDQyY2U4ZjkxYTM1ZTcyNGMyM2MwIn0.eyJhdWQiOiI1IiwianRpIjoiZmUwYzExOTMxM2FkOTQ0NzQ0YzQ2NDgyMGUzODIyMzczY2QwYWMxMDFiNTNmODdiNTQ0NzMwYzZjZDIwNDJjZThmOTFhMzVlNzI0YzIzYzAiLCJpYXQiOjE1NzE3MDcxMTMsIm5iZiI6MTU3MTcwNzExMywiZXhwIjoxNTczMDAzMTEzLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.pz_dIe_gsgRYOsEpTKizIGI41rXAobm60_SHWgvy1SK28-0gypNV0PFyBjLM8sVJYez8cAZd2gAGrFmAaeB8Z9q64tiWj2I-FTKx5yggNohzMA0T9wu9P-m0YDX4NVCz1ZWAGrSAlPH4Qxtjrof6N-GibL-APXinXE-cGv6P-SW-yYeMlqw7EYkWBglJ28cTH4ZQ8fp7aBm7FvILdKetVpt2vBqLsl-UNckDqob3nie6skdHVcZUZoXrRN_fzYGP0sxrK_Y6AjnkcwidqHZWyjBLgqdIU_ErK_OVKGW4yDmmEo17mvxj2uF6nzbIQwDK78Mjq9rgwWeb3K53MrkRKYgFXRT7qBgHl3S4L8i4bNfupOMCQeAU3NrB1iE3Ko2kjX9ZAS93cO0mLihXBA0XhpIZnYexQAMzEPdMeeFBAyLH6VeoQVePRDXRwj0BqzLcwznDr0DeQUzD1qQ8AHCSUhlRZTGWjUJxPd8SEN4xUb40LfnEKbEd6PZJy5cwM7tLMs7xcFrfyJ1e-hpOcFK0wQdXD1xUU4IKopTZypMg8GE5fJ2-QW3HwLPM3hEJ_Hi1VMSfkqA9ksiudmNcYKXqIYrtTa58XFbIfxgMdafV7Z1zXNzWNZtiIGL3LPm-Ccy7-bUz3oisvX-LMFPWzF4SYqx1oD8MFjNPxQYg_bSVPS8"
 *    }
 *
 */
//Route::get('/auths/client_credentials', function(Request $request) {
//    $guzzle = new GuzzleHttp\Client;
//    $response = $guzzle->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'client_credentials',
//            'client_id' => 5,// 'client-id',
//            'client_secret' => 'cJN5b2CXvSovrUdQsqJLLOYNM3vKjkaNsSYFqrBm',//'client-secret',
//            'scope' => '',// 'your-scope',
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//    // return json_decode((string) $response->getBody(), true)['access_token'];
//});// ->middleware('client');

// 使用 Laravel Passport 处理 API 认证 https://juejin.im/post/5d8ed3536fb9a04e0925f9dd
//Route::group([
//    'prefix' => 'auth'
//], function () {
//    Route::post('login', 'AuthController@login');
//    Route::post('signup', 'AuthController@signup');
//
//    Route::group([
//        'middleware' => 'auth:api'
//    ], function() {
//        Route::get('logout', 'AuthController@logout');
//        Route::get('user', 'AuthController@user');
//    });
//});

//使用 Laravel Passport 为你的 REST API 增加用户认证功能
//https://zhuanlan.zhihu.com/p/64902443

//Route::post('login', 'PassportaaController@login');
//Route::post('register', 'PassportaaController@register');
//
//Route::middleware('auth:api')->group(function () {
//    Route::get('user', 'PassportaaController@details');
//
//    Route::resource('products', 'ProductaaController');
//});

//laravel5.4 使用Dingo/Api v2.0+Passport实现api认证
//https://blog.csdn.net/qq_20455399/article/details/79262002

//Route::post('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');
//
//Route::group(['middleware' => 'auth:api'], function(){
//    Route::post('details', 'API\UserController@details');
//});

// 创建路由(router/api.php) 验证为：auth中间件，guards为api
// http://www.manongjc.com/article/106150.html
//Route::post('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');
//
//Route::group(['middleware' => 'auth:api'], function(){
//    Route::post('details', 'API\UserController@details');
//});
