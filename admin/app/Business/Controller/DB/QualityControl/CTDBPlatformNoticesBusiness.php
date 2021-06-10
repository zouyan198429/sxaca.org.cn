<?php
// 通知公告管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPlatformNoticesBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\PlatformNotices';
    public static $table_name = 'platform_notices';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
