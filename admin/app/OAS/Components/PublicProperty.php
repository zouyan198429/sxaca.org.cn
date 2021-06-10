<?php
// 数据表公共属性

namespace App\OAS\Components;

use OpenApi\Annotations as OA;


/**
 * 公共属性-价格
 * @OA\Schema(
 *   schema="common_Schema_price",
 *   type="number",
 *   format="float",
 *   title="公共属性-价格",
 *   description="公共属性-价格",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 *  公共属性-数量
 * @OA\Schema(
 *   schema="common_Schema_amount",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-数量",
 *   description="公共属性-数量",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 *  公共属性-总数量
 * @OA\Schema(
 *   schema="common_Schema_total_amount",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-总数量",
 *   description="公共属性-总数量",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 * 公共属性-金额
 * @OA\Schema(
 *   schema="common_Schema_total_amount_money",
 *   type="number",
 *   format="float",
 *   title="公共属性-金额",
 *   description="公共属性-金额",
 *   default=0,
 *   minimum=0,
 *   example="15.88",
 * )
 *
 */

/**
 *  公共属性-记录-code
 * @OA\Schema(
 *   schema="common_Schema_code",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-接口状态码",
 *   description="公共属性-接口状态码",
 *   default=1,
 *   example="1",
 * )
 */

/**
 *  公共属性-记录-数据
 * @OA\Schema(
 *   schema="common_Schema_data_int",
 *   type="string",
 *   format="int64",
 *   title="公共属性-接口数据",
 *   description="公共属性-接口数据(不同情况数据结构不一样)",
 *   default=1,
 *   example="1",
 * )
 */

/**
 *  公共属性-记录-id
 * @OA\Schema(
 *   schema="common_Schema_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-记录-id",
 *   description="公共属性-记录-id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 *  公共属性-记录-empty
 * @OA\Schema(
 *   schema="common_Schema_empty",
 *   type="string",
 *   title="公共属性-记录-空",
 *   description="公共属性-记录-empty",
 *   default="",
 *   example="",
 * )
 */

/**
 *  公共属性-记录-emptyObject
 * @OA\Schema(
 *   schema="common_Schema_empty_object",
 *   type="object",
 *   title="公共属性-记录-空对象",
 *   description="公共属性-记录-空对象",
 * )
 */

/**
 *  公共属性-删除成功数量
 * @OA\Schema(
 *   schema="common_Schema_deleted_nums",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-删除成功数量",
 *   description="公共属性-删除成功数量",
 *   default=1,
 *   minimum=0,
 *   example="1",
 * )
 */

/**
 * 公共属性-版本号
 * @OA\Schema(
 *   schema="common_Schema_version_num",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-版本号",
 *   description="公共属性-版本号",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-历史id
 * @OA\Schema(
 *   schema="common_Schema_version_history_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-历史id",
 *   description="公共属性-历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-版本号[从0开始]
 * @OA\Schema(
 *   schema="common_Schema_version_num_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-版本号[从0开始]",
 *   description="公共属性-版本号[从0开始]",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-是否开通1未开通2已开通
 * @OA\Schema(
 *   schema="common_Schema_is_open",
 *   type="integer",
 *   format="int32",
 *   title="公共属性-是否开通",
 *   description="公共属性-是否开通1未开通2已开通",
 *   default=1,
 *   enum={"1","2"},
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-是否开通文字
 * @OA\Schema(
 *   schema="common_Schema_is_open_text",
 *   type="string",
 *   title="公共属性-是否开通文字",
 *   description="公共属性-是否开通文字",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="未开通",
 * )
 *
 */

/**
 * 公共属性-冻结状态0正常 1冻结
 * @OA\Schema(
 *   schema="common_Schema_is_frozen",
 *   type="integer",
 *   format="int32",
 *   title="公共属性-冻结状态",
 *   description="公共属性-冻结状态0正常 1冻结",
 *   default=0,
 *   enum={"0","1"},
 *   example="0",
 * )
 *
 */

