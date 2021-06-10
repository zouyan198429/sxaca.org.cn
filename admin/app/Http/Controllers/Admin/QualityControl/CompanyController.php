<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIIndustryBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompanyController extends StaffController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public static $ADMIN_TYPE = 2;// 类型1平台2企业4个人
    public static $VIEW_NAME = 'Company';// 视图文件夹名称

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                $company_name = CommonRequest::get($request, 'company_name');
                $company_credit_code = CommonRequest::get($request, 'company_credit_code');
                $company_is_legal_persion = CommonRequest::getInt($request, 'company_is_legal_persion');
                if($company_is_legal_persion != 1) $company_is_legal_persion = 2;
                $company_legal_credit_code = CommonRequest::get($request, 'company_legal_credit_code');
                $company_legal_name = CommonRequest::get($request, 'company_legal_name');
                $city_id = CommonRequest::getInt($request, 'city_id');
                $company_type = CommonRequest::getInt($request, 'company_type');
                $company_prop = CommonRequest::getInt($request, 'company_prop');
                $addr = CommonRequest::get($request, 'addr');
                $zip_code = CommonRequest::get($request, 'zip_code');
                $fax = CommonRequest::get($request, 'fax');
                $email = CommonRequest::get($request, 'email');
                $company_legal = CommonRequest::get($request, 'company_legal');
                $company_peoples_num = CommonRequest::getInt($request, 'company_peoples_num');
                $company_industry_id = CommonRequest::getInt($request, 'company_industry_id');
                $company_certificate_no = CommonRequest::get($request, 'company_certificate_no');
                $ratify_date = CommonRequest::get($request, 'ratify_date');
                $valid_date = CommonRequest::get($request, 'valid_date');
                // $laboratory_addr = CommonRequest::get($request, 'laboratory_addr');
                // 判断开始结束日期
                if(judgeDate($ratify_date) !== false && judgeDate($valid_date) !== false){
                    Tool::judgeBeginEndDate($ratify_date, $valid_date, 1 + 2 + 256 + 512, 1, date('Y-m-d'), '有效起止日期');
                }
                $company_contact_name = CommonRequest::get($request, 'company_contact_name');
                $company_contact_mobile = CommonRequest::get($request, 'company_contact_mobile');
                $company_contact_tel = CommonRequest::get($request, 'company_contact_tel');
                $is_perfect = CommonRequest::getInt($request, 'is_perfect');
                // 可能会用的参数
                $admin_username = CommonRequest::get($request, 'admin_username');
                $admin_password = CommonRequest::get($request, 'admin_password');
                $sure_password = CommonRequest::get($request, 'sure_password');
                $userInfo = [];
                if($id > 0){
                    $userInfo = $this->judgePower($request, $id);
                    // 判断是否有操作权限
                    // 根据具体功能 ，加上或去掉要判断的下标
                    $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
                    if(!$this->batchJudgeRecordOperateAuth($userInfo, $powerFields, 0, 0, 0, true)){
                        return ajaxDataArr(0, null, '您没有操作权限！');
                    }
                }

                // 图片资源
                $resource_id = CommonRequest::get($request, 'resource_id');
                // 如果是字符，则转为数组
                if(is_string($resource_id) || is_numeric($resource_id)){
                    if(strlen(trim($resource_id)) > 0){
                        $resource_id = explode(',' ,$resource_id);
                    }
                }
                if(!is_array($resource_id)) $resource_id = [];

                // 再转为字符串
                $resource_ids = implode(',', $resource_id);
                if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

                $saveData = [
                    'admin_type' => static::$ADMIN_TYPE,
                    'is_perfect' => $is_perfect,
                    'company_name' => $company_name,
                    'company_credit_code' => $company_credit_code,
                    'company_is_legal_persion' => $company_is_legal_persion,
                    'company_legal_credit_code' => $company_legal_credit_code,
                    'company_legal_name' => $company_legal_name,
                    'city_id' => $city_id,
                    'company_type' => $company_type,
                    'company_prop' => $company_prop,
                    'addr' => $addr,
                    'zip_code' => $zip_code,
                    'fax' => $fax,
                    'email' => $email,
                    'company_legal' => $company_legal,
                    'company_peoples_num' => $company_peoples_num,
                    'company_industry_id' => $company_industry_id,
                    'company_certificate_no' => $company_certificate_no,
                    // 'laboratory_addr' => $laboratory_addr,
                    'company_contact_name' => $company_contact_name,
                    'company_contact_mobile' => $company_contact_mobile,
                    'company_contact_tel' => $company_contact_tel,
                    'admin_username' => $admin_username,
                    // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
                    'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
                    'resourceIds' => $resource_id,// 此下标为图片资源关系
                ];

                if(judgeDate($ratify_date) !== false && judgeDate($valid_date) !== false){
                    $saveData = array_merge($saveData,[
                        'ratify_date' => $ratify_date,
                        'valid_date' => $valid_date,
                        ]);
                }
                if($admin_password != '' || $sure_password != ''){
                    if ($admin_password != $sure_password){
                        return ajaxDataArr(0, null, '密码和确定密码不一致！');
                    }
                    $saveData['admin_password'] = $admin_password;
                }
                // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

                if($id <= 0) {// 新加;要加入的特别字段
                    $addNewData = [
                        // 'account_password' => $account_password,
//                'is_perfect' => 1,
                        'company_grade' => 1,// 新加的会员默认等级为非会员单位
                        'issuper' => 2,
//                'company_type' => 0,// 企业类型1检测机构、2生产企业
//                'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
//                'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
                        'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
                        'account_status' => 1// 状态 1正常 2冻结
                    ];
                    $saveData = array_merge($saveData, $addNewData);
                }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');

        });
    }


    /**
     * 获得会员地区分布
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function grade_area(Request $request)
    {
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.' . static::$VIEW_NAME . '.grade_area', true
            , '', [], function (&$reDataArr) use ($request){

                // 地区分布及企业数量
                $city_list = CTAPICitysBusiness::exeDBBusinessMethodCT($request, $this, '',  'getCompanyGradeNumGroup', [], 1, 1);
                $reDataArr['city_count'] = $city_list;
            });
    }

    /**
     * 获得会员行业分布
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function grade_industry(Request $request)
    {
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.' . static::$VIEW_NAME . '.grade_industry', true
            , '', [], function (&$reDataArr) use ($request){

                // 行业分布及企业数量
                $industry_list = CTAPIIndustryBusiness::exeDBBusinessMethodCT($request, $this, '',  'getCompanyGradeNumGroup', [], 1, 1);
                $reDataArr['industry_count'] = $industry_list;
            });
    }

}
