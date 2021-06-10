<?php
// 示例函数
function foo() {
    return "foo";
}

function vd($s, $exit = true)
{
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $exit && exit();
}

function pr($s, $exit = true)
{
    echo '<pre>';
    print_r($s);
    echo '</pre>';
    $exit && exit();
}

function throwsTest($message){
    if(is_array($message) || is_object($message)) $message = json_encode($message);
    throws($message);
}

//function throws($message, $code = -1)
//{
//    $controller = request()->route()->getController();// 获得当前控制器对象
//    // $action = request()->route()->getAction();// action 数组
//    // $actionName = request()->route()->getActionName();// App\Http\Controllers\WX\MiniProgramController@test
//    $source = $controller->source ?? '';
//    if($code == -1 && is_numeric($source)) $code = $source;
//    throw new \App\Exceptions\ExportException($message, $code);
//}

function throws($message, $code = -1)
{
    try {
        $controller = request()->route()->getController();// 获得当前控制器对象
        // $action = request()->route()->getAction();// action 数组
        // $actionName = request()->route()->getActionName();// App\Http\Controllers\WX\MiniProgramController@test
        $source = $controller->source ?? '';
        if($code == -1 && is_numeric($source)) $code = $source;
    } catch ( \Exception $e) {
    }finally{
        throwsException($message, $code);
    }
    // throw new \App\Exceptions\ExportException($message, $code);
}

function throwsException($message, $code = -1)
{
    throw new \App\Exceptions\ExportException($message, (int) $code);
}

//判断数据不是JSON格式:
function isNotJson($str){
    return is_null(json_decode($str));
}

/**
 * 判断当前请求是否ajax请求
 * @return bool true　ajax请求　false非ajax请求
 */
