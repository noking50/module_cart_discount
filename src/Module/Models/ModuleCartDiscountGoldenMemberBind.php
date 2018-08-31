<?php

namespace Noking50\Modules\Cart\Discount\Models;

use Noking50\Modules\Required\Models\BaseModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModuleCartDiscountGoldenMemberBind extends BaseModel {

    public $incrementing = false;
    protected $guarded = [];

    public function __construct($attributes = []) {
        $this->table = config('module_cart_discount.datatable.golden_member_bind');
        parent::__construct($attributes);
    }


}
