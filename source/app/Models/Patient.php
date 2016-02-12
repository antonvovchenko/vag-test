<?php

namespace App\Models;

class Patient extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'patient';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'email',
        'phone'
    ];

    public function getItemLabel() {
        return 'Patient';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }
}
