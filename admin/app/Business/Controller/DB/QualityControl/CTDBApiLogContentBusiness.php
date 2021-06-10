<?php
// 接口调用日志数据
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBApiLogContentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\ApiLogContent';
    public static $table_name = 'api_log_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
