<?php
// 选民组表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBVoterGroupBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\VoterGroup';
    public static $table_name = 'voter_group';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
