<?php
// 支付订单
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrderPayBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\OrderPay';
    public static $table_name = 'order_pay';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
