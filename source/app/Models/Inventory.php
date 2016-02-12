<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Inventory extends BaseModel
{
    CONST TYPE_IN = 'in';
    CONST TYPE_RETURN = 'return';
    CONST TYPE_SHIPMENT = 'shipment';
    CONST TYPE_SELL = 'sell';
    CONST TYPE_REFUND = 'refund';

    public $batch_qoh;
    public $batch_expiry_date;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity_on',
        'reason',
        'supplier_id',
        'expiry_date',
        'type',
        'order_id',
        'batch_id',
        'pharmacy'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Inventory $item) {
            self::afterSave($item);
        });

        static::updated(function (Inventory $item) {
            self::afterSave($item);
        });

        static::creating(function ($item) {
            self::beforeCreate($item);
            return true;
        });
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    /**
     * Get the supplier.
     */
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    /**
     * Get the order.
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    /**
     * Function to update selling price when item is created or updated
     */
    static public function afterSave($item) {
        $sum = self::where('product_id', $item->product_id)->sum('quantity_on');
        Product::where('id', $item->product_id)->first()->update(['qoh' => $sum]);
    }

    /**
     * Function to update smth before create item
     */
    static public function beforeCreate($item) {
        $item->user_id = session('user_id');
        if ($item->type == self::TYPE_IN) {
            $item->batch_id = self::getNextBatchId();
        }
    }

    public function getBatchQOH()
    {
        if ($this->batch_qoh === null) {
            return self::where('batch_id', $this->batch_id)->sum('quantity_on');
        }
        return $this->batch_qoh;
    }

    public function getBatchExpiryDate()
    {
        if ($this->batch_expiry_date === null && $this->batch_id) {
            return self::where('batch_id', $this->batch_id)->where('type', self::TYPE_IN)->first()->expiry_date;
        }
        return $this->batch_expiry_date;
    }

    public function getItemLabel() {
        return 'Inventory';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user ? $this->user->getItemData() : '',
            'product_id' => $this->product_id,
            'product' => $this->product ? $this->product->getItemData() : '',
            'quantity_on' => $this->quantity_on,
            'reason' => $this->reason,
            'supplier_id' => $this->supplier_id,
            'supplier' => $this->supplier ? $this->supplier->getItemData() : '',
            'expiry_date' => $this->expiry_date,
            'qoh' => $this->product ? $this->product->qoh : 0,
            'type' => $this->type,
            'order_id' => $this->order_id,
            'order' => $this->order ? $this->order->getItemData() : '',
            'batch_id' => $this->batch_id,
            'batch_qoh' => $this->getBatchQOH(),
            'batch_expiry_date' => strtotime($this->getBatchExpiryDate()) ? date('m/d/Y', strtotime($this->getBatchExpiryDate())) : '',
            'pharmacy' => $this->pharmacy,
            'created_at' => $this->created_at->format('m/d/Y g:i a')
        ];
    }

    static public function getNextBatchId()
    {
        $last = self::where('type', self::TYPE_IN)->orderBy('batch_id', 'desc')->first();
        if ($last) {
            return $last->batch_id+1;
        } else {
            return 1;
        }
    }

    static public function getProductBatches ($product_id)
    {
        $inventory_items = self::where('product_id', $product_id)->get();
        //get all batches
        $batches = [];
        foreach ($inventory_items as $inventory_item) {
            $batches[$inventory_item->batch_id] = [
                'batch_id' => $inventory_item->batch_id,
                'quantity_on' => isset($batches[$inventory_item->batch_id]) ? $batches[$inventory_item->batch_id]['quantity_on'] + $inventory_item->quantity_on : $inventory_item->quantity_on,
                'expiry_date' => strtotime($inventory_item->getBatchExpiryDate())
            ];
        }
        //sort batches by expiry_date asc
        usort($batches, function($a, $b) {
            if (!$b['expiry_date']) {
                return -1;
            } else if ($a['expiry_date'] > $b['expiry_date']) {
                return 1;
            }
            return 0;
        });
        return $batches;
    }
}
