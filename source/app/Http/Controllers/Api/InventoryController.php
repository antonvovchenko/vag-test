<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Http\Requests;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Input;

class InventoryController extends ApiController
{
    public  $_validators = [
        'product_id' => 'required'
    ];

    public $query_with = ['user', 'product', 'supplier'];

    public $user_field = 'user_id';

    public function __construct()
    {
        $this->model = new Inventory();
        parent::__construct();
    }

    public function applyRequestFilters()
    {
        $product_id = Input::get('product_id');
        if ($product_id && !empty($product_id)) {
            $this->query->where('product_id', $product_id);
        }

        $type = Input::get('type');
        if ($type && !empty($type)) {
            $this->query->where('type', $type);
        }
    }
}
