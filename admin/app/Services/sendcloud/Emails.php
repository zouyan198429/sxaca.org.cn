<?php
namespace App\Services\sendcloud;

use App\User;
use Naux\Mail\SendCloudTemplate;
use Mail;

class Emails
{

    /**
     * 是否开启发邮件功能
     *
     * @param object $user 用户对象
     * @return  boolean TRUE:开启邮件；FALSE:关闭邮件
     * @author zouyan(305463219@qq.com)
     */
    public static function isMailOpen()
    {
        return config('public.mail_open', false);// env('MAIL_OPEN', false);
    }

    /**
     * 是否开启发短信功能
     *
     * @param object $user 用户对象
     * @return  boolean TRUE:开启短信；FALSE:关闭短信
     * @author zouyan(305463219@qq.com)
     */
    public static function isMSGOpen()
    {
        return config('public.mail_msg_open', false);// env('MAIL_MSG_OPEN', false);
    }

    /**
     * 激活您的邮箱
     *
     * @param object $user 用户对象
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function getWebInfo()
    {
        return [
            'webName' => config('public.webName'),// 系统名称
            'webEmail' => config('public.webEmail'),// 系统名称
        ];
    }

    // 激活您的邮箱
// 使用sendcloud发送邮件
// https://blog.csdn.net/shangyanaf/article/details/79293253
// https://blog.csdn.net/gh254172840/article/details/80418302
// 注意点
// 一定要在文件中添加
//    use Naux\Mail\SendCloudTemplate;
//    use Mail;
    /**
     * 激活您的邮箱
     *
     * @param object $user 用户对象
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function sendVerifyEmailTo(User $user)
    {
        if(!static::isMailOpen()) return;

        if($user->is_active != 0) throws('您的邮箱已激活！不可重复激活！');
        list($webName, $webEmail) = array_values(static::getWebInfo());

        $data = [
            'url' => route('email.verify', ['token' => $user->confirmation_token]),// '你的网址'. $user->confirmation_token,
            'name' => $user->name,
        ];
        $template = new SendCloudTemplate('test_template_active', $data);

        Mail::raw($template, function ($message) use ($user, $webName, $webEmail) {
            // $message->from('example@example.com', 'example');
            $message->from($webEmail, $webName);
            $message->to($user->email);
        });
    }
}