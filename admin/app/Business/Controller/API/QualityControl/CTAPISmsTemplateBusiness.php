<?php
// 短信模板
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPISmsTemplateBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\SmsTemplateAPI';
    public static $table_name = 'sms_template';// 表名称
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
            // 获得所属模块名称
            'sms_module' => CTAPISmsModuleBusiness::getTableRelationConfigInfo($request, $controller
                , ['module_id' => 'id']
                , 1, 2
                ,'','',
                CTAPISmsModuleBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'sms_module'),
                    static::getUboundRelationExtendParams($extendParams, 'sms_module')),
                static::getRelationSqlParams([], $extendParams, 'sms_module'), '', []),
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

        if(($return_num & 2) == 2){// 给上一级返回名称 template_name 下标
            $one_field = ['key' => 'template_name', 'return_type' => 2, 'ubound_name' => 'template_name', 'split' => '、'];// 获得名称
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

        $template_type = CommonRequest::get($request, 'template_type');
        if(strlen($template_type) > 0 && $template_type != 0)  Tool::appendCondition($queryParams, 'template_type',  $template_type . '=' . $template_type, '&');

        $module_id = CommonRequest::get($request, 'module_id');
        if(strlen($module_id) > 0 && !in_array($module_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $module_id, 'module_id', [0, '0', ''], ',', false);

        $limit_code = CommonRequest::get($request, 'limit_code');
        if(strlen($limit_code) > 0 && $limit_code != 0)  Tool::appendCondition($queryParams, 'limit_code',  $limit_code . '=' . $limit_code, '&');

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }


    /**
     * 短信发送页面组织数据， 可以用请求参数 template_id：短信模板id; 或 module_id：短信模块id
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $reDataArr 需要传递到前端视图中的数据
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function smsSend(Request $request, Controller $controller, &$reDataArr, $notLog = 0){
        $ids = CommonRequest::get($request, 'ids');// 记录id,多个用逗号分隔--适用于 多选 和 指定 某一个记录操作
        $reDataArr['ids'] = $ids;
        $sms_operate_no = CommonRequest::getInt($request, 'sms_operate_no');// 操作来源： 1、按条件[id 传 0]；2：选中的 id,多个用逗号分隔； 4：单条记录的[id 传 对应的id]
        $reDataArr['sms_operate_no'] = $sms_operate_no;

        $sms_template_id = CommonRequest::getInt($request, 'sms_template_id');
        // 如果有具体的短信模板id，则先获得指定的短信模板信
        if($sms_template_id > 0){
            // 获得短信模板详情
            $templateInfo = CTAPISmsTemplateBusiness::getInfoData($request, $controller, $sms_template_id, [], '', []);
            if(empty($templateInfo)) throws('短信模板【' . $sms_template_id . '】记录不存在！');
            $template_name = $templateInfo['template_name'] ?? '';
            if($templateInfo['open_status'] != 1) throws('短信模板【' . $template_name . '】非启用状态，不可发送短信！');
            $sms_module_id = $templateInfo['module_id'] ?? 0;
        }else{
            $sms_module_id = CommonRequest::getInt($request, 'sms_module_id');// 1;
        }

        $reDataArr['defaultSmsTemplateId'] = $sms_template_id;



        // 获得常用参数管理
        $sms_template_list = CTAPISmsTemplateBusiness::getFVFormatList( $request,  $controller, 1, 1
            , ['module_id' => $sms_module_id, 'open_status' => 1], false, [], []);
        // $reDataArr['sms_template_list'] = $sms_template_list;

        // 获得常用参数管理
        $sms_params_common_list = CTAPISmsModuleParamsCommonBusiness::getFVFormatList( $request,  $controller, 1, 1
            , [], true, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]]);
        $reDataArr['sms_params_common_list'] = $sms_params_common_list;

        // 获得当前模块的参数
        // $sms_module_id = $info['module_id'] ?? -1;
        $sms_params_list = [];
        if($sms_module_id > 0){
            $sms_params_list = CTAPISmsModuleParamsBusiness::getFVFormatList( $request,  $controller, 1, 1
                , ['module_id' => $sms_module_id], false, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]]);
        }

        $reDataArr['sms_params_list'] = $sms_params_list;

        // 对参数进行处理，模块参数优先常用参数
        $params_list = [];
        $smsParamsFormatList = [];// 参数代码为下标的参数数组
        if(!empty($sms_params_common_list)) {
            $smsCommonParamsFormatList = Tool::arrUnderReset($sms_params_common_list, 'param_code', 1, '_');// 转为参数代码为下标的数组
            $smsParamsFormatList = array_merge($smsParamsFormatList, $smsCommonParamsFormatList);
        }
        if(!empty($sms_params_list)){
            $smsModuleParamsFormatList = Tool::arrUnderReset($sms_params_list, 'param_code', 1, '_');// 转为参数代码为下标的数组
            $smsParamsFormatList = array_merge($smsParamsFormatList, $smsModuleParamsFormatList);
        }

        $reDataArr['all_params_list'] = $smsParamsFormatList;// 最终的可用的参数

        foreach($sms_template_list as &$v){
            $template_content = $v['template_content'] ?? '';
            // 获得模板内容参数
            $paramsArr = Tool::getLabelArr($template_content, '{', '}');
            $temArr = $smsParamsFormatList;
            $needParamsList = Tool::formatArrByKeys($temArr, $paramsArr, false);// 获得指定下标的参数
            $v['params_list'] = array_values($needParamsList);
        }
        $reDataArr['sms_template_list'] = $sms_template_list;

    }

}
