<?php

namespace App\Http\Controllers;

use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function InitParams(Request $request, $errDo = true)
    {
        if (!session_id()) session_start();// 初始化session
        $userInfo = $_SESSION['userInfo']?? [];
        if(empty($userInfo) && $errDo) {
            throws('非法请求！', $this->getNotLoginErrCode());
//            if(isAjax()){
//                ajaxDataArr(0, null, '非法请求！');
//            }else{
//                redirect('login');
//            }
        }
        $company_id = $userInfo['company_id'] ?? null;//CommonRequest::getInt($request, 'company_id');
        if($errDo && ( empty($company_id) || (!is_numeric($company_id)))){
            throws('非法请求！', $this->getNotLoginErrCode());
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
        // $this->operate_staff_id_history = $this->user_id;
        // $company_id = config('public.company_id');
        $this->company_id =  $company_id;//'99999';//
        $this->own_organize_id = $this->initOwnOrganizeId();// 组织id--登录者是企业的自己的id
        $this->organize_id = $userInfo['company_id'] ?? 0;
        $this->personal_id = $this->initPersonalId();// 0;// $userInfo['id'] ?? 0;;// 个人id--最底层登录人员id

        $real_name = $userInfo['real_name'] ?? '';
        $this->reDataArr['baseArr']['real_name'] = $real_name;
    }

}
