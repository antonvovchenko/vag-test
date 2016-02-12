<?php

namespace App\Http\Controllers;
use App\Library\CommonService;
use App\Models\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use JWTAuth;

class WelcomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //post
        if ($request->isMethod('post')) {
            $license_key = $request->input('key');
            $check = CommonService::checkLicenseKey($license_key);

            if (!$check['error']) {
                CommonService::updateLicenseSettings($check['license']);
                return redirect('/');
            } else {
                Session::flash('error', $check['error']);
                return view('license');
            }
        }

        $license = Settings::getStoredLicense();

        //show license form
        if ($license && $license->value) {

            $license_value = json_decode($license->value);
            $license_key = $license_value->key;

            $check = CommonService::checkLicenseKey($license_key);

            if (!$check['error']) {
                $license_check = [
                    'valid' => true,
                    'data' => $check['license']
                ];
            } else {
                $license_check = [
                    'valid' => false,
                    'message' => $check['error']
                ];
            }
            return view('index', [
                'licenseBLD' => json_encode($license_check),
                'userFullName' => session('full_name')
            ]);
        } else {
            return view('license');
        }
    }

    public function headers()
    {
        echo '<pre>';
        var_dump(apache_request_headers());
    }

}
