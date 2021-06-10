<?php
//报名企业(主表)
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\CourseOrder;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPICourseOrderBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CourseOrderAPI';
    public static $table_name = 'course_order';// 表名称
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
            // 获得企业的报名人员信息--人员的单价会格式化为浮点小数
            'course_order_staff' => CTAPICourseOrderStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'course_order_id']
                , 2, 2
                ,'','',
                CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_order_staff'),
                    static::getUboundRelationExtendParams($extendParams, 'course_order_staff'))
                , static::getRelationSqlParams([], $extendParams, 'course_order_staff'), '', ['extendConfig' => ['listHandleKeyArr' => ['priceIntToFloat']]]),
            // 获得企业的报名人员信息[可缴费的]--人员的单价会格式化为浮点小数
            'course_order_staff_pay' => CTAPICourseOrderStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'course_order_id']
                , 2, 2
                ,'','',
                CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_order_staff_pay'),
                    static::getUboundRelationExtendParams($extendParams, 'course_order_staff_pay'))
                , static::getRelationSqlParams(['where' => [['staff_status', '!=', 4], ['pay_status', '!=', 4]]], $extendParams, 'course_order_staff_pay'), '', ['extendConfig' => ['listHandleKeyArr' => ['priceIntToFloat']]]),
            // 获得发票开票模板名称
            'invoice_template_name' => CTAPIInvoiceTemplateBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_template_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIInvoiceTemplateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_template_name'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_template_name')),
                static::getRelationSqlParams([], $extendParams, 'invoice_template_name'), '', []),// 'where' => [['admin_type', 2]]

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
        if(($return_num & 4) == 4){// 给上一级返回  company_grade_text , join_num , contacts, tel下标
            $fields_merge = Tool::arrEqualKeyVal(['company_grade_text', 'join_num', 'contacts', 'tel'],true);// 获得名称
            if(!isset($return_data['fields_merge'])) $return_data['fields_merge'] = [];
            array_push($return_data['fields_merge'], $fields_merge);
        }
        if(($return_num & 8) == 8){// 给上一级返回  company_grade_text , contacts, tel下标
            $fields_merge = Tool::arrEqualKeyVal(['company_grade_text', 'contacts', 'tel'],true);// 获得名称
            if(!isset($return_data['fields_merge'])) $return_data['fields_merge'] = [];
            array_push($return_data['fields_merge'], $fields_merge);
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

        $course_id = CommonRequest::getInt($request, 'course_id');
        if($course_id > 0 )  array_push($queryParams['where'], ['course_id', '=', $course_id]);

        $company_id = CommonRequest::getInt($request, 'company_id');
        if($company_id > 0 )  array_push($queryParams['where'], ['company_id', '=', $company_id]);

        $company_id_history = CommonRequest::getInt($request, 'company_id_history');
        if($company_id_history > 0 )  array_push($queryParams['where'], ['company_id_history', '=', $company_id_history]);

        $admin_type = CommonRequest::get($request, 'admin_type');
        if(strlen($admin_type) > 0 && $admin_type != 0)  Tool::appendParamQuery($queryParams, $admin_type, 'admin_type', [0, '0', ''], ',', false);

        $company_grade = CommonRequest::get($request, 'company_grade');
        if(strlen($company_grade) > 0 && $company_grade != 0)  Tool::appendParamQuery($queryParams, $company_grade, 'company_grade', [0, '0', ''], ',', false);

        $pay_status = CommonRequest::get($request, 'pay_status');
        if(strlen($pay_status) > 0 && $pay_status != 0)  Tool::appendParamQuery($queryParams, $pay_status, 'pay_status', [0, '0', ''], ',', false);

        $join_class_status = CommonRequest::get($request, 'join_class_status');
        if(strlen($join_class_status) > 0 && $join_class_status != 0)  Tool::appendParamQuery($queryParams, $join_class_status, 'join_class_status', [0, '0', ''], ',', false);

        $company_status = CommonRequest::get($request, 'company_status');
        if(strlen($company_status) > 0 && !in_array($company_status, [0, -1]))  Tool::appendParamQuery($queryParams, $company_status, 'company_status', [0, '0', ''], ',', false);

        $invoice_template_id = CommonRequest::get($request, 'invoice_template_id');
        if(strlen($invoice_template_id) > 0 && !in_array($invoice_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id, 'invoice_template_id', [0, '0', ''], ',', false);

        $invoice_template_id_history = CommonRequest::get($request, 'invoice_template_id_history');
        if(strlen($invoice_template_id_history) > 0 && !in_array($invoice_template_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id_history, 'invoice_template_id_history', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

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
            $is_joined_text = '未报名';
            if(!empty($temDataList)){
                $is_joined = 1;
                $is_joined_text = '已报名';
            }
            $info['is_joined'] = $is_joined;
            $info['is_joined_text'] = $is_joined_text;
        }

        return $returnFields;
    }

    /**
     * 报名页面数据
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $reDataArr  会传到前端的参数组数  ['info' => [课程详情], 'staff_list'=>[['id' => '', 'real_name' => '', 'id_number' => '', 'sex_text' => '', 'mobile' => '', 'is_joined' => '', 'is_joined_text' => '']]]
     * @param int $courseId  课程id
     * @param int $company_id  企业id
     * @param int / string / array $user_ids 指定用户id；多个时：一维数组或逗号分隔
     * @return mixed  新增报名表的id
     * @author zouyan(305463219@qq.com)
     */
    public static function getCourseStaff(Request $request, Controller $controller, &$reDataArr, $courseId = 0, $company_id = 0, $user_ids = ''){

        // 获得课程信息及报名的学员信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPICourseBusiness::getRelationConfigs($request, $controller, [], []),// 'resource_list' => '', 'course_content' => '', 'course_order_company' => ''
            // 'infoHandleKeyArr' => ['resetPayMethod'],
            'listHandleKeyArr' => ['initPayMethodText'],
            'sqlParams' => ['where' => [['status_online', 1]]]
        ];
        $info = CTAPICourseBusiness::getInfoData($request, $controller, $courseId, [], '', $extParams);
        if(empty($info)) throws('课程信息不存在！');

        $reDataArr['info'] = $info;
        // 获得所有的学员信息
        $staffKV = ['company_id' => $company_id, 'admin_type' => 4, 'is_perfect' => 2, 'open_status' => 2, 'account_status' => 1];
        if(!empty($user_ids)) $staffKV['id'] = $user_ids;
        $staffList = CTAPIStaffBusiness::getFVFormatList( $request,  $controller, 1, 1
            , $staffKV , false, [], []);

        if(!empty($staffList)) Tool::formatTwoArrKeys($staffList, Tool::arrEqualKeyVal(['id', 'real_name', 'id_number', 'sex_text', 'mobile', 'company_id', 'resource_id', 'resource_ids']), false);

        $relationFormatConfigs = CTAPIStaffBusiness::getRelationConfigs($request, $controller, ['company_info' => '', 'resource_list' => ''], []);
        CTAPIStaffBusiness::formatRelationList( $request, $controller, $staffList, $relationFormatConfigs);

        // 获得所属企业的名称及证件照
        $staffIds = Tool::getArrFields($staffList, 'id');
        // 获得面授课程正在进行中的学员--非 4已作废8已结业
        $courseOrderStaff = [];
        if(!empty($staffIds)) $courseOrderStaff = CTAPICourseOrderStaffBusiness::getFVFormatList( $request,  $controller, 1, 1
            ,  ['course_id' => $courseId,'staff_id' => $staffIds, 'staff_status' => [1,8]], false, [], []);
        //if(!empty($courseOrderStaff)){
            $courseFormatOrderStaff = Tool::arrUnderReset($courseOrderStaff, 'staff_id', 2, '_');
            foreach($staffList as $k => &$v){
                // 0未报名[可报名]  1已报名   8已结业[可再次报名]
                $temOrderStaff = $courseFormatOrderStaff[$v['id']] ?? [];
                $is_joined = 0;
                $is_joined_text_arr = [];
                foreach($temOrderStaff as $t_v){
                    $is_joined |= $t_v['staff_status'];
                    if($t_v['staff_status'] == 1) {
                        array_push($is_joined_text_arr, '已报名');
                    }
                    if($t_v['staff_status'] == 8) {
                        array_push($is_joined_text_arr, '已结业');
                    }
                }
                if(empty($is_joined_text_arr)) array_push($is_joined_text_arr, '未报名');
                $v['is_joined'] = $is_joined;
                $v['is_joined_text'] = implode('、', $is_joined_text_arr);
            }
        // }
        // pr($staffList);
        $reDataArr['staff_list'] = $staffList;

    }

    /**
     * 报名操作
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $courseId  课程id
     * @param array $staff_id  报名的人员id数组 -- 一维数组
     * @param array $extendArr  其它扩展参数，// 一维数组 ['contacts' => $contacts,'tel' => $tel, 'certificate_company' => '每个学员对应的-证书所属单位']
     * @return mixed  新增报名表的id
     * @author zouyan(305463219@qq.com)
     */
    public static function courseJoin(Request $request, Controller $controller, $courseId = 0, $staff_id = [], $extendArr = []){

        if(empty($staff_id)) throws('请选择学员');

        // 获得课程信息及报名的学员信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPICourseBusiness::getRelationConfigs($request, $controller, [], []),// 'resource_list' => '', 'course_content' => '', 'course_order_company' => ''
            // 'infoHandleKeyArr' => ['resetPayMethod'],
            'listHandleKeyArr' => ['initPayMethodText'],
            'sqlParams' => ['where' => [['status_online', 1]]]
        ];
        $info = CTAPICourseBusiness::getInfoData($request, $controller, $courseId, [], '', $extParams);
        if(empty($info)) throws('课程信息不存在或已下线！');

        $company_grade = $controller->user_info['company_grade'];
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

        $join_num = count($staff_id);

        // 判断选择的人员是不是有不可以报名的用户
        $staffCount = CTAPICourseOrderStaffBusiness::getFVFormatList( $request,  $controller, 8, 1
            ,  ['course_id' => $courseId, 'staff_id' => $staff_id, 'staff_status' => 1], false,[], []);
        if($staffCount > 0) throws('人员不可重复报名同一课程！');

        $courseOrderStaff = [];
        foreach($staff_id as $temKey => $temStaffId){
            array_push($courseOrderStaff, [
                'course_id' => $courseId,
                // 'course_order_id' => '',
                'company_id' => $controller->user_id,
                // 'company_id_history' => '',
                'staff_id' => $temStaffId,
                // 'staff_id_history' => '',
                'certificate_company' => $extendArr['certificate_company'][$temKey]  ?? '',
                // 'class_id' => '',
                // 'class_company_id' => '',
                'price' => $price,
                // 'order_no' => '',
                'pay_status' => 1,
                'join_class_status' => 1,
                'staff_status' => 1,
                'order_date' => date('Y-m-d'),
                'order_time' => date('Y-m-d H:i:s'),
                // 'pay_date' => date('Y-m-d'),
                // 'pay_time' => date('Y-m-d H:i:s'),
                // 'join_class_date' => '',
                // 'join_class_time' => '',
                 'invoice_template_id' => $invoice_template_id,
                 'invoice_project_template_id' => $invoice_project_template_id,
            ]);
        }

        $saveData = [
            'course_id' => $courseId,
            'admin_type' => $controller->user_type,
            'company_id' => $controller->user_id,
            'company_grade' => $company_grade,
            'join_num' => $join_num,
            'contacts' => $extendArr['contacts']  ?? '',
            'tel' => $extendArr['tel'] ?? '',
            'price' => $price,
            'price_total' => bcmul($price, $join_num),// $price * $join_num,
            'pay_status' => 1,
            'joined_class_num' => 0,
            'join_class_status' => 1,
            'order_date' => date('Y-m-d'),
            'order_time' => date('Y-m-d H:i:s'),
            // 'pay_date' => date('Y-m-d'),
            // 'pay_time' => date('Y-m-d H:i:s'),
            // 'staff_id' => $staff_id[0] ?? 0,// 第一个图片资源的id
            // 'staff_ids' => $staff_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            // 'resourceIds' => $staff_id,// 此下标为图片资源关系
            'invoice_template_id' => $invoice_template_id,
            'courseOrderStaff' => $courseOrderStaff
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

        $headArr = ['course_name'=>'课程', 'company_name'=>'单位', 'company_grade_text'=>'会员等级', 'join_num'=>'报名人数', 'joined_class_num'=>'分班人数', 'finish_num'=>'结业人数'
            , 'contacts'=>'联络人', 'tel'=>'联络人电话', 'company_status_text'=>'报名状态', 'price'=>'单价', 'price_total'=>'总价', 'pay_status_text'=>'缴费状态'
            , 'join_class_status_text'=>'分班状态',  'order_time'=>'报名时间', 'pay_time'=>'缴费时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','报名企业' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => '报名企业']);
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
            Tool::bathPriceCutFloatInt($data_list, CourseOrder::$IntPriceFields, CourseOrder::$IntPriceIndex, 2, 2);
        }

        // 重写结束
        return $returnFields;
    }

    /**
     * 格式化关系数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array/ string $course_order_id 课程企业报名表的id, 多条可以是一维数组或逗号分隔的字符
     * @return array  需要缴费的 服名用户企业 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getCourseOrderStaffIdsByCourseOrderIds(Request $request, Controller $controller, $course_order_id){

        if(is_string($course_order_id)) $course_order_id = explode(',', $course_order_id);
        if(!is_array($course_order_id)) $course_order_id = [];
        if(empty($course_order_id)) throws('请选择要缴费的企业');
        // 获得企业报名记录
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPICourseOrderBusiness::getRelationConfigs($request, $controller, ['course_order_staff_pay' => '', 'company_name' => ''], []),
            // 'infoHandleKeyArr' => ['resetPayMethod']
            'listHandleKeyArr' => ['priceIntToFloat'],
        ];
        $courseOrderList = CTAPICourseOrderBusiness::getFVFormatList( $request,  $controller, 1, 1
            , ['id' => $course_order_id], false, [], $extParams);
        if(empty($courseOrderList)) throws('请选择需要缴费的记录！');
        // pr($courseOrderList);
        $orderStaffArr = [];
        foreach($courseOrderList as $courseOrderInfo){
            $company_name = $courseOrderInfo['company_name'] ?? '';
            if(in_array($courseOrderInfo['company_status'], [4])) throws($company_name . '报名批次【' . $courseOrderInfo['id'] . '】已作废状态，不可进行缴费操作！');
            if(in_array($courseOrderInfo['pay_status'], [4])) throws($company_name . '报名批次【' . $courseOrderInfo['id'] . '】已缴费状态，不可进行缴费操作！');
            $course_order_staff = $courseOrderInfo['course_order_staff_pay'] ?? [];// 企业报名的学员
            if(empty($course_order_staff)) throws($company_name . '报名批次【' . $courseOrderInfo['id'] . '】没有需要维修费的学员，不可进行缴费操作！');
            $orderStaffArr = array_merge($orderStaffArr, $course_order_staff);
        }
        $companyIds = Tool::getArrFields($courseOrderList, 'company_id');
        if(count($companyIds) > 1) throws('每次缴费，只能选择相同的企业，才能进行多条记录批量缴费！');
        $invoiceTemplateIds = Tool::getArrFields($courseOrderList, 'invoice_template_id');
        if(count($invoiceTemplateIds) > 1) throws('每次缴费，只能选择相同的【发票开票模板】，才能进行多条记录批量缴费！');
        $id = Tool::getArrFields($orderStaffArr, 'id');
        return $id;
    }
}
