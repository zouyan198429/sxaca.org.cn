<?php
// 资源
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\File\DownFile;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use App\Services\Upload\UploadFile;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Log;

class CTAPIResourceBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\ResourceAPI';
    public static $table_name = 'resource';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 大后台 admin/年/月/日/文件
    // 企业 company/[生产单元/]年/月/日/文件
//    protected static $source_path = '/resource/company/';
//    protected static $source_tmp_path = '/resource/tmp/';// 临时文件夹
//    protected static $cache_block = 2; // 1 redis缓存分片内容--适合redis内存比较大的服务器，2 临时文件缓存分片内容--redis内存比较小时

    // 1:图片;2:excel
//    public static $resource_type = [
//        '1' => [
//            'name' => '图片文件',
//            'ext' => ['jpg','jpeg','gif','png','bmp','ico'],// 扩展名
//            'dir' => 'images',// 文件夹名称
//            'maxSize' => 5,// 文件最大值  单位 M
//            'other' => [],// 其它各自类型需要判断的指标
//        ],
//        '2' => [
//            'name' => 'excel文件',
//            'ext' => ['xlsx', 'xls'],// 扩展名
//            'dir' => 'excel',// 文件夹名称
//            'maxSize' => 10,// 文件最大值 单位 M
//            'other' => [],// 其它各自类型需要判断的指标
//        ]
//    ];

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

        $ower_type = CommonRequest::getInt($request, 'ower_type');
        if($ower_type > 0 )  array_push($queryParams['where'], ['ower_type', '=', $ower_type]);

        $ower_id = CommonRequest::getInt($request, 'ower_id');
        if($ower_id > 0 )  array_push($queryParams['where'], ['ower_id', '=', $ower_id]);

        $type_self_id = CommonRequest::getInt($request, 'type_self_id');
        if($type_self_id > 0 )  array_push($queryParams['where'], ['type_self_id', '=', $type_self_id]);

        $type_self_id_history = CommonRequest::getInt($request, 'type_self_id_history');
        if($type_self_id_history > 0 )  array_push($queryParams['where'], ['type_self_id_history', '=', $type_self_id_history]);

        $resource_type = CommonRequest::getInt($request, 'resource_type');
        if($resource_type > 0 )  array_push($queryParams['where'], ['resource_type', '=', $resource_type]);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }




    /**
     * 删除单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delAjax(Request $request, Controller $controller, $notLog = 0)
    {
         // $controller->company_id = 1;
         // $controller->user_id = 1;
         // $controller->operate_staff_id = 1;

        $company_id = $controller->company_id;

        $id = CommonRequest::getInt($request, 'id');
        Tool::judgeInitParams('id', $id);

        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        $resultDatas = $modelObj::ResourceDelById($id, $company_id, $notLog);
        return ajaxDataArr(1, $resultDatas, '');
        // $id = CommonRequest::getInt($request, 'id');
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

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
        if(in_array('format_resource', $handleKeyArr)){
            $data_list = Tool::formatResource($data_list, 2);
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
        if(in_array('resource_list', $infoHandleKeyArr)){
            // $resource_list = [];
            $resource_list = $temDataList;// $resourceDataArr[$info['resource_id']] ?? [];
            if(isset($info['site_resources'])){
                Tool::resourceUrl($info, 2);
                $resource_list = Tool::formatResource($info['site_resources'], 2);
                unset($info['site_resources']);
            }
            $info['resource_list'] = $resource_list;
            $returnFields['resource_list'] = 'resource_list';
        }

        return $returnFields;
    }
    /**
     * 上传文件
     * post参数 photo 文件；name  文件名称;note 资源说明[可为空];;;;
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $resource_type 资源类型 1:图片;2:excel ； 可以上传的文件类型编号 ，多个 用 ｜ 如 ： 1 ｜ 2 ｜ 4
     * @return  array 列表数据
     * {"id":8330,"name":"测试名称","savePath":"/resource/company/1/excel/2020/06/21/",
     * "saveName":"202006211522330a4022f5282c1530.xlsx","
     * store_result":"resource/company/1/excel/2020/06/21/202006211522330a4022f5282c1530.xlsx"}
     * @author zouyan(305463219@qq.com)
     * @author zouyan(305463219@qq.com)
     */
    public static function uploadFile(Request $request, Controller $controller, $resource_type = 1)
    {
//         $controller->company_id = 1;
//         $controller->user_id = 1;
//         $controller->operate_staff_id = 1502;

        try{
            $company_id = $controller->company_id;
            $operate_staff_id =  $controller->operate_staff_id;
            if(!is_numeric($operate_staff_id)) $operate_staff_id = 0;

//            ini_set('memory_limit','1024M');    // 临时设置最大内存占用为 3072M 3G
//            ini_set("max_execution_time", "300");
//            set_time_limit(300);   // 设置脚本最大执行时间 为0 永不过期
            Tool::phpInitSet('3072M', 300);

            // $pro_unit_id = CommonRequest::getInt($request, 'pro_unit_id');
            $name = CommonRequest::get($request, 'name'); // 文件名称
            $resource_note = CommonRequest::get($request, 'note'); // 资源说明
            // 日志
            $requestLog = [
                'files'       => $request->file(),
                'posts'  => $request->post(),
                'input'      => $request->input(),
            ];
            Log::info('上传文件日志',$requestLog);

            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photo = $request->file('photo');
                $uuid =  CommonRequest::get($request, 'uuid');
                Log::info('上传文件日志-uuid',[$uuid]);// o_1d1r9ofhi5v88bk46di171f53a
                $num = CommonRequest::getInt($request, 'chunk');// 分片序号 0,1,2  或 0
                Log::info('上传文件日志-分片序号',[$num]);// 分片序号 [0]
                $count = CommonRequest::getInt($request, 'chunks');// 分片总数量 3 或 1
                Log::info('上传文件日志-分片总数量count',[$count]);// 分片总数量count [1]
                Log::info('上传文件日志-文件信息',[$photo]);// 文件信息 ["[object] (Illuminate\\Http\\UploadedFile: /tmp/phpa2eXco)"]
                //获取原文件名
                $originalName = $photo->getClientOriginalName();
                Log::info('上传文件日志-原文件名',[$originalName]);// 原文件名 ["7.jpg"] 原文件名 ["blob"]
                // 文件类型
                $type = $photo->getClientMimeType();
                Log::info('上传文件日志-文件类型',[$type]);// 文件类型 ["image/jpeg"]
                //临时绝对路径
                $realPath = $photo->getRealPath();
                Log::info('上传文件日志-临时绝对路径',[$realPath]);// 临时绝对路径 ["/tmp/phpa2eXco"]
                // 扩展名 jpg
                $extFirst = strtolower($photo->extension());// 扩展名  该扩展名可能会和客户端提供的扩展名不一致
                $ext = $photo->getClientOriginalExtension(); //上传文件的后缀. 扩展名
                Log::info('上传文件日志-文件后缀extFirst',[$extFirst]);// 文件后缀extFirst ["jpeg"]
                Log::info('上传文件日志-文件后缀',[$ext]);// 文件后缀 ["jpg"]

                if(empty($ext)) $ext = $extFirst;
                // 修复是二进制文件的问题
                $isGetExtByExt = false;// 是否通过文件获取过文件后缀
                if(empty($ext) ||  in_array($ext, ['bin', 'g3'])  ){// g3 ：有分片上传时，$extFirst值为g3的，
                    $isGetExtByExt = true;// 是否通过文件获取过文件后缀
                    $ext = DownFile::getLocalFileExt($name);
                    Log::info('上传文件日志-二进制文件后缀',[$ext]);// 文件后缀 ["jpg"]
                }

                // 根据扩展名，重新获得文件的操作类型
                $resourceConfig = UploadFile::getResourceConfig($ext);
                // 有错误，则再纠错一次
                if(empty($resourceConfig) && !$isGetExtByExt && $type == 'application/octet-stream'){//  && in_array($extFirst, ['bin', 'g3', 'zz'])
                    $ext = DownFile::getLocalFileExt($name);
                    Log::info('上传文件日志-纠错二进制文件后缀',[$ext]);// 文件后缀 ["jpg"]
                    // 根据扩展名，重新获得文件的操作类型
                    $resourceConfig = UploadFile::getResourceConfig($ext);
                }

                if(empty($resourceConfig))  throws('请选择正确的文件！');

                $resourceType = $resourceConfig['resource_type'] ?? 0;
                // 判断上传文件的类型，是否是允许的类型
                if(($resource_type & $resourceType) !== $resourceType){
                    $resourceName = $resourceConfig['name'] ?? '';
                    $resourceExt = $resourceConfig['ext'] ?? [];
                    // $errMsg = $resourceName . '扩展名必须为[' . implode('、', $resourceExt) . ']';
                    // throws($errMsg);
                    throws('文件类型有误，请上传正确类型的文件');
                }
                // 修改文件类型为当前正确的文件类型--历史原因，只能在这里重新改值
                $resource_type = $resourceType;

                $hashname = $photo->hashName();// "geEGcIfovpc8gRSlTYREDZiW4frld0helrZKzmoA.jpeg"
                //获取上传文件的大小
                $size = $photo->getSize();
                Log::info('上传文件日志-文件大小',[$size]);// 文件大小 [61563]

                // 分片时，所有片的共同标识
                $allBlockUuid = $operate_staff_id . $originalName . $uuid . $count;
                Log::info('上传文件日志-分片时，所有片的共同标识',[$allBlockUuid]);//


                $temFile = [
                    'extension' => $ext,
                    'hashname' => $hashname,
                    'name' => $name,
                ];
                // 文件信息 {"extension":"jpg","hashname":"vggBtITcoKWwYCcRhheEIBsEr6upX16WnYTadvFY.jpeg","name":"7.jpg"}
                Log::info('上传文件日志-文件信息',$temFile);

                $fileArr =[
                    'name' => $name,// 文件名称
                    'allBlockUuid' => $allBlockUuid,// 分片时，所有片的共同标识
                    'ext' => $ext,// 文件扩展名
                    'mime_type' => $type,// 资源mime类型
                    'size' => $size,// 文件大小
                    'count' => $count,// 分片总数量 3
                    'saveData' => [// 要保存的一维数据
                        'resource_note' => $resource_note,
                    ],
                ];
                Log::info('上传文件日志-fileArr',$fileArr);
                //直接保存
                if( ($num == $count &&  $count <= 1) || ($num ==0 && $count == 1)){
                    return self::saveFile($request, $controller, 1, $company_id, $resource_type, $photo, $fileArr);
                }else{
                    //分片临时文件名
                    $filename = md5($allBlockUuid).'-'.($num + 1).'.tmp';
                    //上传目录
                    $path_name = UploadFile::$source_tmp_path . $filename;// 'uploads/tmp/'.$filename;
                    if(UploadFile::$cache_block == 2){
                        //方式一、保存临时文件
                        // $bool = Storage::disk('tmp')->put($filename, file_get_contents($realPath));
                         $store_result = $photo->storeAs(UploadFile::$source_tmp_path, $filename);// 保存片文件
                    }else{
                        // 方式二、将内容写入缓存
                        $publicPath = Tool::getPath('public');
                        //打开临时文件
                        // $handle = fopen($publicPath . $path_name,"rb");
                        $handle = fopen($realPath,"rb");
                        //读取临时文件 写入最终文件
                        Tool::setRedis(static::getProjectKeyPre(1) . 'tmpFilesBin', md5($path_name), fread($handle, filesize($realPath)), 60*20, 2); // 5分钟
                        //关闭句柄 不关闭删除文件会出现没有权限
                        fclose($handle);
                    }

                    // 缓存0的扩展名
                    if ($num == 0){
                        Tool::setRedis(static::getProjectKeyPre(1) . 'extend', md5($allBlockUuid), $ext, 60*20, 2); // 5分钟
                    }
                    // 文件大小处理
                    $allSize = Tool::getRedis(static::getProjectKeyPre(1) . 'size' . md5($allBlockUuid), 2);
                    if(!is_numeric($allSize)) $allSize = 0;
                    Tool::setRedis(static::getProjectKeyPre(1) . 'size', md5($allBlockUuid), $allSize + $size, 60*20, 2); // 5分钟

                    // 缓存分存临时文件名
                    $tmpFiles = Tool::getRedis(static::getProjectKeyPre(1) . 'tmpFiles' . md5($allBlockUuid), 2);
                    if(!is_array($tmpFiles)) $tmpFiles = [];
                    array_push($tmpFiles, $path_name);

                    Tool::setRedis(static::getProjectKeyPre(1) . 'tmpFiles', md5($allBlockUuid), $tmpFiles, 60*20, 2); // 5分钟
                    Log::info('上传文件日志-count($tmpFiles)',[count($tmpFiles)]);
                    Log::info('上传文件日志-$count',[$count]);
                    //当分片上传完时 合并
                    // if(($num + 1) == $count){
                    if( count($tmpFiles) >= $count){// 因为可能是无序的，所以只能通过总数量来判断
                        Log::info('上传文件日志-当分片上传完时 合并',[]);
                        // 扩展名和文件大小处理
                        $fileArr = array_merge($fileArr, [
                            'ext' => Tool::getRedis(static::getProjectKeyPre(1) . 'extend' . md5($allBlockUuid), 2),// 文件扩展名
                            'size' => Tool::getRedis(static::getProjectKeyPre(1) . 'size' . md5($allBlockUuid), 2),// 文件大小
                        ]);
                        return self::saveFile($request, $controller, 2, $company_id, $resource_type, $photo, $fileArr);
                    }else{
                        return [
                            'id' => 0,// 资源id
                            'name' => $name,// 文件名
                            'savePath' => $path_name,// 保存路径 /结束
                            'saveName' => $filename,// 保存文件名称
                            'store_result' => $path_name,// storeAs
                            // 'info' => [],// 资源表记录 一维
                            'resource_size' => $size,// 文件大小
                            'resource_size_format' => Tool::formatBytesSize($size, 2),// 文件大小
                            'resource_file_extension' => $ext,// 扩展名
                            'resource_mime_type' => $type,// 资源mime类型
                        ];
                    }
                }
            }else{
                throws('请选择要上传的文件！');
            }
        } catch ( \Exception $e) {
            if(isset($allBlockUuid)){
                // 缓存分存临时文件名
                $tmpFiles = Tool::getRedis(static::getProjectKeyPre(1) . 'tmpFiles' . md5($allBlockUuid), 2);
                if(!is_array($tmpFiles)) $tmpFiles = [];
                if(!empty($tmpFiles)) Log::info('上传文件日志-分片失败，删除临时文件',[$tmpFiles]);
                foreach($tmpFiles as $tmp_files){
                    if(UploadFile::$cache_block == 2){
                        // 方式一、删除临时文件
                         @unlink($tmp_files);
                    }else{
                        // 方式二、删除缓存
                        Tool::setRedis(static::getProjectKeyPre(1) . 'tmpFilesBin', md5($tmp_files), '', 2, 2); // 2 秒
                    }
                }

                // 清除缓存
                // 缓存0的扩展名
                Tool::setRedis(static::getProjectKeyPre(1) . 'extend', md5($allBlockUuid), '', 2, 2); // 2秒
                // 文件大小处理
                Tool::setRedis(static::getProjectKeyPre(1) . 'size', md5($allBlockUuid), 0, 2, 2); // 2秒
                // 缓存分存临时文件名
                Tool::setRedis(static::getProjectKeyPre(1) . 'tmpFiles', md5($allBlockUuid), [], 2, 2); // 2秒

            }
            throws($e->getMessage());
        }
    }

    /**
     * 保存文件
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $operate_type 操作类型 1 单文件保存 2 分片文件保存
     * @param int $company_id 企业id
     * @param int $resource_type 资源类型
     * @param object $photo 上传文件对象
     * @param array $fileArr 文件信息
        $fileArr =[
            'name' => '',// 文件名称
            'allBlockUuid' => '',// 分片时，所有片的共同标识
            'ext' => 'jpg',// 文件扩展名
            'size' => '',// 文件大小
            'count' => '',// 分片总数量 3
            'saveData' => [// 要保存的一维数据
                // 'resource_note' => $resource_note,
            ],
        ];
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function saveFile(Request $request, Controller $controller, $operate_type = 1, $company_id, $resource_type, &$photo = null, $fileArr = []){

        $name = $fileArr['name'] ?? '';
        $allBlockUuid = $fileArr['allBlockUuid'] ?? '';
        $ext = $fileArr['ext'] ?? '';
        $mime_type = $fileArr['mime_type'] ?? '';// 资源mime类型
        $size =  $fileArr['size'] ?? 0;
        $count =  $fileArr['count'] ?? 0;
        $saveData =  $fileArr['saveData'] ?? [];

        $filePathArr = UploadFile::getFilePath($company_id, $resource_type, 1, [1,2,3,4], '', '', $ext, $size);
        $publicPath = $filePathArr['publicPath'] ?? '';// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
        $savePath = $filePathArr['savePath'] ?? '';// 文件目录 '/resource/company/1/images/2019/10/04/'
        $saveName = $filePathArr['saveName'] ?? '';// 文件名  20191003121326d710d554edce12a1.png
        $files_names = $filePathArr['files_names'] ?? '';//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        $full_names = $filePathArr['full_names'] ?? '';// 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        // 生成保存路径
//        $savePath = UploadFile::$source_path . $company_id . '/';
//
//        // $resourceTypeArr = self::$resource_type[$resource_type] ?? [];
//        $resourceTypeArr = UploadFile::$resource_type[$resource_type] ?? [];
//        if(empty($resourceTypeArr)) throws('不明确的资源类型!');
//        $typeName = $resourceTypeArr['name'] ?? '';// 类型名称
//        $typeExt = $resourceTypeArr['ext'] ?? [];// 扩展名
//        $typeDir = $resourceTypeArr['dir'] ?? '';// 文件夹名称
//        $typeMaxSize = $resourceTypeArr['maxSize'] ?? '0.5';// 文件最大值 单位 M
//        if(!is_numeric($typeMaxSize)) $typeMaxSize = 0.5;// 0.5M
//        $typeOther = $resourceTypeArr['other'] ?? [];// 其它各自类型需要判断的指标
//        if(!in_array($ext , $typeExt)) throws($typeName . '扩展名必须为[' . implode('、', $typeExt) . ']');
//        //这里可根据配置文件的设置，做得更灵活一点
//        if($size > $typeMaxSize * 1024 * 1024){
//            throws('上传文件不能超过[' . $typeMaxSize . 'M]');
//        }
//
//        if($typeDir != '' ) $savePath .=   $typeDir . '/';// 类型文件夹
//
//        //if(is_numeric($pro_unit_id)){
//        //    $savePath .=   'pro' . $pro_unit_id . '/';
//        //}
//
//        $savePath .= date('Y/m/d/',time());
//
//        $saveName = Tool::createUniqueNumber(30) .'.' . $ext;
        //$store_result = $photo->store('photo');
        try{
            if($operate_type == 1 ){// 小于等于0时，为没有分片上传 !empty($photo)
                // $savePath = rtrim($savePath,'/');
                // throws(rtrim($savePath,'/') . '---' . $saveName);
                // $store_result = $photo->storeAs($savePath, $saveName);// 返回 "resource/company/1/pro0/2018/10/13//20181013182843dc1a9783e212840f.jpeg"
                $store_result = $photo->storeAs(rtrim($savePath,'/'), $saveName);
            }else{
//                $publicPath = Tool::getPath('public');
                //最后合成后的名字及路径
                // $files_names = 'uploads/'.date("YmdHis",time()).rand(100000,999999).'.'.$ext;
//                makeDir($publicPath . $savePath);// 创建目录
//                $files_names = $savePath . $saveName;
                //打开文件
                $fp = fopen($publicPath . $files_names,"ab");
                //循环读取临时文件，写入最终文件
                for($i = 0; $i < $count; $i++){
                    //临时文件路径及名称
                    $tmp_files = UploadFile::$source_tmp_path . md5($allBlockUuid).'-'.($i+1).'.tmp'; // 'uploads/tmp/'.md5($allBlockUuid).'-'.($i+1).'.tmp';
                    if(UploadFile::$cache_block == 2){
                        //方式一、打开临时文件
                        $handle = fopen($publicPath . $tmp_files,"rb");
                        //读取临时文件 写入最终文件
                         fwrite($fp, fread($handle, filesize($publicPath . $tmp_files)));
                        //关闭句柄 不关闭删除文件会出现没有权限
                         fclose($handle);
                        //方式一、删除临时文件
                         @unlink($tmp_files);
                    }else{
                        // 方式二、从缓存读取
                        fwrite($fp, Tool::getRedis(static::getProjectKeyPre(1) . 'tmpFilesBin' . md5($tmp_files), 2));
                        // 方式二、删除缓存
                        Tool::setRedis(static::getProjectKeyPre(1) . 'tmpFilesBin', md5($tmp_files), '', 2, 2); // 2 秒
                    }
                }
                //关闭句柄
                fclose($fp);
                $store_result = trim($files_names,'/');
                Log::info('上传文件日志-完成保存图片-分片',[$store_result]);
                // 清除缓存
                // 缓存0的扩展名
                Tool::setRedis(static::getProjectKeyPre(1) . 'extend', md5($allBlockUuid), '', 2, 2); // 2秒
                // 文件大小处理
                Tool::setRedis(static::getProjectKeyPre(1) . 'size', md5($allBlockUuid), 0, 2, 2); // 2秒
                // 缓存分存临时文件名
                Tool::setRedis(static::getProjectKeyPre(1) . 'tmpFiles', md5($allBlockUuid), [], 2, 2); // 2秒
            }
            // 保存资源
            $saveData = array_merge($saveData ,[
                'resource_name' => $name,
                'resource_type' => $resource_type,
                'resource_url' => $savePath . $saveName,
                'resource_size' => $size,
                'resource_size_format' => Tool::formatBytesSize($size, 2),
                'resource_ext' => $ext,
                'resource_mime_type' => $mime_type,
            ]);

            // 加入上传者用户类型
            $upload_admin_type = $controller->admin_type ?? 0;
            if(is_numeric($upload_admin_type) && $upload_admin_type > 0){
                $saveData['ower_type'] = $upload_admin_type;
                // 加入上传者id
                $upload_user_id = $controller->user_id ?? 0;
                if(is_numeric($upload_user_id) && $upload_user_id > 0) $saveData['ower_id'] = $upload_user_id;
            }

            Log::info('上传文件日志-保存数据',[$saveData]);
            // $reslut = CommonBusiness::createApi(self::$model_name, $saveData, $company_id);
            $id = 0;
            $extParams = [
                // 'judgeDataKey' => 'replace',// 数据验证的下标
            ];
            $reslut = self::replaceById($request, $controller, $saveData, $id, $extParams);
            // $id = $reslut['id'] ?? '';
            Log::info('上传文件日志-reslut',[$reslut]);
            if(empty($id)){
                Log::info('上传文件日志-保存资源失败',[$id]);
                throws('保存资源失败!');
            }
        } catch ( \Exception $e) {

            throws($e->getMessage());
        }
        return [
            'id' => $id,// 资源id
            'name' => $name,// 文件名
            'savePath' => $savePath,// 保存路径 /结束
            'saveName' => $saveName,// 保存文件名称
            'store_result' => $store_result,// storeAs
            // 'info' => $reslut,// 资源表记录 一维
            'resource_size' => $size,// 文件大小
            'resource_size_format' => Tool::formatBytesSize($size, 2),// 文件大小
            'resource_file_extension' => $ext,// 扩展名
            'resource_mime_type' => $mime_type,// 资源mime类型
        ];
    }

    /**
     * 上传文件 --plupload
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $resource_type 资源类型 1:图片;2:excel
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function filePlupload(Request $request, Controller $controller, $resource_type = 1)
    {
        Log::info('上传文件日志-resource_type',[$resource_type]);// 分片序号 [0]
        try{
            $result = self::uploadFile($request, $controller, $resource_type);
            $sucArr = [
                'result' => 'ok',// 文件上传成功
                'id' => $result['id'] , // 文件在服务器上的唯一标识  8324
                'url'=> url($result['savePath'] . $result['saveName']),//'http://example.com/file-10001.jpg',// 文件的下载地址  "http://dogtools.admin.cunwo.net/resource/company/1/images/2020/06/21/20200621143249a9f868c5b4baf7cb.png"
                'store_result' => $result['store_result'],// "resource/company/1/images/2020/06/21//20200621143249a9f868c5b4baf7cb.png"
                'data_list' => [
                    array_merge([
                        'id' => $result['id'],// 8324
                        'resource_name' => $result['name'],// "测试名称"
                        'resource_url' => url($result['savePath'] . $result['saveName']),// "http://dogtools.admin.cunwo.net/resource/company/1/images/2020/06/21/20200621143249a9f868c5b4baf7cb.png",
                        'created_at' =>  date('Y-m-d H:i:s',time()),// "2020-06-21 14:32:49"
                        'column_type' => 0,//'',
                        'column_id' => 0,// '',
                        'resource_url_old' => $result['savePath'] . $result['saveName'],// /resource/company/47/pdf/2020/10/24/202010241053524a8f99d830f58729.pdf
                        'resource_size' => $result['resource_size'],
                        'resource_size_format' => $result['resource_size_format'],
                        'resource_file_extension' => $result['resource_file_extension'],
                        'resource_mime_type' => $result['resource_mime_type'],
                        // 'resource_file_name' => '',// 202010241053524a8f99d830f58729.pdf
                        // 'resource_url_format' => '', // "http://qualitycontrol.admin.cunwo.net/resource/company/47/pdf/2020/10/24/202010241053524a8f99d830f58729.pdf"
                    ], Tool::formatUrlByExtension(url($result['savePath'] . $result['saveName'])))
                ],
            ];
            Log::info('上传文件日志-成功',$sucArr);
            return $sucArr;
        } catch ( \Exception $e) {
            $errArr = [
                'result' => 'failed',// 文件上传失败
                'message' => $e->getMessage(),//'文件内容包含违规内容',//用于在界面上提示用户的消息
            ];
            Log::info('上传文件日志-失败',$errArr);
            return $errArr;
        }
    }

    /**
     * 上传文件 --plupload
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $resource_type 资源类型 1:图片;2:excel
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function fileSingleUpload(Request $request, Controller $controller, $resource_type = 1)
    {
        try{
            $result = self::uploadFile($request, $controller, $resource_type);
            $sucArr = [
                'id' => $result['id'] , // 文件在服务器上的唯一标识 8324
                'url'=> url($result['savePath'] . $result['saveName']),//'http://example.com/file-10001.jpg',// 文件的下载地址  "http://dogtools.admin.cunwo.net/resource/company/1/images/2020/06/21/20200621143249a9f868c5b4baf7cb.png"
                'filePath' => $result['savePath'] . $result['saveName'],// /resource/company/1/excel/2020/06/21/202006211522330a4022f5282c1530.xlsx
                'store_result' => $result['store_result'],// "resource/company/1/excel/2020/06/21/202006211522330a4022f5282c1530.xlsx"
                'resource_name' => $result['name'],// "测试名称"
                'created_at' =>  date('Y-m-d H:i:s',time()),// "2020-06-21 14:32:49"
            ];
            Log::info('上传文件日志-成功',$sucArr);
            return ajaxDataArr(1, $sucArr, '');

        } catch ( \Exception $e) {
            Log::info('上传文件日志-失败',[$e->getMessage()]);
            return ajaxDataArr(0, null,$e->getMessage());
        }
    }

    /**
     * 获得列表数据--根据图片ids
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param string / array $ids  查询的id ,多个用逗号分隔, 或数组
     * @param string $idFieldName id的字段名称 默认 id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getResourceByIds(Request $request, Controller $controller, $ids = '', $idFieldName = 'id', $notLog = 0){
        $data_list = static::getListByIds($request, $controller, $ids, [], [], $idFieldName, $notLog);
//        $reList = [];
//        foreach($data_list as $k => $v){
//            $temArr = [
//                'id' => $v['id'],
//                'resource_name' => $v['resource_name'],
//                'resource_url' => url($v['resource_url']),
//                'created_at' => $v['created_at'],
//            ];
//            array_push($reList, $temArr);
//        }
        $reList = Tool::formatResource($data_list, 2);
        return $reList;
    }


}
