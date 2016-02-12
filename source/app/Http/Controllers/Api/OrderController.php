<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderItem;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class OrderController extends ApiController
{
    public  $_validators = [

    ];

    public $query_with = ['order_items', 'customer'];

    public function __construct()
    {
        $this->model = new Order();
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        parent::store($request);

        if ($this->is_success_request === true) {

            //add order items with labels
            $inputData = $request->all();

            if (isset($inputData['order_items'])) {
                foreach ($inputData['order_items'] as $orderItemData) {
                    $orderItemData['order_id'] = $this->item->id;
                    OrderItem::create($orderItemData);
                }
            }
            //reload relation
            $this->item->load('order_items');

            //update prescriptions status
            $this->updateCompletedOrderItems($this->item);
        }
        return $this->successResponse($this->getSuccessStoreResponseData());
    }

    /**
     * Update resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $parentResponse = parent::update($request, $id);

        if ($this->is_success_request === true) {
            //remove old items
            OrderItem::where('order_id', $this->item->id)->delete();
            //add items
            $inputData = $request->all();
            if (isset($inputData['order_items'])) {
                foreach ($inputData['order_items'] as $orderItemData) {
                    $orderItemData['order_id'] = $this->item->id;
                    OrderItem::create($orderItemData);
                }
            }
            //reload relation
            $this->item->load('order_items');

            //update prescriptions status
            $this->updateCompletedOrderItems($this->item);
        }

        return $parentResponse;
    }

    /**
     * Get paid types
     *
     */
    public function getPaidTypes()
    {
        return $this->successResponse(Order::$paidTypes);
    }

    /**
     * Update order's prescriptions, products and inventory
     *
     */
    public function updateCompletedOrderItems(Order $order)
    {
        //complete order
        if ($order->status == Order::STATUS_COMPLETED && $order->type == Order::TYPE_SELL && $order->order_items) {
            $order->updateInventory();
            foreach ($order->order_items as $order_item) {
                if ($order_item->prescription) {
                    $order_item->prescription->update(['status' => Prescription::STATUS_PAID]);
                }
            }
        }
        //refund order
        if ($order->status == Order::STATUS_COMPLETED && $order->type == Order::TYPE_REFUND && $order->order_items) {
            $order->updateInventory();
        }
    }

    public function applyRequestFilters()
    {
        $refunded_order_id = Input::get('refunded_order_id');

        if ($refunded_order_id && !empty($refunded_order_id)) {
            $this->query->where('refunded_order_id', $refunded_order_id);
        }
    }
}
