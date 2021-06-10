<?php
// 证书表
namespace App\Business\API\QualityControl;


class CertificateAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Certificate';
    public static $table_name = 'certificate';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
