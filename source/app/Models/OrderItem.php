<?php

namespace App\Models;


class OrderItem extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_item';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'prescription_id',
        'product_id',
        'qty'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    /**
     * Get the prescription.
     */
    public function prescription()
    {
        return $this->belongsTo('App\Models\Prescription', 'prescription_id');
    }

    public function getItemLabel() {
        return 'Order Item';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'prescription_id' => $this->prescription_id,
            'prescription' => $this->prescription ? $this->prescription->getItemData() : '',
            'product_id' => $this->product_id,
            'product' => $this->product ? $this->product->getItemData() : '',
            'qty' => $this->qty,
            'created_at' => $this->created_at->format('m/d/Y'),
            'created_at_full' => $this->created_at->format('m/d/Y H:i:s a')
        ];
    }
}
