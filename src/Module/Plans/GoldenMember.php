<?php

namespace Noking50\Modules\Cart\Discount\Plans;

use Noking50\Modules\Cart\Discount\Services\ModuleCartDiscountGoldenMemberService;
use Noking50\Modules\Cart\Discount\Validations\ModuleCartDiscountGoldenMemberValidation;

/**
 * 黃金會員優惠
 */
class GoldenMember {

    protected $discountGoldenMemberService;
    protected $discountGoldenMemberValidation;

    public function __construct() {
        $this->discountGoldenMemberService = \App::make(ModuleCartDiscountGoldenMemberService::class);
        $this->discountGoldenMemberValidation = \App::make(ModuleCartDiscountGoldenMemberValidation::class);
    }

    public function getList() {
        $dataSet_discount_golden_member = $this->discountGoldenMemberService->getListBackend();

        return $dataSet_discount_golden_member;
    }

    public function getDetail() {
        
    }

    public function add() {
        $this->discountGoldenMemberValidation->validate_add();

        DB::beginTransaction();
        try {
            $dataRow_discount_golden_member = $this->discountGoldenMemberService->add(Request::all());
            $dataSet_discount_golden_member_product = $this->discountGoldenMemberService->addProduct($dataRow_discount_golden_member->id, Request::all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable_golden_member = config('module_cart_discount.datatable.golden_member');
        $datatable_golden_member_product = config('module_cart_discount.datatable.golden_member_product');
        // dblog
        DBLog::write($datatable_golden_member, null, $dataRow_discount_golden_member);
        foreach ($dataSet_discount_golden_member_product as $k => $v) {
            DBLog::write($datatable_golden_member_product, null, $v);
        }
    }

    public function edit() {
        
    }

    public function replace() {
        
    }

    public function delete() {
        
    }

}
