<?php

namespace Noking50\Modules\Cart\Discount\Services;

use Noking50\Modules\Cart\Discount\Repositories\ModuleCartDiscountGoldenMemberRepository;
use Noking50\Modules\Cart\Discount\Repositories\ModuleCartDiscountGoldenMemberProductRepository;
use Noking50\Modules\Cart\Discount\Repositories\ModuleCartDiscountGoldenMemberBindRepository;
use User;

class ModuleCartDiscountGoldenMemberService {

    protected $discountGoldenMemberRepository;
    protected $discountGoldenMemberProductRepository;
    protected $discountGoldenMemberBindRepository;

    public function __construct(ModuleCartDiscountGoldenMemberRepository $discountGoldenMemberRepository
    , ModuleCartDiscountGoldenMemberProductRepository $discountGoldenMemberProductRepository
    , ModuleCartDiscountGoldenMemberBindRepository $discountGoldenMemberBindRepository) {
        $this->discountGoldenMemberRepository = $discountGoldenMemberRepository;
        $this->discountGoldenMemberProductRepository = $discountGoldenMemberProductRepository;
        $this->discountGoldenMemberBindRepository = $discountGoldenMemberBindRepository;
    }

    public function getListBackend() {
        $dataSet = $this->discountGoldenMemberRepository->getListBackend();

        return $dataSet;
    }

    public function getDetailBackend($id) {
        $dataRow = $this->discountGoldenMemberRepository->getDetailBackend($id);

        if (!empty($dataRow)) {
            $dataSet_golden_member_product = $this->discountGoldenMemberProductRepository->getListInfoByParent($id);
            $dataRow->product = $dataSet_golden_member_product;
        }

        return $dataRow;
    }

    public function getDetailBackendEdit($id) {
        $dataRow = $this->discountGoldenMemberRepository->getDetailBackendEdit($id);

        if (!empty($dataRow)) {
            $dataSet_golden_member_product = $this->discountGoldenMemberProductRepository->getListInfoByParent($id);
            $dataRow->product = $dataSet_golden_member_product;
        }

        return $dataRow;
    }

    public function getDetailByMember($member_id) {
        $dataRow_bind = $this->getDetailBind($member_id);
        if (is_null($dataRow_bind)) {
            return null;
        }

        $dataRow = $this->discountGoldenMemberRepository->getDetailByMember($dataRow_bind->bind_member_id);
        if (!empty($dataRow)) {
            $dataRow->is_active = $this->isDiscountActive($dataRow);
            if ($dataRow->type == 2) {
                $dataSet_golden_member_product = $this->discountGoldenMemberProductRepository->getListByParent($dataRow->id)->pluck('discount', 'product_id');
                $dataRow->product = $dataSet_golden_member_product;
            } else {
                $dataRow->product = collect();
            }
        }

        return $dataRow;
    }

    public function getDetailByCode($code) {
        $dataRow = $this->discountGoldenMemberRepository->getDetailByCode($code);
        if (!empty($dataRow)) {
            $dataRow->is_active = $this->isDiscountActive($dataRow);
            if ($dataRow->type == 2) {
                $dataSet_golden_member_product = $this->discountGoldenMemberProductRepository->getListByParent($dataRow->id)->pluck('discount', 'product_id');
                $dataRow->product = $dataSet_golden_member_product;
            } else {
                $dataRow->product = collect();
            }
        }

        return $dataRow;
    }

    public function getDetailByBindMember($member_id) {
        $dataRow = $this->discountGoldenMemberRepository->getDetailByMember($member_id);
        if (!empty($dataRow)) {
            $dataRow->is_active = $this->isDiscountActive($dataRow);
            if ($dataRow->type == 2) {
                $dataSet_golden_member_product = $this->discountGoldenMemberProductRepository->getListByParent($dataRow->id)->pluck('discount', 'product_id');
                $dataRow->product = $dataSet_golden_member_product;
            } else {
                $dataRow->product = collect();
            }
        }

        return $dataRow;
    }
    
