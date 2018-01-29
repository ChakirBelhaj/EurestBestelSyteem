<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Login\LoginService;
use App\Services\Redirect;
use Cake\Chronos\Chronos;

class OrderController extends Controller
{
    /**
     * Get the current incomplete order.
     * @return Order
     */
    public function currentOrder()
    {
        return LoginService::getCurrentUser()->currentOrder();
    }

    /**
     * Add item to the current order or update an existing one.
     */
    public function add()
    {
        $product = Product::findOrFail(request()->get('product'));

        $order = $this->currentOrder();

        foreach ($order->items() as $item) {
            if ($item->item_id === $product->id) {
                $item->update([
                    'remarks' => request()->get('remarks', $item->remarks),
                    'amount' => request()->get('amount', $item->amount),
                ]);

                message('Product(en) bijgewerkt');

                return Redirect::back();
            }
        }

        $item = [
            'name' => $product->name,
            'image' => $product->image,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'item_id' => $product->id,
            'order_id' => $order->id,
            'amount' => request()->get('amount', 1),
            'remarks' => request()->get('remarks')
        ];

        OrderItem::table()->insert($item);

        message('Product(en) toegevoegd aan winkelwagen');

        return Redirect::back();
    }

    /**
     * Remove an item from the current order.
     */
    public function remove()
    {
        Order::findOrFail(request('order'))->remove(request('item'));

        message('Product verwijderd');

        Redirect::back();
    }

    /**
     * Remove all items from the current order.
     */
    public function clear()
    {
        Order::findOrFail(request('order'))->clear();

        Redirect::back();
    }

    /**
     * Change status to complete and redirect to Order details page.
     */
    public function submit()
    {
        $order = $this->currentOrder();

        if (! $order->orderAllowed()) {
            app()->abort(500);
        }

        if (! $order->is_event && ! in_array(request('pickup'), ['today', 'tomorrow'])) {
            app()->abort(500);
        }

        if ($order->is_event) {
            $date = Chronos::parse(request('date'));
        } else {
            if (request('pickup') === 'today') {
                if (Chronos::now()->between(Chronos::today(), Chronos::today()->addHour(11))) {
                    $date = Chronos::today()->addHours(11)->addMinutes(15);
                }
                else {
                    $date = Chronos::now()->addMinutes(15);
                }
            } elseif (request('pickup') === 'tomorrow') {
                $date = Chronos::tomorrow()->addHour(11)->addMinutes(15);
            }
        }

        $order->update([
            'date_by' => $date->toDateTimeString(),
            'remarks' => request('remarks'),
            'status' => Order::COMPLETED,
        ]);

        if ($order->is_event) {
            return Redirect::to('/account/orders/'.$order->id);
        }

        return Redirect::to("/payment/pay/{$order->id}");
    }

    /**
     * Change order to event.
     */
    public function change()
    {
        $order = $this->currentOrder();

        $order->clear();

        $order->update([
            'is_event' => ! $order->is_event,
        ]);

        return Redirect::back();
    }
}