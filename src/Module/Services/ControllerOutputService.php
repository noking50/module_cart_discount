<?php

namespace Noking50\Modules\BannerCarousel\Services;

use Noking50\Modules\BannerCarousel\Services\ModuleBannerCarouselService;
use Noking50\Modules\Required\Services\LanguageService;
use Noking50\Modules\BannerCarousel\Validations\ModuleBannerCarouselValidation;
use Request;
use Route;
use DB;
use DBLog;
use FileUpload;

class ControllerOutputService {

    protected $moduleBannerCarouselService;
    protected $languageService;
    protected $moduleBannerCarouselValidation;

    public function __construct(ModuleBannerCarouselService $moduleBannerCarouselService
    , LanguageService $languageService
    , ModuleBannerCarouselValidation $moduleBannerCarouselValidation) {
        $this->moduleBannerCarouselService = $moduleBannerCarouselService;
        $this->languageService = $languageService;
        $this->moduleBannerCarouselValidation = $moduleBannerCarouselValidation;
    }

    ## List
    
    public function listBackend($group) {
        $dataSet_module_banner_carousel = $this->moduleBannerCarouselService->getListBackend($group);

        return [
            'dataSet_module_banner_carousel' => $dataSet_module_banner_carousel,
        ];
    }
    
    public function listFrontend($group) {
        $dataSet_module_banner_carousel = $this->moduleBannerCarouselService->getListFrontend($group);

        return [
            'dataSet_module_banner_carousel' => $dataSet_module_banner_carousel,
        ];
    }

    ## Detail
    
    public function detailBackend($group) {
        $id = Route::input('id', 0);

        $dataRow_module_banner_carousel = $this->moduleBannerCarouselService->getDetailBackend($id, $group);

        $langs = is_null($dataRow_module_banner_carousel) ? [] : $dataRow_module_banner_carousel->lang->pluck('lang')->toArray();
        $form_choose_lang = $this->languageService->getListFormChoose($langs);

        return [
            'dataRow_module_banner_carousel' => $dataRow_module_banner_carousel,
            'form_choose_lang' => $form_choose_lang,
        ];
    }
    
    public function detailBackendAdd($group) {
        $form_choose_lang = $this->languageService->getListFormChoose();
        $dataSet_module_banner_carousel = $this->moduleBannerCarouselService->getListOrder($group);

        return [
            'form_choose_lang' => $form_choose_lang,
            'dataSet_module_banner_carousel' => $dataSet_module_banner_carousel,
        ];
    }
    
    public function detailBackendEdit($group) {
        $id = Route::input('id', 0);

        $dataRow_module_banner_carousel = $this->moduleBannerCarouselService->getDetailBackendEdit($id, $group);
        $dataSet_module_banner_carousel = $this->moduleBannerCarouselService->getListOrder($group);

        $langs = is_null($dataRow_module_banner_carousel) ? [] : $dataRow_module_banner_carousel->lang->pluck('lang')->toArray();
        $form_choose_lang = $this->languageService->getListFormChoose($langs);

        return [
            'dataRow_module_banner_carousel' => $dataRow_module_banner_carousel,
            'form_choose_lang' => $form_choose_lang,
            'dataSet_module_banner_carousel' => $dataSet_module_banner_carousel,
        ];
    }

    ## Action

