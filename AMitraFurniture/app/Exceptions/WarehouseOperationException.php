<?php

namespace App\Exceptions;

use Exception;

class WarehouseOperationException extends Exception
{
    public function render()
    {
        return response()->json([
            'status' => 'error',
            'code' => 'WAREHOUSE_OPERATION_ERROR',
            'message' => $this->message,
        ], 400);
    }
}
