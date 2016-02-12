<?php

namespace App\Http\Controllers\Api;

use App\Models\PrescriptionInsuranceCard;
use App\Models\PrescriptionItem;
use App\Models\PrescriptionItemLabelCode;
use App\Models\TransactionRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Prescription;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class PrescriptionController extends ApiController
{
    public  $_validators = [
        'patient_id' => 'required',
        'pharmacist_user_id' => 'required',
        'prescriber_id' => 'required'
    ];

    public $query_with = ['patient', 'pharmacist_user', 'prescriber', 'prescription_items', 'insurance_cards'];

    public $user_field = 'creator_user_id';

    public function __construct()
    {
        $this->model = new Prescription();
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        parent::store($request);

        if ($this->is_success_request === true) {

            //add prescription items with labels
            $inputData = $request->all();

            if (isset($inputData['prescription_items'])) {
                foreach ($inputData['prescription_items'] as $prescriptionItemData) {
                    $prescriptionItemData['prescription_id'] = $this->item->id;
                    if ($prescriptionItem = PrescriptionItem::create($prescriptionItemData)) {
                        if (!empty($prescriptionItemData['label_codes'])) {
                            $labelCodes = [];
                            foreach ($prescriptionItemData['label_codes'] as $labelCodeData) {
                                $labelCodes[] = new PrescriptionItemLabelCode(['label_code_id' => $labelCodeData['id']]);
                            }
                            $prescriptionItem->label_codes()->saveMany($labelCodes);
                        }
                    }
                }
            }
            //add cards
            if (isset($inputData['insurance_cards'])) {
                $prescriptionInsuranceCards = [];
                foreach ($inputData['insurance_cards'] as $insuranceCardItemData) {
                    $prescriptionInsuranceCards[] = new PrescriptionInsuranceCard(['insurance_card_id' => $insuranceCardItemData['id']]);
                }
                $this->item->insurance_cards()->saveMany($prescriptionInsuranceCards);
            }

            //reload relation
            $this->item->load('prescription_items');
            $this->item->load('insurance_cards');
        }
        return $this->successResponse($this->getSuccessStoreResponseData());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $parentResponse = parent::update($request, $id);

        if ($this->is_success_request === true) {
            //update labels
            $inputData = $request->all();
            if (isset($inputData['prescription_items'])) {
                $oldPrescriptionItems = $this->item->prescription_items;
                $updatedIds = [];
                foreach ($inputData['prescription_items'] as $prescriptionItemData) {
                    $prescriptionItemData['prescription_id'] = $this->item->id;
                    //new item
                    if (isset($prescriptionItemData['id'])) {
                        $updatedIds[] = $prescriptionItemData['id'];
                        $prescriptionItem = PrescriptionItem::where('id', $prescriptionItemData['id'])->first();
                        $prescriptionItem->update($prescriptionItemData);
                    }
                    //exist item
                    else {
                        $prescriptionItem = PrescriptionItem::create($prescriptionItemData);
                    }
                    if ($prescriptionItem) {
                        PrescriptionItemLabelCode::where('prescription_item_id', $prescriptionItem->id)->delete();
                        if (!empty($prescriptionItemData['label_codes'])) {
                            $labelCodes = [];
                            foreach ($prescriptionItemData['label_codes'] as $labelCodeData) {
                                $labelCodes[] = new PrescriptionItemLabelCode(['label_code_id' => $labelCodeData['id']]);
                            }
                            $prescriptionItem->label_codes()->saveMany($labelCodes);
                        }
                    }
                }
            }
            //remove other prescription items
            foreach ($oldPrescriptionItems as $oldPrescriptionItem) {
                $check = false;
                foreach ($updatedIds as $updatedId) {
                    if ($oldPrescriptionItem['id'] == $updatedId) {
                        $check = true;
                    }
                }
                if (!$check) {
                    PrescriptionItem::where('id', $oldPrescriptionItem['id'])->delete();
                }
            }

            //update insurance cards
            //remove old items
            PrescriptionInsuranceCard::where('prescription_id', $this->item->id)->delete();
            //add new
            if (isset($inputData['insurance_cards'])) {
                $prescriptionInsuranceCards = [];
                foreach ($inputData['insurance_cards'] as $insuranceCardItemData) {
                    $prescriptionInsuranceCards[] = new PrescriptionInsuranceCard(['insurance_card_id' => $insuranceCardItemData['id']]);
                }
                $this->item->insurance_cards()->saveMany($prescriptionInsuranceCards);
            }

            //reload relation
            $this->item->load('prescription_items');
            $this->item->load('insurance_cards');
        }

        return $parentResponse;
    }

    public function applyRequestFilters()
    {
        $patient_name = Input::get('patient_name');
        if ($patient_name && !empty($patient_name)) {
            $this->query->whereHas('patient', function ($query) {
                $query->where('name', 'like', '%'.Input::get('patient_name').'%');
            });
        }

        $status = Input::get('status');
        if ($status && !empty($status)) {
            if (strpos($status, '-') === 0) {
                $status = mb_substr($status, 1);
                $this->query->where('status', '<>', $status);
            } else {
                $this->query->where('status', $status);
            }
        }
    }

    public function postReverseTransactions()
    {
        $id = Input::get('prescriptionId');
        if ($id) {
            if ($prescription = Prescription::where('id', $id)->first()) {
                $results = [];
                foreach ($prescription->prescription_items as $prescription_item) {
                    foreach ($prescription_item->prescription_item_requests as $prescription_item_request) {
                        //reverse only success-adjudicate requests
                        if ($prescription_item_request->status == TransactionRequest::STATUS_SUCCESS && $prescription_item_request->type == TransactionRequest::TYPE_ADJUDICATE) {
                            $results[] = $this->transaction_service->reversalRequest($prescription_item_request->id);
                        }
                    }
                }
                //check if success
                $errors = [];
                foreach ($results as $result) {
                    if ($result['status'] == TransactionRequest::STATUS_ERROR) {
                        foreach ($result['errors'] as $error) {
                            $errors[] = $error['error'];
                        }
                    }
                }
                if (count($errors) == 0) {
                    return $this->successResponse(['message' => 'Successfully reversed!']);
                } else {
                    return $this->errorResponse(['errors' => $errors]);
                }
            }
        }
        return $this->errorResponse(['errors' => 'Item not found']);
    }

}
