<?php

namespace App\Models;

use App\Library\CommonService;
use Illuminate\Support\Facades\Auth;

class Order extends BaseModel
{
    CONST STATUS_IN_PROCESS = 'in_process';
    CONST STATUS_COMPLETED = 'completed';

    CONST TYPE_SELL = 'sell';
    CONST TYPE_REFUND = 'refund';
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'total',
        'taxes',
        'discount',
        'net_due',
        'tendered',
        'status',
        'customer_id',
        'type',
        'refunded_order_id',
        'paid_type',
        'multi_payment_cash',
        'multi_payment_cheque',
        'multi_payment_credit_card',
        'multi_payment_other',
        'user_id'
    ];

    static public $paidTypes = [
        'cash' => [
            'title' => 'Cash',
            'is_enabled' => true
        ],
        'cheque' => [
            'title' => 'Cheque',
            'is_enabled' => true
        ],
        'charge' => [
            'title' => 'Charge',
            'is_enabled' => true
        ],
        'credit_card' => [
            'title' => 'Credit Card',
            'is_enabled' => true
        ],
        'multi_payment' => [
            'title' => 'Multi payment',
            'is_enabled' => true
        ],
        'other' => [
            'title' => 'Other',
            'is_enabled' => true
        ]
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

        static::created(function ($item) {
            $item->user_id = session('user_id');
            self::afterSave($item);
        });

        static::updated(function ($item) {
            self::afterSave($item);
        });

        static::saving(function ($item) {
            return self::beforeSave($item);
        });
    }

    /**
     * Function to do smth before save
     */
    static public function beforeSave($item)
    {
        //set status completed if get valid tendered
        $tendered = CommonService::convertNumber($item->tendered);
        if ($tendered && !empty($tendered) && $tendered >= CommonService::convertNumber($item->net_due) && $item->status != self::STATUS_COMPLETED) {
            $item->status = self::STATUS_COMPLETED;
        }
        return true;
    }

    /**
     * Function to do smth after save
     */
    static public function afterSave($item) {
        //update selling price
        if (empty($item->label)) {
            self::where('id', $item->id)->first()->update(['label' => 'Order#'.$item->id]);
        }
    }

    /**
     * Get the order items.
     */
    public function order_items()
    {
        return $this->hasMany('App\Models\OrderItem', 'order_id');
    }

    /**
     * Get the customer.
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Get the order items data.
     */
    public function order_items_get_data()
    {
        $order_items = [];
        foreach ($this->order_items as $item) {
            $order_items[] = $item->getItemData();
        }
        return $order_items;
    }

    public function getItemLabel() {
        return 'Order';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'total' => $this->total,
            'taxes' => $this->taxes,
            'discount' => $this->discount,
            'net_due' => $this->net_due,
            'tendered' => $this->tendered,
            'status' => $this->status,
            'order_items' => $this->order_items_get_data(),
            'customer_id' => $this->customer_id,
            'customer' => $this->customer ? $this->customer->getItemData() : '',
            'type' => $this->type,
            'refunded_order_id' => $this->refunded_order_id,
            'paid_type' => $this->paid_type,
            'multi_payment_cash' => $this->multi_payment_cash,
            'multi_payment_cheque' => $this->multi_payment_cheque,
            'multi_payment_credit_card' => $this->multi_payment_credit_card,
            'multi_payment_other' => $this->multi_payment_other,
            'user_id' => $this->user_id,
            'user' => $this->user ? $this->user->getItemData() : '',
            'created_at' => $this->created_at->format('m/d/Y g:i a')
        ];
    }

    public function updateInventory()
    {
        if ($this->order_items) {
            foreach ($this->order_items as $order_item) {
                if ($order_item->prescription) {
                    //create sell inventory for each product in inventory
                    if ($order_item->prescription->prescription_items) {
                        foreach ($order_item->prescription->prescription_items as $prescription_item) {
                            if ($prescription_item->product) {
                                $this->addSellInventory($this->id, $prescription_item->product->id, $prescription_item->qty);
                            }
                        }
                    }
                } elseif ($order_item->product) {
                    if ($this->type == self::TYPE_SELL) {
                        //create sell inventory
                        $this->addSellInventory($this->id, $order_item->product->id, $order_item->qty);
                    } else if ($this->type == self::TYPE_REFUND) {
                        $this->addRefundInventory($this, $order_item->product->id, $order_item->qty);
                    }
                }
            }
        }
    }

    public function addSellInventory($order_id, $product_id, $qty)
    {
        $batches = Inventory::getProductBatches($product_id);

        //create sell inventory item(s)
        $qty_processed = $qty;
        foreach ($batches as $batch) {

            if (!$qty_processed) {
                break;
            } else if ($batch['quantity_on'] > 0) {
                //check if we need to process another batch
                if ($batch['quantity_on'] >= $qty) {
                    $quantity_out = $qty;
                    $qty_processed = 0;
                } else {
                    $quantity_out = $batch['quantity_on'];
                    $qty_processed = $qty - $batch['quantity_on'];
                }
                //add
                $inventory = new Inventory([
                    'product_id' => $product_id,
                    'user_id' => session('user_id'),
                    'quantity_on' => $quantity_out*-1,
                    'type' => Inventory::TYPE_SELL,
                    'order_id' => $order_id,
                    'batch_id' => $batch['batch_id']
                ]);
                $inventory->save();
            }
        }

    }

    public function addRefundInventory($order, $product_id, $qty)
    {
        //get batch_id
        $inventory = Inventory::where('order_id', $order->refunded_order_id)->where('type', self::TYPE_SELL)->first();

        //add
        $inventory = new Inventory([
            'product_id' => $product_id,
            'user_id' => session('user_id'),
            'quantity_on' => $qty,
            'type' => Inventory::TYPE_REFUND,
            'order_id' => $order->id,
            'batch_id' => $inventory ? $inventory->batch_id : null
        ]);
        $inventory->save();
    }

}
