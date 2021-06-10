<?php
// 老师登录验证码
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSmsCodeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\SmsCode';
    public static $table_name = 'sms_code';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
