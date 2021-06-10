<?php

namespace App\Http\Controllers\WebFront\Web\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Captcha\CaptchaCode;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use OpenApi\Annotations as OA;

class SMSController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同


    /**
     * @OA\Get(
     *     path="/api/web/ajax_send_mobile_vercode",
     *     tags={"前端-帐号注册登录"},
     *     summary="发送手机注册验证码",
     *     description="验证码2分钟内有效，过期请重新发送。未注册有效手机号。限:10次/天；30次/月；50次/半年；",
     *     operationId="webSMSAjax_send_mobile_vercode",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_mobile"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_result_data_int_object"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_send_mobile_vercode(Request $request)
    {
        $mobile = CommonRequest::get($request, 'mobile');
        $countryCode = '86';
        $templateParams = [];
        // 判断用户是否有效
        $user_type = $this->user_type;
        CTAPIStaffBusiness::mobileIsValid($request, $this, $user_type, $mobile, $countryCode, 1);
        // 发送手机验证码
        CTAPIStaffBusiness::sendSMSCodeLimit($request, $this, 'reg', $mobile, $countryCode, $templateParams, 1);
        return ajaxDataArr(1, ['data' => 1], '');
    }

    /**
     * @OA\Post(
     *     path="/api/web/ajax_mobile_code_verify",
     *     tags={"前端-帐号注册登录"},
     *     summary="发送手机注册验证码校验是否正确",
     *     description="验证码2分钟内有效，过期请重新发送。未注册有效手机号。限:10次/天；30次/月；50次/半年；",
     *     operationId="webSMSAjax_mobile_code_verify",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_mobile"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_mobile_code"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_result_data_int_object"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_mobile_code_verify(Request $request)
    {
        $mobile = CommonRequest::get($request, 'mobile');
        $countryCode = '86';
        $mobile_vercode = CommonRequest::get($request, 'mobile_vercode');
        // 发送手机验证码验证有效性
        CTAPIStaffBusiness::SMSCodeVerify($request, $this, 'reg', $mobile, $countryCode,  $mobile_vercode, false);
        return ajaxDataArr(1, ['data' => 1], '');
    }
}
