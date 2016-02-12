<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\TransactionRequest;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class TransactionRequestController extends ApiController
{
    public  $_validators = [

    ];

    public function __construct()
    {
        $this->model = new TransactionRequest();
        parent::__construct();
    }

    public function applyRequestFilters()
    {
        $type = Input::get('type');
        $status = Input::get('status');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        $insurance_provider = Input::get('insurance_provider');
        $is_payment_confirmed = Input::get('is_payment_confirmed');

        if ($type !== null && !empty($type)) {
            $this->query->where('type', $type);
        }
        if ($status !== null && !empty($status)) {
            $this->query->where('status', $status);
        }
        if ($from_date !== null && !empty($from_date)) {
            $this->query->where('created_at', '>', $from_date);
        }
        if ($to_date !== null && !empty($to_date)) {
            $this->query->where('created_at', '<', $to_date);
        }
        if ($insurance_provider !== null && !empty($insurance_provider)) {
            $this->query->whereHas('insurance_card', function ($query) {
                $query->where('insurance_provider', Input::get('insurance_provider'));
            });
        }
        if ($is_payment_confirmed !== null) {
            $this->query->where('is_payment_confirmed', $is_payment_confirmed);
        }
    }
}
