<?php

namespace App\Models\QualityControl;

use App\Models\BaseCacheModel;

class BasePublicModel extends BaseCacheModel
{
    public static $modelPath = "QualityControl"; // 模型路径 名称\..\名称


    //---------一对多--------开始----------

    /**
     * 获取**模块[如相册]所属的公司 - 一维
     * 公司对其它，1：n的反向
     */
//    public function CompanyInfo()
//    {
//        return $this->belongsTo('App\Models\QualityControl\Company', 'company_id', 'id');
//    }

    /**
     * 获取操作***表的员工-一维
     */
    public function oprateStaff()
    {
        return $this->belongsTo('App\Models\QualityControl\Staff', 'operate_staff_id', 'id');
    }

    /**
     * 获取操作***表员工的历史-一维
     */
    public function oprateStaffHistory()
    {
        return $this->belongsTo('App\Models\QualityControl\StaffHistory', 'operate_staff_id_history', 'id');
    }

    /**
     * 获取**模块[如家事记录]所属的生产单元 - 一维
     * 生产单元对其它，1：n的反向
     */
//    public function companyProUnit()
//    {
//        return $this->belongsTo('App\Models\QualityControl\CompanyProUnit', 'pro_unit_id', 'id');
//    }

    /**
     * 获取**模块的操作人员所属的帐号 - 一维
     * 帐号对其它模块操作人，1：n的反向
     */
//    public function companyAccount()
//    {
//        return $this->belongsTo('App\Models\QualityControl\CompanyAccounts', 'account_id', 'id');
//    }

    /**
     * 获取**模块province_id 对应的省
     */
//    public function province()
//    {
//        return $this->belongsTo('App\Models\QualityControl\City', 'province_id', 'id');
//    }

    /**
     * 获取**模块province_id 对应的省历史
     */
//    public function provinceHistory()
//    {
//        return $this->belongsTo('App\Models\QualityControl\CityHistory', 'province_id_history', 'id');
//    }

    /**
     * 获取**模块city_id 对应的市
     */
//    public function city()
//    {
//        return $this->belongsTo('App\Models\QualityControl\City', 'city_id', 'id');
//    }

    /**
     * 获取**模块city_id 对应的市历史
     */
//    public function cityHistory()
//    {
//        return $this->belongsTo('App\Models\QualityControl\CityHistory', 'city_id_history', 'id');
//    }

    /**
     * 获取**模块area_id 对应的城市[包括县乡]
     */
//    public function area()
//    {
//        return $this->belongsTo('App\Models\QualityControl\City', 'area_id', 'id');
//    }

    /**
     * 获取**模块area_id 对应的城市[包括县乡]历史
     */
//    public function areaHistory()
//    {
//        return $this->belongsTo('App\Models\QualityControl\CityHistory', 'area_id_history', 'id');
//    }
    //---------一对多--------结束----------

}
