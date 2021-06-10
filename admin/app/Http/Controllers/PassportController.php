<?php
namespace App\Http\Controllers;

use App\Services\DB\CommonDB;
use Illuminate\Http\Request;
use App\User;
use App\OAuthClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Redirect;

class PassportController extends Controller
{

    // 在注册功能中，我首先保存用户，然后获取用户并id在oauth_clients表中使用它
    public function register(Request $request)
    {
//        DB::beginTransaction();

        try {
            return CommonDB::doTransactionFun(function() use(&$request){
                try {
                    // 在注册表格中，我检查了一些基本验证，然后继续插入用户。
                    $validator = Validator::make($request->all(),[
                        'name' => 'required|string',
                        'email' => 'required|string|email|unique:users',
                        'password' => 'required|string|confirmed'
                    ]);
                    if ($validator->fails()) {
                        return Redirect::back()
                            ->withErrors($validator)
                            ->withInput();
                    }
                    else{
                        // 创建一个新用户，其中包含用于回滚方案的try catch块
                        try {
                            $user_save = User::create([
                                'name' => $request->name,
                                'email' => $request->email,
                                'password' => bcrypt($request->password)
                            ]);
                        }
                        catch(\Exception $e){
                            // DB::rollback();
                            $message = $e->getMessage();

                            // return response()->json(['error' => 1, 'message' => $message]);
                            throws($message);
                        }
                    }
                    // 创建用户后，我将获得插入的ID以在oauth_clients表中使用它。
                    $insertedId = $user_save->id;
                    // 秘密–随机的40个字符串
                    $secret = Str::random(40);
                    try {
                        $oauth_clients = OAuthClient::create([
                            "user_id" => $insertedId,
                            "secret" => $secret,
                            "name" => "Password Grant",// 名称–任何名称
                            "revoked" => 0,// 已撤消–客户端是否撤消访问，由于我们需要访问API，因此应为“ 0”
                            "password_client" => 1,// 密码客户端–由于是密码访问客户端，该值应为“ 1”
                            "personal_access_client" => 0,// 个人访问客户端–应为0，因为它是密码客户端，不是个人用户
                            "redirect" => "http://localhost",// 重定向-重定向URL
                        ]);
                    }
                    catch(\Exception $e){
                       // DB::rollback();
                        $message = $e->getMessage();
                        // return response()->json(['error' => 1, 'message' => $message]);

                        throws($message);
                    }
                    // 成功插入两个查询后，我可以使用数据库提交并为用户提供所需的客户端ID和密码。
                    // DB::commit();
                    return response()->json([
                        'message' => 'Successfully created user!',
                        'client_secret' => $secret,
                        'client_id' => $oauth_clients->id
                    ], 201);
                } catch (\Exception $e) {
                    // DB::rollback();
                    // something went wrong
                    $message = $e->getMessage();
                    // return response()->json(['error' => 1, 'message' => $message]);

                    throws($message);
                }
            });

        } catch ( \Exception $e) {
            $errStr = $e->getMessage();
            $errCode = $e->getCode();
            // throws($errStr, $errCode);
            // throws($e->getMessage());
            return response()->json(['error' => 1, 'message' => $errStr]);
        }
    }
}