    public function actionAdd($group) {
        $this->moduleBannerCarouselValidation->validate_add();

        DB::beginTransaction();
        try {
            $dataRow_module_banner_carousel = $this->moduleBannerCarouselService->add($group, Request::all());
            $dataSet_module_banner_carousel_lang = $this->moduleBannerCarouselService->addLang($dataRow_module_banner_carousel->id, Request::all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable = config('module_banner_carousel.datatable');
        // dblog
        DBLog::write($datatable, null, $dataRow_module_banner_carousel);
        foreach ($dataSet_module_banner_carousel_lang as $k => $v) {
            DBLog::write("{$datatable}_lang", null, $v);
        }

        // upload
        foreach ($dataSet_module_banner_carousel_lang as $k => $v) {
            FileUpload::handleFile($v->photo);
            FileUpload::handleFile($v->photo_m);
        }

        return [
            'msg' => trans('message.success.add'),
        ];
    }

    public function actionEdit($group) {
        $this->moduleBannerCarouselValidation->validate_edit();

        $id = Request::input('id');
        DB::beginTransaction();
        try {
            $result = $this->moduleBannerCarouselService->edit($id, $group, Request::all());
            $result_lang = null;
            if ($result) {
                $result_lang = $this->moduleBannerCarouselService->editLang($id, Request::all());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable = config('module_banner_carousel.datatable');
        // log
        DBLog::write($datatable, array_get($result, 'before'), array_get($result, 'after'));
        if ($result_lang) {
            foreach ($result_lang['add'] as $k => $v) {
                DBLog::write("{$datatable}_lang", null, $v);
            }
            foreach ($result_lang['edit'] as $k => $v) {
                DBLog::write("{$datatable}_lang", array_get($v, 'before'), array_get($v, 'after'));
            }
            foreach ($result_lang['delete'] as $k => $v) {
                DBLog::write("{$datatable}_lang", $v, null);
            }
        }

        // upload
        if ($result_lang) {
            foreach ($result_lang['add'] as $k => $v) {
                if ($v) {
                    FileUpload::handleFile($v->photo);
                    FileUpload::handleFile($v->photo_m);
                }
            }
            foreach ($result_lang['edit'] as $k => $v) {
                if (array_get($v, 'before') && array_get($v, 'after')) {
                    FileUpload::handleFile(array_get($v, 'after')->photo, array_get($v, 'before')->photo);
                    FileUpload::handleFile(array_get($v, 'after')->photo_m, array_get($v, 'before')->photo_m);
                }
            }
            foreach ($result_lang['delete'] as $k => $v) {
                if ($v) {
                    FileUpload::handleFile(null, $v->photo);
                    FileUpload::handleFile(null, $v->photo_m);
                }
            }
        }

        return [
            'msg' => trans('message.success.edit'),
        ];
    }
    
    public function actionStatus($group) {
        $this->moduleBannerCarouselValidation->validate_status();

        $ids = Request::input('id', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $status = Request::input('status');
        DB::beginTransaction();
        try {
            $result = collect();
            foreach ($ids as $k => $v) {
                $result->push($this->moduleBannerCarouselService->changeStatus($v, $group, $status));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable = config('module_banner_carousel.datatable');
        // log
        foreach ($result as $k => $v) {
            DBLog::write($datatable, array_get($v, 'before'), array_get($v, 'after'));
        }

        return [
            'msg' => trans('message.success.edit'),
        ];
    }
    
    public function actionDelete($group) {
        $this->moduleBannerCarouselValidation->validate_delete();

        $ids = Request::input('id', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        DB::beginTransaction();
        try {
            $result = collect();
            $result_lang = collect();
            foreach ($ids as $k => $v) {
                $result->put($k, $this->moduleBannerCarouselService->delete($v, $group));
                if (array_get($result->get($k), 'before') && !array_get($result->get($k), 'after')) {
                    $result_lang_tmp = $this->moduleBannerCarouselService->deleteLang($v);
                    if ($result_lang_tmp) {
                        foreach ($result_lang_tmp as $kk => $vv) {
                            $result_lang->push($vv);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $datatable = config('module_banner_carousel.datatable');
        // log
        foreach ($result as $k => $v) {
            DBLog::write($datatable, array_get($v, 'before'), array_get($v, 'after'));
        }
        foreach ($result_lang as $k => $v) {
            DBLog::write("{$datatable}_lang", $v, null);
        }

        // upload
        foreach ($result_lang as $k => $v) {
            if ($v) {
                FileUpload::handleFile(null, $v->photo);
                FileUpload::handleFile(null, $v->photo_m);
            }
        }

        return [
            'msg' => trans('message.success.delete'),
        ];
    }

    #

}
