<?php

namespace App\Http\Controllers;

use App\Business\Controller\API\QualityControl\CTAPIApplyBusiness;
use App\Services\DB\CommonDB;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class WorksController extends BaseController
{
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器

    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function InitParams(Request $request, $errDo = true)
    {
        // 获得redisKey 参数值
        $temRedisKey = CommonRequest::get($request, 'redisKey');
        if(isAjax()){
            $this->source = 2;
        }
        if(!empty($temRedisKey)){// 不为空，则是从小程序来的
            $this->redisKey = $temRedisKey;
            $this->save_session = false;
            $this->source = 3;
        }
        //session_start(); // 初始化session
        //$userInfo = $_SESSION['userInfo']?? [];
        $userInfo = $this->getUserInfo($request, '', $errDo);
        if(empty($userInfo) && $errDo) {
            throws('非法请求！', $this->getNotLoginErrCode());// $this->source);
//            if(isAjax()){
//                ajaxDataArr(0, null, '非法请求！');
//            }else{
//                redirect('login');
//            }
        }
        // 根据获得的用户信息，初始化【设置】控制器属性
        $this->setAttrByInfo($request, $userInfo, $errDo);
    }

    // 根据获得的用户信息，初始化【设置】控制器属性
    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function setAttrByInfo(Request $request, $userInfo = [], $errDo = true){
        $company_id = $userInfo['id'] ?? null;//$userInfo['company_id'] ?? null;//CommonRequest::getInt($request, 'company_id');
        if($errDo && (empty($company_id) || (!is_numeric($company_id)))){
            throws('非法请求！', $this->getNotLoginErrCode());// $this->source);
//            if(isAjax()){
//                ajaxDataArr(0, null, '非法请求！');
//            }else{
//                redirect('login');
//            }
        }
        // Tool::judgeInitParams('company_id', $company_id);
        $this->user_info =$userInfo;
        $this->user_id = $userInfo['id'] ?? '';
        $this->operate_staff_id = $this->user_id;
        $this->operate_staff_id_history = $this->user_id;
        $this->company_id = $company_id;
        $this->own_organize_id = $this->initOwnOrganizeId();// 组织id--登录者是企业的自己的id
        $this->organize_id = $userInfo['company_id'] ?? 0;
        $this->personal_id = $this->initPersonalId();// 0;// $userInfo['id'] ?? 0;;// 个人id--最底层登录人员id


        $this->admin_type = $userInfo['admin_type'] ?? 0;
        $this->city_site_id = $userInfo['city_site_id'] ?? 0;
        $this->city_partner_id = $userInfo['city_partner_id'] ?? 0;
        $this->seller_id = $userInfo['seller_id'] ?? 0;
        $this->shop_id = $userInfo['shop_id'] ?? 0;

        $real_name = $userInfo['real_name'] ?? '';
        $company_name = $userInfo['company_name'] ?? '';
        if(empty($real_name) && !empty($company_name))$real_name = $company_name;
        $mobile = $userInfo['mobile'] ?? '';
        if(empty($real_name)){
            $real_name = $mobile;
        }
        $this->reDataArr['baseArr']['real_name'] = $real_name;
        $this->reDataArr['baseArr']['mobile'] = $mobile;
        $this->reDataArr['baseArr']['id'] = $this->user_id;
        $this->reDataArr['baseArr']['admin_type'] = $this->admin_type;
        $this->reDataArr['qqMapsKey'] = config('public.qqMapsKey');// 腾讯地图Key鉴权
        // 每*分钟，自动更新一下左则
//        $recordTime  = time();
//        $difTime = 60 * 5 ;// 5分钟
//        $modifyTime = $userInfo['modifyTime'] ?? ($recordTime - $difTime - 1);
//        if($this->save_session &&  ($modifyTime + $difTime) <=  $recordTime){// 后台
//            $proUnits = $this->getUnits($this->user_info);
//            $userInfo['proUnits'] = $proUnits;
//            $userInfo['modifyTime'] = time();
//            $redisKey = $this->setUserInfo($userInfo, -1);
//        }
    }

    // 登陆信息
    // 获得生产单元信息
//    public function getUnits($user_info = []){
//        $proUnits = [];
//        // $user_info = $this->user_info;
//        $user_id = $user_info['id'] ?? 0;
//        $company_id = $user_info['company_info']['id'] ?? 0;//$this->company_id;
//        // 判断是否在VIP有效期内-- 没有有效期，则处理[重新登录]
//        $company_vipbegin = $user_info['company_info']['company_vipbegin'] ?? '';
//        $company_vipend = $user_info['company_info']['company_vipend'] ?? '';
//        //判断开始
//        $comp_begin_time_unix = judgeDate($company_vipbegin);
//        if($comp_begin_time_unix === false){
//            // ajaxDataArr(0, null, 'VIP开始日期不是有效日期');
//            // 删除登陆状态
//            $resDel = $this->delUserInfo();
//            return $proUnits;
//        }
//
//        //判断期限结束
//        $comp_end_time_unix = judgeDate($company_vipend);
//        if($comp_end_time_unix === false){
//            // ajaxDataArr(0, null, 'VIP结束日期不是有效日期');
//            // 删除登陆状态
//            $resDel = $this->delUserInfo();
//            return $proUnits;
//        }
//
//        if($comp_end_time_unix < $comp_begin_time_unix){
//            // ajaxDataArr(0, null, 'VIP结束日期不能小于开始日期');
//            // 删除登陆状态
//            //$resDel = $this->delUserInfo();
//            //return $proUnits;
//        }
//        $nowTime = time();
//        if($nowTime < $comp_begin_time_unix){
//            // ajaxDataArr(0, null, 'VIP还未到开始日期，不能新加生产单元!');
//            // 删除登陆状态
//            //$resDel = $this->delUserInfo();
//            //return $proUnits;
//        }
//        if($nowTime > $comp_end_time_unix){
//            // ajaxDataArr(0, null, 'VIP已过期，不能新加生产单元!');
//            // 删除登陆状态
//            //$resDel = $this->delUserInfo();
//            // return $proUnits;
//        }
//
//        // 判断用户状态
//        $relations = "";
//        $userInfo = CommonBusiness::getinfoApi('CompanyAccounts', '', $relations, 0 , $user_id,1);
//
//        $account_status = $userInfo['account_status'] ?? 1;
//        if($account_status != 0){
//            // 删除登陆状态
//            $resDel = $this->delUserInfo();
//            return $proUnits;
//        }
//        // 获得当前所有的
//        //$relations = '';// 关系
//        //if(!$this->save_session){
//            $relations =['siteResources'];
//        //}
//        $queryParams = [
//            'where' => [
//                ['company_id', $company_id],
//            ],
//            'orderBy' => ['id'=>'desc'],
//        ];// 查询条件参数
//        $proUnitList = CommonBusiness::ajaxGetAllList('CompanyProUnit', '', $company_id,$queryParams ,$relations );
//
//        foreach($proUnitList as $v){
//            $status = $v['status'] ?? 0;
//            if($this->save_session && (! in_array($status,[1]))){//后台
//                continue;
//            }elseif( (! $this->save_session) && (! in_array($status,[1]))){// 小程序[0,1]
//                continue;
//            }
//            $begin_time = $v['begin_time'] ?? '';
//            $end_time = $v['end_time'] ?? '';
//            //判断开始
//            $begin_time_unix = judgeDate($begin_time);
//            if($begin_time_unix === false){
//                continue;
//                // ajaxDataArr(0, null, '开如日期不是有效日期');
//            }
//
//            //判断期限结束
//            $end_time_unix = judgeDate($end_time);
// //           if($end_time_unix === false){
// //               continue;
//                // ajaxDataArr(0, null, '结束日期不是有效日期');
// //           }
//
//            if( $end_time_unix !== false && $end_time_unix < $begin_time_unix){
//                continue;
//                // ajaxDataArr(0, null, '结束日期不能小于开始日期');
//            }
//            $time = time();
//            if($end_time_unix !== false && $end_time_unix < $time ){// 过期
//                continue;
//            }
//
//            $tem = [
//                'unit_id' => $v['id'],
//                'site_pro_unit_id' => $v['site_pro_unit_id'],
//                'pro_input_name' => $v['pro_input_name'],
//                'status' => $v['status'],
//                'status_text' => $v['status_text'],
//                'begin_time' => judge_date($v['begin_time'],'Y-m-d'),
//                'end_time' => judge_date($v['end_time'],'Y-m-d'),
//            ];
//
//            //if(! $this->save_session) {
//                // $resource_url = $v['company_pro_config']['site_resources'][0]['resource_url'] ?? '';
//                $resource_url = $v['site_resources'][0]['resource_url'] ?? '';
//                $tem['resource_url'] = $resource_url;
//                CommonBusiness::resourceUrl($tem, 1);
//
//            //}
//            $proUnits[$v['id']] = $tem;
//        }
//        return $proUnits;
//    }

    // 判断应用是否合法及请求是否有效
    /**
     * 根据应用的key，判断应用并判断当前的用户信息，并返回用户信息
     * -- 一般为 权限 判断方法调用【来初始化数据用】 public function getUserInfo(Request $request, $siteLoginUniqueKey = '')
     * @param Request $request
     * @param int $power_no 权限判断 1 api请求sign签名验签
     * @param int $staff_power_no 用户数据 的判断 判断 1：判断是否存在；2 判断用户类型id；4判断是否用户已冻结；8 判断审核中；16 判断审核未通过；32判断非审核通过；；；；；
     * @param boolean $delSession 是否需要删除登录session true：需要[默认] ； false:不需要
     * @param array $admin_type_arr 如果判断用户类型时，可进行访问的用户类型id数组
     * @return array 返回用户信息
     */
    public function getUserInfoByAppId(Request $request, $power_no = 1, $apply_power_no = 1 | 2 | 4 | 8 | 16, $staff_power_no = 0, $delSession = true, $admin_type_arr = []){
        $appid = CommonRequest::get($request, 'appid');
        $this->app_key = $appid;
        $appInfo = $this->getApplyInfo($request, $appid, $apply_power_no);
        $this->app_Info = $appInfo;
        $this->app_id = $appInfo['id'] ?? 0;
        $appAecret = $appInfo['app_secret'] ?? '';
        $staffId = $appInfo['staff_id'] ?? 0;
        $userInfo = $this->getStaffInfoById($request, $staffId, $staff_power_no, $delSession, $admin_type_arr);
        // 判断企业信息
//        $userInfo = $this->getStaffInfo($staffId);
//        if(empty($userInfo))  throws('应用所属企业信息不存在!');
//        // 判断权限
////        if($userInfo['admin_type'] != $this->user_type ){
////            throws('非法访问！');
////        }
//        if($userInfo['account_status'] == 2 ){
//            throws('用户已冻结！');
//        }
//
//        if($userInfo['open_status'] == 1 ){
//            throws('审核中，请耐心等待！');
//        }
//        if($userInfo['open_status'] == 4 ){
//            throws('审核未通过！');
//        }
//        if($userInfo['open_status'] != 2 ){
//            throws('非审核通过！');
//        }
        // 根据获得的用户信息，初始化【设置】控制器属性
        $this->setAttrByInfo($request, $userInfo);

        if( ($power_no & 1) == 1  ){
            /**
             *
             *  服务端接到这个请求：
             *  1 先验证sign签名是否合理，证明请求参数没有被中途篡改
             *  2 再验证timestamp是否过期，证明请求是在最近60s被发出的
             *  3 最后验证nonce是否已经有了，证明这个请求不是60s内的重放请求
             *
             */
            $params = CommonRequest::getParamsByUbound($request, 2, false, [], []);
            // throws(json_encode($params));
            $res = CommonRequest::apiJudgeSign($request, $params, 1,  $appAecret);
            // if(is_string($res)) ajaxDataArr(0, null, $res);
        }
        return $userInfo;
    }

}