/**
 * 公共属性-冻结状态文字
 * @OA\Schema(
 *   schema="common_Schema_is_frozen_text",
 *   type="string",
 *   title="公共属性-冻结状态文字",
 *   description="公共属性-冻结状态文字",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="正常",
 * )
 *
 */

/**
 * 公共属性-性别0未知1男2女
 * @OA\Schema(
 *   schema="common_Schema_sex",
 *   type="integer",
 *   format="int32",
 *   title="公共属性-性别",
 *   description="公共属性-性别0未知1男2女",
 *   default=0,
 *   enum={"0","1","2"},
 *   example="0",
 * )
 *
 */

/**
 * 公共属性-性别文字
 * @OA\Schema(
 *   schema="common_Schema_sex_text",
 *   type="string",
 *   title="公共属性-性别文字",
 *   description="公共属性-性别文字",
 *   default="",
 *   minLength=0,
 *   maxLength=3,
 *   example="男",
 * )
 *
 */

/**
 * 公共属性-排序[降序]
 * @OA\Schema(
 *   schema="common_Schema_sort_num",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-排序[降序]",
 *   description="公共属性-排序[降序]",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-资源id
 * @OA\Schema(
 *   schema="common_Schema_resource_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-资源id",
 *   description="公共属性-资源id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-资源历史id
 * @OA\Schema(
 *   schema="common_Schema_resource_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-资源历史id",
 *   description="公共属性-资源历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */


/**
 * 公共属性-图片资源id
 * @OA\Schema(
 *   schema="common_Schema_resource_ids",
 *   type="string",
 *   title="公共属性-图片资源id",
 *   description="公共属性-图片资源id，多个用,逗号分隔",
 *   default="",
 *   minLength=0,
 *   maxLength=100,
 *   example="1,2,3",
 * )
 *
 */

/**
 * 公共属性-真实姓名
 * @OA\Schema(
 *   schema="common_Schema_real_name",
 *   type="string",
 *   title="公共属性-真实姓名",
 *   description="公共属性-真实姓名",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="王总",
 * )
 *
 */


/**
 * 公共属性-联系人
 * @OA\Schema(
 *   schema="common_Schema_linkman",
 *   type="string",
 *   title="公共属性-联系人",
 *   description="公共属性-联系人",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="王总",
 * )
 *
 */

/**
 * 公共属性-手机
 * @OA\Schema(
 *   schema="common_Schema_mobile",
 *   type="string",
 *   title="公共属性-手机",
 *   description="公共属性-手机",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="15829686966",
 * )
 *
 */

/**
 * 公共属性-电话
 * @OA\Schema(
 *   schema="common_Schema_tel",
 *   type="string",
 *   title="公共属性-电话",
 *   description="公共属性-电话",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="029-88214602",
 * )
 *
 */

/**
 * 公共属性-省id
 * @OA\Schema(
 *   schema="common_Schema_province_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-省id",
 *   description="公共属性-省id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-省历史id
 * @OA\Schema(
 *   schema="common_Schema_province_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-省历史id",
 *   description="公共属性-省历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-省
 * @OA\Schema(
 *   schema="common_Schema_province_name",
 *   type="string",
 *   title="公共属性-省",
 *   description="公共属性-省",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="陕西省",
 * )
 *
 */

/**
 * 公共属性-市id
 * @OA\Schema(
 *   schema="common_Schema_city_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-市id",
 *   description="公共属性-市id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-市历史id
 * @OA\Schema(
 *   schema="common_Schema_city_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-市历史id",
 *   description="公共属性-市历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-市
 * @OA\Schema(
 *   schema="common_Schema_city_name",
 *   type="string",
 *   title="公共属性-市",
 *   description="公共属性-市",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="咸阳市",
 * )
 *
 */

