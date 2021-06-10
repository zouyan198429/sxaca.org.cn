<?php

namespace App\Http\Controllers\Api;

// Laravel+passport 实现API认证 --未验证
// https://blog.csdn.net/hhhzua/article/details/80170447

use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use  AuthenticatesUsers;
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('api');
    }

    //调用认证接口获取授权码
    protected function authenticateClient(Request $request)
    {
        $credentials = $this->credentials($request);

        $data = $request->all();
        if ($request->refresh_token) {
            $request->request->add([
                'grant_type' => $data['grant_type'],
                'client_id' => $data['client_id'],
                'client_secret' => $data['client_secret'],
                'refresh_token' => $data['refresh_token'],
                'scope' => ''
            ]);
        } else {
            $request->request->add([
                'grant_type' => $data['grant_type'],
                'client_id' => $data['client_id'],
                'client_secret' => $data['client_secret'],
                'email' => $data['email'],
                'password' => $data['password'],
                'scope' => ''
            ]);
        }

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = \Route::dispatch($proxy);

        return $response;
    }
    /*
     *重写AuthenticatesUsers部分功能函数来实现整个完整的授权流程
     */
    protected function authenticated(Request $request)
    {
        return $this->authenticateClient($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $msg = $request['errors'];
        $code = $request['code'];
        return $this->failed($msg,$code);
    }
}