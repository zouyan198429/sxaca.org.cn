<?php

namespace App\Http\Controllers\Admin\QualityControl\Abilitys;

use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Http\Controllers\Admin\QualityControl\BasicController as ParentBasicController;
use Illuminate\Http\Request;

class BasicController extends ParentBasicController
{
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    // 通过能力验证id获得当前的对力验证对象
    public function getAbilityInfo($ability_id){
        if(!is_numeric($ability_id)) throws('参数【ability_id】有误！');
        $abilityInfo = CTAPIAbilitysBusiness::getInfoDataBase(\request(), $this,'', $ability_id, [], '', 1);
        if(!is_array($abilityInfo) || empty($abilityInfo)) $abilityInfo = [];
        // 对数据进行有效性验证
        if(empty($abilityInfo) || count($abilityInfo) <= 0){
            throws('能力验证信息不存在！');
        }
        return $abilityInfo;
    }

}
