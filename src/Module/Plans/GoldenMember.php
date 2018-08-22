<?php

namespace Noking50\Modules\Cart\Discount\Plans;

use Noking50\Modules\Cart\Discount\Services\ModuleCartDiscountGoldenMemberService;

/**
 * 黃金會員優惠
 */
class GoldenMember {

    protected $discountGoldenMemberService;

    public function __construct(ModuleCartDiscountGoldenMemberService $discountGoldenMemberService) {
        $this->discountGoldenMemberService = $discountGoldenMemberService;
    }

    public function getList() {
        $dataSet_discount_golden_member = $this->discountGoldenMemberService->getListBackend();
        
        return $dataSet_discount_golden_member;
    }

    public function getDetail() {
        
    }

    public function add() {
        
    }

    public function edit() {
        
    }

    public function replace() {
        
    }

    public function delete() {
        
    }

}
