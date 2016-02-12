<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Prescriber;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class PrescriberController extends ApiController
{
    public  $_validators = [
        'first_name' => 'required|max:225',
        'last_name' => 'required|max:225',
        'registration_number' => 'required|max:225|unique:prescriber,registration_number'
    ];

    public function __construct()
    {
        $this->model = new Prescriber();
        parent::__construct();
    }

    public function applyRequestFilters()
    {
        $name = Input::get('name');

        if ($name && !empty($name)) {
            $this->query->where('first_name', 'like', '%'.$name.'%')->orWhere('last_name', 'like', '%'.$name.'%')->orWhere('registration_number', 'like', '%'.$name.'%');
        }
    }
}
