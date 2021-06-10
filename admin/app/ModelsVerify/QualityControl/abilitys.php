<?php
namespace App\ModelsVerify\QualityControl;

use OpenApi\Annotations as OA;

class abilitys extends BaseDBVerify
{
    public static $model_name = 'QualityControl\Abilitys';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = 'abilitys';// 数据表名称
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
        // $tableConfig = brands::getVerifyRuleArr($dbFileTag);
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
     * 个人访问客户端属性-品牌名称
     * @ OA\Schema(
     *   schema="Schema_QualityControl_abilitys_brand_name",
     *   type="string",
     *   title="个人访问客户端属性-品牌名称",
     *   description="个人访问客户端属性-品牌名称",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="川渝人家",
     * )
     *
     */

    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @ OA\Parameter(
     *      parameter="Schema_QualityControl_abilitys_brand_name_optional",
     *      name="type_name",
     *      in="query",
     *      description="类型名称",
     *      required=false,
     *      deprecated=false,
     *      @ OA\Schema(ref="#/components/schemas/Schema_QualityControl_abilitys_brand_name")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @ OA\Parameter(
     *      parameter="Schema_QualityControl_abilitys_brand_name_required",
     *      name="type_name",
     *      in="query",
     *      description="类型名称",
     *      required=true,
     *      deprecated=false,
     *      @ OA\Schema(ref="#/components/schemas/Schema_QualityControl_abilitys_brand_name")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    // 其它表会用到的属性字段

    /**
     * 个人访问客户端属性-id
     * @OA\Schema(
     *   schema="Schema_QualityControl_abilitys_id",
     *   type="integer",
     *   format="int32",
     *   title="个人访问客户端属性-id",
     *   description="个人访问客户端属性-id",
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
     *      parameter="Schema_QualityControl_abilitys_id_optional",
     *      name="id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_abilitys_id")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_abilitys_id_required",
     *      name="id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_abilitys_id")
     * ),
     *
     *
     */

    // **_id 类型 ---别的表引用的
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_abilitys_id_optional_quote",
     *      name="template_type_id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_abilitys_id")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_abilitys_id_required_quote",
     *      name="template_type_id",
     *      in="query",
     *      description="模板库分类-id",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_abilitys_id")
     * ),
     *
     *
     */


    //##################请求参数#######################################################
    /**
     * 模糊查询字段名--具体的表模型用
     * @OA\Parameter(
     *      parameter="Parameter_QualityControl_abilitys_field",
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
     *     schema="Schema_QualityControl_abilitys_obj",
     *     title="个人访问客户端",
     *     description="个人访问客户端列表",
     *     required={},
     *     @OA\Property(property="id", ref="#/components/schemas/common_Schema_id"),
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
     *     request="RequestBody_QualityControl_info_abilitys",
     *     description="单个增加",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(property="info", ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
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
     *     request="RequestBody_QualityControl_multi_abilitys",
     *     description="批量增加",
     *     required=true,
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *              property="data_list",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
     *          ),
     *     ),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="data_list",
     *                  @OA\Property(property="info", ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
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
     *         response="Response_QualityControl_list_abilitys",
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
     *                      @OA\Items(ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
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
     *                          @OA\Items(ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
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
     *         response="Response_QualityControl_info_abilitys",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_QualityControl_abilitys_obj",),
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
     *         response="Response_QualityControl_result_abilitys",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_QualityControl_abilitys_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */

}
