<?php
// 所属企业检验检测机构资质认定证书附表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCertificateScheduleBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CertificateSchedule';
    public static $table_name = 'certificate_schedule';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
