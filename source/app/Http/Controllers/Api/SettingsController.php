<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Library\CommonService;
use App\Library\PrinterService;
use App\Models\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class SettingsController extends ApiController
{
    public  $_validators = [
        'name' => 'required|max:255'
    ];

    public function __construct()
    {
        $this->model = new Settings();
        parent::__construct();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get current license
     */
    public function getLicense()
    {
        $license_check = [
            'error' => 'License not found',
            'license' => false
        ];

        $license = Settings::getStoredLicense();

        if ($license) {
            $license_value = json_decode($license->value);
            if (isset($license_value->key)) {
                $license_key = $license_value->key;

                $license_check = CommonService::checkLicenseKey($license_key);
                CommonService::updateLicenseSettings($license_check['license']);
            }
        }

        return $this->successResponse($license_check);
    }

    /**
     * Get current license
     */
    public function postLicense()
    {
        $license_key = Input::get('key');

        $license_check = CommonService::checkLicenseKey($license_key);


        if (!$license_check['error']) {
            $license = CommonService::updateLicenseSettings($license_check['license']);
            return $this->successResponse([
                'license' => json_decode($license->value, true),
                'error' => false
            ]);

        } else {
            return $this->errorResponse([
                'license' => false,
                'error' => $license_check['error']
            ]);
        }
    }

    public function applyRequestFilters()
    {
        $name = Input::get('name');

        if ($name && !empty($name)) {
            $this->query->where('name', 'like', '%'.$name.'%');
        }
    }

    /**
     * Get available printers
     */
    public function getPrinters()
    {
        $printerService = new PrinterService();
        return $this->successResponse($printerService->printers);
    }
}
