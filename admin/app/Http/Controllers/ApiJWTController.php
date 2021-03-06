<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
// use App\User;
use App\Admin as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use JWTFactory;use Tymon\JWTAuth\Exceptions\JWTException;

class ApiJWTController extends Controller
{

    public $loginAfterSignUp = true;

    public function register(RegisterAuthRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * {
     *      "success": true,
     *       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9ydW5idXkuYWRtaW4uY3Vud28ubmV0XC9hcGlcL3JlZ2lzdGVyIiwiaWF0IjoxNTcxMTk3Njc4LCJleHAiOjE1NzEyMDEyNzgsIm5iZiI6MTU3MTE5NzY3OCwianRpIjoiMk1vUGRGNkcyRGlIZTJlaiIsInN1YiI6MSwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9._XTTUbeDvIqguL82O62L_TBrOFng7VYdJyqERO2UtxM"
     *  }
     *
     */
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        $tokenData = $input;

        //if (!$jwt_token = JWTAuth::attempt($input)) {
        if (!$jwt_token = JWTAuth::attempt($tokenData)) {
        // if (!$jwt_token = JWTAuth::fromSubject($tokenData)) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    // ??????
    //    // ??????????????? Facade?????????????????????
    //    auth('api')->refresh();
    //    // ??? JWTAuth Facade
    //    JWTAuth::parseToken()->refresh();

    public function getAuthUser(Request $request)
    {

        $this->validate($request, [
            'token' => 'required'
        ]);

        // ??????token??????????????????
//        $tokenData = JWTAuth::getPayload()->get('sub');//->get('userData');
//        pr($tokenData);
        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function testaa(Request $request){
//        $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
//        $payload = JWTFactory::make($customClaims);
//        $token = JWTAuth::encode($payload);
//        pr($token);


        //token??????
        $customClaims = [
            'sub' => [
                'foo' => 'bar'
            ],
            'testaa' => 'dfsfsdfsdfd',
        ];
        $payload = JWTFactory::customClaims($customClaims)->make();
        $token = JWTAuth::encode($payload);
        pr($token);

//        $payload = JWTFactory::customClaims($customClaims)->make();
//        pr($payload);
//        $token = Auth::guard('api')->attempt($payload);
//        pr($token);

        // ????????????????????????????????????????????????token???
        // JWTAuth::setToken('foo.bar.baz');


//        $customClaims = ['sub'=> 'aaabbb', 'foo' => 'bar', 'baz' => 'bob'];
//        $payload = JWTFactory::make($customClaims);
//        $token = JWTAuth::encode($payload);
//        pr($token);
//        // Facade - 3
//        $payload = JWTFactory::sub(123)->aud('foo')->foo(['bar' => 'baz'])->make();
//        $token = JWTAuth::encode($payload);
//        pr($token);
    }

    public function testbb(Request $request){

        // ?????? token
        // ?????? token ????????????????????????????????????????????????????????????????????? token ????????? token ??????????????????????????????????????? false???
        // header?????? Authorization Bearer {jwt} ??? post/get ?????? token  ???????????????
        // ????????????
        // $token = auth('api')->getToken();
        // Facade
        // $token = JWTAuth::parseToken()->getToken();// ??? request ????????? token ?????????????????????????????????????????????
        //  $token = JWTAuth::getToken();// // ???????????? parseToken ??????????????????????????????????????????
        // pr($token);
        // ?????? token???
        // $newToken = JWTAuth::parseToken()->refresh()

        // ????????? token ?????????
       // JWTAuth::parseToken()->invalidate(JWTAuth::getToken());
       //  auth('api')->invalidate(auth('api')->getToken());
       // refresh
        // ?????? token???
        // $newToken = JWTAuth::parseToken()->refresh();
//        $newToken = auth('api')->refresh();
//        echo $newToken;
//        echo '<br/><br/>';

        // ?????? token ???????????????
        if(JWTAuth::parseToken()->check()) {
        // if(Auth::guard('api')->check()) {// ?????????-????????????: $credentials = request(['email', 'password']); auth('api')->attempt($credentials)
            dd("token????????????");
        }else{
            dd("token????????????");
        }


        // ?????? payload ??????
        // $payload = auth('api')->payload();
//        $payload = JWTAuth::parseToken()->payload();
//        pr($payload);
        // ?????? payload ??????
        // $sub = auth('api')->payload()->get('sub');
        //$sub = JWTAuth::parseToken()->payload()->get('sub');


        // $json = auth('api')->payload()->toJson();
//        $array = JWTAuth::parseToken()->payload()->jsonSerialize();
//        //  pr($array);
//        $sub = $array['sub'];
//        pr($sub);

        //???????????????????????????????????????
        // $sub = JWTAuth::parseToken()->getClaim('sub');
        //  pr($sub);

    }
}
