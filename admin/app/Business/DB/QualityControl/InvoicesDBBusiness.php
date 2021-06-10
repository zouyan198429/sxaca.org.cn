<?php
// 发票主表
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\InvoiceOrderFlow;
use App\Models\QualityControl\InvoiceProject;
use App\Models\QualityControl\Invoices;
use App\Services\DB\CommonDB;
use App\Services\File\DownFile;
use App\Services\Invoice\hydzfp\InvoiceHydzfp;
use App\Services\Tool;
use App\Services\Upload\UploadFile;
use Carbon\Carbon;

/**
 *
 */
class InvoicesDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Invoices';
    public static $table_name = 'invoices';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['order_num']) && empty($saveData['order_num'])  ){
            throws('业务单据号不能为空！');
        }

        // 电子发票项目
        $invoice_project = [];
        $has_invoice_project = false;// 是否有方法修改 false:没有 ； true:有
        Tool::getInfoUboundVal($saveData, 'invoice_project', $has_invoice_project, $invoice_project, 1);

        // 订单发票关联表
        $invoice_order = [];
        $has_invoice_order = false;// 是否有方法修改 false:没有 ； true:有
        Tool::getInfoUboundVal($saveData, 'invoice_order', $has_invoice_order, $invoice_order, 1);


        // 是否有图片资源
        $hasResource = false;

        $resourceIds = [];
        if(Tool::getInfoUboundVal($saveData, 'resourceIds', $hasResource, $resourceIds, 1)){
            // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
        }

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify
            , &$invoice_project, &$has_invoice_project, &$invoice_order, &$has_invoice_order, &$hasResource, &$resourceIds){


            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

            // 发票配置购买方id
            InvoiceBuyerDBBusiness::appendFieldIdHistory($saveData, 'invoice_buyer_id', 'invoice_buyer_id_history');

            // 发票配置销售方id
            InvoiceSellerDBBusiness::appendFieldIdHistory($saveData, 'invoice_seller_id', 'invoice_seller_id_history');

            // 发票开票模板id
            InvoiceTemplateDBBusiness::appendFieldIdHistory($saveData, 'invoice_template_id', 'invoice_template_id_history');

            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改

                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                if($has_invoice_order){
                    $resultDatas = static::getInfo($id);
                }
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            // 同步修改图片资源关系
            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 8, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 8, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 2048, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }

            // 如果有电子发票项目 修改
            if($has_invoice_project){
                $invoice_project_ids = InvoiceProjectDBBusiness::updateByDataList(['invoice_id' => $id], ['invoice_id' => $id]
                    , $invoice_project, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 如果有订单发票关联表 修改
            if($has_invoice_order){
                $order_num = $resultDatas['order_num'];
                $invoice_order_ids = InvoiceOrderDBBusiness::updateByDataList(['order_num' => $order_num], ['order_num' => $order_num]
                    , $invoice_order, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * 生成订单
     *
     * @param int  $company_id 企业id 或用户id--无所属企业
     * @param int  $invoice_result 开票结果1待开票1已开蓝票2已冲红
     * @param int $organize_id 操作的所属企业id
     * @param array $ordersList 订单号数组 二维
     * @param array $invoiceOrderInfo 电子发票数据
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return string 业务单号
     *
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrder($company_id, $invoice_result = 1, $organize_id = 0, $ordersList = [],  $invoiceOrderInfo = [], $operate_staff_id = 0, $modifAddOprate = 0){

        $order_num = static::createSn($organize_id , $operate_staff_id, 51);
        $invoiceOrderInfo['order_num'] = $order_num;// 加入订单号
        // 加入订单号
        Tool::arrAppendKeys($invoiceOrderInfo['invoice_project'], ['order_num' => $order_num]);

        $order_nos = Tool::getArrFields($ordersList, 'order_no');
        // 订单发票关联表
        $invoiceOrderData = [];
        foreach($order_nos as $k => $tem_order_no){
            array_push($invoiceOrderData, [
                'id' => 0,
                'order_no' => $tem_order_no,
                'order_num' => $order_num,
            ]);
        }
        $invoiceOrderInfo['invoice_order'] = $invoiceOrderData; // 订单发票关联表

        $nowTime = date('Y-m-d H:i:s');

        $invoiceNewFields = [
            'invoice_status' => 1 , // 开票状态1待开票2开票中4已开票
            'upload_status' => 1 , // 开票数据状态1待上传2已上传4已开票8已作废16已冲红
            'kprq' => date('YmdHis'),// $tem_v['kprq'] ?? '' , // 开票日期(20161107145525格式：yyyymmddhhmiss)
            'create_time' => $nowTime , // 生成时间
        ];
        if($invoice_result == 1) {// 蓝票
            $invoiceNewFields['kplx'] = '0';// $tem_v['kplx'] ?? '0' , // 开票类型 0-蓝字发票；1-红字发票
        }else{// 红票
            $invoiceNewFields['kplx'] = '1';// $tem_v['kplx'] ?? '0' , // 开票类型 0-蓝字发票；1-红字发票
        }
        $invoiceOrderInfo = array_merge($invoiceOrderInfo, $invoiceNewFields);
        // 价格转为整型
        Tool::bathPriceCutFloatInt($invoiceOrderInfo['invoice_project'], InvoiceProject::$IntPriceFields, InvoiceProject::$IntPriceIndex, 1);
        Tool::bathPriceCutFloatInt($invoiceOrderInfo, Invoices::$IntPriceFields, Invoices::$IntPriceIndex, 1);
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id,  &$invoiceOrderInfo, &$operate_staff_id,
            &$modifAddOprate, &$nowTime, &$order_num, &$order_nos, &$ordersList, &$invoice_result){
            $invoiceId = 0;


            // 生成电子发票信息
            static::replaceById($invoiceOrderInfo, $company_id, $invoiceId, $operate_staff_id, $modifAddOprate);
            // 修改订单信息
            $updateOrder = [
                'invoice_result' => $invoice_result,// 开票结果1待开票1已开蓝票2已冲红
                'invoice_status' => $invoiceOrderInfo['invoice_status'],// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                'upload_status' => $invoiceOrderInfo['upload_status'],// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                // 'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                // 'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                // 'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                // 'invoice_red_time' => $nowTime,// 充红时间【最近一次】
            ];
            if($invoice_result == 1){// 蓝票
                // 发票配置购买方id
                $invoice_buyer_id = $invoiceOrderInfo['invoice_buyer_id'];
                $updateOrder['invoice_buyer_id'] = $invoice_buyer_id;
                InvoiceBuyerDBBusiness::appendFieldIdHistory($updateOrder, 'invoice_buyer_id', 'invoice_buyer_id_history');
                // $invoice_buyer_id_history = InvoiceBuyerDBBusiness::getIdHistory($invoice_buyer_id);
                // 发票开票模板id
                $invoice_template_id = $invoiceOrderInfo['invoice_template_id'];
                $updateOrder['invoice_template_id'] = $invoice_template_id;
                InvoiceTemplateDBBusiness::appendFieldIdHistory($updateOrder, 'invoice_template_id', 'invoice_template_id_history');
                // $invoice_template_id_history = InvoiceTemplateDBBusiness::getIdHistory($invoice_template_id);
                $updateOrder = array_merge($updateOrder,[
                    // 'invoice_buyer_id' => $invoice_buyer_id,// 发票配置购买方id
                    // 'invoice_buyer_id_history' => $invoice_buyer_id_history,// 发票配置购买方id历史【开票时更新】
                    // 'invoice_template_id' => $invoice_template_id,// 发票开票模板id
                    // 'invoice_template_id_history' => $invoice_template_id_history,// 发票开票模板id历史
                    'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                    // 'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                    'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                    // 'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                ]);
            }else{// 红票
                $updateOrder = array_merge($updateOrder,[
                    // 'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                    'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                    // 'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                    'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                ]);

            }
            $saveQueryParams = Tool::getParamQuery(['order_no' => $order_nos],[], []);
            OrdersDBBusiness::save($updateOrder, $saveQueryParams);

        });

        return $order_num;
    }


    /**
     * 请求接口生成电子发票-- 生成蓝票
     *
     * @param int  $company_id 企业id 或用户id--无所属企业
     * @param int $organize_id 操作的所属企业id
     * @param string $order_num 订单号数组 二维
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return string 业务单号
     *
     * @author zouyan(305463219@qq.com)
     */
    public static function apiSendInvoice($company_id, $organize_id = 0, $order_num = '', $operate_staff_id = 0, $modifAddOprate = 0){
        $invoiceInfo = static::getDBFVFormatList(4, 1, ['order_num' => $order_num]
            , false, '', []);
        if(empty($invoiceInfo)) throws('发票记录不存在！');
        if(!in_array($invoiceInfo['invoice_status'], [1]) ) throws('开票状态非待开票！');
        if(!in_array($invoiceInfo['upload_status'], [1]) ) throws('开票数据状态非待上传！');
        //整数转为小数
        Tool::bathPriceCutFloatInt($invoiceInfo, Invoices::$IntPriceFields, Invoices::$IntPriceIndex, 2, 2);
        $invoice_service = $invoiceInfo['invoice_service'];
        $kplx = $invoiceInfo['kplx'];// 开票类型 0-蓝字发票；1-红字发票
        $invoice_id = $invoiceInfo['id'];
        $pay_config_id = $invoiceInfo['pay_config_id'];
        $invoice_seller_id_history = $invoiceInfo['invoice_seller_id_history'];
        $invoice_buyer_id_history = $invoiceInfo['invoice_buyer_id_history'];
        $invoice_template_id_history = $invoiceInfo['invoice_template_id_history'];

        // 获得发票项目
        $invoiceProjectList = InvoiceProjectDBBusiness::getDBFVFormatList(1, 1, ['order_num' => $order_num, 'invoice_id' => $invoice_id], false);
        if(empty($invoiceProjectList)) throws('发票项目记录不存在！');
        //整数转为小数
        Tool::bathPriceCutFloatInt($invoiceProjectList, InvoiceProject::$IntPriceFields, InvoiceProject::$IntPriceIndex, 2, 2);

        // 获得订单发票关联表
        $invoiceOrderList = InvoiceOrderDBBusiness::getDBFVFormatList(1, 1, ['order_num' => $order_num], false);
        if(empty($invoiceOrderList)) throws('订单发票关联记录不存在！');
        $order_nos = Tool::getArrFields($invoiceOrderList, 'order_no');

        // 获得订单信息
        $orderList = OrdersDBBusiness::getDBFVFormatList(1, 1, ['order_no' => $order_nos], false);
        if(empty($orderList)) throws('订单记录不存在！');


        // 获得销售方信息
        $invoiceSellerInfo = InvoiceSellerHistoryDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_seller_id_history]
            , false, '', []);
        if(empty($invoiceSellerInfo)) throws('【销售方开票信息】记录不存在！');
        // if(!in_array($invoiceSellerInfo['open_status'], [1])) throws('【销售方开票信息】非开启状态！');
        $invoice_seller_id = $invoiceSellerInfo['invoice_seller_id'];

        // 获得购买方信息
        $invoiceBuyerInfo = InvoiceBuyerHistoryDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_buyer_id_history]
            , false, '', []);
        if(empty($invoiceBuyerInfo)) throws('【购买方开票信息】记录不存在！');
        // if($organize_id != $invoiceBuyerInfo['company_id'])  throws('您没有【购买方开票信息】操作此记录的权限！');
        // if(!in_array($invoiceBuyerInfo['open_status'], [1])) throws('【购买方开票信息】非开启状态！');

        $invoiceTemplateInfo = InvoiceTemplateHistoryDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_template_id_history]
            , false, '', []);
        if(empty($invoiceTemplateInfo)) throws('【发票开票模板】记录不存在！');
        // if(!in_array($invoiceTemplateInfo['open_status'], [1])) throws('【发票开票模板】非开启状态！');

        $apiProjectList = [];

        $nowTime = date('Y-m-d H:i:s');
        switch($invoice_service){
            case 1:// 1沪友
                foreach($invoiceProjectList as $v){
                    // 发票项目
                    $apiProjectInfo = [
                        "fphxz" => "0",// 是	string	2	发票行性质 0正常行、1折扣行、2被折扣行
                        "spbm" => $v['spbm'],// $tem_spbm,// "3070201020000000000",// "",// 是	string	19	商品编码(商品编码为税务总局颁发的19位税控编码)
                        "zxbm" => $v['zxbm'],// $tem_zxbm,// "",// 否	string	20	自行编码(一般不建议使用自行编码)
                        "yhzcbs" => $v['yhzcbs'],// $tem_yhzcbs,// "0",// "",//否	string	2	优惠政策标识 0：不使用，1：使用
                        "lslbs" => $v['lslbs'],// $tem_lslbs,// "",// 否	string	2	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
                        // 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填，
                        // 具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
                        "zzstsgl" => $v['zzstsgl'],// $tem_zzstsgl,// "",// aa  bbb
                        // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
                        // 如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
                        "xmmc" => $v['xmmc'],// $tem_good_name,// "培训费",// "更具自身业务决定",// aa  bbb
                        "ggxh" => $v['ggxh'],// $tem_ggxh,// "",// 否	string	30	规格型号(折扣行请不要传)
                        "dw" => $v['dw'],// $tem_dw,// "",// 否	string	20	计量单位(折扣行请不要传)
                        "xmsl" => $v['xmsl'],// $tem_amount,//"1",// "",// 否	string	#.######	项目数量 小数点后6位,大于0的数字
                        "xmdj" => $v['xmdj'],// $tem_price,// "1.00",// 否	string	#.######	项目单价 小数点后6位 注意：单价是含税单价,大于0的数字
                        "xmje" => $v['xmje'],// $tem_total_price,// "1.00",// 是	string	#.##	项目金额 注意：金额是含税，单位：元（2位小数）
                        "sl" => $v['sl'],// $tem_sl,//"0.03",// "0.13",// 是	string	#.##	税率 例1%为0.01
                        "se" => $v['se'],// $tem_se,// "0.03",// "0.12"// 是	string	#.##	税额 单位：元（2位小数）
                    ];
                    array_push($apiProjectList, $apiProjectInfo);
                }

                // 发票配置沪友
                $invoiceConfigInfo = InvoiceConfigHydzfpDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id]
                    , false, '', []);
                if(empty($invoiceConfigInfo)) throws('【发票配置信息】记录不存在！');

                // $invoicesInfo['jqbh'] = $invoiceConfigInfo['tax_num'] ?? '';// $tem_v['jqbh'] ?? '', // 税控设备机器编号

                // $companyConfig = static::$companyConfig;
                $tem_kce = $invoiceInfo['kce'];
                if(is_numeric($tem_kce) && $tem_kce <= 0) $tem_kce = "";
                $invoiceData = [
                    "data_resources" => "API",// 是	string	4	固定参数 “API”
                    "nsrsbh" => $invoiceSellerInfo['xsf_nsrsbh'],// $companyConfig['tax_num'],// "1246546544654654",// 是	string	20	销售方纳税人识别号
                    "skph" => "",// "123213123212",// 否	string	12	税控盘号（使用税控盒子必填，其他设备为空）
                    "order_num" => $order_num,// "1120521299480004",// "order_num_1474873042826",// 是	string	200	业务单据号；必须是唯一的
                    "bmb_bbh" => "33.0", // "29.0",// 是	string	10	税收编码版本号，参数“29.0”，具体值请询问提供商-- ?
                    "zsfs" => $invoiceTemplateInfo['zsfs'],// "0",// 是	string	2	征税方式 0：普通征税 1: 减按计增 2：差额征税
                    "tspz" => $invoiceTemplateInfo['tspz'],// "00",// 否	string	2	特殊票种标识:“00”=正常票种,“01”=农产品销售,“02”=农产品收购
                    "xsf_nsrsbh" => $invoiceSellerInfo['xsf_nsrsbh'],// $companyConfig['tax_num'],// "1246546544654654",//是	string	20	销售方纳税人识别号
                    "xsf_mc" => $invoiceSellerInfo['xsf_mc'],// $companyConfig['pay_company_name'],// "\t自贡市有限公司",// 是	string	100	销售方名称
                    "xsf_dzdh" => $invoiceSellerInfo['xsf_dz'] . $invoiceSellerInfo['xsf_dh'],// "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端 13132254",// 是	string	100	销售方地址、电话
                    "xsf_yhzh" =>  $invoiceSellerInfo['xsf_yh'] . $invoiceSellerInfo['xsf_yhzh'],// "124654654123154",// 是	string	100	销售方开户行名称与银行账号
                    "gmf_nsrsbh" => $invoiceBuyerInfo['gmf_nsrsbh'],//  "",// 否	string	100	购买方纳税人识别号(税务总局规定企业用户为必填项)
                    "gmf_mc" => $invoiceBuyerInfo['gmf_mc'],//  "个人",// 是	string	100	购买方名称
                    "gmf_dzdh" => $invoiceBuyerInfo['gmf_dz'] . $invoiceBuyerInfo['gmf_dh'],//  "",// 否	string	100	购买方地址、电话
                    "gmf_yhzh" =>  $invoiceBuyerInfo['gmf_yh'] . $invoiceBuyerInfo['gmf_yhzh'],//  "",// 否	string	100	购买方开户行名称与银行账号
                    "kpr" => $invoiceTemplateInfo['kpr'],// "开票员A",// 是	string	8	开票人
                    "skr" =>  $invoiceTemplateInfo['skr'],// "",// 否	string	8	收款人
                    "fhr" =>  $invoiceTemplateInfo['fhr'],// "",// 否	string	8	复核人
                    "yfp_dm" =>  "",// 否	string	12	原发票代码
                    "yfp_hm" =>  "",// 否	string	8	原发票号码
                    // 是	string	#.##	价税合计;单位：元（2位小数） 价税合计=合计金额(不含税)+合计税额
                    // 注意：不能使用商品的单价、数量、税率、税额来进行累加，最后四舍五入，只能是总合计金额+合计税额
                    "jshj" => $invoiceInfo['jshj'],// $total_jshj,//  "1.00",
                    "hjje" => $invoiceInfo['hjje'],// $total_hjje,// "0.97",// "0.88",// 是	string	#.##	合计金额 注意：不含税，单位：元（2位小数）
                    "hjse" => $invoiceInfo['hjse'],// $total_hjse,// "0.03",// "0.12",// 是	string	#.##	合计税额单位：元（2位小数）
                    "kce" =>  $tem_kce,// "",// 否	string	#.##	扣除额小数点后2位，当ZSFS为2时扣除额为必填项
                    "bz" => replace_enter_char($invoiceTemplateInfo['bz'],2) ,// "备注啊哈哈哈哈",// 否	string	100	备注 (长度100字符)
                    // "kpzdbs" => "",// 否	string	20	已经失效，不再支持
                    "jff_phone" => $invoiceBuyerInfo['jff_phone'],//  "",// "手机号",// 否	string	11	手机号，针对税控盒子主动交付，需要填写
                    "jff_email" => $invoiceBuyerInfo['jff_email'],//  "",// "电子邮件",// 否	string	100	电子邮件，针对税控盒子主动交付，需要填写
                    "common_fpkj_xmxx" => $apiProjectList,
//             [
//                [
//                    "fphxz" => "0",// 是	string	2	发票行性质 0正常行、1折扣行、2被折扣行
//                    "spbm" => "3070201020000000000",// "",// 是	string	19	商品编码(商品编码为税务总局颁发的19位税控编码)
//                    "zxbm" => "",// 否	string	20	自行编码(一般不建议使用自行编码)
//                    "yhzcbs" => "0",// "",//否	string	2	优惠政策标识 0：不使用，1：使用
//                    "lslbs" => "",// 否	string	2	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
//                    // 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填，
//                    // 具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
//                    "zzstsgl" => "",// aa  bbb
//                    // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
//                    // 如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
//                    "xmmc" => "培训费",// "更具自身业务决定",// aa  bbb
//                    "ggxh" => "",// 否	string	30	规格型号(折扣行请不要传)
//                    "dw" => "",// 否	string	20	计量单位(折扣行请不要传)
//                    "xmsl" => "1",// "",// 否	string	#.######	项目数量 小数点后6位,大于0的数字
//                    "xmdj" => "1.00",// 否	string	#.######	项目单价 小数点后6位 注意：单价是含税单价,大于0的数字
//                    "xmje" => "1.00",// 是	string	#.##	项目金额 注意：金额是含税，单位：元（2位小数）
//                    "sl" => "0.03",// "0.13",// 是	string	#.##	税率 例1%为0.01
//                    "se" => "0.03",// "0.12"// 是	string	#.##	税额 单位：元（2位小数）
//                ]
//            ]
                ];
                // A0001-开具蓝字发票
                $resultAPI = InvoiceHydzfp::ebiInvoiceHandleNewBlueInvoice($invoiceData, $invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], 0,  false);

                // C0005-获取发票PDF下载地址---保存交付的pdf文件
                $resource_id = static::getInvoiceFile($company_id, $organize_id, $order_num, $invoiceSellerInfo['xsf_nsrsbh'], $invoiceConfigInfo, $operate_staff_id, $modifAddOprate);

                // C0002-获取平台交付二维码
                $APIQRCodeResult = InvoiceHydzfp::ebiInvoiceHandleGetInvoiceDelayQRCode($invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], $order_num, $invoiceSellerInfo['xsf_nsrsbh'], '', 0,  false);
                $qrcodeurl = $APIQRCodeResult['qrcodeurl'];

                // 有手机号或有邮箱
                if(!empty($invoiceBuyerInfo['jff_phone']) || !empty($invoiceBuyerInfo['jff_email'])){
                    // C0001-平台在线交付---会发电子邮件
                    InvoiceHydzfp::ebiInvoiceHandleNewInvoiceDelay($invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], $order_num, $invoiceSellerInfo['xsf_nsrsbh'], $invoiceBuyerInfo['jff_phone'], $invoiceBuyerInfo['jff_email'],0,  false);

                }
                $currentNow = Carbon::now();
                CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$order_num, &$operate_staff_id, &$modifAddOprate, &$resultAPI, &$nowTime
                    , &$invoice_service, &$order_nos, &$orderList, &$kplx, &$invoiceInfo, &$currentNow, &$resource_id, &$invoice_id, &$APIQRCodeResult, &$qrcodeurl){

                    // 图片资源
                    // $resource_id = CommonRequest::get($request, 'resource_id');
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

                    foreach($resultAPI as $tem_v){
                        $kce = $tem_v['kce'] ?? '' ;
                        if(!is_numeric($kce)) $kce = 0;
                        $updateInvoiceInfo = [
                            'invoice_status' => 4 , // 开票状态1待开票2开票中4已开票
                            'upload_status' => 4 , // 开票数据状态1待上传2已上传4已开票8已作废16已冲红
                            'kpzdbs' => $tem_v['kpzdbs'] ?? '', // 开票终端标识 已经失效，不再支持
                            'jqbh' => $tem_v['jqbh'] ?? '', // 税控设备机器编号
                            'itype' => $tem_v['itype'] ?? '' , // 发票类型(026=电票,004=专票,007=普票，025=卷票)
                            'kce' => $kce,// $tem_v['kce'] ?? '' , // 扣除额小数点后2位，当ZSFS为2【差额征税】时扣除额为必填项
                            'yfp_hm' => $tem_v['yfp_hm'] ?? '', // 原发票号码
                            'fp_hm' => $tem_v['fp_hm'] ?? '' , // 发票号码
                            'yfp_dm' => $tem_v['yfp_dm'] ?? '' , // 原发票代码
                            'fp_dm' => $tem_v['fp_dm'] ?? '' , // 发票代码
                            'hjse' => $tem_v['hjse'] ?? '0' , // 合计税额【税总额】
                            'hjje' => $tem_v['hjje'] ?? '0' , // 合计金额(不含税)
                            'jshj' => $tem_v['jshj'] ?? '0' , // 价税合计(含税)
                            'jff_phone' => $tem_v['jff_phone'] ?? '' , // 手机号，针对税控盒子主动交付，需要填写
                            'jff_email' => $tem_v['jff_email'] ?? '' , // 电子邮件，针对税控盒子主动交付，需要填写
                            'jym' => $tem_v['jym'] ?? '' , // 校验码
                            'pdf_item_key' => $tem_v['pdf_item_key'] ?? '' , // 发票清单PDF文件获取key
                            'pdf_key' => $tem_v['pdf_key'] ?? '' , // 发票PDF文件获取key
                            'ext_code' => $tem_v['ext_code'] ?? '' , // 提取码
                            'fpqqlsh' => $tem_v['fpqqlsh'] ?? '' , // 发票请求流水号
                            'fp_mw' => $tem_v['fp_mw'] ?? '' , // 发票密文
                            'kprq' => $tem_v['kprq'] ?? '' , // 开票日期(20161107145525格式：yyyymmddhhmiss)
                            // 'create_time' => $nowTime , // 生成时间
                            'submit_time' => $nowTime , // 提交数据时间
                            'make_time' => $nowTime , // 开票时间
                            // 'closel_time' => $nowTime , // 作废时间
                            // 'cancel_time' => $nowTime , // 冲红时间
                            'qrcodeurl' => $qrcodeurl,// 二维码交付发票的链接地址
                            'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
                            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
                            'resourceIds' => $resource_id,// 此下标为图片资源关系
                        ];
                        // $invoiceInfo = array_merge($invoiceInfo, $updateInvoiceInfo);
//                        foreach($updateInvoiceInfo as $k => $v){
//                            $invoiceInfo[$k] = $v;
//                        }
                        Tool::arrAppendKeys($invoiceInfo, $updateInvoiceInfo);
                        // 价格转为整型
                        Tool::bathPriceCutFloatInt($updateInvoiceInfo, Invoices::$IntPriceFields, Invoices::$IntPriceIndex, 1);
                        static::replaceById($updateInvoiceInfo, $company_id, $invoice_id, $operate_staff_id, $modifAddOprate);
                        // $saveQueryParams = Tool::getParamQuery(['order_num' => $order_num],[], []);
                        // static::save($updateInvoiceInfo, $saveQueryParams);
//                        if($invoice_service == 2){
//                            $updateInvoiceInfo['']
//                        }
//                        $apiGoodsList = $tem_v['common_fpkj_xmxx'] ?? [];
//                        foreach($apiGoodsList as $temGoodInfo){
//
//                        }
                    }
                    // 修改订单信息
                    foreach($orderList as $orderInfo){
                        $invoice_result = $orderInfo['invoice_result'];
                        if($kplx == 0) {// 蓝票
                            $invoice_result |= 2;
                        }else{
                            $invoice_result |= 4;
                        }
                        $updateOrder = [
                            'invoice_result' => $invoice_result,// 开票结果1待开票1已开蓝票2已冲红
                            // 'invoice_status' => $invoiceOrderInfo['invoice_status'],// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                            // 'upload_status' => $invoiceOrderInfo['upload_status'],// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                            // 'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                            // 'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                            // 'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                            // 'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                        ];
                        if($kplx == 0){// 蓝票
                            $updateOrder = array_merge($updateOrder,[
                                'invoice_status' => 4,// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                                'upload_status' => 4,// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                                'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                                // 'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                                'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                                // 'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                            ]);
                        }else{// 红票
                            $updateOrder = array_merge($updateOrder,[
                                'invoice_status' => 1,// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                                'upload_status' => 1,// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                                // 'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                                'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                                // 'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                                'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                            ]);

                        }
                        $saveQueryParams = Tool::getParamQuery(['order_no' => $orderInfo['order_no']],[], []);
                        OrdersDBBusiness::save($updateOrder, $saveQueryParams);
                    }
                    // 保存订单流水
                    $invoiceOrderFlow = [
                        'company_id' => $invoiceInfo['company_id'],// 企业id/学员id(无所属企业)
                        // 'order_type' => $invoiceInfo['aaaaa'],// 订单类型1面授培训2会员年费
                        'pay_config_id' => $invoiceInfo['pay_config_id'],// 收款帐号配置id
                        'order_num' => $invoiceInfo['order_num'],// 发票业务单据号
                        'kplx' => $invoiceInfo['kplx'],// 开票类型 0-蓝字发票；1-红字发票
                        'itype' => $invoiceInfo['itype'],// 发票类型(026=电票,004=专票,007=普票，025=卷票)
                        'hjse' => $invoiceInfo['hjse'],// 合计税额【税总额】
                        'hjje' => $invoiceInfo['hjje'],// 合计金额(不含税)
                        'jshj' => $invoiceInfo['jshj'],// 价税合计(含税)
                        'count_date' => $currentNow->toDateString(),// 日期
                        'count_year' => $currentNow->year,// 年
                        'count_month' => $currentNow->month,// 月
                        'count_day' => $currentNow->day,// 日
                    ];
                    // 价格转为整型
                    Tool::bathPriceCutFloatInt($invoiceOrderFlow, InvoiceOrderFlow::$IntPriceFields, InvoiceOrderFlow::$IntPriceIndex, 1);
                    $invoiceOrderFlowId = 0;
                    InvoiceOrderFlowDBBusiness::replaceById($invoiceOrderFlow, $company_id, $invoiceOrderFlowId, $operate_staff_id, $modifAddOprate);


                });
                break;
            case 2:
                break;
            default:
                break;
        }
    }


    /**
     * 请求接口生成电子发票-- 生成红票
     *
     * @param int  $company_id 企业id 或用户id--无所属企业
     * @param int $organize_id 操作的所属企业id
     * @param array $invoiceBlueInfo 原发票数组 一维
     * @param string $order_num 订单号数组 二维
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return string 业务单号
     *
     * @author zouyan(305463219@qq.com)
     */
    public static function apiSendRedInvoice($company_id, $organize_id = 0, $invoiceBlueInfo = [], $order_num = '', $operate_staff_id = 0, $modifAddOprate = 0){
        if(empty($invoiceBlueInfo)) throws('原发票记录不存在！');
        $invoiceInfo = static::getDBFVFormatList(4, 1, ['order_num' => $order_num]
            , false, '', []);
        if(empty($invoiceInfo)) throws('发票记录不存在！');
        if(!in_array($invoiceInfo['invoice_status'], [1]) ) throws('开票状态非待开票！');
        if(!in_array($invoiceInfo['upload_status'], [1]) ) throws('开票数据状态非待上传！');
        //整数转为小数
        Tool::bathPriceCutFloatInt($invoiceInfo, Invoices::$IntPriceFields, Invoices::$IntPriceIndex, 2, 2);
        $invoice_service = $invoiceInfo['invoice_service'];
        $kplx = $invoiceInfo['kplx'];// 开票类型 0-蓝字发票；1-红字发票
        $invoice_id = $invoiceInfo['id'];
        $pay_config_id = $invoiceInfo['pay_config_id'];
        $invoice_seller_id_history = $invoiceInfo['invoice_seller_id_history'];
        $invoice_buyer_id_history = $invoiceInfo['invoice_buyer_id_history'];
        $invoice_template_id_history = $invoiceInfo['invoice_template_id_history'];

        // 获得发票项目
        $invoiceProjectList = InvoiceProjectDBBusiness::getDBFVFormatList(1, 1, ['order_num' => $order_num, 'invoice_id' => $invoice_id], false);
        if(empty($invoiceProjectList)) throws('发票项目记录不存在！');
        //整数转为小数
        Tool::bathPriceCutFloatInt($invoiceProjectList, InvoiceProject::$IntPriceFields, InvoiceProject::$IntPriceIndex, 2, 2);

        // 获得订单发票关联表
        $invoiceOrderList = InvoiceOrderDBBusiness::getDBFVFormatList(1, 1, ['order_num' => $order_num], false);
        if(empty($invoiceOrderList)) throws('订单发票关联记录不存在！');
        $order_nos = Tool::getArrFields($invoiceOrderList, 'order_no');

        // 获得订单信息
        $orderList = OrdersDBBusiness::getDBFVFormatList(1, 1, ['order_no' => $order_nos], false);
        if(empty($orderList)) throws('订单记录不存在！');


        // 获得销售方信息
        $invoiceSellerInfo = InvoiceSellerHistoryDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_seller_id_history]
            , false, '', []);
        if(empty($invoiceSellerInfo)) throws('【销售方开票信息】记录不存在！');
        // if(!in_array($invoiceSellerInfo['open_status'], [1])) throws('【销售方开票信息】非开启状态！');
//        $invoice_seller_id = $invoiceSellerInfo['invoice_seller_id'];

        // 获得购买方信息
        $invoiceBuyerInfo = InvoiceBuyerHistoryDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_buyer_id_history]
            , false, '', []);
        if(empty($invoiceBuyerInfo)) throws('【购买方开票信息】记录不存在！');
        // if($organize_id != $invoiceBuyerInfo['company_id'])  throws('您没有【购买方开票信息】操作此记录的权限！');
        // if(!in_array($invoiceBuyerInfo['open_status'], [1])) throws('【购买方开票信息】非开启状态！');

        $invoiceTemplateInfo = InvoiceTemplateHistoryDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_template_id_history]
            , false, '', []);
        if(empty($invoiceTemplateInfo)) throws('【发票开票模板】记录不存在！');