    public function getDetailBind($member_id) {
        $dataRow_bind = $this->discountGoldenMemberBindRepository->getDetail($member_id);

        return $dataRow_bind;
    }

    public function isMemberExist($member_id, $id = null) {
        $is_exist = $this->discountGoldenMemberRepository->isExistMember($member_id, $id);

        return $is_exist;
    }

    public function isCodeExist($code, $id = null) {
        $is_exist = $this->discountGoldenMemberRepository->isExistCode($code, $id);

        return $is_exist;
    }

    public function isDateLatest($id, $date_start, $is_exclude = true) {
        $is_latest = $this->discountGoldenMemberRepository->isDateLatest($id, $date_start, $is_exclude);

        return $is_latest;
    }

    public function isDiscountActive($dataRow) {
        $is_active = true;
        $dt_today = new \DateTime('today');
        $dt_start = new \DateTime($dataRow->date_start);
        $dt_end = is_null($dataRow->date_end) ? null : new \DateTime($dataRow->date_end);

        if ($dt_today < $dt_start ||
                (!is_null($dt_end) && $dt_today > $dt_end) ||
                (isset($dataRow->status) && $dataRow->status != 1)) {
            $is_active = false;
        }

        return $is_active;
    }

    #

    public function add($data) {
        $data_insert = [
            "create_admin_id" => User::id(),
            "update_admin_id" => User::id(),
            'deprecated_at' => null,
            'member_id' => array_get($data, 'member_id'),
            'code' => array_get($data, 'code', '') ?: '',
            'deprecate_flag' => 0,
            'type' => array_get($data, 'type'),
            'discount_all' => array_get($data, 'discount_all'),
            'date_start' => array_get($data, 'date_start'),
            'date_end' => array_get($data, 'date_end') ?: null,
            'description' => array_get($data, 'description', '') ?: '',
            'status' => array_get($data, 'status'),
        ];
        $result = $this->discountGoldenMemberRepository->insert($data_insert);

        return $result;
    }

    public function edit($id, $data) {
        $data_update = [
            "update_admin_id" => User::id(),
            'type' => array_get($data, 'type'),
            'discount_all' => array_get($data, 'discount_all'),
            'date_start' => array_get($data, 'date_start'),
            'date_end' => array_get($data, 'date_end') ?: null,
            'description' => array_get($data, 'description', '') ?: '',
            'status' => array_get($data, 'status'),
        ];
        $result = $this->discountGoldenMemberRepository->update($id, $data_update);

        return $result;
    }

    public function replace($id, $data) {
        $dataRow = $this->discountGoldenMemberRepository->getDetail($id);
        if (empty($dataRow) || $dataRow->deprecate_flag != 0) {
            return null;
        }
        $dataSet_golden_member_product = $this->discountGoldenMemberProductRepository->getListByParent($id);

        $dt_now = new \DateTime();
        $data['member_id'] = $dataRow->member_id;
        $data['code'] = $dataRow->code;
        $data['status'] = 1;
        $result = array(
            'add' => null,
            'edit' => null,
            'add_product' => collect(),
            'edit_product' => collect(),
        );

        //
        $data_update = [
            "update_admin_id" => User::id(),
            'deprecated_at' => $dt_now,
            'deprecate_flag' => $dataRow->id,
        ];
        $result['edit'] = $this->discountGoldenMemberRepository->update($id, $data_update);

        //
        $result['add'] = $this->add($data);

        //
        foreach ($dataSet_golden_member_product as $k => $v) {
            $data_update = [
                "update_admin_id" => User::id(),
                'deprecated_at' => $dt_now,
                'deprecate_flag' => $v->id,
            ];
            $result['edit_product']->put($k, $this->discountGoldenMemberProductRepository->update($dataRow->id, $v->id, $data_update));
        }

        //
        if (!empty($result['add'])) {
            $result['add_product'] = $this->addProduct($result['add']->id, $data);
        }

        return collect($result);
    }

