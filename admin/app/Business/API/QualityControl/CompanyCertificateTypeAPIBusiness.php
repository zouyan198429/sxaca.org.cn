<?php
// 资质证书类型[一级分类]
namespace App\Business\API\QualityControl;


class CompanyCertificateTypeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyCertificateType';
    public static $table_name = 'company_certificate_type';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
