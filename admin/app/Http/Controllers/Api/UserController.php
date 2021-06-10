<?php
namespace App\Http\Controllers\Api;

// 创建路由(router/api.php) 验证为：auth中间件，guards为api
// http://www.manongjc.com/article/106150.html
//    编辑控制器
//    利用passport自带方法，实现token请求
//    利用封装工具来实现获取token
//    注意 获取下一个token，记得删除上一个token值（如果不删除之前的token也可以验证成功）

use App\Helpers\ProxyTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    use ProxyTrait;
    public $successStatus = 200;

    /**
     * 登录
     * */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            //删除之前的token（此删除适合方法一）
            DB::table('oauth_access_tokens')->where('user_id',$user->id)->where('name','MyApp')->update(['revoked'=>1]);

            //方法一：获取新的token
            $success['token'] =  $user->createToken('MyApp')->accessToken;

            //方法二：获取新的token（先引入ProxyTrait工具）
            $token = $this->authenticate();
            $user['token'] = $token['access_token'];

            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
     * 注册
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //方法一：获取token（注册成功后自动登录）
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success], $this->successStatus);
    }

    /**
     * 获取用户详情
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}