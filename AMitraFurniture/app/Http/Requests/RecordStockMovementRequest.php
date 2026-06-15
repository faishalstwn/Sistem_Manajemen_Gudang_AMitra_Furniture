<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordStockMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999999',
            'type' => 'required|in:in,out',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.integer' => 'Jumlah harus berupa angka bulat',
            'quantity.min' => 'Jumlah minimal 1',
            'type.required' => 'Tipe pergerakan harus dipilih',
            'type.in' => 'Tipe pergerakan hanya boleh masuk atau keluar',
        ];
    }
}
