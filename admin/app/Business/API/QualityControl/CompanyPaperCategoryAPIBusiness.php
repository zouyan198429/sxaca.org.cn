<?php
// 试卷题目
namespace App\Business\API\QualityControl;


class CompanyPaperCategoryAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyPaperCategory';
    public static $table_name = 'company_paper_category';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
