<?php

namespace App\Http\Controllers\WebFront\Web\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
// use App\Business\Controller\API\RunBuy\CTAPITablesBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Captcha\CaptchaCode;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public function test(Request $request){
        $extParams['sqlParams']['whereIn']['id'] = 123;
        pr($extParams);
            $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        //pr($this->getUserInfo($request));
        //die;
        pr($this->user_id);
        echo '1111';
    }

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//
//            return view('web.QualityControl.index', $reDataArr);
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'web.QualityControl.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

        });
    }

    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha(Request $request)
//    {
//        $captchaParams = CaptchaCode::createCodeAPI(__CLASS__ . $request->ip(),'default');// app('captcha')->create('default', true);
//
//        return ajaxDataArr(1, $captchaParams, '');
//    }

    /**
     * api验证验证码信息是否正确
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha_verify(Request $request)
//    {
//        $captcha_code = CommonRequest::get($request, 'captcha_code');
//        $captcha_key = CommonRequest::get($request, 'captcha_key');
////        if(!captcha_api_check($captcha_code, $captcha_key)) {
////            Cache::forget($captcha_key);
////            return ajaxDataArr(0, null, '验证码错误');
////        }
////        Cache::forget($captcha_key);
//        CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, false, 1);
//        return ajaxDataArr(1, ['data' => 1], '验证码正确');
//    }

    /**
     * 登陆
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('web.QualityControl.login', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 修改密码
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function password(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $user_info = $this->user_info;
            $reDataArr = array_merge($reDataArr, $user_info);
            return view('web.QualityControl.admin.password', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 显示
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $user_info = $this->user_info;

            $reDataArr['adminType'] =  Staff::$adminTypeArr;
            $reDataArr['defaultAdminType'] = $user_info['admin_type'] ?? 0;// 列表页默认状态
            $reDataArr = array_merge($reDataArr, $user_info);
            return view('web.QualityControl.admin.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * err404
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function err404(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('404', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Post(
     *     path="/api/web/ajax_login",
     *     tags={"前端-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行登录",
     *     operationId="webIndexAjax_login",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_username"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_password"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_key"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_code"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_staff_login"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_multi_brands"}
     */
    /**
     * ajax保存数据
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,4, 1, 1);
    }

    /**
     * 注销
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function logout(Request $request)
    {
        // $this->InitParams($request);
        CTAPIStaffBusiness::loginOut($request, $this);
        $reDataArr = $this->reDataArr;
        return redirect('web/login');
    }

    /**
     * ajax修改密码
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_password_save(Request $request)
    {
        $this->InitParams($request);
        return CTAPIStaffBusiness::modifyPassWord($request, $this);
    }

    /**
     * ajax 修改设置
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info_save(Request $request)
    {
        $this->InitParams($request);
        $user_info = $this->user_info;

        $id = $this->user_id;
        $company_id = $this->company_id;
        $admin_username = CommonRequest::get($request, 'admin_username');
        $mobile = CommonRequest::get($request, 'mobile');
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
        $tel = CommonRequest::get($request, 'tel');
        $qq_number = CommonRequest::get($request, 'qq_number');

        $saveData = [
            'admin_type' => $user_info['admin_type'],
            'admin_username' => $admin_username,
            'mobile' => $mobile,
            'real_name' => $real_name,
            'sex' => $sex,
           // 'gender' => $sex,
            'tel' => $tel,
            'qq_number' => $qq_number,
        ];
        $extParams = [
            // 'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 下载二维码
     *
     * @param Request $request
     * @param int $id id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function down(Request $request,$id = 0)
//    {
//        $this->InitParams($request);
//        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
//        $relations = '';//  CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');
//
//        $info = CTAPITablesBusiness::getInfoData($request, $this, $id, ['id','table_name','has_qrcode','qrcode_url'], $relations, []);
//        $has_qrcode = $info['has_qrcode'] ?? 1;
//        $qrcode_url = $info['qrcode_url'] ?? '';//  http://runbuy.admin.cunwo.net/resource/company/1/images/qrcode/tables/1.png
//        $qrcode_url_old = $info['qrcode_url_old'] ?? '';// /resource/company/1/images/qrcode/tables/1.png
//        if($has_qrcode != 2 ) die('记录不存在或未生成二维码');
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
//        $res = DownFile::downFilePath(2, $publicPath . $qrcode_url_old, 1024, $save_file_name);
//        if(is_string($res)) echo $res;
//    }

    /**
     * 下载网页打印机驱动
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function down_drive(Request $request)
//    {
////        $this->InitParams($request);
//        // $this->source = 2;
////        $reDataArr = $this->reDataArr;
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $fileName = '/CLodopPrint_Setup_for_Win32NT.exe';
//        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
//        $res = DownFile::downFilePath(2, $publicPath . '/' . $fileName, 1024, $save_file_name);
//        if(is_string($res)) echo $res;
//    }

}
