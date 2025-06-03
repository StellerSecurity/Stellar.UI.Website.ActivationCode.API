<?php

namespace App\Helpers;

use App\LicenseType;

class LicenseHelper
{

    public static function whichLicenseType(string $code): int
    {

        $licenseType = LicenseType::VPN->value;

        $first = strtolower(mb_substr($code, 0, 1));

        if($first == "a") {
            $licenseType = LicenseType::ANTIVIRUS->value;
        }

        return $licenseType;

    }

}