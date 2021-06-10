<?php
// 城市[一级分类]
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCitysBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Citys';
    public static $table_name = 'citys';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
