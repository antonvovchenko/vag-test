<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests;
use App\Models\Product;
use App\Models\Tax;

class TaxController extends ApiController
{
    public  $_validators = [
        'value' => 'required|max:225|unique:tax,value'
    ];

    public function __construct()
    {
        $this->model = new Tax();
        parent::__construct();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //check products in DB which have this tax
        $count = Product::where('tax_id', $id)->count();

        if ($count > 0) {
            return $this->errorResponse([
                'errors' => $count.' products have this tax option. You can\'t remove this item now.'
            ]);
        }

        return parent::destroy($id);
    }
}
