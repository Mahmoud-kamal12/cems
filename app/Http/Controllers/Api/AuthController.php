<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Services\TwilioService;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public $twilioService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
        $this->middleware('auth:api', ['only' => ['profile','logout']]);
    }

    public function login()
    {
        $credentials = request(['phone', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'phone' => 'required|unique:users,phone,'. $request->phone,
            'username' => 'required|unique:users,email,'. $request->username,
            'email' => 'required|unique:users,email,'. $request->email,
            'password' => 'required',
            'name' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userData = $request->all();
        try {
            if ($request->has('image')){
                $path = $request->file('image')->store('uploads', 'public');
                $userData['image'] = $path;
            }
            $userData['password'] = bcrypt($userData['password']);
            if (User::create($userData)){
                $token = random_int(100000, 999999);
                $this->twilioService->sendMessage($userData['phone'] , "code {$token}" , $token);
                return Helper::ResponseSuccuss(msg:"user created successfully");
            }else{
                throw new \Exception("error when create user");
            }
        }catch (\Exception $exception){
            return Helper::ResponseError(msg: $exception->getMessage());
        }
    }

    public function verfiyPhone(Request $request){

        $validator = \Validator::make($request->all(), [
            'phone' => 'required',
            'token' => 'required'
        ]);

        $checkToken = DB::table('password_resets')
            ->where("phone" , $request->phone)
            ->where("token" , $request->token)
            ->first();

        if ($checkToken){
            $user = User::where("phone" , $request->phone)->first();
            $user->update(["phone_verified" => now()]);
            DB::table('password_resets')
                ->where("phone" , $request->phone)
                ->where("token" , $request->token)
                ->delete();
            return Helper::ResponseSuccuss(msg:"phone verified");
        }

        return Helper::ResponseError(msg:"error token");
    }

}