/**
 * 公共属性-区id
 * @OA\Schema(
 *   schema="common_Schema_area_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-区id",
 *   description="公共属性-区id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-区历史id
 * @OA\Schema(
 *   schema="common_Schema_area_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-区历史id",
 *   description="公共属性-区历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-区
 * @OA\Schema(
 *   schema="common_Schema_area_name",
 *   type="string",
 *   title="公共属性-区/县",
 *   description="公共属性-区/县",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="彬州市",
 * )
 *
 */

/**
 * 公共属性-地址名称
 * @OA\Schema(
 *   schema="common_Schema_addr_name",
 *   type="string",
 *   title="公共属性-地址名称",
 *   description="公共属性-地址名称",
 *   default="",
 *   minLength=0,
 *   maxLength=100,
 *   example="家里",
 * )
 *
 */

/**
 * 公共属性-所在地址
 * @OA\Schema(
 *   schema="common_Schema_addr",
 *   type="string",
 *   title="公共属性-所在地址",
 *   description="公共属性-所在地址",
 *   default="",
 *   minLength=0,
 *   maxLength=100,
 *   example="城关镇北大街明珠馨苑",
 * )
 *
 */

/**
 * 公共属性-操作员工id
 * @OA\Schema(
 *   schema="common_Schema_operate_staff_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-操作员工id",
 *   description="公共属性-操作员工id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-操作员工
 * @OA\Schema(
 *   schema="common_Schema_operate_staff__name",
 *   type="string",
 *   title="公共属性-操作员工",
 *   description="公共属性-操作员工",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="小王",
 * )
 *
 */


/**
 * 公共属性-操作员工历史id
 * @OA\Schema(
 *   schema="common_Schema_operate_staff_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-操作员工历史id",
 *   description="公共属性-操作员工历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-员工id
 * @OA\Schema(
 *   schema="common_Schema_staff_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-员工id",
 *   description="公共属性-员工id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-员工
 * @OA\Schema(
 *   schema="common_Schema_staff_name",
 *   type="string",
 *   title="公共属性-员工",
 *   description="公共属性-员工",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="小王",
 * )
 *
 */


/**
 * 公共属性-员工历史id
 * @OA\Schema(
 *   schema="common_Schema_staff_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-员工历史id",
 *   description="公共属性-员工历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-操作用户id
 * @OA\Schema(
 *   schema="common_Schema_make_staff_id",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-操作用户id",
 *   description="公共属性-操作用户id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-操作用户历史id
 * @OA\Schema(
 *   schema="common_Schema_make_staff_id_history",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-操作用户历史id",
 *   description="公共属性-操作用户历史id",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-开始时间
 * @OA\Schema(
 *   schema="common_Schema_begin_time",
 *   type="string",
 *   format="date-time",
 *   title="公共属性-开始时间",
 *   description="公共属性-开始时间",
 *   example="2019-12-04 12:31:30",
 * )
 *
 */

/**
 * 公共属性-结束时间
 * @OA\Schema(
 *   schema="common_Schema_end_time",
 *   type="string",
 *   format="date-time",
 *   title="公共属性-结束时间",
 *   description="公共属性-结束时间",
 *   example="2019-12-04 12:31:30",
 * )
 *
 */

/**
 * 公共属性-创建时间
 * @OA\Schema(
 *   schema="common_Schema_created_at",
 *   type="string",
 *   format="date-time",
 *   title="公共属性-创建时间",
 *   description="公共属性-创建时间",
 *   example="2019-12-04 12:31:30",
 * )
 *
 */

/**
 * 公共属性-更新时间
 * @OA\Schema(
 *   schema="common_Schema_updated_at",
 *   type="string",
 *   format="date-time",
 *   title="公共属性-更新时间",
 *   description="公共属性-更新时间",
 *   example="2019-12-04 12:31:30",
 * )
 *
 */

/**
 * 公共属性-确认时间
 * @OA\Schema(
 *   schema="common_Schema_sure_time",
 *   type="string",
 *   format="date-time",
 *   title="公共属性-确认时间",
 *   description="公共属性-确认时间",
 *   example="2019-12-04 12:31:30",
 * )
 *
 */

