<?php

namespace App\Http\Controllers;

use App\Services\DB\CommonDB;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompController extends ApiController
{
     protected $company_id = null;
    // protected $pro_unit_id = null;

    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function InitParams(Request $request, $errDo = true)
    {
        $not_log = CommonRequest::getInt($request, 'not_log');
        if($not_log != 1){
            $company_id = CommonRequest::getInt($request, 'company_id');

            Tool::judgeInitParams('company_id', $company_id);
            $this->company_id = $company_id;
        }
    }
}
