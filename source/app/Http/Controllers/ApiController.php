<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Library\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public $model = null;
    public $query = null;
    public $query_with = [];
    public $user_field = null;

    public $is_success_request = null;
    public $item = null;
    public $_validators = null;

    public $transaction_service;

    public function __construct()
    {
        if (Config::get('pharmacy.is_checking_auth_tokens_enabled')) {
            $this->middleware('jwt.auth');
        }
        $this->query = $this->getNewQuery();
        if (!empty($this->query_with)) {
            $this->query->with($this->query_with);
        }
        $this->transaction_service = TransactionService::getInstance();
    }

    public function getNewQuery() {
        return $this->model->newQuery();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->applyRequestFilters();

        //get all count
        $total = $this->query->count();

        $this->applyRequestOrder();
        $this->applyRequestLimit();

        $items = $this->query->get();

        $output = [
            'total' => $total,
            'data' => []
        ];

        foreach ($items as $item) {
            $output['data'][] = $item->getItemData();
        }

        return $this->successResponse($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->query->where('id', $id)->first();

        if (!$item) {
            return $this->errorResponse([
                'errors' => 'Item not found'
            ]);
        } else {
            $this->setItem($item);
            return $this->successResponse($item->getItemData());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidators());

        if ($validator->fails()) {
            return $this->errorResponse([
                'errors' => $validator->errors()->all(['field' => ':key', 'message' => ':message'])
            ]);
        }

        $requestData = $request->all();
        $data = [];
        foreach ($requestData as $name=>$value) {
            foreach ($this->model->getFillable() as $fillable) {
                if ($fillable == $name) {
                    $data[$name] = $value;
                }
            }
        }

        if ($this->user_field) {
            $data[$this->user_field] = session('user_id');
        }

        if (!$item = $this->model->create($data)) {
            return $this->errorResponse([
                'errors' => 'Internal Error. No item created'
            ]);
        } else {
            $this->setItem($item);
            return $this->successResponse($this->getSuccessStoreResponseData());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->getValidators($id));

        if ($validator->fails()) {
            return $this->errorResponse([
                'errors' => $validator->errors()->all(['field' => ':key', 'message' => ':message'])
            ]);
        }

        if (!$item = $this->query->where('id', $id)->first()) {
            return $this->errorResponse([
                'errors' => 'Internal Error. Item not found'
            ]);
        }

        $requestData = $request->all();
        $data = [];
        foreach ($requestData as $name=>$value) {
            foreach ($this->model->getFillable() as $fillable) {
                if ($fillable == $name) {
                    $data[$name] = $value;
                }
            }
        }

        $update = $item->update($data);

        if (!$update) {
            return $this->errorResponse([
                'errors' => 'Internal Error. Item not updated'
            ]);
        } else {
            $this->setItem($item);
            return $this->successResponse($this->getSuccessUpdateResponseData());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->model->destroy($id)) {
            return $this->errorResponse([
                'errors' => 'Internal Error. Item not deleted'
            ]);
        } else {
            return $this->successResponse([
                'message' => 'Successfully deleted!'
            ]);
        }
    }

    /**
     * Get success store response data.
     * @return array
     */
    public function getSuccessStoreResponseData()
    {
        return [
            'message' => 'Successfully added!',
            'item' => $this->item->getItemData()
        ];
    }

    /**
     * Get success update response data.
     * @return array
     */
    public function getSuccessUpdateResponseData()
    {
        return [
            'message' => 'Successfully updated!',
            'item' => $this->item->getItemData()
        ];
    }

    /**
     * Returns error json response
     */
    public function errorResponse($data)
    {
        $this->is_success_request = false;
        return response()->json([
            'success' => false,
            'data' => $data
        ], 422);
    }

    /**
     * Returns success json response
     */
    public function successResponse($data)
    {
        $this->is_success_request = true;
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function applyRequestFilters()
    {
        $inputFilterName = Input::get('filter_name');
        $inputFilterValue = Input::get('filter_value');

        //filter for search input
        if ($inputFilterName && !empty($inputFilterName) && $inputFilterValue && !empty($inputFilterValue)) {
            $this->query->where($inputFilterName, 'like', '%'.$inputFilterValue.'%');
        }
    }

    public function applyRequestOrder()
    {
        $order = $this->getInputOrder();

        if ($order && !empty($order)) {
            $this->query->orderBy(trim($order, '-'), $this->getOrderType());
        }
    }

    public function getOrderType()
    {
        $order = $this->getInputOrder();
        $orderType = 'asc';
        if (strpos($order, '-') === 0) {
            $orderType = 'desc';
        }
        return $orderType;
    }

    public function getInputOrder($isClearRelation = false)
    {
        $order = Input::get('order');
        if ($isClearRelation) {
            $order = substr($order, strpos($order, '.')+1);
        }
        return $order;
    }

    public function applyRequestLimit()
    {
        $limit = Input::get('limit');
        $page = Input::get('page');

        if ($limit && !empty($limit)) {
            $this->query->take($limit);
            //offset
            if ($page && !empty($page)) {
                $this->query->skip(($page - 1) * $limit);
            }
        }
    }

    protected function setItem($item) {
        $this->item = $item;
    }

    protected function getValidators($id = false) {
        if ($id) {
            foreach ($this->_validators as $field=>$validator) {
                if (strpos($validator, 'unique') !== false) {
                    $this->_validators[$field] .= ','.$id;
                }
            }
        }
        return $this->_validators;
    }

    /**
     * Get next item id
     *
     */
    public function getNextId()
    {
        $lastUser = $this->query->orderBy('id','desc')->take(1)->get()->first();
        return $this->successResponse([
            'nextId' => $lastUser ? $lastUser->id + 1 : 1
        ]);
    }

}
