<?php

namespace Noking50\Modules\Cart\Discount;

use InvalidArgumentException;

/**
 * 購物車優惠管理
 * 
 * @package App\Classes\FileUpload\CloudStorage
 */
class DiscountManager {

    /**
     * 紀錄已使用的優惠實例
     *
     * @var array 
     */
    protected $discounts = array();

    /**
     * Construct
     */
    public function __construct() {
        
    }

    /**
     * 取得優惠實例
     * 
     * @param string $discount 優惠名稱
     * @return mix
     */
    public function make($discount) {
        $discount_name = studly_case($discount);
        if (!isset($this->discounts[$discount_name])) {
            $classname = __NAMESPACE__ . "\\Plans\\" . $discount_name;
            if (!class_exists($classname)) {
                throw new InvalidArgumentException("discount '" . $discount_name . "' not found.");
            }
            
            $this->discounts[$discount_name] = new $classname();
        }
        return $this->discounts[$discount_name];
    }

}
