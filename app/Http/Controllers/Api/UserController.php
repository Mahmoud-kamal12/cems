<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class UserController extends Controller
{
    public function getNearestUsers(Request $request)
    {
        try {
            $userLatitude = $request->input('user_latitude');
            $userLongitude = $request->input('user_longitude');
            $radius = 100000;

            $users = User::select(
            '*',
            \DB::raw('(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance')
            )
            ->where("type" , User::$type[1])
            ->addBinding($userLatitude, 'select')
            ->addBinding($userLongitude, 'select')
            ->addBinding($userLatitude, 'select')
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->get();

            return Helper::ResponseSuccuss(data:$users);
        } catch (\Exception $e) {
            return Helper::ResponseError(msg: $e->getMessage());
        }
    }

    public function updateToken(Request $request){
        User::where('email',$request->email)->update([
            "fcm_token" => $request->token
        ]);
        return Helper::ResponseSuccuss(msg:"token updated successfully");
    }

    public function sendMessageToAll(){
        $optionBuilder = new OptionsBuilder();

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'test data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = User::pluck('fcm_token')->toArray();

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        return response()->json([
            "numberSuccess" => $downstreamResponse->numberSuccess() ,
            "numberFailure" => $downstreamResponse->numberFailure() ,
            "numberModification" => $downstreamResponse->numberModification()
        ]);
    }
}
