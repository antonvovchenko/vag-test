<?php

namespace App\Models;

use OwenIt\Auditing\Auditing;

class BaseModel extends Auditing
{
    //default model values
    public $defaults = [];

    // Disables the log record in this model.
    protected $auditEnabled  = true;

    // Disables the log record after 500 records.
    //protected $historyLimit = 500;

    // Fields you do NOT want to register.
    //protected $dontKeepLogOf = ['created_at', 'updated_at'];

    // Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    public function getItemLabel() {
        return 'Item Label';
    }

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes($this->defaults, true);
        parent::__construct($attributes);
    }
}
