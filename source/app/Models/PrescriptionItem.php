<?php

namespace App\Models;

class PrescriptionItem extends BaseModel
{
    public $defaults = array(
        'is_readable' => true,
    );

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prescription_item';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prescription_id',
        'product_id',
        'qty',
        'course_days',
        'refill_allowed_times',
        'fee_id',
        'total',
        'insurance',
        'is_readable'
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    /**
     * Get the label codes.
     */
    public function label_codes()
    {
        return $this->hasMany('App\Models\PrescriptionItemLabelCode', 'prescription_item_id');
    }

    /**
     * Get the label codes data.
     */
    public function label_codes_get_data()
    {
        $label_codes = [];
        foreach ($this->label_codes as $item) {
            $label_codes[] = $item->label_code->getItemData();
        }
        return $label_codes;
    }

    /**
     * Get the fee.
     */
    public function fee()
    {
        return $this->belongsTo('App\Models\Fee', 'fee_id');
    }


    /**
     * Get the prescription items requests.
     */
    public function prescription_item_requests()
    {
        return $this->hasMany('App\Models\TransactionRequest', 'prescription_item_id');
    }

    public function get_prescription_item_requests_data()
    {
        $prescription_item_requests_data = [];
        foreach ($this->prescription_item_requests as $item) {
            $prescription_item_requests_data[] = $item->getItemData();
        }
        return $prescription_item_requests_data;
    }

    /**
     * Get the prescription item taxes.
     */
    public function get_tax($id = false)
    {
        $taxes = 0;
        if ($id) {
            $item = $this->newQuery()->where('id', $id)->first();
        } else {
            $item = $this;
        }
        if ($item && isset($item->product) && $item->product  && isset($item->product->tax) && $item->product->tax) {
            $taxes = ($item->product->unit_price * $item->product->tax->value / 100)  * $item->qty;
        }
        return round($taxes, 2);
    }

    public function getItemLabel() {
        return 'Prescription Item';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'prescription_id' => $this->prescription_id,
            'product_id' => $this->product_id,
            'product' => $this->product ? $this->product->getItemData() : '',
            'qty' => $this->qty,
            'course_days' => $this->course_days,
            'refill_allowed_times' => $this->refill_allowed_times,
            'label_codes' => $this->label_codes_get_data(),
            'fee_id' => $this->fee_id,
            'fee' => $this->fee ? $this->fee->getItemData() : '',
            'prescription_item_requests' => $this->get_prescription_item_requests_data(),
            'total' => $this->total,
            'insurance' => $this->insurance,
            'is_readable' => $this->is_readable,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }
}
