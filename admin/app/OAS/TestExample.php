<?php
namespace App\OAS;


class TestExample
{
    /**
     * @  OA\Post(
     *   path="/api/exampleaa/{id}",
     *   summary="具体例が無かったので寄せ集めてみた",
     *   @  OA\RequestBody(
     *     required=true,
     *     @  OA\JsonContent(
     *       type="object",
     *       required={"number", "text"},
     *       @  OA\Property(
     *         property="number",
     *         type="integer",
     *         format="int32",
     *         example=1,
     *         description="リクエストボディのjsonのプロパティの例"
     *       ),
     *       @  OA\Property(
     *         property="text",
     *         type="string",
     *         example="text",
     *         description="リクエストボディのjsonのプロパティの例"
     *       )
     *     )
     *   ),
     *   @  OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="パスからのパラメータ例",
     *     @  OA\Schema(type="string")
     *   ),
     *   @  OA\Parameter(
     *     name="queryString",
     *     in="query",
     *     required=true,
     *     description="クエリーストリングからのパラメータ例",
     *     @  OA\Schema(type="string")
     *   ),
     *   @  OA\Response(
     *     response=200,
     *     description="OK",
     *     @  OA\JsonContent(
     *       type="object",
     *       @  OA\Property(
     *         property="message",
     *         type="string",
     *         description="レスポンスボディjsonパラメータの例"
     *       )
     *     )
     *   ),
     *   @  OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @  OA\JsonContent(
     *       type="object",
     *       @  OA\Property(
     *         property="message",
     *         type="string",
     *         description="レスポンスボディjsonパラメータの例"
     *       )
     *     )
     *   ),
     *   @  OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @  OA\JsonContent(
     *       type="object",
     *       @  OA\Property(
     *         property="message",
     *         type="string",
     *         description="レスポンスボディjsonパラメータの例"
     *       )
     *     )
     *   ),
     *   @  OA\Response(
     *     response="default",
     *     description="Unexpected Error",
     *     @  OA\JsonContent(
     *       type="object",
     *       @  OA\Property(
     *         property="message",
     *         type="string",
     *         description="レスポンスボディjsonパラメータの例"
     *       )
     *     )
     *   )
     * )
     */
//    public function example($id)
//    {
//        // example
//    }

    /**
     * @  OA\Post(
     *   path="/api/example/{id}",
     *   summary="具体例が無かったので寄せ集めてみた",
     *   @  OA\RequestBody(
     *     required=true,
     *     @  OA\JsonContent(
     *       type="object",
     *       required={"number", "text"},
     *       @  OA\Property(
     *         property="number",
     *         type="integer",
     *         format="int32",
     *         example=1,
     *         description="リクエストボディのjsonのプロパティの例"
     *       ),
     *       @  OA\Property(
     *         property="text",
     *         type="string",
     *         example="text",
     *         description="リクエストボディのjsonのプロパティの例"
     *       )
     *     )
     *   ),
     *   @  OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="パスからのパラメータ例",
     *     @  OA\Schema(type="string")
     *   ),
     *   @  OA\Parameter(
     *     name="queryString",
     *     in="query",
     *     required=true,
     *     description="クエリーストリングからのパラメータ例",
     *     @  OA\Schema(type="string")
     *   ),
     *   @  OA\Response(
     *     response=200,
     *     description="OK",
     *     @  OA\JsonContent(@  OA\Property(ref="#/components/schemas/message"))
     *   ),
     *   @  OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @  OA\JsonContent(@  OA\Property(ref="#/components/schemas/message"))
     *   ),
     *   @  OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @  OA\JsonContent(@  OA\Property(ref="#/components/schemas/message"))
     *   ),
     *   @  OA\Response(
     *     response="default",
     *     description="Unexpected Error",
     *     @  OA\JsonContent(@  OA\Property(ref="#/components/schemas/message"))
     *   )
     * )
     */
//    public function example($id)
//    {
//        // example
//    }
}