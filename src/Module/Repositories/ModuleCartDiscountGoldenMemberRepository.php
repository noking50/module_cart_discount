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
                    "{$this->table_member}.{$column_member_name} AS member_name",
                    "{$this->table}.code",
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
                    "{$this->table}.member_id",
                    "{$this->table_member}.{$column_member_name} AS member_name",
                    "{$this->table}.code",
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

    # other

    public function isExistMember($member_id, $id = null) {
        $query = $this->discountGoldenMember
                ->where("{$this->table}.member_id", '=', $member_id);
        if (!is_null($id)) {
            $query->where("{$this->table}.id", '!=', $id)
                    ->where("{$this->table}.deprecate_flag", '=', 0);
        }

        return $query->exists();
    }

    public function isExistCode($code, $id = null) {
        $query = $this->discountGoldenMember
                ->where("{$this->table}.code", '=', $code);
        if (!is_null($id)) {
            $query->where("{$this->table}.id", '!=', $id)
                    ->where("{$this->table}.deprecate_flag", '=', 0);
        }

        return $query->exists();
    }

    public function isDateLatest($id, $date_start, $is_exclude = true) {
        $member_id = $this->discountGoldenMember
                ->where("{$this->table}.id", '=', $id)
                ->value('member_id');

        $query = $this->discountGoldenMember
                ->where("{$this->table}.member_id", '=', $member_id)
                ->where("{$this->table}.status", '=', 1)
                ->where(function ($query) use ($date_start) {
            $query->where("{$this->table}.date_start", '>=', $date_start)
            ->orWhere("{$this->table}.date_end", '>=', $date_start);
        });
        if ($is_exclude) {
            $query->where("{$this->table}.id", '!=', $id);
        }

        return !$query->exists();
    }

    # insert update delete

    public function insert($data) {
        $result = $this->discountGoldenMember->create($data);

        return $result;
    }

    public function update($id, $data) {
        $before = $this->getDetail($id);
        $result = $this->discountGoldenMember
                ->where("{$this->table}.id", '=', $id)
                ->usable()
                ->update($data);
        $after = $this->getDetail($id);

        if ($before && $after && $before->deprecate_flag == 0) {
            return collect([
                'before' => $before,
                'after' => $after,
            ]);
        }
        return null;
    }

    public function delete($id) {
        $before = $this->getDetail($id);
        $result = $this->discountGoldenMember
                ->where("{$this->table}.id", '=', $id)
                ->usable()
                ->delete();

        if ($before && $before->deprecate_flag == 0) {
            return collect([
                'before' => $before,
                'after' => null,
            ]);
        }
        return null;
    }

}
