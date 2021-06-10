<?php
// 发票主表
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\Invoices;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIInvoicesBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\InvoicesAPI';
    public static $table_name = 'invoices';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

    /**
     * 特殊的验证 关键字 -单个 的具体验证----具体的子类----重写此方法来实现具体的验证
     *
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param string $key 验证规则的关键字 -单个
     * @param array $tableLangConfig 多语言单个数据表配置数组--也就是表多语言的那个配置数组
     * @param array $extParams 其它扩展参数，
     * @return  array 错误：非空数组；正确：空数组
     * @author zouyan(305463219@qq.com)
     */
    public static function singleJudgeDataByKey(&$mustFields = [], &$judgeData = [], $key = '', $tableLangConfig = [], $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        $errMsgs = [];// 错误信息的数组--一维数组，可以指定下标
        // if( (is_string($key) && strlen($key) <= 0 ) || (is_array($key))) return $errMsgs;
        switch($key){
            case 'add':// 添加；

                break;
            case 'modify':// 修改
                break;
            case 'replace':// 新加或修改

                $id = $extParams['id'] ?? 0;
                if($id > 0){

                }

                break;
            default:
                break;
        }
        return $errMsgs;
    }

    // ****表关系***需要重写的方法**********开始***********************************
    /**
     * 获得处理关系表数据的配置信息--重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $relationKeys
     * @param array $extendParams  扩展参数---可能会用
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigs(Request $request, Controller $controller, $relationKeys = [], $extendParams = []){
        if(empty($relationKeys)) return [];
        list($relationKeys, $relationArr) = static::getRelationParams($relationKeys);// 重新修正关系参数
        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;
        // 关系配置
        $relationFormatConfigs = [
            // 下标 'relationConfig' => []// 下一个关系
            // 获得企业名称
//            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
//                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
//                , 1, 2
//                ,'','',
//                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
//                    static::getUboundRelation($relationArr, 'company_info'),
//                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
//                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info'), '', []),
            // 获得企业名称
            'company_name' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_name'),
                    static::getUboundRelationExtendParams($extendParams, 'company_name')),
                static::getRelationSqlParams([], $extendParams, 'company_name'), '', []),
            // 获得企业名称
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 16
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams([], $extendParams, 'company_info'), '', []),// ['where' => [['admin_type', 2]]]
            // 发票配置购买方
            'invoice_buyer' => CTAPIInvoiceBuyerBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_buyer_id' => 'id']
                , 1, 1
                ,'','',
                CTAPIInvoiceBuyerBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_buyer'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_buyer')),
                static::getRelationSqlParams([], $extendParams, 'invoice_buyer'), '', []),
            // 发票配置购买方历史
            'invoice_buyer_history' => CTAPIInvoiceBuyerHistoryBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_buyer_id_history' => 'id']
                , 1, 1
                ,'','',
                CTAPIInvoiceBuyerHistoryBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_buyer_history'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_buyer_history')),
                static::getRelationSqlParams([], $extendParams, 'invoice_buyer_history'), '', []),
            // 发票配置销售方
            'invoice_seller' => CTAPIInvoiceSellerBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_seller_id' => 'id']
                , 1, 1
                ,'','',
                CTAPIInvoiceSellerBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_seller'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_seller')),
                static::getRelationSqlParams([], $extendParams, 'invoice_seller'), '', []),
            // 发票配置销售方历史
            'invoice_seller_history' => CTAPIInvoiceSellerHistoryBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_seller_id_history' => 'id']
                , 1, 1
                ,'','',
                CTAPIInvoiceSellerHistoryBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_seller_history'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_seller_history')),
                static::getRelationSqlParams([], $extendParams, 'invoice_seller_history'), '', []),
            // 发票开票模板
            'invoice_template' => CTAPIInvoiceTemplateBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_template_id' => 'id']
                , 1, 1
                ,'','',
                CTAPIInvoiceTemplateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_template'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_template')),
                static::getRelationSqlParams([], $extendParams, 'invoice_template'), '', []),
            // 发票开票模板历史
            'invoice_template_history' => CTAPIInvoiceTemplateHistoryBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_template_id_history' => 'id']
                , 1, 1
                ,'','',
                CTAPIInvoiceTemplateHistoryBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_template_history'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_template_history')),
                static::getRelationSqlParams([], $extendParams, 'invoice_template_history'), '', []),
            // 收款帐号配置
            'pay_config' => CTAPIOrderPayConfigBusiness::getTableRelationConfigInfo($request, $controller
                , ['pay_config_id' => 'id']
                , 1, 1
                ,'','',
                CTAPIOrderPayConfigBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'pay_config'),
                    static::getUboundRelationExtendParams($extendParams, 'pay_config')),
                static::getRelationSqlParams([], $extendParams, 'pay_config'), '', []),
            // 发票配置沪友
            'config_hydzfp' => CTAPIInvoiceConfigHydzfpBusiness::getTableRelationConfigInfo($request, $controller
                , ['pay_config_id' => 'pay_config_id']
                , 1, 1
                ,'','',
                CTAPIInvoiceConfigHydzfpBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'config_hydzfp'),
                    static::getUboundRelationExtendParams($extendParams, 'config_hydzfp')),
                static::getRelationSqlParams([], $extendParams, 'config_hydzfp'), '', []),
            // 获得封面图
            'resource_list' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['resource_id' => 'id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list')),
                static::getRelationSqlParams([], $extendParams, 'resource_list'), '', ['extendConfig' => ['listHandleKeyArr' => ['format_resource'], 'infoHandleKeyArr' => ['resource_list']]]),

        ];
        return Tool::formatArrByKeys($relationFormatConfigs, $relationKeys, false);
    }

    /**
     * 获得要返回数据的return_data数据---每个对象，重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $return_num 指定要获得的关系数据类型格式化后的数据 编号 1[占用：原数据] 2 4 8..
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigReturnData(Request $request, Controller $controller, $return_num = 0){
        $return_data = [];// 为空，则会返回对应键=> 对应的数据， 具体的 结构可以参考 Tool::formatConfigRelationInfo  $return_data参数格式

        if(($return_num & 1) == 1) {// 返回源数据--特别的可以参考这个配置
            $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [], 'ubound_keys' => [], 'ubound_type' =>1];
        }

//        if(($return_num & 2) == 2){// 给上一级返回名称 company_name 下标
//            $one_field = ['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'];// 获得名称
//            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
//            array_push($return_data['one_field'], $one_field);
//        }

        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************

    /**
     * 获得列表数据时，查询条件的参数拼接--有特殊的需要自己重写此方法--每个字类都有此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function joinListParams(Request $request, Controller $controller, &$queryParams, $notLog = 0){
        // 自己的参数查询拼接在这里-- 注意：多个id 的查询默认就已经有了，参数是 ids  多个用逗号分隔

        $pay_config_id = CommonRequest::get($request, 'pay_config_id');
        if(strlen($pay_config_id) > 0 && !in_array($pay_config_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $pay_config_id, 'pay_config_id', [0, '0', ''], ',', false);

        $company_id = CommonRequest::get($request, 'company_id');
        if(strlen($company_id) > 0 && !in_array($company_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $company_id, 'company_id', [0, '0', ''], ',', false);

        $invoice_service = CommonRequest::get($request, 'invoice_service');
        if(strlen($invoice_service) > 0 && !in_array($invoice_service, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_service, 'invoice_service', [0, '0', ''], ',', false);

        $invoice_buyer_id = CommonRequest::get($request, 'invoice_buyer_id');
        if(strlen($invoice_buyer_id) > 0 && !in_array($invoice_buyer_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_buyer_id, 'invoice_buyer_id', [0, '0', ''], ',', false);

        $invoice_buyer_id_history = CommonRequest::get($request, 'invoice_buyer_id_history');
        if(strlen($invoice_buyer_id_history) > 0 && !in_array($invoice_buyer_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_buyer_id_history, 'invoice_buyer_id_history', [0, '0', ''], ',', false);

        $invoice_seller_id = CommonRequest::get($request, 'invoice_seller_id');
        if(strlen($invoice_seller_id) > 0 && !in_array($invoice_seller_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $company_id, 'invoice_seller_id', [0, '0', ''], ',', false);

        $invoice_seller_id_history = CommonRequest::get($request, 'invoice_seller_id_history');
        if(strlen($invoice_seller_id_history) > 0 && !in_array($invoice_seller_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_seller_id_history, 'invoice_seller_id_history', [0, '0', ''], ',', false);

//        $order_no = CommonRequest::get($request, 'order_no');
//        if(strlen($order_no) > 0 && !in_array($order_no, [0, '-1']))  Tool::appendParamQuery($queryParams, $order_no, 'order_no', [0, '0', ''], ',', false);

        $invoice_template_id = CommonRequest::get($request, 'invoice_template_id');
        if(strlen($invoice_template_id) > 0 && !in_array($invoice_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id, 'invoice_template_id', [0, '0', ''], ',', false);

        $invoice_template_id_history = CommonRequest::get($request, 'invoice_template_id_history');
        if(strlen($invoice_template_id_history) > 0 && !in_array($invoice_template_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id_history, 'invoice_template_id_history', [0, '0', ''], ',', false);

        $invoice_status = CommonRequest::get($request, 'invoice_status');
        if(strlen($invoice_status) > 0 && !in_array($invoice_status, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_status, 'invoice_status', [0, '0', ''], ',', false);

        $upload_status = CommonRequest::get($request, 'upload_status');
        if(strlen($upload_status) > 0 && !in_array($upload_status, [0, '-1']))  Tool::appendParamQuery($queryParams, $upload_status, 'upload_status', [0, '0', ''], ',', false);

        $order_num = CommonRequest::get($request, 'order_num');
        if(strlen($order_num) > 0 && !in_array($order_num, [0, '-1']))  Tool::appendParamQuery($queryParams, $order_num, 'order_num', [0, '0', ''], ',', false);

        $kplx = CommonRequest::get($request, 'kplx');
        if(strlen($kplx) > 0 && !in_array($kplx, ['-1']))  Tool::appendParamQuery($queryParams, $kplx, 'kplx', [''], ',', false);

        $itype = CommonRequest::get($request, 'itype');
        if(strlen($itype) > 0 && !in_array($itype, [0, '-1']))  Tool::appendParamQuery($queryParams, $itype, 'itype', [0, '0', ''], ',', false);

        $tspz = CommonRequest::get($request, 'tspz');
        if(strlen($tspz) > 0 && !in_array($tspz, [0, '-1']))  Tool::appendParamQuery($queryParams, $tspz, 'tspz', [0, '0', ''], ',', false);

        $zsfs = CommonRequest::get($request, 'zsfs');
        if(strlen($zsfs) > 0 && !in_array($zsfs, ['-1']))  Tool::appendParamQuery($queryParams, $zsfs, 'zsfs', [''], ',', false);

//        $order_type = CommonRequest::get($request, 'order_type');
//        if(strlen($order_type) > 0 && !in_array($order_type, [0, '-1']))  Tool::appendParamQuery($queryParams, $order_type, 'order_type', [0, '0', ''], ',', false);

//        $status_online = CommonRequest::get($request, 'status_online');
//        if(strlen($status_online) > 0 && $status_online != 0)  Tool::appendParamQuery($queryParams, $status_online, 'status_online', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 格式化关系数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $main_list 关系主记录要格式化的数据
     * @param array $data_list 需要格式化的从记录数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function handleRelationDataFormat(Request $request, Controller $controller, &$main_list, &$data_list, $handleKeyArr, &$returnFields = []){
        // if(empty($data_list)) return $returnFields;
        // 重写开始

        // 对外显示时，批量价格字段【整数转为小数】
        if(in_array('priceIntToFloat', $handleKeyArr)){
            Tool::bathPriceCutFloatInt($data_list, Invoices::$IntPriceFields, Invoices::$IntPriceIndex, 2, 2);
        }

        // 重写结束
        return $returnFields;
    }

    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function exportListData(Request $request, Controller $controller, &$data_list, $notLog = 0){
        $formatList = [];
        foreach($data_list as $info){


            array_push($formatList, [
                'fpqqlsh' => $info['fpqqlsh'] ?? '',// 发票请求流水号
                'xsf_mc' => $info['invoice_seller_history']['xsf_mc'] ?? '',// 销售方名称
                'xsf_nsrsbh' => $info['invoice_seller_history']['xsf_nsrsbh'] ?? '',// 销售方纳税人识别号
                'gmf_mc' => $info['invoice_buyer_history']['gmf_mc'] ?? '',// 购买方名称
                'gmf_nsrsbh' => $info['invoice_buyer_history']['gmf_nsrsbh'] ?? '',// 购买方纳税人识别号
                'gmf_dz' => $info['invoice_buyer_history']['gmf_dz'] ?? '',// 购买方地址
                'gmf_dh' => $info['invoice_buyer_history']['gmf_dh'] ?? '',// 购买方电话
                'gmf_yh' => $info['invoice_buyer_history']['gmf_yh'] ?? '',// 购买方银行
                'gmf_yhzh' => $info['invoice_buyer_history']['gmf_yhzh'] ?? '',// 购买方银行账号
                'order_num' => $info['order_num'] ?? '',// 业务单据号
                'invoice_service_text' => $info['invoice_service_text'] ?? '',// 开票服务商
                'invoice_status_text' => $info['invoice_status_text'] ?? '',// 开票状态
                'upload_status_text' => $info['upload_status_text'] ?? '',// 数据状态
                'yfp_hm' => $info['fp_hm'] ?? '',// 原发票号码
                'fp_hm' => $info['fp_hm'] ?? '',// 发票号码
                'yfp_dm' => $info['yfp_dm'] ?? '',// 原发票代码
                'fp_dm' => $info['fp_dm'] ?? '',// 发票代码
                'hjje' => $info['hjje'] ?? '',// 合计金额(不含税)
                'hjse' => $info['hjse'] ?? '',// 合计税额【税总额】
                'jshj' => $info['jshj'] ?? '',// 价税合计(含税)
                'qrcodeurl' => $info['qrcodeurl'] ?? '',// 交付发票的链接地址
                'create_time' => $info['create_time'] ?? '',// 生成时间
                'submit_time' => $info['submit_time'] ?? '',// 提交数据时间
                'make_time' => $info['make_time'] ?? '',// 开票时间
                'cancel_time' => $info['cancel_time'] ?? '',// 冲红时间
            ]);
        }
        $headArr = ['fpqqlsh'=>'发票请求流水号', 'xsf_mc'=>'销售方名称', 'xsf_nsrsbh'=>'销售方纳税人识别号', 'gmf_mc'=>'购买方名称'
            , 'gmf_nsrsbh'=>'购买方纳税人识别号', 'gmf_dz'=>'购买方地址' , 'gmf_dh'=>'购买方电话', 'gmf_yh'=>'购买方银行'
            , 'gmf_yhzh'=>'购买方银行账号', 'order_num'=>'业务单据号', 'invoice_service_text'=>'开票服务商', 'invoice_status_text'=>'开票状态'
            ,  'upload_status_text'=>'数据状态', 'yfp_hm'=>'原发票号码', 'fp_hm'=>'发票号码', 'yfp_dm'=>'原发票代码'
            , 'fp_dm'=>'发票代码',  'hjje'=>'合计金额(不含税)',  'hjse'=>'合计税额【税总额】',  'jshj'=>'价税合计(含税)'
            ,  'qrcodeurl'=>'交付发票的链接地址',  'create_time'=>'生成时间',  'submit_time'=>'提交数据时间',  'make_time'=>'开票时间',  'cancel_time'=>'冲红时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','电子发票' . date('YmdHis'),$formatList,1, $headArr, 0, ['sheet_title' => '电子发票']);
    }

}
