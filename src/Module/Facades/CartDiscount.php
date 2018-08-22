<?php

namespace Noking50\Modules\Cart\Discount\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see Noking50\User\User
 */
class CartDiscount extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'module_cart_discount';
    }

}
