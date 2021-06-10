<?php

namespace App\Http\Controllers\Admin\QualityControl\API;

use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIApplyBusiness;
use App\Http\Controllers\Admin\QualityControl\BasicController as ParentBasicController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class BasicController extends ParentBasicController
{

    // public $user_type = 16;// 登录用户所属的后台类型  1平台2企业4个人
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public $user_type = 16;// 登录用户所属的后台类型  1平台2企业4个人 16第三方服务商
    // 应用的 AppKey：公匙 =>  http://coolaf.com/tool/rd
//    public static $appConfig = [
//        '1' => [
//            'appId' => 'tJEdrjOU7RvD9UXZ',// AppID：应用的唯一标识
//            // 'appKey' => '',// AppKey：公匙（相当于账号） API公钥
//            'appSecret' => '06DpogbTRxis1Q1YBNHJfyhKWNYCERW7',//  AppSecret：私匙（相当于密码）API密钥
//        ]
//    ];

    // 根据 appid 获得应用的密钥
//    public static function getAppInfo($appId){
//        $appConfigInfo = [];
//        foreach(static::$appConfig as $tConfig){
//            if($tConfig['appId'] == $appId) {
//                $appConfigInfo = $tConfig;
//                break;
//            }
//        }
//        if(empty($appConfigInfo)){
//
//        }
//        return $appConfigInfo;
//    }

    // 重购方法


    // 获取
    //  -  $siteLoginUniqueKey 指定就使用指定的，没有，则使用设置的 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //
    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function getUserInfo(Request $request, $siteLoginUniqueKey = '', $errDo = true){
        $userInfo = [];
        if($errDo) $userInfo = $this->getUserInfoByAppId($request, 1, 1 | 2 | 4 | 8 | 16,
            1 | 2 | 4 | 8 | 16 | 32, false, [$this->user_type]);
//        if(empty($siteLoginUniqueKey)) $siteLoginUniqueKey = $this->siteLoginUniqueKey;
//        $staff_id = Tool::getSession($this->redisKey, $this->save_session,
//            config('public.sessionKey') . $siteLoginUniqueKey, config('public.sessionRedisTye'));
//        if($errDo && !is_numeric($staff_id)) throws('登录失效，请重新登录！', $this->getNotLoginErrCode());
//        $userInfo = $this->getStaffInfo($staff_id, $errDo);
////      $userInfo = CTAPIStaffBusiness::getInfoDataBase(\request(), $this,'', $staff_id, [], '', 1);
//        // 对数据进行有效性验证
//        if($errDo && (empty($userInfo) || count($userInfo) <= 0)){
//            $this->delUserInfo();
//            throws('用户名信息不存在！');
//        }
//
//        if($errDo && $userInfo['admin_type'] != $this->user_type ){
//            $this->delUserInfo();
//            throws('非法访问！');
//        }
//        if($errDo && $userInfo['account_status'] == 2 ){
//            $this->delUserInfo();
//            throws('用户已冻结！');
//        }
//
//        if($errDo && $userInfo['open_status'] == 1 ){
//            $this->delUserInfo();
//            throws('审核中，请耐心等待！');
//        }
//        if($errDo && $userInfo['open_status'] == 4 ){
//            $this->delUserInfo();
//            throws('审核未通过！');
//        }
//        if($errDo && $userInfo['open_status'] != 2 ){
//            $this->delUserInfo();
//            throws('非审核通过！');
//        }
        return $userInfo;
    }
}
