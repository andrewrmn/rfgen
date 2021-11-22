<?php

namespace WLMStripe;

/**
 * Class Discount.
 *
 * @property string $object
 * @property Coupon $coupon
 * @property string $customer
 * @property int $end
 * @property int $start
 * @property string $subscription
 */
class Discount extends StripeObject
{
    const OBJECT_NAME = 'discount';
}
