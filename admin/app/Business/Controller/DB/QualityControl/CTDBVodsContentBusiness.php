<?php
// 点播课程内容
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodsContentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodsContent';
    public static $table_name = 'vods_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
