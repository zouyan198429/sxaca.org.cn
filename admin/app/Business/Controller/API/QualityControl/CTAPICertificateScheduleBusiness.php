<?php
// 所属企业检验检测机构资质认定证书附表
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

class CTAPICertificateScheduleBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CertificateScheduleAPI';
    public static $table_name = 'certificate_schedule';// 表名称
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
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 16
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info'), '', []),
            // 获得证书号
            'certificate_info' => CTAPICertificateBusiness::getTableRelationConfigInfo($request, $controller
                , ['certificate_id' => 'id']
                , 1, 1
                ,'','',
                CTAPICertificateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'certificate_info'),
                    static::getUboundRelationExtendParams($extendParams, 'certificate_info')),
                static::getRelationSqlParams([], $extendParams, 'certificate_info'), '', ['extendConfig' => ['infoHandleKeyArr' => ['formatCertificate']]]),
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

        // TODO 自己的参数查询拼接在这里-- 注意：多个id 的查询默认就已经有了，参数是 ids  多个用逗号分隔

        // 方式一  --- 自己拼接
        // $type_id = CommonRequest::get($request, 'type_id');
        // if(is_numeric($type_id) )   Tool::appendCondition($queryParams, 'type_id',  $type_id);//  array_push($queryParams['where'], ['type_id', '=', $type_id]);

        $certificate_id = CommonRequest::getInt($request, 'certificate_id');
        if($certificate_id > 0 )  array_push($queryParams['where'], ['certificate_id', '=', $certificate_id]);

        $company_id = CommonRequest::getInt($request, 'company_id');
        if($company_id > 0 )  array_push($queryParams['where'], ['company_id', '=', $company_id]);

        $category_name_id = CommonRequest::getInt($request, 'category_name_id');
        if($category_name_id > 0 )  array_push($queryParams['where'], ['category_name_id', '=', $category_name_id]);

        $project_name_id = CommonRequest::getInt($request, 'project_name_id');
        if($project_name_id > 0 )  array_push($queryParams['where'], ['project_name_id', '=', $project_name_id]);

        $param_name_id = CommonRequest::getInt($request, 'param_name_id');
        if($param_name_id > 0 )  array_push($queryParams['where'], ['param_name_id', '=', $param_name_id]);

        // 方式二 --- 单个拼接--封装
        // static::joinParamQuery($request, $controller, $queryParams, 'class_id', 'class_id', true, [0, '0', ''], ',', false);

        // 方式三 ---  批量拼接 -- 封装

