<?php
// 公共请求参数

namespace App\OAS\Components;

use OpenApi\Annotations as OA;

//###############header参数#############################

/**
 * 请求头Accept以指定api版本
 * @OA\Parameter(
 *      parameter="Accept",
 *      name="Accept",
 *      description="Accept header to specify api version(请求头Accept以指定api版本)",
 *      required=true,
 *      in="header",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          default="application/vnd.myCUNwoApp.v1+json",
 *          enum={"application/vnd.myCUNwoApp.v1+json","application/vnd.myCUNwoApp.v2+json"},
 *          example="application/vnd.myCUNwoApp.v1+json",
 *      )
 * ),
 *
 */

/**
 * 请求头Accept以指定api版本
 * @OA\Parameter(
 *      parameter="Content-Type",
 *      name="Content-Type",
 *      description="The Content-Type for encoding a specific property(请求中的媒体类型信息)",
 *      required=false,
 *      in="header",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * ),
 * https://www.runoob.com/http/http-content-type.html
 *
 * 常见的媒体格式类型如下：
 * text/html ： HTML格式
 * text/plain ：纯文本格式
 * text/xml ： XML格式
 * image/gif ：gif图片格式
 * image/jpeg ：jpg图片格式
 * image/png：png图片格式
 * 以application开头的媒体格式类型：
 * application/xhtml+xml ：XHTML格式
 * application/xml： XML数据格式
 * application/atom+xml ：Atom XML聚合格式
 * application/json： JSON数据格式
 * application/pdf：pdf格式
 * application/msword ： Word文档格式
 * application/octet-stream ： 二进制流数据（如常见的文件下载）
 * application/x-www-form-urlencoded ： <form encType=””>中默认的encType，form表单数据被编码为key/value格式发送到服务器（表单默认的提交数据的格式）
 * 另外一种常见的媒体格式是上传文件之时使用的：
 * multipart/form-data ： 需要在表单中进行文件上传时，就需要使用该格式
 *
 */

/**
 * 请求标头包含用于向服务器认证用户代理的凭证
 * @OA\Parameter(
 *      parameter="Authorization",
 *      name="Authorization",
 *      description="请求标头包含用于向服务器认证用户代理的凭证",
 *      required=true,
 *      in="header",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijg5M2U0MWRjYTRjN2E2ZDIzMDdkYTI4MjY0MmNiZDE5YzYwYWNlOWY0MDIwNGJjNTgyZTMzZGM1MDU1NDcyNmYwMjJjYzA4NmQ4Mzg2NzgzIn0.eyJhdWQiOiI4IiwianRpIjoiODkzZTQxZGNhNGM3YTZkMjMwN2RhMjgyNjQyY2JkMTljNjBhY2U5ZjQwMjA0YmM1ODJlMzNkYzUwNTU0NzI2ZjAyMmNjMDg2ZDgzODY3ODMiLCJpYXQiOjE1NzE2NDc4NzAsIm5iZiI6MTU3MTY0Nzg3MCwiZXhwIjoxNTcyOTQzODcwLCJzdWIiOiI3Iiwic2NvcGVzIjpbIioiXX0.CZrd29R8TxAdOl7-tx9iLlr_NREs1zMITgtIvJLjSOJuv3He8oPwErwF5gpkdNuDh69y4wHC6CWnJzyEi-bvwT6dCwPozyGO2mFxMbJcwQd3w7dQc2YF72e31OkO3crhS0dndW68sEaeFy3fJxnv7tk-yrPi3HBCS_7YIgulCsLrdQ021mbT6h1A3j2RcU4gZQzr0UpuR2TbjZcimN9SOqe5WI7gL0bzqCChZjCawFeLznScap3LxUwhqR75jg8XWaeCYdBsSUweLlFfO3NW9kOoE5nz-_Kjp4xTi5Od_n6osoErrlO_7_j308R552mF9o2Eeg7ZbpJoXfBfxp1NQVhrp6WSvKs5OWvvbBS2ITJEoj4cDjXHNFsVIhlKzsTGzE5Og4_Y0btbIOl3r9oPsGkVC3ehiFZgw4tjO8X21X6PGjLLR-wazGwhrM_dkRnr4lFbsksEzumg9pQrRwQwLGKyPzloiTzp8EKRGwYQ4InxAWG7TWZVucUMZngOrmYe2ZFQEvRSfuWAI-JP5SVmnCM0vYH5Hs67d8es5Y_MwL1B_VWpmupBBlBN0ZcNAkvIADVkIsJzmRKNmZ9auih3OR_fdNaFZEQy-QTO2ifEvCzi_JhELxhKtNC2gzR8i9r9J7JBr3gnB1_u_Rswz9W4RU83wLyf6FNgU9fBjAQo1-8",
 *      )
 * ),
 *
 */

