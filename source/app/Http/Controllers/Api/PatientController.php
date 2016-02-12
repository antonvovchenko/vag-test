<?php

namespace App\Http\Controllers\Api;

use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Patient;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class PatientController extends ApiController
{
    public  $_validators = [
        'name' => 'required|max:225|unique:patient,name'
    ];

    public function __construct()
    {
        $this->model = new Patient();
        parent::__construct();
    }

    public function applyRequestFilters()
    {
        $name = Input::get('name');

        if ($name && !empty($name)) {
            $this->query->where('name', 'like', '%'.$name.'%');
        }
    }

    public function postHistory()
    {
        $patient_id = Input::get('patient_id');

        $prescriptions = Prescription::where('patient_id', $patient_id)
            ->with(['patient', 'pharmacist_user', 'creator_user', 'prescriber', 'prescription_items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($prescriptions as $prescription) {
            $data[] = $prescription->getItemData();
        }
        return $this->successResponse($data);
    }
}
