<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function render()
    {
        return response()->json([
            'status' => 'error',
            'code' => 'INSUFFICIENT_STOCK',
            'message' => $this->message,
        ], 422);
    }
}
