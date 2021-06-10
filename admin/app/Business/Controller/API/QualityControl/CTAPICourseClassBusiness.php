<?php
//培训班管理
namespace App\Business\Controller\API\QualityControl;

use App\Business\DB\QualityControl\OrderPayConfigDBBusiness;
use App\Models\QualityControl\OrderPayConfig;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPICourseClassBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CourseClassAPI';
    public static $table_name = 'course_class';// 表名称
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
     * @param array $extendParams  扩展参数---可能会用；需要指定的实时特别的 条件配置
     *          格式： [
     *                    '关系下标' => [
     *                          'fieldValParams' => [ '字段名1' => '字段值--多个时，可以是一维数组或逗号分隔字符', ...],// 也可以时 Tool getParamQuery 方法的参数$fieldValParams的格式
     *                          'sqlParams' => []// 与参数 $sqlDefaultParams 相同格式的条件
     *                          '关系下标' => ... 下下级的
     *                       ]
     *                ]
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
            // 获得 行业名称
            'city_info' => CTAPICitysBusiness::getTableRelationConfigInfo($request, $controller
                , ['city_id' => 'id']
                , 1, 2
                ,'','',
                CTAPICitysBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'city_info'),
                    static::getUboundRelationExtendParams($extendParams, 'city_info')),
                static::getRelationSqlParams([], $extendParams, 'city_info'), '', []),
            // 获得课程名称
            'course_name' => CTAPICourseBusiness::getTableRelationConfigInfo($request, $controller
                , ['course_id' => 'id']
                , 1, 2
                ,'','',
                CTAPICourseBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_name'),
                    static::getUboundRelationExtendParams($extendParams, 'course_name')),
                static::getRelationSqlParams([], $extendParams, 'course_name'), '', []),
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

        if(($return_num & 2) == 2){// 给上一级返回名称 class_name 下标
            $one_field = ['key' => 'class_name', 'return_type' => 2, 'ubound_name' => 'class_name', 'split' => '、'];// 获得名称
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

        $course_id = CommonRequest::get($request, 'course_id');
        if(strlen($course_id) > 0 && $course_id != '-1')  Tool::appendParamQuery($queryParams, $course_id, 'course_id', [0, '0', ''], ',', false);

        $city_id = CommonRequest::getInt($request, 'city_id');
        if($city_id > 0 )  array_push($queryParams['where'], ['city_id', '=', $city_id]);

        $class_status = CommonRequest::get($request, 'class_status');
        if(strlen($class_status) > 0 && $class_status != 0)  Tool::appendParamQuery($queryParams, $class_status, 'class_status', [0, '0', ''], ',', false);

        $pay_config_id = CommonRequest::get($request, 'pay_config_id');
        if(strlen($pay_config_id) > 0 && $pay_config_id != 0)  Tool::appendParamQuery($queryParams, $pay_config_id, 'pay_config_id', [0, '0', ''], ',', false);

        $pay_method = CommonRequest::get($request, 'pay_method');
        if(strlen($pay_method) > 0 && $pay_method != 0)  Tool::appendCondition($queryParams, 'pay_method',  $pay_method . '=' . $pay_method, '&');

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);
          //  throws(json_encode($queryParams));
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
     * 根据班级id信息，获得班级及班级支付配置信息
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array / string $class_ids  班级id 一维数组，或 字符 --多个逗号分隔
     * @return array  以班级id为下标的二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getClassPayList(Request $request, Controller $controller, $class_ids = []){
        Tool::formatOneArrVals($class_ids);// 去掉 0
        $classFormatList = [];// 以班级id为下标的二维数组
        if(!empty($class_ids)){
            $handleKeyConfigArr = [];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICourseClassBusiness::getRelationConfigs($request, $controller, $handleKeyConfigArr, []),
                // 'infoHandleKeyArr' => ['resetPayMethod']
                'listHandleKeyArr' => ['initPayMethodText']
            ];
            $classList = CTAPICourseClassBusiness::getFVFormatList( $request,  $controller, 1, 1
                , ['id' => $class_ids], false, [], $extParams);

            $classFormatList = Tool::arrUnderReset($classList, 'id', 1, '_');
        }
        // pr($classFormatList);
        return $classFormatList;
    }


    /**
     * 开班 、 结业、作废 批量 或 单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string $id 记录id，多个用逗号分隔
     * @param int $operate_type 操作类型 1开班 、 2结业、4作废
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   //   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function operateStatusAjax(Request $request, Controller $controller, $organize_id = 0, $id = 0, $operate_type = 1, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'organize_id' => $organize_id,
            'id' => $id,
            'operate_type' => $operate_type,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $modifyNum = static::exeDBBusinessMethodCT($request, $controller, '',  'operateStatusById', $apiParams, $company_id, $notLog);

        return $modifyNum;
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }
}
