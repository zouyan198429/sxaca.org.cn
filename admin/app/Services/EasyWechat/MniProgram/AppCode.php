<?php
namespace App\Services\EasyWechat\MniProgram;

use App\Services\Upload\UploadFile;
use EasyWeChat\Kernel\Http\StreamResponse;
use Illuminate\Support\Facades\Log;
class AppCode
{

    /**
     * 获取小程序码 接口A: 适用于需要的码数量较少的业务场景
     * 获取小程序码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制
     * @param object $app   当前对象
     * @param  string  $savePath 保存路径 --linux系统全路径 '/path/to/directory'
     * @param  string  $fileName 保存文件名  'appcode.png'
     * @param string $path  小程序路径
     *        扫码进入的小程序页面路径，最大长度 128 字节，不能为空；
     *        对于小游戏，可以只传入 query 部分，来实现传参效果，
     *        如：传入 "?foo=bar"，即可在 wx.getLaunchOptionsSync 接口中的 query 参数获取到 {foo:"bar"}。
     * @param  array  $optional  可选参数
     *   $optional = [
     *       'width' => '', // number  二维码的宽度，单位 px。最小 280px，最大 1280px
     *      'auto_color' => '', // boolean  false自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
     *       'line_color' => '', // object  / array [ 'r' => 105, 'g' => 166, 'b' => 134,] {"r":0,"g":0,"b":0}	否	auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
     *       'is_hyaline' => '', // boolean	false	否	是否需要透明底色，为 true 时，生成透明底色的小程序码
     *   ];
     * @return  string
     * @author zouyan(305463219@qq.com)
     */
    public static function getwxacode(&$app, $savePath, $fileName = '', $path = '', $optional = []){
        $filename = '';
        $response = $app->app_code->get($path, $optional);

        // 保存小程序码到文件
        // if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
        if ($response instanceof StreamResponse) {
            if(empty($fileName)) {
//                $filename = $response->save('/path/to/directory');
                $filename = $response->save($savePath);
                // 或
            }else{
//                $filename = $response->saveAs('/path/to/directory', 'appcode.png');
                $filename = $response->saveAs($savePath, $fileName);
            }
        }
        return $filename;
    }

    /**
     * 获取小程序码 接口A: 适用于需要的码数量较少的业务场景
     * 获取小程序码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制
     * @param object $app   当前对象
     * @param  string  $savePath 保存路径 --linux系统全路径  '/path/to/directory'
     * @param  string  $fileName 保存文件名  'appcode.png'
     * @param string $scene  最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，
     *                       其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * @param  array  $optional  可选参数
     *   $optional = [
     *       'page' => '',// string	主页	否	必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index,
     *                           根路径前不要填加 /,不能携带参数（参数请放在scene字段里），
     *                           如果不填写这个字段，默认跳主页面
     *       'width' => '', // number  二维码的宽度，单位 px。最小 280px，最大 1280px
     *      'auto_color' => '', // boolean  false自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
     *       'line_color' => '', // object  / array [ 'r' => 105, 'g' => 166, 'b' => 134,] {"r":0,"g":0,"b":0}	否	auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
     *       'is_hyaline' => '', // boolean	false	否	是否需要透明底色，为 true 时，生成透明底色的小程序码
     *   ];
     * @return  string
     * @author zouyan(305463219@qq.com)
     */
    public static function getwxacodeunlimit(&$app, $savePath, $fileName = '', $scene = '', $optional = []){
        $filename = '';
        $response = $app->app_code->getUnlimit($scene, $optional);

        // 保存小程序码到文件
//        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
        if ($response instanceof StreamResponse) {
            if(empty($fileName)) {
//                $filename = $response->save('/path/to/directory');
                $filename = $response->save($savePath);
                // 或
            }else{
//                $filename = $response->saveAs('/path/to/directory', 'appcode.png');
                $filename = $response->saveAs($savePath, $fileName);
            }
        }
        return $filename;
    }

    /**
     * 获取小程序二维码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制
     * @param object $app   当前对象
     * @param  string  $savePath 保存路径 --linux系统全路径 '/path/to/directory'
     * @param  string  $fileName 保存文件名  'appcode.png'
     * @param string $path  string		是	扫码进入的小程序页面路径，最大长度 128 字节，不能为空；
     *               对于小游戏，可以只传入 query 部分，来实现传参效果，
     *              如：传入 "?foo=bar"，即可在 wx.getLaunchOptionsSync 接口中的 query 参数获取到 {foo:"bar"}。
     * @param  int  $width  可选参数  number	430	否	二维码的宽度，单位 px。最小 280px，最大 1280px
     * @return  string
     * @author zouyan(305463219@qq.com)
     */
    public static function createwxaqrcode(&$app, $savePath, $fileName = '', $path = '', $width = null){
        $filename = '';
        $response = $app->app_code->getQrCode($path, $width);

        // 保存小程序码到文件
//        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
        if ($response instanceof StreamResponse) {
            if(empty($fileName)) {
//                $filename = $response->save('/path/to/directory');
                $filename = $response->save($savePath);
                // 或
            }else{
//                $filename = $response->saveAs('/path/to/directory', 'appcode.png');
                $filename = $response->saveAs($savePath, $fileName);
            }
        }
        return $filename;
    }


}