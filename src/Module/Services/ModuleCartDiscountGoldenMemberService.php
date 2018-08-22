<?php

namespace Noking50\Modules\Cart\Discount\Services;

use Noking50\Modules\Cart\Discount\Repositories\ModuleCartDiscountGoldenMemberRepository;
use Noking50\Modules\Cart\Discount\Repositories\ModuleCartDiscountGoldenMemberProductRepository;
use User;

class ModuleCartDiscountGoldenMemberService {

    protected $discountGoldenMemberRepository;
    protected $discountGoldenMemberProductRepository;

    public function __construct(ModuleCartDiscountGoldenMemberRepository $discountGoldenMemberRepository
    , ModuleCartDiscountGoldenMemberProductRepository $discountGoldenMemberProductRepository) {
        $this->discountGoldenMemberRepository = $discountGoldenMemberRepository;
        $this->discountGoldenMemberProductRepository = $discountGoldenMemberProductRepository;
    }

    public function getListBackend() {
        $dataSet = $this->discountGoldenMemberRepository->getListBackend();

        return $dataSet;
    }

    public function getDetailBackend($id, $group) {
        $dataRow = $this->moduleBannerCarouselRepository->detailBackend($id, $group);

        if (!empty($dataRow)) {
            $dataSet_lang = $this->moduleBannerCarouselLangRepository->listAll($id);
            foreach ($dataSet_lang as $k => $v) {
                $dataSet_lang[$k]->photo = \FileUpload::getFiles($v->photo);
                $dataSet_lang[$k]->photo_m = \FileUpload::getFiles($v->photo_m);
            }
            $dataRow->lang = $dataSet_lang->keyBy('lang');
        }

        return $dataRow;
    }

    public function getDetailBackendEdit($id, $group) {
        $dataRow = $this->moduleBannerCarouselRepository->detailBackendEdit($id, $group);

        if (!empty($dataRow)) {
            $dataSet_lang = $this->moduleBannerCarouselLangRepository->listAll($id);
            $dataRow->lang = $dataSet_lang->keyBy('lang');
        }

        return $dataRow;
    }

    #

    public function add($group, $data) {
        $datatable_admin = config('user.group.admin.datatable');
        $data_insert = [
            "create_{$datatable_admin}_id" => User::id(),
            "update_{$datatable_admin}_id" => User::id(),
            'module_group' => $group,
            'button_link' => array_get($data, 'button_link', '') ?: '',
            'status' => array_get($data, 'status'),
            'publish' => 1,
            'order' => 0,
        ];
        $result = $this->moduleBannerCarouselRepository->insert($data_insert);
        if ($result) {
            $order_target_id = intval(array_get($data, 'order_target_id'));
            $order_old = $result->order;
            $order_new = $this->order($result->id, $group, $order_old, $order_target_id);

            $result->order = $order_new;
        }

        return $result;
    }

    public function edit($id, $group, $data) {
        $datatable_admin = config('user.group.admin.datatable');
        $data_update = [
            "update_{$datatable_admin}_id" => User::id(),
            'button_link' => array_get($data, 'button_link', '') ?: '',
            'status' => array_get($data, 'status'),
        ];
        $result = $this->moduleBannerCarouselRepository->update($id, $group, $data_update);
        if ($result) {
            $order_target_id = intval(array_get($data, 'order_target_id'));
            $order_old = $result->get('after')->order;
            $order_new = $this->order($result->get('after')->id, $group, $order_old, $order_target_id);

            $result->get('after')->order = $order_new;
        }

        return $result;
    }

    public function delete($id, $group) {
        $result = $this->moduleBannerCarouselRepository->delete($id, $group);

        return $result;
    }

    public function changeStatus($id, $group, $status) {
        $datatable_admin = config('user.group.admin.datatable');
        $data_update = [
            "update_{$datatable_admin}_id" => User::id(),
            'status' => $status,
        ];
        $result = $this->moduleBannerCarouselRepository->update($id, $group, $data_update);

        return $result;
    }

