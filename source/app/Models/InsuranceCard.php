<?php

namespace App\Models;

class InsuranceCard extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'insurance_card';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_data_string',
        'insurance_provider',
        'bin_number',
        'group_number',
        'subscriber_number',
        'first_name',
        'last_name',
        'person_code',
        'relationship_code',
        'sex_code',
        'date_of_birth',
        'effective_date',
        'expiry_date',
        'card_id',
        'version_number'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function getItemLabel() {
        return 'Insurance Card';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'card_data_string' => $this->card_data_string,
            'insurance_provider' => $this->insurance_provider,
            'bin_number' => $this->bin_number,
            'group_number' => $this->group_number,
            'subscriber_number' => $this->subscriber_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'person_code' => $this->person_code,
            'relationship_code' => $this->relationship_code,
            'sex_code' => $this->sex_code,
            'date_of_birth' => $this->date_of_birth,
            'effective_date' => $this->effective_date,
            'expiry_date' => $this->expiry_date,
            'card_id' => $this->card_id,
            'version_number' => $this->version_number,
            'created_at' => $this->created_at->format('m/d/Y g:i a')
        ];
    }
}
