<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UserController extends ApiController
{
    public  $_validators = [
        'email' => 'required|max:255',
        'full_name' => 'required|max:255',
        'password' => '',
        'type' => 'required',
        'is_active' => 'required',
    ];

    public function __construct()
    {
        $this->model = new User();
        parent::__construct();
    }

    /**
     * Get current user id
     */
    public function getCurrentUserId()
    {
        return $this->successResponse([
            'id' => session('user_id') ? session('user_id'): 0
        ]);
    }

    public function applyRequestFilters()
    {
        $type = Input::get('type');
        $types = explode(',', $type);
        if ($type && !empty($type)) {
            $this->query->whereIn('type', $types);
        }
    }

}
