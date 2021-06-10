<?php
// 证书表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCertificateBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Certificate';
    public static $table_name = 'certificate';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
