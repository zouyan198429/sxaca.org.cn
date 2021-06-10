<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     * 不需要加密的 Cookie 名称。
     * @var array
     */
    protected $except = [
        //
    ];
}
