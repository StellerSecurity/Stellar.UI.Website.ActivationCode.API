<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ActivationLicenseService
{
    private $baseUrl = "https://stellaractivationlicenseapiprod.azurewebsites.net/api/";

    private $usernameKey = "APPSETTING_API_USERNAME_STELLAR_ACTIVATIONLICENSE_API";

    private $passwordKey = "APPSETTING_API_PASSWORD_STELLAR_ACTIVATIONLICENSE_API";

    public function activate(string $code, int $type, int $activate): PromiseInterface|Response|null
    {
        try {
            $response = Http::withBasicAuth(getenv($this->usernameKey), getenv($this->passwordKey))->retry(3)->timeout(15)
                ->post($this->baseUrl . "v1/activationlicensecontroller/activate", ['code' => $code, 'type' => $type, 'activate' => $activate]);
        } catch (RequestException $exception) {
            return null;
        }
        return $response;
    }

}