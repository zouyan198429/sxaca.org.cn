<?php
// 检验检测机构资质认定证书附表-名称表【只增，不改不删】
namespace App\Business\API\QualityControl;


class CertificateNamesAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CertificateNames';
    public static $table_name = 'certificate_names';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
