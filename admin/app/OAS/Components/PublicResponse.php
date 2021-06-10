<?php
// 公共响应对象
namespace App\OAS\Components;

use OpenApi\Annotations as OA;


//##################响应参数#######################################################

//响应对象
// response=200,
/**
 * 修改
 * {
 *      "apistatus": "0:失败；1：成功",
 *      "result": "1",
 *      "errorMsg": "有错时的错误信息"
 * }
 *     @OA\Response(
 *         response="common_Response_modify",
 *         description="操作成功返回",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_id"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *        ),
 *        @OA\XmlContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_id"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *              @OA\Xml(
 *                  name="root",
 *                  wrapped=true
 *              ),
 *         ),
 *     )
 */

/**
 * 删除成功
 * {
 *      "apistatus": "0:失败；1：成功",
 *      "result": "1",
 *      "errorMsg": "有错时的错误信息"
 * }
 *     @OA\Response(
 *         response="common_Response_del",
 *         description="操作成功返回",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_deleted_nums"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *        ),
 *        @OA\XmlContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_deleted_nums"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *              @OA\Xml(
 *                  name="root",
 *                  wrapped=true
 *              ),
 *         ),
 *     )
 */


/**
 * 空result成功
 * {
 *      "apistatus": "0:失败；1：成功",
 *      "result": "1",
 *      "errorMsg": "有错时的错误信息"
 * }
 *     @OA\Response(
 *         response="common_Response_result_empty",
 *         description="操作成功返回",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_empty"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *        ),
 *        @OA\XmlContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_empty"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *              @OA\Xml(
 *                  name="root",
 *                  wrapped=true
 *              ),
 *         ),
 *     )
 */

/**
 * 空对象result成功
 * {
 *      "apistatus": "0:失败；1：成功",
 *      "result": "1",
 *      "errorMsg": "有错时的错误信息"
 * }
 *     @OA\Response(
 *         response="common_Response_result_empty_object",
 *         description="操作成功返回",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_empty_object"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *        ),
 *        @OA\XmlContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(property="result", ref="#/components/schemas/common_Schema_empty_object"),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *              @OA\Xml(
 *                  name="root",
 *                  wrapped=true
 *              ),
 *         ),
 *     )
 */

/**
 * 对象成功
 * {
 *      "apistatus": "0:失败；1：成功",
 *      "result": "1",
 *      "errorMsg": "有错时的错误信息"
 * }
 *     @OA\Response(
 *         response="common_Response_result_code_object",
 *         description="操作成功返回",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(
 *                  property="result",
 *                  type="object",
 *                  @OA\Property(
 *                      property="code", ref="#/components/schemas/common_Schema_code"),
 *                  ),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *        ),
 *        @OA\XmlContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(
 *                  property="result",
 *                  type="object",
 *                  @OA\Property(
 *                      property="code", ref="#/components/schemas/common_Schema_code"),
 *                  ),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *              @OA\Xml(
 *                  name="root",
 *                  wrapped=true
 *              ),
 *         ),
 *     )
 */

/**
 * 对象数据为data的数值成功
 * {
 *      "apistatus": "0:失败；1：成功",
 *      "result": "1",
 *      "errorMsg": "有错时的错误信息"
 * }
 *     @OA\Response(
 *         response="common_Response_result_data_int_object",
 *         description="操作成功返回",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(
 *                  property="result",
 *                  type="object",
 *                  @OA\Property(
 *                      property="data", ref="#/components/schemas/common_Schema_data_int"),
 *                  ),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *        ),
 *        @OA\XmlContent(
 *              type="object",
 *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
 *              @OA\Property(
 *                  property="result",
 *                  type="object",
 *                  @OA\Property(
 *                      property="data", ref="#/components/schemas/common_Schema_data_int"),
 *                  ),
 *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
 *              @OA\Xml(
 *                  name="root",
 *                  wrapped=true
 *              ),
 *         ),
 *     )
 */

// 响应对象

// response=400,
/**
 * 400 错误
 * @OA\Response(
 *      response="common_Response_err_400",
 *      description="Bad Request",
 * )
 */

// response=404,
/**
 * 404 错误
 * @OA\Response(
 *      response="common_Response_err_404",
 *      description="Page not found",
 * )
 */

// response=default,
/**
 * default 错误
 * @OA\Response(
 *      response="common_Response_err_default",
 *      description="Unexpected Error",
 * )
 */

class PublicResponse
{

}