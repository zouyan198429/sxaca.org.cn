<?php
namespace App\Http\Controllers;

use App\Business\Controller\API\QualityControl\CTAPIApiLogBusiness;
use App\Business\Controller\API\QualityControl\CTAPIApplyBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\ModelsVerify\QualityControl\industry;
use App\Services\Request\API\Sites\APIRunBuyRequest;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use phpseclib\Math\BigInteger;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    /*
     *  Dingo
     * 响应生成器提供了一个流畅的接口去方便的建立一个更定制化的响应。响应的生成器通常是与 transformer 相结合。
     * 要利用响应生成器，你的控制器需要使用 Dingo\Api\Routing\Helpers trait。为了在你的控制器里保持引入和使用这个 trait，
     * 你可以创建一个基础控制器，然后你的所有的 API 控制器都继承它。
     *
     *
     */
    use Helpers;

    // 控制器方法判断是否api请求配置-- 1如果应用key为空，从appid重新获取下；2判断应用key是否为空；4判断应用信息不存；8判断非审核通过状态；
    //                                 16判断应用API密钥值是否为空
    //  API请求的【密钥】控制器 值为 1 | 2 | 4 | 8 | 16
    //  其它的有默认值 1
    const REQUEST_API_POWER_NO_COMMON = 1;
    const REQUEST_API_POWER_NO_API= 1 | 2 | 4 | 8 | 16;

    public $user_type = null;// 登录用户所属的后台类型  1平台2企业4个人
    public $company_id = null ;
    public $own_organize_id = null;// 组织id--登录者是企业的自己的id
    public $organize_id = null;// 组织id--所属企业
    public $personal_id = null;// 个人id--最底层登录人员id
    public $model_name = null;
    public $user_info = [];
    public $user_id = null;
    public $operate_staff_id = 0; // 操作人员id
    public $operate_staff_id_history = 0;// 操作人员历史id
    public $source = -1;// 来源-1网站页面，2ajax；3小程序
    // 是否从小程序来的请求
    public $redisKey = null;
    public $save_session = true;// true后台来的，false小程序来的
    // 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //        用途，如加入到登录状态session中，就可以一个浏览器同时登录多个后台。--让每一个后台session的键都唯一，不串（重）
    public $siteLoginUniqueKey = '';
    // 返回到前端的数据
    public $reDataArr = [
        'real_name'=>'',
//        'webName' => '',
//        'webIntro' => '',
//        'copyright' => '',
    ];
    //缓存
    public $cache_sel = 1 + 2;//是否强制不缓存 1:缓存读,读到则直接返回;2缓存数据

    public $admin_type = 0;// 当前用户的类型
    public $city_site_id = 0;
    public $city_partner_id = 0;
    public $seller_id = 0;
    public $shop_id = 0;
    public $errMethod = 'errorViewDo';// 根据错误码执行自己独特的操作方法 参数 $request, $reDataArr, $errCode, $errStr
    public $errorView = 'error';//  错误显示的视图

    // 权限相关的
    public $app_key = '';// 项目(应用)键key - appid[前端传入] 32位字串
    public $app_Info = [];// 应用详情
    public $app_id = 0;// 项目(应用)id-[根据$app_key从数据库获得]
    public $menu_id = 0;// 项目菜单id-menu_id[前端传入]
    public $frm_fun_id = 0;// 来源功能id-- frm_fun_id [前端传入]
    public $project_id = 0;// 项目id--后端代码分块 如大后台  企业后台  用户后台 前台
    public $controller_id = 0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同
    public $fun_id = 0;// 功能id fun_id
    // 基本的【固定的】
    // 1->1 首页；2->2 列表页；3->4 ajax列表；4->8 搜索按钮；5->16 添加页；6->32 添加提交按钮；7->64 编辑页；8->128 ajax详情； 注：详情页请看 35-> 17179869184
    // 9->256 编辑提交按钮；10->512 删除功能；11->1024 批量删除功能；12->2048 弹窗选择页面；
    // 13->4096 导出【按条件】；14->8192 导出【勾选】 ；15->16384 导入模版  ； 16->32768 导入；
    // 17->65536 冻结； 18->131072 批量冻结 ；19->262144 解冻 ；20->524288 批量解冻；
    // 21->1048576 审核通过 ；22->2097152 批量审核通过；  23->4194304 审核不通过 ； 24->8388608 批量审核不通过；
    // 25->16777216 上传图片；  26->33554432 上传excel； 27->67108864 上传word ；28->134217728 上传ppt ；29->268435456 上传pdf ；
    // 30-> 536870912 上传音频 ；31-> 1073741824 上传视频  ；32-> 2147483648 上传压缩文件；33-> 4294967296 获得所有的ids；
    // 34-> 8589934592 获得下一级的kv值
    // 35-> 17179869184 详情页
    // 36-> 34359738368 发送手机短信页
    // 37-> 68719476736 ajax 发送手机短信
    // 其它的，自定义的
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器

    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function InitParams(Request $request, $errDo = true)
    {

    }

    // 获得当前登录状态者的 是组织id
    public function initOwnOrganizeId(){
        // $userInfo = $this->user_info;
        return 0;// $this->user_id; 真正的企业后台用这个值 ； $userInfo['company_id'] ?? 0; 真正的个人后台用这个值 ；个人用0比较对
    }

    // 获得个人id--最底层登录人员id，如果是个人登录的话，否则为0
    // 各后台可重写此方法，特别是个人后台中心
    public function initPersonalId(){
        return 0;// $this->user_id; 真正的个人后台用这个值
    }

    // 获取
    //  -  $siteLoginUniqueKey 指定就使用指定的，没有，则使用设置的 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //
    // $errDo 如果未登录是否进行错误处理【抛异常等】 true:处理【抛异常等】--默认； false:不处理--因为有的前端页面可能需要自己去判断
    public function getUserInfo(Request $request, $siteLoginUniqueKey = '', $errDo = true){
        if(empty($siteLoginUniqueKey)) $siteLoginUniqueKey = $this->siteLoginUniqueKey;
        return Tool::getSession($this->redisKey, $this->save_session,
            config('public.sessionKey') . $siteLoginUniqueKey, config('public.sessionRedisTye'));
    }
    // 保存
    //  -  如果是小程序，注意 $preKey 值为0， $this->save_session 设置为false
    //  -  如果是浏览器后台，注意 $preKey 值为1， $this->save_session 设置为 true
    //  -  $siteLoginUniqueKey 指定就使用指定的，没有，则使用设置的 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //
    // $userInfo 需要缓存的数据
    // $preKey  1 后台 ; 0 小程序
    public function setUserInfo($userInfo = '',$preKey = -1, $siteLoginUniqueKey = ''){
        //$preKey 为 -1,则根据 $this->save_session 来处理
        if($preKey == -1){
            $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') . config('public.sessionValPre') . ((int) $this->save_session ) . '_';
        }else{
            // .. 0/1
            $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') . config('public.sessionValPre') . ((int) $preKey ) . '_';
        }

        if(is_array($userInfo)) $userInfo['modifyTime'] = time();// 当前缓存的时间
        if(empty($siteLoginUniqueKey)) $siteLoginUniqueKey = $this->siteLoginUniqueKey;
        $redisKey = Tool::setLoginSession($pre, $userInfo,
            $this->save_session, config('public.sessionKey') . $siteLoginUniqueKey,
            config('public.sessionExpire'), config('public.sessionRedisTye'));
        return $redisKey;
    }

    // 删除
    //  -  $siteLoginUniqueKey 指定就使用指定的，没有，则使用设置的 每一种登录项的唯一标识【大后台：adimn; 企业：company;用户：user】,每一种后台控制器父类，修改成自己的唯一值
    //
    public function delUserInfo($siteLoginUniqueKey = ''){
        if(empty($siteLoginUniqueKey)) $siteLoginUniqueKey = $this->siteLoginUniqueKey;
        return Tool::delSession($this->redisKey, $this->save_session, config('public.sessionKey') . $siteLoginUniqueKey);
    }

    // 公共方法

    /**
     * 获得缓存数据
     *
     * @param string $pre 键前缀 __FUNCTION__
     * @param string $cacheKey 键
     * @param array $paramKeyValArr 会作为键的关键参数值数组 --一维数组
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ;
     * @param keyPush 键加入无素 1 $pre 键前缀 2 当前控制器方法名;
     * @return mixed  ; false失败
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheData($pre, &$cacheKey, $paramKeyValArr, $operate = 1, $keyPush = 0){
        return Tool::getCacheData($pre, $cacheKey, $paramKeyValArr, $operate, $keyPush);
    }

    /**
     * 保存redis值-json/序列化保存
     * @param string 必填 $pre 前缀
     * @param string $key 键 null 自动生成
     * @param string 选填 $cacheData 需要保存的值，如果是对象或数组，则序列化
     * @param int 选填 $expire 有效期 秒 <=0 长期有效
     * @param int 选填 $operate 操作 1 转为json 2 序列化
     * @return $key
     * @author zouyan(305463219@qq.com)
     */
    public function setCacheData($pre, $cacheKey, $cacheData, $expire = 60, $operate =1){
        // 缓存数据
        return Tool::cacheData($pre, $cacheKey, $cacheData, $expire, $operate); // 1分钟
    }

    /** 使用

    // 获得 redis缓存数据  ; 1:缓存读,读到则直接返回
    if( ($this->cache_sel & 1) == 1){
        $cachePre = __FUNCTION__;// 缓存前缀
        $cacheKey = '';// 缓存键[没算前缀]
        $paramKeyValArr = $request->input();//[$company_id, $operate_no];// 关键参数  $request->input()
        $cacheResult =$this->getCacheData($cachePre,$cacheKey, $paramKeyValArr , 1, 1 + 2);
        if($cacheResult !== false) return $cacheResult;
    }
     *
     *
     *
    // 缓存数据
    if( ($this->cache_sel & 2) == 2) {
        $this->setCacheData($cachePre, $cacheKey, $resultData, 60, 1);
    }
     */

    /**
     * 判断当前要操作的数据，是当前登录用户可以操作的
     * @param int $company_id 要判断的 记录 所属平台 id -- 0: 【不判断】
     * @param int $organize_id  要判断的 记录  组织id--所属企业 -- 0: 【不判断】
     * @param int $personal_id 要判断的 个人id--最底层登录人员id -- 0: 【不判断】
     * @param int $is_company_own 如果是企业后台，判断权限时，organize_id 企业 id 为 true: 记录为企业下级的 【默认】； false: 组织id--登录者是企业的自己的id
     * @return boolean 是否有权限  true:有权限  ； false:无权限
     * @author zouyan(305463219@qq.com)
     */
    public function judgeRecordOperateAuth($company_id = 0, $organize_id = 0, $personal_id = 0, $is_company_own = true){
        // own_ 开头的  -- -1: 忽略【不判断】
        $own_company_id = -1;// $this->company_id;
        $login_organize_id = $this->organize_id;
        // 是企业，则是企业自己的id  -- 组织id--登录者是企业的自己的id
        if($this->user_type == 2 && !$is_company_own) $login_organize_id = $this->own_organize_id;
        return Tool::judgeOwnOperateAuth($this->user_type, $own_company_id, $company_id, $login_organize_id, $organize_id, $this->personal_id, $personal_id);
    }

    /**
     * 判断当前要操作的数据，是当前登录用户可以操作的
     * @param array $data 需要判断的数组  一维或二维数组
     * @param array $powerFields 在数组值中，用来判断权限的字段名称指定
     *   $powerFields = [
     *       'company_id' => '',// 所属平台 id 在 数组中的下标，--为空，则不用数组中的字段来判断;--没有字段，则不用此下标或为空值
     *      'organize_id' => '',// 组织id--所属企业 在 数组中的下标，--为空，则不用数组中的字段来判断;--没有字段，则不用此下标或为空值
     *      'personal_id' => '',// 个人id--最底层登录人员id 在 数组中的下标，--为空，则不用数组中的字段来判断;--没有字段，则不用此下标或为空值
     *  ];
     * @param int $company_id 要判断的 记录 所属平台 id -- 0: 【不判断】 --- 如果有 > 0 的值，则 使用这里的，不然使用 $powerFields
     * @param int $organize_id  要判断的 记录  组织id--所属企业 -- 0: 【不判断】 --- 如果有 > 0 的值，则 使用这里的，不然使用 $powerFields
     * @param int $personal_id 要判断的 个人id--最底层登录人员id -- 0: 【不判断】 --- 如果有 > 0 的值，则 使用这里的，不然使用 $powerFields
     * @param int $is_company_own 如果是企业后台，判断权限时，organize_id 企业 id 为 true: 记录为企业下级的 【默认】； false: 组织id--登录者是企业的自己的id
     * @return boolean 是否有权限  true:有权限  ； false:无权限
     * @author zouyan(305463219@qq.com)
     */
    public function batchJudgeRecordOperateAuth($data = [], $powerFields = [], $company_id = 0, $organize_id = 0, $personal_id = 0, $is_company_own = true){
        // own_ 开头的  -- -1: 忽略【不判断】
        $own_company_id = -1;// $this->company_id;
        $login_organize_id = $this->organize_id;
        // 是企业，则是企业自己的id  -- 组织id--登录者是企业的自己的id
        if($this->user_type == 2 && !$is_company_own) $login_organize_id = $this->own_organize_id;
        return Tool::batchJudgeOwnOperateAuth($data, $powerFields, $this->user_type, $own_company_id, $company_id, $login_organize_id, $organize_id, $this->personal_id, $personal_id);
    }

    /**
     * 错误页面视图
     * @param array $reDataArr 在数组值中，用来判断权限的字段名称指定
     *   $reDataArr = [
     *       'errorMsg' => '',// 错误文字 ，  您没有操作权限
     *      'isShowBtn' => '',// // 1:显示“回到首页”；2：显示“返回上页”
     *  ];
     * @param string $errorView 错误显示的视图
     * @return mixed 返回视图
     * @author zouyan(305463219@qq.com)
     */
    public function errorView($reDataArr = [], $errorView = 'error'){
//        $reDataArr['errorMsg'] = '您没有操作权限！';
//        $reDataArr['isShowBtn'] = 1;// 1:显示“回到首页”；2：显示“返回上页”
        if(!isset($reDataArr['errorMsg'])) $reDataArr['errorMsg'] = '您没有操作权限！';
        if(!isset($reDataArr['isShowBtn'])) $reDataArr['isShowBtn'] = 0;
        return view($errorView, $reDataArr);
    }

    /**
     * 页面视图捕获到错误时，根据错误码执行自己独特的操作
     * @param Request $request
     * @param array $reDataArr 在数组值中，用来判断权限的字段名称指定,以及其它想传到错误视图的变量
     *   $reDataArr = [
     *       'errorMsg' => '',// 错误文字 ，  您没有操作权限---此值是throws抛出的错误内容
     *      'isShowBtn' => '',// // 1:显示“回到首页”；2：显示“返回上页”
     *       ...
     *  ];
     * @param int $errCode 错误码
     * @param int $errStr 错误的内容
     * @param string $errorView 错误显示的视图
     * @return mixed 返回视图 或 true:没有返回视图
     * @author zouyan(305463219@qq.com)
     */
    public function errorViewDo(Request $request, $reDataArr, $errCode, $errStr = ''){
        switch($errCode){
            case 700:// 登录状态过期
                break;
            case 1001:// 待补充企业资料
                return redirect('web/perfect_company');
                break;
            case 1002:// 待补充用户资料
                return redirect('web/perfect_user');
                break;
            default:
                break;
        }
        return true;
    }

    // 获得没有登录或登录状态失效时的错误码
    // $source = -1;// 来源-1网站页面，2ajax；3小程序
    // 如果是 2ajax；3小程序，则 返回source的原值，否则，返回999【页面调用来请求数据的】
    public function getNotLoginErrCode(){
        return in_array($this->source, [2,3]) ?  $this->source : 999;//
    }

    // **************公用方法*****************************************************

    /**
     * --- 用这个调用
     * 公用页 --- 各自定义 $extendParams['doFun'] 参数指写的方法 【优先】 或 执行闭包函数 $exeFun
     *  因为 添加页  、 详情页 、 添加对外数据接口  ， 其实数据都是一样的，或其它相同的页，--为了不多份代码，只有一份，方便管理，才有了此方法
     *  列表或其它页也有这样的问题。
     *
     * @param Request $request
     * @param int $pageNum // 页面序号 同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     * @param int $returnType // 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     * @param string $view // 显示的视图名  默认index  admin.QualityControl.Citys.index
     * @param boolean $hasJudgePower // 是否需要判断登录权限 true:判断[默认]  false:不判断
     * @param string $doFun // 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；-- 为空值 "":则使用 $exeFun 方法
     *                               doListPage： 列表页； doInfoPage：详情页
     * @param array $params // 需要传入 doFun 的数据 数组[一维或多维]
     * @param mixed $exeFun 如果传入的是一个函数 ，则执行此函数 可有返回值 参数： &$reDataArr，否则执行  $extendParams['doFun'] 指定的方法
     * @param mixed $request_api_power_no 控制器方法判断是否api请求配置
     *                              1如果应用key为空，从appid重新获取下；2判断应用key是否为空；4判断应用信息不存；8判断非审核通过状态；16判断应用API密钥值是否为空
     *                           API请求的【密钥】控制器 值为 1 | 2 | 4 | 8 | 16
     *                           其它的有默认值 1
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function exeDoPublicFun(Request $request, $pageNum = 1, $returnType = 1, $view = '', $hasJudgePower = true, $doFun = 'doListPage', $params = [], $exeFun = '', $request_api_power_no = 1){
        $extendParams = ['pageNum' => $pageNum,'returnType' => $returnType, 'view' => $view
            , 'hasJudgePower' => $hasJudgePower, 'doFun' => $doFun, 'params' => $params];

        // 如果是应用调用接口，则写接口调用日志
        $appInfo = $this->getApplyInfo($request, '', $request_api_power_no);
        if(!empty($appInfo)){
            return CTAPIApiLogBusiness::exeApi($request, $this, $pageNum, function() use(&$request, &$extendParams, &$exeFun){

                return $this->exePublicFun($request, $extendParams, $exeFun);
            }, 1);
        }else{
            return $this->exePublicFun($request, $extendParams, $exeFun);
        }

    }

    /**
     * 公用页 --- 各自定义 $extendParams['doFun'] 参数指写的方法 【优先】 或 执行闭包函数 $exeFun
     *  因为 添加页  、 详情页 、 添加对外数据接口  ， 其实数据都是一样的，或其它相同的页，--为了不多份代码，只有一份，方便管理，才有了此方法
     *  列表或其它页也有这样的问题。
     *
     *  这个是直接调用参考
     *         return $this->exePublicFun($request, ['pageNum' => 1,'returnType' => 1, 'view' => 'admin.QualityControl.Citys.index'
     *      , 'hasJudgePower' => true, 'doFun' => 'doListPage', 'params' => []], function (&$reDataArr) use ($request){
     *
     *       });
     * 这个是间接调用参考----主要用这个来调用
     *   return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.Citys.index', true
     *   , '', [], function (&$reDataArr) use ($request){
     *
     *   });
     * @param Request $request
     * @param array $extendParams // 扩展参数
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      $extendParams = [
     *          'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *          'view' => 'index', // 显示的视图名 默认index
     *          'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *          'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *          'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @param mixed $exeFun 如果传入的是一个函数 ，则执行此函数 可有返回值 参数： &$reDataArr，否则执行  $extendParams['doFun'] 指定的方法
     * @return mixed 返回视图或 ajax的json数据
     * @author zouyan(305463219@qq.com)
     */
    public function exePublicFun(Request $request,  $extendParams = [], $exeFun = ''){
        $returnType = $extendParams['returnType'] ?? 1;
        $reDataArr = [];// 可以传给视图的全局变量数组
        if(in_array($returnType, [1,8])){// 视图--出错会把错误信息输出到错误视图
            return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$extendParams, $exeFun){
                return $this->exePublicFunBody($request,  $extendParams, $exeFun, $reDataArr);
            }, $this->errMethod, $reDataArr, $this->errorView);
        }else{// ajax --- 出错也会是ajax格式
            return $this->exePublicFunBody($request,  $extendParams, $exeFun, $reDataArr);
        }
    }

    /**
     * 公用页 --- 各自定义 $extendParams['doFun'] 参数指写的方法 【优先】 或 执行闭包函数 $exeFun
     *  因为 添加页  、 详情页 、 添加对外数据接口  ， 其实数据都是一样的，或其它相同的页，--为了不多份代码，只有一份，方便管理，才有了此方法
     *  列表或其它页也有这样的问题。
     *
     *  这个是直接调用参考
     *         return $this->exePublicFun($request, ['pageNum' => 1,'returnType' => 1, 'view' => 'admin.QualityControl.Citys.index'
     *      , 'hasJudgePower' => true, 'doFun' => 'doListPage', 'params' => []], function (&$reDataArr) use ($request){
     *
     *       });
     * 这个是间接调用参考----主要用这个来调用
     *   return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.Citys.index', true
     *   , '', [], function (&$reDataArr) use ($request){
     *
     *   });
     * @param Request $request
     * @param array $extendParams // 扩展参数
     *      'pageNum' => 1,// 页面序号 同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *   $extendParams = [
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @param mixed $exeFun 如果传入的是一个函数 ，则执行此函数 可有返回值 参数： &$reDataArr，否则执行  $extendParams['doFun'] 指定的方法
     * @return mixed 返回视图或 ajax的json数据
     * @author zouyan(305463219@qq.com)
     */
    public function exePublicFunBody(Request $request,  $extendParams = [], $exeFun = '', $reDataArr = []){
        $pageNum = $extendParams['pageNum'] ?? 1;
        $this->fun_id = $pageNum;
        $returnType = $extendParams['returnType'] ?? 1;
        $view = $extendParams['view'] ?? 'index';
        $hasJudgePower = $extendParams['hasJudgePower'] ?? true;
        $doFun = $extendParams['doFun'] ?? '';
        $params = $extendParams['params'] ?? [];
        // 正常流程的代码

        // if($hasJudgePower) $this->InitParams($request);
        $this->InitParams($request, $hasJudgePower);
        // $reDataArr = $this->reDataArr;
        $reDataArr = array_merge($reDataArr, $this->reDataArr);
        // 对权限进行判断
        // 参数 项目菜单id-menu_id[前端传入]、来源功能id-- frm_fun_id [前端传入]、功能小模块[控制器]id - controller_id、 功能id fun_id、  用户id --user_id
//        $this->app_key = CommonRequest::get($request, 'appid');// 项目(应用)键key - appid[前端传入] 32位字串
//        if(strlen($this->app_key) > 0 && ($this->app_key != 0)){
//            $this->app_Info = $this->getApplyInfo($request, $this->app_key, 1 | 2 | 4 | 8 | 16);
//
//            $staffId = $this->app_Info['staff_id'] ?? 0;
//            $this->getStaffInfoById($request, $staffId, 1 | 2 | 4 | 8 | 16 | 32, true, [$this->user_type]);
//        }
        $this->menu_id = CommonRequest::getInt($request, 'menu_id');// 项目菜单id-menu_id[前端传入]
        $this->frm_fun_id = CommonRequest::getInt($request, 'frm_fun_id');// 来源功能id-- frm_fun_id [前端传入]
        // 获和权限
        $ower_funs = [];// 也有的页面功能权限数组--后台再处理
        $reDataArr['power'] = [// 将权限传给前端页面
            // 'app_key' => $this->app_key, // 重要数据，不能返回给前端
            'menu_id' => $this->menu_id,
            'frm_fun_id' => $this->frm_fun_id,
            'project_id' => $this->project_id,
            'controller_id' => $this->controller_id,
            'fun_id' => $this->fun_id,
            'ower_funs' => $ower_funs,
        ];

        // 其它业务逻辑....
        // 执行方法也可以返回Ajax
        $result = '';
        if($doFun == '' && is_callable($exeFun)){
            $result = $exeFun($reDataArr);
        }else if($doFun != ''){
            $result = $this->{$doFun}($request, $reDataArr, $extendParams);
        }
        if($returnType == 4) return $result;// 4 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
        if($returnType == 2)  return ajaxDataArr(1, $reDataArr, '') ;// 2 ajax请求的json数据

        if(is_object($result)) return $result;// 如果方法体内返回的是对象，则直接返回--可能是redirect...
        return view($view, $reDataArr);//  1 视图[默认]

    }

    // **************公用重写方法********************开始*********************************
    /**
     * 公用列表页 --- 可以重写此方法--需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doListPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        $reDataArr['hidden_option'] = $hiddenOption;
    }

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
//         $user_info = $this->user_info;
//        $id = $extendParams['params']['id'] ?? 0;
//
////        // 拥有者类型1平台2企业4个人
////        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
////        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
//        $info = [
//            'id'=>$id,
//            //   'department_id' => 0,
//        ];
//        $operate = "添加";
//
//        if ($id > 0) { // 获得详情数据
//            $operate = "修改";
//            $info = CTAPIRrrDdddBusiness::getInfoData($request, $this, $id, [], '', []);
//        }
//        // $reDataArr = array_merge($reDataArr, $resultDatas);
//        $reDataArr['info'] = $info;
//        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用重写方法********************结束*********************************
    // **************公用方法*****************************************************

    //~~~~~~~~~~~~~~~微信内浏览器支付~~获得用户openid~登录~~~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // 判断是否在微信内访问
    public function judgeWeixinVisit(){
        if(!static::$ALLOW_BROWSER_OPEN && !Tool::isWeixinVisit()){
            if(isAjax()){
                throws('请在微信内访问！');
            }else{
                die('请在微信内访问！');
            }
        }
    }

    // 获得用户的微信id[没有时，去请求微信]
    // 用卡密登陆过后的 缓存键 $cache_pre --唯一
    // $scopes 授权方式 snsapi_base 【默认】 snsapi_userinfo
    // $callback 回调url  url('/api/wx/callback') 中的 '/api/wx/callback' 部分
    // $initWechatAppKey 要实列化的微权三方 对象的键名 默认  'wechat.official_account'
    // $session_openid_key 保存 用户 openid 的session 键名
    // $session_do_url_key 保存 当前正在访问地址的 的session 键名
    // 返回 $openid - string 或 请求微信登录
    public function autoGetOpenid(Request $request, $cache_pre = '', $scopes = 'snsapi_base', $callback = 'api/wx/callback', $initWechatAppKey = 'wechat.official_account', $session_openid_key = 'openid', $session_do_url_key = 'wechat_back_url'){

//        ini_set('session.use_strict_mode', 0);//关闭严格模式
//        ini_set('session.use_cookies', 0);//禁止通过cookie传递session id
//        //获取open id比较简单，就不再赘述了……
//        //微信公众号scywzh，假设 $openId 为从微信服务器得到的用户 open id.
//        $openId = '123';
//        $sessionId = md5($openId);
//        session_id($sessionId);
//        session_start();

        if(empty($cache_pre)) throws('缓存键不能为空！');

        //下面就可以像平常一样该干嘛干嘛了...
        $this->judgeWeixinVisit();// 判断是否微信内浏览器
        $session_openid_key = $cache_pre . '_openid';
        $openid = Tool::getRedis($session_openid_key, 3);
//         $openid = SessionCustom::get($session_openid_key, true);
//        $openid = CookieOperate::get($session_openid_key);
        Log::info('微信日志-登陆情况信息',[$openid]);
        if(empty($openid)){// 没有登录
            Log::info('微信日志-登陆',['未登陆']);
            $session_do_url_key = $cache_pre . '_wechat_back_url';
            $doUrl = $request->fullUrl();// 记录当前正在访问的页面，
//            SessionCustom::set($session_do_url_key, $doUrl, 600);
//            CookieOperate::set($session_do_url_key, $doUrl, 600);
            Tool::setRedis('', $session_do_url_key, $doUrl, 60 * 10 , 3);

//            $target_url = SessionCustom::get($session_do_url_key);
//            pr($target_url);
            // 开始微信授权
            $app = app($initWechatAppKey);
            $oauth = $app->oauth;
            // return $oauth->redirect();

            return $app->oauth->scopes([$scopes])
                ->redirect(url($callback));
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
            // $oauth->redirect()->send();
        }
        Log::info('微信日志-登陆',['已经登录过']);
        return $openid;
    }

    // 具体的使用请参考提货卡的Site/WeChatController.php--回调及相关路由

    // 获得用户的微信id[没有时，去请求微信]
    // 用卡密登陆过后的 缓存键 $cache_pre --唯一
    // 具体参数说明请查看 autoGetOpenid方法
    // 返回 $openid - string 或 请求微信登录
    public function autoSiteGetOpenid(Request $request, $cache_pre = '', $scopes = 'snsapi_base', $initWechatAppKey = 'wechat.official_account'){
        $callback = 'api/site/wx/callback/' . $cache_pre;
        $session_openid_key = 'openid';
        $session_do_url_key = 'wechat_back_url';
        return $this->autoGetOpenid($request, $cache_pre, $scopes, $callback, $initWechatAppKey, $session_openid_key, $session_do_url_key);
    }

    //~~~~~~~~~~~~~~~微信内浏览器支付~~获得用户openid~登录~~~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // 根据应用key 获得应用详情
    // $appid 应用key
    // $power_no 1如果应用key为空，从appid重新获取下；2判断应用key是否为空；4判断应用信息不存；8判断非审核通过状态；16判断应用API密钥值是否为空
    // $admin_type_arr 如果判断用户类型时，可进行访问的用户类型id数组
    public function getApplyInfo(Request $request, $appid, $power_no = 0){
        if( ($power_no & 1) == 1 && empty($appid)) $appid = CommonRequest::get($request, 'appid');
        if( ($power_no & 2) == 2 && empty($appid)) throws('appid参数不存在或值不能为空！');
        // 根据appid,获得API密钥
        $appInfo = [];
        if(!empty($appid)) $appInfo = CTAPIApplyBusiness::getFVFormatList( $request,  $this, 4, 1
            , ['app_id' => $appid], false, [], []);

        if( ($power_no & 4) == 4 && empty($appInfo))  throws('应用信息不存在，appid值不正确！请自查或联系技术人员核对！');
        $open_status = $appInfo['open_status'] ?? 0;
        if( ($power_no & 8) == 8 && !in_array($open_status, [2])) throws('应用非审核通过状态，不可使用！');
        $appAecret = $appInfo['app_secret'] ?? '';
        if( ($power_no & 16) == 16 && empty($appAecret))  throws('应用API密钥值不存在或值不能为空!');

        // 有户判断不在这里判断了，在需要的地方记得调用用户的方法
//        $staffId = $appInfo['staff_id'] ?? 0;
//        $userInfo = $this->getStaffInfoById($request, $staffId);

        return $appInfo;
    }

    // 根据用户id ，判断权限
    // $power_no 判断 1：判断是否存在；2 判断用户类型id；4判断是否用户已冻结；8 判断审核中；16 判断审核未通过；32判断非审核通过；；；；；
    // $delSession 是否需要删除登录session true：需要[默认] ； false:不需要
    // $admin_type_arr 如果判断用户类型时，可进行访问的用户类型id数组
    public function getStaffInfoById(Request $request, $staff_id = 0, $power_no = 0, $delSession = true, $admin_type_arr = []){
        if(!is_numeric($staff_id)) throws('参数【staff_id】有误！');
        // $userInfo = CTAPIStaffBusiness::getInfoDataBase(\request(), $this,'', $staff_id, [], '', 1);
        $userInfo = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 4, 1
            , ['id' => $staff_id], false, [], []);
        if(!is_array($userInfo) || empty($userInfo)) $userInfo = [];
        // 对数据进行有效性验证
        if( ($power_no & 1) == 1 && (empty($userInfo) || count($userInfo) <= 0)){
            if($delSession) $this->delUserInfo();
            throws('用户名信息不存在！');
        }

        if( ($power_no & 2) == 2 && !in_array($userInfo['admin_type'], $admin_type_arr) ){ // && $userInfo['admin_type'] != 1
            if($delSession) $this->delUserInfo();
            throws('非法访问！');
        }

        if( ($power_no & 4) == 4 && $userInfo['account_status'] == 2 ){
            if($delSession) $this->delUserInfo();
            throws('用户已冻结！');
        }

        if( ($power_no & 8) == 8 && $userInfo['open_status'] == 1 ){
            if($delSession) $this->delUserInfo();
            throws('审核中，请耐心等待！');
        }
        if( ($power_no & 16) == 16 && $userInfo['open_status'] == 4 ){
            if($delSession) $this->delUserInfo();
            throws('审核未通过！');
        }
        if( ($power_no & 32) == 32 && $userInfo['open_status'] != 2 ){
            if($delSession) $this->delUserInfo();
            throws('非审核通过！');
        }
        return $userInfo;
    }
}
