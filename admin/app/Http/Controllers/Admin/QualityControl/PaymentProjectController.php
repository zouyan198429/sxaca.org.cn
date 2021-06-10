<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIInvoiceProjectTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIInvoiceTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayConfigBusiness;
use App\Business\Controller\API\QualityControl\CTAPIPaymentProjectBusiness;
use App\Business\Controller\API\QualityControl\CTAPIPaymentRecordBusiness;
use App\Business\Controller\API\QualityControl\CTAPIPaymentTypeBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Business\DB\QualityControl\OrderPayMethodDBBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\PaymentProject;
use App\Models\QualityControl\PaymentProjectFields;
use App\Models\QualityControl\PaymentType;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class PaymentProjectController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

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
//            return view('admin.QualityControl.PaymentProject.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.PaymentProject.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request)
//    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $reDataArr['province_kv'] = CTAPIPaymentProjectBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIPaymentProjectBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('admin.QualityControl.PaymentProject.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('admin.QualityControl.PaymentProject.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.PaymentProject.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * 收款填写页面
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add_pay(Request $request,$id = 0)
    {
        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.PaymentProject.add_pay', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

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
        return $this->exeDoPublicFun($request, 17179869184, 1,'admin.QualityControl.PaymentProject.info', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/payment_project/ajax_info",
     *     tags={"大后台-付款/收款相关的-付款/收款项目"},
     *     summary="付款/收款项目--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlPaymentProjectAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_payment_project_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_payment_project"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_payment_project"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info(Request $request){
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        $info = CTAPIPaymentProjectBusiness::getInfoData($request, $this, $id, [], '', []);
        $resultDatas = ['info' => $info];
        return ajaxDataArr(1, $resultDatas, '');

//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
    }

    /**
     * @OA\Post(
     *     path="/api/admin/payment_project/ajax_save",
     *     tags={"大后台-付款/收款相关的-付款/收款项目"},
     *     summary="付款/收款项目--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlPaymentProjectAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_payment_project_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_payment_project"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                return CTAPIPaymentProjectBusiness::replaceInfo($request, $this, 0);
        });
    }

    /**
     * ajax保存数据--收款
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_pay_save(Request $request)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){

                $pay_company_id = CommonRequest::getInt($request, 'pay_company_id');
                $pay_user_id = CommonRequest::getInt($request, 'pay_user_id');
                return CTAPIPaymentRecordBusiness::paySave($request, $this, $pay_company_id, $pay_user_id, 0);
            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/payment_project/ajax_alist",
     *     tags={"大后台-付款/收款相关的-付款/收款项目"},
     *     summary="付款/收款项目--列表",
     *     description="付款/收款项目--列表......",
     *     operationId="adminQualityControlPaymentProjectAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_payment_project_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_payment_project"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_payment_project"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
//        $this->InitParams($request);
//        return  CTAPIPaymentProjectBusiness::getList($request, $this, 2 + 4);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // 根据条件获得项目列表数据
//            $mergeParams = [
//                'company_id' => $this->user_id,
//            ];
//            CTAPIAbbbBusiness::mergeRequest($request, $this, $mergeParams);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIPaymentProjectBusiness::getRelationConfigs($request, $this,
                    [
                        'resource_list' => '',
                        'company_info' => '',
                        'project_fields' => '',
                        'invoice_template_name' => '',
                        'invoice_project_template_name' => ''
                    ], []),
                // 'listHandleKeyArr' => ['priceIntToFloat'],
                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat', 'initTypeNoText'],
                'finalHandleKeyArr'=> ['sysFormatFieldsData'],
            ];
            return  CTAPIPaymentProjectBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
        });
    }

    /**
     * 选择短信模板页面
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sms_send(Request $request)
    {
        return $this->exeDoPublicFun($request, 34359738368, 8, 'admin.QualityControl.SmsTemplate.sms_send', true
            , '', [], function (&$reDataArr) use ($request){
                $sms_operate_type = 1;// 操作类型 1 发送短信  ; 2测试发送短信
                $reDataArr['sms_operate_type'] = $sms_operate_type;
                // 设置参数
                $mergeParams = [// template_id 与 module_id 二选一
                    // 'sms_template_id' => 1,// 短信模板id;--可为0 ；
                    'sms_module_id' => 1,// 短信模块id
                ];
                CTAPISmsTemplateBusiness::mergeRequest($request, $this, $mergeParams);

                $smsMobileFieldKV = ['mobile' => '手机号'];// 可以发送短信的手机号字段
                $smsMobileField = 'mobile';// 默认的发送短信的手机号字段
                $reDataArr['smsMobileFieldKV'] = $smsMobileFieldKV;
                $reDataArr['defaultSmsMobileField'] = $smsMobileField;
                CTAPISmsTemplateBusiness::smsSend($request,  $this, $reDataArr);
            });
    }

    /**
     * ajax发送手机短信
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_sms_send(Request $request){
        return $this->exeDoPublicFun($request, 68719476736, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // 根据条件获得项目列表数据
//            $mergeParams = [
//                'company_id' => $this->user_id,
//            ];
//            CTAPIAbbbBusiness::mergeRequest($request, $this, $mergeParams);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
//                    ['resource_list' => '', 'company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
//                'listHandleKeyArr' => ['priceIntToFloat'],
//                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat', 'initTypeNoText'],
//                'finalHandleKeyArr'=> ['sysFormatFieldsData'],
            ];
            return CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
        });
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIPaymentProjectBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//        return $this->exeDoPublicFun($request, 4294967296, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $result = CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//            $data_list = $result['result']['data_list'] ?? [];
//            $ids = implode(',', array_column($data_list, 'id'));
//            return ajaxDataArr(1, $ids, '');
//        });
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIPaymentProjectBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//        });
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPIPaymentProjectBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/admin/payment_project/ajax_del",
     *     tags={"大后台-付款/收款相关的-付款/收款项目"},
     *     summary="付款/收款项目--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlPaymentProjectAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_payment_project_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_payment_project"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request)
    {
//        $this->InitParams($request);
//        return CTAPIPaymentProjectBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            // return CTAPIPaymentProjectBusiness::delAjax($request, $this);
            $organize_id = 0;// $this->user_id;// CommonRequest::getInt($request, 'company_id');// 可有此参数
            return CTAPIPaymentProjectBusiness::delCustomizeAjax($request,  $this, $organize_id, [], '');
        });
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIPaymentProjectBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIPaymentProjectBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//        return $this->exeDoPublicFun($request, 8589934592, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $parent_id = CommonRequest::getInt($request, 'parent_id');
//            // 获得一级城市信息一维数组[$k=>$v]
//            $childKV = CTAPIRrrDdddBusiness::getCityByPid($request, $this, $parent_id);
//            // $childKV = CTAPIRrrDdddBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//            return  ajaxDataArr(1, $childKV, '');
//        });
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIPaymentProjectBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
///
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $fileName = 'staffs.xlsx';
//            $resultDatas = CTAPIRrrDdddBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import(Request $request)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPIPaymentProjectBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIPaymentProjectBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }



    /**
     * 单文件上传-上传文件
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_file(Request $request)
    {
        $this->InitParams($request);
        // $this->company_id = 1;
        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');

        // 上传并保存文件
        return CTAPIResourceBusiness::filePlupload($request, $this, 1);//  | 2 | 8 | 16
    }

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

        // 获得试题分类KV值
        $reDataArr['type_no_kv'] = CTAPIPaymentTypeBusiness::getListKV($request, $this, ['key' => 'type_no', 'val' => 'type_name'], [
            'sqlParams' => ['orderBy' => PaymentType::ORDER_BY]// 'where' => [['open_status', 1]],
        ]);
        $reDataArr['defaultTypeNo'] = 0;// 默认

        // 是否指定金额1用户输入金额；2卖家输入金额；4指定金额【可以输入优惠金额】
        $reDataArr['specifiedAmountStatus'] =  PaymentProject::SPECIFIED_AMOUNT_STATUS_ARR;
        $reDataArr['defaultSpecifiedAmountStatus'] = -1;// 列表页默认状态
        // $reDataArr['regConfig']['specified_amount_status'] = Tool::getPregByKVArr(PaymentProject::SPECIFIED_AMOUNT_STATUS_ARR);
        $reDataArr['regConfig']['specified_amount_status'] = Tool::getPregByKeyArr(Tool::getBitArr(63));

        // 收费生效时间1长期有效; 2指定时间
        $reDataArr['payValidStatus'] =  PaymentProject::PAY_VALID_STATUS_ARR;
        $reDataArr['defaultPayValidStatus'] = -1;// 列表页默认状态
        $reDataArr['regConfig']['pay_valid_status'] = Tool::getPregByKVArr(PaymentProject::PAY_VALID_STATUS_ARR);

        // 有效时长1长期有效【可再次付费】；2长期有效【不可再次付费】；4指定有效时长
        $reDataArr['validLimit'] =  PaymentProject::VALID_LIMIT_ARR;
        $reDataArr['defaultValidLimit'] = -1;// 列表页默认状态
        $reDataArr['regConfig']['valid_limit'] = Tool::getPregByKVArr(PaymentProject::VALID_LIMIT_ARR);

        // 判断唯一用户付款标准1不用登录付款【不判断】； 2 登录【不判断】；  4登录【唯一用户】 ；8 登录【唯一企业】
        $reDataArr['uniqueUserStandard'] =  PaymentProject::UNIQUE_USER_STANDARD_ARR;
        $reDataArr['defaultUniqueUserStandard'] = -1;// 列表页默认状态
        $reDataArr['regConfig']['unique_user_standard'] = Tool::getPregByKVArr(PaymentProject::UNIQUE_USER_STANDARD_ARR);

        // 开通状态1开通；2关闭；4作废【过时关闭】；
        $reDataArr['openStatus'] =  PaymentProject::OPEN_STATUS_ARR;
        $reDataArr['defaultOpenStatus'] = -1;// 列表页默认状态
        $reDataArr['regConfig']['open_status'] = Tool::getPregByKVArr(PaymentProject::OPEN_STATUS_ARR);

        // 收费状态1待收费；2收费中；4已收费
        $reDataArr['payStatus'] =  PaymentProject::PAY_STATUS_ARR;
        $reDataArr['defaultPayStatus'] = -1;// 列表页默认状态
        $reDataArr['regConfig']['pay_status'] = Tool::getPregByKVArr(PaymentProject::PAY_STATUS_ARR);

        // 记录处理方式1自动处理【直接完成状态】；2人工处理
        $reDataArr['handleMethod'] =  PaymentProject::HANDLE_METHOD_ARR;
        $reDataArr['defaultHandleMethod'] = -1;// 列表页默认状态
        $reDataArr['regConfig']['handle_method'] = Tool::getPregByKVArr(PaymentProject::HANDLE_METHOD_ARR);

        // 获得收款帐号KV值
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultPayConfig'] = -1;// 默认

        // 获得发票开票模板KV值
        $reDataArr['invoice_template_kv'] = CTAPIInvoiceTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultInvoiceTemplate'] = -1;// 默认

        // 获得发票商品项目模板KV值
        $reDataArr['invoice_project_template_kv'] = CTAPIInvoiceProjectTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultInvoiceProjectTemplate'] = -1;// 默认
        // 收款开通类型(1现金、2微信支付、4支付宝)
        $disablePayMethod = [];
        $payKVList = [];
        list($payKVList, $disablePayMethod, $payMethodList, $formatPayMethodList) = OrderPayMethodDBBusiness::getPayMethodDisable($disablePayMethod, $payKVList);

        $reDataArr['payMethod'] =  $payKVList;
        $reDataArr['defaultPayMethod'] = -1;// 列表页默认状态
        // $reDataArr['payMethodDisable'] = $disablePayMethod;// 不可用的--禁用

        $reDataArr['hidden_option'] = $hiddenOption;

        $company_id = CommonRequest::getInt($request, 'company_id');// $this->user_id;
        // $hiddenOption |= 1;
        $info = [];
        if(is_numeric($company_id) && $company_id > 0){
            // 获得企业信息
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
        }
        $reDataArr['info'] = $info;
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

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
        $info = [
            'id'=>$id,
            //   'department_id' => 0,
            'company_id' => 0,
            'user_company_name' => ''
        ];
        $operate = "添加";

        // 如果是企业列表点《企业简介》
        $company_id = CommonRequest::getInt($request, 'company_id');// $this->user_id;
        // $hiddenOption |= 1;
        if($id <= 0 && $company_id > 0){
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
        }

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIPaymentProjectBusiness::getRelationConfigs($request, $this,
                    [
                        'resource_list' => '',
                        'company_info' => '',
                        'project_fields' => '',
                        'invoice_template_name' => '',
                        'invoice_project_template_name' => ''
                    ], []),
                // 'listHandleKeyArr' => ['priceIntToFloat'],
                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat'],// , 'initTypeNoText'
                'finalHandleKeyArr'=> ['sysFormatFieldsData'],
            ];
            $info = CTAPIPaymentProjectBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        // 获得试题分类KV值
        $reDataArr['type_no_kv'] = CTAPIPaymentTypeBusiness::getListKV($request, $this, ['key' => 'type_no', 'val' => 'type_name'], [
            'sqlParams' => ['orderBy' => PaymentType::ORDER_BY]// 'where' => [['open_status', 1]],
        ]);
        $reDataArr['defaultTypeNo'] = $info['type_no'] ?? 0;// 默认

        // 是否指定金额1用户输入金额；2卖家输入金额；4指定金额【可以输入优惠金额】
        $reDataArr['specifiedAmountStatus'] =  PaymentProject::SPECIFIED_AMOUNT_STATUS_ARR;
        $reDataArr['defaultSpecifiedAmountStatus'] = $info['specified_amount_status'] ?? -1;// 列表页默认状态
        // $reDataArr['regConfig']['specified_amount_status'] = Tool::getPregByKVArr(PaymentProject::SPECIFIED_AMOUNT_STATUS_ARR);
        $reDataArr['regConfig']['specified_amount_status'] = Tool::getPregByKeyArr(Tool::getBitArr(63));

        // 收费生效时间1长期有效; 2指定时间
        $reDataArr['payValidStatus'] =  PaymentProject::PAY_VALID_STATUS_ARR;
        $reDataArr['defaultPayValidStatus'] = $info['pay_valid_status'] ?? -1;// 列表页默认状态
        $reDataArr['regConfig']['pay_valid_status'] = Tool::getPregByKVArr(PaymentProject::PAY_VALID_STATUS_ARR);

        // 有效时长1长期有效【可再次付费】；2长期有效【不可再次付费】；4指定有效时长
        $reDataArr['validLimit'] =  PaymentProject::VALID_LIMIT_ARR;
        $reDataArr['defaultValidLimit'] = $info['valid_limit'] ?? -1;// 列表页默认状态
        $reDataArr['regConfig']['valid_limit'] = Tool::getPregByKVArr(PaymentProject::VALID_LIMIT_ARR);

        // 判断唯一用户付款标准1不用登录付款【不判断】； 2 登录【不判断】；  4登录【唯一用户】 ；8 登录【唯一企业】
        $reDataArr['uniqueUserStandard'] =  PaymentProject::UNIQUE_USER_STANDARD_ARR;
        $reDataArr['defaultUniqueUserStandard'] = $info['unique_user_standard'] ?? -1;// 列表页默认状态
        $reDataArr['regConfig']['unique_user_standard'] = Tool::getPregByKVArr(PaymentProject::UNIQUE_USER_STANDARD_ARR);

        // 开通状态1开通；2关闭；4作废【过时关闭】；
        $reDataArr['openStatus'] =  PaymentProject::OPEN_STATUS_ARR;
        $reDataArr['defaultOpenStatus'] = $info['open_status'] ?? -1;// 列表页默认状态
        $reDataArr['regConfig']['open_status'] = Tool::getPregByKVArr(PaymentProject::OPEN_STATUS_ARR);

        // 收费状态1待收费；2收费中；4已收费
        $reDataArr['payStatus'] =  PaymentProject::PAY_STATUS_ARR;
        $reDataArr['defaultPayStatus'] = $info['pay_status'] ?? -1;// 列表页默认状态
        $reDataArr['regConfig']['pay_status'] = Tool::getPregByKVArr(PaymentProject::PAY_STATUS_ARR);

        // 记录处理方式1自动处理【直接完成状态】；2人工处理
        $reDataArr['handleMethod'] =  PaymentProject::HANDLE_METHOD_ARR;
        $reDataArr['defaultHandleMethod'] = $info['handle_method'] ?? -1;// 列表页默认状态
        $reDataArr['regConfig']['handle_method'] = Tool::getPregByKVArr(PaymentProject::HANDLE_METHOD_ARR);

        // 获得收款帐号KV值
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultPayConfig'] = $info['pay_config_id'] ?? -1;// 默认

        // 获得发票开票模板KV值
        $reDataArr['invoice_template_kv'] = CTAPIInvoiceTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultInvoiceTemplate'] = $info['invoice_template_id'] ?? -1;// 默认

        // 获得发票商品项目模板KV值
        $reDataArr['invoice_project_template_kv'] = CTAPIInvoiceProjectTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultInvoiceProjectTemplate'] = $info['invoice_project_template_id'] ?? -1;// 默认
        // 收款开通类型(1现金、2微信支付、4支付宝)
        $disablePayMethod = [];
        $payKVList = [];
        list($payKVList, $disablePayMethod, $payMethodList, $formatPayMethodList) = OrderPayMethodDBBusiness::getPayMethodDisable($disablePayMethod, $payKVList);

        $reDataArr['payMethod'] =  $payKVList;
        $reDataArr['defaultPayMethod'] = $info['pay_method'] ?? -1;// 列表页默认状态
        // $reDataArr['payMethodDisable'] = $disablePayMethod;// 不可用的--禁用

        // 字段相关的选项
        $reDataArr['fieldsConfig']['val_type'] = PaymentProjectFields::VAL_TYPE_ARR;// 字段值类型1输入框 ；2多行文本 ； 4富文本；8单选框；16复选框
        $reDataArr['fieldsConfig']['required_status'] = PaymentProjectFields::REQUIRED_STATUS_ARR;// 是否必填1可填；2必填
        $reDataArr['fieldsConfig']['input_status'] = PaymentProjectFields::INPUT_STATUS_ARR;// 填写终端1客户端；2企业端
        $reDataArr['fieldsConfig']['show_status'] = PaymentProjectFields::SHOW_STATUS_ARR;// 显示终端1客户端；2企业端

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
