<?php

namespace Noking50\Modules\BannerCarousel\Validations;

use Noking50\Modules\Required\Validation\BaseValidation;
use Noking50\FileUpload\Rules\JsonFile;

class ModuleBannerCarouselValidation extends BaseValidation {

    public function validate_add($request_data = null) {
        $rules = [
            'button_link' => ['string', 'nullable', 'max:200'],
            'status' => ['integer', 'required', 'in:0,1'],
            'lang' => ['array'],
        ];
        $rules_lang = [
            'name' => ['string', 'required', 'max:100'],
            'title' => ['string', 'nullable', 'max:100'],
            'subtitle' => ['string', 'nullable', 'max:100'],
            'photo' => ['required', new JsonFile(1, 1)],
            'photo_m' => ['required', new JsonFile(1, 1)],
            'button_text' => ['string', 'nullable', 'max:50'],
        ];
        $attributes = array_merge(
                trans('module_required::validation.attributes'),
                trans('module_banner_carousel::validation.attributes.module_banner_carousel')
                );
        foreach($rules_lang as $k => $v){
            $rules['lang.*.' . $k] = $v;
            if(isset($attributes[$k])){
                $attributes['lang.*.' . $k] = $attributes[$k];
            }
        }

        return $this->validate($rules, $request_data, $attributes);
    }

    public function validate_edit($request_data = null) {
        $rules = [
            'id' => ['integer', 'required'],
            'button_link' => ['string', 'nullable', 'max:200'],
            'status' => ['integer', 'required', 'in:0,1'],
            'lang' => ['array'],
        ];
        $rules_lang = [
            'name' => ['string', 'required', 'max:100'],
            'title' => ['string', 'nullable', 'max:100'],
            'subtitle' => ['string', 'nullable', 'max:100'],
            'photo' => ['required', new JsonFile(1, 1)],
            'photo_m' => ['required', new JsonFile(1, 1)],
            'button_text' => ['string', 'nullable', 'max:50'],
        ];
        $attributes = array_merge(
                trans('module_required::validation.attributes'),
                trans('module_banner_carousel::validation.attributes.module_banner_carousel')
                );
        foreach($rules_lang as $k => $v){
            $rules['lang.*.' . $k] = $v;
            if(isset($attributes[$k])){
                $attributes['lang.*.' . $k] = $attributes[$k];
            }
        }

        return $this->validate($rules, $request_data, $attributes);
    }

}
