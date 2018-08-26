<?php

namespace Noking50\Modules\Cart\Discount\Validations;

use Noking50\Modules\Required\Validation\BaseValidation;

class ModuleCartDiscountGoldenMemberValidation extends BaseValidation {

    public function validate_add($request_data = null) {
        $rules = [
            'member_id' => ['integer', 'required'],
            'code' => ['string', 'required', 'max:10'],
            'type' => ['integer', 'required'],
            'discount_all' => ['numeric', 'max:1', 'min:0'],
            'date_start' => ['date_format:Y/m/d', 'required', 'max:10'],
            'date_end' => ['date_format:Y/m/d', 'nullable', 'max:10', 'after_or_equal:date_start'],
            'description' => ['string', 'max:10'],
            'status' => ['integer', 'required', 'in:0,1'],
            'product' => ['array'],
        ];
        $rules_product = [
            'product_id' => ['integer', 'required'],
            'discount' => ['numeric', 'required', 'max:1', 'min:0'],
        ];
        $attributes = array_merge(
                trans('module_required::validation.attributes'), trans('module_cart_discount::validation.attributes.module_cart_discount_golden_member'), trans('module_cart_discount::validation.attributes.module_cart_discount_golden_member_product')
        );
        foreach ($rules_product as $k => $v) {
            $rules['product.*.' . $k] = $v;
            if (isset($attributes[$k])) {
                $attributes['product.*.' . $k] = $attributes[$k];
            }
        }

        return $this->validate($rules, $request_data, $attributes, [], [
                    ['discount_all', ['required'], function($input) {
                            return $input->{"type"} == 1;
                        }],
                    ['product', ['required'], function($input) {
                            return $input->{"type"} == 2;
                        }],
        ]);
    }

    public function validate_edit($request_data = null) {
        $rules = [
            'id' => ['integer', 'required'],
            'type' => ['integer', 'required'],
            'discount_all' => ['numeric', 'max:1', 'min:0'],
            'date_start' => ['date_format:Y/m/d', 'required', 'max:10'],
            'date_end' => ['date_format:Y/m/d', 'nullable', 'max:10', 'after_or_equal:date_start'],
            'description' => ['string', 'max:10'],
            'status' => ['integer', 'required', 'in:0,1'],
            'product' => ['array'],
        ];

        $rules_product = [
            'product_id' => ['integer', 'required'],
            'discount' => ['numeric', 'required', 'max:1', 'min:0'],
        ];
        $attributes = array_merge(
                trans('module_required::validation.attributes'), trans('module_cart_discount::validation.attributes.module_cart_discount_golden_member'), trans('module_cart_discount::validation.attributes.module_cart_discount_golden_member_product')
        );
        foreach ($rules_product as $k => $v) {
            $rules['product.*.' . $k] = $v;
            if (isset($attributes[$k])) {
                $attributes['product.*.' . $k] = $attributes[$k];
            }
        }

        return $this->validate($rules, $request_data, $attributes, [], [
                    ['discount_all', ['required'], function($input) {
                            return $input->{"type"} == 1;
                        }],
                    ['product', ['required'], function($input) {
                            return $input->{"type"} == 2;
                        }],
        ]);
    }

}
