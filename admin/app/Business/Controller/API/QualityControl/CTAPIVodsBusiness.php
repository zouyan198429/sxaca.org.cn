<?php
// 点播课程
namespace App\Business\Controller\API\QualityControl;

use App\Business\DB\QualityControl\OrderPayConfigDBBusiness;
use App\Models\QualityControl\Vods;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIVodsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\VodsAPI';
    public static $table_name = 'vods';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    public static $orderBy = Vods::ORDER_BY;// ['recommend_status' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']


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

            // 获得分类名称
            'type_name' => CTAPIVodTypeBusiness::getTableRelationConfigInfo($request, $controller
                , ['vod_type_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIVodTypeBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'type_name'),
                    static::getUboundRelationExtendParams($extendParams, 'type_name')),
                static::getRelationSqlParams([], $extendParams, 'type_name'), '', []),
            // 获得详细内容
            'vod_content' => CTAPIVodsContentBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'vod_id']
                , 1, 2
                ,'','',
                CTAPIVodsContentBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'vod_content'),
                    static::getUboundRelationExtendParams($extendParams, 'vod_content')),
                static::getRelationSqlParams([], $extendParams, 'vod_content'), '', []),
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

            // 目录及课件--开启的
            'vod_video' => CTAPIVodVideoBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'vod_id']
                , 2, 0
                ,'','',
                CTAPIVodVideoBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'vod_video'),
                    static::getUboundRelationExtendParams($extendParams, 'vod_video')),
                static::getRelationSqlParams(['where' => [['status_online', 1]], 'orderBy' => CTAPIVodVideoBusiness::$orderBy], $extendParams, 'vod_video'), ''
                , []),
            // 获得报名企业报名项-- 企业报名的
            'vod_orders' => CTAPIVodOrdersBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'vod_id']
                , 2, 2
                ,'','',
                CTAPIVodOrdersBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'vod_orders'),
                    static::getUboundRelationExtendParams($extendParams, 'vod_orders')),