//        $paramConfigs = [
//            [
//                'paramName' => 'class_id', // 参数的名称 -- 必填
//                'fieldName' => 'class_id', // 查询的字段名--表中的 -- 必填
//                'paramIsNum' => false,// 参数的值是一个，且是数字类型  true:数字；false:非数字--默认 -- 选填
//                'excludeVals' => [0, '0', ''],// 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  [''] -- 选填
//                'valsSeparator' => ',',// 如果是多值字符串，多个值的分隔符;默认逗号 , -- 选填
//                'hasInIsMerge' => false,// 如果In条件有值时  true:合并；false:用新值--覆盖 --默认 -- 选填
//            ],
//        ];
//        static::joinParamQueryByArr($request, $controller, $queryParams, $paramConfigs);

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

        $headArr = ['company_id'=>'所属企业ID', 'user_company_name'=>'所属企业', 'certificate_no'=>'CMA证书号', 'ratify_date'=>'批准日期', 'valid_date'=>'有效期至'
            , 'addr'=>'实验室地址', 'category_name'=>'一级名称', 'project_name'=>'二级名称', 'three_name'=>'三级名称', 'four_name'=>'四级名称',  'param_name'=>'项目名称', 'method_name'=>'标准（方法）名称', 'limit_range'=>'限制范围'
            , 'explain_text'=>'说明', 'created_at'=>'创建时间', 'updated_at'=>'更新时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','能力范围' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => '能力范围']);
    }

    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 初始数据  -- 二维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function importTemplateExcel(Request $request, Controller $controller, $data_list = [], $notLog = 0){
        $data_list = [];
        $headArr = ['category_name'=>'一级', 'project_name'=>'二级', 'three_name'=>'三级', 'four_name'=>'四级', 'param_name'=>'项目'
            , 'method_name'=>'标准（方法）名称', 'limit_range'=>'限制范围', 'explain_text'=>'说明', 'ratify_date'=>'批准日期'];
        ImportExport::export('','能力范围导入模版',$data_list,1, $headArr, 0, ['sheet_title' => '能力范围导入模版']);
    }


    /**
     * 删除单条数据--2企业4个人 数据删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delDatasAjax(Request $request, Controller $controller, $organize_id = 0, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $id = CommonRequest::get($request, 'id');
        if(is_array($id)) $id = implode(',', $id);
        // 调用删除接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 1,
            'extendParams' => [
                'organize_id' => $organize_id,
            ]
        ];
        static::exeDBBusinessMethodCT($request, $controller, '',  'delById', $apiParams, $company_id, $notLog);
        return ajaxDataArr(1, $id, '');
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 批量导入员工--通过文件路径
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $fileName 文件全路径
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function importByFile(Request $request, Controller $controller, $fileName = '', $notLog = 0){
        // $fileName = 'staffs.xlsx';
        $dataStartRow = 1;// 数据开始的行号[有抬头列，从抬头列开始],从1开始
        // 需要的列的值的下标关系：一、通过列序号[1开始]指定；二、通过专门的列名指定;三、所有列都返回[文件中的行列形式],$headRowNum=0 $headArr=[]
        $headRowNum = 1;//0:代表第一种方式，其它数字：第二种方式; 1开始 -必须要设置此值，$headArr 参数才起作用
        // 下标对应关系,如果设置了，则只获取设置的列的值
        // 方式一格式：['1' => 'name'，'2' => 'chinese',]
        // 方式二格式: ['姓名' => 'name'，'语文' => 'chinese',]
        $headArr = [
            '一级' => 'category_name',
            '二级' => 'project_name',
            '三级' => 'three_name',
            '四级' => 'four_name',
            '项目' => 'param_name',
            '标准（方法）名称' => 'method_name',
            '限制范围' => 'limit_range',
            '说明' => 'explain_text',
            '批准日期' => 'ratify_date',
        ];
//        $headArr = [
//            '1' => 'name',
//            '2' => 'chinese',
//            '3' => 'maths',
//            '4' => 'english',
//        ];
        try{
            $dataArr = ImportExport::import($fileName, $dataStartRow, $headRowNum, $headArr);
        } catch ( \Exception $e) {
            throws($e->getMessage());
        }
        return self::import($request, $controller, $dataArr, $notLog);
    }

    /**
     * 批量导入
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function import(Request $request, Controller $controller, $saveData , $notLog = 0)
    {
        $company_id = $controller->company_id;

        $open_status = CommonRequest::getInt($request, 'open_status');// // 1首次 ;2扩项
        $company_id = CommonRequest::getInt($request, 'company_id');
        $certificate_no = CommonRequest::get($request, 'certificate_no');
        $addr = CommonRequest::get($request, 'addr');
        $ratify_date = CommonRequest::get($request, 'ratify_date');
        $valid_date = CommonRequest::get($request, 'valid_date');
        // 判断开始结束日期 --  // 初次或时间都填写了
        if($open_status == 1 || ($ratify_date != '' && $valid_date != '')){
            Tool::judgeBeginEndDate($ratify_date, $valid_date, 1 + 2 + 256 + 512, 1, date('Y-m-d'), '有效起止日期');
        }else{
            if($ratify_date != '' && judgeDate($ratify_date) === false ){
                throws('批准日期不是有效的日期格式！');
            }
            if($valid_date != '' && judgeDate($valid_date) === false ){
                throws('有效期止不是有效的日期格式！');
            }
        }
        $params = [
            'company_id' => $company_id,
            'certificate_no' => $certificate_no,
            // 'ratify_date' => $ratify_date,
            // 'valid_date' => $valid_date,
            'addr' => $addr,
        ];
        if($ratify_date != '') $params['ratify_date'] = $ratify_date;
        if($valid_date != '') $params['valid_date'] = $valid_date;

        foreach($saveData as $k => $v){
            // 优化下，如果含有 / ，则转为 -
            if(isset($v['ratify_date']) && strpos($v['ratify_date'], '/') !== false){
                $v['ratify_date'] = str_replace(['/'], ['-'], $v['ratify_date']);
            }
            // 如果 ratify_date 内容是 2021年03月05日，则进行处理
            if(isset($v['ratify_date']) && strpos($v['ratify_date'], '年') !== false){
                $v['ratify_date'] = str_replace(['年', '月', '日'], ['-', '-', ''], $v['ratify_date']);
            }
            // 对 ratify_date 进行处理， excel文件中有则按文件的，文件为空，则按填入的
            if(isset($v['ratify_date']) && judgeDate($v['ratify_date']) === false ){// 有下标且不是日期-- 用填写的 isset($params['ratify_date']) &&
                unset($v['ratify_date']);
            }
            $saveData[$k] = array_merge($params, $v);
        }
        $certificate_info = [];
        if($open_status == 1){
            $certificate_info = [
                'certificate_no' => $certificate_no,
                 'ratify_date' => $ratify_date,
                 'valid_date' => $valid_date,
                'addr' => $addr,
            ];
        }

        // 参数
        $apiParams = [
            'save_data' => $saveData,
            'company_id' => $company_id,
            'operate_staff_id' =>  $controller->user_id,
            'modifAddOprate' => 1,
            'doType' => 1,
            'open_status' => $open_status,
            'certificate_info' => $certificate_info,
        ];
        $methodName = 'importDatas';
//        if(isset($saveData['mini_openid']))  $methodName = 'replaceByIdWX';
        $result = static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        return $result;
//        $requestData = [
//            'company_id' => $company_id,
//            'staff_id' =>  $controller->user_id,
//            'admin_type' =>  $controller->admin_type,//self::$admin_type,
//            'save_data' => $saveData,
//        ];
//        $url = config('public.apiUrl') . config('apiUrl.apiPath.staffImport');
//        // 生成带参数的测试get请求
//        // $requestTesUrl = splicQuestAPI($url , $requestData);
//        return HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');
    }

    /**
     * 批量文件保存接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function filesSaveRequest(Request $request, Controller $controller, $notLog = 0){

        // $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $company_id = CommonRequest::getInt($request, 'company_id');
        $company_info = [];

        $company_name = CommonRequest::get($request, 'company_name');
        $company_info['company_name'] = $company_name;

        $certificate_no = CommonRequest::get($request, 'certificate_no');
        $company_info['company_certificate_no'] = $certificate_no;

        // 企业数据验证
        CTAPIStaffBusiness::companyDataJudge( $request,  $controller, $company_info, '', $notLog);

        $file_json = CommonRequest::get($request, 'file_json');// 文件信息
        if (isNotJson($file_json)) {
            throws('文件信息不是有效的json格式！');
        }
        $fileArr = json_decode($file_json , true);
         if(!is_array($fileArr) || empty($fileArr))  throws('文件信息不能为空！');

        // 验证每一项
        $errArr = [];

        // 文件数据验证
        static::fileDataJudge($request, $controller, $fileArr, $errArr, $notLog );

        // 有错误信息
        if(!empty($errArr) ) throws(implode(';', $errArr));

         // throws('接口数据通过验证');

        // 保存数据
        // 调用新加或修改接口
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $modifAddOprate = true;
        $apiParams = [
            'saveData' => [
                'company_info' => $company_info,
//                    [
//                        'company_name' => $company_name,// 机构名称
//                        'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
//                    ],
                'file_list' => $fileArr,
            ],
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        // throws(json_encode($apiParams));
        $methodName = 'bathSaveFiles';

        return static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);

    }

    /**
     * 批量保存接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function bathSaveRequest(Request $request, Controller $controller, $notLog = 0){

        // $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $company_id = CommonRequest::getInt($request, 'company_id');
        $company_info = [];

        $company_name = CommonRequest::get($request, 'company_name');
        $company_info['company_name'] = $company_name;

        $certificate_no = CommonRequest::get($request, 'certificate_no');
        $company_info['company_certificate_no'] = $certificate_no;

        $ratify_date = CommonRequest::get($request, 'ratify_date');
        $company_info['ratify_date'] = $ratify_date;

        $valid_date = CommonRequest::get($request, 'valid_date');
        $company_info['valid_date'] = $valid_date;

        $addr = CommonRequest::get($request, 'addr');
        $company_info['laboratory_addr'] = $addr;
        $company_info['addr'] = $addr;

        $contact_name = CommonRequest::get($request, 'contact_name');
        $company_info['company_contact_name'] = $contact_name;

        $contact_mobile = CommonRequest::get($request, 'contact_mobile');
        $company_info['company_contact_mobile'] = $contact_mobile;

        // 企业数据验证
        CTAPIStaffBusiness::companyDataJudge( $request,  $controller, $company_info, '', $notLog);

        $file_json = CommonRequest::get($request, 'file_json');// 文件信息
        if (isNotJson($file_json)) {
            throws('文件信息不是有效的json格式！');
        }
        $fileArr = json_decode($file_json , true);
        // if(!is_array($fileArr) || empty($fileArr))  throws('文件信息不能为空！');


        $schedule_json = CommonRequest::get($request, 'schedule_json');// 能力范围
        if (isNotJson($schedule_json)) {
            throws('能力范围不是有效的json格式！');
        }
        $scheduleArr = json_decode($schedule_json , true);
        if(!is_array($scheduleArr) || empty($scheduleArr))  throws('能力范围不能为空！');
        // 验证每一项
        $errArr = [];

        // 文件数据验证
        static::fileDataJudge($request, $controller, $fileArr, $errArr, $notLog );
        // 能力范围数据验证
        static::scheduleDataJudge($request, $controller, $scheduleArr, $errArr, $notLog );

        // 有错误信息
        if(!empty($errArr) ) throws(implode(';', $errArr));

        // throws('接口数据通过验证');

        // 保存数据
        // 调用新加或修改接口
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $modifAddOprate = true;
        $apiParams = [
            'saveData' => [
                'company_info' => $company_info,
//                    [
//                        'company_name' => $company_name,// 机构名称
//                        'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
//                        'ratify_date' => $ratify_date,// 发证日期 格式 2020-11-06
//                        'valid_date' => $valid_date,// 证书有效日期 格式 2020-11-06
//                        'laboratory_addr' => $addr,// 实验室地址
//                        'company_contact_name' => $contact_name,// 联系人
//                        'company_contact_mobile' => $contact_mobile,// 联系人手机或电话
//                    ],
                'file_list' => $fileArr,
                'schedule_list' => $scheduleArr,
            ],
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        // throws(json_encode($apiParams));
        $methodName = 'bathSaveDatas';

        return static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);

    }

    /**
     * 能力范围删除或新加接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function bathModifyRequest(Request $request, Controller $controller, $notLog = 0){

        // $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $company_id = CommonRequest::getInt($request, 'company_id');
        $company_info = [];

        $company_name = CommonRequest::get($request, 'company_name');
        $company_info['company_name'] = $company_name;

        $certificate_no = CommonRequest::get($request, 'certificate_no');
        $company_info['company_certificate_no'] = $certificate_no;

        // 企业数据验证
        CTAPIStaffBusiness::companyDataJudge( $request,  $controller, $company_info, '', $notLog);

        $schedule_del_json = CommonRequest::get($request, 'schedule_del_json');// 能力范围
        if (isNotJson($schedule_del_json)) {
            throws('删除能力范围不是有效的json格式！');
        }
        $scheduleDelArr = json_decode($schedule_del_json , true);
        // if(!is_array($scheduleDelArr) || empty($scheduleDelArr))  throws('能力范围不能为空！');

        $schedule_add_json = CommonRequest::get($request, 'schedule_add_json');// 能力范围
        if (isNotJson($schedule_add_json)) {
            throws('新加能力范围不是有效的json格式！');
        }
        $scheduleAddArr = json_decode($schedule_add_json , true);
        // if(!is_array($scheduleAddArr) || empty($scheduleAddArr))  throws('能力范围不能为空！');

         if(!is_array($scheduleAddArr) || !is_array($scheduleDelArr) || (empty($scheduleDelArr) && empty($scheduleAddArr)))  throws('删除或新加的能力范围必须至少操作一项！');

        // 验证每一项
        $errArr = [];

        // 能力范围数据验证
        static::scheduleDataJudge($request, $controller, $scheduleDelArr, $errArr, $notLog );
        static::scheduleDataJudge($request, $controller, $scheduleAddArr, $errArr, $notLog );

        // 有错误信息
        if(!empty($errArr) ) throws(implode(';', $errArr));

        // throws('接口数据通过验证');

        // 保存数据
        // 调用新加或修改接口
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $modifAddOprate = true;
        $apiParams = [
            'saveData' => [
                'company_info' => $company_info,
//                    [
//                        'company_name' => $company_name,// 机构名称
//                        'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
//                    ],
                'schedule_del_list' => $scheduleDelArr,
                'schedule_add_list' => $scheduleAddArr,
            ],
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        // throws(json_encode($apiParams));
        $methodName = 'bathModifyDatas';

        return static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);

    }



    /**
     * 根据条件修改能力范围接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function UpdateRequest(Request $request, Controller $controller, $notLog = 0){

        // $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $company_id = CommonRequest::getInt($request, 'company_id');
        $company_info = [];

        $company_name = CommonRequest::get($request, 'company_name');
        $company_info['company_name'] = $company_name;

        $certificate_no = CommonRequest::get($request, 'certificate_no');
        $company_info['company_certificate_no'] = $certificate_no;

        // 企业数据验证
        CTAPIStaffBusiness::companyDataJudge( $request,  $controller, $company_info, '', $notLog);

        $search_json = CommonRequest::get($request, 'search_json');// 能力范围
        if (isNotJson($search_json)) {
            throws('查询能力范围不是有效的json格式！');
        }
        $scheduleSearchArr = json_decode($search_json , true);
         if(!is_array($scheduleSearchArr) || empty($scheduleSearchArr))  throws('查询能力范围不能为空！');
         if(Tool::isMultiArr($scheduleSearchArr, false)){
             throws('查询能力范围必须是一维对象！');
         }

        $schedule_json = CommonRequest::get($request, 'schedule_json');// 能力范围
        if (isNotJson($schedule_json)) {
            throws('更新能力范围不是有效的json格式！');
        }
        $scheduleArr = json_decode($schedule_json , true);
        if(!is_array($scheduleArr) || empty($scheduleArr))  throws('更新能力范围不能为空！');
        if(Tool::isMultiArr($scheduleArr, false)){
            throws('更新能力范围必须是一维对象！');
        }

        // 验证每一项
        $errArr = [];

        // 能力范围数据验证
        $scheduleTemSearchArr = [$scheduleSearchArr];
        static::scheduleDataJudge($request, $controller, $scheduleTemSearchArr, $errArr, $notLog );
        $scheduleSearchArr = $scheduleTemSearchArr[0] ?? [];
        $scheduleTemArr = [$scheduleArr];
        static::scheduleDataJudge($request, $controller, $scheduleTemArr, $errArr, $notLog );
        $scheduleArr = $scheduleTemArr[0] ?? [];

        // 有错误信息
        if(!empty($errArr) ) throws(implode(';', $errArr));

        // throws('接口数据通过验证');

        // 保存数据
        // 调用新加或修改接口
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $modifAddOprate = true;
        $apiParams = [
            'saveData' => [
                'company_info' => $company_info,
//                    [
//                        'company_name' => $company_name,// 机构名称
//                        'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
//                    ],
                'schedule_search' => $scheduleSearchArr,
                'schedule_update' => $scheduleArr,
            ],
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        // throws(json_encode($apiParams));
        $methodName = 'updateDatas';

        return static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);

    }


    /**
     * 注册/修改企业信息接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function companySaveRequest(Request $request, Controller $controller, $notLog = 0){

        // $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $company_id = CommonRequest::getInt($request, 'company_id');
        $company_info = [];

        $company_name = CommonRequest::get($request, 'company_name');
        $company_info['company_name'] = $company_name;

        $certificate_no = CommonRequest::get($request, 'certificate_no');
        $company_info['company_certificate_no'] = $certificate_no;

        $ratify_date = CommonRequest::get($request, 'ratify_date');
        $company_info['ratify_date'] = $ratify_date;

        $valid_date = CommonRequest::get($request, 'valid_date');
        $company_info['valid_date'] = $valid_date;

        $addr = CommonRequest::get($request, 'addr');
        $company_info['laboratory_addr'] = $addr;
        $company_info['addr'] = $addr;

        $contact_name = CommonRequest::get($request, 'contact_name');
        $company_info['company_contact_name'] = $contact_name;

        $contact_mobile = CommonRequest::get($request, 'contact_mobile');
        $company_info['company_contact_mobile'] = $contact_mobile;

        // 企业数据验证
        CTAPIStaffBusiness::companyDataJudge( $request,  $controller, $company_info, '', $notLog);

        // throws('接口数据通过验证');

        // 保存数据
        // 调用新加或修改接口
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $modifAddOprate = true;
        $apiParams = [
            'saveData' => [
                'company_info' => $company_info,
//                    [
//                        'company_name' => $company_name,// 机构名称
//                        'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
//                        'ratify_date' => $ratify_date,// 发证日期 格式 2020-11-06
//                        'valid_date' => $valid_date,// 证书有效日期 格式 2020-11-06
//                        'laboratory_addr' => $addr,// 实验室地址
//                        'company_contact_name' => $contact_name,// 联系人
//                        'company_contact_mobile' => $contact_mobile,// 联系人手机或电话
//                    ],
            ],
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        // throws(json_encode($apiParams));
        $methodName = 'saveCompany';

        return static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);

    }

    // **************验证信息************开始******************************
    /**
     * 文件数据验证
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $fileArr 文件数据数组 二维数组
     * @param array $errArr 错误信息数组 一维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function fileDataJudge(Request $request, Controller $controller, &$fileArr, &$errArr, $notLog = 0 ){

        foreach($fileArr as $k => &$v){
            $preStr = '文件信息第' . $k . '项:';
            $temErrArr = [];
            $t_file_title = $v['file_title'] ?? '';
            $t_file_url = $v['file_url'] ?? '';
            $t_file_content = $v['file_content'] ?? '';
            $t_file_content_name = $v['file_content_name'] ?? '';
            $t_file_type = $v['file_type'] ?? 0;
            $t_schedule_type = $v['schedule_type'] ?? 0;
            if(!isset($v['file_title']) || empty($v['file_title'])){
                array_push($temErrArr, '文件名称不存在或不能为空！');
            }

            if(empty(trim($t_file_url)) && empty(trim($t_file_content))){
                array_push($temErrArr, '文件网络读取地址、文件内容的base64_encode值确保至少有一个有值！');
            }

            if(!empty(trim($t_file_content))){
                if(!isset($v['file_content_name']) || empty($v['file_content_name'])){
                    array_push($temErrArr, '文件内容的原文件名不存在或不能为空！');
                }
                if(strpos($t_file_content_name, '.') === false){
                    array_push($temErrArr, '文件内容的原文件名参数【file_content_name】必需带文件扩展名！');
                }
            }


//            if(!empty(trim($t_file_url))){
//                if(!isset($v['file_url']) || empty($v['file_url'])){
//                    array_push($temErrArr, '文件网络读取地址不存在或不能为空！');
//                }
//            }

            if(!isset($v['file_type']) || empty($v['file_type']) || !in_array($v['file_type'], [1,2,5])){
                array_push($temErrArr, '文件类型必须是【1：能力附表 ; 2：机构自我声明 ;5：机构处罚】！');
            }
            if(!isset($v['schedule_type']) || strlen($v['schedule_type']) <= 0 || !in_array($v['schedule_type'], [0, 1, 2, 4, 8 ,16])){
                array_push($temErrArr, '文件操作类型必须是【0：excel文件；1：首次;2：扩项;4：地址变更;8：标准变更;16：复查;】！');
            }

            if(!empty($temErrArr)){
                array_unshift($temErrArr, $preStr);
                array_push($errArr, implode(';', $temErrArr));
            }
        }
    }

    /**
     * 能力范围数据验证
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $scheduleArr 能力范围数据数组 二维数组
     * @param array $errArr 错误信息数组 一维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function scheduleDataJudge(Request $request, Controller $controller, &$scheduleArr, &$errArr, $notLog = 0 ){

        foreach($scheduleArr as $k => &$v){
            $preStr = '能力范围第' . $k . '项:';
            $temErrArr = [];
            $t_category_name = $v['category_name'] ?? '';// 类别[第一级]
            $t_project_name = $v['project_name'] ?? '';//  产品[第二级]
            $t_three_name = $v['three_name'] ?? '';// 第三级
            $t_four_name = $v['four_name'] ?? '';// 第四级
            $t_param_name = $v['param_name'] ?? '';// 项目[第五级]
            $t_method_name = $v['method_name'] ?? '';// 标准（方法）名称
            $t_limit_range = $v['limit_range'] ?? '';// 限制范围
            $t_explain_text = $v['explain_text'] ?? '';// 说明
            // 判断是否依次填写
            $isEmpty = false;
            $isOrder = true;// 是否依次填写

            // if($isOrder && $isEmpty && !empty($t_category_name)) $isOrder = false;
            if($isOrder && empty($t_category_name)) $isEmpty = true;

            if($isOrder && $isEmpty && !empty($t_project_name)) $isOrder = false;
            if($isOrder && empty($t_project_name)) $isEmpty = true;

            if($isOrder && $isEmpty && !empty($t_three_name)) $isOrder = false;
            if($isOrder && empty($t_three_name)) $isEmpty = true;

            if($isOrder && $isEmpty && !empty($t_four_name)) $isOrder = false;
            if($isOrder && empty($t_four_name)) $isEmpty = true;

            if($isOrder && $isEmpty && !empty($t_param_name)) $isOrder = false;
            if($isOrder && empty($t_param_name)) $isEmpty = true;

            if(!$isOrder) array_push($temErrArr, '请依次填写各分类，不要跳跃！');

            //  大于二级的，最后一级优先填到 项目[第五级] ；剩下的再依次填three_name  第三级；four_name  第四级

            if(!empty($t_three_name)){// 第三级不为空
                if(!empty($t_four_name)){// 第四级不为空
                    if(!empty($t_param_name)){// 第五级不为空

                    }else{// 第五级为空
                        $v['param_name'] = $t_four_name;
                        $t_four_name = '';
                        $v['four_name'] = $t_four_name;
                    }
                }else{// 第四级为空
                    $v['param_name'] = $t_three_name;
                    $t_three_name = '';
                    $v['three_name'] = $t_three_name;
                }
            }
            if(!isset($v['category_name']) || empty($v['category_name'])){
                array_push($temErrArr, '类别[第一级]不存在或不能为空！');
            }
            // 验证长度
            if(strlen($t_category_name) < 1 || strlen($t_category_name) > 150) array_push($temErrArr, '类别[第一级]长度为1~ 150个字符！');
            if(strlen($t_project_name) < 1 || strlen($t_project_name) > 150) array_push($temErrArr, '产品[第二级]长度为1~ 150个字符！');
            if(strlen($t_three_name) < 0 || strlen($t_three_name) > 150) array_push($temErrArr, '第三级长度为0~ 150个字符！');
            if(strlen($t_four_name) < 0 || strlen($t_four_name) > 150) array_push($temErrArr, '第四级长度为0~ 150个字符！');
            if(strlen($t_param_name) < 0 || strlen($t_param_name) > 500) array_push($temErrArr, '项目[第五级]长度为0~ 500个字符！');
            if(strlen($t_method_name) < 0 || strlen($t_method_name) > 3500) array_push($temErrArr, '标准（方法）名称长度为0~ 3500个字符！');
            if(strlen($t_limit_range) < 0 || strlen($t_limit_range) > 1500) array_push($temErrArr, '限制范围长度为0~ 1500个字符！');
            if(strlen($t_explain_text) < 0 || strlen($t_explain_text) > 1000) array_push($temErrArr, '说明长度为0~ 1000个字符！');

            if(!empty($temErrArr)){
                array_unshift($temErrArr, $preStr);
                array_push($errArr, implode(';', $temErrArr));
            }
        }
    }
    // **************验证信息************结束******************************
}
