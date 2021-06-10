<?php
// 点播课程分类
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodTypeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodType';
    public static $table_name = 'vod_type';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
