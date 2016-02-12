<?php

namespace App\Models;

class Product extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        //todo: move this functional to base model->beforeSave
        static::creating(function (Product $item) {
            self::beforeSave($item);
            return true;
        });
        static::updating(function (Product $item) {
            self::beforeSave($item);
            return true;
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'is_prescription',
        'is_generic',
        'generic_product_id',
        'barcode',
        'ndc',
        'pkg_size',
        'markup',
        'tax_id',
        'unit_cost',
        'description',
        'qoh',
        'markup_type_id',
        'min_qty',
        'max_qty',
        'is_active',
        'location',
        'lead_time',
        'pkg_price',
        'pkg_cost',
        'unit_price'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the generic.
     */
    public function generic()
    {
        return $this->belongsTo('App\Models\Product', 'generic_product_id');
    }

    /**
     * Get the markup type.
     */
    public function markup_type()
    {
        return $this->belongsTo('App\Models\ProductMarkupType', 'markup_type_id');
    }

    /**
     * Get the label codes.
     */
    public function label_codes()
    {
        return $this->hasMany('App\Models\ProductLabelCode', 'product_id');
    }

    /**
     * Get the inventory items.
     */
    public function inventory_items()
    {
        return $this->hasMany('App\Models\Inventory', 'product_id');
    }

    /**
     * Get the label codes data.
     */
    public function label_codes_get_data()
    {
        $label_codes = [];
        foreach ($this->label_codes as $item) {
            $label_codes[] = $item->label_code->getItemData();
        }
        return $label_codes;
    }

    /**
     * Get the tax.
     */
    public function tax()
    {
        return $this->belongsTo('App\Models\Tax', 'tax_id');
    }

    /**
     * Function to update some fields before save
     */
    static public function beforeSave($item) {

    }

    public function getItemLabel() {
        return 'Product';
    }

    public function getItemData() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_prescription' => $this->is_prescription,
            'is_generic' => $this->is_generic,
            'generic_product_id' => $this->generic_product_id,
            'generic' => $this->generic ? $this->generic->getItemData() : '',
            'barcode' => $this->barcode,
            'ndc' => $this->ndc,
            'pkg_size' => $this->pkg_size,
            'markup' => $this->markup,
            'tax_id' => $this->tax_id,
            'tax' => $this->tax ? $this->tax->getItemData() : '',
            'unit_cost' => $this->unit_cost,
            'description' => $this->description,
            'qoh' => $this->qoh,
            'markup_type_id' => $this->markup_type_id,
            'markup_type' => $this->markup_type ? $this->markup_type->getItemData() : '',
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'is_active' => $this->is_active,
            'location' => $this->location,
            'lead_time' => $this->lead_time,
            'pkg_price' => $this->pkg_price,
            'pkg_cost' => $this->pkg_cost,
            'unit_price' => $this->unit_price,
            'label_codes' => $this->label_codes_get_data(),
            'created_at' => $this->created_at->format('m/d/Y')
        ];
    }

}
