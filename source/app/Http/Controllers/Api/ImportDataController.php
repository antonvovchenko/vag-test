<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Library\ImportDataService;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class ImportDataController extends ApiController
{

    public function __construct()
    {
        $this->model = new User();
        parent::__construct();
    }

    /**
     *
     */
    public function postUpload()
    {
        $data = file_get_contents($_FILES['file']['tmp_name']);
        if (!$data) {
            return $this->errorResponse([
                'error' => 'No data found in file. Please check requirements.'
            ]);
        }
        $importService = new ImportDataService();
        return $this->successResponse($importService->parseDataFromCSV($_FILES['file']['tmp_name']));
    }

    /**
     *
     */
    public function postCheck()
    {
        $type = Input::get('type');
        $data = Input::get('data');
        $matches = Input::get('matches');

        $importService = new ImportDataService();
        return $this->successResponse([
            'message' => 'Import finished successfully.',
            'data' => $importService->checkData($type, $data, $matches)
        ]);
    }

    /**
     *
     */
    public function postImport()
    {
        $type = Input::get('type');
        $data = Input::get('data');

        $importService = new ImportDataService();

        $importService->importData($type, $data);

        return $this->successResponse([
            'message' => 'Import finished successfully.'
        ]);
    }
}
