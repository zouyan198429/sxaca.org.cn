<?php
// 点播课程评论
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVodCommentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VodComment';
    public static $table_name = 'vod_comment';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
