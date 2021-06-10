<?php
// 直播公告内容
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBLiveNoticeContentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\LiveNoticeContent';
    public static $table_name = 'live_notice_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
