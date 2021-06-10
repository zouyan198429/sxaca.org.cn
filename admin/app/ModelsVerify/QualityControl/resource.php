<?php
namespace App\ModelsVerify\QualityControl;

use OpenApi\Annotations as OA;

class resource extends BaseDBVerify
{
    public static $model_name = 'QualityControl\Resource';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = 'resource';// 数据表名称
    // 需要从父的去掉的字段  -- 一维数组
    // 如 ['version_history_id', 'version_num_history']
    public static $delFields = [];

    /**
     * 获得验证规则
     * // 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择***
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getTableConfig($dbFileTag = 'models', $langModel = [], $langModelDB = [], $langTable = []){
        $tableConfig = [
            'fields' => [
                'id' => [
                    "field_name" => '',//  $langModel['field_names']['id'] ?? 'id',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'version_num' => [
                    "field_name" => '',//  $langModel['field_names']['version_num'] ?? '', //
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'version_history_id' => [
                    "field_name" => '',//  $langModel['field_names']['version_history_id'] ?? '', //
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'version_num_history' => [
                    "field_name" => '',//  $langModel['field_names']['version_num_history'] ?? '', //,
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'ower_type' => [
                    "field_name" => '',// '拥有者类型',
                    'data_type'=>'tinyint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([01248]|16|32|64)$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（1平台2城市分站4城市代理8商家16店铺32快跑人员64用户）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'ower_id' => [
                    "field_name" => '',// '拥有者',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'type_self_id' => [
                    "field_name" => '',// '类型自定义',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'type_self_id_history' => [
                    "field_name" => '',// '类型自定义历史',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'resource_name' => [
                    "field_name" => '',// '资源名称',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"500","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 500 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'resource_type' => [
                    "field_name" => '',// '资源类型',
                    'data_type'=>'smallint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([01248]|16|32|64|128|256|512|1024|2048|4096|8192|16384|32768|65536)$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（1图片）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'resource_note' => [
                    "field_name" => '',// '资源说明',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"2000","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 2000 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'resource_url' => [
                    "field_name" => '',// '资源地址',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"500","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 500 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'operate_staff_id' => [
                    "field_name" => '',//  $langModel['field_names']['operate_staff_id'] ?? '', // '操作员工',,
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'operate_staff_id_history' => [
                    "field_name" => '',//  $langModel['field_names']['operate_staff_id_history'] ?? '', // '操作员工历史',,
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'created_at' => [
                    "field_name" => '',//  $langModel['field_names']['created_at'] ?? '', // '操作日期',
                    'data_type'=>'timestamp',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"datatime","message" => '',// '{fieldName}' . ($langModel['valMustDateTime'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'updated_at' => [
                    "field_name" => '',//  $langModel['field_names']['updated_at'] ?? '', // '更新日期',
                    'data_type'=>'timestamp',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"datatime","message" => '',// '{fieldName}' . ($langModel['valMustDateTime'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
            ]
        ];
        // 如果用父类的，则在此指定父类
        // $tableConfig = projects::getVerifyRuleArr($dbFileTag);
        return $tableConfig;
    }

    /**
     * 单个数据表配置数组中，需要新加的字段配置
     *
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组中需要新加的字段配置-- field下标的值--数组
     * @author zouyan(305463219@qq.com)
     */
    public static function addFields($langModel = [], $langModelDB = [], $langTable = []){
        $addFields = [

        ];
        return $addFields;
    }

    //##################属性#######################################################

