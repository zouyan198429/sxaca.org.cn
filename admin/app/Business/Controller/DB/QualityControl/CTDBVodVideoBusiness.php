<?php
// 点播课程视频目录
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodVideoBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodVideo';
    public static $table_name = 'vod_video';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
