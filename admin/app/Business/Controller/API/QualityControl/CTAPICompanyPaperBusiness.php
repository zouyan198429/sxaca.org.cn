<?php
// 试卷
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\CompanyPaper;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPICompanyPaperBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CompanyPaperAPI';
    public static $table_name = 'company_paper';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    public static $orderBy = CompanyPaper::ORDER_BY;// ['sort_num' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']


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
//        if(strlen($aaaa) > 0  && !in_array($aaaa, [0, '-1']))  Tool::appendCondition($queryParams, 'aaaa',  $aaaa . '=' . $aaaa, '&');

//        $bbbb = CommonRequest::get($request, 'bbbb');
//        if(strlen($bbbb) > 0 && !in_array($bbbb, [0, '-1']))  Tool::appendParamQuery($queryParams, $bbbb, 'bbbb', [0, '0', ''], ',', false);

        $type_no = CommonRequest::get($request, 'type_no');
        if(strlen($type_no) > 0  && !in_array($type_no, [0, '-1']))  Tool::appendCondition($queryParams, 'type_no',  $type_no . '=' . $type_no, '&');

        $subject_order_type = CommonRequest::get($request, 'subject_order_type');
        if(strlen($subject_order_type) > 0  && !in_array($subject_order_type, [0, '-1']))  Tool::appendCondition($queryParams, 'subject_order_type',  $subject_order_type . '=' . $subject_order_type, '&'
            , 'where', 1, false, CompanyPaper::SUBJECT_ORDER_TYPE_ARR);

        $has_block = CommonRequest::get($request, 'has_block');
        if(strlen($has_block) > 0  && !in_array($has_block, [0, '-1']))  Tool::appendCondition($queryParams, 'has_block',  $has_block . '=' . $has_block, '&'
            , 'where', 1, false, CompanyPaper::HAS_BLOCK_ARR);

        $subject_updated = CommonRequest::get($request, 'subject_updated');
        if(strlen($subject_updated) > 0  && !in_array($subject_updated, [0, '-1']))  Tool::appendCondition($queryParams, 'subject_updated',  $subject_updated . '=' . $subject_updated, '&'
            , 'where', 1, false, CompanyPaper::SUBJECT_UPDATED_ARR);

        $review_method = CommonRequest::get($request, 'review_method');
        if(strlen($review_method) > 0  && !in_array($review_method, [0, '-1']))  Tool::appendCondition($queryParams, 'review_method',  $review_method . '=' . $review_method, '&'
            , 'where', 1, false, CompanyPaper::REVIEW_METHOD_ARR);

        $open_status = CommonRequest::get($request, 'open_status');
        if(strlen($open_status) > 0  && !in_array($open_status, [0, '-1']))  Tool::appendCondition($queryParams, 'open_status',  $open_status . '=' . $open_status, '&'
            , 'where', 1, false, CompanyPaper::OPEN_STATUS_ARR);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

}
