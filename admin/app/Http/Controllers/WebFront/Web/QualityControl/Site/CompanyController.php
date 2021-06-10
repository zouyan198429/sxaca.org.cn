<?php

namespace App\Http\Controllers\WebFront\Web\QualityControl\Site;

use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompanyController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function index(Request $request)
//    {
//        return $this->exeDoPublicFun($request, 1, 1, 'web.QualityControl.Site.Company.index', false
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }


    /**
     *  iframe
     * ?field=&keyword=
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function iframe(Request $request)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'web.QualityControl.Site.Company.iframe', false
            , '', [], function (&$reDataArr) use ($request){
                $companyGradeArr = Staff::$companyGradeArr;

                // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
                $reDataArr['companyGrade'] =  $companyGradeArr;// Staff::$companyGradeArr;
                if(isset($reDataArr['companyGrade'][1])) unset($reDataArr['companyGrade'][1]);
                $company_grade = 1;// CommonRequest::get($request, 'company_grade');
                if(strlen($company_grade) <= 0 ) $company_grade = -1;
                $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态
                $reDataArr['company_grade'] = $company_grade;

                $companyGradeList = [];
                foreach($companyGradeArr as $company_grade => $company_grade_name){
                    // 获得最新更新企业
                    $company_update_list = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 2, 12
                        ,  ['admin_type' => 2, 'company_grade' => $company_grade], false,[]
                        , [
                            'sqlParams' => [
                                'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                                'orderBy' => ['updated_at' => 'desc', 'id' => 'desc']
                            ]
                        ]);
                    $companyGradeList[$company_grade] = $company_update_list;

                }
                $reDataArr['companyGradeList'] = $companyGradeList;


            });
    }

    /**
     *  列表页--企业的
     * ?field=&keyword=
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function company(Request $request, $city_id = 0, $industry_id = 0, $pagesize = 20, $page = 1)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'web.QualityControl.Site.Company.index', false
            , '', [], function (&$reDataArr) use ($request, &$city_id, &$industry_id, &$pagesize, &$page){

                $keyArr = CTAPIStaffBusiness::getCompanyList($request, $this, $reDataArr, 'web/company/', $city_id, $industry_id, $pagesize, $page, 1);
                $reDataArr['key_str'] = implode(',', $keyArr);

                // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
                $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
                if(isset($reDataArr['companyGrade'][1])) unset($reDataArr['companyGrade'][1]);
                $company_grade = CommonRequest::get($request, 'company_grade');
                if(strlen($company_grade) <= 0 ) $company_grade = -1;
                $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态
                $reDataArr['company_grade'] = $company_grade;

                // 获得最新更新企业
//                $company_update_list = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 2, 20
//                    ,  ['admin_type' => 2], false,[]
//                    , [
//                        'sqlParams' => [
//                            'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
//                            'orderBy' => ['updated_at' => 'desc', 'id' => 'desc']
//                        ]
//                    ]);
//                $reDataArr['company_update_list'] = $company_update_list;
//                pr($reDataArr);

            });
    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function add(Request $request,$id = 0)
//    {
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'web.QualityControl.Site.Company.add', false
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
//    }


    /**
     * 详情页
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request,$id = 0)
    {
        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.Site.Company.info', false
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * ajax获得查询数据详情
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_search(Request $request){

//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        return $this->exeDoPublicFun($request, 0, 4,'', false, '', [], function (&$reDataArr) use ($request){

            $company_name = CommonRequest::get($request, 'company_name');
            if(empty($company_name)) return ajaxDataArr(0, null, '参数[单位名称]不能为空！');

            $companyInfo = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 4, 1
                , ['company_name' => $company_name], false,[]
                , [
                    'sqlParams' => [
                        'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                        // 'orderBy' => ['updated_at' => 'desc', 'id' => 'desc']
                    ]
                ]);
            if(empty($companyInfo))  throws('您查询的单位信息不存在！');
            $companyInfo['company_begin_time_format'] = judgeDate($companyInfo['company_begin_time'],'Y-m-d');
            $companyInfo['company_end_time_format'] = judgeDate($companyInfo['company_end_time'],'Y-m-d');
            return ajaxDataArr(1, $companyInfo, '');
        });
    }

    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info(Request $request){
//
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', false, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_alist(Request $request){
////        $this->InitParams($request);
////        return  CTAPIStaffBusiness::getList($request, $this, 2 + 4);
//        return $this->exeDoPublicFun($request, 4, 4,'', false, '', [], function (&$reDataArr) use ($request){
//
//            $handleKeyConfigArr = ['company_info' => '', 'resource_list' => ''];
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//            ];
//            $result = CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
//            $dataList = $result['result']['data_list'] ?? [];
//            foreach($dataList as $k => &$v){
//                $v['created_at_fmt'] = judgeDate($v['created_at'],'Y-m-d');
//            }
//            $result['result']['data_list'] = $dataList;
//            return $result;
//        });
//    }

    /**
     * 选择短信模板页面
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function sms_send(Request $request)
//    {
//        return $this->exeDoPublicFun($request, 34359738368, 8, 'admin.QualityControl.SmsTemplate.sms_send', true
//            , '', [], function (&$reDataArr) use ($request){
//                $sms_operate_type = 1;// 操作类型 1 发送短信  ; 2测试发送短信
//                $reDataArr['sms_operate_type'] = $sms_operate_type;
//                // 设置参数
//                $mergeParams = [// template_id 与 module_id 二选一
//                    // 'sms_template_id' => 1,// 短信模板id;--可为0 ；
//                    'sms_module_id' => 1,// 短信模块id
//                ];
//                CTAPISmsTemplateBusiness::mergeRequest($request, $this, $mergeParams);
//
//                $smsMobileFieldKV = ['mobile' => '手机号'];// 可以发送短信的手机号字段
//                $smsMobileField = 'mobile';// 默认的发送短信的手机号字段
//                $reDataArr['smsMobileFieldKV'] = $smsMobileFieldKV;
//                $reDataArr['defaultSmsMobileField'] = $smsMobileField;
//                CTAPISmsTemplateBusiness::smsSend($request,  $this, $reDataArr);
//            });
//    }

    /**
     * ajax发送手机短信
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_sms_send(Request $request){
//        return $this->exeDoPublicFun($request, 68719476736, 4,'', true, '', [], function (&$reDataArr) use ($request){
//
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
////                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
////                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
////                'listHandleKeyArr' => ['priceIntToFloat'],
//            ];
//            return CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
//        });
//    }

    // **************公用方法**********************开始*******************************

    /**
     * 公用列表页 --- 可以重写此方法--需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doListPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        $reDataArr['hidden_option'] = $hiddenOption;
    }

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
        // $user_info = $this->user_info;
        $id = $extendParams['params']['id'] ?? 0;

        $info = [
            'id'=>$id,
            //   'department_id' => 0,
        ];
        $operate = "添加";


        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $handleKeyConfigArr = ['company_content' => ''];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        $company_hidden = CommonRequest::getInt($request, 'company_hidden');
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

        // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
        $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
        if(isset($reDataArr['companyGrade'][1])) unset($reDataArr['companyGrade'][1]);
        $company_grade = $info['company_grade'] ?? 0 ;// CommonRequest::get($request, 'company_grade');
        if(strlen($company_grade) <= 0 ) $company_grade = -1;
        $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态
        $reDataArr['company_grade'] = $company_grade;

        // 获得会员等级
        $company_grade_name = '';
        if(!empty($company_grade)){
            $companyGradeArr = Staff::$companyGradeArr;
            $company_grade_name = $companyGradeArr[$company_grade] ?? '';
        }else{
            $company_grade_name = '所有';
        }
        $reDataArr['company_grade_name'] = $company_grade_name;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
