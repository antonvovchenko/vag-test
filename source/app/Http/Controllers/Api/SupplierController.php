<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Supplier;
use App\Http\Requests;

class SupplierController extends ApiController
{
    public  $_validators = [
        'name' => 'required|max:255'
    ];

    public function __construct()
    {
        $this->model = new Supplier();
        parent::__construct();
    }
}
