<?php

namespace Noking50\Modules\Cart\Discount\Models;

use Noking50\Modules\Required\Models\BaseModel;

class ModuleCartDiscountCate extends BaseModel {

    protected $guarded = [];

    public function __construct($attributes = []) {
        $this->table = config('module_cart_discount.datatable.discount_cate');
        parent::__construct($attributes);
    }

}
