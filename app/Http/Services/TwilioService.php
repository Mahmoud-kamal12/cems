<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

class TwilioService
{

    private mixed $sid;
    private mixed $token;
    private Client $twilioservice;


    /**
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $this->sid    = config("twilio.sid");
        $this->token  = config("twilio.token");
        $this->twilioservice = new Client($this->sid, $this->token);
    }

    /**
     * @throws \Exception
     */
    public function sendMessage($to , $message , $token): void
    {
        try {
            $message = $this->twilioservice->messages
                ->create($to,
                    array(
                        "from" => "+12512374959",
                        "body" => $message
                    )
                );
            $this->storeToken($token , $to);
        }catch (\Exception $exception){
            return;
        }

    }

    private function storeToken($token ,$phone): void
    {
            DB::table('password_resets')->insert([
                'phone' => $phone,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
    }
}
