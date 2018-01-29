<?php

namespace App\Models;

class Product extends Model
{
    protected $table = 'products';

    public function category()
    {
        return Category::find($this->category_id);
    }

    // Wether the item is in sale or not
    public function isSale() {
        return $this->sale_price != NULL;
    }

    // The effective price, either the sale_price if present or else the normal price
    public function effectivePrice() {
    	return $this->sale_price != NULL ? $this->sale_price : $this->price;
    }

   	 // The VAT for the normal price
    public function vat() {
    	return $this->price * env('VAT_PERCENTAGE');
    }

    // The VAT for the sale price
    public function saleVat() {
    	return $this->sale_price != NULL ? $this->sale_price * env('VAT_PERCENTAGE') : 0.0;
    }

    // The effective VAT, either the VAT of the normal price, or the VAT of the sale price if present
    public function effectiveVat() {
    	return $this->effectivePrice() * env('VAT_PERCENTAGE');
    }
}