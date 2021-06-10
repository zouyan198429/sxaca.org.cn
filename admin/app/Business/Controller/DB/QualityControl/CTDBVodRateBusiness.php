<?php
// 点播课程学员学习进度
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodRateBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodRate';
    public static $table_name = 'vod_rate';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
