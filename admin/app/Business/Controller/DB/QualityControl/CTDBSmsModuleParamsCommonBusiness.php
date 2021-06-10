<?php
// 快捷常用参数
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSmsModuleParamsCommonBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\SmsModuleParamsCommon';
    public static $table_name = 'sms_module_params_common';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
