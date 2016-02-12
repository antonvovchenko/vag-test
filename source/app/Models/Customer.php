<?php

namespace App\Models;


class Customer extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'credit_limit',
        'balance',
        'contact',
        'address',
        'city',
        'phone',
        'email'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function getItemLabel()
    {
        return 'Customer';
    }

    public function getBalance()
    {
        $balance = $this->balance;
        $completedOrders = Order::where('customer_id', $this->id)->where('status', Order::STATUS_COMPLETED)->get();
        if (!empty($completedOrders)) {
            foreach ($completedOrders as $completedOrder) {
                $balance += $completedOrder->net_due;
            }
        }
        return $balance;
    }

    public function getAvailable()
    {
        return $this->credit_limit - $this->getBalance();
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'credit_limit' => $this->credit_limit,
            'balance' => $this->getBalance(),
            'available' => $this->getAvailable(),
            'contact' => $this->contact,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => $this->created_at->format('m/d/Y g:i a')
        ];
    }
}
