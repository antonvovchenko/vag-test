<?php

namespace App\Models;

use App\Library\TransactionService;

class TransactionRequest extends BaseModel
{
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';
    const STATUS_DUPLICATE = 'duplicate';
    const STATUS_REVERSED = 'reversed';

    const TYPE_ADJUDICATE = 'adjudicate';
    const TYPE_REVERSAL = 'reversal';

    public $prescription_item;
    public $prescription;
    public $patient;

    protected $auditEnabled = false;

    public $transaction_service;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transaction_request';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request',
        'response',
        'socket',
        'ip',
        'port',
        'prescription_item_id',
        'status',
        'errors',
        'type',
        'adjudicate_request_id',
        'insurance_card_id',
        'actual',
        'fee',
        'tax',
        'paid',
        'is_payment_confirmed',
        'reference_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->transaction_service = TransactionService::getInstance();
    }

    /**
     * Get the card.
     */
    public function insurance_card()
    {
        return $this->belongsTo('App\Models\InsuranceCard', 'insurance_card_id');
    }

    /**
     * Get prescription item data.
     */
    public function getPatientItemData()
    {
        $patientData = '';
        if ($this->getPatient()) {
            $patientData = $this->patient->getItemData();
        }
        return $patientData;
    }

    /**
     * Get the card.
     */
    public function adjudicate_request()
    {
        return $this->belongsTo('App\Models\TransactionRequest', 'adjudicate_request_id');
    }

    public function getItemLabel() {
        return 'Transaction Request';
    }

    public function get_request_parsed()
    {
        if ($this->type == self::TYPE_ADJUDICATE) {
            return $this->transaction_service->parseAdjudicateRequest($this->request);
        } else if ($this->type == self::TYPE_REVERSAL) {
            return $this->transaction_service->parseReversalRequest($this->request);
        }
    }

    public function getPrescriptionItem ()
    {
        if ($this->prescription_item === null) {
            $this->prescription_item = PrescriptionItem::where('id', $this->prescription_item_id)->first();
        }
        return $this->prescription_item;
    }

    public function getPrescription()
    {
        if ($this->prescription === null && $this->getPrescriptionItem()) {
            $this->prescription = Prescription::where('id', $this->prescription_item->prescription_id)->first();
        }
        return $this->prescription;
    }

    public function getPatient()
    {
        if ($this->patient === null && $this->getPrescription()) {
            $this->patient = Patient::where('id', $this->prescription->patient_id)->first();
        }
        return $this->patient;
    }

    public function getTotal()
    {
        if ($this->getPrescriptionItem()) {
            return $this->prescription_item->total;
        }
        return '';
    }

    public function getInsurancePaid()
    {
        if ($this->getPrescriptionItem()) {
            return $this->prescription_item->insurance;
        }
        return '';
    }

    public function get_response_parsed()
    {
        return $this->transaction_service->parseResponse($this->response, $this->type);
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'request' => str_replace(' ', '&nbsp;', $this->request),
            'request_parsed' => $this->get_request_parsed(),
            'response' => str_replace(' ', '&nbsp;', $this->response),
            'response_parsed' => $this->get_response_parsed(),
            'socket' => $this->socket,
            'ip' => $this->ip,
            'port' => $this->port,
            'status' => $this->status,
            'errors' => json_decode($this->errors, true),
            'insurance_card' => $this->insurance_card ? $this->insurance_card->getItemData() : '',
            'type' => $this->type,
            'adjudicate_request_id' => $this->adjudicate_request_id,
            'adjudicate_request' => $this->adjudicate_request ? $this->adjudicate_request->getItemData() : '',
            'prescription_item_id' => $this->prescription_item_id,
            'patient' => $this->getPatientItemData(),
            'total' => $this->getTotal(),
            'insurance_paid' => $this->getInsurancePaid(),
            'actual' => $this->actual,
            'fee' => $this->fee,
            'tax' => $this->tax,
            'paid' => $this->paid,
            'is_payment_confirmed' => $this->is_payment_confirmed,
            'reference_id' => $this->reference_id,
            'created_at_short' => $this->created_at->format('m/d/Y'),
            'created_at' => $this->created_at->format('m/d/Y g:i a')
        ];
    }

    public function getErrors()
    {

    }
}
