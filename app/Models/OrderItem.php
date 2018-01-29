<?php

namespace App\Models;

class OrderItem extends Model
{
    protected $table = 'order_items';

    // Wether the product still exists in the catalog
    public function stillExists() {
        return app('database')->table('products')->select()->where('id', $this->item_id)->first() != NULL;
    }

    // Wether the item is in sale or not
    public function isSale() {
        return $this->sale_price != NULL;
    }

    // The price of a single item, the sale_price if in sale, else the normal price
    public function effectivePrice() {
    	return $this->sale_price != NULL ? $this->sale_price : $this->price;
    }

    // The VAT for a single item not in sale
    public function vat() {
    	return $this->price * env('VAT_PERCENTAGE');
    }

    // The VAT for a single item in sale
    public function saleVat() {
    	return $this->sale_price * env('VAT_PERCENTAGE');
    }

    // The VAT that's effective: aka the VAT of the normal price when not in sale, the sale price if in sale
    public function effectiveVat() {
    	return $this->effectivePrice() * env('VAT_PERCENTAGE');
    }

    // The normal price * amount of items in orderItem
    public function totalPrice() {
    	return $this->price * $this->amount;
    }

    // The sale price * amount of items in orderItem
    public function totalSalePrice() {
    	return $this->isSale() ? $this->sale_price * $this->amount : NULL;
    }

    // The effective price of the item * the amount of items
    public function effectiveTotalPrice() {
    	return $this->effectivePrice() * $this->amount;
    }

    // The discount on a single product
    public function discount() {
    	return $this->isSale() ? $this->price - $this->sale_price : 0.0;
    }

    // The total discount on this order item
    public function totalDiscount() {
    	return $this->discount * $this->amount;
    }

}