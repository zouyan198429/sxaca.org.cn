<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIInvoiceConfigHydzfpBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayConfigBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Invoice\hydzfp\InvoiceHydzfp;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceConfigHydzfpController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 测试
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function test(Request $request){
        $companyConfig = InvoiceHydzfp::$companyConfig;
        $apiDataMode = 0;
        // 获取access token
//        $accessTokenConfig = InvoiceHydzfp::getAccessToken($companyConfig['open_id'], $companyConfig['app_secret'], false);
//        pr($accessTokenConfig);
        // D0001-查询税收编码(单个商品)
        // $result = InvoiceHydzfp::esdSkdataQueryItemInfo($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // D0003-查询税收编码(分页获取)
        // $result = InvoiceHydzfp::esdSkdataQueryItemsList($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);

        // A0001-开具蓝字发票
        // $result = InvoiceHydzfp::ebiInvoiceHandleNewBlueInvoice([], $companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // A0002-开具红字发票
        // $result = InvoiceHydzfp::ebiInvoiceHandleNewRedInvoice($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // A0003-获取发票或使用抬头开票
        // $result = InvoiceHydzfp::ebiInvoiceHandleNewOrGetBlueInvoice($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // A0004-历史发票数据导入平台
        // $result = InvoiceHydzfp::ebiInvoiceHandleNewPDFInvoice($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // A0005-开具红字发票(部分冲红)
        // $result = InvoiceHydzfp::ebiInvoiceHandleNewRedApiInvoice($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);


        // B0002-查单个发票
        // $result = InvoiceHydzfp::ebiInvoiceHandleQueryInvoice($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // B0003-查单个发票(简易查询)
        // $result = InvoiceHydzfp::ebiInvoiceHandleQueryInvoice4DMDH($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // B0004-税控盒子开票状态查询
        // $result = InvoiceHydzfp::ebiInvoiceHandleGetInvoiceStatus($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);

        // C0001-平台在线交付
        // $result = InvoiceHydzfp::ebiInvoiceHandleNewInvoiceDelay($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // C0002-获取平台交付二维码
        // $result = InvoiceHydzfp::ebiInvoiceHandleGetInvoiceDelayQRCode($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // C0003-获取预览用PDF图片
        // $result = InvoiceHydzfp::epPdfGetPdfImgByte($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // C0004-获取原始PDF文件
        // $result = InvoiceHydzfp::epPdfGetPdfByte($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // C0005-获取发票PDF下载地址
        // $result = InvoiceHydzfp::ebiInvoiceHandleGetInvoiceDownloadUrl($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);

        // WX_CARD_001-获取授权链接
        // $result = InvoiceHydzfp::piaojuHydzfpCardGetAtuhurl($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);
        // 合作商户-查单个发票(完整信息)
        // $result = InvoiceHydzfp::ebiCmQueryInvoice($companyConfig['open_id'], $companyConfig['app_secret'], $apiDataMode,  false);

        // E0001-获取离线开票authid
        // $result = InvoiceHydzfp::getAuthidConfig($companyConfig['open_id'], $companyConfig['app_secret'], $companyConfig['tax_num'], $apiDataMode, false);
        // E0002设置二维码有效期
        // $result = InvoiceHydzfp::offlineAuthIdSetQRTerm($companyConfig['open_id'], $companyConfig['app_secret'], $companyConfig['tax_num'], $apiDataMode, false);
        // E0003-上传待开发票数据
        // $result = InvoiceHydzfp::offlineOrigUploadData($companyConfig['open_id'], $companyConfig['app_secret'], $companyConfig['tax_num'], $apiDataMode, false);
        // E0004-客户端获取某订单信息
        // $result = InvoiceHydzfp::offlineOrigGetData($companyConfig['open_id'], $companyConfig['app_secret'], $companyConfig['tax_num'], $apiDataMode, false);
        // E0005-客户端绑定待开发票数据
        // $result = InvoiceHydzfp::offlineOrigBindData($companyConfig['open_id'], $companyConfig['app_secret'], $companyConfig['tax_num'], $apiDataMode, false);
        // E0006-客户端获取已绑定数据列表
        // $result = InvoiceHydzfp::offlineOrigList($companyConfig['open_id'], $companyConfig['app_secret'], $companyConfig['tax_num'], $apiDataMode, false);

        pr(json_encode($result));
    }

    /**
     *  E0018-开票结果通知
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function notify(Request $request){
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
            'post_data' => apiGetPost(),
        ];
        Log::info('电子发票接口日志 沪友   开票结果通知--回调-->' . __FUNCTION__, $requestLog);
        $resultData = apiGetPost();
        if (!isNotJson($resultData)) $data = json_decode($resultData , true);

        /**
         *
        $resultData = [
                "result" => "SUCCESS",// String	开票结果SUCCESS-开票成功,其他值为开票失败
                "data" => [// aaaaa
                    "bz" => "备注",// String	备注
                    "ext_code" => "iveryllxf3",// String	提取码
                    "fhr" => "复核人",// String	复核人
                    "fp_dm" => "050003521270",// String	发票代码
                    "fp_hm" => "69023573",// String	发票号码
                    "gmf_dzdh" => "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",// String	购买方地址电话
                    "gmf_mc" => "西安卓彩广告装饰有限公司",// String	购买方名称
                    "gmf_nsrsbh" => "610113399802848",// String	购买方纳税人识别号
                    "gmf_yhzh" => "华夏银行股份有限公司西安分行营业部11450000000871357",// String	购买方银行账号
                    "hjje" => ".97",// String	合计金额
                    "hjse" => ".03",// String	合计税额
                    "id" => "45f42cf88a624cc1ac8f071c49f76aba",// String	发票唯一id
                    "items" => [// aaaaa
                        [
                            "dw" => "",// String	单位
                            "fphxz" => "0",// String	发票行性质
                            "fpqqlsh" => "",// aaaaa
                            "ggxh" => "",// String	规格型号
                            "id" => "B7680F97FB55B633E0538F00A8C06FC4",// String	发票id
                            "invoice_id" => "45f42cf88a624cc1ac8f071c49f76aba",// String	发票唯一id
                            "kprq" => "",// aaaaa
                            "lslbs" => "",// String	零税率标识
                            "order_num" => "",// aaaaa
                            "se" => ".03",// String	税额
                            "sl" => "0.03",// String	税率
                            "spbm" => "3070201020000000000",// String	商品编码
                            "xmdj" => ".97087379",// String	项目单价
                            "xmdj_old" => "",// aaaaa
                            "xmje" => ".97",// String	项目金额
                            "xmmc" => "*非学历教育服务**非学历教育服务*培训费",// String	项目名称
                            "xmsl" => "1",// String	项目数量
                            "yhzcbs" => "0",// String	优惠政策标识
                            "zxbm" => "",// String	自行编码
                            "zzstsgl" => ""// String	增值税特殊管理
                        ]
                    ],
                    "jshj" => "1",// String	价税合计
                    "jym" => "06801466665358756235",// String	校验码
                    "kce" => "",// String	扣除额
                    "kplx" => "0",// String	开票类型
                    "kpr" => "开票员",// String	开票人
                    "kprq" => "20201230153752",// String	开票日期
                    "order_id" => "12020123015001500520000055834854",// String	订单id
                    "order_num" => "1120512670520001",// String	业务单据号
                    "pdf_item_key" => "pdf_detail_OMKjM791609317875383",// String	发票清单pdf获取key
                    "pdf_key" => "pdf_OMKjM791609317875383",// String	发票pdf获取key
                    "skr" => "收款人",// String	收款人
                    "tspz" => "00",// String	特殊票种
                    "xsf_dzdh" => "上海市某某路0232345678",// String	销售方地址电话
                    "xsf_mc" => "江苏百旺金赋信息科技有限公司",// String	销货方名称
                    "xsf_nsrsbh" => "91320106598035469W",// String	销售方纳税人识别号
                    "xsf_yhzh" => "环球银行123456",// String	销售方银行账号
                    "yfp_dm" => "",// String	原发票代码
                    "yfp_hm" => "",// String	原发票号码
                    "zsfs" => "0"// String	征收方式
                ],
                "mac" => "",// aaaaa
                "msg" => "开票成功",// String	开票结果描述
                "key" => ""// aaaaa
            ];
         *
         */

        $result = $resultData['result'] ?? 0;
        $msg = $resultData['msg'] ?? '返回数据错误!';
        $data = $resultData['data'] ?? [];
        if ($result != 'SUCCESS'){
            // throws('沪友接口错误:' . $msg);
        }else{

        }

        $result = [
            "msg" => "成功",
            "result" => "SUCCESS",
            "rows" => []
        ];
        return jsonAPIFormatData($result);// 接口json返回数据
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
//            return view('admin.QualityControl.InvoiceConfigHydzfp.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.InvoiceConfigHydzfp.index', true
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
//            $reDataArr['province_kv'] = CTAPIInvoiceConfigHydzfpBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIInvoiceConfigHydzfpBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('admin.QualityControl.InvoiceConfigHydzfp.select', $reDataArr);
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
//            return view('admin.QualityControl.InvoiceConfigHydzfp.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.InvoiceConfigHydzfp.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/invoice_config_hydzfp/ajax_info",
     *     tags={"大后台-订单管理-发票配置沪友"},
     *     summary="发票配置沪友--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlInvoiceConfigHydzfpAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_invoice_config_hydzfp_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_invoice_config_hydzfp"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_invoice_config_hydzfp"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPIInvoiceConfigHydzfpBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');

        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @OA\Post(
     *     path="/api/admin/invoice_config_hydzfp/ajax_save",
     *     tags={"大后台-订单管理-发票配置沪友"},
     *     summary="发票配置沪友--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlInvoiceConfigHydzfpAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_invoice_config_hydzfp_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_invoice_config_hydzfp"}
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
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');
                $tax_num = CommonRequest::get($request, 'tax_num');
                $open_id = CommonRequest::get($request, 'open_id');
                $app_secret = CommonRequest::get($request, 'app_secret');

                $saveData = [
                    'pay_config_id' => $pay_config_id,
                    'tax_num' => $tax_num,
                    'open_id' => $open_id,
                    'app_secret' => $app_secret,
                ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPIInvoiceConfigHydzfpBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
        });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/invoice_config_hydzfp/ajax_alist",
     *     tags={"大后台-订单管理-发票配置沪友"},
     *     summary="发票配置沪友--列表",
     *     description="发票配置沪友--列表......",
     *     operationId="adminQualityControlInvoiceConfigHydzfpAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_invoice_config_hydzfp_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_invoice_config_hydzfp"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_invoice_config_hydzfp"}
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
//        return  CTAPIInvoiceConfigHydzfpBusiness::getList($request, $this, 2 + 4);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIInvoiceConfigHydzfpBusiness::getRelationConfigs($request, $this,
                    [
                        'pay_company_name' => '',
                    ], []),
                // 'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            return  CTAPIInvoiceConfigHydzfpBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
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

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
//                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
//                'listHandleKeyArr' => ['priceIntToFloat'],
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
//        $result = CTAPIInvoiceConfigHydzfpBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIInvoiceConfigHydzfpBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPIInvoiceConfigHydzfpBusiness::getRelationConfigs($request, $this,
//                    [
//                        'pay_company_name' => '',
//                    ], []),
//                // 'listHandleKeyArr' => ['priceIntToFloat'],
//            ];
//            CTAPIInvoiceConfigHydzfpBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
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
//        CTAPIInvoiceConfigHydzfpBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/admin/invoice_config_hydzfp/ajax_del",
     *     tags={"大后台-订单管理-发票配置沪友"},
     *     summary="发票配置沪友--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlInvoiceConfigHydzfpAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_invoice_config_hydzfp_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_invoice_config_hydzfp"}
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
//        return CTAPIInvoiceConfigHydzfpBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            return CTAPIInvoiceConfigHydzfpBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIInvoiceConfigHydzfpBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIInvoiceConfigHydzfpBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPIInvoiceConfigHydzfpBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIInvoiceConfigHydzfpBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIInvoiceConfigHydzfpBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
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

        // 获得收款帐号KV值
        $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');// $hiddenOption = 2
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name'], []);// ['sqlParams' => ['where' => [['open_status', 1]]]]
        $reDataArr['defaultPayConfig'] = (!is_numeric($pay_config_id) || $pay_config_id <= 0 ) ? -1 : $pay_config_id;// 默认

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

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
        $info = [
            'id'=>$id,
            //   'department_id' => 0,
        ];
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIInvoiceConfigHydzfpBusiness::getRelationConfigs($request, $this,
                [
                    'pay_company_name' => '',
                ], []),
            // 'listHandleKeyArr' => ['priceIntToFloat'],
        ];

        // 如果是指定的
        $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');// $hiddenOption = 2
        $dataInfo = [];
        if($id <= 0 && $pay_config_id > 0){
            // 获得记录
            $dataInfo = CTAPIInvoiceConfigHydzfpBusiness::getFVFormatList( $request,  $this, 4, 1
                , ['pay_config_id' => $pay_config_id], false, [], $extParams);
            $id = $dataInfo['id'] ?? 0;
        }

        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            if(empty($contentInfo)) {
                $info = CTAPIInvoiceConfigHydzfpBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            }else{
                $info = $dataInfo;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        // 获得收款帐号KV值
        $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');// $hiddenOption = 2
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name'], []);// ['sqlParams' => ['where' => [['open_status', 1]]]]
        $reDataArr['defaultPayConfig'] = $info['pay_config_id'] ?? ((!is_numeric($pay_config_id) || $pay_config_id <= 0 ) ? -1 : $pay_config_id);// 默认

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
