<?php
// 能力验证
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyAbilityBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyAbility';
    public static $table_name = 'company_ability';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
