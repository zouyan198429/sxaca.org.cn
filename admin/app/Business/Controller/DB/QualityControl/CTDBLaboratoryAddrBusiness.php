<?php
// 实验室地址
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBLaboratoryAddrBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\LaboratoryAddr';
    public static $table_name = 'laboratory_addr';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
