<?php

namespace App\Library;

use App\Models\Settings;
use Illuminate\Support\Facades\Config;

class CommonService
{
    static public function checkLicenseKey($license_key)
    {
        $lic_url = Config::get('app.lic_server_url').'?key='.$license_key;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $lic_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        //success
        if ($httpCode == 200) {
            return json_decode($response, true);
        }
        //license server not work
        else if ($httpCode != 200 && $httpCode != 404) {
            return [
                'license' => false,
                'error' => "License is not verified. License Server is not work at the moment. Please try again later."
            ];
        }
        //license error
        else {
            return json_decode($response, true);
        }
    }

    static public function updateLicenseSettings($licenseData)
    {
        Settings::where('name', 'license')->delete();
        $license = new Settings();
        $license->name = 'license';
        $license->value = json_encode($licenseData);
        $license->save();
        return $license;
    }

    static public function convertNumber($value)
    {
        $switched = str_replace(',', '', $value);
        if (is_numeric($value)) {
            return intval($value);
        } elseif (is_numeric($switched)){
            return floatval($switched);
        } else {
            return $value;
        }
    }

}
