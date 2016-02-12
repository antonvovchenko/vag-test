<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Prescription;
use App\Models\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class PrintController extends Controller
{
    public function printScreen(Request $request)
    {
        if (Input::get('prescription_id') !== null) {

            if (!$prescription = Prescription::where('id', Input::get('prescription_id'))->first()) {
                //todo: add error template
            }

            return view('print/prescription/prescription', [
                'title' => 'Prescription #'.$prescription->id,
                'pharmacist' => $request->session()->get('full_name'),
                'content_title' => 'CUSTOMER RECEIPT',

                'pharmacy_name' => Settings::getValue('common_pharmacy_name'),
                'pharmacy_address' => Settings::getValue('common_pharmacy_address'),
                'pharmacy_city' => Settings::getValue('common_pharmacy_city'),
                'pharmacy_phone' => Settings::getValue('common_pharmacy_phone'),
                'pharmacy_gct' => Settings::getValue('common_pharmacy_gct'),

                'date' => date('m/d/Y'),
                'time' => date('H:i:s a'),

                'prescription' => $prescription

            ]);
        } else if (Input::get('prescription_labels_id') !== null) {

            if (!$prescription = Prescription::where('id', Input::get('prescription_labels_id'))->first()) {
                //todo: add error template
            }
            $data = [];
            foreach ($prescription->prescription_items as $key=>$prescription_item) {
                $data[$key]['product'] = [
                    'key' => $key+1,
                    'title' => $prescription_item->product->title
                ];
                $data[$key]['labels'] = '';
                $data[$key]['rows'] = 0;
                foreach ($prescription_item->label_codes as $k=>$label_code) {
                    $data[$key]['labels'] .= $label_code->label_code->description.PHP_EOL;
                    $data[$key]['rows'] ++;
                }
            }

            return view('print/prescription/prescription_labels', [
                'title' => 'Prescription Labels #'.$prescription->id,
                'pharmacist' => $request->session()->get('full_name'),
                'content_title' => 'LIST OF LABELS',

                'pharmacy_name' => Settings::getValue('common_pharmacy_name'),
                'pharmacy_address' => Settings::getValue('common_pharmacy_address'),
                'pharmacy_city' => Settings::getValue('common_pharmacy_city'),
                'pharmacy_phone' => Settings::getValue('common_pharmacy_phone'),

                'date' => date('m/d/Y'),

                'prescription' => $prescription,
                'data' => $data

            ]);
        } else if (Input::get('return_drug_id') !== null) {

            if (!$inventory = Inventory::where('id', Input::get('return_drug_id'))->first()) {
                //todo: add error template
            }

            return view('print/inventory/return_drug', [
                'title' => 'Return Drug #'.$inventory->id,

                'pharmacy_name' => Settings::getValue('common_pharmacy_name'),
                'pharmacy_address' => Settings::getValue('common_pharmacy_address'),
                'pharmacy_city' => Settings::getValue('common_pharmacy_city'),
                'pharmacy_phone' => Settings::getValue('common_pharmacy_phone'),
                'pharmacy_gct' => Settings::getValue('common_pharmacy_gct'),

                'date' => date('m/d/Y'),
                'time' => date('H:i:s a'),

                'inventory' => $inventory

            ]);

        }

    }


}
