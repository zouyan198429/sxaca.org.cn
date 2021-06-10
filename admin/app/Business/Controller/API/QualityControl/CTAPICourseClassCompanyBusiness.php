<?php
// 培训班企业管理
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

class CTAPICourseClassCompanyBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CourseClassCompanyAPI';
    public static $table_name = 'course_class_company';// 表名称
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
            'company_name' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'company_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_name'),
                    static::getUboundRelationExtendParams($extendParams, 'company_name')),
                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_name'), '', []),
            // 获得课程名称
            'course_name' => CTAPICourseBusiness::getTableRelationConfigInfo($request, $controller
                , ['course_id' => 'id']
                , 1, 2
                ,'','',
                CTAPICourseBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_name'),
                    static::getUboundRelationExtendParams($extendParams, 'course_name')),
                static::getRelationSqlParams([], $extendParams, 'course_name'), '', []),
            // 获得班级名称
            'class_name' => CTAPICourseClassBusiness::getTableRelationConfigInfo($request, $controller
                , ['class_id' => 'id']
                , 1, 2
                ,'','',
                CTAPICourseClassBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'class_name'),
                    static::getUboundRelationExtendParams($extendParams, 'class_name')),
                static::getRelationSqlParams([], $extendParams, 'class_name'), '', []),
            // 获得报名主表信息--联系人等信息
            'course_order_info' => CTAPICourseOrderBusiness::getTableRelationConfigInfo($request, $controller
                , ['course_order_id' => 'id']
                , 1, 8
                ,'','',
                CTAPICourseOrderBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_order_info'),
                    static::getUboundRelationExtendParams($extendParams, 'course_order_info')),
                static::getRelationSqlParams([], $extendParams, 'course_order_info'), '', []),
            // 获得企业的报名人员信息--人员的单价会格式化为浮点小数
            'course_order_staff' => CTAPICourseOrderStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'class_company_id']
                , 2, 2
                ,'','',
                CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_order_staff'),
                    static::getUboundRelationExtendParams($extendParams, 'course_order_staff'))
                , static::getRelationSqlParams([], $extendParams, 'course_order_staff'), '', ['extendConfig' => ['listHandleKeyArr' => ['priceIntToFloat']]]),
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

        $course_id = CommonRequest::getInt($request, 'course_id');
        if($course_id > 0 )  array_push($queryParams['where'], ['course_id', '=', $course_id]);

        $course_order_id = CommonRequest::getInt($request, 'course_order_id');
        if($course_order_id > 0 )  array_push($queryParams['where'], ['course_order_id', '=', $course_order_id]);

        $admin_type = CommonRequest::get($request, 'admin_type');
        if(strlen($admin_type) > 0 && $admin_type != 0)  Tool::appendParamQuery($queryParams, $admin_type, 'admin_type', [0, '0', ''], ',', false);

        $company_id = CommonRequest::getInt($request, 'company_id');
        if($company_id > 0 )  array_push($queryParams['where'], ['company_id', '=', $company_id]);

        $company_id_history = CommonRequest::getInt($request, 'company_id_history');
        if($company_id_history > 0 )  array_push($queryParams['where'], ['company_id_history', '=', $company_id_history]);

        $staff_id = CommonRequest::getInt($request, 'staff_id');
        if($staff_id > 0 )  array_push($queryParams['where'], ['staff_id', '=', $staff_id]);

        $staff_id_history = CommonRequest::getInt($request, 'staff_id_history');
        if($staff_id_history > 0 )  array_push($queryParams['where'], ['staff_id_history', '=', $staff_id_history]);

        $class_id = CommonRequest::getInt($request, 'class_id');
        if($class_id > 0 )  array_push($queryParams['where'], ['class_id', '=', $class_id]);

        $pay_status = CommonRequest::get($request, 'pay_status');
        if(strlen($pay_status) > 0 && $pay_status != 0)  Tool::appendParamQuery($queryParams, $pay_status, 'pay_status', [0, '0', ''], ',', false);


        $class_status = CommonRequest::get($request, 'class_status');
        if(strlen($class_status) > 0 && $class_status != 0)  Tool::appendParamQuery($queryParams, $class_status, 'class_status', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
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

        $headArr = ['course_name'=>'课程', 'company_name'=>'单位', 'company_grade_text'=>'会员等级', 'course_order_id'=>'报名批次', 'class_name'=>'班级', 'join_num'=>'人数'
            , 'contacts'=>'联络人', 'tel'=>'联络人电话', 'pay_status_text'=>'缴费状态', 'class_status_text'=>'班级状态', 'created_at'=>'分班时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','班级企业信息' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => '班级企业信息']);
    }

}