function isAjax()
{
    $returnErrType = request()->header('returnErrType', 0);// 错误回返类型 1 json ; 0 非json
    \Illuminate\Support\Facades\Log::info('微信日志-hearder参数returnErrType:',[$returnErrType]);
    if(!is_numeric($returnErrType)) $returnErrType = 0;
    if ( (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") || $returnErrType == 1 ) {
        return true;
    } else {
        return false;
    }
}

/**
 *
 * @brief API接口，获得POST过来的数据
 * @details
 * @return null
 *
 */
if ( ! function_exists('apiGetPost'))
{
    function apiGetPost(){
        $post_data = file_get_contents("php://input");
        if(empty($post_data)){
            $post_data = $GLOBALS['HTTP_RAW_POST_DATA'] ?? '';
        }
        return $post_data;
    }
}


// 表单字段初始化
function initEmptyFields(array &$params, array $fields)
{
    foreach ($fields as $field) {
        if (! isset($params[$field])) {
            $params[$field] = '';
        }
    }
}

// 必填字段检测
function ensureFieldsNotEmpty(array &$params, array $fields, bool $strict = false)
{
    initEmptyFields($params, $fields);

    // 必填字段检测
    foreach ($fields as $field) {
        if ($strict) {
            if ($params[$field] === '') {
                throws('必填参数不能为空：' . $field);
            }
        }
        else {
            if (! $params[$field]) {
                throws('必填参数不能为空：' . $field);
            }
        }
    }
}

// 将数组中空值转换为null
function purifyArray(array $array)
{
    if (! $array) {
        return null;
    }

    foreach ($array as $key => &$value) {
        if ($value instanceof \yii\base\Model) {
            $value = $value->toArray();
        }
        if (is_array($value)) {
            if (! $value) {
                $value = null;
            }
            else {
                $func = __FUNCTION__;
                $value = $func($value);
            }
        }
    }

    return $array;
}

// 遍历数据，将数据中的null，改为空
function nullToEmpty($params){
    if(is_array($params)){
        foreach($params as $k =>$v){
            if(is_array($v)){
                $func = __FUNCTION__;
                $params[$k] = $func($v);
            }else{
                if(is_null($v)){
                    $params[$k] = '';
                }
            }
        }
        return $params;
    }else{
        if(is_null($params)){
            return '';
        }else{
            return $params;
        }
    }

}

function getErrArr($msg = '', $code = null, $data = null){
    $returnDataArr = [
        'code' => ($code === null) ? (config('public.apiErrorCode')) : $code,
        'msg'  => $msg,
        'data' => $data,
    ];
    return jsonAPIFormatData($returnDataArr);// 接口json返回数据
}

function errorArray($msg = '', $code = null, $data = null)
{
    return getErrArr($msg, $code, $data);
}

function errorJson($msg = '', $code = null, $data = null)
{
    $resp = getErrArr($msg, $code, $data);
    if(!is_array($resp)) return $resp;
    return toJSON($resp);
}


function getOkArr($data = null, $code = null){
    $returnDataArr = [
        'code' => ($code === null) ? (config('public.apiSuccesCode')) : $code,
        'msg'  => 'success',
        'data' => $data,
    ];
    return jsonAPIFormatData($returnDataArr);// 接口json返回数据
}

function okJson($data = null, $code = null)
{
    // 为保持对客户端输出空 hashMap/list 的结构一致
    // 这里统一把所有空数组转换为 null
//        if (is_array($data)) {
//            $data = purifyArray($data);
//        }

    $resp = getOkArr($data, $code);
    return toJSON($resp);
}


function okArray($data = null, $code = null)
{
    // 为保持对客户端输出空 hashMap/list 的结构一致
    // 这里统一把所有空数组转换为 null
//        if (is_array($data)) {
//            $data = purifyArray($data);
//        }
    return getOkArr($data, $code);
}

function toJson($source)
{
    // return json_encode($source);
    return json_encode($source, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function outputJson($resp)
{
    header('Content-type: text/json');
    header('Content-type: application/json; charset=UTF-8');

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 864000');

    // 允许所有自定义请求头
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
    }

    exit(toJson($resp));
}

// ajax返回正确数组
function ajaxDataArr($status = 0, $data = [], $errorMsg = '')// , $status_code = 200
{
    $returnDataArr = ['apistatus' => $status, 'result' => $data, 'errorMsg' => $errorMsg];
//    $returnDataArr = array_merge($returnDataArr, [
//        'code' => $status,// '新版的错误编号'
//        'message' => $errorMsg,
//        'status_code' => $status_code,// '200'
//    ]);
    return jsonAPIFormatData($returnDataArr);// 接口json返回数据
}

// 接口json返回数据
function jsonAPIFormatData($returnDataArr)
{
    /**
     *
     *  JSON响应
     *  json 方法会自动将 Content-Type 头设置为 application/json，并使用 PHP 函数 json_encode 方法将给定数组转化为 JSON 格式数据：
     *
     *  return response()->json([
     *  'name' => 'Abigail',
     *  'state' => 'CA'
     *  ]);
     *
     *
     *  如果你想要创建一个 JSONP 响应，可以在 json 方法之后调用 withCallback 方法：
     *
     *  return response()
     *  ->json(['name' => 'Abigail', 'state' => 'CA'])
     *  ->withCallback($request->input('callback'));
     *  或者直接使用 jsonp 方法：
     *
     *  return response()
     *  ->jsonp($request->input('callback'), ['name' => 'Abigail', 'state' => 'CA']);
     *
     */
    $callback = request('callback', '');// 是否jsonp的方式

    // 是jsonp的方式
    if(is_string($callback) && strlen($callback) > 0){
//        exit($callback . '(' . json_encode($returnDataArr) . ')'); // -- 原生php方式
//        return $callback . '(' . json_encode($returnDataArr) . ')'; // -- 原生php方式
        return response()->jsonp($callback, $returnDataArr);
    }
    // 正常方式
    return $returnDataArr;
}

function setSessionByTag($key, $field, $value)
{
    $session = \Yii::$app->session;

    $result = $session->get($key) ?? [];
    $result[$field] = $value;

    $session->set($key, $result);
}

function setCacheByTag($key, $field, $value)
{
    $cache = \Yii::$app->cache;

    $result = $cache->get($key) ?? [];
    $result[$field] = $value;

    $cache->set($key, $result);
}

function setMergeCacheByTag($key, $field, array $value)
{
    $cache = \Yii::$app->cache;

    $result = $cache->get($key) ?? [];
    $result[$field] = array_merge($result[$field] ?? [], $value);

    $cache->set($key, $result);
}

// 只记录1次
function logOnce($message, $category = 'application', $level = 'info', $uniqid = null)
{
    if (\Yii::$app->cache->add($uniqid ?? $message, time())) {
        \Yii::$level($message, $category);
    }
}


/**
 * 分页函数
 * author baihaijiang
 * date 2015-06-29
 * @param $totalpg 总页数
 * @param $pg 当前页数
 * @param $record 总数量
 * @param $showpage 显示页码数量
 * @param $show_num 显示其它功能[与操作]
 * 1输入页码跳转[按钮]通过 calss page_go 实现翻页
 * 2输入页码跳转[按钮]通过btn_go()方法实现翻页
 */
function showPage($totalpg, $pg = 1, $record = 0,$showpage = 9,$show_num = 0)
{

    $pre = $pg > 1 ? $pg - 1 : $pg;

    $nex = $pg < $totalpg ? $pg + 1 : $totalpg;

    //$differ = 4;         //差页码值
    //$showpage = 8;       //显示页码数

    $page = '';

    if($totalpg <= 1){
        return $page;
    }

    $page .= "<li><a href='javascript:;' id='totalpage' totalpage='" . $totalpg . "' >总数:" . $record . "个 / " . $totalpg . "页</a></li>";
    //第一页
    $first_page_class = "";
    if ($pg == 1) {
        $first_page_class = ' class="disabled"';
    }
    $page .= '<li '. $first_page_class.'>';
    $page .= '<a href="javascript:void(0)" pg="1" aria-label="首页">';
    $page .= '<span aria-hidden="true">首页</span>';
    $page .= '</a></li>';

    $page .= '<li '. $first_page_class .'>';
    $page .= '<a href="javascript:void(0)" pg="' . $pre . '" aria-label="前页">';
    $page .= '<span aria-hidden="true">前页</span>';
    $page .= '</a></li>';

    //if ($totalpg < ($showpage + $differ)) {
    if ($totalpg <= $showpage) {//总页数小于要显示的8页数,显示所有页码[总页数小于/等于要显示的页数，从头开始]
        $s = 1;
        $e = $totalpg;
    } else {//[总页数大于要显示的页数，从尾开始]
        $pg_back = ceil(($showpage-1)/2);//后面显示页数
        if($pg+$pg_back > $totalpg){
            $pg_back = $totalpg - $pg;//后面显示页数
        }
        $pg_pre = $showpage - $pg_back -1;//前面显示页数

        $s = $pg - $pg_pre;
        $e = $pg + $pg_back;
        if($s <=0){//纠正
            $s = 1;
            $e = $showpage;
        }
        /*
        if ($pg >= $showpage) {//当前页数,大于显示8页
            //if (($pg + 4) < $totalpg) {//当前页数+4页,小于总页数 if (($pg + 4) < $totalpg) {
            //    $s = ($pg - 2);
            //    $e = ($pg + 2);
            //} else {//当前页数+4页,大于/等于总页数
            //    $s = ($pg - $showpage) + 2;
            //    $e = ($pg + $differ) < $totalpg ? ($pg + $differ) : $totalpg;
            //}
        } else {//当前页数,小于显示8页
            $s = 1;
            $e = $showpage;
        }
         *
         */
    }

    for ($i = $s; $i <= $e; $i++) {
        if ($pg == $i) {
            $page .= '<li class="active"><a herf="javascript:void(0)" pg="' . $i . '">' . $i . '</a></li>';
        } else {
            $page .= '<li><a href="javascript:void(0)" pg="' . $i . '">' . $i . '</a></li>';
        }
    }
    //后一页
    $last_page_class = "";
    if ($pg == $totalpg) {
        $last_page_class = ' class="disabled"';
    }

    $page .= '<li ' . $last_page_class .'>';
    $page .= '<a aria-label="后页" href="javascript:void(0)" pg="' . $nex . '">';
    $page .= '<span aria-hidden="true">后页</span>';
    $page .= '</a></li>';

    $page .= '<li ' . $last_page_class .'>';
    $page .= '<a aria-label="末页" href="javascript:void(0)" pg="' . $totalpg . '">';
    $page .= '<span aria-hidden="true">末页</span>';
    $page .= '</a></li>';

    if ($totalpg >= 1 && ( ($show_num & 1) ==1 ) ) {//1输入页码跳转[按钮]通过 calss page_go 实现翻页
        $page .= '&nbsp;&nbsp;<span class="pagespan2" ><input class="form-control pagenum" id="page_num" name="page_num" type="text" value="" onkeyup="this.value=this.value.replace(/[^0-9]/g, \'\');" style="width:50px;">';
        $page .= '&nbsp;&nbsp;<button class="btn btn-primary btn-page btn-xs page_go" type="button"  totalpage="' . $totalpg . '" > 跳转 </button></span>';
    }
    if ($totalpg >= 1 && ( ($show_num & 2) ==2 ) ) {//2输入页码跳转[按钮]通过btn_go()方法实现翻页；第一个参数：当前对象；第二个参数数字:代表类型[可能会用其它同类方法]
        $page .= '&nbsp;&nbsp;<span class="pagespan2"><input class="form-control pagenum" id="page_num" name="page_num" type="text" value=""  onkeyup="this.value=this.value.replace(/[^0-9]/g, \'\');" style="width:50px;">';
        $page .= '&nbsp;&nbsp;<button class="btn btn-primary btn-page btn-xs" type="button" onclick="btn_go(this, 1)"  totalpage="' . $totalpg . '" > 跳转 </button></span>';
    }
    return $totalpg >= 1 ? $page : '';
}


/**
 * 分页函数--直接链接地址--主要给前端页面用seo
 * author baihaijiang
 * date 2015-06-29
 * @param string $url_model 链接地址模板 http://www.***.com/list/{page} 主要是这个page 替换为具体的页数
 * @param string $page_tag  链接地址模板 $url_model 中的页数标签 默认 {page}
 * @param int $totalpg 总页数
 * @param $pg 当前页数
 * @param $record 总数量
 * @param $showpage 显示页码数量
 * @param $show_num 显示其它功能[与操作]
 * 1输入页码跳转[按钮]通过 calss page_go 实现翻页
 * 2输入页码跳转[按钮]通过btn_go()方法实现翻页
 */
function showPageLink($url_model, $page_tag = '{page}', $totalpg = 0, $pg = 1, $record = 0,$showpage = 9,$show_num = 0)
{

    $pre = $pg > 1 ? $pg - 1 : $pg;

    $nex = $pg < $totalpg ? $pg + 1 : $totalpg;

    //$differ = 4;         //差页码值
    //$showpage = 8;       //显示页码数


    $page = '';

    if($totalpg <= 1){
        return $page;
    }
    $page .= "<input type='hidden' name='url_model' value='" . $url_model . "' />";
    $page .= "<input type='hidden' name='page_tag' value='" . $page_tag . "' />";
    $page .= "<li><a href='javascript:;' id='totalpage' totalpage='" . $totalpg . "' >总数:" . $record . "个 / " . $totalpg . "页</a></li>";
    //第一页
    $first_page_class = "";
    if ($pg == 1) {
        $first_page_class = ' class="disabled"';
    }
    $page .= '<li '. $first_page_class.'>';
    $page .= '<a href="' . str_replace($page_tag, 1, $url_model) . '" pg="1" aria-label="首页">';
    $page .= '<span aria-hidden="true">首页</span>';
    $page .= '</a></li>';

    $page .= '<li '. $first_page_class .'>';
    $page .= '<a href="' . str_replace($page_tag, $pre, $url_model) . '" pg="' . $pre . '" aria-label="前页">';
    $page .= '<span aria-hidden="true">前页</span>';
    $page .= '</a></li>';

    //if ($totalpg < ($showpage + $differ)) {
    if ($totalpg <= $showpage) {//总页数小于要显示的8页数,显示所有页码[总页数小于/等于要显示的页数，从头开始]
        $s = 1;
        $e = $totalpg;
    } else {//[总页数大于要显示的页数，从尾开始]
        $pg_back = ceil(($showpage-1)/2);//后面显示页数
        if($pg+$pg_back > $totalpg){
            $pg_back = $totalpg - $pg;//后面显示页数
        }
        $pg_pre = $showpage - $pg_back -1;//前面显示页数

        $s = $pg - $pg_pre;
        $e = $pg + $pg_back;
        if($s <=0){//纠正
            $s = 1;
            $e = $showpage;
        }
        /*
        if ($pg >= $showpage) {//当前页数,大于显示8页
            //if (($pg + 4) < $totalpg) {//当前页数+4页,小于总页数 if (($pg + 4) < $totalpg) {
            //    $s = ($pg - 2);
            //    $e = ($pg + 2);
            //} else {//当前页数+4页,大于/等于总页数
            //    $s = ($pg - $showpage) + 2;
            //    $e = ($pg + $differ) < $totalpg ? ($pg + $differ) : $totalpg;
            //}
        } else {//当前页数,小于显示8页
            $s = 1;
            $e = $showpage;
        }
         *
         */
    }

    for ($i = $s; $i <= $e; $i++) {
        if ($pg == $i) {
            $page .= '<li class="active"><a herf="' . str_replace($page_tag, $i, $url_model) . '" pg="' . $i . '">' . $i . '</a></li>';
        } else {
            $page .= '<li><a href="' . str_replace($page_tag, $i, $url_model) . '" pg="' . $i . '">' . $i . '</a></li>';
        }
    }
    //后一页
    $last_page_class = "";
    if ($pg == $totalpg) {
        $last_page_class = ' class="disabled"';
    }

    $page .= '<li ' . $last_page_class .'>';
    $page .= '<a aria-label="后页" href="' . str_replace($page_tag, $nex, $url_model) . '" pg="' . $nex . '">';
    $page .= '<span aria-hidden="true">后页</span>';
    $page .= '</a></li>';

    $page .= '<li ' . $last_page_class .'>';
    $page .= '<a aria-label="末页" href="' . str_replace($page_tag, $totalpg, $url_model) . '" pg="' . $totalpg . '">';
    $page .= '<span aria-hidden="true">末页</span>';
    $page .= '</a></li>';

    if ($totalpg >= 1 && ( ($show_num & 1) ==1 ) ) {//1输入页码跳转[按钮]通过 calss page_go 实现翻页
        $page .= '&nbsp;&nbsp;<span class="pagespan2" ><input class="form-control pagenum" id="page_num" name="page_num" type="text" value="" onkeyup="this.value=this.value.replace(/[^0-9]/g, \'\');" style="width:50px;">';
        $page .= '&nbsp;&nbsp;<button class="btn btn-primary btn-page btn-xs page_go" type="button"  totalpage="' . $totalpg . '" > 跳转 </button></span>';
    }
    if ($totalpg >= 1 && ( ($show_num & 2) ==2 ) ) {//2输入页码跳转[按钮]通过btn_go()方法实现翻页；第一个参数：当前对象；第二个参数数字:代表类型[可能会用其它同类方法]
        $page .= '&nbsp;&nbsp;<span class="pagespan2"><input class="form-control pagenum" id="page_num" name="page_num" type="text" value=""  onkeyup="this.value=this.value.replace(/[^0-9]/g, \'\');" style="width:50px;">';
        $page .= '&nbsp;&nbsp;<button class="btn btn-primary btn-page btn-xs" type="button" onclick="btn_go(this, 2)"  totalpage="' . $totalpg . '" > 跳转 </button></span>';
    }
    return $totalpg >= 1 ? $page : '';
}

/**
 * 修改文件权限 ---用这个
 *
 * @param string $full_names 要修改文件权限的文件 全路径  如 /data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
 * @param  $mode 权限 需要注意一点，权限值最好使用八进制表示，即“0”开头，而且一定不要加引号。
 * @return boolean
 */
function chmodFile($full_names, $mode = 0777) {
    // 修改文件权限
    @chmod($full_names, $mode);
}

/**
 * 循环创建目录 ---用这个
 *
 * @param string $dir 待创建的目录
 * @param  $mode 权限 需要注意一点，权限值最好使用八进制表示，即“0”开头，而且一定不要加引号。
 * @return boolean
 */
function makeDir($dir, $mode = 0777) {
    // mkdir ($filePath,0777,true);
    if (is_dir($dir) || (@mkdir($dir, $mode) && @chmod($dir, $mode) ))
        return true;
    if (!makeDir(dirname($dir), $mode))
        return false;
    return (@mkdir($dir, $mode) && @chmod($dir, $mode) );
}

/**
 *
 * 创建目录，支持递归创建目录
 * @param String $dirName 要创建的目录
 * @param int $mode 目录权限
 */
function smkdir($dirName , $mode = 0777) {
    $dirs = explode('/' , str_replace('\\' , '/' , $dirName));
    $dir = '';
    foreach ($dirs as $part) {
        $dir .= $part . '/';
        if ( ! is_dir($dir) && strlen($dir) > 0) {
            if ( ! mkdir($dir , $mode)) {
                return false;
            }
            if ( ! chmod($dir , $mode)) {
                return false;
            }
        }
    }
    return true;
}

/**
 * 转换特殊字符[<]
 *
 * @param string $string 要转换的字符串
 * @param int $replace_type 转换类型 1<转换为&lt;;2&lt;转换为<
 * @return string 字符串类型的返回结果
 */
if ( ! function_exists('replace_special_char'))
{
    function replace_special_char($string,$replace_type = 1){
//        $string = htmlspecialchars($string);
        $old_replace_arr = array("&", '"', "<", ">","'");
        $new_replace_arr =array("&amp;", "&quot;", "&lt;", "&gt;", "&apos;");
        if($replace_type == 1){
            $string = str_replace($old_replace_arr,$new_replace_arr,$string);
        }else{
            $string = str_replace($new_replace_arr,$old_replace_arr,$string);
        }
        return $string;
    }
}

/**
 * 转换特殊字符[回车换行制表符]
 *
 * @param string $string 要转换的字符串
 * @param int $replace_type 转换类型 1回车换行\r\n转换为<br/>;2<br/>转换为回车换行\r\n
 * @return string 字符串类型的返回结果
 */
if ( ! function_exists('replace_enter_char'))
{
    function replace_enter_char($string,$replace_type = 1){
        $old_replace_arr = array("\r\n","\n", PHP_EOL,"\t",PHP_EOL);// array("\r\n","\t","\n");
        $new_replace_arr =array("<br/>","<br/>","<br/>","    ","<br/>");//array("<br />","    ","<br/>");
        if($replace_type == 1){
            $string = str_replace($old_replace_arr,$new_replace_arr,$string);
        }else{
            $string = str_replace($new_replace_arr,$old_replace_arr,$string);
            // $string = str_replace(["\r\n\r\n"],["\r\n"],$string);// 这里是对历史数据的纠正。2个换行为一个换行--万一我真的是2个换行呢。所以去掉
        }
        return $string;
    }
}

/**
 * json字符转让为数组
 *
 * @param string $jsonStr 当前json字符串[处理]/或数组[不处理]
 * @param int $errType 转换有错的处理为型 1 throws错误，2返回false
 * @return object 数据对象:  false：失败或 throws
 * @author zouyan(305463219@qq.com)
 */
function jsonStrToArr(&$jsonStr , $errType, $throwErrStr){
    if ( is_string($jsonStr) ) {
        if (isNotJson($jsonStr)) {
            if($errType == 1){
                throws($throwErrStr);
            }
            return false;
        } else {
            $jsonStr = json_decode($jsonStr , true);
        }
    }else if(!is_array($jsonStr)){
        if($errType == 1){
            throws($throwErrStr);
        }
        return false;
    }
    // null 值转为 ''
    $jsonStr = nullToEmpty($jsonStr);
    return $jsonStr;
}

//判断日期格式
//$dateTime 日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59 ，也可以是UNIX时间戳[转换为时间格式化用]
//$format 格式化 "Y-m-d H:i:s","Y-m-d","H:i:s"
//return 默认返回 false 不是有效日期 $format有值，则返回格式化后的日期，否则还回UNIX时间戳
//1.首先使用正则验证是否为“2011-11-07 12:30:55”这种格式。
//就可以了
//2.然后使用strtotime()函数判断验证,传入日期字符串即可。
//strtotime()函数默认返回指定日期时间字符串对应的UNIX时间戳。
//strtotime()函数有个特点，就是如果传入日期字符串格式错误的话会返回false，而且支持各种的日期格式，非常方便。
if ( ! function_exists('judgeDate'))
{
    function judgeDate($dateTime='',$format=false){
        if(empty($dateTime)){
            return false;
        }
        //匹配时间格式为2012-02-16或2012-02-16 23:59:59前面为0的时候可以不写
        $patten = '/^\\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])(\\s+(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/';
        if(preg_match($patten,$dateTime)) {
            $unixTime = strtotime($dateTime);
            if($unixTime==false){
                return false;
            }
            if($format!==false && (!empty($format))){
                return date($format,$unixTime);
            }
            return $unixTime;
        }else{
            if(is_numeric($dateTime)){//是时间戳
                if($format!==false && (!empty($format))){
                    return date($format,$dateTime);
                }
                return $dateTime;
            }
            return false;
        }
    }
}

//判断时间格式 返回值 true:是正确的时间;false：时间格式不正确
//$time 时间格式 23:59:59
if ( ! function_exists('judgeTime'))
{
    function judgeTime($time = ''){
        if(empty($time)) return false;
        //匹配时间格式为2012-02-16或2012-02-16 23:59:59前面为0的时候可以不写
        $patten = '/^(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9])$/';
        if(preg_match($patten,$time))  return true;
        return false;
    }
}

//时间转换为当天的秒数 返回值 >=0：秒值 正确-通过; 字符 :失败-有误;
// $time 时间格式 23:59:59
// $timeName 时间名称 如 开始时间
// $errDo 错误处理方式 1 throws 2直接返回错误
if ( ! function_exists('timeToDaySecond'))
{
    function timeToDaySecond($time = '', $timeName = "时间", $errDo = 1){
        $intDaySecnd = -1;
        if(judgeTime($time)){
            $timeArr = explode(":", $time);
            if(count($timeArr) == 3){
                $intDaySecnd = intval($timeArr[0]) * 3600 + intval($timeArr[1]) * 60 + intval($timeArr[2]);
            }else{
                $errMsg = $timeName . "格式错误";
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
        }else{
            $errMsg = $timeName . "格式错误";
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        return $intDaySecnd;
    }
}

//比较两个时间,返回  end_time 结束时间 - begin_time 开始时间 返回值：字符：错误信息;数字：时间差
// begin_time 开始时间
// end_time 结束时间
// begin_time_name 时间名称 如 开始时间
// end_time_name 时间名称 如 结束时间
// $errDo 错误处理方式 1 throws 2直接返回错误
if ( ! function_exists('compare_time'))
{
    function compare_time($begin_time, $end_time, $begin_time_name, $end_time_name, $errDo = 1){
        $beginDaySecond = timeToDaySecond($begin_time, $begin_time_name, $errDo);
        if(is_string($beginDaySecond) ){// 有错
            return $beginDaySecond;
        }
        if($beginDaySecond < 0){
            return $begin_time_name . '有误';
        }

        $endDaySecond = timeToDaySecond($end_time, $end_time_name, $errDo);
        if(is_string($endDaySecond) ){// 有错
            return $endDaySecond;
        }
        if($endDaySecond < 0){
            return $endDaySecond . '有误';
        }
        return $endDaySecond - $beginDaySecond;
    }
}


// 获得请求的地址及参数。方便测试接口
function splicQuestAPI($url , $params = []){
    if(!is_array($params)){
        $params = [];
    }
    foreach($params as $k=>$v){
        if(is_array($v)){
            $params[$k] = json_encode($v);
        }

    }
    return $url . '?' . http_build_query($params);
}

//判断日期格式
//$dateTime 日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59 ，也可以是UNIX时间戳[转换为时间格式化用]
//$format 格式化 "Y-m-d H:i:s","Y-m-d","H:i:s"
//return 默认返回 false 不是有效日期 $format有值，则返回格式化后的日期，否则还回UNIX时间戳
//1.首先使用正则验证是否为“2011-11-07 12:30:55”这种格式。
//就可以了
//2.然后使用strtotime()函数判断验证,传入日期字符串即可。
//strtotime()函数默认返回指定日期时间字符串对应的UNIX时间戳。
//strtotime()函数有个特点，就是如果传入日期字符串格式错误的话会返回false，而且支持各种的日期格式，非常方便。
if ( ! function_exists('judge_date'))
{
    function judge_date($dateTime='',$format=false){
        if(empty($dateTime)){
            return false;
        }
        //匹配时间格式为2012-02-16或2012-02-16 23:59:59前面为0的时候可以不写
        $patten = '/^\\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])(\\s+(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/';
        if(preg_match($patten,$dateTime)) {
            $unixTime = strtotime($dateTime);
            if($unixTime==false){
                return false;
            }
            if($format!==false && (!empty($format))){
                return date($format,$unixTime);
            }
            return $unixTime;
        }else{
            if(is_numeric($dateTime)){//是时间戳
                if($format!==false && (!empty($format))){
                    return date($format,$dateTime);
                }
                return $dateTime;
            }
            return false;
        }
    }
}

//
//$unix_time 当前的unix时间
//$day 天数 0:当天;1:前一天开始/后一天结束.....
/**
 *
 * 按日格式化时间
 * @details
 * @param int $re_type 1:[当天的开始]前*天开始[>=];2:后[后推一天的开始]*天结束[<]
 * @param mix $unix_time 为"",是当前时间，日期格式 2012-02-16或2012-02-16 23:59:59 2012-2-8或2012-02-16 23:59:59 ，也可以是UNIX时间戳[转换为时间格式化用]
 * @param int $day 天数 0:当天;1:前一天开始/后一天结束.....
 * @return array  转换好的一维数组
 *
 */
if ( ! function_exists('day_format_time'))
{
    function day_format_time($re_type=1,$unix_time="",$day=0){
        $unix_time = judge_date($unix_time);
        if( $unix_time == false){
            $unix_time = time();
        }
        if($re_type == 1){
            return strtotime(date('Y-m-d',$unix_time)) - 60*60*24*(0+$day);//>=;当天开始
        }else{
            return strtotime(date('Y-m-d',$unix_time)) + 60*60*24*(1+$day);//<;当天结束
        }
    }
}

/**
 *
 * PHP 的笛卡尔积算法实现 https://www.ruoxiaozh.com/blog/article/55
 * @details概念
 * 在数学中，两个集合X和Y的笛卡儿积（Cartesian product），又称直积，表示为 X × Y。设A、B是任意两个集合，
 * 在集合A中任意取一个元素x，在集合B中任意取一个元素y，组成一个有序对（x，y），把这样的有序对作为新的元素，
 * 他们的全体组成的集合称为集合A和集合B的直积，记为A×B，即 A×B={（x，y）|x∈A且y∈B}。
 * 假设集合 A={a, b}，集合 B={0, 1, 2}，则两个集合的笛卡尔积为 {(a, 0), (a, 1), (a, 2), (b, 0), (b, 1), (b, 2)}。
 * 举例
 * 给出三个域：
 * D1 = { 张清玫，刘逸 }
 * D2 = {计算机专业，信息专业}
 * D3 = {李勇，刘晨，王敏}
 * 则 D1，D2，D3 的笛卡尔积 D = D1×D2×D3，等于：
 * {
 *    (张清玫, 计算机专业, 李勇),
 *    (张清玫, 计算机专业, 刘晨),
 *    (张清玫, 计算机专业, 王敏),
 *    (张清玫, 信息专业, 李勇),
 *    (张清玫, 信息专业, 刘晨),
 *    (张清玫, 信息专业, 王敏),
 *    (刘逸, 计算机专业, 李勇),
 *   (刘逸, 计算机专业, 刘晨),
 *    (刘逸, 计算机专业, 王敏),
 *   (刘逸, 信息专业, 李勇),
 *   (刘逸, 信息专业, 刘晨),
 *   (刘逸, 信息专业, 王敏)
 * }
 * 这样就把D1、D2、D3这三个集合中的每个元素加以对应组合，形成庞大的集合群。
 * 本个例子中的D中就会有 2X2X3=12 个元素，如果一个集合有1000个元素，有这样3个集合，他们的笛卡尔积所组成的新集合会达到十亿个元素。
 * 假若某个集合是无限集，那么新的集合就将是有无限个元素。
 * @return array
 * 使用：
 * $arr = [
 *   [
 *      '张清玫',
 *      '刘逸'
 *  ],
 *   [
 *       '计算机专业',
 *       '信息管理与信息系统专业',
 *       '电子商务专业'
 *  ],
 *   [
 *       '2018级',
 *       '2017级'
 *   ]
 * ];
 * $r = Descartes($arr);
 * 效果：
 *   array(12) {
 *      [0]=>
 *      array(3) {
 *              [0]=>
 *              string(9) "张清玫"
 *              [1]=>
 *              string(15) "计算机专业"
 *              [2]=>
 *              string(7) "2018级"
 *      }
 *      [1]=>
 *      array(3) {
 *          [0]=>
 *              string(9) "张清玫"
 *              [1]=>
 *              string(15) "计算机专业"
 *              [2]=>
 *              string(7) "2017级"
 *      },
 *      .....
 *    }
 *
 *
 */
if ( ! function_exists('Descartes')) {
    function Descartes()
    {
        $t = func_get_args();                                    // 获取传入的参数
        if (func_num_args() == 1) {                               // 判断参数个数是否为1
            return call_user_func_array(__FUNCTION__, $t[0]);  // 回调当前函数，并把第一个数组作为参数传入
        }

        $a = array_shift($t);        // 将 $t 中的第一个元素移动到 $a 中，$t 中索引值重新排序
        if (!is_array($a)) {
            $a = [$a];
        }

        $a = array_chunk($a, 1);     // 分割数组 $a ，为每个单元1个元素的新数组
        do {
            $r = [];
            $b = array_shift($t);
            if (!is_array($b)) {
                $b = [$b];
            }
            foreach ($a as $p) {
                foreach (array_chunk($b, 1) as $q) {
                    $r[] = array_merge($p, $q);
                }
            }
            $a = $r;
        } while ($t);

        return $r;
    }
}
