<?php
// 付款/收款记录
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\PaymentProject;
use App\Models\QualityControl\PaymentProjectFields;
use App\Models\QualityControl\PaymentRecord;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIPaymentRecordBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\PaymentRecordAPI';
    public static $table_name = 'payment_record';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // public static $orderBy = PaymentRecord::ORDER_BY;// ['sort_num' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']

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
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 16
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams([], $extendParams, 'company_info'), '', []),// ['where' => [['admin_type', 2]]]

            // 获得报名用户的名称
            'user_staff_name' => CTAPIStaffHistoryBusiness::getTableRelationConfigInfo($request, $controller
                , ['pay_user_id_history' => 'id']
                , 1, 128
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'user_staff_name'),
                    static::getUboundRelationExtendParams($extendParams, 'user_staff_name')),
                static::getRelationSqlParams([], $extendParams, 'user_staff_name'), '', []),
            // 获得报名企业的名称
            'pay_company_name' => CTAPIStaffHistoryBusiness::getTableRelationConfigInfo($request, $controller
                , ['pay_company_id_history' => 'id']
                , 1, 1024
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'pay_company_name'),
                    static::getUboundRelationExtendParams($extendParams, 'pay_company_name')),
                static::getRelationSqlParams([], $extendParams, 'pay_company_name'), '', []),
            // 获得付款/收款项目名称--历史
            'payment_project_history' => CTAPIPaymentProjectHistoryBusiness::getTableRelationConfigInfo($request, $controller
                , ['payment_project_id_history' => 'id']
                , 1, 2
                ,'','',
                CTAPIPaymentProjectHistoryBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'payment_project_history'),
                    static::getUboundRelationExtendParams($extendParams, 'payment_project_history')),
                static::getRelationSqlParams([], $extendParams, 'payment_project_history'), '', []),

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

//        $aaaa = CommonRequest::get($request, 'aaaa');
//        if(strlen($aaaa) > 0  && !in_array($aaaa, [0, '-1']))  Tool::appendCondition($queryParams, 'aaaa', $aaaa . '=' . $aaaa, '&', 'where', 1, false, Model::CONST_ARR); // --逻辑 & 操作，且有数组范围
//        if(strlen($aaaa) > 0  && !in_array($aaaa, [0, '-1']))  Tool::appendCondition($queryParams, 'aaaa', $aaaa . '=' . $aaaa, '&'); // --逻辑 & 操作，没有数组范围
//        if(strlen($aaaa) > 0  && !in_array($aaaa, [0, '-1']))  Tool::appendCondition($queryParams, 'aaaa', [1, 2, 4], '', 'whereIn', 2, true, Model::CONST_ARR); // --whereIn 合并 操作，且有数组范围

