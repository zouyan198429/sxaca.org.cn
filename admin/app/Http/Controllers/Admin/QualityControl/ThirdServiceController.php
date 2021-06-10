<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIIndustryBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class ThirdServiceController extends StaffController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public static $ADMIN_TYPE = 16;// 类型1平台2企业4个人16第三方服务商
    public static $VIEW_NAME = 'ThirdService';// 视图文件夹名称

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_save(Request $request)
//    {
////        $this->InitParams($request);
//
//        $id = CommonRequest::getInt($request, 'id');
//        $pageNum = ($id > 0) ? 256 : 32;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//            $id = CommonRequest::getInt($request, 'id');
//            $company_id = CommonRequest::getInt($request, 'company_id');
//            $real_name = CommonRequest::get($request, 'real_name');
//            $sex = CommonRequest::getInt($request, 'sex');
//            $email = CommonRequest::get($request, 'email');
//            $mobile = CommonRequest::get($request, 'mobile');
//            $qq_number = CommonRequest::get($request, 'qq_number');
//            $id_number = CommonRequest::get($request, 'id_number');
//            $position_name = CommonRequest::get($request, 'position_name');
//            $city_id = CommonRequest::getInt($request, 'city_id');
//            $addr = CommonRequest::get($request, 'addr');
//            $is_perfect = CommonRequest::getInt($request, 'is_perfect');
//            // 可能会用的参数
//            $admin_username = CommonRequest::get($request, 'admin_username');
//            $admin_password = CommonRequest::get($request, 'admin_password');
//            $sure_password = CommonRequest::get($request, 'sure_password');
//
//            // 角色
//            $role_nums = CommonRequest::get($request, 'role_nums');
//            // 如果是字符，则转为数组
//            Tool::formatOneArrVals($role_nums, [null, ''], ',', 1 | 2 | 4 | 8);
//            if(!is_array($role_nums)) $role_nums = [];
//
//
//            $sign_status = 1;// 授权人审核状态 1待审核 2 审核通过  4 审核未通过
//
//            $sign_range = CommonRequest::get($request, 'sign_range');
//            $sign_is_food = CommonRequest::getInt($request, 'sign_is_food');
//            if(!in_array('8', $role_nums)){// 不包含授权签 字人
//                $sign_range = '';
//                $sign_is_food = 0;
//                $sign_status = 0;
//            }else{// 包含授权签 字人
//                if($sign_is_food != 1) $sign_is_food = 2;
//            }
//
//
//
//            // 生成最终的角色值
//            $last_role_num = 0;
//            foreach($role_nums as $tem_role_num){
//                $last_role_num |= $tem_role_num;
//            }
//
//            $role_status = 1;// 人员角色审核状态 1待审核 2 审核通过  4 审核未通过
//            if( ($last_role_num & (1 | 2 | 4)) > 0  ){// 包含有 1 或 2 或 4
//                if($id <= 0){
//                    $role_status = 1;
//                }
//            }else{
//                $role_status = 0;
//            }
//
//
//            $userInfo = [];
//            if($id > 0){
//                $userInfo = $this->judgePower($request, $id);
//                // 判断是否有操作权限
//                // 根据具体功能 ，加上或去掉要判断的下标
//                $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
//                if(!$this->batchJudgeRecordOperateAuth($userInfo, $powerFields, 0, 0, 0, true)){
//                    return ajaxDataArr(0, null, '您没有操作权限！');
//                }
//                // 判断授权范围是否有改动
//                if($sign_status > 0){
//                    $newSignInfo = ['sign_range' => $sign_range, 'sign_is_food' => $sign_is_food];
//                    $oldSignInfo = Tool::getArrFormatFields($userInfo, ['sign_range', 'sign_is_food'], false);
//                    if(Tool::isEqualArr($newSignInfo, $oldSignInfo, 1) ){// 相等无变化
//                        $sign_status = $userInfo['sign_status'];
//                    }
//                }
//
//                // 判断角色是否有改动--姓名也没有变
//                if($role_status > 0 && ($last_role_num & (1 | 2 | 4)) == ($userInfo['role_num'] & (1 | 2 | 4)) && $real_name == $userInfo['real_name']){//  无改动
//                    $role_status = $userInfo['role_status'];
//                }
//
//            }
//            $saveData = [
//                'admin_type' => static::$ADMIN_TYPE,
//                'is_perfect' => $is_perfect,
//                'company_id' => $company_id,
//                'real_name' => $real_name,
//                'sex' => $sex,
//                'mobile' => $mobile,
//                'email' => $email,
//                'qq_number' => $qq_number,
//                'id_number' => $id_number,
//                'position_name' => $position_name,
//                'city_id' => $city_id,
//                'addr' => $addr,
//                'role_num' => $last_role_num,
//                'sign_range' => $sign_range,
//                'sign_is_food' => $sign_is_food,
//                'sign_status' => $sign_status,
//                'role_status' => $role_status,
//            ];
//            if(!empty($admin_username)) $saveData['admin_username'] = $admin_username;
//            if($admin_password != '' || $sure_password != ''){
//                if ($admin_password != $sure_password){
//                    return ajaxDataArr(0, null, '密码和确定密码不一致！');
//                }
//                $saveData['admin_password'] = $admin_password;
//            }
//            // 超级帐户 不可 冻结
//    //        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//    //            $saveData['account_status'] = $account_status;
//    //        }
//
//                if($id <= 0) {// 新加;要加入的特别字段
//                    $addNewData = [
//                        // 'account_password' => $account_password,
////                'is_perfect' => 1,
//                        'company_grade' => 0,// 新加的会员默认等级为非会员单位
//                        'issuper' => 2,
//                        'company_type' => 0,// 企业类型1检测机构、2生产企业
//                        'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
//                        'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
//                        'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
//                        'account_status' => 1// 状态 1正常 2冻结
//                    ];
//                    $saveData = array_merge($saveData, $addNewData);
//                }else{
//                    // 如果改变了所属企业,需要重新统计员工数
//                    if(isset($saveData['company_id']) && $company_id != $userInfo['company_id']) $saveData['force_company_num'] = 1;
//                }
//                $extParams = [
//                    'judgeDataKey' => 'replace',// 数据验证的下标
//                ];
//                $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//
//        });
//
//    }

    /**
     * 添加--导入
     *
     * @param Request $request
     * @param int $company_id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_bath(Request $request,$company_id = 0)
//    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$company_id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//
//            $info = [
//                'company_id'=>$company_id,
//                'user_company_name' => '',
//            ];
//            if ($company_id > 0) { // 获得详情数据
//                $info = CTAPIStaffBusiness::getInfoData($request, $this, $company_id, [], '', []);
//                $info['user_company_name'] = $info['company_name'] ?? '';
//            }
//            $info['company_id'] = $company_id ?? 0;
//            // $reDataArr = array_merge($reDataArr, $resultDatas);
//            $reDataArr['info'] = $info;
//            $company_hidden = CommonRequest::getInt($request, 'company_hidden');
//            $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择
//
//            return view('admin.QualityControl.User.import', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//    }

    /**
     * 首页--单个企业的员工
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function show(Request $request,$company_id = 0)
//    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$company_id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//
//            $info = [];
//            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id, [], '', []);
//            if(empty($companyInfo)) throws('企业信息不存在！');
//            $info['company_id'] = $company_id;
//            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
//            $company_id = CommonRequest::getInt($request, 'company_id');
//
//            $reDataArr['info'] = $info;
//
//            // 获得城市KV值--企业和用户有城市
//            if(in_array(static::$ADMIN_TYPE , [2, 4])) {
//                $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
//                $reDataArr['defaultCity'] = -1;// 默认
//            }
//
//            // 所属行业--只有企业有
//            if(static::$ADMIN_TYPE == 2){
//                $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
//                $reDataArr['defaultIndustry'] = -1;// 默认
//
//            }
//
//            // 拥有者类型1平台2企业4个人
//            $reDataArr['adminType'] =  Staff::$adminTypeArr;
//            $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
//
//            // 是否完善资料1待完善2已完善
//            $reDataArr['isPerfect'] =  Staff::$isPerfectArr;
//            $reDataArr['defaultIsPerfect'] = -1;// 列表页默认状态
//
//            // 是否超级帐户2否1是
//            $reDataArr['issuper'] =  Staff::$issuperArr;
//            $reDataArr['defaultIssuper'] = -1;// 列表页默认状态
//
//            // 审核状态1待审核2审核通过4审核不通过
//            $reDataArr['openStatus'] =  Staff::$openStatusArr;
//            $reDataArr['defaultOpenStatus'] = -1;// 列表页默认状态
//
//            // 状态 1正常 2冻结
//            $reDataArr['accountStatus'] =  Staff::$accountStatusArr;
//            $reDataArr['defaultAccountStatus'] = -1;// 列表页默认状态
//
//            // 性别0未知1男2女
//            $reDataArr['sex'] =  Staff::$sexArr;
//            $reDataArr['defaultSex'] = -1;// 列表页默认状态
//
//            // 企业--是否独立法人1独立法人 2非独立法人
//            $reDataArr['companyIsLegalPersion'] =  Staff::$companyIsLegalPersionArr;
//            $reDataArr['defaultCompanyIsLegalPersion'] = -1;// 列表页默认状态
//
//            // 企业--企业类型1检测机构、2生产企业
//            $reDataArr['companyType'] =  Staff::$companyTypeArr;
//            $reDataArr['defaultCompanyType'] = -1;// 列表页默认状态
//
//            // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
//            $reDataArr['companyProp'] =  Staff::$companyPropArr;
//            $reDataArr['defaultCompanyProp'] = -1;// 列表页默认状态
//
//            // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
//            $reDataArr['companyPeoples'] =  Staff::$companyPeoplesNumArr;
//            $reDataArr['defaultCompanyPeoples'] = -1;// 列表页默认状态
//
//            // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
//            $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
//            $company_grade = CommonRequest::get($request, 'company_grade');
//            if(strlen($company_grade) <= 0 ) $company_grade = -1;
//            $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态
//            $reDataArr['company_grade'] = $company_grade;
//
//            return view('admin.QualityControl.' . static::$VIEW_NAME . '.show', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//    }

}
