<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\ProductMarkupType;
use Illuminate\Http\Request;
use App\Http\Requests;

class ProductMarkupTypeController extends ApiController
{
    public  $_validators = [
        'title' => 'required|max:255',
        'description' => 'required|max:255',
        'default_markup' => 'required|max:5'
    ];

    public function __construct()
    {
        $this->model = new ProductMarkupType();
        parent::__construct();
    }

}