    /**
     * 资源属性-资源名称
     * @OA\Schema(
     *   schema="Schema_QualityControl_resource_resource_name",
     *   type="string",
     *   title="资源属性-资源名称",
     *   description="资源属性-资源名称",
     *   default="",
     *   minLength=0,
     *   maxLength=500,
     *   example="",
     * )
     *
     */

    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @ OA\Parameter(
     *      parameter="Schema_QualityControl_resource_brand_name_optional",
     *      name="type_name",
     *      in="query",
     *      description="类型名称",
     *      required=false,
     *      deprecated=false,
     *      @ OA\Schema(ref="#/components/schemas/Schema_QualityControl_resource_brand_name")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @ OA\Parameter(
     *      parameter="Schema_QualityControl_resource_brand_name_required",
     *      name="type_name",
     *      in="query",
     *      description="类型名称",
     *      required=true,
     *      deprecated=false,
     *      @ OA\Schema(ref="#/components/schemas/Schema_QualityControl_resource_brand_name")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    /**
     * 资源属性-资源类型1图片
     * @OA\Schema(
     *   schema="Schema_QualityControl_resource_resource_type",
     *   type="integer",
     *   format="int32",
     *   title="资源属性-资源类型",
     *   description="资源属性-资源类型:1图片",
     *   default=1,
     *   enum={"1"},
     *   example="1",
     * )
     *
     */

    /**
     * 资源属性-资源类型文字
     * @OA\Schema(
     *   schema="Schema_QualityControl_resource_resource_type_text",
     *   type="string",
     *   title="资源属性-资源类型文字",
     *   description="资源属性-资源类型文字",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="图片",
     * )
     *
     */

    /**
     * 资源属性-资源说明
     * @OA\Schema(
     *   schema="Schema_QualityControl_resource_resource_note",
     *   type="string",
     *   title="资源属性-资源说明",
     *   description="资源属性-资源说明",
     *   default="",
     *   minLength=0,
     *   maxLength=2000,
     *   example="",
     * )
     *
     */

    /**
     * 资源属性-资源地址
     * @OA\Schema(
     *   schema="Schema_QualityControl_resource_resource_url",
     *   type="string",
     *   title="资源属性-资源地址",
     *   description="资源属性-资源地址",
     *   default="",
     *   minLength=0,
     *   maxLength=500,
     *   example="",
     * )
     *
     */
    // 其它表会用到的属性字段

    /**
     * 资源属性-id
     * @OA\Schema(
     *   schema="Schema_QualityControl_resource_id",
     *   type="integer",
     *   format="int32",
     *   title="资源属性-id",
     *   description="资源属性-id",
     *   default=0,
     *   minimum="0",
     *   example="1",
     * )
     *
     */


    // id 类型 ---自已用的
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_resource_id_optional",
     *      name="id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_resource_id")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_resource_id_required",
     *      name="id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_resource_id")
     * ),
     *
     *
     */

    // **_id 类型 ---别的表引用的
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_resource_id_optional_quote",
     *      name="template_type_id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_resource_id")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_resource_id_required_quote",
     *      name="template_type_id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_resource_id")
     * ),
     *
     *
     */

    //##################请求参数#######################################################
    /**
     * 模糊查询字段名--具体的表模型用
     * @OA\Parameter(
     *      parameter="Parameter_QualityControl_resource_field",
     *      name="field",
     *      in="query",
     *      description="模糊查询字段名",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(
     *          type="string",
     *          default="字段名1",
     *          enum={"字段名1","字段名2"},
     *          example="字段名1",
     *      )
     * ),
     *
     */

    //##################对象属性集#######################################################
    // 有所有字段的对象
    /**
     * @OA\Schema(
     *     schema="Schema_QualityControl_resource_obj",
     *     title="资源",
     *     description="资源列表",
     *     required={},
     *     @OA\Property(property="id", ref="#/components/schemas/common_Schema_id"),
     *     @OA\Property(property="version_num", ref="#/components/schemas/common_Schema_version_num"),
     *     @OA\Property(property="version_history_id", ref="#/components/schemas/common_Schema_version_history_id"),
     *     @OA\Property(property="version_num_history", ref="#/components/schemas/common_Schema_version_num_history"),
     *     @OA\Property(property="ower_type", ref="#/components/schemas/common_Schema_QualityControl_ower_type"),
     *     @OA\Property(property="ower_type_text", ref="#/components/schemas/common_Schema_QualityControl_ower_type_text"),
     *     @OA\Property(property="ower_id", ref="#/components/schemas/common_Schema_QualityControl_ower_id"),
     *     @OA\Property(property="type_self_id", ref="#/components/schemas/Schema_QualityControl_resource_type_self_id"),
     *     @OA\Property(property="type_self_id_history", ref="#/components/schemas/Schema_QualityControl_resource_type_self_history_id"),
     *     @OA\Property(property="resource_name", ref="#/components/schemas/Schema_QualityControl_resource_resource_name"),
     *     @OA\Property(property="resource_type", ref="#/components/schemas/Schema_QualityControl_resource_resource_type"),
     *     @OA\Property(property="resource_type_text", ref="#/components/schemas/Schema_QualityControl_resource_resource_type_text"),
     *     @OA\Property(property="resource_note", ref="#/components/schemas/Schema_QualityControl_resource_resource_note"),
     *     @OA\Property(property="resource_url", ref="#/components/schemas/Schema_QualityControl_resource_resource_url"),
     *     @OA\Property(property="operate_staff_id", ref="#/components/schemas/common_Schema_operate_staff_id"),
     *     @OA\Property(property="operate_staff_id_history", ref="#/components/schemas/common_Schema_operate_staff_id_history"),
     *     @OA\Property(property="created_at", ref="#/components/schemas/common_Schema_created_at"),
     *     @OA\Property(property="updated_at", ref="#/components/schemas/common_Schema_updated_at"),
     * )
     */

    //##################请求主体对象#######################################################
    /**
     * 单条记录请求体
     *
     * @OA\RequestBody(
     *     request="RequestBody_QualityControl_info_resource",
     *     description="单个增加",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(property="info", ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *          ),
     *     )
     * )
     *
     */

    /**
     * 多条记录请求体
     *
     * @OA\RequestBody(
     *     request="RequestBody_QualityControl_multi_resource",
     *     description="批量增加",
     *     required=true,
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *              property="data_list",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *          ),
     *     ),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="data_list",
     *                  @OA\Property(property="info", ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *              ),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *          ),
     *     ),
     * )
     *
     *
     */

    //##################响应参数#######################################################

    //响应对象
    // response=200,
    /**
     *
     * {
     *      "apistatus": "0:失败；1：成功",
     *      "result": {
     *              "has_page": "true:有下一页数据；false:没有下一页数据",
     *          "data_list": [
     *              {
     *                  "id": "1",
     *                  "version_num": "1",
     *                  "history_id": "1",
     *                  "version_num_history": "1",
     *                  "brand_name": "川渝人家",
     *                  ......
     *                  "addr": "城关镇北大街明珠馨苑",
     *                  "operate_staff_id": "1",
     *                  "operate_staff_id_history": "1",
     *                  "created_at": "2019-12-04 12:31:30",
     *                  "updated_at": "2019-12-04 12:31:30"
     *              }
     *          ],
     *          "total": "1008",
     *          "page": "1",
     *          "pagesize": "15",
     *          "totalPage": "68",
     *          "pageInfo": "<li><a href='javascript:;' id='totalpage' totalpage='68' >总数:1008个 / 68页<> 跳转 </button></span>"
     *      },
     *      "errorMsg": "有错时的错误信息"
     * }
     *
     *
     * 列表页
     *     @OA\Response(
     *         response="Response_QualityControl_list_resource",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="has_page", ref="#/components/schemas/common_Schema_has_page"),
     *                  @OA\Property(
     *                      property="data_list",
     *                      type="array",
     *                      description="当前页列表数据",
     *                      @OA\Items(ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *                  ),
     *                  @OA\Property(property="total", ref="#/components/schemas/common_Schema_total"),
     *                  @OA\Property(property="page", ref="#/components/schemas/common_Schema_page"),
     *                  @OA\Property(property="pagesize", ref="#/components/schemas/common_Schema_pagesize"),
     *                  @OA\Property(property="totalPage", ref="#/components/schemas/common_Schema_totalPage"),
     *                  @OA\Property(property="pageInfo", ref="#/components/schemas/common_Schema_pageInfo"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="has_page", ref="#/components/schemas/common_Schema_has_page"),
     *                  @OA\Property(
     *                      property="data_list",
     *                      type="object",
     *                      description="当前页列表数据",
     *                      @OA\Property(
     *                          property="info",
     *                          type="array",
     *                          @OA\Items(ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *                      ),
     *                  ),
     *                  @OA\Property(property="total", ref="#/components/schemas/common_Schema_total"),
     *                  @OA\Property(property="page", ref="#/components/schemas/common_Schema_page"),
     *                  @OA\Property(property="pagesize", ref="#/components/schemas/common_Schema_pagesize"),
     *                  @OA\Property(property="totalPage", ref="#/components/schemas/common_Schema_totalPage"),
     *                  @OA\Property(property="pageInfo", ref="#/components/schemas/common_Schema_pageInfo"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */

    /**
     * 详情页
     * {
     *  "apistatus": "0:失败；1：成功",
     *   "result": {
     *      "info": {
     *          "id": "1",
     *          "version_num": "1",
     *          "history_id": "1",
     *           ......
     *          "addr": "城关镇北大街明珠馨苑",
     *          "operate_staff_id": "1",
     *          "operate_staff_id_history": "1",
     *          "created_at": "2019-12-04 12:31:30",
     *          "updated_at": "2019-12-04 12:31:30"
     *      }
     *   },
     *  "errorMsg": "有错时的错误信息"
     * }
     *     @OA\Response(
     *         response="Response_QualityControl_info_resource",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_QualityControl_resource_obj",),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */



    /**
     * 详情页--result对象
     * {
     *  "apistatus": "0:失败；1：成功",
     *   "result": {
     *          "id": "1",
     *          "version_num": "1",
     *          "history_id": "1",
     *           ......
     *          "addr": "城关镇北大街明珠馨苑",
     *          "operate_staff_id": "1",
     *          "operate_staff_id_history": "1",
     *          "created_at": "2019-12-04 12:31:30",
     *          "updated_at": "2019-12-04 12:31:30"
     *   },
     *  "errorMsg": "有错时的错误信息"
     * }
     *     @OA\Response(
     *         response="Response_QualityControl_result_resource",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_QualityControl_resource_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */

}