//        // if(!in_array($invoiceTemplateInfo['open_status'], [1])) throws('【发票开票模板】非开启状态！');

        $apiProjectList = [];

        $nowTime = date('Y-m-d H:i:s');
        switch($invoice_service){
            case 1:// 1沪友
                // 发票配置沪友
                $invoiceConfigInfo = InvoiceConfigHydzfpDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id]
                    , false, '', []);
                if(empty($invoiceConfigInfo)) throws('【发票配置信息】记录不存在！');

                // $invoicesInfo['jqbh'] = $invoiceConfigInfo['tax_num'] ?? '';// $tem_v['jqbh'] ?? '', // 税控设备机器编号

                // $companyConfig = static::$companyConfig;
                $tem_kce = $invoiceInfo['kce'];
                if(is_numeric($tem_kce) && $tem_kce <= 0) $tem_kce = "";

                // A0002-开具红字发票
                $dataApi = [
                    "data_resources" => "API",// 是	string	4	固定值 API
                    "nsrsbh" => $invoiceSellerInfo['xsf_nsrsbh'],// $companyConfig['tax_num'],//"123123123",// 是	string	20	销货单位纳税人识别号
                    "skph" => "",// "123213123212",// 否	string	12	税控盘号（使用税控盒子必填，其他设备为空）
                    "order_num" => $order_num,// "1120521299480002",// "order_num_147486801",// 是	string	200	业务单据号；必须是唯一的
                    "yfp_dm" => $invoiceInfo['yfp_dm'],// "050003521270",// "150003529999",// 是	string	12	发票代码
                    "yfp_hm" => $invoiceInfo['yfp_hm'],// "69023540",// "65942490",// 是	string	8	发票号码
                    "bz" =>  replace_enter_char($invoiceTemplateInfo['bz'],2) ,// "行行行存储",// 否	string	100	发票备注
                    "kpr" => $invoiceTemplateInfo['kpr'],// "开票人1",// 否	string	8	开票人
                    "skr" => $invoiceTemplateInfo['skr'],// "收款人2",// 否	string	8	收款人
                    "fhr" => $invoiceTemplateInfo['fhr'],// "复核人3",// 否	string	8	复核人
                    "kpzdbs" => ""// 否	string	20	已经失效，不再支持
                ];
                $resultAPI = InvoiceHydzfp::ebiInvoiceHandleNewRedInvoice($dataApi, $invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], 0,  false);

                // C0005-获取发票PDF下载地址---保存交付的pdf文件
                $resource_id = static::getInvoiceFile($company_id, $organize_id, $order_num, $invoiceSellerInfo['xsf_nsrsbh'], $invoiceConfigInfo, $operate_staff_id, $modifAddOprate);

                // C0002-获取平台交付二维码
                 $APIQRCodeResult = InvoiceHydzfp::ebiInvoiceHandleGetInvoiceDelayQRCode($invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], $order_num, $invoiceSellerInfo['xsf_nsrsbh'], '', 0,  false);
                 $qrcodeurl = $APIQRCodeResult['qrcodeurl'];

                 // 有手机号或有邮箱
