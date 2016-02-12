<?php

namespace App\Http\Controllers\Api;

use App\Library\TransactionService;
use App\Models\InsuranceCard;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class TransactionController extends ApiController
{

    public function __construct()
    {
        if (Config::get('pharmacy.is_checking_auth_tokens_enabled')) {
            $this->middleware('jwt.auth');
        }
        $this->transaction_service = TransactionService::getInstance();
    }

    public function postAdjudicate()
    {
        return $this->successResponse($this->transaction_service->adjudicateRequest(Input::get('prescription')));
    }

    public function postReversal()
    {
        return $this->successResponse($this->transaction_service->reversalRequest(Input::get('transactionId')));
    }
    public function postParseCardInfo()
    {
        $responseData = [
            'cardInformationData' => false,
            'patient' => false
        ];

        //parse card info
        if ($cardInformationData = $this->transaction_service->parseCardInformation(Input::get('cardInformation'))) {

            //save
            if (!$card = InsuranceCard::where('card_data_string', $cardInformationData['card_data_string'])->first()) {
                $card = InsuranceCard::create($cardInformationData);
            }

            if ($card) {
                $responseData['cardInformationData'] = $card->getAttributes();
                //try to find patient with same name
                if ($patient = Patient::where('name', 'like', '%'.$card['first_name'].' '.$card['last_name'].'%')->first()) {
                    $responseData['patient'] = $patient->getItemData();
                }
            }
        }

        return $this->successResponse($responseData);
    }

    public function getInsuranceProviders()
    {
        return $this->successResponse(TransactionService::$insurance_providers);
    }
}
