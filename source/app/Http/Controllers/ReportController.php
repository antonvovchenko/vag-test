<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function getCashier()
    {
        $start = Input::get('start');
        $end = Input::get('end');
        $action = Input::get('action', 'view');

        $data = $this->prepareCashierReportData($start, $end);

        if ($action == 'Export to Excel') {
            Excel::create('text', function($excel) use($data) {
                $excel->sheet('Sheetname', function($sheet) use($data) {
                    $sheet->fromArray($data);
                });
            })->download('xls');
        }

        return view('report/cashier', [
            'title' => "Cashier's Report",
            'current_date' => date('m/d/Y'),
            'start' => date('m/d/Y', strtotime($start)),
            'end' => date('m/d/Y', strtotime($end)),
            'data' => $data,
            'action' => $action
        ]);
    }

    public function getGct()
    {
        $start = Input::get('start');
        $end = Input::get('end');
        $action = Input::get('action', 'view');

        $data = $this->prepareGctReportData($start, $end);

        if ($action == 'Export to Excel') {
            Excel::create('text', function($excel) use($data) {
                $excel->sheet('Sheetname', function($sheet) use($data) {
                    $sheet->fromArray($data);
                });
            })->download('xls');
        }

        return view('report/gct', [
            'title' => "GCT Report",
            'current_date' => date('m/d/Y'),
            'start' => date('m/d/Y', strtotime($start)),
            'end' => date('m/d/Y', strtotime($end)),
            'data' => $data,
            'action' => $action
        ]);
    }

    public function getPurchase()
    {
        $start = Input::get('start');
        $end = Input::get('end');
        $action = Input::get('action', 'view');

        $data = $this->preparePurchaseReportData($start, $end);

        if ($action == 'Export to Excel') {
            Excel::create('text', function($excel) use($data) {
                $excel->sheet('Sheetname', function($sheet) use($data) {
                    $sheet->fromArray($data);
                });
            })->download('xls');
        }

        return view('report/purchase', [
            'title' => "Purchase Report",
            'current_date' => date('m/d/Y'),
            'start' => date('m/d/Y', strtotime($start)),
            'end' => date('m/d/Y', strtotime($end)),
            'data' => $data,
            'action' => $action
        ]);
    }

    public function prepareCashierReportData($start, $end)
    {
        $data = [
            [
                'data1', 'data2'
            ],
            [
                'data3', 'data4'
            ]
        ];
        return $data;
    }

    public function prepareGctReportData($start, $end)
    {
        $data = [
            [
                'data1', 'data2'
            ],
            [
                'data3', 'data4'
            ]
        ];
        return $data;
    }

    public function preparePurchaseReportData($start, $end)
    {
        $data = [
            [
                'data1', 'data2'
            ],
            [
                'data3', 'data4'
            ]
        ];
        return $data;
    }
}
