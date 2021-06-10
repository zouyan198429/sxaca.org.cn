<?php
// 表格下载管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPlatformDownFilesBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\PlatformDownFiles';
    public static $table_name = 'platform_down_files';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
