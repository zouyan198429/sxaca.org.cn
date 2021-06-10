<?php
// 限次配置
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSmsLimitBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\SmsLimit';
    public static $table_name = 'sms_limit';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
