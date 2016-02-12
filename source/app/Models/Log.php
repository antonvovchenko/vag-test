<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends BaseModel
{
    protected $auditEnabled  = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'owner_type',
        'owner_id',
        'old_value',
        'new_value',
        'type'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getItemLabel() {
        return 'Log';
    }

    public function getOwner() {
        $ownerData = '';
        $model = new $this->owner_type();
        if ($owner = $model->where('id', $this->owner_id)->first()) {
            $ownerData = $owner->getItemData() + ['item_label' => $owner->getItemLabel()];
        }
        return $ownerData;
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user ? $this->user->getItemData() : '',
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'owner' => $this->getOwner(),
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'type' => $this->type,
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }

}
