<?php
// 付款/收款项目
namespace App\Business\Controller\API\QualityControl;

use App\Business\DB\QualityControl\OrderPayConfigDBBusiness;
use App\Models\QualityControl\PaymentProject;
use App\Models\QualityControl\PaymentProjectFields;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIPaymentProjectBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\PaymentProjectAPI';
    public static $table_name = 'payment_project';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    public static $orderBy = PaymentProject::ORDER_BY;// ['sort_num' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']

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
            // 获得封面图
            'resource_list' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['resource_id' => 'id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list')),
                static::getRelationSqlParams([], $extendParams, 'resource_list'), '', ['extendConfig' => ['listHandleKeyArr' => ['format_resource']]]),// , 'infoHandleKeyArr' => ['resource_list']

            // 获得企业名称
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 16
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams([], $extendParams, 'company_info'), '', []),// ['where' => [['admin_type', 2]]]

            // 获得付款/收款项目字段
            'project_fields' => CTAPIPaymentProjectFieldsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'payment_project_id']
                , 2, 1
                ,'','',
                CTAPIPaymentProjectFieldsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'project_fields'),
                    static::getUboundRelationExtendParams($extendParams, 'project_fields')),
                static::getRelationSqlParams(['orderBy' => CTAPIPaymentProjectFieldsBusiness::$orderBy], $extendParams, 'project_fields'), '', []),

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

        if(($return_num & 2) == 2){// 给上一级返回名称 title 下标
            $one_field = ['key' => 'title', 'return_type' => 2, 'ubound_name' => 'title', 'split' => '、'];// 获得名称
            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
            array_push($return_data['one_field'], $one_field);
        }

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

        $resource_id = CommonRequest::get($request, 'resource_id');
        if(strlen($resource_id) > 0 && !in_array($resource_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $resource_id, 'resource_id', [0, '0', ''], ',', false);

        $specified_amount_status = CommonRequest::get($request, 'specified_amount_status');
        if(strlen($specified_amount_status) > 0  && !in_array($specified_amount_status, [0, '-1']))  Tool::appendCondition($queryParams, 'specified_amount_status', $specified_amount_status . '=' . $specified_amount_status, '&', 'where', 1, false, PaymentProject::SPECIFIED_AMOUNT_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $pay_valid_status = CommonRequest::get($request, 'pay_valid_status');
        if(strlen($pay_valid_status) > 0  && !in_array($pay_valid_status, [0, '-1']))  Tool::appendCondition($queryParams, 'pay_valid_status', $pay_valid_status . '=' . $pay_valid_status, '&', 'where', 1, false, PaymentProject::PAY_VALID_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $valid_limit = CommonRequest::get($request, 'valid_limit');
        if(strlen($valid_limit) > 0  && !in_array($valid_limit, [0, '-1']))  Tool::appendCondition($queryParams, 'valid_limit', $valid_limit . '=' . $valid_limit, '&', 'where', 1, false, PaymentProject::VALID_LIMIT_ARR); // --逻辑 & 操作，且有数组范围

        $unique_user_standard = CommonRequest::get($request, 'unique_user_standard');
        if(strlen($unique_user_standard) > 0  && !in_array($unique_user_standard, [0, '-1']))  Tool::appendCondition($queryParams, 'unique_user_standard', $unique_user_standard . '=' . $unique_user_standard, '&', 'where', 1, false, PaymentProject::UNIQUE_USER_STANDARD_ARR); // --逻辑 & 操作，且有数组范围

        $open_status = CommonRequest::get($request, 'open_status');
        if(strlen($open_status) > 0  && !in_array($open_status, [0, '-1']))  Tool::appendCondition($queryParams, 'open_status', $open_status . '=' . $open_status, '&', 'where', 1, false, PaymentProject::OPEN_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $pay_status = CommonRequest::get($request, 'pay_status');
        if(strlen($pay_status) > 0  && !in_array($pay_status, [0, '-1']))  Tool::appendCondition($queryParams, 'pay_status', $pay_status . '=' . $pay_status, '&', 'where', 1, false, PaymentProject::PAY_STATUS_ARR); // --逻辑 & 操作，且有数组范围

        $handle_method = CommonRequest::get($request, 'handle_method');
        if(strlen($handle_method) > 0  && !in_array($handle_method, [0, '-1']))  Tool::appendCondition($queryParams, 'handle_method', $handle_method . '=' . $handle_method, '&', 'where', 1, false, PaymentProject::HANDLE_METHOD_ARR); // --逻辑 & 操作，且有数组范围

        $pay_method = CommonRequest::get($request, 'pay_method');
        if(strlen($pay_method) > 0  && !in_array($pay_method, [0, '-1']))  Tool::appendCondition($queryParams, 'pay_method',  $pay_method . '=' . $pay_method, '&');

        $pay_config_id = CommonRequest::get($request, 'pay_config_id');
        if(strlen($pay_config_id) > 0 && !in_array($pay_config_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $pay_config_id, 'pay_config_id', [0, '0', ''], ',', false);

        $invoice_template_id = CommonRequest::get($request, 'invoice_template_id');
        if(strlen($invoice_template_id) > 0 && !in_array($invoice_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id, 'invoice_template_id', [0, '0', ''], ',', false);

        $invoice_project_template_id = CommonRequest::get($request, 'invoice_project_template_id');
        if(strlen($invoice_project_template_id) > 0 && !in_array($invoice_project_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_project_template_id, 'invoice_project_template_id', [0, '0', ''], ',', false);

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

        // 组织支付方式文字【去掉非上线的支付方式】
        if(in_array('initPayMethodText', $handleKeyArr)){
            $disablePayMethod = [];
            $payKVList = [];
            OrderPayConfigDBBusiness::formatConfigPayMethodAppendMethodText($data_list, $disablePayMethod, $payKVList, $returnFields);
        }

        // 对外显示时，批量价格字段【整数转为小数】
        if(in_array('priceIntToFloat', $handleKeyArr)){
            Tool::bathPriceCutFloatInt($data_list, PaymentProject::$IntPriceFields, PaymentProject::$IntPriceIndex, 2, 2);
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
        if(in_array('sysFormatFieldsData', $finalHandleKeyArr)){
            foreach($data_list as $k => &$v){
                $project_fields = $v['project_fields'] ?? [];
                $field_name_arr = Tool::getArrFields($project_fields, 'field_name');
                $v['project_fields_names'] = implode('、', $field_name_arr);
            }
            $returnFields['project_fields_names'] = 'project_fields_names';
        }

        return $data_list;
    }

    /**
     * 修改或修试题
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $ower_type 操作类型 0：大后台 ； >0 所属的用户
     * @return mixed  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceInfo(Request $request, Controller $controller, $ower_type = 0){

        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $company_id = ($ower_type == 0) ? CommonRequest::getInt($request, 'company_id') : $controller->user_id;
        $type_no = CommonRequest::get($request, 'type_no');
        $title = CommonRequest::get($request, 'title');
        $pay_explain = CommonRequest::get($request, 'pay_explain');
        $pay_explain = stripslashes($pay_explain);
        $specified_amount_status = CommonRequest::getInt($request, 'specified_amount_status');
        $pay_amount = CommonRequest::get($request, 'pay_amount');
        if($specified_amount_status != PaymentProject::SPECIFIED_AMOUNT_STATUS_FIXED)  $pay_amount = 0;
        $pay_valid_status = CommonRequest::getInt($request, 'pay_valid_status');
        $pay_begin_time = CommonRequest::get($request, 'pay_begin_time');
        $pay_end_time = CommonRequest::get($request, 'pay_end_time');
        $pay_status = PaymentProject::PAY_STATUS_WAIT;// CommonRequest::getInt($request, 'pay_status');
        if($pay_valid_status == PaymentProject::PAY_VALID_STATUS_FIXED){
            // 判断开始结束日期
            // Tool::judgeBeginEndDate($pay_begin_time, $pay_end_time, 1 + 2 + 16 + 128 + 256 + 512, 1, date('Y-m-d H:i:s'), '报名时间');
            Tool::judgeBeginEndDate($pay_begin_time, $pay_end_time, 1 + 2 + 256 + 512, 1, date('Y-m-d H:i:s'), '收费起止时间');
        }else{
            $pay_status = PaymentProject::PAY_STATUS_DOING;
            $pay_begin_time = null;
            $pay_end_time = null;
        }
        $pay_limit_year = CommonRequest::getInt($request, 'pay_limit_year');
        $pay_limit_day = CommonRequest::getInt($request, 'pay_limit_day');
        $pay_limit_hour = CommonRequest::getInt($request, 'pay_limit_hour');
        $pay_limit_minute = CommonRequest::getInt($request, 'pay_limit_minute');
        $pay_limit_second = CommonRequest::getInt($request, 'pay_limit_second');
        $pay_valid_second = $pay_limit_year * 365 * 24 * 60 * 60 + $pay_limit_day * 24 * 60 * 60 + $pay_limit_hour * 60 * 60 + $pay_limit_minute * 60 + $pay_limit_second;
        $valid_limit = CommonRequest::getInt($request, 'valid_limit');
        $limit_year = CommonRequest::getInt($request, 'limit_year');
        $limit_day = CommonRequest::getInt($request, 'limit_day');
        $limit_hour = CommonRequest::getInt($request, 'limit_hour');
        $limit_minute = CommonRequest::getInt($request, 'limit_minute');
        $limit_second = CommonRequest::getInt($request, 'limit_second');
        if($valid_limit != PaymentProject::VALID_LIMIT_FIXED){
            $limit_year = $limit_day = $limit_hour = $limit_minute = $limit_second = 0;
        }
        $valid_limit_second = $limit_year * 365 * 24 * 60 * 60 + $limit_day * 24 * 60 * 60 + $limit_hour * 60 * 60 + $limit_minute * 60 + $limit_second;

        $unique_user_standard = CommonRequest::getInt($request, 'unique_user_standard');
        $open_status = CommonRequest::getInt($request, 'open_status');
        $handle_method = CommonRequest::getInt($request, 'handle_method');
        $sort_num = CommonRequest::getInt($request, 'sort_num');
        $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');
        // 开通的付款方式
        $pay_method = CommonRequest::get($request, 'pay_method');
        // 如果是字符，则转为数组
        Tool::valToArrVal($pay_method);
        $sel_pay_method = Tool::bitJoinVal($pay_method);// 将位数组，
        $invoice_template_id = CommonRequest::getInt($request, 'invoice_template_id');
        $invoice_project_template_id = CommonRequest::getInt($request, 'invoice_project_template_id');

        // 如果是字符，则转为数组
        Tool::valToArrVal($type_no);
        $sel_type_no = Tool::bitJoinVal($type_no);// 将位数组，合并为一个数值

        // 文件资源
        // $resource_id = [];
        $resource_id = CommonRequest::get($request, 'resource_id');
        // 如果是字符，则转为数组
        Tool::valToArrVal($resource_id);

        // 再转为字符串
        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

        // 相关字段
        $fields_ids = CommonRequest::get($request, 'fields_id');// id值数组或 逗号分隔的多个id值
        Tool::valToArrVal($fields_ids, ',');// 不是数组，则转为数组
        $field_name = CommonRequest::get($request, 'field_name');
        Tool::valToArrVal($field_name, ',');// 不是数组，则转为数组
        $val_type = CommonRequest::get($request, 'val_type');
        Tool::valToArrVal($val_type, ',');// 不是数组，则转为数组
        $sel_items = CommonRequest::get($request, 'sel_items');
        Tool::valToArrVal($sel_items, ',');// 不是数组，则转为数组
        $required_status = CommonRequest::get($request, 'required_status');
        Tool::valToArrVal($required_status, ',');// 不是数组，则转为数组
        $input_status = CommonRequest::get($request, 'input_status');
        Tool::valToArrVal($input_status, ',');// 不是数组，则转为数组
        $show_status = CommonRequest::get($request, 'show_status');
        Tool::valToArrVal($show_status, ',');// 不是数组，则转为数组


        $fieldList = [];
        $max_sort_num = count($fields_ids);
        foreach($fields_ids as $k => $field_id ){
            $temSelItems = $sel_items[$k] ?? '';
            $temSelItems = replace_enter_char($temSelItems, 1);
            $temValType = $val_type[$k] ?? '';

            // 去掉空行
            $temItemArr = explode('<br/>', $temSelItems);
            $temSelItems = implode('<br/>', Tool::arrClsEmpty($temItemArr));

            // 非选项的，清空选项
            if(!in_array($temValType, [PaymentProjectFields::VAL_TYPE_RADIO, PaymentProjectFields::VAL_TYPE_CHECKBOX])) $temSelItems = '';

            $temInputStatus = $input_status[$k] ?? '';
            // 对数组或逗号分隔的进行处理
            // 如果是字符，则转为数组
            Tool::valToArrVal($temInputStatus);
            Tool::bitArrToInt($temInputStatus);//  将位数组，合并为一个数值
            $temShowStatus = $show_status[$k] ?? '';
            // 对数组或逗号分隔的进行处理
            // 如果是字符，则转为数组
            Tool::valToArrVal($temShowStatus);
            Tool::bitArrToInt($temShowStatus);//  将位数组，合并为一个数值
            $temField = [
                'id' => $field_id,
                'company_id' => $company_id,
                'field_name' => $field_name[$k] ?? '',
                'val_type' => $temValType,
                'required_status' => $required_status[$k] ?? '',
                'input_status' => $temInputStatus,
                'show_status' => $temShowStatus,
                'sel_items' => $temSelItems,
                'sort_num' => $max_sort_num,
            ];
            array_push($fieldList, $temField);
            $max_sort_num--;
        }

        $saveData = [
            'company_id' => $company_id,
            'type_no' => $sel_type_no,
            'title' => $title,
            'pay_explain' => $pay_explain,
            'specified_amount_status' => $specified_amount_status,
            'pay_amount' => $pay_amount,
            'pay_valid_status' => $pay_valid_status,
            'pay_begin_time' => $pay_begin_time,
            'pay_end_time' => $pay_end_time,
            'pay_limit_year' => $pay_limit_year,
            'pay_limit_day' => $pay_limit_day,
            'pay_limit_hour' => $pay_limit_hour,
            'pay_limit_minute' => $pay_limit_minute,
            'pay_limit_second' => $pay_limit_second,
            'pay_valid_second' => $pay_valid_second,
            'valid_limit' => $valid_limit,
            'limit_year' => $limit_year,
            'limit_day' => $limit_day,
            'limit_hour' => $limit_hour,
            'limit_minute' => $limit_minute,
            'limit_second' => $limit_second,
            'valid_limit_second' => $valid_limit_second,
            'unique_user_standard' => $unique_user_standard,
            'open_status' => $open_status,
            'pay_status' => $pay_status,
            'handle_method' => $handle_method,
            'sort_num' => $sort_num,
            'pay_config_id' => $pay_config_id,
            'pay_method' => $sel_pay_method,
            'invoice_template_id' => $invoice_template_id,
            'invoice_project_template_id' => $invoice_project_template_id,
            'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
            'field_list' => $fieldList, // 收集相关字段
        ];
        // 价格转为整型
        Tool::bathPriceCutFloatInt($saveData, PaymentProject::$IntPriceFields, PaymentProject::$IntPriceIndex, 1);

        if($id <= 0) {// 新加;要加入的特别字段
//                    $addNewData = [
//                        // 'account_password' => $account_password,
//                    ];
//                    $saveData = array_merge($saveData, $addNewData);
        }else{
            $info = CTAPIPaymentProjectBusiness::getInfoData($request, $controller, $id, [], '', []);
            if(empty($info)) throws('记录不存在');
            if($ower_type > 0 && $company_id != $info['company_id']) throws('您没有此记录的操作权限');
            // 如果改变了所属企业,需要重新统计数
            if(isset($saveData['company_id']) && $company_id != $info['company_id']) $saveData['force_company_num'] = 1;
        }

        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIPaymentProjectBusiness::replaceById($request, $controller, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 根据课程id信息，获得课程及课程支付配置信息
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array / string $payment_project_ids  课程id 一维数组，或 字符 --多个逗号分隔
     * @return array  以课程id为下标的二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getPaymentProjectPayList(Request $request, Controller $controller, $payment_project_ids = []){
        Tool::formatOneArrVals($payment_project_ids);// 去掉 0
        $formatList = [];// 以课程id为下标的二维数组
        if(!empty($payment_project_ids)){
            $handleKeyConfigArr = [];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> static::getRelationConfigs($request, $controller, $handleKeyConfigArr, []),
                // 'infoHandleKeyArr' => ['resetPayMethod']
                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat'],
            ];
            $dataList = static::getFVFormatList( $request,  $controller, 1, 1
                , ['id' => $payment_project_ids], false, [], $extParams);

            $formatList = Tool::arrUnderReset($dataList, 'id', 1, '_');
        }
        // pr($formatList);
        return $formatList;
    }
}
