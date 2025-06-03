<?php

namespace App\Http\Controllers\V1;

use App\Helpers\LicenseHelper;
use app\Http\Controllers\Controller;
use App\LicenseType;
use App\Services\ActivationLicenseService;
use App\Services\SubscriptionService;
use App\Services\UserService;
use App\SubscriptionStatus;
use App\SubscriptionType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{

    private ActivationLicenseService $activationLicenseService;

    private UserService $userService;

    private SubscriptionService  $subscriptionService;

    public function __construct(ActivationLicenseService $activationLicenseService, UserService $userService, SubscriptionService $subscriptionService)
    {
        $this->activationLicenseService = $activationLicenseService;
        $this->userService = $userService;
        $this->subscriptionService = $subscriptionService;
    }

    public function login(Request $request): JsonResponse
    {

        $code = $request->input('code');

        if(empty($code)) {
            return response()->json(['response_code' => 400, 'response_message' => 'No code provided.']);
        }

        $username = $request->input('username');
        $password = $request->input('password');

        $auth = $this->userService->auth(['username' => $username, 'password' => $password])->object();

        if(!isset($auth->user->id)) {
            return response()->json(['response_code' => 400, 'response_message' => 'User not authenticated.']);
        }

        $licenseType = LicenseHelper::whichLicense($code);

        $subscriptionType = SubscriptionType::ANTIVIRUS->value;

        if($licenseType == LicenseType::VPN->value) {
            $subscriptionType = SubscriptionType::VPN->value;
        }

        $subscriptions = $this->subscriptionService->findusersubscriptions($auth->user->id, $subscriptionType)->object();

        $license = $this->activationLicenseService->activate($code, $licenseType, 1)->object();

        if(!isset($license->subscription_days)) {
            return response()->json(['response_code' => 400, 'response_message' => 'License expired.']);
        }

        if(!is_array($subscriptions)) {

            $this->subscriptionService->add([
                'user_id' => $auth->user->id,
                'type' => $subscriptionType,
                'status' => SubscriptionStatus::ACTIVE->value,
                'expires_at' => Carbon::now()->addDays($license->subscription_days)
            ])->object();

        } else {

            $subscription = $subscriptions[0];
            // if the expires_at is in the past, then set it to now, so we add x days from now.
            $now = Carbon::now();
            if($subscription->expires_at < $now) {
                $subscription->expires_at = $now;
            }

            $subscription->status = SubscriptionStatus::ACTIVE->value;
            $subscription->expires_at = Carbon::parse($subscription->expires_at)->addDays($license->subscription_days);
            $this->subscriptionService->patch((array) $subscription)->object();

        }

        return response()->json(['response_code' => 200, 'response_message' => 'OK.', 'auth' => $auth]);

    }

    public function create(Request $request): JsonResponse
    {

        $code = $request->input('code');

        if(empty($code)) {
            return response()->json(['response_code' => 400, 'response_message' => 'No code provided.']);
        }

        $username = $request->input('username');
        $password = $request->input('password');

        $auth = $this->userService->create([
            'username' => $username,
            'password' => $password
        ])->object();

        if(!isset($auth->user->id)) {
            return response()->json(['response_code' => 400, 'response_message' => 'User not created.']);
        }

        $licenseType = LicenseHelper::whichLicense($code);

        $license = $this->activationLicenseService->activate($code, $licenseType, 1)->object();

        $licenseType = LicenseHelper::whichLicense($code);

        $subscriptionType = SubscriptionType::ANTIVIRUS->value;

        if($licenseType == LicenseType::VPN->value) {
            $subscriptionType = SubscriptionType::VPN->value;
        }

        $subscription = $this->subscriptionService->add([
            'user_id' => $auth->user->id,
            'type' => $subscriptionType,
            'status' => SubscriptionStatus::ACTIVE->value,
            'expires_at' => Carbon::now()->addDays($license->subscription_days)
        ])->object();

        $auth->subscription_id = $subscription->id;

        return response()->json($auth);

    }

}