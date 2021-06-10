<?php
// 资源
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBResourceBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Resource';
    public static $table_name = 'resource';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
