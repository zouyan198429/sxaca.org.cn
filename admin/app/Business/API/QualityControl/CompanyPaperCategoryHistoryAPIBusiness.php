<?php
// 试卷题目
namespace App\Business\API\QualityControl;


class CompanyPaperCategoryHistoryAPIBusiness extends CompanyPaperCategoryAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyPaperCategoryHistory';
    public static $table_name = 'company_paper_category_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
