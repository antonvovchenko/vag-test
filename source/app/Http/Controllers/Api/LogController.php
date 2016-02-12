<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Log;
use App\Http\Requests;

class LogController extends ApiController
{
    public function __construct()
    {
        $this->model = new Log();
        parent::__construct();
    }

    public function destroy($id)
    {
        //
    }
}
