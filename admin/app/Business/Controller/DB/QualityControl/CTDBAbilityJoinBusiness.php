<?php
// 能力验证报名主表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityJoinBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoin';
    public static $table_name = 'ability_join';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
