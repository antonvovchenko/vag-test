<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests;
use App\Models\Customer;
use Illuminate\Support\Facades\Input;

class CustomerController extends ApiController
{
    public  $_validators = [
        'name' => 'required|max:225|unique:customer,name',
    ];

    public function __construct()
    {
        $this->model = new Customer();
        parent::__construct();
    }

    public function applyRequestFilters()
    {
        $name = Input::get('name');
        if ($name && !empty($name)) {
            $this->query->where('name', 'like', '%'.$name.'%');
        }
    }
}
