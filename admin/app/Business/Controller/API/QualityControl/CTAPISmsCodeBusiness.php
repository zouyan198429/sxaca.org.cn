<?php
// 老师登录验证码
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\SmsCode;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPISmsCodeBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\SmsCodeAPI';
    public static $table_name = 'sms_code';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

    /**
     * 获得列表数据时，查询条件的参数拼接--有特殊的需要自己重写此方法
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
        $staff_id = CommonRequest::getInt($request, 'staff_id');
        if(is_numeric($staff_id) && $staff_id > 0)  array_push($queryParams['where'], ['staff_id', '=', $staff_id]);

        $sms_type = CommonRequest::getInt($request, 'sms_type');
        if(is_numeric($sms_type) && $sms_type > 0 )  array_push($queryParams['where'], ['sms_type', '=', $sms_type]);

        $sms_status = CommonRequest::getInt($request, 'sms_status');
        // if(is_numeric($sms_status) && $sms_status > 0 )  array_push($queryParams['where'], ['sms_status', '=', $sms_status]);
        if(strlen($sms_status) > 0 &&  !in_array($sms_status, [0, '-1']))  Tool::appendCondition($queryParams, 'sms_status',  $sms_status . '=' . $sms_status, '&');


        $template_type = CommonRequest::get($request, 'template_type');
        if(strlen($template_type) > 0 && !in_array($template_type, [0, '-1']))  Tool::appendCondition($queryParams, 'template_type',  $template_type . '=' . $template_type, '&');

        $send_type = CommonRequest::get($request, 'send_type');
        if(strlen($send_type) > 0 && !in_array($send_type, [0, '-1']))  Tool::appendCondition($queryParams, 'send_type',  $send_type . '=' . $send_type, '&');

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 格式化数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param boolean 原数据类型 true:二维[默认];false:一维
     * @return  boolean true
     * @author zouyan(305463219@qq.com)
     */
//    public static function handleDataFormat(Request $request, Controller $controller, &$data_list, $handleKeyArr, $isMulti = true){
//
//        // 重写开始
//
//        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记
//
//        $staffArr = [];
//
//        //        if(!empty($data_list) ){
//        // 获得所属老师
//        if(in_array('staff', $handleKeyArr)){
//            $staffIdArr = array_values(array_filter(array_column($data_list,'staff_id')));// 资源id数组，并去掉值为0的
//            // 主键为下标的二维数组
//            if(!empty($staffIdArr)) $staffArr = Tool::arrUnderReset(CTAPIStaffBusiness::getListByIds($request, $controller, $staffIdArr), 'id', 1);
//            if(!$isNeedHandle && !empty($staffArr)) $isNeedHandle = true;
//        }
//
//        //        }
//
//        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
////        if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
////            return true;
////        }
//
//        foreach($data_list as $k => $v){
//            //            // 公司名称
//            //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//
//            // 获得所属老师
//            if(in_array('staff', $handleKeyArr)){
//                $data_list[$k]['staff_info'] = $staffArr[$v['staff_id']] ?? [];
//            }
//        }
//
//        // 重写结束
//        return true;
//    }

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
            'staff_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['staff_id' => 'id']
                , 1, 8 | 4
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'staff_info'),
                    static::getUboundRelationExtendParams($extendParams, 'staff_info')),
                static::getRelationSqlParams([], $extendParams, 'staff_info'), '', ['extendConfig' => ['listHandleKeyArr' => ['realNameOrCompanName']]]),
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


}
