<?php

namespace App\Http\Controllers\Api;

// laravelpassport实现API认证（Laravel5.6）---authuser+jwt格式token的登陆状态
// https://segmentfault.com/a/1190000017560443?utm_source=tag-newest
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public $successStatus = 200;

    /**
     * 登录API
     *
     * @param Request $request
     * @param email    登录邮箱
     * @param password 登录密码
     *
     * @return \Illuminate\Http\Response
     *
     *  {
     *       "success": {
     *           "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjEyNmM2YjRkZDlkMWQzODRlZTA1Nzg1MmM5MzMzMGYyZjJhZTI4NGQ2MmRhNzYxZTA3YjQ5YTI3OWUzYTkzMGIyZjVlY2JiNzk0Y2MyYWMzIn0.eyJhdWQiOiIxIiwianRpIjoiMTI2YzZiNGRkOWQxZDM4NGVlMDU3ODUyYzkzMzMwZjJmMmFlMjg0ZDYyZGE3NjFlMDdiNDlhMjc5ZTNhOTMwYjJmNWVjYmI3OTRjYzJhYzMiLCJpYXQiOjE1NzE2Mzg1NzEsIm5iZiI6MTU3MTYzODU3MSwiZXhwIjoxNjAzMjYwOTcxLCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.JhS6uAm3t2p-EsfoHeyaszmcQSNnq8Ebg-dhN7aONSmbMNNYIBsiEnWWZ9yAjroxeF4RV_xr_prcmdgWHikY_ghjGBGbFUenIwszKxXA2OOTuUZYWSOZYWciBJ8ORfbAQtX4AP9MuHAdMPMy07QF-IH551QE4kdXiBILfXSNoUi4X3xLGJtZf9Yhv1SZIs3QVhbXPRCBMuJ00DGPVufja1ii0l5yBUSIBUnIA0umgno40-BHz9qD8AujJWPFMspo7y2n_2NlP20v0HIPsHIBk_ifXQY5ftzxU0lYGajVhZBj313euWx3WIGUUd4IroRUjD3b8gbq4AnoPslg74E9cFvP_wsl0bvN7ARrKv6MaSRq416rsZYFnLdzkyIP07OkiEKA1qMYnEIEPxfJ6oJP22LpGt3IhQk3Ga8uCTnCiCGbBMkMXHCGLZGOZtQq522A6-vVDcyqNd42ZNLAcH1Bvbjwewoc7AvknQ6yW_Ii5lxfbFdfboKi-xgj8JQmPXwfrYqKniJekGzKJ5XrOJwH-VVHi46oS3YxRPbOd2cyBJbTN2dhqySeoyg_lOU9EAH0lmTKgEORJZMvXJ_O1sghfVdezHrbpx5pK4SJYVAkICJKexy6AEH9HEguafkrOvQ9Yky-hwfwNFGPpB1UiUszmsPsocxv687_bAREUx_e4YU"
     *      }
     *  }
     *
     */
    public function login(Request $request)
    {
        // 邮箱和密码验证
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('EDU')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
     * 注册API     * @param Request $request
     * @param email    登录邮箱
     * @param password 登录密码
     *
     * @return \Illuminate\Http\Response
     *
     *  {
     *      "success": {
     *           "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI4NDk4MWJiYzE3NjllNmNmY2NkOWUyYTk0ZWM5Mzk3ZjBkZGQ2OGEyZjA3YzQ4YmM4NTYyY2UyMjllNDQ0YjVmNDEyZDJhOGY5ZGE1ZWMyIn0.eyJhdWQiOiIxIiwianRpIjoiYjg0OTgxYmJjMTc2OWU2Y2ZjY2Q5ZTJhOTRlYzkzOTdmMGRkZDY4YTJmMDdjNDhiYzg1NjJjZTIyOWU0NDRiNWY0MTJkMmE4ZjlkYTVlYzIiLCJpYXQiOjE1NzE2MzgwMDEsIm5iZiI6MTU3MTYzODAwMSwiZXhwIjoxNjAzMjYwNDAxLCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.R1jBnV4ChS-PhDX5LmJVFAux5x-oHZXbMRNGR3Qj7A62QM12CeS5oZjSpooPJEXZAA4tg8BAapqod18R4NpuHEqQIh2zaf118V2P8wyxefKmkbq5vnIhl0VS73QwF1jIB8lnYYG1guvZk3GMTDIikf_cXsUrtnJTUYddtz6-M7YohKVw8ktL-cdel5ep9KR-pQFMXnGRuJEhN4kCyuMmvDZjusqIjJquhR4V9aQsPOvTzpPsgJusKmNn_ismgauBaLbDDbwhryOG7mDf5N2NT_PonRqj5MRrooUKaYR-kuuaEi3tLeprKkKEw767kaOjRvcm55Dd08x39WkPOBJmLvnf4czP1O6FOXbdbOLPqrJvUzz-Cy8Ey_rf672j3Yi5wfGC1tgjb7D-K_TKz-PsmcWtmZ51-yo-2Fek5ok7RJt1oBMWzLk5-toIa7lGuSKVvMCpCq_M9GBaeeJLFVFm5uvA8xiSTnRSvyTH-j1Pt3E1BTngSTJj90457sptkmvRR6bWsYn0_OEh-SXbimCAKzigUKub1CRxop5aPw4N0gVX7pNREeSoLpq7dpUgv8Vk6J8UGiLemTYpPWTkt3RdeieFv9fPImSzks4SvTqHITH235VB-I9JVd_EbHwJJ15znvEbqlIt1Epq0WPIbvbP0FV_A4fssCCcE6kxQUdtx1I",
     *           "name": "dafdsfdfsfas"
     *       }
     *   }
     *
     */
    public function register(Request $request)
    {
        // 数据验证
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email',
            'password'   => 'required',
            'c_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        // 读取参数并保存数据
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        // 创建token并返回
        $success['token'] = $user->createToken('EDU')->accessToken;
        $success['name'] = $user->name;
        return response()->json(['success'=>$success], $this->successStatus);
    }

    /**
     * 读取用户信息API
     *  head Authorization:Bearer{空格}eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI4NDk4MWJiYzE3NjllNmNmY2NkOWUyYTk0ZWM5Mzk3ZjBkZGQ2OGEyZjA3YzQ4YmM4NTYyY2UyMjllNDQ0YjVmNDEyZDJhOGY5ZGE1ZWMyIn0.eyJhdWQiOiIxIiwianRpIjoiYjg0OTgxYmJjMTc2OWU2Y2ZjY2Q5ZTJhOTRlYzkzOTdmMGRkZDY4YTJmMDdjNDhiYzg1NjJjZTIyOWU0NDRiNWY0MTJkMmE4ZjlkYTVlYzIiLCJpYXQiOjE1NzE2MzgwMDEsIm5iZiI6MTU3MTYzODAwMSwiZXhwIjoxNjAzMjYwNDAxLCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.R1jBnV4ChS-PhDX5LmJVFAux5x-oHZXbMRNGR3Qj7A62QM12CeS5oZjSpooPJEXZAA4tg8BAapqod18R4NpuHEqQIh2zaf118V2P8wyxefKmkbq5vnIhl0VS73QwF1jIB8lnYYG1guvZk3GMTDIikf_cXsUrtnJTUYddtz6-M7YohKVw8ktL-cdel5ep9KR-pQFMXnGRuJEhN4kCyuMmvDZjusqIjJquhR4V9aQsPOvTzpPsgJusKmNn_ismgauBaLbDDbwhryOG7mDf5N2NT_PonRqj5MRrooUKaYR-kuuaEi3tLeprKkKEw767kaOjRvcm55Dd08x39WkPOBJmLvnf4czP1O6FOXbdbOLPqrJvUzz-Cy8Ey_rf672j3Yi5wfGC1tgjb7D-K_TKz-PsmcWtmZ51-yo-2Fek5ok7RJt1oBMWzLk5-toIa7lGuSKVvMCpCq_M9GBaeeJLFVFm5uvA8xiSTnRSvyTH-j1Pt3E1BTngSTJj90457sptkmvRR6bWsYn0_OEh-SXbimCAKzigUKub1CRxop5aPw4N0gVX7pNREeSoLpq7dpUgv8Vk6J8UGiLemTYpPWTkt3RdeieFv9fPImSzks4SvTqHITH235VB-I9JVd_EbHwJJ15znvEbqlIt1Epq0WPIbvbP0FV_A4fssCCcE6kxQUdtx1I

     *
     * @return \Illuminate\Http\Response
     *
     *{
     *   "success": {
     *       "id": 3,
     *      "name": "dafdsfdfsfas",
     *      "email": "dsfdsaf@163.com",
     *       "created_at": "2019-10-21 14:06:41",
     *      "updated_at": "2019-10-21 14:06:41"
     *  }
     *}
     *
     *
     *
     */
    public function read()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}
