<?php

namespace Noking50\Modules\Cart\Discount\Repositories;

use Noking50\Modules\Cart\Discount\Models\ModuleCartDiscountGoldenMember;

class ModuleCartDiscountGoldenMemberRepository {

    protected $discountGoldenMember;
    protected $table;
    protected $table_member;

    public function __construct(ModuleCartDiscountGoldenMember $discountGoldenMember) {
        $this->discountGoldenMember = $discountGoldenMember;
        $this->table = $this->discountGoldenMember->getTable();
        $this->table_member = config('module_cart_discount.datatable.member');
    }

    # List

    public function getListBackend($sorts = null) {
        $column_member_pk = config('module_cart_discount.datacolumn.member.pk');
        $column_member_name = config('module_cart_discount.datacolumn.member.name');

        $dataSet = $this->discountGoldenMember->select([
                    "{$this->table}.id",
                    "{$this->table}.created_at",
                    "{$this->table}.code",
                    "{$this->table_member}.{$column_member_name} AS member_name",
                    "{$this->table}.type",
                    "{$this->table}.date_start",
                    "{$this->table}.date_end",
                    "{$this->table}.status",
                ])
                ->leftJoin($this->table_member, "{$this->table}.member_id", '=', "{$this->table_member}.{$column_member_pk}")
                ->usable()
                ->setPagination()
                ->get();

        return $dataSet;
    }

    # Detail

    public function getDetail($id, $columns = ['*']) {
        return $this->discountGoldenMember
                        ->where("{$this->table}.id", '=', $id)
                        ->first($columns);
    }

    public function getDetailBackend($id) {
        $column_member_pk = config('module_cart_discount.datacolumn.member.pk');
        $column_member_name = config('module_cart_discount.datacolumn.member.name');
        
        $dataRow = $this->discountGoldenMember->select([
                    "{$this->table}.id",
                    "{$this->table}.created_at",
                    "{$this->table}.updated_at",
                    "{$this->table}.code",
                    "{$this->table_member}.{$column_member_name} AS member_name",
                    "{$this->table}.type",
                    "{$this->table}.discount_all",
                    "{$this->table}.date_start",
                    "{$this->table}.date_end",
                    "{$this->table}.description",
                    "{$this->table}.status",
                ])
                ->leftJoin($this->table_member, "{$this->table}.member_id", '=', "{$this->table_member}.{$column_member_pk}")
                ->selectUpdaterAdmin()
                ->where("{$this->table}.id", '=', $id)
                ->usable()
                ->first();

        return $dataRow;
    }

    public function getDetailBackendEdit($id) {
        $column_member_pk = config('module_cart_discount.datacolumn.member.pk');
        $column_member_name = config('module_cart_discount.datacolumn.member.name');
        
        $dataRow = $this->discountGoldenMember->select([
                    "{$this->table}.id",
                    "{$this->table}.created_at",
                    "{$this->table}.updated_at",
                    "{$this->table}.code",
                    "{$this->table}.member_id",
                    "{$this->table_member}.{$column_member_name} AS member_name",
                    "{$this->table}.type",
                    "{$this->table}.discount_all",
                    "{$this->table}.date_start",
                    "{$this->table}.date_end",
                    "{$this->table}.description",
                    "{$this->table}.status",
                ])
                ->leftJoin($this->table_member, "{$this->table}.member_id", '=', "{$this->table_member}.{$column_member_pk}")
                ->selectUpdaterAdmin()
                ->where("{$this->table}.id", '=', $id)
                ->usable()
                ->first();

        return $dataRow;
    }

    # insert update delete

    public function insert($data) {
        $result = $this->discountGoldenMember->create($data);

        return $result;
    }

    public function update($id, $data) {
        $before = $this->detail($id);
        $result = $this->discountGoldenMember
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
        $result = $this->discountGoldenMember
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

}
