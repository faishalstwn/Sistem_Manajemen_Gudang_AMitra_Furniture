<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    /**
     * Validasi dan hitung diskon voucher (AJAX endpoint).
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', strtoupper(trim($request->code)))->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher tidak ditemukan.',
            ], 404);
        }

        $subtotal = (float) $request->subtotal;
        $validation = $voucher->isValid($subtotal);

        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message'],
            ], 422);
        }

        $discount = $voucher->calculateDiscount($subtotal);

        return response()->json([
            'valid' => true,
            'message' => $validation['message'],
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'value' => $voucher->value,
                'max_discount' => $voucher->max_discount,
            ],
            'discount' => $discount,
            'total_after_discount' => max(0, $subtotal - $discount),
        ]);
    }
}
