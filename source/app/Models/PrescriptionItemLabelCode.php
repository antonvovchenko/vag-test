<?php

namespace App\Models;

class PrescriptionItemLabelCode extends BaseModel
{

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prescription_item_label_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prescription_item_id',
        'label_code_id'
    ];

    /**
     * Get the creator.
     */
    public function label_code()
    {
        return $this->belongsTo('App\Models\LabelCode', 'label_code_id');
    }

    public function getItemLabel() {
        return 'Label code of prescription item';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'prescription_item_id' => $this->prescription_item_id,
            'label_code_id' => $this->label_code_id,
            'label_code' => $this->label_code ? $this->label_code->getItemData() : ''
        ];
    }
}