//                ['where' => [
//                    ['admin_type', $user_info['admin_type']],
//                    ['company_id', $user_info['id']]
//                ]],
                static::getRelationSqlParams([], $extendParams, 'vod_orders')
                ,
                '', ['extendConfig' => ['infoHandleKeyArr' => ['judgeJoined']]]),
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

        if(($return_num & 2) == 2){// 给上一级返回名称 company_name 下标
            $one_field = ['key' => 'vod_name', 'return_type' => 2, 'ubound_name' => 'vod_name', 'split' => '、'];// 获得名称
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

         $open_status = CommonRequest::get($request, 'open_status');
         if(strlen($open_status) > 0  && !in_array($open_status, [0, '-1']))  Tool::appendCondition($queryParams, 'open_status',  $open_status . '=' . $open_status, '&');

        $recommend_status = CommonRequest::get($request, 'recommend_status');
        if(strlen($recommend_status) > 0  && !in_array($recommend_status, [0, '-1']))  Tool::appendCondition($queryParams, 'recommend_status',  $recommend_status . '=' . $recommend_status, '&');

        $pay_method = CommonRequest::get($request, 'pay_method');
        if(strlen($pay_method) > 0  && !in_array($pay_method, [0, '-1']))  Tool::appendCondition($queryParams, 'pay_method',  $pay_method . '=' . $pay_method, '&');

         $vod_type_id = CommonRequest::get($request, 'vod_type_id');
         if(strlen($vod_type_id) > 0 && !in_array($vod_type_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $vod_type_id, 'vod_type_id', [0, '0', ''], ',', false);

        $resource_id = CommonRequest::get($request, 'resource_id');
        if(strlen($resource_id) > 0 && !in_array($resource_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $resource_id, 'resource_id', [0, '0', ''], ',', false);

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
            Tool::bathPriceCutFloatInt($data_list, Vods::$IntPriceFields, Vods::$IntPriceIndex, 2, 2);
        }

        // 重写结束
        return $returnFields;
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
//        if(in_array('resetPayMethod', $infoHandleKeyArr)){
//
//            // 支付方式-实时处理
//            OrderPayConfig::unionPayMethod($info, 'pay_method','pay_config_id');
//            OrderPayConfig::getPayMethodText($info, 'pay_method');
////            $info['resource_list'] = $resource_list;
////            $returnFields['resource_list'] = 'resource_list';
//        }

        return $returnFields;
    }

    /**
     * 根据课程id信息，获得课程及课程支付配置信息
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array / string $course_ids  课程id 一维数组，或 字符 --多个逗号分隔
     * @return array  以课程id为下标的二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getCoursePayList(Request $request, Controller $controller, $course_ids = []){
        Tool::formatOneArrVals($course_ids);// 去掉 0
        $courseFormatList = [];// 以课程id为下标的二维数组
        if(!empty($course_ids)){
            $handleKeyConfigArr = [];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> static::getRelationConfigs($request, $controller, $handleKeyConfigArr, []),
                // 'infoHandleKeyArr' => ['resetPayMethod']
                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat'],
            ];
            $courseList = static::getFVFormatList( $request,  $controller, 1, 1
                , ['id' => $course_ids], false, [], $extParams);

            $courseFormatList = Tool::arrUnderReset($courseList, 'id', 1, '_');
        }
        // pr($courseFormatList);
        return $courseFormatList;
    }

    /**
     * 获得企业数据验证
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $reDataArr 可以传到前端的参数
     * @param string $url_path url地址的路径目录 如：jigou/list/
     * @param int $vod_type_id 所属分类 id
     * @param int $recommend_status 是否推荐1非推荐2推荐
     * @param int $pagesize 每页显示 的数量
     * @param int $page 当前页号
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array $keyArr 关键字数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getVodsList(Request $request, Controller $controller, &$reDataArr, $url_path = 'jigou/list/', $vod_type_id = 0, $recommend_status = 0, $pagesize = 20, $page = 1, $notLog = 0){

        $field = CommonRequest::get($request, 'field');
        $keyword = CommonRequest::get($request, 'keyword');
//        $company_grade = CommonRequest::get($request, 'company_grade');
        if(is_numeric($pagesize) && $pagesize >= 100) $pagesize = 100;// 限止最多每面100条记录
        if(is_numeric($pagesize) && $pagesize <= 0) $pagesize = 20;
        $pathParamStr = $vod_type_id . '_' . $recommend_status . '_' . $pagesize . '_{page}';// . $page;
        if($field != '' && $keyword != '') $pathParamStr .= '?field=' . $field . '&keyword=' . $keyword;

        // 加上会员类型参数
//        if(!empty($company_grade)) $pathParamStr .= ((strpos($pathParamStr, '?') === false) ? '?' : '&') . 'company_grade=' . $company_grade;

        $appParams = [
            'vod_type_id' => $vod_type_id,
            'recommend_status' => $recommend_status,
            'pagesize' => $pagesize,
            'page' => $page,
            // 'url_model' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/web/certificate/company/' . $pathParamStr,
            // 'url_model' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/jigou/list/' . $pathParamStr,
            'url_model' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/'. $url_path . $pathParamStr,
//            'admin_type' => 2,
//            'is_perfect' => 2,
            'open_status' => 1,
//            'account_status' => 1
        ];
        static::mergeRequest($request, $controller, $appParams);
        $reDataArr = array_merge($reDataArr, $appParams);
        $keyArr = [];
        $reDataArr['pagesize'] = $pagesize;
        $reDataArr['page'] = $page;
        $reDataArr['field'] = $field;
        $reDataArr['keyword'] = $keyword;


        // 获得分类名称
        if(is_numeric($vod_type_id) && $vod_type_id > 0){
            $cityInfo = CTAPIVodTypeBusiness::getInfoData($request, $controller, $vod_type_id, [], '', []);
            $type_name = $cityInfo['type_name'] ?? '';
            array_push($keyArr, $type_name);
        }

        // 获得是否推荐1非推荐2推荐 名称
        if(is_numeric($recommend_status) && $recommend_status > 0){
            $recommendStatusArr = Vods::$recommendStatusArr;
            $recommend_status_name = $recommendStatusArr[$recommend_status] ?? '';
            if(!empty($recommend_status_name)) array_push($keyArr, $recommend_status_name);
        }

        // 获得会员等级
//        $company_grade_name = '';
//        if(!empty($company_grade)){
//            $companyGradeArr = Staff::$companyGradeArr;
//            $company_grade_name = $companyGradeArr[$company_grade] ?? '';
//            if(!empty($company_grade_name)) array_push($keyArr, $company_grade_name);
//        }else{
//            $company_grade_name = '所有';
//        }
//        $reDataArr['company_grade_name'] = $company_grade_name;

        if($field != '' && $keyword != '') array_push($keyArr, $keyword);

        $extParamsCompany = [
//            'sqlParams'=> [
//                'orderBy' => ['id' => 'asc']
//            ]
        ];
        $extParams = array_merge($extParamsCompany,[
            // , 'vod_content' => ''
            // , 'invoice_template_name' => '', 'invoice_project_template_name' => ''
            'relationFormatConfigs'=> static::getRelationConfigs($request, $controller, ['resource_list' => '', 'type_name' =>''], []),
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            // 'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $controller, ['industry_info', 'city_info'], []),// , 'extend_info'
        ]);
        $data_arr = static::getList($request, $controller, 2 + 8, [], [], $extParams)['result'] ?? [];
        $reDataArr['data_list'] = $data_arr['data_list'] ?? [];
        $reDataArr['pageInfoLink'] = $data_arr['pageInfoLink'] ?? '';
        return $keyArr;
    }
}
