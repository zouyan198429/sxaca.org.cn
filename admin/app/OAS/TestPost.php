<?php
namespace App\OAS;

class TestPost
{
    /**
     * @  OA\Post(
     *     path="路径，比如：/test/push",
     *     tags={"标签"},
     *     summary="描述信息",
     *     @  OA\RequestBody(
     *         @  OA\MediaType(
     *             mediaType="application/json  请求格式",
     *             @  OA\Schema(
     *                 required={"id"},
     *                 @  OA\Property(
     *                     property="id",
     *                     type="int",
     *                     description="id 参数"
     *                 ),
     *                 @  OA\Property(
     *                     property="sess",
     *                     type="string",
     *                     description="session 参数"
     *                 ),
     *                 example={"id": 1, "sess": "wdff"}
     *             )
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="successful",
     *         @  OA\JsonContent(
     *          type="array|object|string",
     *          @  OA\Items(
     *          	@  OA\Property(property="seminarName", type="string", description="主会名"),
     *          	@  OA\Property(property="seminarId", type="int", description="主会id"),
     *          	@  OA\Property(
     *              	property="items",
     *              	type="object",
     *                  	@  OA\Property(property="subSeminarId", type="int", description="分会id"),
     *                  	@  OA\Property(property="subName", type="string", description="分会名")
     *
     *          	)
     * 			)
     *         )
     *
     *     )
     * )
     */

    /**
     *  https://whatsupkorea.com/2018/10/06/laravel-%ED%94%84%EB%A1%9C%EC%A0%9D%ED%8A%B8%EC%97%90%EC%84%9C-openapiswagger-%EC%82%AC%EC%9A%A9%ED%95%98%EA%B8%B0/
     * @  OA\Post(
     *     path="/api/v2/type",
     *     tags={"Match"},
     *     summary="介绍",
     *     description="说明部分",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="matchType",
     *     @  OA\Response(response=200,
     *         ref="schemas/responses/match-type.json"
     *     ),
     *     @  OA\Response(response="401",
     *         ref="schemas/responses/error-auth-invalid.json"
     *     ),
     *     @  OA\Response(response="422",
     *         ref="schemas/responses/error-unprocessable-entity.json"
     *     ),
     *     @  OA\RequestBody(
     *         required=true,
     *         @  OA\MediaType(
     *             mediaType="application/json",
     *             @  OA\Schema(
     *                 @  OA\Property(
     *                     description="MatchDto",
     *                     property="match_dto",
     *                     ref="schemas/match-dto.json"
     *                 ),
     *                 @  OA\Property(
     *                     description="拼写介绍类型",
     *                     property="match_type",
     *                     type="string",
     *                     enum={
     *                         "ugly",
     *                         "good-looking"
     *                     }
     *                 ),
     *                 type="object",
     *             )
     *         )
     *     )
     * )
     */
//    public function match(MatchTypeRequest $request, Responder $responder)
//    {
//        :
//    }
}