//        $bbbb = CommonRequest::get($request, 'bbbb');
//        if(strlen($bbbb) > 0 && !in_array($bbbb, [0, '-1']))  Tool::appendParamQuery($queryParams, $bbbb, 'bbbb', [0, '0', ''], ',', false);

        $type_no = CommonRequest::get($request, 'type_no');
        if(strlen($type_no) > 0  && !in_array($type_no, [0, '-1']))  Tool::appendCondition($queryParams, 'type_no',  $type_no . '=' . $type_no, '&');

        $pay_company_id = CommonRequest::get($request, 'pay_company_id');
        if(strlen($pay_company_id) > 0 && !in_array($pay_company_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $pay_company_id, 'pay_company_id', [0, '0', ''], ',', false);

        $pay_user_id = CommonRequest::get($request, 'pay_user_id');
        if(strlen($pay_user_id) > 0 && !in_array($pay_user_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $pay_user_id, 'pay_user_id', [0, '0', ''], ',', false);

        $payment_project_id = CommonRequest::get($request, 'payment_project_id');
        if(strlen($payment_project_id) > 0 && !in_array($payment_project_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $payment_project_id, 'payment_project_id', [0, '0', ''], ',', false);

        $order_no = CommonRequest::get($request, 'order_no');
        if(strlen($order_no) > 0 && !in_array($order_no, [0, '-1']))  Tool::appendParamQuery($queryParams, $order_no, 'order_no', [0, '0', ''], ',', false);

        $pay_status = CommonRequest::get($request, 'pay_status');
        if(strlen($pay_status) > 0  && !in_array($pay_status, [0, '-1']))  Tool::appendCondition($queryParams, 'pay_status', $pay_status . '=' . $pay_status, '&', 'where', 1, false, PaymentRecord::PAY_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $record_status = CommonRequest::get($request, 'record_status');
        if(strlen($record_status) > 0  && !in_array($record_status, [0, '-1']))  Tool::appendCondition($queryParams, 'record_status', $record_status . '=' . $record_status, '&', 'where', 1, false, PaymentRecord::RECORD_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $handle_status = CommonRequest::get($request, 'handle_status');
        if(strlen($handle_status) > 0  && !in_array($handle_status, [0, '-1']))  Tool::appendCondition($queryParams, 'handle_status', $handle_status . '=' . $handle_status, '&', 'where', 1, false, PaymentRecord::HANDLE_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $valid_limit = CommonRequest::get($request, 'valid_limit');
        if(strlen($valid_limit) > 0  && !in_array($valid_limit, [0, '-1']))  Tool::appendCondition($queryParams, 'valid_limit', $valid_limit . '=' . $valid_limit, '&', 'where', 1, false, PaymentRecord::VALID_LIMIT_ARR); // --逻辑 & 操作，且有数组范围

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
            Tool::bathPriceCutFloatInt($data_list, PaymentRecord::$IntPriceFields, PaymentRecord::$IntPriceIndex, 2, 2);
        }

        // 组织分类编号文字
        if(in_array('initTypeNoText', $handleKeyArr)){
            $type_no_kv = CTAPIPaymentTypeBusiness::getListKV($request, $controller, ['key' => 'type_no', 'val' => 'type_name'], [
                // 'sqlParams' => ['orderBy' => CompanySubject::ORDER_BY]// 'where' => [['open_status', 1]],
            ]);
            foreach($data_list as $k => &$v){
                $type_no = $v['type_no'];
                $seled_type_no_kv = [];
                foreach($type_no_kv as $t_k => $t_v){
                    if( ($type_no & $t_k) === $t_k) $seled_type_no_kv[$t_k] = $t_v;
                }
                $v['type_no_kv'] = $seled_type_no_kv;
                $v['type_no_text'] = implode('、', $seled_type_no_kv);
            }
            $returnFields['type_no_kv'] = 'type_no_kv';
            $returnFields['type_no_text'] = 'type_no_text';
        }

        // 把付款时限及有效时限转为时分秒
        foreach($data_list as $k => &$v){
            $pay_valid_second = $v['pay_valid_second'] ?? 0;
            $pay_valid_second_format = '';
            if($pay_valid_second > 0){
                $pay_valid_second_format = Tool::formatSecondNum($pay_valid_second);
            }
            $v['pay_valid_second_format'] = $pay_valid_second_format;


            $valid_limit_second = $v['valid_limit_second'] ?? 0;
            $valid_limit_second_format = '';
            if($valid_limit_second > 0){
                $valid_limit_second_format = Tool::formatSecondNum($valid_limit_second);
            }
            $v['valid_limit_second_format'] = $valid_limit_second_format;
        }
        $returnFields['pay_valid_second_format'] = 'pay_valid_second_format';
        $returnFields['valid_limit_second_format'] = 'valid_limit_second_format';

        // 重写结束
        return $returnFields;
    }

    /**
     * 格式化数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $finalHandleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @param boolean 原数据类型 true:二维[默认];false:一维  ----没有用了，到不到
     * @return  array
     * @author zouyan(305463219@qq.com)
     */
    public static function handleFinalDataFormat(Request $request, Controller $controller, &$data_list, $finalHandleKeyArr, &$returnFields = [], $isMulti = true)
    {

        // if(empty($data_list)) return $data_list;
        // 重写开始

//        if(in_array('mergeZeroName', $finalHandleKeyArr)){
//          // ...对 $data_list做特殊处理
//        }

        // 列表显示时对字段数据进行格式化--主有用于后台列表的格式化数据
         if(in_array('sysFormatPayNameData', $finalHandleKeyArr)){
            foreach($data_list as $k => &$v){
//                $project_fields = $v['project_fields'] ?? [];
//                $field_name_arr = Tool::getArrFields($project_fields, 'field_name');
//                $v['project_fields_names'] = implode('、', $field_name_arr);
                $pay_company_id = $v['pay_company_id'] ?? 0;
                $pay_company_name = $v['pay_company_name'] ?? '';
                $user_staff_name = $v['user_staff_name'] ?? '';
                $pay_user_id = $v['pay_user_id'] ?? 0;
                $pay_show_name = $pay_company_name;
                if(!empty($user_staff_name)){
                    $pay_show_name = $user_staff_name;
                }
                $pay_show_id = $pay_company_id;
                if(is_numeric($pay_user_id) && $pay_user_id > 0 ){
                    $pay_show_id = $pay_user_id;
                }
                $v['pay_show_id'] = $pay_show_id;
                $v['pay_show_name'] = $pay_show_name;
            }
            $returnFields['pay_show_name'] = 'pay_show_name';
         }

        return $data_list;
    }

    /**
     * 收款功能
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $ower_type 操作类型 0：大后台 ； >0 所属的用户
     * @param int $pay_company_id 支付公司ID
     * @param int $pay_user_id 支付用户ID
     * @return mixed  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function paySave(Request $request, Controller $controller, $pay_company_id = 0, $pay_user_id = 0, $ower_type = 0){

        $now_time = date('Y-m-d H:i:s');
        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $payment_project_id = CommonRequest::getInt($request, 'payment_project_id');
        $company_id = ($ower_type == 0) ? CommonRequest::getInt($request, 'company_id') : $controller->user_id;
        // 获得收款项目信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIPaymentProjectBusiness::getRelationConfigs($request, $controller,
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
        $info = CTAPIPaymentProjectBusiness::getInfoData($request, $controller, $payment_project_id, [], '', $extParams);
        if(empty($info)) throws('记录不存在');
        if($ower_type > 0 && $company_id != $info['company_id']) throws('您没有此记录的操作权限');
        $db_company_id = $info['company_id'];
        // 判断状态
        $open_status = $info['open_status'] ?? 0;
        if(!in_array($open_status, [PaymentProject::OPEN_STATUS_OPEN])) throws('记录非开启状态，不可进行此操作');
        //判断收费状态
        $pay_status = $info['pay_status'] ?? 0;
        if(!in_array($pay_status, [PaymentProject::PAY_STATUS_DOING])) throws('记录非收费中状态，不可进行此操作');
        $pay_valid_status = $info['pay_valid_status'] ?? 0;
        $pay_begin_time = $info['pay_begin_time'] ?? '';
        $pay_end_time = $info['pay_end_time'] ?? '';
        // 指定时间--判断是否在时间内
        if(in_array($pay_valid_status, [PaymentProject::PAY_VALID_STATUS_FIXED])){
            // 判断开始结束日期
             Tool::judgeBeginEndDate($pay_begin_time, $pay_end_time, 1 + 2 + 4 + 128 + 256 + 512, 1, date('Y-m-d H:i:s'), '收费起止时间');
        }

        // 收款金额
        $specified_amount_status = $info['specified_amount_status'] ?? 0;
        $db_pay_amount = $info['pay_amount'] ?? 0;
        $pay_amount = CommonRequest::get($request, 'pay_amount');
        if(in_array($specified_amount_status, [PaymentProject::SPECIFIED_AMOUNT_STATUS_FIXED])){
            if($db_pay_amount != $pay_amount) throws('收款金额必须是【¥' . $db_pay_amount . '】，不可进行修改！');
        }
        if(!is_numeric($pay_amount) || $pay_amount < 0) throws('收款金额必须是大于等于0的数值！');

        // 优惠金额
        $discount_amount = CommonRequest::get($request, 'discount_amount');
        if(!is_numeric($discount_amount) || empty($discount_amount)) $discount_amount = 0;// 初始为0
        $tem_real_amount = bcsub($pay_amount, $discount_amount, 2);// $total_price - $total_price_discount;

        // 优惠说明
        $discount_explain = CommonRequest::get($request, 'discount_explain');
        $discount_explain = replace_enter_char($discount_explain, 1);
        // 实收金额
        $real_amount = CommonRequest::get($request, 'real_amount');
//        if($tem_real_amount != $real_amount){
//            throws('实收金额不一致！');
//        }
        if(!is_numeric($tem_real_amount) || $tem_real_amount < 0) throws('实收金额必须是大于等于0的数值！');

        // 对有效时长进行判断
        $project_valid_limit = $info['valid_limit'] ?? 0;
        if(in_array($project_valid_limit, [PaymentProject::VALID_LIMIT_FIXED_NOT_REPEAT, PaymentProject::VALID_LIMIT_FIXED])){
            // 获得有效的数据列表
            $extParamsRecord = [
                'sqlParams' => [
                    // 'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                    'orderBy' => CTAPIPaymentRecordBusiness::$orderBy
                ]
            ];
            if($project_valid_limit == PaymentProject::VALID_LIMIT_FIXED){
                $extParamsRecord['sqlParams']['where'] = [
                    ['begin_time', '<=', $now_time],
                    ['finish_time', '>=', $now_time]
                ];
            }
            $recordInfo = CTAPIPaymentRecordBusiness::getFVFormatList( $request,  $controller, 4, 1
                ,  [
                    'pay_company_id' => $pay_company_id,
                    'pay_user_id' => $pay_user_id,
                    'payment_project_id' => $payment_project_id,
                    'record_status' => PaymentRecord::RECORD_STATUS_NORMAL,
                    'pay_status' => [
                        PaymentRecord::PAY_STATUS_PART_PAY,PaymentRecord::PAY_STATUS_PAYED,PaymentRecord::PAY_STATUS_PART_REFUND
                    ]], false,[]
                , $extParamsRecord);
            if($project_valid_limit == PaymentProject::VALID_LIMIT_FIXED_NOT_REPEAT && !empty($recordInfo)) throws('已有付款记录，不可再次付款！');
            if($project_valid_limit == PaymentProject::VALID_LIMIT_FIXED && !empty($recordInfo)){
                // 对时间进行再次判断
                $record_begin_time = $recordInfo['begin_time'];
                $record_finish_time = $recordInfo['finish_time'];

                if(!empty($record_begin_time) && !empty($record_finish_time)){
                    throws('有效期【' . $record_begin_time . '--' . $record_finish_time . '】内已有付款记录，不可再次付款！');
                }
            }

        }


        // 字段值
        $field_arr = [];
        $project_fields = $info['project_fields'] ?? [];
        $field_num = count($project_fields);
        foreach($project_fields as $field_info){
            $field_id = $field_info['id'] ?? 0;
            $field_name = $field_info['field_name'] ?? '';
            $required_status = $field_info['required_status'] ?? 0;
            $input_status = $field_info['input_status'] ?? 0;
            $show_status = $field_info['show_status'] ?? 0;
            $val_type = $field_info['val_type'] ?? 0;
            $sel_items = $field_info['sel_items'] ?? 0;
            $field_input = 'field_' . $field_id;
            $field_val = '';
            // 输入框 多行文本 富文本  单选框
            if(in_array($val_type, [PaymentProjectFields::VAL_TYPE_INPUT, PaymentProjectFields::VAL_TYPE_TEXTAREA,
                PaymentProjectFields::VAL_TYPE_RICHTEXT, PaymentProjectFields::VAL_TYPE_RADIO])){
                $field_val = CommonRequest::get($request, $field_input);
                if($val_type == PaymentProjectFields::VAL_TYPE_RICHTEXT) $field_val = stripslashes($field_val); // 富文本
                if($val_type == PaymentProjectFields::VAL_TYPE_TEXTAREA) $field_val = replace_enter_char($field_val, 1);// 多行文本
            }else if($val_type == PaymentProjectFields::VAL_TYPE_CHECKBOX){// 复选框
                $field_val = CommonRequest::get($request, $field_input);
                Tool::valToArrVal($field_val, ',');// 不是数组，则转为数组
                $field_val = implode(',', $field_val);// 数组转为逗号分隔的字符
            }
            array_push($field_arr, [
                'company_id' => $db_company_id,
                // 'payment_record_id' => 0,
                'payment_project_id' => $payment_project_id,
                // 'payment_project_id_history' => 0,
                'payment_project_fields_id' => $field_id,
                // 'payment_project_fields_id_history' => 0,
                'val_type' => $val_type,
                'field_val' => $field_val,
                'sort_num' => $field_num,
            ]);
            $field_num--;
        }

        $pay_valid_second = $info['pay_valid_second'] ?? 0;// 缴费期限时长【秒】
        $pay_end_time = Tool::addMinusDate($now_time, ['+' . $pay_valid_second . ' second'], 'Y-m-d H:i:s', 1, '时间');

        $saveData = [
            'company_id' => $db_company_id,// 公司ID
            'payment_project_id' => $payment_project_id,// 所属付款/收款项目id
            // 'payment_project_id_history' => $bbbbbb,// 所属付款/收款项目id历史
            'type_no' => $info['type_no'] ?? 0,// 分类编号值；< 64 ;(只能是1\2\4\8等；如：1第一类、2第二类、4第三类)
            'pay_company_id' => $pay_company_id,// 支付公司ID
            'pay_user_id' => $pay_user_id,// 支付用户ID
            //'order_no' => $bbbbbb,// 订单号
            'pay_status' => PaymentRecord::PAY_STATUS_WAIT,//  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
            'order_date' => date('Y-m-d'),// 下单日期
            'order_time' => date('Y-m-d H:i:s'),// 下单时间
            'pay_valid_second' => $pay_valid_second,// 缴费期限时长【秒】
            'pay_end_time' => $pay_end_time,// 最后缴费时间
            // 'pay_date' => $bbbbbb,// 缴费日期
            // 'pay_time' => $bbbbbb,// 缴费时间
            'record_status' => PaymentRecord::RECORD_STATUS_NORMAL,// 记录状态1正常4已作废8已到期
            'handle_status' => PaymentRecord::HANDLE_STATUS_WAIT,// 记录处理状态1待处理；2处理中；4已处理
            // 'cancel_date' => $bbbbbb,// 作废日期【全作废】
            // 'cancel_time' => $bbbbbb,// 作废时间【全作废】
            'valid_limit' => $info['valid_limit'] ?? 0,// 有效时长1长期有效；2指定有效时长
            'valid_limit_second' => $info['valid_limit_second'] ?? 0,// 有效时长【总秒数】
            // 'begin_date' => $bbbbbb,// 开始日期【开始计时】
            // 'begin_time' => $bbbbbb,// 开始时间【开始计时】
            // 'finish_date' => $bbbbbb,// 到期日期【全到期】
            // 'finish_time' => $bbbbbb,// 到期时间【全到期】
            'pay_amount' => $pay_amount,// 收费金额
            'discount_amount' => $discount_amount,// 优惠金额
            'discount_explain' => $discount_explain,// 优惠说明
            'real_amount' => $tem_real_amount,// 实收金额
            'wait_refund_amount' => 0,// 待退金额
            'refunded_amount' => 0,// 已退金额
            'final_amount' => $tem_real_amount,// 最终金额
            'invoice_template_id' => $info['invoice_template_id'] ?? 0,// 发票开票模板id【付款是会更新为最新的】
            // 'invoice_template_id_history' => $bbbbbb,// 发票开票模板id历史【付款是会更新为最新的】
            'invoice_project_template_id' => $info['invoice_project_template_id'] ?? 0,// 发票商品项目模板id【付款是会更新为最新的】
            // 'invoice_project_template_id_history' => $bbbbbb,// 发票商品项目模板id历史【付款是会更新为最新的】
            // 'invoice_buyer_id' => $bbbbbb,// 发票配置购买方id【暂不使用此方法】
            'field_list' => $field_arr, // 字段值
        ];
        // 价格转为整型
        Tool::bathPriceCutFloatInt($saveData, PaymentRecord::$IntPriceFields, PaymentRecord::$IntPriceIndex, 1);

        if($id <= 0) {// 新加;要加入的特别字段
//                    $addNewData = [
//                        // 'account_password' => $account_password,
//                    ];
//                    $saveData = array_merge($saveData, $addNewData);
        }else{
            $info = CTAPIPaymentRecordBusiness::getInfoData($request, $controller, $id, [], '', []);
            if(empty($info)) throws('记录不存在');
            if($ower_type > 0 && $company_id != $info['company_id']) throws('您没有此记录的操作权限');
            // 如果改变了所属企业,需要重新统计数
            if(isset($saveData['company_id']) && $company_id != $info['company_id']) $saveData['force_company_num'] = 1;
        }

        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIPaymentRecordBusiness::replaceById($request, $controller, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }


    /**
     * 格式化关系数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array/ string $payment_record_id 课程企业报名表的id, 多条可以是一维数组或逗号分隔的字符
     * @return array  需要缴费的 订单记录 二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeRecordByPaymentRecordIds(Request $request, Controller $controller, $payment_record_id){

        Tool::valToArrVal($payment_record_id, ',');// 不是数组，则转为数组
        if(empty($payment_record_id)) throws('请选择要缴费的记录');
        // 获得企业报名记录
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIPaymentRecordBusiness::getRelationConfigs($request, $controller, [
                'company_info' => '',
                'pay_company_name' => '',
                'user_staff_name' => '',
                'payment_project_history' => '',
            ], []),
            // 'infoHandleKeyArr' => ['resetPayMethod']
            'listHandleKeyArr' => ['priceIntToFloat', 'initTypeNoText'],
            'finalHandleKeyArr'=> ['sysFormatPayNameData'],
        ];
        $paymentRecordList = CTAPIPaymentRecordBusiness::getFVFormatList( $request,  $controller, 1, 1
            , ['id' => $payment_record_id], false, [], $extParams);
        if(empty($paymentRecordList)) throws('请选择需要缴费的记录！');
        foreach($paymentRecordList as $recordInfo){
            $pay_show_name = $recordInfo['pay_show_name'] ?? '';
            if(in_array($recordInfo['record_status'], [PaymentRecord::RECORD_STATUS_CANCEL])) throws($pay_show_name . '订单批次【' . $recordInfo['id'] . '】已作废状态，不可进行缴费操作！');
            if(in_array($recordInfo['record_status'], [PaymentRecord::RECORD_STATUS_EXPIRE])) throws($pay_show_name . '订单批次【' . $recordInfo['id'] . '】已到期状态，不可进行缴费操作！');
            if(in_array($recordInfo['pay_status'], [PaymentRecord::PAY_STATUS_PAYED])) throws($pay_show_name . '订单批次【' . $recordInfo['id'] . '】已缴费状态，不可进行缴费操作！');

        }
        $payShowIds = Tool::getArrFields($paymentRecordList, 'pay_show_id');
        if(count($payShowIds) > 1) throws('每次缴费，只能选择相同的缴费者，才能进行多条记录批量缴费！');
        $invoiceTemplateIds = Tool::getArrFields($paymentRecordList, 'invoice_template_id');
        if(count($invoiceTemplateIds) > 1) throws('每次缴费，只能选择相同的【发票开票模板】，才能进行多条记录批量缴费！');

        return $paymentRecordList;
    }

    /**
     * 根据报名用户id,获得报名用户及支付信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $dataList 记录数据 -- 二维数组
     * @param int $pay_show_id 所属企业id ---付款的企业或用户id, 没有
     * @return  array 数组 [ '报名用户数据列表'， '用户包含的收款帐号信息-- 支付配置id为下标的二维数组', '企业id => 企业名称 键值对 --一维数组']
     * @author zouyan(305463219@qq.com)
     */
    public static function getPayByIds(Request $request, Controller $controller, $dataList, $pay_show_id = 0){

        // 判断人员是否可以一起缴费【同一收款帐号，就可以付费】
        $payment_project_ids = Tool::getArrFields($dataList, 'payment_project_id');
        $invoice_template_id = Tool::getArrFields($dataList, 'invoice_template_id');
        if(count($invoice_template_id) > 1){
            throws('不同的【发票开票模板】的课程，不可以一起进行付款！请分别付款！');
        }

        $PaymentProjectFormatList = CTAPIPaymentProjectBusiness::getPaymentProjectPayList($request, $controller, $payment_project_ids);// 以课程id为下标的二维数组
        $PayShowIdNameKV = [];// 企业id => 企业名称 键值对-- 付款的用户id => 名称 --一维数组

        $pay_configs = [];// 课程id 为下标的 支付配置 二维数组
        $pay_configs_format = [];// 支付配置id为下标的二维数组
        foreach($dataList as &$t_info){
            $tem_payment_project_id = $t_info['payment_project_id'];
            $tem_pay_show_id = $t_info['pay_show_id'];
            $tem_pay_show_name = $t_info['pay_show_name'];
//            $tem_real_name = $t_info['contacts'];
//            $tem_mobile = $t_info['tel'];
            if(!isset($PayShowIdNameKV[$tem_pay_show_id])) $PayShowIdNameKV[$tem_pay_show_id] = $tem_pay_show_name;
            $tem_pay_config = $pay_configs[$tem_payment_project_id] ?? [];
            if(empty($tem_pay_config)){

                // 课程信息
                $tem_pay_config_vod = $PaymentProjectFormatList[$tem_payment_project_id] ?? [];
                // if(empty($tem_pay_config_vod)) throws('【' . $tem_real_name . '(' . $tem_mobile . ')'  . '】课程信息不存在');
                if(empty($tem_pay_config_vod)) throws('收费项目信息不存在');

                // 判断课程状态
//                $tem_course_status_online = $tem_pay_config_vod['status_online'];// 状态(1正常(报名中)  2下架)
//                if(!in_array($tem_course_status_online, [1])) throws('课程非正常状态，不可进行缴费操作');

                $tem_pay_config_vod_format = Tool::getArrFormatFields($tem_pay_config_vod, ['pay_config_id', 'pay_method_text', 'pay_method', 'allow_pay_method', 'pay_key', 'pay_company_name'], false);
                $tem_pay_config = $tem_pay_config_vod_format;
                $pay_configs[$tem_payment_project_id] = $tem_pay_config;
                $pay_configs_format[$tem_pay_config['pay_config_id']] = $tem_pay_config;
            }
            // if(empty($tem_pay_config)) throws('【' . $tem_real_name . '(' . $tem_mobile . ')'  . '】没有收款方式，不可以进行此操作');
            if(empty($tem_pay_config)) throws('没有收款方式，不可以进行此操作');
            $t_info = array_merge($t_info, $tem_pay_config);
            // if(is_numeric($pay_show_id) && $pay_show_id > 0 && $pay_show_id != $tem_pay_show_id) throws('【' . $tem_real_name . '(' . $tem_mobile . ')'  . '】不是当前所属的企业，不可以进行此操作');

        }
        return [$dataList , $pay_configs_format, $PayShowIdNameKV];
    }


    /**
     * 选择完收款帐号及支付方式时，获得相关的数据
     * 根据报名用户id,及收款账号和收款方式 获得报名用户及支付信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $pay_show_id 付款的用户 或 企业id , 没有
     * @return  array 数组 [ '当前支付方式的详情-一维数组', '报名用户数据列表--收款帐号id下标的数组'， '用户包含的收款帐号信息-- 支付配置id为下标的二维数组', '企业id => 企业名称 键值对 --一维数组']
     * @author zouyan(305463219@qq.com)
     */
    public static function getMethodInfoAndDataList(Request $request, Controller $controller, $id, $pay_show_id = 0, $pay_config_id = 0, $pay_method = 0){

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

        $recordList = CTAPIPaymentRecordBusiness::judgeRecordByPaymentRecordIds($request, $controller, $id);

        // 根据报名用户id,获得报名用户及支付信息
        list($dataList, $pay_configs_format, $payShowKV) = CTAPIPaymentRecordBusiness::getPayByIds($request, $controller, $recordList, $pay_show_id);
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
        return [$payMethodInfo , $dataPanyConfigList, $pay_configs_format, $payShowKV];
    }

    /**
     * 生成订单操作
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $pay_show_id 付费的用户id或企业 id
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
    public static function createOrderAjax(Request $request, Controller $controller, $organize_id = 0, $pay_show_id = 0
        , $ids = 0, $pay_config_id = 0, $pay_method = 0, $otherParams = [], $operate_type = 2, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $pay_show_id,
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

}