    public function delete($id) {
        $result = $this->discountGoldenMemberRepository->delete($id);

        return $result;
    }

    public function changeStatus($id, $status) {
        $data_update = [
            "update_admin_id" => User::id(),
            'status' => $status,
        ];
        $result = $this->discountGoldenMemberRepository->update($id, $data_update);

        return $result;
    }

    # product

    public function addProduct($parent_id, $data) {
        $parent_table = config('module_cart_discount.datatable.golden_member');
        $data_product = collect(array_get($data, 'product', []))->keyBy('product_id');
        $result = collect();
        foreach ($data_product as $k => $v) {
            $data_insert = [
                "create_admin_id" => User::id(),
                "update_admin_id" => User::id(),
                'deprecated_at' => null,
                "{$parent_table}_id" => $parent_id,
                'product_id' => array_get($v, 'product_id'),
                'deprecate_flag' => 0,
                'discount' => array_get($v, 'discount'),
            ];
            $result->put($k, $this->discountGoldenMemberProductRepository->insert($data_insert));
        }

        return $result;
    }

    public function editProduct($parent_id, $data) {
        $parent_table = config('module_cart_discount.datatable.golden_member');
        $data_compare = $this->productEditCompare($parent_id, $data);
        $result_add = collect();
        $result_edit = collect();
        $result_delete = collect();
        foreach ($data_compare['add'] as $k => $v) {
            $data_insert = [
                "create_admin_id" => User::id(),
                "update_admin_id" => User::id(),
                'deprecated_at' => null,
                "{$parent_table}_id" => $parent_id,
                'product_id' => array_get($v, 'product_id'),
                'deprecate_flag' => 0,
                'discount' => array_get($v, 'discount'),
            ];
            $result_add->put($k, $this->discountGoldenMemberProductRepository->insert($data_insert));
        }
        foreach ($data_compare['edit'] as $k => $v) {
            $data_update = [
                "update_admin_id" => User::id(),
                'discount' => array_get($v, 'discount'),
            ];
            $result_edit->put($k, $this->discountGoldenMemberProductRepository->update($parent_id, array_get($v, 'id'), $data_update));
        }
        foreach ($data_compare['delete'] as $k => $v) {
            $result_delete->put($k, $this->discountGoldenMemberProductRepository->delete($parent_id, array_get($v, 'id')));
        }

        return collect([
            'add' => $result_add,
            'edit' => $result_edit,
            'delete' => $result_delete,
        ]);
    }

    public function deleteProduct($parent_id) {
        $result = $this->discountGoldenMemberProductRepository->deleteAll($parent_id);

        return $result;
    }

    protected function productEditCompare($parent_id, $data) {
        $data_new = collect(array_get($data, 'product', []))->keyBy('product_id');
        $data_old = $this->discountGoldenMemberProductRepository->getListByParent($parent_id)->keyBy('product_id')->toArray();
        $data_add = collect();
        $data_edit = collect();
        $data_del = collect();
        foreach ($data_new as $k => $v) {
            if (isset($data_old[$k])) {
                $v['id'] = $data_old[$k]['id'];
                $data_edit->put($k, $v);
            } else {
                $data_add->put($k, $v);
            }
        }
        foreach ($data_old as $k => $v) {
            if (!isset($data_new[$k])) {
                $data_del->put($k, $v);
            }
        }

        return collect([
            'add' => $data_add,
            'edit' => $data_edit,
            'delete' => $data_del,
        ]);
    }

    #bind member

    public function addBind($member_id, $bind_member_id) {
        $data_insert = [
            "member_id" => $member_id,
            "bind_member_id" => $bind_member_id,
        ];
        $result = $this->discountGoldenMemberBindRepository->insert($data_insert);

        return $result;
    }

}
