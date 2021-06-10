<?php

namespace App\OAS\Components;

use OpenApi\Annotations as OA;

//##################请求主体对象#######################################################

/**
 * 上传文件的
 *
 * @OA\RequestBody(
 *     request="common_RequestBody_uploud_images",
 *     description="上传文件",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="application/octet-stream",
 *         @OA\Schema(
 *              type="string",
 *              format="binary"
 *          ),
 *     )
 * )
 *
 *
 */

class PublicRequestBody
{

}