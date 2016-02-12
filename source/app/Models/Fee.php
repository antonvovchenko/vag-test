<?php

namespace App\Models;


class Fee extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_type',
        'description',
        'fees',
        'sort'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function getItemLabel() {
        return 'Fee';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'card_type' => $this->card_type,
            'description' => $this->description,
            'fees' => $this->fees,
            'sort' => $this->sort,
            'created_at' => $this->created_at->format('m/d/Y g:i a')
        ];
    }
}
