<?php

namespace App\Models;

class ProductMarkupType extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_markup_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'default_markup'
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'markup_type_id');
    }

    public function getProductsCount()
    {
        if ( ! array_key_exists('products', $this->relations))
            $this->load('products');

        $related = $this->getRelation('products');

        return ($related) ? (int) count($related) : 0;
    }

    public function getItemLabel() {
        return 'Category of product';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'default_markup' => $this->default_markup,
            'products_count' => $this->getProductsCount(),
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }
}
