<?php
// 短信模板所属模块
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSmsModuleBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\SmsModule';
    public static $table_name = 'sms_module';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