/**
 * 公共属性-介绍
 * @OA\Schema(
 *   schema="common_Schema_intro",
 *   type="string",
 *   title="公共属性-介绍",
 *   description="公共属性-介绍",
 *   default="",
 *   minLength=0,
 *   maxLength=6000,
 *   example="",
 * )
 *
 */

/**
 * 公共属性-经度
 * @OA\Schema(
 *   schema="common_Schema_longitude",
 *   type="string",
 *   title="公共属性-经度",
 *   description="公共属性-经度",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="108.3616304397583",
 * )
 *
 */

/**
 * 公共属性-纬度
 * @OA\Schema(
 *   schema="common_Schema_latitude",
 *   type="string",
 *   title="公共属性-纬度",
 *   description="公共属性-纬度",
 *   default="",
 *   minLength=0,
 *   maxLength=25,
 *   example="21.765597805478272",
 * )
 *
 */

/**
 * 公共属性-经度数值
 * @OA\Schema(
 *   schema="common_Schema_lng",
 *   type="number",
 *   format="double",
 *   title="公共属性-经度数值",
 *   description="公共属性-经度数值",
 *   default="",
 *   example="108.361630439758300000",
 * )
 *
 */

/**
 * 公共属性-纬度数值
 * @OA\Schema(
 *   schema="common_Schema_lat",
 *   type="number",
 *   format="double",
 *   title="公共属性-纬度数值",
 *   description="公共属性-纬度数值",
 *   default="",
 *   example="21.765597805478272000",
 * )
 *
 */


/**
 * 公共属性-geohash值
 * @OA\Schema(
 *   schema="common_Schema_geohash",
 *   type="string",
 *   title="公共属性-geohash值",
 *   description="公共属性-geohash值",
 *   default="",
 *   minLength=0,
 *   maxLength=15,
 *   example="w7v5cf69bcxb",
 * )
 *
 */

/**
 * 公共属性-geohash字段3[156km×156km]
 * @OA\Schema(
 *   schema="common_Schema_geohash3",
 *   type="string",
 *   title="公共属性-geohash字段3[156km×156km]",
 *   description="公共属性-geohash字段3[156km×156km]",
 *   default="",
 *   minLength=0,
 *   maxLength=4,
 *   example="w7v",
 * )
 *
 */

/**
 * 公共属性-geohash字段4[39.1km×19.5km]
 * @OA\Schema(
 *   schema="common_Schema_geohash4",
 *   type="string",
 *   title="公共属性-geohash字段4[39.1km×19.5km]",
 *   description="公共属性-geohash字段4[39.1km×19.5km]",
 *   default="",
 *   minLength=0,
 *   maxLength=6,
 *   example="w7v5",
 * )
 *
 */

/**
 * 公共属性-geohash字段5[4.89km×4.89km]
 * @OA\Schema(
 *   schema="common_Schema_geohash5",
 *   type="string",
 *   title="公共属性-geohash字段5[4.89km×4.89km]",
 *   description="公共属性-geohash字段5[4.89km×4.89km]",
 *   default="",
 *   minLength=0,
 *   maxLength=8,
 *   example="w7v5c",
 * )
 *
 */

/**
 * 公共属性-总金额
 * @OA\Schema(
 *   schema="common_Schema_total_money",
 *   type="number",
 *   format="double",
 *   title="公共属性-总金额",
 *   description="公共属性-总金额",
 *   default=0,
 *   minimum="0",
 *   example="2.00",
 * )
 *
 */

/**
 * 公共属性-冻结金额
 * @OA\Schema(
 *   schema="common_Schema_frozen_money",
 *   type="number",
 *   format="double",
 *   title="公共属性-冻结金额",
 *   description="公共属性-冻结金额",
 *   default=0,
 *   minimum="0",
 *   example="2.00",
 * )
 *
 */

