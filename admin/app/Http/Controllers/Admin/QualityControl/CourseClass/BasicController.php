<?php

namespace App\Http\Controllers\Admin\QualityControl\CourseClass;

use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Http\Controllers\Admin\QualityControl\BasicController as ParentBasicController;
use Illuminate\Http\Request;

class BasicController extends ParentBasicController
{
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    // 通过班级id获得当前的班级对象
    public function getCourseClassInfo($class_id){
        if(!is_numeric($class_id)) throws('参数【class_id】有误！');

        // $abilityInfo = CTAPICourseClassBusiness::getInfoDataBase(\request(), $this,'', $class_id, [], '', 1);

        $handleKeyConfigArr = [
            'city_info' => '',
            'course_name' => '',
        ];
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPICourseClassBusiness::getRelationConfigs(\request(), $this, $handleKeyConfigArr, []),
            // 'infoHandleKeyArr' => ['resetPayMethod']
            'listHandleKeyArr' => ['initPayMethodText']
        ];
        $abilityInfo = CTAPICourseClassBusiness::getInfoData(\request(), $this, $class_id, [], '', $extParams);

        if(!is_array($abilityInfo) || empty($abilityInfo)) $abilityInfo = [];
        // 对数据进行有效性验证
        if(empty($abilityInfo) || count($abilityInfo) <= 0){
            throws('班级信息不存在！');
        }
        return $abilityInfo;
    }

}
