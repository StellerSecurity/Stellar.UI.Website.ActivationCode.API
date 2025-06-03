<?php

namespace App\Http\Controllers\V1;

use App\Helpers\LicenseHelper;
use app\Http\Controllers\Controller;
use App\LicenseType;
use App\Services\ActivationLicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class ActivationController extends Controller
{

    private ActivationLicenseService $activationLicenseService;

    public function __construct(ActivationLicenseService $activationLicenseService)
    {
        $this->activationLicenseService = $activationLicenseService;
    }

    public function verify(Request $request): JsonResponse
    {

        $code = $request->input('code');

        if(empty($code)) {
            return response()->json(['response_code' => 400, 'response_message' => 'No code provided.']);
        }

        $licenseType = LicenseHelper::whichLicenseType($code);

        $license = $this->activationLicenseService->activate($code, $licenseType, 0)->object();

        if($license === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'Activation License is not valid']);
        }

        if($license->response_code !== 200) {
            return response()->json(['response_code' => 400, 'response_message' => 'Activation License is not valid / already used.']);
        }

        return response()->json(['response_code' => 200, 'response_message' => 'OK. Found.', 'license_type' => $licenseType]);

    }

}