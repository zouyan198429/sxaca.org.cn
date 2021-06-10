<?php

namespace App\Http\Controllers;

//使用 Laravel Passport 为你的 REST API 增加用户认证功能
//https://zhuanlan.zhihu.com/p/64902443

use App\User;
use Illuminate\Http\Request;

class PassportaaController extends Controller
{
    /**
     * Handles Registration Request
     * 在 register 的方法中，我们验证请求数据然后创建用户。我们使用 createToken 方法创建 token，
     * 并将名称作为参数传递。最后，我们在 JSON 响应中返回 token。
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('TutsForWeb')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Handles Login Request
     * 在 login 方法中，我们尝试使用请求参数进行身份验证。然后，根据尝试的成功或失败返回适当的响应。
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('TutsForWeb')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    /**
     * Returns Authenticated User Details
     * 在 details 方法中我们只返回用户模型。
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
}