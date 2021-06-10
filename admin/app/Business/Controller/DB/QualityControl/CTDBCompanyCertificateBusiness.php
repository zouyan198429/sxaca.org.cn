<?php
// 企业资质证书
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyCertificateBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyCertificate';
    public static $table_name = 'company_certificate';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
