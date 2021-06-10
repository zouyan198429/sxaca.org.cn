<?php
// 机构处罚管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyPunishBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyPunish';
    public static $table_name = 'company_punish';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
