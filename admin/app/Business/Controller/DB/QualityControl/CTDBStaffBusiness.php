<?php
// 帐号管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBStaffBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Staff';
    public static $table_name = 'staff';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
