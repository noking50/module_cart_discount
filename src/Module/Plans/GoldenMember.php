<?php

namespace Noking50\Modules\Cart\Discount\Plans;

use Noking50\Modules\Cart\Discount\Services\ModuleCartDiscountGoldenMemberService;
use Noking50\Modules\Cart\Discount\Validations\ModuleCartDiscountGoldenMemberValidation;
use Noking50\Modules\Required\Exceptions\DatabaseLogicException;
use Request;
use Route;
use DB;
use DBLog;

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
            $is_member_exist = $this->discountGoldenMemberService->isMemberExist(Request::input('member_id', ''));
            if ($is_member_exist) {
                throw new DatabaseLogicException(trans('module_cart_discount::database.golden_member.member_exist'));
            }
            $is_code_exist = $this->discountGoldenMemberService->isCodeExist(Request::input('code', ''));
            if ($is_code_exist) {
                throw new DatabaseLogicException(trans('module_cart_discount::database.golden_member.code_exist'));
            }

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
        $this->discountGoldenMemberValidation->validate_edit();

        $id = Request::input('id');
        DB::beginTransaction();
        try {
            $result = $this->discountGoldenMemberService->edit($id, Request::all());
            $result_product = null;
            if ($result) {
                $result_product = $this->discountGoldenMemberService->editProduct($id, Request::all());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable_golden_member = config('module_cart_discount.datatable.golden_member');
        $datatable_golden_member_product = config('module_cart_discount.datatable.golden_member_product');
        // dblog
        DBLog::write($datatable_golden_member, array_get($result, 'before'), array_get($result, 'after'));
        if ($result_product) {
            foreach ($result_product['add'] as $k => $v) {
                DBLog::write($datatable_golden_member_product, null, $v);
            }
            foreach ($result_product['edit'] as $k => $v) {
                DBLog::write($datatable_golden_member_product, array_get($v, 'before'), array_get($v, 'after'));
            }
            foreach ($result_product['delete'] as $k => $v) {
                DBLog::write($datatable_golden_member_product, array_get($v, 'before'), array_get($v, 'after'));
            }
        }
    }

    public function replace() {
        
    }

    public function editStatus() {
        $this->discountGoldenMemberValidation->validate_status();

        $ids = Request::input('id', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $status = Request::input('status');
        DB::beginTransaction();
        try {
            $result = collect();
            foreach ($ids as $k => $v) {
                $result->push($this->discountGoldenMemberService->changeStatus($v, $status));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable_golden_member = config('module_cart_discount.datatable.golden_member');
        // dblog
        foreach ($result as $k => $v) {
            DBLog::write($datatable_golden_member, array_get($v, 'before'), array_get($v, 'after'));
        }
    }

    public function delete() {
        $this->discountGoldenMemberValidation->validate_delete();

        $ids = Request::input('id', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        DB::beginTransaction();
        try {
            $result = collect();
            $result_product = collect();
            foreach ($ids as $k => $v) {
                $result->put($k, $this->discountGoldenMemberService->delete($v));
                if (array_get($result->get($k), 'before') && !array_get($result->get($k), 'after')) {
                    $result_product_tmp = $this->discountGoldenMemberService->deleteProduct($v);
                    if ($result_product_tmp) {
                        foreach ($result_product_tmp as $kk => $vv) {
                            $result_product->push($vv);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable_golden_member = config('module_cart_discount.datatable.golden_member');
        $datatable_golden_member_product = config('module_cart_discount.datatable.golden_member_product');
        // dblog
        foreach ($result as $k => $v) {
            DBLog::write($datatable_golden_member, array_get($v, 'before'), array_get($v, 'after'));
        }
        foreach ($result_product as $k => $v) {
            DBLog::write($datatable_golden_member_product, array_get($v, 'before'), array_get($v, 'after'));
        }
    }

}