//###############query参数#############################
/**
 * 用户授权
 * @OA\Parameter(
 *      parameter="common_Parameter_access_token",
 *      name="access_token",
 *      in="query",
 *      description="用户授权",
 *      required=true,
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string"
 *      )
 * ),
 *
 */

/**
 * 模糊查询字段名
 * @OA\Parameter(
 *      parameter="common_Parameter_field",
 *      name="field",
 *      in="query",
 *      description="模糊查询字段名",
 *      required=false,
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string"
 *      )
 * ),
 *
 */


/**
 * 模糊查询关键字
 * @OA\Parameter(
 *      parameter="common_Parameter_keyword",
 *      name="keyword",
 *      in="query",
 *      description="模糊查询关键字",
 *      required=false,
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string"
 *      )
 * ),
 *
 */

/**
 * 第几页
 * @OA\Parameter(
 *      parameter="common_Parameter_page",
 *      name="page",
 *      description="第几页(>=1)",
 *      required=false,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="1",
 *          minimum="1",
 *          example="1",
 *      )
 * ),
 *
 */

/**
 * 每页显示数量
 * @OA\Parameter(
 *      parameter="common_Parameter_pagesize",
 *      name="pagesize",
 *      description="每页显示数量(>= 1 或 <= 100)",
 *      required=false,
 *      in="query",
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="15",
 *          minimum="1",
 *          maximum="100",
 *          example="15",
 *      )
 * ),
 *
 *
 *      @ OA\Schema(ref ="# /components/schemas/common_Schema_pagesize")
 */

/**
 * 列表记录总数量
 * @OA\Parameter(
 *      parameter="common_Parameter_total",
 *      name="total",
 *      description="记录总数量(>=-1;-1:接口会重新请求总记录数，>=1:接口不会重新请求总记录数量)",
 *      required=false,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="integer",
 *          format="int32",
 *          default="-1",
 *          minimum="-1",
 *          example="-1",
 *      )
 * ),
 *
 */

/**
 * 手机号参数
 * @OA\Parameter(
 *      parameter="common_Parameter_mobile",
 *      name="mobile",
 *      description="手机号",
 *      required=true,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          minLength=11,
 *          maxLength=11,
 *          example="",
 *      )
 * ),
 *
 */

/**
 * 手机验证码
 * @OA\Parameter(
 *      parameter="common_Parameter_mobile_code",
 *      name="mobile_vercode",
 *      description="验证码",
 *      required=true,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          minLength=3,
 *          maxLength=10,
 *      )
 * ),
 *
 */

/**
 * 用户名
 * @OA\Parameter(
 *      parameter="common_Parameter_admin_username",
 *      name="admin_username",
 *      description="用户名",
 *      required=true,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          minLength=6,
 *          maxLength=20,
 *      )
 * ),
 *
 */

/**
 * 密码
 * @OA\Parameter(
 *      parameter="common_Parameter_admin_password",
 *      name="admin_password",
 *      description="密码",
 *      required=true,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          minLength=6,
 *          maxLength=20,
 *      )
 * ),
 *
 */

/**
 * 确认密码
 * @OA\Parameter(
 *      parameter="common_Parameter_repass",
 *      name="repass",
 *      description="确认密码",
 *      required=true,
 *      in="query",
 *      deprecated=false,
 *      @OA\Schema(
 *          type="string",
 *          minLength=6,
 *          maxLength=20,
 *      )
 * ),
 *
 */
class PublicParameter
{

}
