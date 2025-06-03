<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


class SubscriptionService
{

    private $baseUrl = "https://stellersubscriptionapiprod.azurewebsites.net/api/";

    private $usernameKey = "APPSETTING_API_USERNAME_STELLER_SUBSCRIPTION_API";

    private $passwordKey = "APPSETTING_API_PASSWORD_STELLER_SUBSCRIPTION_API";

    public function findusersubscriptions(int $user_id, int $type = 0): Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey),getenv($this->passwordKey))
            ->get($this->baseUrl . "v1/subscriptioncontroller/user/subscriptions?user_id={$user_id}&type={$type}");
        return $response;
    }

    /**
     * @param string $id
     * @param string $type
     * @return Response
     */
    public function find(string $id, int $type = 0): Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey),getenv($this->passwordKey))
            ->get($this->baseUrl . "v1/subscriptioncontroller/find/{$id}?type={$type}");
        return $response;
    }

    public function user(int $user_id, int $type = 0): Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey),getenv($this->passwordKey))
            ->get($this->baseUrl . "v1/user/subscriptions?user_id={$user_id}&type={$type}");
        return $response;
    }

    public function patch(array $data): Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey),getenv($this->passwordKey))
            ->patch($this->baseUrl . "v1/subscriptioncontroller/patch", $data);
        return $response;
    }

    /**
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|Response
     */
    public function add(array $data) {
        $response = Http::withBasicAuth(getenv($this->usernameKey),getenv($this->passwordKey))
            ->post($this->baseUrl . "v1/subscriptioncontroller/add", $data);
        return $response;
    }

}