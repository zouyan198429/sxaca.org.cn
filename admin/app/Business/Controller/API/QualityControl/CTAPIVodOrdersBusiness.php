<?php
// 点播课程订单
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\VodOrders;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIVodOrdersBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\VodOrdersAPI';
    public static $table_name = 'vod_orders';// 表名称
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
            // 获得课程名称
            'vod_name' => CTAPIVodsBusiness::getTableRelationConfigInfo($request, $controller
                , ['vod_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIVodsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'vod_name'),
                    static::getUboundRelationExtendParams($extendParams, 'vod_name')),
                static::getRelationSqlParams([], $extendParams, 'vod_name'), '', []),

            // 获得企业名称
            'company_name' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'company_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_name'),
                    static::getUboundRelationExtendParams($extendParams, 'company_name')),
                static::getRelationSqlParams([], $extendParams, 'company_name'), '', []),// 'where' => [['admin_type', 2]]

            // 获得发票开票模板名称
            'invoice_template_name' => CTAPIInvoiceTemplateBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_template_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIInvoiceTemplateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_template_name'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_template_name')),
                static::getRelationSqlParams([], $extendParams, 'invoice_template_name'), '', []),// 'where' => [['admin_type', 2]]
            // 获得发票商品项目模板名称
            'invoice_project_template_name' => CTAPIInvoiceProjectTemplateBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_project_template_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIInvoiceProjectTemplateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_project_template_name'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_project_template_name')),
                static::getRelationSqlParams([], $extendParams, 'invoice_project_template_name'), '', []),// 'where' => [['admin_type', 2]]

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

         $admin_type = CommonRequest::get($request, 'admin_type');
         if(strlen($admin_type) > 0  && !in_array($admin_type, [0, '-1']))  Tool::appendCondition($queryParams, 'admin_type',  $admin_type . '=' . $admin_type, '&');

        $company_grade = CommonRequest::get($request, 'company_grade');
        if(strlen($company_grade) > 0  && !in_array($company_grade, [0, '-1']))  Tool::appendCondition($queryParams, 'company_grade',  $company_grade . '=' . $company_grade, '&');

        $pay_status = CommonRequest::get($request, 'pay_status');
        if(strlen($pay_status) > 0  && !in_array($pay_status, [0, '-1']))  Tool::appendCondition($queryParams, 'pay_status',  $pay_status . '=' . $pay_status, '&');

        $company_status = CommonRequest::get($request, 'company_status');
        if(strlen($company_status) > 0  && !in_array($company_status, [0, '-1']))  Tool::appendCondition($queryParams, 'company_status',  $company_status . '=' . $company_status, '&');

        $vod_id = CommonRequest::get($request, 'vod_id');
        if(strlen($vod_id) > 0 && !in_array($vod_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $vod_id, 'vod_id', [0, '0', ''], ',', false);

         $company_id = CommonRequest::get($request, 'company_id');
         if(strlen($company_id) > 0 && !in_array($company_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $company_id, 'company_id', [0, '0', ''], ',', false);

        $order_no = CommonRequest::get($request, 'order_no');
        if(strlen($order_no) > 0 && !in_array($order_no, [0, '-1']))  Tool::appendParamQuery($queryParams, $order_no, 'order_no', [0, '0', ''], ',', false);

        $invoice_template_id = CommonRequest::get($request, 'invoice_template_id');
        if(strlen($invoice_template_id) > 0 && !in_array($invoice_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id, 'invoice_template_id', [0, '0', ''], ',', false);

        $invoice_project_template_id = CommonRequest::get($request, 'invoice_project_template_id');
        if(strlen($invoice_project_template_id) > 0 && !in_array($invoice_project_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_project_template_id, 'invoice_project_template_id', [0, '0', ''], ',', false);

        $invoice_buyer_id = CommonRequest::get($request, 'invoice_buyer_id');
        if(strlen($invoice_buyer_id) > 0 && !in_array($invoice_buyer_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_buyer_id, 'invoice_buyer_id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 对单条数据关系进行格式化--具体的可以重写
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $info  单条数据  --- 一维数组
     * @param array $temDataList  关系数据  --- 一维或二维数组 -- 主要操作这个数据到  $info 的特别业务数据
     *                              如果是二维数组：下标已经是他们关系字段的值，多个则用_分隔好的
     * @param array $infoHandleKeyArr 其它扩展参数，// 一维数组，单条 数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function infoRelationFormatExtend(Request $request, Controller $controller, &$info, &$temDataList, $infoHandleKeyArr, &$returnFields){
        // if(empty($info)) return $returnFields;
        // $returnFields[$tem_ubound_old] = $tem_ubound_old;

        // 判断企业是否已经报名
        if(in_array('judgeJoined', $infoHandleKeyArr)){
            $is_joined = 0;
            $is_joined_text = '未购买';
            if(!empty($temDataList)){
                $is_joined = 1;
                $is_joined_text = '已购买';
            }
            $info['is_joined'] = $is_joined;
            $info['is_joined_text'] = $is_joined_text;
        }

        return $returnFields;
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
            Tool::bathPriceCutFloatInt($data_list, VodOrders::$IntPriceFields, VodOrders::$IntPriceIndex, 2, 2);
        }

        // 重写结束
        return $returnFields;
    }

    /**
     * 格式化关系数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array/ string $vod_order_id 课程企业报名表的id, 多条可以是一维数组或逗号分隔的字符
     * @return array  需要缴费的 订单记录 二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeRecordByVodOrderIds(Request $request, Controller $controller, $vod_order_id){

        if(is_string($vod_order_id)) $vod_order_id = explode(',', $vod_order_id);
        if(!is_array($vod_order_id)) $vod_order_id = [];
        if(empty($vod_order_id)) throws('请选择要缴费的记录');
        // 获得企业报名记录
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIVodOrdersBusiness::getRelationConfigs($request, $controller, [
                'vod_name' => ''
                , 'company_name' => ''
            ], []),
            // 'infoHandleKeyArr' => ['resetPayMethod']
            'listHandleKeyArr' => ['priceIntToFloat'],
        ];
        $vodOrderList = CTAPIVodOrdersBusiness::getFVFormatList( $request,  $controller, 1, 1
            , ['id' => $vod_order_id], false, [], $extParams);
        if(empty($vodOrderList)) throws('请选择需要缴费的记录！');
        foreach($vodOrderList as $vodOrderInfo){
            $company_name = $vodOrderInfo['company_name'] ?? '';
            if(in_array($vodOrderInfo['company_status'], [4])) throws($company_name . '订单批次【' . $vodOrderInfo['id'] . '】已作废状态，不可进行缴费操作！');
            if(in_array($vodOrderInfo['company_status'], [8])) throws($company_name . '订单批次【' . $vodOrderInfo['id'] . '】已到期状态，不可进行缴费操作！');
            if(in_array($vodOrderInfo['pay_status'], [4])) throws($company_name . '订单批次【' . $vodOrderInfo['id'] . '】已缴费状态，不可进行缴费操作！');

        }
        $companyIds = Tool::getArrFields($vodOrderList, 'company_id');
        if(count($companyIds) > 1) throws('每次缴费，只能选择相同的企业，才能进行多条记录批量缴费！');
        $invoiceTemplateIds = Tool::getArrFields($vodOrderList, 'invoice_template_id');
        if(count($invoiceTemplateIds) > 1) throws('每次缴费，只能选择相同的【发票开票模板】，才能进行多条记录批量缴费！');

        return $vodOrderList;
    }

    /**
     * 根据报名用户id,获得报名用户及支付信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $dataList 记录数据 -- 二维数组
     * @param int $company_id 所属企业id , 没有
     * @return  array 数组 [ '报名用户数据列表'， '用户包含的收款帐号信息-- 支付配置id为下标的二维数组', '企业id => 企业名称 键值对 --一维数组']
     * @author zouyan(305463219@qq.com)
     */
    public static function getPayByIds(Request $request, Controller $controller, $dataList, $company_id = 0){

        // 判断人员是否可以一起缴费【同一收款帐号，就可以付费】
        $vod_ids = Tool::getArrFields($dataList, 'vod_id');
        $invoice_template_id = Tool::getArrFields($dataList, 'invoice_template_id');
        if(count($invoice_template_id) > 1){
            throws('不同的【发票开票模板】的课程，不可以一起进行付款！请分别付款！');
        }

        $vodFormatList = CTAPIVodsBusiness::getCoursePayList($request, $controller, $vod_ids);// 以课程id为下标的二维数组
        $companyKV = [];// 企业id => 企业名称 键值对 --一维数组

        $pay_configs = [];// 课程id 为下标的 支付配置 二维数组
        $pay_configs_format = [];// 支付配置id为下标的二维数组
        foreach($dataList as &$t_info){
            $tem_vod_id = $t_info['vod_id'];
            $tem_company_id = $t_info['company_id'];
            $tem_company_name = $t_info['company_name'];
            $tem_real_name = $t_info['contacts'];
            $tem_mobile = $t_info['tel'];
            if(!isset($companyKV[$tem_company_id])) $companyKV[$tem_company_id] = $tem_company_name;
            $tem_pay_config = $pay_configs[$tem_vod_id] ?? [];
            if(empty($tem_pay_config)){

                // 课程信息
                $tem_pay_config_vod = $vodFormatList[$tem_vod_id] ?? [];
                if(empty($tem_pay_config_vod)) throws('【' . $tem_real_name . '(' . $tem_mobile . ')'  . '】课程信息不存在');

                // 判断课程状态
//                $tem_course_status_online = $tem_pay_config_vod['status_online'];// 状态(1正常(报名中)  2下架)
//                if(!in_array($tem_course_status_online, [1])) throws('课程非正常状态，不可进行缴费操作');

                $tem_pay_config_vod_format = Tool::getArrFormatFields($tem_pay_config_vod, ['pay_config_id', 'pay_method_text', 'pay_method', 'allow_pay_method', 'pay_key', 'pay_company_name'], false);
                $tem_pay_config = $tem_pay_config_vod_format;
                $pay_configs[$tem_vod_id] = $tem_pay_config;
                $pay_configs_format[$tem_pay_config['pay_config_id']] = $tem_pay_config;
            }
            if(empty($tem_pay_config)) throws('【' . $tem_real_name . '(' . $tem_mobile . ')'  . '】没有收款方式，不可以进行此操作');
            $t_info = array_merge($t_info, $tem_pay_config);
            // if(is_numeric($company_id) && $company_id > 0 && $company_id != $tem_company_id) throws('【' . $tem_real_name . '(' . $tem_mobile . ')'  . '】不是当前所属的企业，不可以进行此操作');

        }
        return [$dataList , $pay_configs_format, $companyKV];
    }

    /**
     * 选择完收款帐号及支付方式时，获得相关的数据
     * 根据报名用户id,及收款账号和收款方式 获得报名用户及支付信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $company_id 所属企业id , 没有
     * @return  array 数组 [ '当前支付方式的详情-一维数组', '报名用户数据列表--收款帐号id下标的数组'， '用户包含的收款帐号信息-- 支付配置id为下标的二维数组', '企业id => 企业名称 键值对 --一维数组']
     * @author zouyan(305463219@qq.com)
     */
    public static function getMethodInfoAndDataList(Request $request, Controller $controller, $id, $company_id = 0, $pay_config_id = 0, $pay_method = 0){

        if($pay_config_id <= 0 || $pay_method <= 0) throws('缴费方式信息有误');

        // 获得收款方式详情
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIOrderPayMethodBusiness::getRelationConfigs($request, $controller, ['resource_list'], []),

        ];
        $payMethodInfo = CTAPIOrderPayMethodBusiness::getFVFormatList( $request,  $controller, 4, 1
            , ['pay_method' => $pay_method], false, [], $extParams);
        if(empty($payMethodInfo)) throws('收款方式不存在');
        if($payMethodInfo['status_online'] != 1) throws('收款方式【' . $payMethodInfo['pay_name'] . '】已关闭');
//        $reDataArr['method_info'] = $payMethodInfo;

        $vodList = CTAPIVodOrdersBusiness::judgeRecordByVodOrderIds($request, $controller, $id);

        // 根据报名用户id,获得报名用户及支付信息
        list($dataList, $pay_configs_format, $companyKV) = CTAPIVodOrdersBusiness::getPayByIds($request, $controller, $vodList, $company_id);
        $dataPanyConfigList = Tool::arrUnderReset($dataList, 'pay_config_id', 2, '_');

        // $reDataArr['course_order_staff'] = $dataList;
//        $reDataArr['pay_config_format'] = $pay_configs_format;
//        $reDataArr['config_staff_list'] = $dataPanyConfigList;

        // 判断收款账号及选择的收款方式是否正确
        if(count($pay_configs_format) != 1) throws('收款账号信息有误');
        $payConfigInfo = $pay_configs_format[$pay_config_id] ?? [];
        if(empty($payConfigInfo)) throws('收款账号参数有误');
        $pay_config_method = $payConfigInfo['pay_method'] ?? 0;
        if(($pay_config_method & $pay_method) != $pay_method)  throws('缴费方式不可用，请重新选择或重新刷新页面再缴费');
        return [$payMethodInfo , $dataPanyConfigList, $pay_configs_format, $companyKV];
    }


    /**
     * 生成订单操作
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $company_id 人员所属的企业 id，可为 0--没有所属企业的人员  或用户id--无所属企业
     * @param string/array $ids 数组或字符串 学员id
     * @param int $pay_config_id 收款帐号配置id
     * @param int $pay_method 收款方式id
     * @param array $otherParams 其它参数
     *   $otherParams = [
     *      'total_price_discount' => '0.02',// 商品下单时优惠金额
     *      'payment_amount' => 0,// 总支付金额
     *      'change_amount' => 0,// 找零金额
     *       'remarks' => '',// 订单备注
     *       'auth_code' => '',// 扫码枪扫的付款码
     *  ];
     * @param int $operate_type 操作类型1用户操作2平台操作
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array
     *  [
     *    'order_no' =>$order_no ,
     *    'pay_config_id' => $pay_config_id,
     *    'pay_method' => $pay_method,
     *    'params' => $return_params
     *   ]
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrderAjax(Request $request, Controller $controller, $organize_id = 0, $own_company_id = 0
        , $ids = 0, $pay_config_id = 0, $pay_method = 0, $otherParams = [], $operate_type = 2, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $own_company_id,
            'organize_id' => $organize_id,
            'ids' => $ids,
            'pay_config_id' => $pay_config_id,
            'pay_method' => $pay_method,
            'otherParams' => $otherParams,
            'operate_type' => $operate_type,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $reData = static::exeDBBusinessMethodCT($request, $controller, '',  'createOrder', $apiParams, $company_id, $notLog);

        return $reData;
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 报名操作
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $vodId  课程id
     * @param array $userInfo  报名的用户信息-- 一维数组
     * @param array $extendArr  其它扩展参数，// 一维数组 ['contacts' => $contacts,'tel' => $tel, 'certificate_company' => '每个学员对应的-证书所属单位']
     * @return mixed  新增报名表的id
     * @author zouyan(305463219@qq.com)
     */
    public static function vodJoin(Request $request, Controller $controller, $vodId = 0, $userInfo = [], $extendArr = []){

        if(empty($userInfo)) throws('请选择报名企业或学员');
        $staff_id = $userInfo['id'] ?? 0;
        // 获得课程信息及报名的学员信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIVodsBusiness::getRelationConfigs($request, $controller, [], []),// 'resource_list' => '', 'course_content' => '', 'course_order_company' => ''
            // 'infoHandleKeyArr' => ['resetPayMethod'],
            'listHandleKeyArr' => ['initPayMethodText'],// , 'priceIntToFloat'
            'sqlParams' => ['where' => [['open_status', 1]]]
        ];
        $info = CTAPIVodsBusiness::getInfoData($request, $controller, $vodId, [], '', $extParams);
        if(empty($info)) throws('课程信息不存在或已下线！');

         $company_grade = $userInfo['company_grade'] ?? 0;// $controller->user_info['company_grade'];
        $price = $info['price_general'];
        if($company_grade > 1) $price = $info['price_member'];// 会员价
        $invoice_template_id = $info['invoice_template_id'];
        $invoiceTemplateInfo = CTAPIInvoiceTemplateBusiness::getFVFormatList( $request,  $controller, 4, 1
            , ['id' => $invoice_template_id], false, [], [], 1);
        if(empty($invoiceTemplateInfo)) throws('发票开票模板不存在！请联系系统管理员。');
        if(!in_array($invoiceTemplateInfo['open_status'], [1])) throws('发票开票模板非开启状态！请联系系统管理员。');

        $invoice_project_template_id = $info['invoice_project_template_id'];
        $invoiceProjectInfo = CTAPIInvoiceProjectTemplateBusiness::getFVFormatList( $request,  $controller, 4, 1
            , ['id' => $invoice_project_template_id], false, [], [], 1);
        if(empty($invoiceProjectInfo)) throws('发票商品项目模板不存在！请联系系统管理员。');
        if(!in_array($invoiceProjectInfo['open_status'], [1])) throws('发票商品项目模板非开启状态！请联系系统管理员。');
        // $price = Tool::formatFloatVal($price, 2, 4);

        $admin_type = $userInfo['admin_type'] ?? 0;// 用户类型1平台2企业4个人
        $staff_company_id = $userInfo['company_id'] ?? 0;
        $company_ids = [$staff_id];
        if($staff_company_id > 0){
            array_push($company_ids, $staff_company_id);
        }

        // 判断选择的人员是不是有不可以报名的用户
        $staffCount = static::getFVFormatList( $request,  $controller, 8, 1
            ,  ['vod_id' => $vodId, 'company_id' => $company_ids, 'company_status' => [1]], false,[], []);
        if($staffCount > 0) throws('企业或人员不可重复购买同一课程！如果未付款生效，请完成付款！才能生效');

        $join_num = 1;
        $saveData = [
            'vod_id' => $vodId,
            'admin_type' => $admin_type,
            'company_id' => $staff_id,
            'company_grade' => $company_grade,
            'join_num' => $join_num,
            // 'cancel_num' => 0, // 已作废人数
            'contacts' => $extendArr['contacts']  ?? '',
            'tel' => $extendArr['tel'] ?? '',
            'price' => $price,
            'price_total' => bcmul($price, $join_num),// $price * $join_num,
            // 'order_no' = '',// 订单号
            'pay_status' => 1,// 缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
            'order_date' => date('Y-m-d'),
            'order_time' => date('Y-m-d H:i:s'),
            // 'pay_date' => date('Y-m-d'),
            // 'pay_time' => date('Y-m-d H:i:s'),
            'company_status' => 1,// 报名状态1正常4已作废8已到期
            // 'cancel_date' => date('Y-m-d'),
            // 'cancel_time' => date('Y-m-d H:i:s'),
            // 'finish_date' => date('Y-m-d'),
            // 'finish_time' => date('Y-m-d H:i:s'),
            'invoice_template_id' => $invoice_template_id,
            'invoice_project_template_id' => $invoice_project_template_id,
            // 'invoice_buyer_id' => 0,
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
        $courseOrderId = 0;
        $resultDatas = static::replaceById($request, $controller, $saveData, $courseOrderId, $extParams, true);
        return $resultDatas;
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

        $headArr = ['vod_name'=>'视频课程', 'company_name'=>'单位', 'company_grade_text'=>'会员等级'
            , 'contacts'=>'联络人', 'tel'=>'联络人电话', 'company_status_text'=>'报名状态', 'price'=>'单价'
            , 'price_total'=>'总价', 'pay_status_text'=>'缴费状态', 'invoice_project_template_name'=> '发票项目模板'
            , 'invoice_template_name'=> '发票开票模板', 'order_time'=>'报名时间', 'pay_time'=>'缴费时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','视频课程购买' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => '视频课程购买']);
    }
}
