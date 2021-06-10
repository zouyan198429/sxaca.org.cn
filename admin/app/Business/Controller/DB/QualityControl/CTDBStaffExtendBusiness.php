<?php
// 人员扩展信息
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBStaffExtendBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\StaffExtend';
    public static $table_name = 'staff_extend';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
