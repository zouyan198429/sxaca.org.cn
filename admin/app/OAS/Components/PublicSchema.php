<?php
namespace App\OAS\Components;

use OpenApi\Annotations as OA;

// 所有属性的样本--参照用

/**
 * 接口最外层属性-状态
 * @ OA\Schema(
 *   schema="common_Schema_aaaaa",
 *   type="object",
 *   title="接口最外层属性-状态",
 *   description="接口最外层属性-状态",
 *   @ OA\Property(
 *      property="apistatus",
 *      type="integer",
 *      format="int32",
 *      description="接口返回状态",
 *      title="接口返回状态",
 *      default=0,
 *      example="0:失败；1：成功",
 *   ),
 * )
 *
 */


/**
 * 测试用
 * @OA\Schema(
 *   schema="common_Schema_testaa",
 *   type="object",
 *   title="接口最外层属性-状态",
 *   description="接口最外层属性-状态",
 *   @OA\Property(
 *      property="apistatus",
 *      type="integer",
 *      format="int32",
 *      description="接口返回状态",
 *      title="接口返回状态",
 *      default=0,
 *      example="0:失败；1：成功",
 *   ),
 * )
 *
 */



// 返回接口公共属性

/**
 * 接口最外层属性-状态
 * @OA\Schema(
 *   schema="common_Schema_apistatus",
 *   type="integer",
 *   format="int32",
 *   title="接口最外层属性-状态",
 *   description="接口最外层属性-状态",
 *   default=0,
 *   example="0:失败；1：成功",
 * )
 *
 */

/**
 * 接口最外层属性-错误信息
 * @OA\Schema(
 *   schema="common_Schema_errorMsg",
 *   type="string",
 *   title="接口最外层属性-错误信息",
 *   description="接口最外层属性-错误信息",
 *   maxLength=200,
 *   default="",
 *   example="有错时的错误信息",
 * )
 *
 */


// 返回接口公共属性--列表页相关的
/**
 * 接口列表属性-是否还有下一页数据
 * @OA\Schema(
 *   schema="common_Schema_has_page",
 *   type="boolean",
 *   title="接口列表属性-是否还有下一页数据",
 *   description="接口列表属性-是否还有下一页数据",
 *   default=false,
 *   example="true:有下一页数据；false:没有下一页数据",
 * )
 *
 */

/**
 * 接口列表属性-总数量
 * @OA\Schema(
 *   schema="common_Schema_total",
 *   type="integer",
 *   format="int32",
 *   title="接口列表属性-总数量",
 *   description="接口列表属性-总数量",
 *   default=0,
 *   example="1008",
 * )
 *
 */

/**
 * 接口列表属性-第几页
 * @OA\Schema(
 *   schema="common_Schema_page",
 *   type="integer",
 *   format="int32",
 *   title="接口列表属性-第几页",
 *   description="接口列表属性-第几页",
 *   default=0,
 *   example="1",
 * )
 *
 */

/**
 * 接口列表属性-每页显示数量
 * @OA\Schema(
 *   schema="common_Schema_pagesize",
 *   type="integer",
 *   format="int32",
 *   title="接口列表属性-每页显示数量",
 *   description="接口列表属性-每页显示数量",
 *   default=0,
 *   maximum="100",
 *   example="15",
 * )
 *
 */

/**
 * 接口列表属性-总页数
 * @OA\Schema(
 *   schema="common_Schema_totalPage",
 *   type="integer",
 *   format="int32",
 *   title="接口列表属性-总页数",
 *   description="接口列表属性-总页数",
 *   default=0,
 *   example="68",
 * )
 *
 */

/**
 * 接口列表属性-翻页html代码
 * @OA\Schema(
 *   schema="common_Schema_pageInfo",
 *   type="string",
 *   title="接口列表属性-翻页html代码",
 *   description="接口列表属性-翻页html代码",
 *   maxLength=5000,
 *   default="",
 *   example="<li><a href='javascript:;' id='totalpage' totalpage='68' >总数:1008个 / 68页</a></li><li  class='disabled'><a href='javascript:void(0)' pg='1' aria-label='首页'><span aria-hidden='true'>首页</span></a></li><li  class='disabled'><a href='javascript:void(0)' pg='1' aria-label='前页'><span aria-hidden='true'>前页</span></a></li><li class='active'><a herf='javascript:void(0)' pg='1'>1</a></li><li><a href='javascript:void(0)' pg='2'>2</a></li><li><a href='javascript:void(0)' pg='3'>3</a></li><li><a href='javascript:void(0)' pg='4'>4</a></li><li><a href='javascript:void(0)' pg='5'>5</a></li><li><a href='javascript:void(0)' pg='6'>6</a></li><li><a href='javascript:void(0)' pg='7'>7</a></li><li><a href='javascript:void(0)' pg='8'>8</a></li><li><a href='javascript:void(0)' pg='9'>9</a></li><li><a href='javascript:void(0)' pg='10'>10</a></li><li><a href='javascript:void(0)' pg='11'>11</a></li><li><a href='javascript:void(0)' pg='12'>12</a></li><li ><a aria-label='后页' href='javascript:void(0)' pg='2'><span aria-hidden='true'>后页</span></a></li><li ><a aria-label='末页' href='javascript:void(0)' pg='68'><span aria-hidden='true'>末页</span></a></li>&nbsp;&nbsp;<span class='pagespan2' ><input class='form-control pagenum' id='page_num' name='page_num' type='text' value='' onkeyup='this.value=this.value.replace(/[^0-9]/g, '');' style='width:50px;'>&nbsp;&nbsp;<button class='btn btn-primary btn-page btn-xs page_go' type='button'  totalpage='68' > 跳转 </button></span>",
 * )
 *
 */


class PublicSchema
{

}