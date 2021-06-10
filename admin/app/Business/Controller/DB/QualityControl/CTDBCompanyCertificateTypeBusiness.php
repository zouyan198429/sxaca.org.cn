<?php
// 资质证书类型[一级分类]
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyCertificateTypeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyCertificateType';
    public static $table_name = 'company_certificate_type';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
