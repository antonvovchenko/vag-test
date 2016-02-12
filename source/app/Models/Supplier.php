<?php

namespace App\Models;

class Supplier extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'supplier';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'postal_code',
        'eorder_number',
        'last_date',
        'lead_time',
        'contact',
        'phone',
        'fax',
        'email'
    ];

    public function getItemLabel() {
        return 'Supplier';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'eorder_number' => $this->eorder_number,
            'last_date' => $this->last_date,
            'lead_time' => $this->lead_time,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }

}
