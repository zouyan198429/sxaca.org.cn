<?php
// 点播课程订单
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodOrdersBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodOrders';
    public static $table_name = 'vod_orders';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
