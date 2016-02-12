<?php

namespace App\Models;

class Prescription extends BaseModel
{
    CONST STATUS_IN_PROCESS = 'in_process';
    CONST STATUS_COMPLETED = 'completed';
    CONST STATUS_PAID = 'paid';
    CONST STATUS_VOID = 'void';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prescription';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'pharmacist_user_id',
        'creator_user_id',
        'prescriber_id',
        'total',
        'net_due',
        'insurance',
        'discount_percent',
        'discount_flat',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        //todo: move this functional to base model->beforeSave
        static::creating(function ($item) {
            self::beforeSave($item);
            return true;
        });
        static::updating(function ($item) {
            self::beforeSave($item);
            return true;
        });
    }

    /**
     * Function to do smth before save
     */
    static public function beforeSave($item)
    {
        //calculate prices, discounts etc

        $net_due = $item->total;
        //decrement insurance
        $insurance = $item->get_insurance();
        if ($insurance > 0) {
            $net_due -= $insurance;
        }
        //decrement discount
        if ($item->discount_flat > 0) {
            $net_due -= $item->discount_flat;
        }
        $item->net_due = $net_due;
    }

    /**
     * Scope a query to only include completed items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', 1);
    }

    /**
     * Get the patient.
     */
    public function patient()
    {
        return $this->belongsTo('App\Models\Patient', 'patient_id');
    }

    /**
     * Get the patient.
     */
    public function pharmacist_user()
    {
        return $this->belongsTo('App\Models\User', 'pharmacist_user_id');
    }

    /**
     * Get the creator.
     */
    public function creator_user()
    {
        return $this->belongsTo('App\Models\User', 'creator_user_id');
    }

    /**
     * Get the patient.
     */
    public function prescriber()
    {
        return $this->belongsTo('App\Models\Prescriber', 'prescriber_id');
    }

    /**
     * Get the prescription items.
     */
    public function prescription_items()
    {
        return $this->hasMany('App\Models\PrescriptionItem', 'prescription_id');
    }

    /**
     * Get the insurance cards.
     */
    public function insurance_cards()
    {
        return $this->hasMany('App\Models\PrescriptionInsuranceCard', 'prescription_id');
    }

    /**
     * Get the insurance cards data.
     */
    public function insurance_cards_get_data()
    {
        $insurance_cards = [];
        foreach ($this->insurance_cards as $item) {
            if ($item->insurance_card) {
                $insurance_cards[] = $item->insurance_card->getItemData();
            }
        }
        return $insurance_cards;
    }

    /**
     * Get the prescription tax.
     */
    public function get_tax()
    {
        $tax = 0;
        foreach ($this->prescription_items as $item) {
            $tax += $item->get_tax();
        }
        return round($tax, 2);
    }

    /**
     * Get the insurance paids.
     */
    public function get_insurance()
    {
        $insurance = 0;
        foreach ($this->prescription_items as $item) {
            $insurance += $item->insurance;
        }
        return round($insurance, 2);
    }

    /**
     * Get the prescription items data.
     */
    public function prescription_items_get_data()
    {
        $prescription_items = [];
        foreach ($this->prescription_items as $item) {
            $prescription_items[] = $item->getItemData();
        }
        return $prescription_items;
    }

    public function getItemLabel() {
        return 'Prescription';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'patient' => $this->patient ? $this->patient->getItemData() : '',
            'pharmacist_user_id' => $this->pharmacist_user_id,
            'pharmacist_user' => $this->pharmacist_user ? $this->pharmacist_user->getItemData() : '',
            'creator_user_id' => $this->creator_user_id,
            'creator_user' => $this->creator_user ? $this->creator_user->getItemData() : '',
            'prescriber_id' => $this->prescriber_id,
            'prescriber' => $this->prescriber ? $this->prescriber->getItemData() : '',
            'prescription_items' => $this->prescription_items_get_data(),
            'insurance_cards' => $this->insurance_cards_get_data(),
            'tax' => $this->get_tax(),
            'total' => $this->total,
            'net_due' => $this->net_due,
            'insurance' => $this->get_insurance(),
            'discount_percent' => $this->discount_percent,
            'discount_flat' => $this->discount_flat,
            'status' => $this->status,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }
}
