<?php
// 接口日志
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CTAPIApiLogBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\ApiLogAPI';
    public static $table_name = 'api_log';// 表名称
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
            // 获得详细内容
            'log_content' => CTAPIApiLogContentBusiness::getTableRelationConfigInfo($request, $controller
                , ['log_no' => 'log_no']
                , 1, 4
                ,'','',
                CTAPIApiLogContentBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'log_content'),
                    static::getUboundRelationExtendParams($extendParams, 'log_content')),
                static::getRelationSqlParams([], $extendParams, 'log_content'), '', []),
            // 获得应用信息
            'apply_info' => CTAPIApplyBusiness::getTableRelationConfigInfo($request, $controller
                , ['app_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIApplyBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'apply_info'),
                    static::getUboundRelationExtendParams($extendParams, 'apply_info')),
                static::getRelationSqlParams([], $extendParams, 'apply_info'), '', []),
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

        $log_no = CommonRequest::get($request, 'log_no');
        if(strlen($log_no) > 0 && $log_no != 0)  Tool::appendParamQuery($queryParams, $log_no, 'log_no', [0, '0', ''], ',', false);

        $app_id = CommonRequest::get($request, 'app_id');
        if(is_numeric($app_id) > 0 && $app_id > 0)  Tool::appendParamQuery($queryParams, $app_id, 'app_id', [0, '0', ''], ',', false);

        $app_id_history = CommonRequest::get($request, 'app_id_history');
        if(is_numeric($app_id_history) > 0 && $app_id_history > 0)  Tool::appendParamQuery($queryParams, $app_id_history, 'app_id_history', [0, '0', ''], ',', false);

        $system_no = CommonRequest::get($request, 'system_no');
        if(strlen($system_no) > 0 && $system_no != 0)  Tool::appendParamQuery($queryParams, $system_no, 'system_no', [0, '0', ''], ',', false);

        $model_no = CommonRequest::get($request, 'model_no');
        if(strlen($model_no) > 0 && $model_no != 0)  Tool::appendParamQuery($queryParams, $model_no, 'model_no', [0, '0', ''], ',', false);

        $fun_no = CommonRequest::get($request, 'fun_no');
        if(strlen($fun_no) > 0 && $fun_no != 0)  Tool::appendParamQuery($queryParams, $fun_no, 'fun_no', [0, '0', ''], ',', false);

        $exe_year = CommonRequest::get($request, 'exe_year');
        if(strlen($exe_year) > 0 && $exe_year != 0)  Tool::appendParamQuery($queryParams, $exe_year, 'exe_year', [0, '0', ''], ',', false);

        $exe_month = CommonRequest::get($request, 'exe_month');
        if(strlen($exe_month) > 0 && $exe_month != 0)  Tool::appendParamQuery($queryParams, $exe_month, 'exe_month', [0, '0', ''], ',', false);

        $exe_day = CommonRequest::get($request, 'exe_day');
        if(strlen($exe_day) > 0 && $exe_day != 0)  Tool::appendParamQuery($queryParams, $exe_day, 'exe_day', [0, '0', ''], ',', false);

        $response_level = CommonRequest::get($request, 'response_level');
        if(is_numeric($response_level) > 0 && $response_level > 0)  Tool::appendParamQuery($queryParams, $response_level, 'response_level', [0, '0', ''], ',', false);

        $do_status = CommonRequest::get($request, 'do_status');
        if(is_numeric($do_status) > 0 && $do_status > 0)  Tool::appendParamQuery($queryParams, $do_status, 'do_status', [0, '0', ''], ',', false);

        $operate_staff_id = CommonRequest::get($request, 'operate_staff_id');
        if(is_numeric($operate_staff_id) > 0 && $operate_staff_id > 0)  Tool::appendParamQuery($queryParams, $operate_staff_id, 'operate_staff_id', [0, '0', ''], ',', false);

        $operate_staff_id_history = CommonRequest::get($request, 'operate_staff_id_history');
        if(is_numeric($operate_staff_id_history) > 0 && $operate_staff_id_history > 0)  Tool::appendParamQuery($queryParams, $operate_staff_id_history, 'operate_staff_id_history', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 执行api内容，并写api日志
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pageNum 方法的权限编号
     * @param mixed $doFun 需要统计执行时间 的闭包函数  function(){}
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function exeApi(Request $request, Controller $controller, $pageNum, $doFun, $notLog = 0){
        $result = null;
        $logId = 0;// 日志记录id
        $do_status = 2;// 执行状态
        $errStr = '';// 错误信息
        $startTime = microtime(true);// 当前 Unix 时间戳的微秒数
        try{
            // 日志
            $requestLog = [
                'files'       => $request->file(),
                'posts'  => $request->post(),//获取 POST 请求参数值, 从 request 属性对象中获取参数值
                // 'input'      => $request->input(),//  获取所有 HTTP 请求参数值, 从 query + request属性对象中获取参数值
                'query' => $request->query(),// 获取 GET 请求查询字符串参数值, 从 query 属性对象中获取参数值
            ];
            Log::info('API日志--请求参数：',$requestLog);
            // 记录日志及开始执行时间
            $log_no = static::createSn( $request,  $controller, 9);
            // $appid = CommonRequest::get($request, 'appid') ?? 0;
            // if(!is_numeric($appid)) $appid = 0;
            $appInfo = $controller->getApplyInfo($request, '', 1);
            $appid = $appInfo['id'] ?? 0;
            $currentNow = Carbon::now();
            $apiLogInfo = [
                'log_no' => $log_no,// 接口执行编号
                'action_method' => Tool::getActionMethod(),// 执行控制器@方法
                'app_id' => $appid,// 调起应用id
                // 'app_id_history' => $sssss,// 调起应用历史id
                'system_no' => $controller->project_id,// 系统编号
                'model_no' => $controller->controller_id ?? '',// 模块编号
                'fun_no' => $pageNum,// 功能编号
                'begin_time' => date('Y-m-d H:i:s'),// 执行时间
                'exe_year' => $currentNow->year,// 年
                'exe_month' => $currentNow->month,// 月
                'exe_day' => $currentNow->day,// 日
                // 'end_time' => date('Y-m-d H:i:s'),// 结束时间
                // 'exe_time' => $sssss,// 执行时长[毫秒]
                'response_level' => 128,// 执行响应等级(1超光速-半秒; 2 光速-1秒；4极速-2秒；8普速-4秒；16慢速-6秒；32 超慢速-8秒；64 龟速；128等定 )
                'do_status' => 1,// 状态1执行中 2成功4失败
                'log_content' => [
                    'log_no' => $log_no,// 接口执行编号
                    'request_content' => json_encode($requestLog),// 请求参数内容
                    // 'results_content' => $sssss,// 执行结果
                ]
            ];

            $extParams = [
                'judgeDataKey' => 'replace',// 数据验证的下标
            ];
            static::replaceById($request, $controller, $apiLogInfo, $logId, $extParams, true);

            if(is_callable($doFun)){
                $result = $doFun();
            }
            // 记录成功内容
            Log::info('API日志--执行成功：', [$result]);

        } catch ( \Exception $e) {
            $errStr = $e->getMessage();
            $errCode = $e->getCode();
            // 记录失败内容
            $do_status = 4;
            Log::info('API日志--执行失败：',[$errStr, $errCode]);
            throws($errStr, $errCode);
        }finally {
            if($logId > 0){
                // 记录完成时间
                $endTime = microtime(true);// 当前 Unix 时间戳的微秒数
                $doTime = $endTime - $startTime ;
                $response_level = 64;
                $millisecond = ceil($doTime * 1000);// 转为毫秒
                if($millisecond <= 500 ){
                    $response_level = 1;
                }else if($millisecond <= 1000 ){
                    $response_level = 2;
                }else if($millisecond <= 2000 ){
                    $response_level = 4;
                }else if($millisecond <= 4000 ){
                    $response_level = 8;
                }else if($millisecond <= 6000 ){
                    $response_level = 16;
                }else if($millisecond <= 8000 ){
                    $response_level = 32;
                }
                $apiLogInfo = [
                    'end_time' => date('Y-m-d H:i:s'),// 结束时间
                    'exe_time' => $millisecond,// 执行时长[毫秒]
                    'response_level' => $response_level,// 执行响应等级(1超光速-半秒; 2 光速-1秒；4极速-2秒；8普速-4秒；16慢速-6秒；32 超慢速-8秒；64 龟速；128等定 )
                    'do_status' => $do_status,// 状态1执行中 2成功4失败
                    'log_content' => [
                        'log_no' => $log_no,// 接口执行编号
                        // 'request_content' => json_encode($requestLog),// 请求参数内容
                        'results_content' => $errStr ?: '执行成功',// 执行结果
                    ]
                ];
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                static::replaceById($request, $controller, $apiLogInfo, $logId, $extParams, true);

            }
        }
        return $result;
    }
}
