<?php
namespace App\OAS;


/**
 * https://my.oschina.net/u/232595/blog/2998779
 * @  OA\OpenApi(
 *    @  OA\Info(
 *      version="1.0.0",
 *      title="api文档",
 *      description="api文档"
 *    ),
 *    @  OA\Server(
 *       description="测试环境",
 *       url="127.0.0.1",
 *   ),
 *    @  OA\Server(
 *       description="正式环境",
 *       url="10.0.0.1",
 *   )
 * )
 *
 * @  OA\Schema(
 *     schema="news",
 *     type="object",
 *     required={"title","content"},
 *     @  OA\Property(
 *         property="id",
 *         type="integer",
 *         description="编号"
 *     ),
 *     @  OA\Property(
 *         property="title",
 *         type="string",
 *         description="标题"
 *     ),
 *     @  OA\Property(
 *         property="content",
 *         type="string",
 *         description="正文"
 *     ),
 *     @  OA\Property(
 *         property="cover",
 *         type="string",
 *         description="配图地址"
 *     ),
 *     @  OA\Property(
 *         property="time",
 *         type="string",
 *         description="发布时间"
 *     )
 * )
 *
 * @  OA\Schema(
 *     schema="Paging",
 *     @  OA\Property(
 *          property="page",
 *          type="integer",
 *          description="页码",
 *          format="int32",
 *          default="1"
 *     ),
 *     @  OA\Property(
 *          property="limit",
 *          type="integer",
 *          description="每页个数",
 *          format="int32",
 *          minimum="0",
 *          exclusiveMinimum=true,
 *          maximum="100",
 *          exclusiveMaximum=false
 *     )
 * )
 */
class NewsController
{
    /**
     * @  OA\Get(
     *     path="/news/{type}",
     *     summary="获取资讯",
     *     description="返回包含已发布的资讯列表",
     *     @  OA\Parameter(
     *          name="type",
     *          in="path",
     *          required=true,
     *          description="类型",
     *          @  OA\Schema(
     *             type="string",
     *             enum={"entertainment","Sports"}
     *         )
     *     ),
     *     @  OA\Parameter(
     *          name="page",
     *          in="query",
     *          @  OA\Schema(
     *             @  OA\Items(ref="#/components/schemas/Paging")
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="一个资讯列表",
     *         @  OA\Schema(
     *             type="array",
     *             @  OA\Items(ref="#/components/schemas/news"),
     *         ),
     *     )
     * )
     */
//    public function indexAction()
//    {
//        echo 'a';
//    }
}