<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends BaseModel
{
    protected $auditEnabled  = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    static public function getStoredLicense()
    {
        return Settings::where('name', 'license')->first();
    }

    public function getItemLabel() {
        return 'Settings';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'valueParsed' => json_decode($this->value, true),
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }

    static public function getValue($name)
    {
        if ($item = self::where('name', $name)->first()) {
            return $item->value;
        }
        return '';
    }
}
