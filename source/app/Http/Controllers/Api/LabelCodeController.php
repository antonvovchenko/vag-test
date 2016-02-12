<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\LabelCode;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class LabelCodeController extends ApiController
{
    public  $_validators = [
        'title' => 'required|max:225|unique:label_code,title',
        'description' => 'required|max:225|unique:label_code,description'
    ];

    public function __construct()
    {
        $this->model = new LabelCode();
        parent::__construct();
    }

    public function applyRequestFilters()
    {
        $title = Input::get('title');

        if ($title && !empty($title)) {
            $this->query->where('title', 'like', '%'.$title.'%');
        }
    }
}
