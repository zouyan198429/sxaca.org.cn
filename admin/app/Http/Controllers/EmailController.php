<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * @param $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function verify(Request $request, $token = '')
    {
        if(strlen($token) <= 0 ) return redirect('/' );
        // 在注册用户表中查找对应的token
        $user = User::where('confirmation_token', $token)->where('is_active', 0)->first();

        // 如果是空，则跳转到主页
        if (is_null($user)) {
            return redirect('/' );
        }

        // 如果非空，则置is_active为1，重置token，防止反复验证，并保存到用户数据库中，自动登录，最后跳转到用户中心
        $user->is_active = 1;
        $user->confirmation_token = str_random(40);
        $user->save();

        \Auth::login($user);
        // return redirect('/home');
        return redirect($this->redirectTo);
    }
}
