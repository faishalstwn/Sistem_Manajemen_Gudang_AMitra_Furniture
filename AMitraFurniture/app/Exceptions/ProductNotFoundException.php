<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function render()
    {
        return response()->json([
            'status' => 'error',
            'code' => 'PRODUCT_NOT_FOUND',
            'message' => $this->message,
        ], 404);
    }
}
