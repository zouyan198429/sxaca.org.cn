<?php
// 接口日志
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBApiLogBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\ApiLog';
    public static $table_name = 'api_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
