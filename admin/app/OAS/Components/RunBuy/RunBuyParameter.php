<?php
// 公共请求参数

namespace App\OAS\Components\RunBuy;

use OpenApi\Annotations as OA;


//###############query参数#############################

/**
 * 城市id
 * @OA\Parameter(
 *      parameter="common_Parameter_RunBuy_city_site_id",
 *      name="city_site_id",
 *      description="城市id(>=1)",
 *      required=false,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="",
 *          minimum="1",
 *          example="",
 *      )
 * ),
 *
 */

/**
 * 城市代理id
 * @OA\Parameter(
 *      parameter="common_Parameter_RunBuy_city_partner_id",
 *      name="city_partner_id",
 *      description="城市代理id(>=1)",
 *      required=false,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="",
 *          minimum="1",
 *          example="",
 *      )
 * ),
 *
 */

/**
 * 商家id
 * @OA\Parameter(
 *      parameter="common_Parameter_RunBuy_seller_id",
 *      name="seller_id",
 *      description="商家id(>=1)",
 *      required=false,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="",
 *          minimum="1",
 *          example="",
 *      )
 * ),
 *
 */

/**
 * 店铺id
 * @OA\Parameter(
 *      parameter="common_Parameter_RunBuy_shop_id",
 *      name="shop_id",
 *      description="店铺id(>=1)",
 *      required=false,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="",
 *          minimum="1",
 *          example="",
 *      )
 * ),
 *
 */

class RunBuyParameter
{

}