//                if(!empty($invoiceBuyerInfo['jff_phone']) || !empty($invoiceBuyerInfo['jff_email'])){
//                    // C0001-平台在线交付---会发电子邮件
//                    InvoiceHydzfp::ebiInvoiceHandleNewInvoiceDelay($invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], $order_num, $invoiceSellerInfo['xsf_nsrsbh'], $invoiceBuyerInfo['jff_phone'], $invoiceBuyerInfo['jff_email'],0,  false);
//
//                }
                 $currentNow = Carbon::now();
                CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$invoiceBlueInfo, &$order_num, &$operate_staff_id, &$modifAddOprate, &$resultAPI, &$nowTime
                    , &$invoice_service, &$order_nos, &$orderList, &$kplx, &$invoiceInfo, &$currentNow, &$resource_id, &$invoice_id, &$APIQRCodeResult, &$qrcodeurl){

                    // 图片资源
                    // $resource_id = CommonRequest::get($request, 'resource_id');
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
                    // 修改原蓝票信息
                    if($kplx != 0) {// 红票
                        $saveQueryParams = Tool::getParamQuery(['order_num' => $invoiceBlueInfo['order_num']],[], []);
                        InvoicesDBBusiness::save([
                            'upload_status' => 16,// 开票数据状态1待上传2已上传4已开票8已作废16已红冲
                        ], $saveQueryParams);
                    }

                    foreach($resultAPI as $tem_v){
                        $kce = $tem_v['kce'] ?? '' ;
                        if(!is_numeric($kce)) $kce = 0;
                        $updateInvoiceInfo = [
                            'invoice_status' => 4 , // 开票状态1待开票2开票中4已开票
                            'upload_status' => 4 , // 开票数据状态1待上传2已上传4已开票8已作废16已冲红
                            'kpzdbs' => $tem_v['kpzdbs'] ?? '', // 开票终端标识 已经失效，不再支持
                            'jqbh' => $tem_v['jqbh'] ?? '', // 税控设备机器编号
                            'itype' => $tem_v['itype'] ?? '' , // 发票类型(026=电票,004=专票,007=普票，025=卷票)
                            'kce' => $kce,// $tem_v['kce'] ?? '' , // 扣除额小数点后2位，当ZSFS为2【差额征税】时扣除额为必填项
                            'yfp_hm' => $tem_v['yfp_hm'] ?? '', // 原发票号码
                            'fp_hm' => $tem_v['fp_hm'] ?? '' , // 发票号码
                            'yfp_dm' => $tem_v['yfp_dm'] ?? '' , // 原发票代码
                            'fp_dm' => $tem_v['fp_dm'] ?? '' , // 发票代码
                            'hjse' => $tem_v['hjse'] ?? '0' , // 合计税额【税总额】
                            'hjje' => $tem_v['hjje'] ?? '0' , // 合计金额(不含税)
                            'jshj' => $tem_v['jshj'] ?? '0' , // 价税合计(含税)
                            'jff_phone' => $tem_v['jff_phone'] ?? '' , // 手机号，针对税控盒子主动交付，需要填写
                            'jff_email' => $tem_v['jff_email'] ?? '' , // 电子邮件，针对税控盒子主动交付，需要填写
                            'jym' => $tem_v['jym'] ?? '' , // 校验码
                            'pdf_item_key' => $tem_v['pdf_item_key'] ?? '' , // 发票清单PDF文件获取key
                            'pdf_key' => $tem_v['pdf_key'] ?? '' , // 发票PDF文件获取key
                            'ext_code' => $tem_v['ext_code'] ?? '' , // 提取码
                            'fpqqlsh' => $tem_v['fpqqlsh'] ?? '' , // 发票请求流水号
                            'fp_mw' => $tem_v['fp_mw'] ?? '' , // 发票密文
                            'kprq' => $tem_v['kprq'] ?? '' , // 开票日期(20161107145525格式：yyyymmddhhmiss)
                            // 'create_time' => $nowTime , // 生成时间
                            'submit_time' => $nowTime , // 提交数据时间
                            'make_time' => $nowTime , // 开票时间
                            // 'closel_time' => $nowTime , // 作废时间
                             'cancel_time' => $nowTime , // 冲红时间
                            'qrcodeurl' => $qrcodeurl,// 二维码交付发票的链接地址
                            'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
                            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
                            'resourceIds' => $resource_id,// 此下标为图片资源关系
                        ];
                        // $invoiceInfo = array_merge($invoiceInfo, $updateInvoiceInfo);
//                        foreach($updateInvoiceInfo as $k => $v){
//                            $invoiceInfo[$k] = $v;
//                        }
                        Tool::arrAppendKeys($invoiceInfo, $updateInvoiceInfo);
                        // 价格转为整型
                        Tool::bathPriceCutFloatInt($updateInvoiceInfo, Invoices::$IntPriceFields, Invoices::$IntPriceIndex, 1);
                        static::replaceById($updateInvoiceInfo, $company_id, $invoice_id, $operate_staff_id, $modifAddOprate);
                        // $saveQueryParams = Tool::getParamQuery(['order_num' => $order_num],[], []);
                        // static::save($updateInvoiceInfo, $saveQueryParams);
//                        if($invoice_service == 2){
//                            $updateInvoiceInfo['']
//                        }
//                        $apiGoodsList = $tem_v['common_fpkj_xmxx'] ?? [];
//                        foreach($apiGoodsList as $temGoodInfo){
//
//                        }
                    }
                    // 修改订单信息
                    foreach($orderList as $orderInfo){
                        $invoice_result = $orderInfo['invoice_result'];
                        if($kplx == 0) {// 蓝票
                            $invoice_result |= 2;
                        }else{
                            $invoice_result |= 4;
                        }
                        $updateOrder = [
                            'invoice_result' => $invoice_result,// 开票结果1待开票1已开蓝票2已冲红
                            // 'invoice_status' => $invoiceOrderInfo['invoice_status'],// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                            // 'upload_status' => $invoiceOrderInfo['upload_status'],// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                            // 'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                            // 'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                            // 'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                            // 'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                        ];
                        if($kplx == 0){// 蓝票
                            $updateOrder = array_merge($updateOrder,[
                                'invoice_status' => 4,// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                                'upload_status' => 4,// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                                'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                                // 'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                                'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                                // 'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                            ]);
                        }else{// 红票
                            $updateOrder = array_merge($updateOrder,[
                                'invoice_status' => 1,// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
                                'upload_status' => 1,// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
                                // 'invoic_blue_num' => $order_num,// 业务单据号【[蓝票--最近一次】
                                'invoic_red_num' => $order_num,// 业务单据号【[红票--最近一次】
                                // 'invoice_blue_time' => $nowTime,// 蓝票时间【最近一次】
                                'invoice_red_time' => $nowTime,// 充红时间【最近一次】
                            ]);

                        }
                        $saveQueryParams = Tool::getParamQuery(['order_no' => $orderInfo['order_no']],[], []);
                        OrdersDBBusiness::save($updateOrder, $saveQueryParams);

                    }

                    // 保存订单流水
                    $invoiceOrderFlow = [
                        'company_id' => $invoiceInfo['company_id'],// 企业id/学员id(无所属企业)
                        // 'order_type' => $invoiceInfo['aaaaa'],// 订单类型1面授培训2会员年费
                        'pay_config_id' => $invoiceInfo['pay_config_id'],// 收款帐号配置id
                        'order_num' => $invoiceInfo['order_num'],// 发票业务单据号
                        'kplx' => $invoiceInfo['kplx'],// 开票类型 0-蓝字发票；1-红字发票
                        'itype' => $invoiceInfo['itype'],// 发票类型(026=电票,004=专票,007=普票，025=卷票)
                        'hjse' => $invoiceInfo['hjse'],// 合计税额【税总额】
                        'hjje' => $invoiceInfo['hjje'],// 合计金额(不含税)
                        'jshj' => $invoiceInfo['jshj'],// 价税合计(含税)
                        'count_date' => $currentNow->toDateString(),// 日期
                        'count_year' => $currentNow->year,// 年
                        'count_month' => $currentNow->month,// 月
                        'count_day' => $currentNow->day,// 日
                    ];
                    // 价格转为整型
                    Tool::bathPriceCutFloatInt($invoiceOrderFlow, InvoiceOrderFlow::$IntPriceFields, InvoiceOrderFlow::$IntPriceIndex, 1);
                    $invoiceOrderFlowId = 0;
                    InvoiceOrderFlowDBBusiness::replaceById($invoiceOrderFlow, $company_id, $invoiceOrderFlowId, $operate_staff_id, $modifAddOprate);
                });
                break;
            case 2:
                break;
            default:
                break;
        }
    }

    /**
     * 发票交付--C0005-获取发票PDF下载地址 ;保存到本地，并更新发票表相关数据
     *
     * @param int  $company_id 企业id 或用户id--无所属企业
     * @param int $organize_id 操作的所属企业id
     * @param string $order_num 订单号
     * @param string $nsrsbh 开票商户纳税人识别号
     * @param array $invoiceConfigInfo 发票配置沪友 --一维数组
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return int $resource_id 资源文件id
     *
     * @author zouyan(305463219@qq.com)
     */
    public static function getInvoiceFile($company_id, $organize_id = 0, $order_num = '', $nsrsbh = '', $invoiceConfigInfo = [], $operate_staff_id = 0, $modifAddOprate = 0){

        // C0005-获取发票PDF下载地址
        $invoiceQueryParams = [
            "nsrsbh" => $nsrsbh, //$invoiceSellerInfo['xsf_nsrsbh'],// $companyConfig['tax_num'],//"开票商户纳税人识别号",// 是	string	20	开票商户纳税人识别号
            "order_num" => $order_num,// "1120521299480004",//"业务单据号"// 是	string	200	业务单据号
        ];
        $apiQueryResult = InvoiceHydzfp::ebiInvoiceHandleGetInvoiceDownloadUrl($invoiceQueryParams, $invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], 0,  false);
        // pr($apiQueryResult);
        $fp_url = $apiQueryResult['fp_url'];
        // 保存pdf文件
        $fileArr = DownFile::getUrlFileToLocal($fp_url, $organize_id,2, 'invoices', $order_num . '.pdf' );
        $files_names = $fileArr['files_names'];// /resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
        $full_names = $fileArr['full_names'];// /srv/www/quality_control/quality_control/admin/public/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
        $files_name_txt = $fileArr['saveName'];// 20191003121326d710d554edce12a1.png
        $suffix = DownFile::getLocalFileExt($full_names);
        // 保存图片资源--到数据库
        // 根据扩展名，重新获得文件的操作类型
        $resourceConfig = UploadFile::getResourceConfig($suffix);
        // if(empty($resourceConfig)) throws('请选择正确的文件！');
        $resourceType = $resourceConfig['resource_type'] ?? 0;
        $file_size = filesize($full_names);
        $mime_type = DownFile::getLocalFileMIME($full_names);

        $saveData =[
            'resource_name' => $files_name_txt,
            'resource_type' => $resourceType,
            'resource_url' => $files_names,
            'resource_size' => $file_size,
            'resource_size_format' => Tool::formatBytesSize($file_size, 2),
            'resource_ext' => $suffix,
            'resource_mime_type' => $mime_type,
            'ower_type' => 2,// $admin_type
            'ower_id' => $organize_id,// $company_id,
            'resource_note' => '',
        ];

        $resource_id = 0;
        ResourceDBBusiness::replaceById($saveData, $company_id, $resource_id, $operate_staff_id, $modifAddOprate);
        return $resource_id;

    }
}
