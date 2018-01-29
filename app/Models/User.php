<?php

namespace App\Models;

class User extends Model
{
    protected $table = 'users';

	public function fullname()
    {
        if ($this->middlename != '') {
            return ucfirst(strtolower($this->firstname)) . ' ' . strtolower($this->middlename) . ' ' . ucfirst(strtolower($this->lastname));
        }

        return ucfirst(strtolower($this->firstname)) . ' ' . ucfirst(strtolower($this->lastname));
    }

    public function currentOrder()
    {
        $query = Order::table()->where('user_id', $this->id)->where('status', Order::INCOMPLETE);

        if ($query->exists()) {
            return new Order($query->first());
        }

        return Order::find(Order::table()->insertGetId(['user_id' => $this->id]));
    }
}