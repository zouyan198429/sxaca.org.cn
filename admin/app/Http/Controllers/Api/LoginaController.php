<?php
namespace App\Http\Controllers\Api;

// Laravel+passport 实现API认证 --未验证
// https://blog.csdn.net/hhhzua/article/details/80170447
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Validator;


class LoginaController extends ApiController
{
    protected $successStatus = 200;

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'logout'
        ]);
    }

    // 登录用户名标示为email字段
    public function username()
    {
        return 'email';
    }

    public function login(Request $request){

        $user = $request->only(['password','email']);
        if(count($user) != 2)return response()->json(['error_code'=>203,'error'=>'Missing Parameter']);
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('Imagingbay')->accessToken;
            $success['email'] =  $user->email;
            $success['uid'] =  $user->id;
            return response()->json(['error_code'=>0,'data' => $success], 200)->withHeaders(
                [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$success['token']
                ]
            );
        }
        else{
            return response()->json(['error_code'=>202,'error'=>'Unauthorised'], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:user_ordinary|min:4',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('Imagingbay')->accessToken;
        $success['email'] =  $user->email;
        $success['uid'] =  $user->id;

        return response()->json(['error_code'=>0,'data'=>$success], $this->successStatus);
    }

    /*
     * 注销账号
     */
    public function logout(Request $request)
    {
        // 'status_code' => 200, 'data' => null]);

        $user = $this->guard()->user();
        if (empty($user)) {
            return response()->json(['message' => '暂未登录', 'error_code' => 20403, 'data' => null]);
        }

        // 获取当前登陆用户的token并且将其删除
        $token = Auth::guard('api')->user()->token();
        if (empty($token)) {
            return response()->json(['message' => '暂无有效令牌', 'error_code' => 20403, 'data' => null]);
        }

        if (!empty($token->delete())) {
            return response()->json(['message' => '退出成功', 'error_code' => 0, 'data' => null]);
        } else {
            return response()->json(['message' => '退出失败', 'error_code' => 0, 'data' => null]);
        }
    }


    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetails()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

}