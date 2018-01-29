<?php

namespace App\Services\Input;

class Input
{
    public function old($key, $default = '') {
    	if (!isset($_SESSION['old'])) {
    		return $default;
    	}
        return $_SESSION['old']->has($key) ? $_SESSION['old']->get($key) : $default;
    }

    public function checkbox($key, $item)
    {
        if (in_array($item, (array) $this->old($key))) {
            return 'checked';
        }
    }
}