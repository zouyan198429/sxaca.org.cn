<?php
// 检验检测机构资质认定证书附表-名称表【只增，不改不删】
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCertificateNamesBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CertificateNames';
    public static $table_name = 'certificate_names';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
