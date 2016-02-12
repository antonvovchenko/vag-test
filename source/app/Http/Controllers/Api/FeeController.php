<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests;
use App\Models\Fee;

class FeeController extends ApiController
{
    public  $_validators = [
        'card_type' => 'required|max:225|unique:fee,card_type',
        'description' => 'required|max:225|unique:fee,description',
        'fees' => 'required|max:225|unique:fee,fees',
    ];

    public function __construct()
    {
        $this->model = new Fee();
        parent::__construct();
    }
}
