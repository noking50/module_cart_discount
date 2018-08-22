<?php

namespace Noking50\Modules\Cart\Discount\Repositories;

use Noking50\Modules\Cart\Discount\Models\ModuleCartDiscountGoldenMemberProduct;

class ModuleCartDiscountGoldenMemberProductRepository {

    protected $discountGoldenMemberProduct;
    protected $table;
    protected $table_parent;
    protected $table_product;

    public function __construct(ModuleCartDiscountGoldenMemberProduct $discountGoldenMemberProduct) {
        $this->discountGoldenMemberProduct = $discountGoldenMemberProduct;
        $this->table = $this->discountGoldenMemberProduct->getTable();
        $this->table_parent = config('module_cart_discount.datatable.golden_member');
        $this->table_product = config('module_cart_discount.datatable.product');
    }

    # List

    public function getListFromParent($parent_id) {
        $column_product_pk = config('module_cart_discount.datacolumn.product.pk');
        $column_product_name = config('module_cart_discount.datacolumn.product.name');
        
        $dataSet = $this->discountGoldenMemberProduct->select([
                    "{$this->table}.id",
                    "{$this->table}.{$this->table_parent}_id",
                    "{$this->table}.product_id",
                    "{$this->table_product}.{$column_product_name} AS product_name",
                    "{$this->table}.discount",
                ])
                ->leftJoin($this->table_product, "{$this->table}.product_id", '=', "{$this->table_product}.{$column_product_pk}")
                ->where("{$this->table}.{$this->table_parent}_id", '=', $parent_id)
                ->usable()
                ->orderBy("{$this->table}.id", 'asc')
                ->get();

        return $dataSet;
    }

    # Detail

    public function detail($id, $columns = ['*']) {
        $dataRow = $this->discountGoldenMemberProduct
                ->where("{$this->table}.id", '=', $id)
                ->first($columns);

        return $dataRow;
    }

    # insert update delete

    public function insert($data) {
        $result = $this->discountGoldenMemberProduct->create($data);

        return $result;
    }

    public function update($id, $data) {
        $before = $this->detail($id);
        $result = $this->discountGoldenMemberProduct
                ->where("{$this->table}.id", '=', $id)
                ->usable()
                ->update($data);
        $after = $this->detail($id);

        if ($before && $after && is_null($before->deprecated_at)) {
            return collect([
                'before' => $before,
                'after' => $after,
            ]);
        }
        return null;
    }

    public function delete($id) {
        $before = $this->detail($id);
        $result = $this->discountGoldenMemberProduct
                ->where("{$this->table}.id", '=', $id)
                ->usable()
                ->delete();

        if ($before && is_null($before->deprecated_at)) {
            return collect([
                'before' => $before,
                'after' => null,
            ]);
        }
        return null;
    }

    public function deleteAll($parent_id) {
        $before = $this->discountGoldenMemberProduct
                ->where("{$this->table}.{$this->table_parent}_id", '=', $parent_id)
                ->get();
        $result = $this->discountGoldenMemberProduct
                ->where("{$this->table}.{$this->table_parent}_id", '=', $parent_id)
                ->delete();

        if (count($before) > 0) {
            return $before;
        }
        return null;
    }

}
