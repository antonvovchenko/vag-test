<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionInsuranceCard extends Model
{

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prescription_insurance_card';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prescription_id',
        'insurance_card_id'
    ];

    /**
     * Get the fee.
     */
    public function prescription()
    {
        return $this->belongsTo('App\Models\Prescription', 'prescription_id');
    }

    /**
     * Get the fee.
     */
    public function insurance_card()
    {
        return $this->belongsTo('App\Models\InsuranceCard', 'insurance_card_id');
    }

    public function getItemLabel() {
        return 'Prescription Insurance Card';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'prescription_id' => $this->prescription_id,
            'product' => $this->prescription ? $this->prescription->getItemData() : '',
            'insurance_card_id' => $this->insurance_card_id,
            'insurance_card' => $this->insurance_card ? $this->insurance_card->getItemData() : '',
        ];
    }
}
