<?php
// 点播课程销量统计【流水】
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodSalesBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodSales';
    public static $table_name = 'vod_sales';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
