<?php

namespace App\Models;

use App\Services\Login\LoginService;
use App\Services\Redirect;
use Cake\Chronos\Chronos;
use Illuminate\Support\Collection;

class Order extends Model
{
    const INCOMPLETE = 'incomplete';
    const COMPLETED = 'completed';
    const PROCESSING = 'processing';
    const FINISHED = 'finished';
    const CANCELLED = 'cancelled';

    protected $table = 'orders';

    /**
     * Retrieve the payment for this order.
     * @return Payment
     */
    public function payment()
    {
        return new Payment(['status' => $this->payment_status, 'mollie_id' => $this->mollie_id]);
    }

    /**
     * Retrieve the items associated with this order.
     * @return mixed
     */
    public function items() {
    	$items = app('database')->table('order_items')->where('order_id', $this->id)->get();

    	return $items->map(function ($item) {
    	    return new OrderItem($item);
        })->toArray();
    }

    /**
     * Remove all items from the order.
     * @return bool
     */
    public function clear()
    {
        if ($this->status === static::INCOMPLETE) {
            app('database')->table('order_items')->where('order_id', $this->id)->delete();

            return true;
        }

        return false;
    }

    /**
     * Rempve OrderItem from the order.
     * @param $item
     * @return bool
     */
    public function remove($item)
    {
        if ($this->status === static::INCOMPLETE) {
            app('database')->table('order_items')->where('id', $item)->delete();

            return true;
        }

        return false;
    }

    public function user() {
        return User::findOrFail($this->user_id);
    }

    public function statusDetails() {
        return new OrderStatusDetails($this->status, $this->payment()->status);
    }

    // The total normal price of this order (every orderItem cobined)
    public function totalPrice() {
        return array_sum(array_map(function($orderItem) {
            return $orderItem->totalPrice();
        }, $this->items()));
    }

    // The total effective price of this order (every orderItem combined)
    public function effectiveTotalPrice() {
        return array_sum(array_map(function($orderItem) {
            return $orderItem->effectiveTotalPrice();
        }, $this->items()));
    }

    // The subtotal price (totalprice - vat)
    public function subtotalPrice() {
        return $this->totalPrice() - $this->totalVat();
    }

    // The total effective price - total effective VAT
    public function effectiveSubtotalPrice() {
        return $this->effectiveTotalPrice() - $this->effectiveTotalVat();
    }

    // The total vat for the normal price of all items
    public function totalVat() {
        return $this->totalPrice() * env('VAT_PERCENTAGE');
    }

    // The total effective vat for all order items
    public function effectiveTotalVat() {
        return $this->effectiveTotalPrice() * env('VAT_PERCENTAGE');
    }

    /**
     * Check if the order has an OrderItem for the given product.
     * @param $product
     * @return bool
     */
    public function hasProduct($product)
    {
        foreach ($this->items() as $item)
        {
            if ($item->item_id === $product) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find an OrderItem by product id.
     * @param $product
     * @return mixed
     */
    public function product($product)
    {
        return (new Collection($this->items()))->where('item_id', $product)->first();
    }

    /**
     * Set the Order to completed status.
     */
    public function submit()
    {
        $this->update([
            'status' => static::COMPLETED
        ]);
    }

    public function reorder()
    {
        $current = LoginService::getCurrentUser()->currentOrder();
        $current->clear();

        $products = (new Collection($this->items()))->map(function ($item) use ($current) {
            $item->id = null;
            $item->order_id = $current->id;
            return $item->attributes;
        });

        app('database')->table('order_items')->where('order_id', $current->id)->insert($products->toArray());

        return Redirect::to('/cart');
    }

    public function orderAllowed()
    {
        if ($this->is_event) {
            return true;
        }

        if (Chronos::now()->isWeekend()) {
            return false;
        }

        if (! $this->tomorrowAllowed() && ! $this->todayAllowed()) {
            return false;
        }

        return true;
    }

    public function tomorrowAllowed()
    {
        if (Chronos::tomorrow()->isWeekend()) {
            return false;
        }

        return true;
    }

    public function todayAllowed()
    {
        if (Chronos::today()->isWeekend()) {
            return false;
        }

        if (! Chronos::now()->between(Chronos::today(), Chronos::today()->addHour(14))) {
            return false;
        }

        return true;
    }
}