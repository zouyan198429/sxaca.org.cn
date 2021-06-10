<?php
// 数据表属性

namespace App\OAS\Components\RunBuy;

use OpenApi\Annotations as OA;

/**
 *  样例
 *  公共属性(具体库)-数量
 * @ OA\Schema(
 *   schema="common_Schema_RunBuy_aaaaa",
 *   type="integer",
 *   format="int64",
 *   title="公共属性(具体库)-数量",
 *   description="公共属性(具体库)-数量",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 * 公共属性(具体库)-支付方式1余额支付2微信支付
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_pay_type",
 *   type="integer",
 *   format="int32",
 *   title="公共属性(具体库)-支付方式",
 *   description="公共属性(具体库)-支付方式1余额支付2微信支付",
 *   default=1,
 *   enum={"1","2"},
 *   example="1",
 * )
 *
 */

/**
 * 公共属性(具体库)-支付方式文字
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_pay_type_text",
 *   type="string",
 *   title="公共属性(具体库)-支付方式文字",
 *   description="公共属性(具体库)-支付方式文字",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="余额支付",
 * )
 *
 */

/**
 *  公共属性(具体库)-拥有者类型
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_ower_type",
 *   type="integer",
 *   format="int64",
 *   title="公共属性(具体库)-拥有者类型",
 *   description="公共属性(具体库)-拥有者类型:1平台2城市分站4城市代理8商家16店铺32快跑人员64用户",
 *   default=1,
 *   enum={"1","2","4","8","16","32","64"},
 *   example="1",
 * )
 */

/**
 * 公共属性(具体库)-拥有者类型文字
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_ower_type_text",
 *   type="string",
 *   title="公共属性(具体库)-拥有者类型文字",
 *   description="公共属性(具体库)-拥有者类型文字",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="平台",
 * )
 *
 */

/**
 *
 *  公共属性(具体库)-拥有者id
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_ower_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性(具体库)-拥有者id",
 *   description="公共属性(具体库)-拥有者id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 *
 *  公共属性(具体库)-拥有者历史id
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_ower_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性(具体库)-拥有者历史id",
 *   description="公共属性(具体库)-拥有者历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 *
 *  公共属性(具体库)-派送用户id
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_send_staff_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性(具体库)-派送用户id",
 *   description="公共属性(具体库)-派送用户id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 *
 *  公共属性(具体库)-派送用户历史id
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_send_staff_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性(具体库)-派送用户历史id",
 *   description="公共属性(具体库)-派送用户历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 * 公共属性(具体库)-作废状态0:正常；1：作废
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_revoked",
 *   type="integer",
 *   format="int32",
 *   title="公共属性(具体库)-作废状态",
 *   description="公共属性(具体库)-作废状态0:正常；1：作废",
 *   default=0,
 *   enum={"0","1"},
 *   example="0",
 * )
 *
 */

/**
 * 公共属性(具体库)-作废状态
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_revoked_text",
 *   type="string",
 *   title="公共属性(具体库)-作废状态",
 *   description="公共属性(具体库)-作废状态",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="正常",
 * )
 *
 */

/**
 * 公共属性(具体库)-名称
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_name",
 *   type="string",
 *   title="公共属性(具体库)-名称",
 *   description="公共属性(具体库)-名称",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="",
 * )
 *
 */

/**
 * 公共属性(具体库)-到期时间
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_expires_at",
 *   type="string",
 *   format="date-time",
 *   title="公共属性(具体库)-到期时间",
 *   description="公共属性(具体库)-到期时间",
 *   example="2019-12-04 12:31:30",
 * )
 *
 */

/**
 * 公共属性(具体库)-下单序号（前缀+ 两位日+当前号）
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_create_num",
 *   type="string",
 *   title="公共属性(具体库)-下单序号",
 *   description="公共属性(具体库)-下单序号（前缀+ 两位日+当前号）",
 *   default="",
 *   minLength=0,
 *   maxLength=10,
 *   example="",
 * )
 *
 */

/**
 * 公共属性(具体库)-付款序号（前缀+ 两位日+当前号）
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_pay_num",
 *   type="string",
 *   title="公共属性(具体库)-付款序号",
 *   description="公共属性(具体库)-付款序号（前缀+ 两位日+当前号）",
 *   default="",
 *   minLength=0,
 *   maxLength=10,
 *   example="",
 * )
 *
 */

/**
 * 公共属性(具体库)-确认序号（前缀+ 两位日+当前号）
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_sure_num",
 *   type="string",
 *   title="公共属性(具体库)-确认序号",
 *   description="公共属性(具体库)-确认序号（前缀+ 两位日+当前号）",
 *   default="",
 *   minLength=0,
 *   maxLength=10,
 *   example="",
 * )
 *
 */

/**
 * 公共属性(具体库)-业务类型1扫桌码点餐(店内-现在)；2扫店码点餐(店内/外-现在)；4扫店码预订（到店用餐）---店铺需开预订功能；8扫店码预订（到店自取）---店铺需开预订功能；16扫店码预订（店铺配送）---店铺需开预订功能;32店铺订单（第三方配送）；64平台预订（第三方配送）-此项店铺不能设置，是系统用这个值
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_business_type",
 *   type="integer",
 *   format="int32",
 *   title="公共属性(具体库)-业务类型",
 *   description="公共属性(具体库)-业务类型1扫桌码点餐(店内-现在)；2扫店码点餐(店内/外-现在)；4扫店码预订（到店用餐）---店铺需开预订功能；8扫店码预订（到店自取）---店铺需开预订功能；16扫店码预订（店铺配送）---店铺需开预订功能;32店铺订单（第三方配送）；64平台预订（第三方配送）-此项店铺不能设置，是系统用这个值",
 *   default=1,
 *   enum={"1","2","4","8","16","32","64"},
 *   example="1",
 * )
 *
 */

/**
 * 公共属性(具体库)-支付方式文字
 * @OA\Schema(
 *   schema="common_Schema_RunBuy_business_type_text",
 *   type="string",
 *   title="公共属性(具体库)-支付方式文字",
 *   description="公共属性(具体库)-支付方式文字",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="扫桌码点餐",
 * )
 *
 */


class RunBuyProperty
{

}