/**
 * 公共属性-可用金额
 * @OA\Schema(
 *   schema="common_Schema_avail_money",
 *   type="number",
 *   format="double",
 *   title="公共属性-可用金额",
 *   description="公共属性-可用金额",
 *   default=0,
 *   minimum="0",
 *   example="2.00",
 * )
 *
 */

/**
 * 公共属性-校验字串
 * @OA\Schema(
 *   schema="common_Schema_check_key",
 *   type="string",
 *   title="公共属性-校验字串",
 *   description="公共属性-校验字串",
 *   default="",
 *   minLength=0,
 *   maxLength=100,
 *   example="hghdfhfghfghfghfg",
 * )
 *
 */


/**
 * 公共属性-日期
 * @OA\Schema(
 *   schema="common_Schema_count_date",
 *   type="string",
 *   format="date",
 *   title="公共属性-日期",
 *   description="公共属性-日期",
 *   example="2019-12-04",
 * )
 *
 */

/**
 * 公共属性-年
 * @OA\Schema(
 *   schema="common_Schema_count_year",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-年",
 *   description="公共属性-年",
 *   default=2019,
 *   minimum=0,
 *   example="2019",
 * )
 *
 */

/**
 * 公共属性-月
 * @OA\Schema(
 *   schema="common_Schema_count_month",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-月",
 *   description="公共属性-月",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-日
 * @OA\Schema(
 *   schema="common_Schema_count_day",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-日",
 *   description="公共属性-日",
 *   default=0,
 *   minimum=0,
 *   example="15",
 * )
 *
 */



/**
 * 公共属性-内容
 * @OA\Schema(
 *   schema="common_Schema_content",
 *   type="string",
 *   title="公共属性-内容",
 *   description="公共属性-内容",
 *   default="",
 *   minLength=0,
 *   maxLength=20000,
 *   example="hghdfhfghfghfghfg",
 * )
 *
 */

/**
 * 公共属性-阅读量
 * @OA\Schema(
 *   schema="common_Schema_volume",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-阅读量",
 *   description="公共属性-阅读量",
 *   default=0,
 *   minimum=0,
 *   example="1",
 * )
 *
 */

/**
 * 公共属性-标题
 * @OA\Schema(
 *   schema="common_Schema_title",
 *   type="string",
 *   title="公共属性-标题",
 *   description="公共属性-标题",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="",
 * )
 *
 */

/**
 * 公共属性-来源
 * @OA\Schema(
 *   schema="common_Schema_resource",
 *   type="string",
 *   title="公共属性-来源",
 *   description="公共属性-来源",
 *   default="",
 *   minLength=0,
 *   maxLength=30,
 *   example="",
 * )
 *
 */

/**
 * 公共属性-E-mail
 * @OA\Schema(
 *   schema="common_Schema_email",
 *   type="string",
 *   format="email",
 *   title="公共属性-E-mail",
 *   description="公共属性-E-mail",
 *   example="305463219@qq.com",
 * )
 *
 */

/**
 * 公共属性-token
 * @OA\Schema(
 *   schema="common_Schema_token",
 *   type="string",
 *   title="公共属性-token",
 *   description="公共属性-token",
 *   default="",
 *   minLength=0,
 *   maxLength=191,
 *   example="",
 * )
 *
 */

/**
 * 公共属性-修改时间戳
 * @OA\Schema(
 *   schema="common_Schema_modifyTime",
 *   type="integer",
 *   format="int64",
 *   title="公共属性-修改时间戳",
 *   description="公共属性-修改时间戳",
 *   default=0,
 *   minimum=0,
 *   example="1581491630",
 * )
 *
 */

/**
 * 公共属性-登录状态redisKey值
 * @OA\Schema(
 *   schema="common_Schema_redisKey",
 *   type="string",
 *   title="公共属性-登录状态redisKey值",
 *   description="公共属性-登录状态redisKey值",
 *   default="",
 *   minLength=0,
 *   maxLength=191,
 *   example="cunwo:runbuy-admin-cunwo-net80:production:login:1_20200211073420a560b893033",
 * )
 *
 */

class PublicProperty
{

}
