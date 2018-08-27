<?php

namespace Noking50\Modules\Cart\Discount\Models;

use Noking50\Modules\Required\Models\BaseModel;

class ModuleCartDiscountGoldenMember extends BaseModel {

    protected $guarded = [];

    public function __construct($attributes = []) {
        $this->table = config('module_cart_discount.datatable.golden_member');
        parent::__construct($attributes);
    }

    public function scopeUsable($query) {
        return $query->where($this->table . '.deprecate_flag', '=', 0);
    }

    /**
     * 選取後台新增人員
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectCreatorAdmin($query) {
        $user_table = config('user.group.admin.datatable', '');
        if (Schema::hasTable($user_table)) {
            $query->addSelect([
                        "{$this->table}.create_admin_id",
                        "create_admin.name AS create_admin_name",
                    ])
                    ->leftJoin("{$user_table} AS create_admin", $this->table . ".create_admin_id", '=', "create_admin.id");
        } else {
            $query->addSelect([
                "{$this->table}.create_admin_id'",
                DB::raw(" NULL AS create_admin_name "),
            ]);
        }
        return $query;
    }

    /**
     * 選取後台更新人員
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectUpdaterAdmin($query) {
        $user_table = config('user.group.admin.datatable', '');
        if (Schema::hasTable($user_table)) {
            $query->addSelect([
                        "{$this->table}.update_admin_id",
                        "update_admin.name AS update_admin_name",
                    ])
                    ->leftJoin("{$user_table} AS update_admin", $this->table . ".update_admin_id", '=', "update_admin.id");
        } else {
            $query->addSelect([
                "{$this->table}.update_admin_id",
                DB::raw(" NULL AS update_admin_name "),
            ]);
        }

        return $query;
    }

    public function getCreateAdminNameAttribute($value) {
        if (!is_null($this->{"create_admin_id"}) && $this->{"create_admin_id"} == 0) {
            return config('user.group.admin.super.name');
        }

        return $value;
    }

    public function getUpdateAdminNameAttribute($value) {
        if (!is_null($this->{"update_admin_id"}) && $this->{"update_admin_id"} == 0) {
            return config('user.group.admin.super.name');
        }

        return $value;
    }

}
