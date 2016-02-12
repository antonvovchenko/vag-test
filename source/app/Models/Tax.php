<?php

namespace App\Models;

class Tax extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tax';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'description'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function getItemLabel() {
        return 'Tax';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'description' => $this->description,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }
}
