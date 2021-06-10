<?php

namespace App\Http\Controllers\Expert\QualityControl;

use App\Http\Controllers\WorksController;
use App\Services\Captcha\CaptchaCode;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use OpenApi\Annotations as OA;

class CaptchaController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同


    /**
     * @OA\Get(
     *     path="/api/expert/ajax_captcha",
     *     tags={"大后台-帐号注册登录"},
     *     summary="生成图形验证码",
     *     description="验证码生成3分钟内有效，过期请重新生成。",
     *     operationId="expertCaptchaAjax_captcha",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_Object_result_captcha"),
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
    public function ajax_captcha(Request $request)
    {
        $captcha_expire = config('public.captcha.common');
        $captchaParams = CaptchaCode::createCodeAPI(__CLASS__ . $request->ip(),'default', $captcha_expire);// app('captcha')->create('default', true);

        return ajaxDataArr(1, $captchaParams, '');
    }

    /**
     * @OA\Post(
     *     path="/api/expert/ajax_captcha_verify",
     *     tags={"大后台-帐号注册登录"},
     *     summary="生成图形验证码校验是否正确",
     *     description="验证码生成3分钟内有效，过期请重新生成。",
     *     operationId="expertCaptchaAjax_captcha_verify",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_key"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_code"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_result_data_int_object"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    /**
     * api验证验证码信息是否正确
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_captcha_verify(Request $request)
    {
        $captcha_code = CommonRequest::get($request, 'captcha_code');
        $captcha_key = CommonRequest::get($request, 'captcha_key');
//        if(!captcha_api_check($captcha_code, $captcha_key)) {
//            Cache::forget($captcha_key);
//            return ajaxDataArr(0, null, '验证码错误');
//        }
//        Cache::forget($captcha_key);
        CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, false, 1);
        return ajaxDataArr(1, ['data' => 1], '验证码正确');
    }

}
