<?php
namespace App\Http\Controllers\Expert\QualityControl;


use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Tool;
use Illuminate\Http\Request;

class BasicController extends WorksController
{
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器
    // 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //        用途，如加入到登录状态session中，就可以一个浏览器同时登录多个后台。--让每一个后台session的键都唯一，不串（重）
    public $siteLoginUniqueKey = 'expert';
    public $user_type = 8;// 登录用户所属的后台类型  1平台2企业4个人

    // 获得当前登录状态者的 是组织id
    public function initOwnOrganizeId(){
        // $userInfo = $this->user_info;
        return 0;// $this->user_id; 真正的企业后台用这个值 ； $userInfo['company_id'] ?? 0; 真正的个人后台用这个值 ；个人用0比较对
    }

    // 获得个人id--最底层登录人员id，如果是个人登录的话，否则为0
    // 各后台可重写此方法，特别是个人后台中心
    public function initPersonalId(){
        return 0;// $this->user_id; 真正的个人后台用这个值
    }

    // 重购方法


    // 获取
    //  -  $siteLoginUniqueKey 指定就使用指定的，没有，则使用设置的 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //
    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function getUserInfo(Request $request, $siteLoginUniqueKey = '', $errDo = true){
        if(empty($siteLoginUniqueKey)) $siteLoginUniqueKey = $this->siteLoginUniqueKey;
        $staff_id = Tool::getSession($this->redisKey, $this->save_session,
            config('public.sessionKey') . $siteLoginUniqueKey, config('public.sessionRedisTye'));
        if($errDo && !is_numeric($staff_id)) throws('登录失效，请重新登录！', $this->getNotLoginErrCode());
        $userInfo = $this->getStaffInfo($staff_id, $errDo);
//        $userInfo = CTAPIStaffBusiness::getInfoDataBase(\request(), $this,'', $staff_id, [], '', 1);
        // 对数据进行有效性验证
        if($errDo && (empty($userInfo) || count($userInfo) <= 0)){
            $this->delUserInfo();
            throws('用户名信息不存在！');
        }

        if($errDo && $userInfo['admin_type'] != $this->user_type ){
            $this->delUserInfo();
            throws('非法访问！');
        }
        if($errDo && $userInfo['account_status'] == 2 ){
            $this->delUserInfo();
            throws('用户已冻结！');
        }

        if($errDo && $userInfo['open_status'] == 1 ){
            $this->delUserInfo();
            throws('审核中，请耐心等待！');
        }
        if($errDo && $userInfo['open_status'] == 4 ){
            $this->delUserInfo();
            throws('审核未通过！');
        }
        if($errDo && $userInfo['open_status'] != 2 ){
            $this->delUserInfo();
            throws('非审核通过！');
        }
        return $userInfo;
    }

    // 根据用户id ，判断权限
    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function getStaffInfo($staff_id = 0, $errDo = true){
        if($errDo && !is_numeric($staff_id)) throws('参数【staff_id】有误！');
        $userInfo = [];
        if(is_numeric($staff_id) && $staff_id > 0) $userInfo = CTAPIStaffBusiness::getInfoDataBase(\request(), $this,'', $staff_id, [], '', 1);
        if(!is_array($userInfo) || empty($userInfo)) $userInfo = [];
        // 对数据进行有效性验证
//        if($errDo && (empty($userInfo) || count($userInfo) <= 0)){
//            $this->delUserInfo();
//            throws('用户名信息不存在！');
//        }
//
//        if($errDo && $userInfo['admin_type'] != $this->user_type ){
//            $this->delUserInfo();
//            throws('非法访问！');
//        }
//
//        if($errDo && $userInfo['account_status'] == 2 ){
//            $this->delUserInfo();
//            throws('用户已冻结！');
//        }

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
