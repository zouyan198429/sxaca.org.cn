<?php

namespace App\Http\Middleware;
// 配置可信代理
use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     * 这个应用程序的可信代理列表
     * @var array|string
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     * 应该用来检测代理的头信息。
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
