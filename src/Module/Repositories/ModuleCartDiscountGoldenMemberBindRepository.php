<?php

namespace Noking50\Modules\Cart\Discount\Repositories;

use Noking50\Modules\Cart\Discount\Models\ModuleCartDiscountGoldenMemberBind;

class ModuleCartDiscountGoldenMemberBindRepository {

    protected $discountGoldenMemberBind;
    protected $table;

    public function __construct(ModuleCartDiscountGoldenMemberBind $discountGoldenMemberBind) {
        $this->discountGoldenMemberBind = $discountGoldenMemberBind;
        $this->table = $this->discountGoldenMemberBind->getTable();
    }

    # List
    
    # Detail

    public function getDetail($member_id, $columns = ['*']) {
        $dataRow = $this->discountGoldenMemberBind
                ->where("{$this->table}.member_id", '=', $member_id)
                ->first($columns);

        return $dataRow;
    }

    # insert update delete

    public function insert($data) {
        $result = $this->discountGoldenMemberBind->create($data);

        return $result;
    }

}
