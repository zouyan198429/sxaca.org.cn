<?php
// 能力验证检测方法依据
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityJoinItemsResultsMethodBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsResultsMethod';
    public static $table_name = 'ability_join_items_results_method';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
