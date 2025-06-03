<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UserService
{

    private $baseUrl = "https://stellaruserapiprod.azurewebsites.net/api/";

    private $usernameKey = "APPSETTING_API_USERNAME_STELLAR_USER_API";

    private $passwordKey = "APPSETTING_API_PASSWORD_STELLAR_USER_API";

    /**
     * @param string $id
     * @return Response
     */
    public function user(string $id): Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey),getenv($this->passwordKey))
            ->get($this->baseUrl . "v1/usercontroller/user/$id");
        return $response;
    }

    public function sendresetpasswordlink(string $email): PromiseInterface|Response
    {

        $response = Http::withBasicAuth(getenv($this->usernameKey), getenv($this->passwordKey))->retry(3)
            ->post($this->baseUrl . "v1/usercontroller/sendresetpasswordlink?email=" . $email);
        return $response;

    }

    /**
     * @param array $data
     * @return PromiseInterface|Response
     */
    public function create(array $data): PromiseInterface|Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey), getenv($this->passwordKey))
            ->post($this->baseUrl . "v1/usercontroller/createuser", $data);
        return $response;
    }

    /**
     * @param array $data
     * @return PromiseInterface|Response
     */
    public function auth(array $data): PromiseInterface|Response
    {
        $response = Http::withBasicAuth(getenv($this->usernameKey), getenv($this->passwordKey))
            ->post($this->baseUrl . "v1/usercontroller/login", $data);
        return $response;
    }



}