<?php
namespace App\OAS\Object;

use OpenApi\Annotations as OA;

class captcha
{

    //##################属性#######################################################

    /**
     * 图形验证码属性-来源
     * @  OA\Schema(
     *   schema="Schema_Object_captcha_resource",
     *   type="string",
     *   title="图形验证码属性-来源",
     *   description="图形验证码属性-来源",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="",
     * )
     *
     */

    /**
     * 图形验证码属性-是否敏感的
     * @OA\Schema(
     *   schema="Schema_Object_captcha_sensitive",
     *   type="boolean",
     *   title="图形验证码属性-是否敏感的",
     *   description="图形验证码属性-是否敏感的:true-敏感的,false-非敏感的",
     *   default="true",
     *   example="true",
     * )
     *
     */

    /**
     * 图形验证码属性-验证码的hash值
     * @OA\Schema(
     *   schema="Schema_Object_captcha_key",
     *   type="string",
     *   title="图形验证码属性-验证码的hash值",
     *   description="图形验证码属性-验证码的hash值",
     *   default="",
     *   minLength=0,
     *   maxLength=200,
     *   example="$2y$10$4UTAMBN0hd1V6wP3bVmbhu/PQf/y9Mz6FhFJ/VtU8CkwmRkBF8/cy",
     * )
     *
     */

    /**
     * 图形验证码属性-base64后的图片
     * @OA\Schema(
     *   schema="Schema_Object_captcha_img",
     *   type="string",
     *   title="图形验证码属性-base64后的图片",
     *   description="图形验证码属性-base64后的图片",
     *   default="",
     *   minLength=0,
     *   maxLength=100000,
     *   example="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAkCAYAAABCKP5eAAAA......",
     * )
     *
     */

    // 其它表会用到的属性字段

    /**
     * 图形验证码属性-id
     * @  OA\Schema(
     *   schema="Schema_Object_captcha_id",
     *   type="integer",
     *   format="int32",
     *   title="图形验证码属性-id",
     *   description="图形验证码属性-id",
     *   default=0,
     *   minimum="0",
     *   example="1",
     * )
     *
     */


    //##################请求参数#######################################################
    /**
     * 模糊查询字段名--具体的表模型用
     * @  OA\Parameter(
     *      parameter="Parameter_Object_captcha_field",
     *      name="field",
     *      in="query",
     *      description="模糊查询字段名",
     *      required=false,
     *      deprecated=false,
     *      @ OA\Schema(
     *          type="string",
     *          default="字段名1",
     *          enum={"字段名1","字段名2"},
     *          example="字段名1",
     *      )
     * ),
     *
     */

    /**
     * 验证码
     * @OA\Parameter(
     *      parameter="Parameter_Object_captcha_captcha_code",
     *      name="captcha_code",
     *      description="验证码",
     *      required=true,
     *      in="query",
     *      deprecated=false,
     *      @OA\Schema(
     *          type="string",
     *          minLength=3,
     *          maxLength=10,
     *      )
     * ),
     *
     */

    /**
     * 验证码的hash值
     * @OA\Parameter(
     *      parameter="Parameter_Object_captcha_captcha_key",
     *      name="captcha_key",
     *      description="验证码的hash值(生成图形验证码接口返回的)",
     *      required=true,
     *      in="query",
     *      deprecated=false,
     *      @OA\Schema(
     *          type="string",
     *          minLength=0,
     *          maxLength=150,
     *      )
     * ),
     *
     *      @ OA\Schema(ref =" #/ components/schemas/Schema_Object_captcha_key")
     */
    //##################对象属性集#######################################################
    // 有所有字段的对象
    /**
     * @OA\Schema(
     *     schema="Schema_Object_captcha_obj",
     *     title="收费标准",
     *     description="收费标准列表",
     *     required={},
     *     @OA\Property(property="sensitive", ref="#/components/schemas/Schema_Object_captcha_sensitive"),
     *     @OA\Property(property="key", ref="#/components/schemas/Schema_Object_captcha_key"),
     *     @OA\Property(property="img", ref="#/components/schemas/Schema_Object_captcha_img"),
     * )
     */

    //##################请求主体对象#######################################################
    /**
     * 单条记录请求体
     *
     * @OA\RequestBody(
     *     request="RequestBody_Object_info_captcha",
     *     description="单个增加",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/Schema_Object_captcha_obj"),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(property="info", ref="#/components/schemas/Schema_Object_captcha_obj"),
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
     *     request="RequestBody_Object_multi_captcha",
     *     description="批量增加",
     *     required=true,
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *              property="data_list",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Schema_Object_captcha_obj"),
     *          ),
     *     ),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="data_list",
     *                  @OA\Property(property="info", ref="#/components/schemas/Schema_Object_captcha_obj"),
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
     *         response="Response_Object_list_captcha",
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
     *                      @OA\Items(ref="#/components/schemas/Schema_Object_captcha_obj"),
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
     *                          @OA\Items(ref="#/components/schemas/Schema_Object_captcha_obj"),
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
     *         response="Response_Object_info_captcha",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_Object_captcha_obj"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_Object_captcha_obj",),
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
     *         response="Response_Object_result_captcha",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_Object_captcha_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_Object_captcha_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */

}
