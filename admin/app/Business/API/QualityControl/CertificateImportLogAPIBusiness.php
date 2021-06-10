<?php
// 证书导入批次
namespace App\Business\API\QualityControl;


class CertificateImportLogAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CertificateImportLog';
    public static $table_name = 'certificate_import_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