    public function order($id, $group, $order_old, $order_target_id) {
        $order_new = $order_old;
        if ($order_target_id <= 0 || $id == $order_target_id) {
            return $order_new;
        }

        $order_tmp = intval($this->moduleBannerCarouselRepository->valueOrder($order_target_id, $group));
        if ($order_tmp > 0 && $order_tmp != $order_new) {
            $order_new = $order_tmp;
            $this->moduleBannerCarouselRepository->order($id, $group, $order_new, $order_old);
        }
        return $order_new;
    }

    # lang

    public function addLang($parent_id, $data) {
        $parent_table = config('module_banner_carousel.datatable');
        $data_lang = array_get($data, 'lang', []);
        $result = collect();
        foreach ($data_lang as $k => $v) {
            $data_insert = [
                "{$parent_table}_id" => $parent_id,
                'lang' => $k,
                'name' => array_get($v, 'name', '') ?: '',
                'title' => array_get($v, 'title', '') ?: '',
                'subtitle' => array_get($v, 'subtitle', '') ?: '',
                'photo' => array_get($v, 'photo', '[]') ?: '[]',
                'photo_m' => array_get($v, 'photo_m', '[]') ?: '[]',
                'button_text' => array_get($v, 'button_text', '') ?: '',
            ];
            $result->put($k, $this->moduleBannerCarouselLangRepository->insert($data_insert));
        }

        return $result;
    }

    public function editLang($parent_id, $data) {
        $parent_table = config('module_banner_carousel.datatable');
        $data_compare = $this->langEditCompare($parent_id, $data);
        $result_add = collect();
        $result_edit = collect();
        $result_delete = collect();
        foreach ($data_compare['add'] as $k => $v) {
            $data_insert = [
                "{$parent_table}_id" => $parent_id,
                'lang' => $k,
                'name' => array_get($v, 'name', '') ?: '',
                'title' => array_get($v, 'title', '') ?: '',
                'subtitle' => array_get($v, 'subtitle', '') ?: '',
                'photo' => array_get($v, 'photo', '[]') ?: '[]',
                'photo_m' => array_get($v, 'photo_m', '[]') ?: '[]',
                'button_text' => array_get($v, 'button_text', '') ?: '',
            ];
            $result_add->put($k, $this->moduleBannerCarouselLangRepository->insert($data_insert));
        }
        foreach ($data_compare['edit'] as $k => $v) {
            $data_update = [
                'name' => array_get($v, 'name', '') ?: '',
                'title' => array_get($v, 'title', '') ?: '',
                'subtitle' => array_get($v, 'subtitle', '') ?: '',
                'photo' => array_get($v, 'photo', '[]') ?: '[]',
                'photo_m' => array_get($v, 'photo_m', '[]') ?: '[]',
                'button_text' => array_get($v, 'button_text', '') ?: '',
            ];
            $result_edit->put($k, $this->moduleBannerCarouselLangRepository->update($parent_id, $k, $data_update));
        }
        foreach ($data_compare['delete'] as $k => $v) {
            $result_delete->put($k, $this->moduleBannerCarouselLangRepository->delete($parent_id, $k));
        }

        return collect([
            'add' => $result_add,
            'edit' => $result_edit,
            'delete' => $result_delete,
        ]);
    }

    public function deleteLang($parent_id) {
        $result = $this->moduleBannerCarouselLangRepository->deleteAll($parent_id);

        return $result;
    }

    protected function langEditCompare($parent_id, $data) {
        $data_new = array_get($data, 'lang', []);
        $data_old = $this->moduleBannerCarouselLangRepository->listAll($parent_id)->keyBy('lang')->toArray();
        $data_add = collect();
        $data_edit = collect();
        $data_del = collect();
        foreach ($data_new as $k => $v) {
            if (isset($data_old[$k])) {
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

}
