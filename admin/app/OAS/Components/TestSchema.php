<?php
namespace App\OAS\Components;

use OpenApi\Annotations as OA;
/**
 * @  OA\Schema(
 *   schema="product_id",
 *   type="integer",
 *   format="int32",
 *   description="参照用"
 * )
 */

/**
 * @  OA\Schema(
 *   schema="message",
 *   type="object",
 *   description="message",
 *   @  OA\Property(
 *     property="message",
 *     type="string",
 *     description="メッセージ"
 *   )
 * ),
 * @  OA\Schema(
 *   schema="user",
 *   type="object",
 *   description="user",
 *   required={"message", "user"},
 *   @  OA\Property(property="message", ref="#/components/schemas/message"),
 *   @  OA\Property(
 *     property="user",
 *     type="object",
 *     description="ユーザー",
 *     required={"id", "name"},
 *     @  OA\Property(
 *       property="id",
 *       type="string",
 *       description="ID"
 *     ),
 *     @  OA\Property(
 *       property="name",
 *       type="string",
 *       description="名前"
 *     )
 *   )
 * )
 */
class TestSchema
{

}