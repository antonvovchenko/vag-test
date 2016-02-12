<?php

namespace App\Models;

class Prescriber extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prescriber';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'registration_number',
        'notes'
    ];

    public function getItemLabel() {
        return 'Doctor';
    }

    public function getFullName() {
        return $this->first_name.', '.$this->last_name;
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->first_name.', '.$this->last_name. ' ('.$this->registration_number.')',
            'registration_number' => $this->registration_number,
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }
}
