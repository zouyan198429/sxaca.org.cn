<?php
// 支付宝授权及令牌
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAlipayAuthTokenBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AlipayAuthToken';
    public static $table_name = 'alipay_auth_token';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
