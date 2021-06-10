<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // laravel dingo api接管异常处理  把异常处理再接回到laravel的异常处理机制中
        // 将所有的 Exception 全部交给 App\Exceptions\Handler 来处理 https://blog.csdn.net/qq_29099209/article/details/88794573
        app('api.exception')->register(function (\Exception $exception) {
            $request = \Illuminate\Http\Request::capture();
            return app('App\Exceptions\Handler')->render($request, $exception);
        });

        //
        Schema::defaultStringLength(191);

        // 自定义多态类型
        Relation::morphMap([
//            'site_news'                 => 'App\Models\SiteNews',           // 站点新闻
//            'company_photo'             => 'App\Models\CompanyPhoto',       // 公司相册
//            'company_honor'             => 'App\Models\CompanyHonor',       // 公司荣誉
//            'company_menu'             => 'App\Models\CompanyProMenu',       // 公司菜单
//            'company_pro_config'        => 'App\Models\CompanyProConfig',   // 公司生产单元微站设置
//            'site_tiny_web_template'    => 'App\Models\SiteTinyWebTemplate',// 站点微店模板
//            'company_pro_report'        => 'App\Models\CompanyProReport',   // 检测报告
//            'company_pro_record'    => 'App\Models\CompanyProRecord',// 公司农事记录图片
//            'company_pro_record_pic'    => 'App\Models\CompanyProRecordPic',// 公司农事记录图片
//            'company_pro_input_pic'     => 'App\Models\CompanyProInputPic', // 公司生产投入品图片
//            'company_pro_input'         => 'App\Models\CompanyProInput',    // 公司生产投入品
//            'company_pro_unit'         => 'App\Models\CompanyProUnit',    // 公司生产单元
//            'shop'                 => 'App\Models\RunBuy\Shop',           // 店铺图片
//            'shop_goods'             => 'App\Models\RunBuy\ShopGoods',       // 商品图片
//            'shop_type'             => 'App\Models\RunBuy\ShopType',       // 店铺分类
//            'tables'                 => 'App\Models\RunBuy\Tables',           // 桌位或包间图片
//            'templates'                 => 'App\Models\DogTools\Templates',           // 小狗工具-模板库图片
//            'teacher_templates'                 => 'App\Models\DogTools\TeacherTemplates',           // 小狗工具-老师卡片图片
//            'classes'                 => 'App\Models\DogTools\Classes',           // 小狗工具-班级图片
            'company_certificate'                 => 'App\Models\QualityControl\CompanyCertificate',           // 1质检-企业资质证书图片
            'company_schedule'                 => 'App\Models\QualityControl\CompanySchedule',           // 2企业能力附表 pdf或word
            'company_ability'                 => 'App\Models\QualityControl\AbilityJoinItemsResults',           // 4企业能力验证上传图片
            'course'                 => 'App\Models\QualityControl\Course',           // 8课程主图上传图片
            'company_statement'                 => 'App\Models\QualityControl\CompanyStatement',           // 16机构自我声明
            'company_punish'                 => 'App\Models\QualityControl\CompanyPunish',           // 32机构处罚
            'platform_notices'                 => 'App\Models\QualityControl\PlatformNotices',           // 64通知公告
            'platform_down_files'                 => 'App\Models\QualityControl\PlatformDownFiles',           // 128表格下载
            'apply'                 => 'App\Models\QualityControl\Apply',           // 256 应用图片
            'order_pay_method'                 => 'App\Models\QualityControl\OrderPayMethod',           // 512 收款方式配置
            'card_photo'                 => 'App\Models\QualityControl\Staff',           // 1024 个人证件照
            'invoice'                 => 'App\Models\QualityControl\Invoices',           // 2048电子发票
            'vod_type'                 => 'App\Models\QualityControl\VodType',           // 4096点播课程分类
            'vods_cover'                 => 'App\Models\QualityControl\Vods',           // 8192点播课程封面
            'vod_video_cover'                 => 'App\Models\QualityControl\VodVideo',           // 16384点播视频封面
            'vod_video_file'                 => 'App\Models\QualityControl\VodVideo',           // 32768点播视频文件
            'vod_courseware'                 => 'App\Models\QualityControl\VodVideo',           // 65536点播视频课件资料
            'pay_project'                 => 'App\Models\QualityControl\PaymentProject',           // 131072收款项目
        ]);

        DB::listen(function ($query) {
            // $query->sql
            // $query->bindings
            // $query->time

            $sqlLog = [
                'sql'       => $query->sql,
                'bindings'  => $query->bindings,
                'time'      => $query->time,
            ];
            Log::info('sql执行日志',$sqlLog);
        });

        // 通过在 Queue facade 中使用 before 和 after 方法，你可以指定一个队列任务被执行前后的回调。
        // 这些回调是添加额外的日志或增加统计的绝好时机。通常，你应该在 服务提供者中调用这些方法。
        // 例如，我们可以使用 Laravel 的 AppServiceProvider ：
        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
            $queueLog = [
                'connectionName'       => $event->connectionName,
                'job'  => $event->job,
                'payload'      => $event->job->payload(),
            ];
            Log::info('队列日志  执行队列-前事件--> '. __FUNCTION__, $queueLog);
        });
        Queue::after(function (JobProcessed $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
            $queueLog = [
                'connectionName'       => $event->connectionName,
                'job'  => $event->job,
                'payload'      => $event->job->payload(),
            ];
            Log::info('队列日志  执行队列-后事件--> '. __FUNCTION__, $queueLog);
        });

        // 任务失败事件
        // 如果你想在任务失败时注册一个可调用的事件，你可以使用 Queue::failing 方法。
        // 该事件是通过 email 或 Slack 通知你团队的绝佳时机。
        //  例如，我们可以在 Laravel 中的 AppServiceProvider 中附加一个回调事件：
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
            $queueLog = [
                'connectionName'       => $event->connectionName,
                'job'  => $event->job,
                'exception'      => $event->exception,
            ];
            Log::info('队列日志  执行队列-任务失败事件--> '. __FUNCTION__, $queueLog);
        });

        // 在 Queue facade 使用 looping 方法可以在处理器尝试获取任务之前执行回调。
        // 例如，你也许想用一个闭包来回滚之前失败的任务尚未关闭的事务：
//        Queue::looping(function () {
//            while (DB::transactionLevel() > 0) {
//                DB::rollBack();
//            }
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        // 如果你不打算使用 Passport 的默认迁移，
        // 你应该在 AppServiceProvider 的 register 方法中调用 Passport::ignoreMigrations 方法。
        // Passport::ignoreMigrations();
    }
}
